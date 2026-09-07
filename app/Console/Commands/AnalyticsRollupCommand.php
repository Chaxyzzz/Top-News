<?php

namespace App\Console\Commands;

use App\Models\ArticleDailyStat;
use App\Models\SiteDailyStat;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AnalyticsRollupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topnews:analytics-rollup {--days=7 : Number of past days to reconcile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile daily editorial and site-wide analytics rollups';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $this->info("Reconciling TopNews analytics aggregates for the past {$days} days...");

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::today()->subDays($i)->toDateString();

            // Total article views on that date
            $articleViews = (int) ArticleDailyStat::where('date', $date)->sum('views');

            // Find or create site daily stat record
            $siteStat = SiteDailyStat::firstOrCreate(
                ['date' => $date],
                [
                    'page_views' => $articleViews,
                    'unique_sessions' => 0,
                    'article_views' => $articleViews,
                    'searches' => 0,
                    'comments_submitted' => 0,
                    'newsletter_subscriptions' => 0,
                ]
            );

            // If article_views in siteStat is lower than actual sum, update it
            if ($siteStat->article_views < $articleViews) {
                $siteStat->update(['article_views' => $articleViews]);
            }
        }

        $this->info('Analytics rollups reconciled successfully.');

        return Command::SUCCESS;
    }
}
