<?php

/**
 * Build the public stylesheet with the Tailwind CLI.
 *
 * Source:   resources/css/tailwind.css
 * Output:   public/assets/css/bundle.css (committed; production needs no Node)
 *
 * Usage:
 *   php bin/build-css.php            build and write bundle.css
 *   php bin/build-css.php --check    exit 1 if bundle.css is missing, empty or older than its sources
 */

$root = dirname(__DIR__);
$source = $root . '/resources/css/tailwind.css';
$output = $root . '/public/assets/css/bundle.css';
$cli = $root . '/node_modules/.bin/tailwindcss';

$isCheck = in_array('--check', $argv ?? [], true);

$build = static function () use ($root, $source, $output, $cli): int {
    if (!is_file($cli)) {
        fwrite(STDERR, "Tailwind CLI not found. Run: npm install\n");
        return 1;
    }

    $command = escapeshellarg($cli)
        . ' -i ' . escapeshellarg($source)
        . ' -o ' . escapeshellarg($output)
        . ' --minify';

    passthru($command, $exitCode);

    return $exitCode === 0 && is_file($output) && filesize($output) > 0 ? 0 : 1;
};

if ($isCheck) {
    if (!is_file($output) || filesize($output) === 0) {
        fwrite(STDERR, "bundle.css is missing or empty. Run: npm run build:tailwind\n");
        exit(1);
    }

    $css = (string) file_get_contents($output);
    $markers = [
        '--color-primary:#078c99' => 'brand colour token',
        '.btn-primary' => 'button component',
        '.site-nav' => 'site navigation',
        '.prose-page' => 'long-form content styles',
        '.contact-filter__button' => 'contact filter component',
        '.work-carousel__track' => 'work carousel',
        'aspect-ratio:16/10' => 'media aspect ratio',
        'prefers-reduced-motion' => 'reduced motion support',
    ];

    $missing = [];
    foreach ($markers as $needle => $label) {
        if (!str_contains($css, $needle)) $missing[] = $label;
    }

    if ($missing !== []) {
        fwrite(STDERR, "bundle.css is out of date (missing: " . implode(', ', $missing) . "). Run: npm run build:tailwind\n");
        exit(1);
    }

    echo "bundle.css is current.\n";
    exit(0);
}

echo "Building public stylesheet with Tailwind...\n";
if ($build() !== 0) {
    fwrite(STDERR, "Build failed. Run: npm install && npm run build:tailwind\n");
    exit(1);
}

echo "Bundle saved to: " . $output . "\n";
echo "Minified size: " . round(filesize($output) / 1024, 2) . " KB\n";
