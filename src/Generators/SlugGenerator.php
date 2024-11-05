<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Yuges\Sluggable\Interfaces\Sluggable;
use Yuges\Sluggable\Traits\HasSluggableModel;

class SlugGenerator
{
    use HasSluggableModel;

    public function __construct(
        protected SlugUniqueGenerator $uniqueGenerator,
        protected SlugSuggestionGenerator $suggestionGenerator,
    ) {
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
        return $this->suggestionGenerator->getSlugs();
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
