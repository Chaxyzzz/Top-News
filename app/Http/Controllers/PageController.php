<?php

namespace App\Http\Controllers;

use App\Enums\PageType;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\Page;
use App\Models\User;
use App\Services\ContactMessageService;
use App\Services\ContentSanitizerService;
use App\Services\SeoService;
use App\Services\SettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function __construct(
        protected SettingsService $settings,
        protected ContactMessageService $contactService,
        protected SeoService $seoService
    ) {}

    /**
     * Display the About page.
     */
    public function about(): View
    {
        $page = Page::published()->systemType(PageType::About)->first();
        $sanitizedContent = $page ? ContentSanitizerService::sanitize($page->content) : null;
        $seoData = $page ? $this->seoService->forPage($page) : $this->seoService->forSection('Tentang Kami', 'Profil dan latar belakang TopNews.', route('about'));

        return view('pages.about', compact('page', 'sanitizedContent', 'seoData'));
    }

    /**
     * Display the Editorial Team page.
     */
    public function editorialTeam(): View
    {
        $page = Page::published()->systemType(PageType::EditorialTeam)->first();
        $sanitizedContent = $page ? ContentSanitizerService::sanitize($page->content) : null;
        $team = User::editorialTeam()->get();
        $seoData = $page ? $this->seoService->forPage($page) : $this->seoService->forSection('Dewan Redaksi', 'Jajaran dewan redaksi dan jurnalis TopNews.', route('editorial.team'));

        return view('pages.editorial-team', compact('page', 'sanitizedContent', 'team', 'seoData'));
    }

    /**
     * Display the Editorial Guidelines page.
     */
    public function editorialGuidelines(): View
    {
        return $this->renderSystemPage(PageType::EditorialGuidelines);
    }

    /**
     * Display the Privacy Policy page.
     */
    public function privacy(): View
    {
        return $this->renderSystemPage(PageType::PrivacyPolicy);
    }

    /**
     * Display the Terms & Conditions page.
     */
    public function terms(): View
    {
        return $this->renderSystemPage(PageType::Terms);
    }

    /**
     * Display the Disclaimer page.
     */
    public function disclaimer(): View
    {
        return $this->renderSystemPage(PageType::Disclaimer);
    }

    /**
     * Display the Advertisement Information page.
     */
    public function advertise(): View
    {
        return $this->renderSystemPage(PageType::AdvertisingInfo);
    }

    /**
     * Display the Contact page.
     */
    public function contact(): View
    {
        $page = Page::published()->systemType(PageType::Contact)->first();
        $sanitizedContent = $page ? ContentSanitizerService::sanitize($page->content) : null;
        $contactSettings = $this->settings->getGroup('contact');
        $seoData = $page ? $this->seoService->forPage($page) : $this->seoService->forSection('Kontak Redaksi', 'Hubungi redaksi TopNews untuk informasi dan pengaduan.', route('contact'));

        return view('pages.contact', compact('page', 'sanitizedContent', 'contactSettings', 'seoData'));
    }

    /**
     * Handle public contact form submission.
     */
    public function submitContact(StoreContactMessageRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->contactService->storeMessage($validated, $request->ip(), $request->userAgent());

        return redirect()->route('contact')->with(
            'success',
            'Terima kasih, '.e($validated['name']).'. Pesan Anda telah kami terima dan akan ditindaklanjuti oleh staf redaksi TopNews.'
        );
    }

    /**
     * Display a custom static page by slug.
     */
    public function showCustomPage(string $slug): View
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();
        $sanitizedContent = ContentSanitizerService::sanitize($page->content);
        $seoData = $this->seoService->forPage($page);

        return view('pages.page', compact('page', 'sanitizedContent', 'seoData'));
    }

    /**
     * Helper to render generic published system pages.
     */
    protected function renderSystemPage(PageType $type): View
    {
        $page = Page::published()->systemType($type)->firstOrFail();
        $sanitizedContent = ContentSanitizerService::sanitize($page->content);
        $seoData = $this->seoService->forPage($page);

        return view('pages.page', compact('page', 'sanitizedContent', 'seoData'));
    }
}
