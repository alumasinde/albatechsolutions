<?php

declare(strict_types=1);

/**
 * Build a public-site Font Awesome subset containing only the icon classes the
 * public views and JS actually use. Admin keeps the full all.min.css.
 *
 * Source: public/assets/vendor/fontawesome/css/all.min.css
 * Output: public/assets/vendor/fontawesome/css/fa-subset.min.css
 *
 * Usage: php bin/subset-fontawesome.php
 */

$root = dirname(__DIR__);
$source = $root . '/public/assets/vendor/fontawesome/css/all.min.css';
$output = $root . '/public/assets/vendor/fontawesome/css/fa-subset.min.css';

$scanDirs = [
    $root . '/resources/views/pages',
    $root . '/resources/views/layouts/main.php',
    $root . '/resources/views/services',
    $root . '/resources/views/work',
    $root . '/resources/views/products',
    $root . '/resources/views/solutions',
    $root . '/resources/views/landing',
    $root . '/resources/views/errors',
    $root . '/public/assets/js',
];

$icons = [];
foreach ($scanDirs as $path) {
    $files = is_dir($path) ? array_merge(glob($path . '/*.php') ?: [], glob($path . '/*.js') ?: []) : [$path];
    foreach ($files ?: [] as $file) {
        $content = (string) file_get_contents($file);
        if (preg_match_all('/\bfa-([a-z0-9-]+)\b/', $content, $m)) {
            foreach ($m[1] as $name) $icons[$name] = true;
        }
    }
}
// Style prefixes are not glyphs.
unset($icons['solid'], $icons['regular'], $icons['brands'], $icons['classic'], $icons['sharp'], $icons['light'], $icons['thin'], $icons['duotone']);

$css = (string) file_get_contents($source);
if ($css === '' || $icons === []) {
    fwrite(STDERR, "Missing source CSS or no icon classes found.\n");
    exit(1);
}

// Split minified CSS into rule chunks: "selector{decls" pieces.
$chunks = explode('}', $css);
$kept = [];
$basePattern = '/^\.(?:fa|fas|far|fab|fa-solid|fa-regular|fa-brands|fa-light|fa-thin|fa-classic|fa-sharp|fa-duotone)\s*(?::before|:after|::before|::after)?\s*$/';

foreach ($chunks as $chunk) {
    $chunk = trim($chunk);
    if ($chunk === '' || !str_contains($chunk, '{')) continue;
    [$selector, $decls] = explode('{', $chunk, 2);
    $selector = trim($selector);
    if (str_starts_with($selector, '@font-face')) {
        $kept[] = $chunk . '}';
        continue;
    }
    // Base icon-family rules (e.g. .fa, .fa-solid, .fa-brands and their :before forms).
    $isBase = true;
    foreach (explode(',', $selector) as $part) {
        if (preg_match($basePattern, trim($part)) !== 1) { $isBase = false; break; }
    }
    if ($isBase) { $kept[] = $chunk . '}'; continue; }
    // Glyph rules for icons actually used (also keeps alias groups sharing the content).
    foreach (array_keys($icons) as $name) {
        if (preg_match('/\.fa-' . preg_quote($name, '/') . '(?![a-z0-9-])/', $selector) === 1) {
            $kept[] = $chunk . '}';
            break;
        }
    }
}

$missing = [];
$out = implode('', $kept);
foreach (array_keys($icons) as $name) {
    if (!str_contains($out, '.fa-' . $name)) $missing[] = $name;
}
if ($missing !== []) {
    fwrite(STDERR, "No CSS rule found for icons: " . implode(', ', $missing) . "\n");
    exit(1);
}

$header = '/* Font Awesome subset for the public site. Regenerate: php bin/subset-fontawesome.php */';
file_put_contents($output, $header . "\n" . $out);
echo 'Icons: ' . count($icons) . ' | ' . round(strlen($css) / 1024, 1) . ' KB -> ' . round(strlen($out) / 1024, 1) . " KB\n";
echo 'Written: ' . $output . "\n";
