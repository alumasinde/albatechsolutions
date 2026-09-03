<?php

declare(strict_types=1);

namespace App\Modules\Cms\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Modules\Cms\Repository\MenuRepository;

final class MenuService extends BaseService
{
    public function __construct(
        private readonly MenuRepository $menus
    ) {
    }

    public function addItem(int $menuId, array $data): int
    {
        $id = $this->menus->createItem([
            'menu_id'       => $menuId,
            'parent_id'     => !empty($data['parent_id']) ? $data['parent_id'] : null,
            'label'         => $data['label'],
            'url'           => $data['url'],
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
            'opens_new_tab' => !empty($data['opens_new_tab']) ? 1 : 0,
        ]);

        AuditLog::record('menu_item.created', 'menu_item', $id);

        return $id;
    }

    public function updateItem(int $id, array $data): void
    {
        $this->menus->updateItem($id, [
            'label'         => $data['label'],
            'url'           => $data['url'],
            'sort_order'    => (int) ($data['sort_order'] ?? 0),
            'opens_new_tab' => !empty($data['opens_new_tab']) ? 1 : 0,
        ]);

        AuditLog::record('menu_item.updated', 'menu_item', $id);
    }

    public function removeItem(int $id): void
    {
        $this->menus->deleteItem($id);
        AuditLog::record('menu_item.deleted', 'menu_item', $id);
    }
}
