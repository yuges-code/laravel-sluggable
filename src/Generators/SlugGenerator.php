<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Yuges\Sluggable\Options\SlugOptions;
use Yuges\Sluggable\Interfaces\Sluggable;
use Illuminate\Database\Eloquent\Builder;

class SlugGenerator
{
    protected Sluggable $model;

    protected SlugOptions $options;

    public function getSlug(Sluggable $model): string
    {
        $this->model = $model;
        $this->options = $model->sluggable();

        $slug = $this->generateSlug();

        if ($this->options->unique) {
            $slug = $this->makeSlugUnique($slug);
        }

        return $slug;
    }

    public function getSlugs(): Collection
    {
        $collection = new Collection();

        return $collection;
    }

    protected function generateSlug(): string
    {
        $sources = $this->getSlugSources();

        return Str::slug($sources->implode(' '), $this->options->separator);
    }

    protected function getSlugSources(): Collection
    {
        $collection = new Collection($this->options->source);

        $sources = $collection->map(function (string $source) {
            return $this->model->{$source};
        });

        return $sources->filter();
    }


    protected function makeSlugUnique(string $slug): string
    {
        $i = 1;
        $originalSlug = $slug;

        while ($this->existsSlug($slug) || $slug === '') {
            $slug = $originalSlug.$this->options->separator.$i++;
        }

        return $slug;
    }

    protected function existsSlug(string $slug): bool
    {
        $builder = $this->model->whereSlug($slug);

        return $this->buildQuery($builder, $slug)->getQuery()->exists();
    }

    protected function buildQuery(Builder $builder, string $slug): Builder
    {
        $this->buildUnionQuery($builder, $slug);

        if ($this->usesSoftDeletes($this->model)) {
            $builder->withTrashed();
        }

        return $builder;
    }

    protected function buildUnionQuery(Builder $builder, string $slug): Builder
    {
        $union = new Collection($this->options->union);

        $union->each(function (string $model) use ($slug, $builder) {
            $query = $model::whereSlug($slug);

            if ($this->usesSoftDeletes($model)) {
                $query->withTrashed();
            }

            $builder->getQuery()->union($query);
        });

        return $builder;
    }

    protected function usesSoftDeletes(string|Sluggable $model): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model), true);
    }
}
