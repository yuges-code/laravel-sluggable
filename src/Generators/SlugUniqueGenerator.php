<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Collection;
use Yuges\Sluggable\Options\SlugOptions;
use Yuges\Sluggable\Interfaces\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class SlugUniqueGenerator
{
    protected Sluggable $model;

    protected SlugOptions $options;

    public function setModel(Sluggable $model): self
    {
        $this->model = $model;
        $this->options = $model->sluggable();

        return $this;
    }

    public function makeSlugUnique(Sluggable $model, string $slug = null): string
    {
        $this->setModel($model);

        $collection = $this->getAllSimilarSlugs($model, $slug);

        $i = 2;
        $originalSlug = $slug;

        while ($this->existsSlug($slug, $collection) || $slug === '') {
            $slug = $originalSlug.$this->options->separator.$i++;
        }

        return $slug;
    }

    protected function getAllSimilarSlugs(Sluggable $model, string $slug = null): Collection
    {
        $union = new Collection($this->options->union);
        $collection = $this->getSimilarSlugs($model, $slug)->toBase();

        if ($union->isNotEmpty()) {
            $union->each(function ($model) use ($slug, $collection) {
                $collection = $collection->merge($this->getSimilarSlugs($model, $slug));
            });
        }

        return $collection;
    }

    protected function getSimilarSlugs(string|Sluggable $model, string $slug = null): EloquentCollection
    {
        $builder = $model::whereSimilarSlugs($slug);

        if ($this->usesSoftDeletes($model)) {
            $builder->withTrashed();
        }

        return $builder->get();
    }

    protected function existsSlug(string $slug, Collection $collection): bool
    {
        return $collection->contains(function (Sluggable $model) use ($slug) {
            $column = $model->sluggable()->column;

            return $model->{$column} === $slug;
        });
    }

    protected function usesSoftDeletes(string|Sluggable $model): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model), true);
    }
}
