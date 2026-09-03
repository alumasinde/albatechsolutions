<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Cms\Repository\BlogCategoryRepository;
use App\Modules\Cms\Repository\BlogPostRepository;
use App\Modules\Cms\Service\BlogPostService;

final class BlogController extends BaseController
{
    public function __construct(
        private readonly BlogPostService $postService,
        private readonly BlogPostRepository $posts,
        private readonly BlogCategoryRepository $categories
    ) {
    }

    public function index(Request $request): Response
    {
        return $this->view('admin.blog.index', ['posts' => $this->posts->allForAdmin()]);
    }

    public function create(Request $request): Response
    {
        return $this->view('admin.blog.form', [
            'post' => null,
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function store(Request $request): Response
    {
        if (trim((string) $request->input('title', '')) === '') {
            Session::flash('_errors', ['title' => ['Title is required.']]);

            return $this->back();
        }

        $id = $this->postService->create($request->all(), (int) Auth::id());
        Session::flash('_success', 'Post created.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/blog/' . $id . '/edit');
    }

    public function edit(Request $request): Response
    {
        $post = $this->posts->find((int) $request->param('id'));

        if (!$post) {
            return Response::text('Not found', 404);
        }

        return $this->view('admin.blog.form', [
            'post' => $post,
            'categories' => $this->categories->allActive(),
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');

        if (trim((string) $request->input('title', '')) === '') {
            Session::flash('_errors', ['title' => ['Title is required.']]);

            return $this->back();
        }

        $this->postService->update($id, $request->all());
        Session::flash('_success', 'Post updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/blog/' . $id . '/edit');
    }

    public function destroy(Request $request): Response
    {
        $this->postService->delete((int) $request->param('id'));
        Session::flash('_success', 'Post deleted.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/blog');
    }

    public function storeCategory(Request $request): Response
    {
        $name = trim((string) $request->input('name', ''));

        if ($name === '') {
            Session::flash('_errors', ['name' => ['Category name is required.']]);

            return $this->back();
        }

        $this->categories->create([
            'name' => $name,
            'slug' => \App\Core\Helpers\Sanitizer::slug($name),
        ]);

        Session::flash('_success', 'Category added.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/blog');
    }
}
