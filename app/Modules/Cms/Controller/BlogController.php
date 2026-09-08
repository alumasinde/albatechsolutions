<?php

declare(strict_types=1);

namespace App\Modules\Cms\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Modules\Admin\Service\MediaService;
use App\Modules\Cms\Repository\BlogCategoryRepository;
use App\Modules\Cms\Repository\BlogPostRepository;
use App\Modules\Cms\Repository\MediaRepository;
use App\Modules\Cms\Service\BlogPostService;

final class BlogController extends BaseController
{
    public function __construct(
        private readonly BlogPostService $postService,
        private readonly BlogPostRepository $posts,
        private readonly BlogCategoryRepository $categories,
        private readonly MediaRepository $media,
        private readonly MediaService $mediaService
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
            'media' => $this->media->imageLibrary(),
        ]);
    }

    public function store(Request $request): Response
    {
        if (trim((string) $request->input('title', '')) === '') {
            Session::flash('_errors', ['title' => ['Title is required.']]);
            return $this->back();
        }

        $data = $request->all();
        $featuredMediaId = $this->storeFeaturedUpload($request);
        if ($featuredMediaId !== null) {
            $data['featured_media_id'] = $featuredMediaId;
        }

        $id = $this->postService->create($data, (int) Auth::id());
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
            'media' => $this->media->imageLibrary(),
        ]);
    }

    public function update(Request $request): Response
    {
        $id = (int) $request->param('id');
        if (trim((string) $request->input('title', '')) === '') {
            Session::flash('_errors', ['title' => ['Title is required.']]);
            return $this->back();
        }

        $data = $request->all();
        $featuredMediaId = $this->storeFeaturedUpload($request);
        if ($featuredMediaId !== null) {
            $data['featured_media_id'] = $featuredMediaId;
        } elseif (array_key_exists('featured_media_id', $data) && $data['featured_media_id'] === '') {
            $data['featured_media_id'] = null;
        }

        $this->postService->update($id, $data);
        Session::flash('_success', 'Post updated.');

        return $this->redirect(Config::get('admin.path', '/admin') . '/blog/' . $id . '/edit');
    }

    public function uploadImage(Request $request): Response
    {
        $file = $request->file('image');
        if (!$file) {
            return $this->json(['success' => false, 'message' => 'Please choose an image.'], 422);
        }

        $result = $this->mediaService->storeUpload($file, 'blog');
        if (!$result['success']) {
            return $this->json(['success' => false, 'message' => $result['message'] ?? 'Image upload failed.'], 422);
        }

        return $this->json([
            'success' => true,
            'id' => $result['id'],
            'url' => url('/' . ltrim((string) $result['path'], '/')),
        ]);
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

    private function storeFeaturedUpload(Request $request): ?int
    {
        $file = $request->file('featured_image');
        if (!$file) {
            return null;
        }

        $result = $this->mediaService->storeUpload($file, 'blog');
        if (!$result['success']) {
            Session::flash('_errors', ['featured_image' => [$result['message'] ?? 'Featured image upload failed.']]);
            return null;
        }

        return (int) $result['id'];
    }
}
