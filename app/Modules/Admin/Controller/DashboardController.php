<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controller;

use App\Core\Auth;
use App\Core\BaseController;
use App\Core\Request;
use App\Core\Response;
use App\Modules\Admin\Repository\AuditLogRepository;
use App\Modules\Auth\Repository\UserRepository;
use App\Modules\Cms\Repository\BannerRepository;
use App\Modules\Cms\Repository\BlogPostRepository;
use App\Modules\Cms\Repository\FaqRepository;
use App\Modules\Cms\Repository\MediaRepository;
use App\Modules\Cms\Repository\PageRepository;
use App\Modules\Cms\Repository\TestimonialRepository;

final class DashboardController extends BaseController
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly PageRepository $pages,
        private readonly BlogPostRepository $posts,
        private readonly MediaRepository $media,
        private readonly TestimonialRepository $testimonials,
        private readonly FaqRepository $faqs,
        private readonly BannerRepository $banners,
        private readonly AuditLogRepository $auditLogs
    ) {
    }

    public function index(Request $request): Response
    {
        $user = Auth::user();

        $stats = [];
        if (Auth::can('pages.view')) {
            $stats[] = ['label' => 'Pages', 'value' => $this->pages->count(), 'icon' => 'fa-file-lines', 'url' => 'pages'];
        }
        if (Auth::can('blog.view')) {
            $stats[] = ['label' => 'Blog Posts', 'value' => $this->posts->count(), 'icon' => 'fa-newspaper', 'url' => 'blog'];
        }
        if (Auth::can('users.view')) {
            $stats[] = ['label' => 'Users', 'value' => $this->users->count(), 'icon' => 'fa-users', 'url' => 'users'];
        }
        if (Auth::can('media.manage')) {
            $stats[] = ['label' => 'Media Files', 'value' => $this->media->count(), 'icon' => 'fa-photo-film', 'url' => 'media'];
        }
        if (Auth::can('testimonials.manage')) {
            $stats[] = ['label' => 'Testimonials', 'value' => $this->testimonials->count(), 'icon' => 'fa-quote-left', 'url' => 'testimonials'];
        }
        if (Auth::can('faqs.manage')) {
            $stats[] = ['label' => 'FAQs', 'value' => $this->faqs->count(), 'icon' => 'fa-circle-question', 'url' => 'faqs'];
        }
        if (Auth::can('banners.manage')) {
            $stats[] = ['label' => 'Banners', 'value' => $this->banners->count(), 'icon' => 'fa-panorama', 'url' => 'banners'];
        }

        return $this->view('admin.dashboard', [
            'user' => $user,
            'stats' => $stats,
            'revenue' => null,
            'recentActivity' => Auth::can('audit.view') ? $this->auditLogs->paginate(1, 8) : [],
            'twoFactorEnabled' => !empty($user['two_factor_enabled']),
            'myOrders' => null,
        ]);
    }
}
