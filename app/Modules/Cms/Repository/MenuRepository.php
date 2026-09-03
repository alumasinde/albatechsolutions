<?php

declare(strict_types=1);

namespace App\Modules\Cms\Repository;

use App\Core\Database;
use PDO;

final class MenuRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM menus WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Flat list of items for a menu, ordered for rendering. Public
     * layouts group by parent_id themselves (kept simple — one level
     * of nesting covers header/footer nav needs).
     */
    public function itemsForMenu(int $menuId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM menu_items WHERE menu_id = :menu_id ORDER BY parent_id IS NOT NULL, sort_order ASC'
        );
        $stmt->execute(['menu_id' => $menuId]);

        return $stmt->fetchAll();
    }

    public function itemsForSlug(string $slug): array
    {
        $menu = $this->findBySlug($slug);

        return $menu ? $this->itemsForMenu((int) $menu['id']) : [];
    }

    public function createItem(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO menu_items (menu_id, parent_id, label, url, sort_order, opens_new_tab, created_at)
             VALUES (:menu_id, :parent_id, :label, :url, :sort_order, :opens_new_tab, NOW())'
        );
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function deleteItem(int $id): bool
    {
        return $this->db->prepare('DELETE FROM menu_items WHERE id = :id')->execute(['id' => $id]);
    }

    public function updateItem(int $id, array $data): bool
    {
        $assignments = implode(', ', array_map(static fn ($c) => "{$c} = :{$c}", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE menu_items SET {$assignments} WHERE id = :id");

        return $stmt->execute([...$data, 'id' => $id]);
    }
}
