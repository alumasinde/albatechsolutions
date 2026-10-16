<?php

/**
 * Rebuild all assets (CSS bundle, favicons, optimized images)
 * Run this after making changes to CSS or images
 */

echo "=== AlbaTech Asset Builder ===\n\n";

// 1. Build CSS bundle
echo "1. Building CSS bundle...\n";
require __DIR__ . '/build-css.php';
echo "\n";

// 2. Generate favicons
echo "2. Generating favicons...\n";
require __DIR__ . '/generate-favicons.php';
echo "\n";

// 3. Optimize images (if source exists)
echo "3. Optimizing images...\n";
if (file_exists(__DIR__ . '/../public/assets/images/albatech-social-card.png')) {
    require __DIR__ . '/optimize-images.php';
} else {
    echo "  Original social card not found, skipping image optimization\n";
}

echo "\n=== Asset build complete ===\n";
echo "Remember to update your database with the new settings if needed.\n";
