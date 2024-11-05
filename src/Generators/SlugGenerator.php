<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Yuges\Sluggable\Options\SlugOptions;
use Yuges\Sluggable\Interfaces\Sluggable;

class SlugGenerator
{
    protected Sluggable $model;

    protected SlugOptions $options;

    public function __construct(
        protected SlugUniqueGenerator $uniqueGenerator
    ) {
    }

    public function setModel(Sluggable $model): self
    {
        $this->model = $model;
        $this->options = $model->sluggable();

        return $this;
    }

    public function getSlug(Sluggable $model): string
    {
        $slug = $this->setModel($model)->generateSlug();

        if (!$this->options->unique) {
            return $slug;
        }

        return $this->uniqueGenerator->makeSlugUnique($model, $slug);
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
}
