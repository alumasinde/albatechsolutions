<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * BaseRepository - all raw SQL must live in repositories (never in
 * services or controllers). Prepared statements only; soft deletes
 * are the default across the app (deleted_at column).
 */
abstract class BaseRepository
{
    protected PDO $db;
    protected string $table;
    protected bool $softDeletes = true;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $sql .= $this->softDeletes ? ' AND deleted_at IS NULL' : '';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function all(int $limit = 100, int $offset = 0): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $sql .= $this->softDeletes ? ' WHERE deleted_at IS NULL' : '';
        $sql .= ' ORDER BY id DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(static fn ($c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $assignments = implode(', ', array_map(static fn ($c) => "{$c} = :{$c}", array_keys($data)));

        $sql = "UPDATE {$this->table} SET {$assignments} WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([...$data, 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        if ($this->softDeletes) {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id");
        } else {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        }

        return $stmt->execute(['id' => $id]);
    }

    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id");

        return $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $sql .= $this->softDeletes ? ' WHERE deleted_at IS NULL' : '';

        return (int) $this->db->query($sql)->fetchColumn();
    }
}
