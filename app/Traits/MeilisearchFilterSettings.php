<?php
namespace App\Traits;

trait MeilisearchFilterSettings
{
    public static function getFilterableArray(): array
    {
        return config('scout.meilisearch.index-settings.' . static::class . '.filterableAttributes');
    }
}