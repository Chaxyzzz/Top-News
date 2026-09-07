<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBrandingSettingsRequest;
use App\Http\Requests\UpdateContactSettingsRequest;
use App\Http\Requests\UpdateEditorialSettingsRequest;
use App\Http\Requests\UpdateFooterSettingsRequest;
use App\Http\Requests\UpdateGeneralSettingsRequest;
use App\Http\Requests\UpdateNewsletterSettingsRequest;
use App\Http\Requests\UpdateSeoSettingsRequest;
use App\Http\Requests\UpdateSocialSettingsRequest;
use App\Models\Media;
use App\Services\SettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    /**
     * Settings landing redirect.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.settings.general');
    }

    /**
     * General settings page.
     */
    public function showGeneral(): View
    {
        $this->authorizeAccess('settings.view');
        $values = $this->settings->getGroup('general');

        return view('admin.settings.general', compact('values'));
    }

    public function updateGeneral(UpdateGeneralSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('general', [
            'site_name' => ['value' => $validated['site_name'], 'type' => 'string', 'is_public' => true],
            'tagline' => ['value' => $validated['tagline'], 'type' => 'string', 'is_public' => true],
            'site_description' => ['value' => $validated['site_description'], 'type' => 'text', 'is_public' => true],
            'default_locale' => ['value' => $validated['default_locale'], 'type' => 'string', 'is_public' => false],
            'timezone' => ['value' => $validated['timezone'], 'type' => 'string', 'is_public' => false],
        ], Auth::user());

        return back()->with('success', 'Pengaturan umum situs berhasil diperbarui.');
    }

    /**
     * Branding settings page.
     */
    public function showBranding(): View
    {
        $this->authorizeAccess('settings.branding', 'settings.view');
        $values = $this->settings->getGroup('branding');
        $logoMedia = ! empty($values['logo_media_id']) ? Media::find($values['logo_media_id']) : null;
        $compactMedia = ! empty($values['compact_logo_media_id']) ? Media::find($values['compact_logo_media_id']) : null;
        $faviconMedia = ! empty($values['favicon_media_id']) ? Media::find($values['favicon_media_id']) : null;
        $footerLogoMedia = ! empty($values['footer_logo_media_id']) ? Media::find($values['footer_logo_media_id']) : null;

        return view('admin.settings.branding', compact('values', 'logoMedia', 'compactMedia', 'faviconMedia', 'footerLogoMedia'));
    }

    public function updateBranding(UpdateBrandingSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('branding', [
            'brand_name' => ['value' => $validated['brand_name'], 'type' => 'string', 'is_public' => true],
            'logo_media_id' => ['value' => $validated['logo_media_id'] ?? null, 'type' => 'media', 'is_public' => true],
            'compact_logo_media_id' => ['value' => $validated['compact_logo_media_id'] ?? null, 'type' => 'media', 'is_public' => true],
            'favicon_media_id' => ['value' => $validated['favicon_media_id'] ?? null, 'type' => 'media', 'is_public' => true],
            'footer_logo_media_id' => ['value' => $validated['footer_logo_media_id'] ?? null, 'type' => 'media', 'is_public' => true],
        ], Auth::user());

        return back()->with('success', 'Identitas branding dan logo berhasil diperbarui.');
    }

    /**
     * Contact settings page.
     */
    public function showContact(): View
    {
        $this->authorizeAccess('settings.view');
        $values = $this->settings->getGroup('contact');

        return view('admin.settings.contact', compact('values'));
    }

    public function updateContact(UpdateContactSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('contact', [
            'public_email' => ['value' => $validated['public_email'], 'type' => 'string', 'is_public' => true],
            'public_phone' => ['value' => $validated['public_phone'] ?? null, 'type' => 'string', 'is_public' => true],
            'whatsapp' => ['value' => $validated['whatsapp'] ?? null, 'type' => 'string', 'is_public' => true],
            'office_address' => ['value' => $validated['office_address'], 'type' => 'text', 'is_public' => true],
            'location' => ['value' => $validated['location'] ?? $validated['office_address'], 'type' => 'string', 'is_public' => true],
            'business_hours' => ['value' => $validated['business_hours'] ?? null, 'type' => 'string', 'is_public' => true],
            'google_maps_url' => ['value' => $validated['google_maps_url'] ?? null, 'type' => 'url', 'is_public' => true],
        ], Auth::user());

        return back()->with('success', 'Informasi kontak publik berhasil diperbarui.');
    }

    /**
     * Social media settings page.
     */
    public function showSocial(): View
    {
        $this->authorizeAccess('settings.view');
        $values = $this->settings->getGroup('social');

        return view('admin.settings.social', compact('values'));
    }

    public function updateSocial(UpdateSocialSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('social', [
            'instagram' => ['value' => $validated['instagram'] ?? null, 'type' => 'url', 'is_public' => true],
            'facebook' => ['value' => $validated['facebook'] ?? null, 'type' => 'url', 'is_public' => true],
            'x' => ['value' => $validated['x'] ?? null, 'type' => 'url', 'is_public' => true],
            'youtube' => ['value' => $validated['youtube'] ?? null, 'type' => 'url', 'is_public' => true],
            'tiktok' => ['value' => $validated['tiktok'] ?? null, 'type' => 'url', 'is_public' => true],
            'linkedin' => ['value' => $validated['linkedin'] ?? null, 'type' => 'url', 'is_public' => true],
        ], Auth::user());

        return back()->with('success', 'Tautan media sosial berhasil diperbarui.');
    }

    /**
     * Footer settings page.
     */
    public function showFooter(): View
    {
        $this->authorizeAccess('settings.view');
        $values = $this->settings->getGroup('footer');

        return view('admin.settings.footer', compact('values'));
    }

    public function updateFooter(UpdateFooterSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('footer', [
            'about_text' => ['value' => $validated['about_text'], 'type' => 'text', 'is_public' => true],
            'copyright_text' => ['value' => $validated['copyright_text'] ?? null, 'type' => 'text', 'is_public' => true],
            'developer_label' => ['value' => $validated['developer_label'] ?? 'Website developed by', 'type' => 'string', 'is_public' => true],
            'developer_name' => ['value' => $validated['developer_name'] ?? 'Zakky Mubaraq', 'type' => 'string', 'is_public' => true],
        ], Auth::user());

        return back()->with('success', 'Konfigurasi teks footer berhasil diperbarui.');
    }

    /**
     * Editorial settings page.
     */
    public function showEditorial(): View
    {
        $this->authorizeAccess('settings.editorial', 'settings.view');
        $values = $this->settings->getGroup('editorial');

        return view('admin.settings.editorial', compact('values'));
    }

    public function updateEditorial(UpdateEditorialSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('editorial', [
            'default_allow_comments' => ['value' => ! empty($validated['default_allow_comments']), 'type' => 'boolean', 'is_public' => false],
            'default_reading_words_per_minute' => ['value' => $validated['default_reading_words_per_minute'], 'type' => 'integer', 'is_public' => false],
            'default_articles_per_page' => ['value' => $validated['default_articles_per_page'], 'type' => 'integer', 'is_public' => false],
        ], Auth::user());

        return back()->with('success', 'Standar pengaturan redaksi berhasil diperbarui.');
    }

    /**
     * SEO settings page.
     */
    public function showSeo(): View
    {
        $this->authorizeAccess('settings.seo', 'settings.view');
        $values = $this->settings->getGroup('seo');
        $socialImage = ! empty($values['default_social_image_id']) ? Media::find($values['default_social_image_id']) : null;

        return view('admin.settings.seo', compact('values', 'socialImage'));
    }

    public function updateSeo(UpdateSeoSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('seo', [
            'default_meta_title' => ['value' => $validated['default_meta_title'], 'type' => 'string', 'is_public' => true],
            'default_meta_description' => ['value' => $validated['default_meta_description'], 'type' => 'text', 'is_public' => true],
            'default_social_image_id' => ['value' => $validated['default_social_image_id'] ?? null, 'type' => 'media', 'is_public' => true],
            'organization_name' => ['value' => $validated['organization_name'], 'type' => 'string', 'is_public' => true],
        ], Auth::user());

        return back()->with('success', 'Default metadata dan SEO situs berhasil diperbarui.');
    }

    /**
     * Newsletter settings page.
     */
    public function showNewsletter(): View
    {
        $this->authorizeAccess('settings.view');
        $values = $this->settings->getGroup('newsletter');

        return view('admin.settings.newsletter', compact('values'));
    }

    public function updateNewsletter(UpdateNewsletterSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->settings->updateGroup('newsletter', [
            'enabled' => ['value' => ! empty($validated['enabled']), 'type' => 'boolean', 'is_public' => true],
            'double_opt_in' => ['value' => ! empty($validated['double_opt_in']), 'type' => 'boolean', 'is_public' => false],
            'default_sender_name' => ['value' => $validated['default_sender_name'], 'type' => 'string', 'is_public' => false],
            'default_reply_to' => ['value' => $validated['default_reply_to'], 'type' => 'string', 'is_public' => false],
            'cta_title' => ['value' => $validated['cta_title'], 'type' => 'string', 'is_public' => true],
            'cta_description' => ['value' => $validated['cta_description'], 'type' => 'text', 'is_public' => true],
        ], Auth::user());

        return back()->with('success', 'Konfigurasi buletin newsletter berhasil diperbarui.');
    }

    /**
     * Helper to verify permissions.
     */
    protected function authorizeAccess(string ...$permissions): void
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        foreach ($permissions as $perm) {
            if ($user->hasPermission($perm)) {
                return;
            }
        }

        abort(403, 'Anda tidak memiliki izin untuk mengelola bagian pengaturan ini.');
    }
}
