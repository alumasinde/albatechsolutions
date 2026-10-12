<?php

/**
 * Generate proper favicon sizes from the logo
 * Creates 48x48, 192x192, 512x512 PNG favicons and an SVG favicon
 */

$sourceLogo = __DIR__ . '/../public/assets/images/albatech-logo.png';
$outputDir = __DIR__ . '/../public/assets/images';

if (!file_exists($sourceLogo)) {
    echo "Error: Source logo not found at $sourceLogo\n";
    exit(1);
}

$sizes = [48, 192, 512];

echo "Generating favicons from logo...\n";

// Load source image
$source = imagecreatefrompng($sourceLogo);
if ($source === false) {
    echo "Error: Could not load source logo\n";
    exit(1);
}

$srcWidth = imagesx($source);
$srcHeight = imagesy($source);

echo "Source logo: {$srcWidth}x{$srcHeight}px\n";

// Generate PNG favicons at different sizes
foreach ($sizes as $size) {
    echo "Creating {$size}x{$size} favicon...\n";

    $favicon = imagecreatetruecolor($size, $size);
    imagealphablending($favicon, false);
    imagesavealpha($favicon, true);

    // Enable transparency
    $transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
    imagefill($favicon, 0, 0, $transparent);

    // Resize source to favicon size
    imagecopyresampled($favicon, $source, 0, 0, 0, 0, $size, $size, $srcWidth, $srcHeight);

    $outputFile = $outputDir . "/favicon-{$size}x{$size}.png";
    imagepng($favicon, $outputFile);
    imagedestroy($favicon);

    echo "  Saved: favicon-{$size}x{$size}.png\n";
}

// Create SVG favicon (simplified text-based version)
echo "\nCreating SVG favicon...\n";
$svgFavicon = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <rect width="100" height="100" fill="#078C99" rx="20"/>
  <text x="50" y="65" font-family="Arial, sans-serif" font-size="40" font-weight="bold" fill="white" text-anchor="middle">A</text>
</svg>';

file_put_contents($outputDir . '/favicon.svg', $svgFavicon);
echo "  Saved: favicon.svg\n";

imagedestroy($source);

echo "\nFavicon generation complete!\n";
echo "Generated files:\n";
echo "  - favicon-48x48.png\n";
echo "  - favicon-192x192.png\n";
echo "  - favicon-512x512.png\n";
echo "  - favicon.svg\n";
echo "\nNext: Update layout to use these favicons\n";
