<?php

/**
 * Bundle and minify CSS files
 * Combines tokens.css, app.css, and work.css into a single minified file
 */

$cssDir = __DIR__ . '/../public/assets/css';
$files = [
    'tokens.css',
    'app.css',
    'work.css',
];

$outputFile = $cssDir . '/bundle.css';

echo "Bundling CSS files...\n";

$combined = '';
foreach ($files as $file) {
    $path = $cssDir . '/' . $file;
    if (!file_exists($path)) {
        echo "Warning: $file not found, skipping\n";
        continue;
    }
    $content = file_get_contents($path);
    $combined .= $content . "\n";
    echo "  Added: $file\n";
}

// Minify CSS
echo "\nMinifying CSS...\n";
$minified = minifyCss($combined);

// Write output
file_put_contents($outputFile, $minified);
$originalSize = strlen($combined);
$minifiedSize = strlen($minified);
$reduction = round((1 - $minifiedSize / $originalSize) * 100, 1);

echo "Bundle saved to: $outputFile\n";
echo "Original size: " . round($originalSize / 1024, 2) . " KB\n";
echo "Minified size: " . round($minifiedSize / 1024, 2) . " KB\n";
echo "Reduction: $reduction%\n";

/**
 * Simple CSS minifier
 */
function minifyCss(string $css): string
{
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

    // Remove whitespace around colons, semicolons, braces
    $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css);

    // Remove trailing semicolons
    $css = preg_replace('/;}/', '}', $css);

    // Remove multiple whitespace
    $css = preg_replace('/\s+/', ' ', $css);

    // Remove whitespace before/after CSS
    $css = trim($css);

    return $css;
}
