<?php

namespace App\Console\Commands;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleWorkflowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PublishScheduledArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topnews:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publikasikan seluruh artikel terjadwal yang telah melewati waktu tayang.';

    /**
     * Execute the console command.
     */
    public function handle(ArticleWorkflowService $workflowService): int
    {
        $this->info('Memeriksa antrean artikel terjadwal...');

        $now = now();
        $articles = Article::where('status', ArticleStatus::Scheduled->value)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($articles->isEmpty()) {
            $this->info('Tidak ada artikel terjadwal yang siap dipublikasikan.');

            return self::SUCCESS;
        }

        // Get or fallback system user for audit attribution
        $systemUser = User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->first()
            ?? User::first()
            ?? User::factory()->create();

        $publishedCount = 0;

        foreach ($articles as $article) {
            try {
                DB::transaction(function () use ($article, $systemUser, $workflowService) {
                    $lockedArticle = Article::where('id', $article->id)->lockForUpdate()->first();

                    if ($lockedArticle && $lockedArticle->status === ArticleStatus::Scheduled) {
                        $workflowService->publish($lockedArticle, $systemUser);
                    }
                });

                $this->info("✓ Berhasil menerbitkan: [ID: {$article->id}] {$article->title}");
                $publishedCount++;
            } catch (\Throwable $e) {
                $this->error("✕ Gagal menerbitkan [ID: {$article->id}]: ".$e->getMessage());
            }
        }

        $this->info("Selesai. Total {$publishedCount} artikel berhasil diterbitkan.");

        return self::SUCCESS;
    }
}
