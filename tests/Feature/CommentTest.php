<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\CommentStatus;
use App\Enums\UserStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected User $reader;

    protected User $author;

    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->reader = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);
        $this->reader->assignRole('reader');

        $this->author = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);
        $this->author->assignRole('journalist');

        $category = Category::factory()->create(['is_active' => true]);

        $this->article = Article::factory()->create([
            'title' => 'Diskusi Publik Kebijakan Anggaran',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(2),
            'allow_comments' => true,
            'category_id' => $category->id,
            'author_id' => $this->author->id,
        ]);
    }

    public function test_reader_can_submit_comment_which_defaults_to_pending(): void
    {
        $response = $this->actingAs($this->reader)
            ->post(route('news.comments.store', $this->article), [
                'body' => 'Analisis yang sangat komprehensif dan objektif.',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'article_id' => $this->article->id,
            'user_id' => $this->reader->id,
            'body' => 'Analisis yang sangat komprehensif dan objektif.',
            'status' => CommentStatus::Pending->value,
        ]);
    }

    public function test_cannot_submit_comment_when_article_comments_are_disabled(): void
    {
        $this->article->update(['allow_comments' => false]);

        $response = $this->actingAs($this->reader)
            ->post(route('news.comments.store', $this->article), [
                'body' => 'Komentar saya tidak boleh masuk.',
            ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('comments', [
            'body' => 'Komentar saya tidak boleh masuk.',
        ]);
    }

    public function test_comment_body_is_stripped_of_raw_html_tags(): void
    {
        $response = $this->actingAs($this->reader)
            ->post(route('news.comments.store', $this->article), [
                'body' => '<script>alert("XSS")</script><b>Komentar bersih</b>',
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $this->reader->id,
            'body' => 'alert("XSS")Komentar bersih',
        ]);
    }

    public function test_reader_can_reply_to_an_existing_comment(): void
    {
        $parent = Comment::create([
            'article_id' => $this->article->id,
            'user_id' => $this->author->id,
            'body' => 'Ini komentar pembuka.',
            'status' => CommentStatus::Approved,
        ]);

        $response = $this->actingAs($this->reader)
            ->post(route('news.comments.store', $this->article), [
                'body' => 'Ini balasan yang santun.',
                'parent_id' => $parent->id,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', [
            'parent_id' => $parent->id,
            'body' => 'Ini balasan yang santun.',
        ]);
    }

    public function test_reader_can_delete_own_comment(): void
    {
        $comment = Comment::create([
            'article_id' => $this->article->id,
            'user_id' => $this->reader->id,
            'body' => 'Komentar yang ingin saya hapus.',
            'status' => CommentStatus::Pending,
        ]);

        $response = $this->actingAs($this->reader)
            ->delete(route('comments.destroy', $comment));

        $response->assertSessionHas('success');
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
