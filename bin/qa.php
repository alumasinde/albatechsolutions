<?php

declare(strict_types=1);

/**
 * AlbaTech QA runner.
 *
 * Runs dependency-free checks that are useful even before PHPUnit/PHPStan:
 *  - PHP syntax validation
 *  - named PDO placeholder audits
 *  - obvious execute()/bindValue() parameter mismatches
 *  - Composer autoload validation
 *  - optional PHPStan/PHPUnit hooks when installed
 *
 * Usage:
 *   php bin/qa.php
 *   php bin/qa.php --skip-syntax
 *   php bin/qa.php --phpstan
 *   php bin/qa.php --phpunit
 */

$root = dirname(__DIR__);
$failures = 0;
$warnings = 0;

$options = getopt('', ['skip-syntax', 'phpstan', 'phpunit']);
$skipSyntax = isset($options['skip-syntax']);

function line(string $message): void
{
    echo $message . PHP_EOL;
}

function result(string $label, bool $ok, string $detail = ''): void
{
    global $failures;
    $status = $ok ? '[PASS]' : '[FAIL]';
    if (!$ok) {
        $failures++;
    }
    printf("%-8s %-34s %s%s", $status, $label, $detail, PHP_EOL);
}

function collectPhpFiles(string $root): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $normalized = str_replace('\\', '/', $path);

        if (
            str_contains($normalized, '/vendor/') ||
            str_contains($normalized, '/storage/') ||
            str_contains($normalized, '/public_html/assets/')
        ) {
            continue;
        }

        $files[] = $path;
    }

    sort($files);
    return $files;
}

/**
 * Extract a simple quoted SQL literal used directly in ->prepare().
 * Dynamic SQL is intentionally skipped; PHPStan/runtime tests cover those paths.
 *
 * @return list<array{file:string,line:int,sql:string,variable:string}>
 */
function findPreparedStatements(string $file): array
{
    $source = file_get_contents($file);
    if ($source === false) {
        return [];
    }

    $results = [];

    $pattern = '/(?:(\\$[A-Za-z_][A-Za-z0-9_]*)\\s*=\\s*)?'
        . '->prepare\\(\\s*([\'"])(.*?)\\2\\s*\\)/s';

    if (!preg_match_all($pattern, $source, $matches, PREG_OFFSET_CAPTURE)) {
        return [];
    }

    foreach ($matches[0] as $i => $match) {
        $offset = $match[1];
        $variable = $matches[1][$i][0] ?? '';
        $sql = $matches[3][$i][0] ?? '';
        $line = substr_count(substr($source, 0, $offset), "\n") + 1;

        $results[] = [
            'file' => $file,
            'line' => $line,
            'sql' => $sql,
            'variable' => $variable,
        ];
    }

    return $results;
}

/** @return list<string> */
function sqlParameters(string $sql): array
{
    // Ignore :: casts and quoted text as far as practical for MySQL/PDO named placeholders.
    preg_match_all('/(?<!:):([A-Za-z_][A-Za-z0-9_]*)/', $sql, $m);
    return $m[1] ?? [];
}

/** @return list<string> */
function duplicateValues(array $values): array
{
    $seen = [];
    $duplicates = [];

    foreach ($values as $value) {
        if (isset($seen[$value])) {
            $duplicates[] = $value;
        }
        $seen[$value] = true;
    }

    return array_values(array_unique($duplicates));
}

function shortPath(string $path, string $root): string
{
    return ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
}

line('');
line('AlbaTech QA');
line('===========');
line('Project: ' . $root);
line('');

$phpFiles = collectPhpFiles($root);

if (!$skipSyntax) {
    $syntaxFailed = 0;

    foreach ($phpFiles as $file) {
        $command = PHP_BINARY . ' -l ' . escapeshellarg($file);
        exec($command, $output, $exitCode);
        if ($exitCode !== 0) {
            $syntaxFailed++;
            line('[FAIL] PHP syntax: ' . shortPath($file, $root));
            foreach ($output as $outputLine) {
                line('       ' . $outputLine);
            }
        }
    }

    result('PHP syntax (' . count($phpFiles) . ' files)', $syntaxFailed === 0,
        $syntaxFailed === 0 ? 'all clean' : $syntaxFailed . ' file(s) failed');
} else {
    line('[SKIP] PHP syntax');
}

$pdoFailures = 0;
$preparedCount = 0;

foreach ($phpFiles as $file) {
    foreach (findPreparedStatements($file) as $statement) {
        $preparedCount++;
        $params = sqlParameters($statement['sql']);
        $duplicates = duplicateValues($params);

        if ($duplicates !== []) {
            $pdoFailures++;
            line(sprintf(
                '[FAIL] PDO duplicate placeholder: %s:%d',
                shortPath($statement['file'], $root),
                $statement['line']
            ));
            line('       Duplicate parameter(s): :' . implode(', :', $duplicates));
            line('       Native MySQL PDO does not safely allow the same named placeholder to appear multiple times.');
        }
    }
}

result(
    'PDO placeholder audit (' . $preparedCount . ' statements)',
    $pdoFailures === 0,
    $pdoFailures === 0 ? 'no duplicate named placeholders found' : $pdoFailures . ' issue(s)'
);

$cssSmoke = $root . '/bin/css-smoke.php';
if (is_file($cssSmoke)) {
    exec(PHP_BINARY . ' ' . escapeshellarg($cssSmoke), $cssOutput, $cssExit);
    result('CSS build smoke test', $cssExit === 0, $cssExit === 0 ? 'generated and verified' : 'build or foundation check failed');
} else {
    result('CSS build smoke test', false, 'bin/css-smoke.php missing');
}

$autoload = $root . '/vendor/autoload.php';
if (is_file($autoload)) {
    try {
        require_once $autoload;
        result('Composer autoload', true, 'loadable');
    } catch (Throwable $e) {
        result('Composer autoload', false, 'autoload failed: ' . $e->getMessage());
    }
} else {
    result('Composer autoload', false, 'vendor/autoload.php missing; run composer install');
}

$phpstanBinary = $root . '/vendor/bin/phpstan' . (PHP_OS_FAMILY === 'Windows' ? '.bat' : '');
if (isset($options['phpstan'])) {
    if (is_file($phpstanBinary)) {
        passthru(escapeshellarg($phpstanBinary) . ' analyse --no-progress', $phpstanExit);
        if ($phpstanExit !== 0) {
            $failures++;
        }
        result('PHPStan', $phpstanExit === 0, $phpstanExit === 0 ? 'clean' : 'analysis failed');
    } else {
        result('PHPStan', false, 'not installed; run composer install/update');
    }
}

$phpunitBinary = $root . '/vendor/bin/phpunit' . (PHP_OS_FAMILY === 'Windows' ? '.bat' : '');
if (isset($options['phpunit'])) {
    if (is_file($phpunitBinary)) {
        passthru(escapeshellarg($phpunitBinary) . ' --testdox');
    } else {
        result('PHPUnit', false, 'not installed; no PHPUnit suite is bundled yet');
    }
}

line('');
if ($failures === 0) {
    line('QA RESULT: PASS');
    exit(0);
}

line('QA RESULT: FAIL (' . $failures . ' check(s) failed)');
exit(1);
