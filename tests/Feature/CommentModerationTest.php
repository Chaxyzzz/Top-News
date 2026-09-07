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

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;

    protected User $journalist;

    protected User $reader;

    protected Article $article;

    protected Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->editor = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);
        $this->editor->assignRole('editor');

        $this->journalist = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'staff',
        ]);
        $this->journalist->assignRole('journalist');

        $this->reader = User::factory()->create([
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);
        $this->reader->assignRole('reader');

        $category = Category::factory()->create(['is_active' => true]);

        $this->article = Article::factory()->create([
            'title' => 'Uji Coba Kebijakan Transportasi',
            'status' => ArticleStatus::Published,
            'published_at' => now()->subHours(3),
            'allow_comments' => true,
            'category_id' => $category->id,
            'author_id' => $this->journalist->id,
        ]);

        $this->comment = Comment::create([
            'article_id' => $this->article->id,
            'user_id' => $this->reader->id,
            'body' => 'Komentar yang menunggu persetujuan.',
            'status' => CommentStatus::Pending,
        ]);
    }

    public function test_editor_can_access_moderation_queue(): void
    {
        $response = $this->actingAs($this->editor)
            ->get(route('admin.comments.index'));

        $response->assertStatus(200);
        $response->assertSee('Moderasi Komentar');
        $response->assertSee('Komentar yang menunggu persetujuan.');
    }

    public function test_journalist_cannot_access_moderation_queue(): void
    {
        $response = $this->actingAs($this->journalist)
            ->get(route('admin.comments.index'));

        $response->assertStatus(403);
    }

    public function test_editor_can_approve_comment(): void
    {
        $response = $this->actingAs($this->editor)
            ->post(route('admin.comments.approve', $this->comment));

        $response->assertSessionHas('success');

        $this->comment->refresh();
        $this->assertEquals(CommentStatus::Approved, $this->comment->status);
        $this->assertEquals($this->editor->id, $this->comment->approved_by);
        $this->assertNotNull($this->comment->approved_at);
    }

    public function test_editor_can_reject_comment(): void
    {
        $response = $this->actingAs($this->editor)
            ->post(route('admin.comments.reject', $this->comment), [
                'reason' => 'Melanggar kesantunan bahasa.',
            ]);

        $response->assertSessionHas('success');

        $this->comment->refresh();
        $this->assertEquals(CommentStatus::Rejected, $this->comment->status);
        $this->assertEquals($this->editor->id, $this->comment->rejected_by);
        $this->assertEquals('Melanggar kesantunan bahasa.', $this->comment->spam_reason);
    }

    public function test_editor_can_mark_comment_as_spam(): void
    {
        $response = $this->actingAs($this->editor)
            ->post(route('admin.comments.spam', $this->comment));

        $response->assertSessionHas('success');

        $this->comment->refresh();
        $this->assertEquals(CommentStatus::Spam, $this->comment->status);
    }

    public function test_public_article_only_displays_approved_comments(): void
    {
        // 1. Initially pending -> Must not be visible publicly
        $response = $this->get(route('news.show', $this->article->slug));
        $response->assertStatus(200);
        $response->assertDontSee('Komentar yang menunggu persetujuan.');

        // 2. Once approved -> Visible publicly
        $this->comment->update([
            'status' => CommentStatus::Approved,
            'approved_by' => $this->editor->id,
            'approved_at' => now(),
        ]);

        $response2 = $this->get(route('news.show', $this->article->slug));
        $response2->assertStatus(200);
        $response2->assertSee('Komentar yang menunggu persetujuan.');
    }
}
