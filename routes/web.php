<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\Advertising\AdCampaignController;
use App\Http\Controllers\Admin\Advertising\AdOverviewController;
use App\Http\Controllers\Admin\Advertising\AdSlotController;
use App\Http\Controllers\Admin\Advertising\AdvertisementController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BreakingNewsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditorialTeamController;
use App\Http\Controllers\Admin\EngagementOverviewController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\HomepagePreviewController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\NewsroomController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SystemInfoController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Workflow\ArticlePreviewController;
use App\Http\Controllers\Admin\Workflow\ArticleWorkflowController;
use App\Http\Controllers\Admin\Workflow\RevisionController;
use App\Http\Controllers\AdTrackingController;
use App\Http\Controllers\ArticleReactionController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaDiscoveryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes - TopNews Platform
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// News & Discovery
Route::get('/latest', [NewsController::class, 'latest'])->name('latest');
Route::get('/category/{slug}', [NewsController::class, 'category'])->name('category.show');
Route::get('/tag/{slug}', [NewsController::class, 'tag'])->name('tag.show');
Route::get('/author/{username}', [NewsController::class, 'author'])->name('author.show');
Route::get('/opinion', [NewsController::class, 'opinion'])->name('opinion.index');
Route::get('/popular', [NewsController::class, 'popular'])->name('popular.index');
Route::get('/trending', [NewsController::class, 'trending'])->name('trending.index');
Route::get('/editors-choice', [NewsController::class, 'editorsChoice'])->name('editors-choice.index');
Route::get('/video', [MediaDiscoveryController::class, 'video'])->name('video.index');
Route::get('/photo-story', [MediaDiscoveryController::class, 'photoStory'])->name('photo-story.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Reader Engagement Routes (Phase 08)
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/news/{article}/bookmark', [BookmarkController::class, 'store'])->name('news.bookmark');
    Route::delete('/account/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    Route::post('/news/{article}/reaction', [ArticleReactionController::class, 'store'])->name('news.reaction');
    Route::post('/news/{article}/comments', [CommentController::class, 'store'])->name('news.comments.store');
    Route::delete('/account/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

// Temporary Signed Preview for Unpublished Articles
Route::get('/preview/article/{article}', [ArticlePreviewController::class, 'preview'])->name('articles.preview.signed');

// Global Search (Phase 08)
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Informational & Institutional Pages (Phase 10)
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/editorial', [PageController::class, 'editorialTeam'])->name('editorial.team');
Route::get('/editorial-guidelines', [PageController::class, 'editorialGuidelines'])->name('editorial.guidelines');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/disclaimer', [PageController::class, 'disclaimer'])->name('disclaimer');
Route::get('/advertise', [PageController::class, 'advertise'])->name('advertise');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])->middleware('throttle:3,30')->name('contact.submit');
Route::get('/page/{slug}', [PageController::class, 'showCustomPage'])->name('page.show');

// Newsletter Public Interactions (Phase 10)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->middleware('throttle:5,60')->name('newsletter.subscribe');
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])->name('newsletter.verify');
Route::get('/newsletter/unsubscribe/{uuid}', [NewsletterController::class, 'showUnsubscribe'])->name('newsletter.unsubscribe.show');
Route::post('/newsletter/unsubscribe/{uuid}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Advertisement Public Tracking (Phase 09)
Route::get('/ads/{advertisement:uuid}/click', [AdTrackingController::class, 'click'])->name('ads.click');
Route::post('/ads/{advertisement:uuid}/impression', [AdTrackingController::class, 'impression'])->name('ads.impression');

// Technical SEO: Sitemaps & Dynamic Robots (Phase 11)
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-articles.xml', [SitemapController::class, 'articles'])->name('sitemap.articles');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-authors.xml', [SitemapController::class, 'authors'])->name('sitemap.authors');
Route::get('/news-sitemap.xml', [SitemapController::class, 'news'])->name('sitemap.news');

// System Health Check
Route::get('/up', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'OK';
    } catch (Throwable $e) {
        $dbStatus = 'FAIL: '.$e->getMessage();
    }

    return response()->json([
        'status' => 'OK',
        'application' => config('topnews.name'),
        'database' => $dbStatus,
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health-check');

/*
|--------------------------------------------------------------------------
| Authentication & Profile Routes (Phase 02)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Administration & Newsroom CMS Foundation (Phases 02, 03, 04)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'active', 'staff'])
    ->group(function () {
        // Newsroom Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Admin Notification Center
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

        // Newsroom Editorial Queues
        Route::get('/newsroom', [NewsroomController::class, 'index'])->name('newsroom.index');

        // Articles Management & CRUD
        Route::resource('articles', ArticleController::class);

        // Content Structure Overview (Backward compatibility)
        Route::get('/content', [ContentController::class, 'index'])->name('content.index');

        // Article Editorial Workflow Actions
        Route::post('/articles/{article}/submit', [ArticleWorkflowController::class, 'submit'])->name('articles.submit');
        Route::post('/articles/{article}/start-review', [ArticleWorkflowController::class, 'startReview'])->name('articles.review.start');
        Route::post('/articles/{article}/request-revision', [ArticleWorkflowController::class, 'requestRevision'])->name('articles.revision.request');
        Route::post('/articles/{article}/approve', [ArticleWorkflowController::class, 'approve'])->name('articles.approve');
        Route::post('/articles/{article}/publish', [ArticleWorkflowController::class, 'publish'])->name('articles.publish');
        Route::post('/articles/{article}/schedule', [ArticleWorkflowController::class, 'schedule'])->name('articles.schedule');
        Route::post('/articles/{article}/unpublish', [ArticleWorkflowController::class, 'unpublish'])->name('articles.unpublish');
        Route::post('/articles/{article}/archive', [ArticleWorkflowController::class, 'archive'])->name('articles.archive');
        Route::post('/articles/{article}/restore', [ArticleWorkflowController::class, 'restore'])->name('articles.restore');

        // Article Revisions & History
        Route::get('/articles/{article}/revisions', [RevisionController::class, 'index'])->name('articles.revisions.index');
        Route::post('/articles/{article}/revisions/{revision}/restore', [RevisionController::class, 'restore'])->name('articles.revisions.restore');

        // Article Preview & Signed Link Generation
        Route::get('/articles/{article}/preview', [ArticlePreviewController::class, 'preview'])->name('articles.preview');
        Route::post('/articles/{article}/preview/share', [ArticlePreviewController::class, 'generateSignedUrl'])->name('articles.preview.share');

        // Categories Management
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Tags Management
        Route::resource('tags', TagController::class)->except(['show', 'create', 'edit']);

        // Media Library (Phase 07)
        Route::get('/media/modal', [MediaController::class, 'modalList'])->name('media.modal');
        Route::resource('media', MediaController::class)->except(['create', 'edit'])->parameters(['media' => 'media']);

        // Photo Galleries (Phase 07)
        Route::post('/galleries/{gallery}/reorder', [GalleryController::class, 'reorder'])->name('galleries.reorder');
        Route::resource('galleries', GalleryController::class);

        // Staff User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Role & Permission Matrix
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');

        // Security & Audit Logs Explorer
        Route::get('/audit-logs', [ActivityLogController::class, 'index'])->name('audit-logs.index');

        // System Diagnostics (Safe read-only)
        Route::get('/system', [SystemInfoController::class, 'index'])->name('system.index');

        // Comments & Engagement Moderation (Phase 08)
        Route::get('/comments', [CommentModerationController::class, 'index'])->name('comments.index');
        Route::post('/comments/{comment}/approve', [CommentModerationController::class, 'approve'])->name('comments.approve');
        Route::post('/comments/{comment}/reject', [CommentModerationController::class, 'reject'])->name('comments.reject');
        Route::post('/comments/{comment}/spam', [CommentModerationController::class, 'spam'])->name('comments.spam');
        Route::delete('/comments/{comment}', [CommentModerationController::class, 'destroy'])->name('comments.destroy');
        Route::get('/engagement', [EngagementOverviewController::class, 'index'])->name('engagement.index');

        // Homepage CMS & Sections (Phase 09)
        Route::get('/homepage', [HomepageSectionController::class, 'index'])->name('homepage.index');
        Route::get('/homepage/preview', [HomepagePreviewController::class, 'preview'])->name('homepage.preview');
        Route::post('/homepage/reorder', [HomepageSectionController::class, 'reorder'])->name('homepage.reorder');
        Route::get('/homepage/{section}/edit', [HomepageSectionController::class, 'edit'])->name('homepage.edit');
        Route::put('/homepage/{section}', [HomepageSectionController::class, 'update'])->name('homepage.update');
        Route::post('/homepage/{section}/toggle-active', [HomepageSectionController::class, 'toggleActive'])->name('homepage.toggle-active');
        Route::get('/homepage/{section}/curate', [HomepageSectionController::class, 'curate'])->name('homepage.curate');
        Route::post('/homepage/{section}/curate', [HomepageSectionController::class, 'updateCurate'])->name('homepage.curate.update');
        Route::delete('/homepage/{section}', [HomepageSectionController::class, 'destroy'])->name('homepage.destroy');

        // Dedicated Breaking News Management (Phase 09)
        Route::post('/breaking-news/{breaking_news}/toggle-active', [BreakingNewsController::class, 'toggleActive'])->name('breaking-news.toggle-active');
        Route::resource('breaking-news', BreakingNewsController::class)->except(['show']);

        // Navigation CMS (Phase 09)
        Route::get('/navigation', [NavigationController::class, 'index'])->name('navigation.index');
        Route::post('/navigation/items', [NavigationController::class, 'storeItem'])->name('navigation.items.store');
        Route::put('/navigation/items/{item}', [NavigationController::class, 'updateItem'])->name('navigation.items.update');
        Route::delete('/navigation/items/{item}', [NavigationController::class, 'destroyItem'])->name('navigation.items.destroy');
        Route::post('/navigation/{menu}/reorder', [NavigationController::class, 'reorderItems'])->name('navigation.reorder');

        // Advertisement Management System (Phase 09)
        Route::prefix('advertising')->name('advertising.')->group(function () {
            Route::get('/', [AdOverviewController::class, 'index'])->name('overview');
            Route::post('/campaigns/{campaign}/toggle-status', [AdCampaignController::class, 'toggleStatus'])->name('campaigns.toggle-status');
            Route::resource('campaigns', AdCampaignController::class)->except(['show']);
            Route::post('/ads/{advertisement}/toggle-active', [AdvertisementController::class, 'toggleActive'])->name('ads.toggle-active');
            Route::resource('ads', AdvertisementController::class)->except(['show']);
            Route::get('/slots', [AdSlotController::class, 'index'])->name('slots.index');
            Route::post('/slots/{slot}/toggle-active', [AdSlotController::class, 'toggleActive'])->name('slots.toggle-active');
        });

        // Static Pages CMS (Phase 10)
        Route::get('/pages/{page}/preview', [AdminPageController::class, 'preview'])->name('pages.preview');
        Route::post('/pages/{page}/toggle-publish', [AdminPageController::class, 'togglePublish'])->name('pages.toggle-publish');
        Route::resource('pages', AdminPageController::class);

        // Newsletter Subscribers (Phase 10)
        Route::get('/newsletter', [NewsletterSubscriberController::class, 'index'])->name('newsletter.index');
        Route::post('/newsletter/{subscriber}/toggle-block', [NewsletterSubscriberController::class, 'toggleBlock'])->name('newsletter.toggle-block');
        Route::get('/newsletter/export', [NewsletterSubscriberController::class, 'export'])->name('newsletter.export');
        Route::delete('/newsletter/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])->name('newsletter.destroy');

        // Contact Messages Inbox (Phase 10)
        Route::get('/contacts', [ContactMessageController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact_message}', [ContactMessageController::class, 'show'])->name('contacts.show');
        Route::put('/contacts/{contact_message}/status', [ContactMessageController::class, 'updateStatus'])->name('contacts.update-status');
        Route::post('/contacts/{contact_message}/assign', [ContactMessageController::class, 'assign'])->name('contacts.assign');
        Route::post('/contacts/{contact_message}/spam', [ContactMessageController::class, 'markSpam'])->name('contacts.spam');
        Route::delete('/contacts/{contact_message}', [ContactMessageController::class, 'destroy'])->name('contacts.destroy');

        // Editorial Team Public Visibility Management (Phase 10)
        Route::get('/editorial-team', [EditorialTeamController::class, 'index'])->name('editorial-team.index');
        Route::put('/editorial-team', [EditorialTeamController::class, 'update'])->name('editorial-team.update');

        // Centralized Site Settings (Phase 10)
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/general', [SettingsController::class, 'showGeneral'])->name('settings.general');
        Route::put('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');
        Route::get('/settings/branding', [SettingsController::class, 'showBranding'])->name('settings.branding');
        Route::put('/settings/branding', [SettingsController::class, 'updateBranding'])->name('settings.branding.update');
        Route::get('/settings/contact', [SettingsController::class, 'showContact'])->name('settings.contact');
        Route::put('/settings/contact', [SettingsController::class, 'updateContact'])->name('settings.contact.update');
        Route::get('/settings/social', [SettingsController::class, 'showSocial'])->name('settings.social');
        Route::put('/settings/social', [SettingsController::class, 'updateSocial'])->name('settings.social.update');
        Route::get('/settings/footer', [SettingsController::class, 'showFooter'])->name('settings.footer');
        Route::put('/settings/footer', [SettingsController::class, 'updateFooter'])->name('settings.footer.update');
        Route::get('/settings/editorial', [SettingsController::class, 'showEditorial'])->name('settings.editorial');
        Route::put('/settings/editorial', [SettingsController::class, 'updateEditorial'])->name('settings.editorial.update');
        Route::get('/settings/seo', [SettingsController::class, 'showSeo'])->name('settings.seo');
        Route::put('/settings/seo', [SettingsController::class, 'updateSeo'])->name('settings.seo.update');
        Route::get('/settings/newsletter', [SettingsController::class, 'showNewsletter'])->name('settings.newsletter');
        Route::put('/settings/newsletter', [SettingsController::class, 'updateNewsletter'])->name('settings.newsletter.update');

        // Editorial Analytics & Performance (Phase 11)
        Route::get('/analytics', [AnalyticsController::class, 'overview'])->name('analytics.overview');
        Route::get('/analytics/content', [AnalyticsController::class, 'content'])->name('analytics.content');
        Route::get('/analytics/traffic', [AnalyticsController::class, 'traffic'])->name('analytics.traffic');
    });
