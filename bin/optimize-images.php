<?php

/**
 * Regenerate the 1200x630 social preview card from the hero artwork.
 * JPEG keeps the file small enough for WhatsApp and other link-preview crawlers.
 */

$sourcePath = __DIR__ . '/../public_html/assets/images/albatech-hero.webp';
$outputPath = __DIR__ . '/../public_html/assets/images/albatech-social-card.jpg';

if (!is_file($sourcePath)) {
    fwrite(STDERR, "Error: hero image not found at {$sourcePath}\n");
    exit(1);
}

$source = imagecreatefromwebp($sourcePath);
if ($source === false) {
    fwrite(STDERR, "Error: could not load {$sourcePath}\n");
    exit(1);
}

$card = imagecreatetruecolor(1200, 630);
imagecopyresampled($card, $source, 0, 0, 0, 0, 1200, 630, imagesx($source), imagesy($source));
imagejpeg($card, $outputPath, 88);

imagedestroy($card);
imagedestroy($source);

echo 'Social card saved to ' . $outputPath . ' (' . round(filesize($outputPath) / 1024) . " KB)\n";
