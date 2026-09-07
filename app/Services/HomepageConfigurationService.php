<?php

namespace App\Services;

use App\Models\HomepageSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomepageConfigurationService
{
    public const CACHE_KEY = 'topnews.homepage.sections.v2.active';

    /**
     * Get active homepage sections in configured order.
     *
     * @return Collection<int, HomepageSection>
     */
    public function getActiveSections(): Collection
    {
        $cached = null;
        try {
            $cached = Cache::get(self::CACHE_KEY);
        } catch (\Throwable) {
            Cache::forget(self::CACHE_KEY);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class || ! $this->isValidIdArray($cached)) {
            Cache::forget(self::CACHE_KEY);
            $sections = HomepageSection::active()
                ->orderBy('sort_order')
                ->get();
            $cached = $sections->pluck('id')->map(fn ($id) => (int) $id)->all();
            Cache::put(self::CACHE_KEY, $cached, 3600);
        }

        return $this->rehydrateSections($cached);
    }

    /**
     * Validate array of integer section IDs.
     */
    protected function isValidIdArray(mixed $cached): bool
    {
        if (! is_array($cached)) {
            return false;
        }

        foreach ($cached as $id) {
            if (! is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Rehydrate active HomepageSection models preserving cached sort order.
     *
     * @param  list<int>  $cachedIds
     * @return Collection<int, HomepageSection>
     */
    protected function rehydrateSections(array $cachedIds): Collection
    {
        $validIds = array_filter(array_map('intval', $cachedIds));
        if (empty($validIds)) {
            return new Collection;
        }

        $sections = HomepageSection::active()
            ->whereIn('id', $validIds)
            ->with(['category:id,name,slug,description,accent_color'])
            ->get()
            ->keyBy('id');

        $result = new Collection;
        foreach ($validIds as $id) {
            if (isset($sections[$id])) {
                $result->push($sections[$id]);
            }
        }

        return $result;
    }

    /**
     * Get all sections for admin dashboard.
     *
     * @return Collection<int, HomepageSection>
     */
    public function getAllSections(): Collection
    {
        return HomepageSection::orderBy('sort_order')
            ->with(['category:id,name,slug'])
            ->withCount('curatedArticles')
            ->get();
    }

    /**
     * Update section configuration and invalidate cache.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateSection(HomepageSection $section, array $data): HomepageSection
    {
        $section->update($data);
        $this->clearCache();

        return $section;
    }

    /**
     * Toggle active state of a section.
     */
    public function toggleActive(HomepageSection $section): bool
    {
        $section->is_active = ! $section->is_active;
        $section->save();
        $this->clearCache();

        return $section->is_active;
    }

    /**
     * Reorder sections by order map: ['section_id' => order].
     *
     * @param  array<int, int>  $orderMap
     */
    public function reorderSections(array $orderMap): void
    {
        DB::transaction(function () use ($orderMap) {
            foreach ($orderMap as $sectionId => $order) {
                HomepageSection::where('id', $sectionId)->update(['sort_order' => (int) $order]);
            }
        });

        $this->clearCache();
    }

    /**
     * Synchronize curated articles for a manual section.
     *
     * @param  list<int>  $articleIds
     */
    public function curateArticles(HomepageSection $section, array $articleIds): void
    {
        DB::transaction(function () use ($section, $articleIds) {
            $syncData = [];
            $order = 1;
            foreach ($articleIds as $articleId) {
                $syncData[$articleId] = [
                    'sort_order' => $order++,
                    'created_at' => now(),
                ];
            }
            $section->curatedArticles()->sync($syncData);
        });

        $this->clearCache();
    }

    /**
     * Clear cached section configurations.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('topnews.homepage.sections.active');
        Cache::forget('topnews.home.v2.data');
        Cache::forget('topnews.home.data');
    }
}
