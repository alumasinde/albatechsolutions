<?php

declare(strict_types=1);

namespace App\Modules\Orders\Service;

use App\Modules\Orders\Repository\OrderDocumentRepository;

final class OrderDocumentService
{
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'application/pdf'];
    private const MAX_BYTES = 5 * 1024 * 1024; // 5MB

    public function __construct(
        private readonly OrderDocumentRepository $documents
    ) {
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function upload(int $orderId, array $file, ?int $uploadedBy): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload failed.'];
        }

        if ($file['size'] > self::MAX_BYTES) {
            return ['success' => false, 'message' => 'File exceeds the 5MB limit.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            return ['success' => false, 'message' => 'Only PDF, JPG, and PNG files are accepted.'];
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'application/pdf' => 'pdf',
            default => 'bin',
        };

        // Private storage — outside the web root, never served directly.
        // Only reachable through the authenticated download route.
        $uploadDir = dirname(__DIR__, 4) . '/storage/uploads/orders/' . $orderId;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Could not save the uploaded file.'];
        }

        $this->documents->create([
            'order_id'      => $orderId,
            'uploaded_by'   => $uploadedBy,
            'disk_path'     => $destination,
            'original_name' => $file['name'],
            'mime_type'     => $mime,
            'size_bytes'    => $file['size'],
        ]);

        return ['success' => true];
    }
}
