<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicArchiveNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_opinion_archive_page_loads_successfully(): void
    {
        $response = $this->get(route('opinion.index'));

        $response->assertStatus(200);
        $response->assertSee('Opini', false);
        $response->assertSee('Kolom Redaksi', false);
    }

    public function test_popular_archive_page_loads_successfully(): void
    {
        $response = $this->get(route('popular.index'));

        $response->assertStatus(200);
        $response->assertSee('Paling Banyak Dibaca');
        $response->assertSee('7 Hari');
    }

    public function test_trending_archive_page_loads_successfully(): void
    {
        $response = $this->get(route('trending.index'));

        $response->assertStatus(200);
        $response->assertSee('Sedang Tren (Trending)');
    }

    public function test_editors_choice_archive_page_loads_successfully(): void
    {
        $response = $this->get(route('editors-choice.index'));

        $response->assertStatus(200);
        $response->assertSee('Pilihan Editor');
    }

    public function test_dynamic_navigation_categories_are_rendered(): void
    {
        $category = Category::where('slug', 'teknologi')->first();
        $this->assertNotNull($category);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($category->name);
    }
}
