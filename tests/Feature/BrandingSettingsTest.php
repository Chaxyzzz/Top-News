<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\MediaService;
use App\Services\SettingsService;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(SettingsSeeder::class);
        Storage::fake('public');
    }

    public function test_logo_renders_fallback_wordmark_when_no_media_assigned(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('TOP', false);
        $response->assertSee('NEWS', false);
    }

    public function test_logo_renders_image_when_media_assigned(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create a media record
        $file = UploadedFile::fake()->image('custom-logo.png', 300, 80);
        $path = $file->store('media', 'public');
        $media = Media::create([
            'original_filename' => 'custom-logo.png',
            'filename' => 'custom-logo.png',
            'path' => $path,
            'disk' => 'public',
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size' => 1024,
            'media_type' => 'image',
            'width' => 300,
            'height' => 80,
            'uploaded_by' => $admin->id,
        ]);

        // Assign to branding settings
        app(SettingsService::class)->set('branding', 'logo_media_id', $media->id, 'media', true);

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee($media->url);
    }

    public function test_active_branding_media_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $file = UploadedFile::fake()->image('site-favicon.png', 32, 32);
        $path = $file->store('media', 'public');
        $media = Media::create([
            'original_filename' => 'site-favicon.png',
            'filename' => 'site-favicon.png',
            'path' => $path,
            'disk' => 'public',
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size' => 512,
            'media_type' => 'image',
            'width' => 32,
            'height' => 32,
            'uploaded_by' => $admin->id,
        ]);

        // Set as active favicon
        app(SettingsService::class)->set('branding', 'favicon_media_id', $media->id, 'media', true);

        $this->assertTrue($media->isUsed());

        $this->expectException(\Exception::class);
        app(MediaService::class)->deleteMedia($media);
    }
}
