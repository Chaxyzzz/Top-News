<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    public const CACHE_KEY = 'topnews.settings.all';

    /**
     * Get all cached settings mapped as 'group.key' => typed_value.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::all()->mapWithKeys(function (Setting $setting) {
                return ["{$setting->group}.{$setting->key}" => $setting->getTypedValue()];
            })->all();
        });
    }

    /**
     * Get a setting value by dot-notation ('group.key').
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    /**
     * Get all settings for a specific group.
     *
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        $prefix = "{$group}.";
        $results = [];

        foreach ($this->all() as $compositeKey => $val) {
            if (str_starts_with($compositeKey, $prefix)) {
                $subKey = substr($compositeKey, strlen($prefix));
                $results[$subKey] = $val;
            }
        }

        return $results;
    }

    /**
     * Update or create a single setting.
     */
    public function set(string $group, string $key, mixed $value, string $type = 'string', bool $isPublic = false): Setting
    {
        $storedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : (string) $value,
            default => $value !== null ? (string) $value : null,
        };

        $setting = Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $storedValue, 'type' => $type, 'is_public' => $isPublic]
        );

        $this->clearCache();

        return $setting;
    }

    /**
     * Bulk update a specific settings group.
     *
     * @param  array<string, array{value: mixed, type: string, is_public?: bool}>  $items
     */
    public function updateGroup(string $group, array $items, ?User $actor = null): void
    {
        DB::transaction(function () use ($group, $items) {
            foreach ($items as $key => $config) {
                $val = $config['value'] ?? null;
                $type = $config['type'] ?? 'string';
                $isPublic = $config['is_public'] ?? false;

                $storedValue = match ($type) {
                    'boolean' => $val ? '1' : '0',
                    'json' => is_array($val) ? json_encode($val) : (string) $val,
                    default => $val !== null ? (string) $val : null,
                };

                Setting::updateOrCreate(
                    ['group' => $group, 'key' => $key],
                    [
                        'value' => $storedValue,
                        'type' => $type,
                        'is_public' => $isPublic,
                    ]
                );
            }
        });

        $this->clearCache();

        if ($actor && class_exists(AuditLogger::class)) {
            AuditLogger::log(
                action: 'settings.updated',
                user: $actor,
                metadata: [
                    'group' => $group,
                    'keys_updated' => array_keys($items),
                ]
            );
        }
    }

    /**
     * Clear the cached settings.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Resolve and cache a Media instance configured in settings.
     */
    public function getMedia(string $key): ?Media
    {
        $mediaId = $this->get($key);
        if (! $mediaId) {
            return null;
        }

        $cacheKey = 'topnews.settings.media.v2.'.$mediaId;

        try {
            $cached = Cache::get($cacheKey);
            if ($cached instanceof \__PHP_Incomplete_Class) {
                Cache::forget($cacheKey);
                $cached = null;
            }
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached instanceof Media) {
            return $cached;
        }

        Cache::forget($cacheKey);

        return Cache::remember($cacheKey, 3600, function () use ($mediaId) {
            return Media::find($mediaId);
        });
    }

    /**
     * Check if a media record is actively assigned to any media setting.
     */
    public function isMediaUsedInSettings(int $mediaId): bool
    {
        return Setting::where('type', 'media')
            ->where('value', (string) $mediaId)
            ->exists();
    }
}
