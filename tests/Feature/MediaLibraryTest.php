<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $journalist;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');

        $this->journalist = User::factory()->create(['status' => 'active']);
        $this->journalist->assignRole('journalist');
    }

    public function test_authorized_user_can_view_media_library(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.media.index'));
        $response->assertStatus(200);
        $response->assertSee('Pustaka Media');
    }

    public function test_guest_cannot_access_media_library(): void
    {
        $response = $this->get(route('admin.media.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_upload_image_and_create_media_record(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('liputan-berita.jpg', 1200, 800);

        $response = $this->actingAs($this->journalist)->post(route('admin.media.store'), [
            'file' => $file,
            'alt_text' => 'Suasana Sidang Paripurna DPR',
            'caption' => 'Rapat paripurna pengesahan RUU di Gedung DPR RI.',
            'credit' => 'Foto: Antara / TopNews',
        ]);

        $response->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseHas('media', [
            'original_filename' => 'liputan-berita.jpg',
            'alt_text' => 'Suasana Sidang Paripurna DPR',
            'caption' => 'Rapat paripurna pengesahan RUU di Gedung DPR RI.',
            'credit' => 'Foto: Antara / TopNews',
            'uploaded_by' => $this->journalist->id,
        ]);
    }

    public function test_invalid_file_type_is_rejected(): void
    {
        Storage::fake('public');

        $fakeScript = UploadedFile::fake()->create('malicious.php', 10, 'application/x-php');

        $response = $this->actingAs($this->journalist)->post(route('admin.media.store'), [
            'file' => $fakeScript,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('media', 0);
    }

    public function test_media_metadata_can_be_updated(): void
    {
        $media = Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/test.jpg',
            'filename' => 'test.jpg',
            'original_filename' => 'original.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 1024,
            'uploaded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.media.update', $media), [
            'alt_text' => 'Updated Alt Text',
            'caption' => 'Updated Caption',
            'credit' => 'Foto: Fotografer Baru',
        ]);

        $response->assertRedirect(route('admin.media.show', $media));

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'alt_text' => 'Updated Alt Text',
            'caption' => 'Updated Caption',
            'credit' => 'Foto: Fotografer Baru',
        ]);
    }

    public function test_media_in_active_use_cannot_be_deleted(): void
    {
        Storage::fake('public');

        $media = Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/used.jpg',
            'filename' => 'used.jpg',
            'original_filename' => 'used.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 2048,
            'uploaded_by' => $this->admin->id,
        ]);

        $category = Category::factory()->create();

        Article::create([
            'title' => 'Artikel Menggunakan Media',
            'slug' => 'artikel-menggunakan-media',
            'content' => '<p>Konten artikel</p>',
            'category_id' => $category->id,
            'author_id' => $this->admin->id,
            'status' => ArticleStatus::Published,
            'content_type' => ArticleType::News,
            'featured_media_id' => $media->id,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.media.destroy', $media));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_unused_media_can_be_deleted_by_authorized_user(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/unused.jpg', 'fake content');

        $media = Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/unused.jpg',
            'filename' => 'unused.jpg',
            'original_filename' => 'unused.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 2048,
            'uploaded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.media.destroy', $media));

        $response->assertRedirect(route('admin.media.index'));
        $this->assertSoftDeleted('media', ['id' => $media->id]);
    }

    public function test_modal_picker_returns_json_response(): void
    {
        Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/picker-test.jpg',
            'filename' => 'picker-test.jpg',
            'original_filename' => 'picker-test.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 1024,
            'uploaded_by' => $this->journalist->id,
        ]);

        $response = $this->actingAs($this->journalist)->getJson(route('admin.media.modal'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'current_page',
            'last_page',
            'total',
        ]);
    }
}
