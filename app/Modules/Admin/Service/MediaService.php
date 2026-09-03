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

        if ($file['size'] > self::MAX_BYTES) {
            return ['success' => false, 'message' => 'File exceeds the 3MB limit.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            return ['success' => false, 'message' => 'Unsupported file type.'];
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

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
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
            'original_name' => $file['name'],
            'mime_type'     => $mime,
            'size_bytes'    => $file['size'],
            'purpose'       => $purpose,
        ]);

        return [
            'success' => true,
            'id' => (int) Database::connection()->lastInsertId(),
            'path' => $relativePath,
        ];
    }
}
