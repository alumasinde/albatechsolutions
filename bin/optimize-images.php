<?php

/**
 * Optimize images for SEO
 * 1. Compress social card to under 300 KB
 * 2. Create WebP hero image at ~60 KB with correct dimensions
 */

$sourceImage = __DIR__ . '/../public/assets/images/albatech-social-card.png';
$socialCardOutput = __DIR__ . '/../public/assets/images/albatech-social-card.webp';
$heroWebPOutput = __DIR__ . '/../public/assets/images/albatech-hero.webp';

if (!file_exists($sourceImage)) {
    echo "Error: Source image not found at $sourceImage\n";
    exit(1);
}

// Load source image
$source = imagecreatefrompng($sourceImage);
if ($source === false) {
    echo "Error: Could not load source image\n";
    exit(1);
}

$width = imagesx($source);
$height = imagesy($source);

echo "Source image: {$width}x{$height}px\n";

// 1. Create optimized social card (WebP, compressed)
// Target: under 300 KB, keep original dimensions for OG
echo "Creating optimized social card WebP...\n";
$tempFile = tempnam(sys_get_temp_dir(), 'social');
$quality = 75;
do {
    imagewebp($source, $tempFile, $quality);
    $size = filesize($tempFile);
    echo "  Quality $quality: " . round($size / 1024, 2) . " KB\n";
    $quality -= 5;
} while ($size > 300 * 1024 && $quality > 50);

copy($tempFile, $socialCardOutput);
unlink($tempFile);
echo "Optimized social card saved to: $socialCardOutput (" . round(filesize($socialCardOutput) / 1024, 2) . " KB)\n";

// 2. Create WebP hero image
// Target: ~60 KB, 1200x630 (1.9:1 ratio for hero display)
echo "\nCreating WebP hero image...\n";
$heroWidth = 1200;
$heroHeight = 630;

$hero = imagecreatetruecolor($heroWidth, $heroHeight);
imagealphablending($hero, false);
imagesavealpha($hero, true);

// Enable transparency
$transparent = imagecolorallocatealpha($hero, 0, 0, 0, 127);
imagefill($hero, 0, 0, $transparent);

// Resize source to hero dimensions
imagecopyresampled($hero, $source, 0, 0, 0, 0, $heroWidth, $heroHeight, $width, $height);

// Save as WebP with quality to target ~60 KB
$quality = 75;
do {
    imagewebp($hero, $heroWebPOutput, $quality);
    $size = filesize($heroWebPOutput);
    echo "  Quality $quality: " . round($size / 1024, 2) . " KB\n";
    $quality -= 5;
} while ($size > 80 * 1024 && $quality > 50);

imagedestroy($hero);
echo "WebP hero saved to: $heroWebPOutput (" . round(filesize($heroWebPOutput) / 1024, 2) . " KB)\n";

imagedestroy($source);

echo "\nImage optimization complete!\n";
echo "Next steps:\n";
echo "1. Replace albatech-social-card.png with the optimized version\n";
echo "2. Update layout to use albatech-hero.webp for hero image\n";
