<?php

declare(strict_types=1);

namespace App\Modules\Admin\Service;

use App\Core\Auth;
use App\Core\BaseService;
use App\Core\Database;

final class MediaService extends BaseService
{
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
    private const MAX_BYTES = 3 * 1024 * 1024; // 3MB

    /**
     * @return array{success: bool, id?: int, path?: string, message?: string}
     */
    public function storeUpload(array $file, string $purpose): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload failed.'];
        }

        if (empty($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Invalid upload.'];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0) {
            return ['success' => false, 'message' => 'The uploaded file is empty.'];
        }

        if ($size > self::MAX_BYTES) {
            return ['success' => false, 'message' => 'File exceeds the 3MB limit.'];
        }

        $mime = $this->detectMimeType($file['tmp_name']);

        if ($mime === null || !in_array($mime, self::ALLOWED_MIME, true)) {
            return ['success' => false, 'message' => 'Unsupported or invalid image file.'];
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            default => 'bin',
        };

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $uploadDir = PUBLIC_PATH . '/assets/uploads/' . $purpose;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            return ['success' => false, 'message' => 'Could not create the upload directory.'];
        }

        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Could not save the uploaded file.'];
        }

        $relativePath = 'assets/uploads/' . $purpose . '/' . $filename;

        $stmt = Database::connection()->prepare(
            'INSERT INTO media (uploaded_by, disk_path, original_name, mime_type, size_bytes, purpose, created_at)
             VALUES (:uploaded_by, :disk_path, :original_name, :mime_type, :size_bytes, :purpose, NOW())'
        );
        $stmt->execute([
            'uploaded_by'   => Auth::id(),
            'disk_path'     => $relativePath,
            'original_name' => (string) ($file['name'] ?? 'upload'),
            'mime_type'     => $mime,
            'size_bytes'    => $size,
            'purpose'       => $purpose,
        ]);

        return [
            'success' => true,
            'id' => (int) Database::connection()->lastInsertId(),
            'path' => $relativePath,
        ];
    }

    /**
     * Detect the uploaded file MIME type without requiring the Fileinfo
     * extension. Fileinfo is preferred when available, while getimagesize()
     * provides a safe fallback for raster images. SVG is validated from its
     * content rather than trusting the client-provided filename.
     */
    private function detectMimeType(string $path): ?string
    {
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mime = @finfo_file($finfo, $path);
                @finfo_close($finfo);

                if (is_string($mime) && $mime !== '') {
                    return $mime;
                }
            }
        }

        if (function_exists('mime_content_type')) {
            $mime = @mime_content_type($path);
            if (is_string($mime) && $mime !== '') {
                return $mime;
            }
        }

        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($path);
            if (is_array($imageInfo) && isset($imageInfo['mime']) && is_string($imageInfo['mime'])) {
                return $imageInfo['mime'];
            }
        }

        $contents = @file_get_contents($path);
        if ($contents === false || strlen($contents) > 1024 * 1024) {
            return null;
        }

        $trimmed = ltrim($contents);
        if (!preg_match('/^<svg\b/i', $trimmed)) {
            return null;
        }

        // Do not accept SVGs containing common active-content vectors.
        if (preg_match('/<\s*(script|iframe|object|embed|foreignObject)\b/i', $contents)
            || preg_match('/\bon[a-z]+\s*=\s*["\']/i', $contents)
            || preg_match('/javascript\s*:/i', $contents)
            || preg_match('/<!DOCTYPE|<!ENTITY/i', $contents)) {
            return null;
        }

        return 'image/svg+xml';
    }
}
