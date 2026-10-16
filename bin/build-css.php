<?php

/**
 * Bundle and minify CSS files.
 * Combines tokens.css and app.css into a single minified bundle.css.
 *
 * Usage:
 *   php bin/build-css.php            build and write bundle.css
 *   php bin/build-css.php --check    exit 1 if bundle.css is missing or stale (for CI/tests)
 */

$cssDir = __DIR__ . '/../public/assets/css';
$files = [
    'tokens.css',
    'app.css',
    'work.css',
];
$outputFile = $cssDir . '/bundle.css';

$minifyCss = static function (string $css): string {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove whitespace around colons, semicolons, braces
    $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css);
    // Remove trailing semicolons
    $css = preg_replace('/;}/', '}', $css);
    // Collapse remaining whitespace
    $css = preg_replace('/\s+/', ' ', $css);
    return trim((string) $css);
};

$build = static function () use ($cssDir, $files, $minifyCss): string {
    $combined = '';
    foreach ($files as $file) {
        $path = $cssDir . '/' . $file;
        if (is_file($path)) $combined .= file_get_contents($path) . "\n";
    }
    return $minifyCss($combined);
};

$isCheck = in_array('--check', $argv ?? [], true);
$expected = $build();

if ($isCheck) {
    $current = is_file($outputFile) ? (string) file_get_contents($outputFile) : '';
    if ($current === $expected) {
        echo "bundle.css is current.\n";
        exit(0);
    }
    fwrite(STDERR, "bundle.css is STALE or missing. Run: php bin/build-css.php\n");
    exit(1);
}

echo "Bundling CSS files...\n";
foreach ($files as $file) {
    if (is_file($cssDir . '/' . $file)) echo "  Added: $file\n";
    else echo "  Warning: $file not found, skipping\n";
}

file_put_contents($outputFile, $expected);
$minifiedSize = strlen($expected);
echo "Bundle saved to: $outputFile\n";
echo "Minified size: " . round($minifiedSize / 1024, 2) . " KB\n";
