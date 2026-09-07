<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAndTagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Kecerdasan Buatan',
            'description' => 'Rubrik riset dan inovasi AI.',
            'accent_color' => '#6D28D9',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Kecerdasan Buatan',
            'slug' => 'kecerdasan-buatan',
            'accent_color' => '#6D28D9',
        ]);
    }

    public function test_category_with_articles_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create(['name' => 'Politik']);
        Article::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_tag_crud_and_article_attachment(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Create tag
        $response = $this->actingAs($admin)->post(route('admin.tags.store'), [
            'name' => 'Startup Digital',
        ]);
        $response->assertRedirect(route('admin.tags.index'));
        $this->assertDatabaseHas('tags', ['slug' => 'startup-digital']);

        $tag = Tag::where('slug', 'startup-digital')->first();
        $article = Article::factory()->create();
        $article->tags()->attach($tag);

        $this->assertTrue($article->tags->contains($tag));

        // Delete tag does not delete article
        $delResponse = $this->actingAs($admin)->delete(route('admin.tags.destroy', $tag));
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
        $this->assertDatabaseHas('articles', ['id' => $article->id]);
    }
}
