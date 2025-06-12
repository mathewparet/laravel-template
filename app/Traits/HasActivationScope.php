<?php
namespace App\Traits;

trait HasActivationScope
{
    public function scopeStatus(\Illuminate\Database\Eloquent\Builder $query, $status): \Illuminate\Database\Eloquent\Builder
    {
        return $query->when($status == 'deleted', fn($query) => $query->whereNotNull('deleted_at'))
                ->when($status == 'active', fn($query) => $query->whereNull('deleted_at'));
    }
}