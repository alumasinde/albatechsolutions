<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\BaseRepository;

final class ContactMessageRepository extends BaseRepository
{
    protected string $table = 'contact_messages';
    protected bool $softDeletes = false;

    public function allForAdmin(): array
    {
        $stmt = $this->db->query('SELECT * FROM contact_messages ORDER BY created_at DESC');

        return $stmt->fetchAll();
    }
}
