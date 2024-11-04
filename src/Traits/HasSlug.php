<?php

namespace Yuges\Sluggable\Traits;

use Yuges\Sluggable\Options\SlugOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Yuges\Sluggable\Observers\SluggableObserver;

trait HasSlug
{
    abstract public function sluggable(): SlugOptions;

    protected static function bootHasSlug(): void
    {
        static::observe(SluggableObserver::class);
    }

    public static function whereSlug(string $slug = null): Builder
    {
        $instance = new static();
        $column = $instance->sluggable()->column;

        return static::where($column, '=', $slug);
    }

    public static function whereSimilarSlugs(string $slug = null): Builder
    {
        $instance = new static();
        $column = $instance->sluggable()->column;

        return static::where($column, '=', $slug)->orWhere($column, 'LIKE', $slug . '%');
    }

    public static function findBySlug(string $slug = null, array $columns = ['*']): ?self
    {
        /** @var ?self */
        $model = static::whereSlug($slug)->first($columns);

        return $model;
    }

    public static function findBySlugOrFail(string $slug = null, array $columns = ['*']): self
    {
        /** @var self */
        $model = static::whereSlug($slug)->firstOrFail($columns);

        return $model;
    }

    public static function findSimilarSlugs(string $slug = null, array $columns = ['*']): Collection
    {
        return static::whereSimilarSlugs($slug)->get($columns);
    }
}
