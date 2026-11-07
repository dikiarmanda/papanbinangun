<?php

namespace App\Traits;

use CodeIgniter\Model;

trait HasUniqueSlug
{
    /**
     * Generate slug unik via single query LIKE (optimasi P3 & DRY C1).
     */
    protected function uniqueSlug(string $slug, ?int $excludeId = null, ?Model $model = null): string
    {
        /** @var Model $targetModel */
        $targetModel = $model ?? ($this->model ?? $this->artikelModel);

        $builder = $targetModel->groupStart()
            ->where('slug', $slug)
            ->orLike('slug', $slug . '-', 'after')
            ->groupEnd();

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        $existing = $builder->findColumn('slug') ?? [];

        if (!in_array($slug, $existing, true)) {
            return $slug;
        }

        $i = 1;
        while (in_array("{$slug}-{$i}", $existing, true)) {
            $i++;
        }

        return "{$slug}-{$i}";
    }
}
