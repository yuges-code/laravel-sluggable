<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Yuges\Sluggable\Interfaces\Sluggable;
use Yuges\Sluggable\Traits\HasSluggableModel;
use Yuges\Sluggable\Options\SuggestionOptions;

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

    public function getSlugs(Sluggable $model, SuggestionOptions $options): Collection
    {
        return $this->suggestionGenerator->getSlugs($model, $options);
    }

    protected function generateSlug(): string
    {
        $sources = $this->getSlugSources();

        return Str::slug($sources->implode(' '), $this->options->separator);
    }

    protected function getSlugSources(): Collection
    {
        $collection = collect($this->options->source)->filter()->unique();

        $sources = $collection->map(function (string $source) {
            return $this->model->{$source};
        });

        return $sources->filter()->unique();
    }
}
