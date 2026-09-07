<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PhotoStoryAndGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $journalist;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');

        $this->journalist = User::factory()->create(['status' => 'active']);
        $this->journalist->assignRole('journalist');

        $this->category = Category::factory()->create(['name' => 'Humaniora', 'slug' => 'humaniora']);
    }

    public function test_gallery_creation_and_photo_sync(): void
    {
        $media1 = Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/photo1.jpg',
            'filename' => 'photo1.jpg',
            'original_filename' => 'photo1.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 1024,
            'uploaded_by' => $this->admin->id,
        ]);

        $media2 = Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/photo2.jpg',
            'filename' => 'photo2.jpg',
            'original_filename' => 'photo2.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 1024,
            'uploaded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.galleries.store'), [
            'title' => 'Galeri Pesta Rakyat 2026',
            'description' => 'Dokumentasi visual pesta rakyat tahunan.',
            'status' => 'published',
            'media_ids' => [$media1->id, $media2->id],
            'captions' => [
                $media1->id => 'Panggung utama saat pembukaan festival.',
                $media2->id => 'Antusiasme warga menyaksikan kembang api.',
            ],
            'credits' => [
                $media1->id => 'Foto: Fotografer A / TopNews',
                $media2->id => 'Foto: Fotografer B / TopNews',
            ],
        ]);

        $this->assertDatabaseHas('galleries', [
            'title' => 'Galeri Pesta Rakyat 2026',
            'status' => 'published',
        ]);

        $gallery = Gallery::where('title', 'Galeri Pesta Rakyat 2026')->first();
        $this->assertEquals(2, $gallery->media()->count());
    }

    public function test_photo_story_article_renders_vertical_photo_sequence(): void
    {
        $media = Media::create([
            'uuid' => (string) Str::uuid(),
            'disk' => 'public',
            'path' => 'media/story-photo.jpg',
            'filename' => 'story-photo.jpg',
            'original_filename' => 'story-photo.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 1024,
            'uploaded_by' => $this->admin->id,
        ]);

        $gallery = Gallery::create([
            'title' => 'Galeri Kehidupan Nelayan Tradisional',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $gallery->media()->attach($media->id, [
            'sort_order' => 1,
            'caption_override' => 'Matahari terbit saat nelayan mulai melaut.',
            'credit_override' => 'Foto: Tim Visual TopNews',
        ]);

        $article = Article::create([
            'title' => 'Foto Cerita: Denyut Fajar Nelayan Pesisir',
            'slug' => 'foto-cerita-denyut-fajar-nelayan-pesisir',
            'content' => '<p>Pengantar narasi foto cerita.</p>',
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'status' => ArticleStatus::Published,
            'content_type' => ArticleType::PhotoStory,
            'published_at' => now()->subHours(1),
        ]);

        $article->photoStory()->create([
            'gallery_id' => $gallery->id,
        ]);

        $response = $this->get(route('news.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Matahari terbit saat nelayan mulai melaut.');
        $response->assertSee('Galeri Foto Cerita (1 Foto)');
    }

    public function test_public_photo_story_index_displays_published_stories(): void
    {
        $article = Article::create([
            'title' => 'Foto Cerita: Pesona Geopark Nusantara',
            'slug' => 'foto-cerita-pesona-geopark-nusantara',
            'content' => '<p>Narasi keindahan geopark.</p>',
            'category_id' => $this->category->id,
            'author_id' => $this->journalist->id,
            'status' => ArticleStatus::Published,
            'content_type' => ArticleType::PhotoStory,
            'published_at' => now()->subHours(2),
        ]);

        $gallery = Gallery::create([
            'title' => 'Galeri Geopark',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $article->photoStory()->create([
            'gallery_id' => $gallery->id,
        ]);

        $response = $this->get(route('photo-story.index'));

        $response->assertStatus(200);
        $response->assertSee('Foto Cerita & Galeri');
        $response->assertSee('Foto Cerita: Pesona Geopark Nusantara');
    }
}
