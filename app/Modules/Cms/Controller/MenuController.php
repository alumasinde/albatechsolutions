<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\MenuRepository;
use App\Modules\Cms\Service\MenuService;

final class MenuController extends BaseController
{
    public function __construct(
        private readonly MenuService $menuService,
        private readonly MenuRepository $menus
    ) {
    }

    public function edit(Request $request): Response
    {
        $slug = (string) $request->param('slug');
        $menu = $this->menus->findBySlug($slug);

        if (!$menu) {
            return Response::text('Not found', 404);
        }

        return $this->view('admin.menus.edit', [
            'menu' => $menu,
            'items' => $this->menus->itemsForMenu((int) $menu['id']),
        ]);
    }

    public function storeItem(Request $request): Response
    {
        $slug = (string) $request->param('slug');
        $menu = $this->menus->findBySlug($slug);

        if (!$menu) {
            return Response::text('Not found', 404);
        }

        if (trim((string) $request->input('label', '')) === '' || trim((string) $request->input('url', '')) === '') {
            Session::flash('_errors', ['label' => ['Label and URL are both required.']]);

            return $this->back();
        }

        $this->menuService->addItem((int) $menu['id'], $request->all());
        Session::flash('_success', 'Menu item added.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/menus/' . $slug);
    }

    public function updateItem(Request $request): Response
    {
        $this->menuService->updateItem((int) $request->param('id'), $request->all());
        Session::flash('_success', 'Menu item updated.');

        return $this->back();
    }

    public function destroyItem(Request $request): Response
    {
        $this->menuService->removeItem((int) $request->param('id'));
        Session::flash('_success', 'Menu item removed.');

        return $this->back();
    }
}
