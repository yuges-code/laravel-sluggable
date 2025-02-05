<?php

namespace Yuges\Sluggable\Interfaces;

use Yuges\Sluggable\Options\SlugOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface Sluggable
{
    public function sluggable(): SlugOptions;

    public static function whereSlug(string $slug = null): Builder;

    public static function whereSimilarSlugs(string $slug = null): Builder;

    public static function findBySlug(string $slug = null, array $columns = ['*']): ?self;

    public static function findBySlugOrFail(string $slug = null, array $columns = ['*']): self;

    public static function findSimilarSlugs(string $slug = null, array $columns = ['*']): Collection;
}
