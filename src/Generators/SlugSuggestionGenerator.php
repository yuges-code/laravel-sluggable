<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Yuges\Sluggable\Interfaces\Sluggable;
use Yuges\Sluggable\Traits\HasSluggableModel;
use Yuges\Sluggable\Options\SuggestionOptions;

class SlugSuggestionGenerator
{
    use HasSluggableModel;

    protected SuggestionOptions $suggestionOptions;

    public function getSlugs(Sluggable $model, SuggestionOptions $options): Collection
    {
        $this->setModel($model);

        $this->suggestionOptions = $options;

        $slugs = $this->makeSuggestion()->map(function (string $suggestion) {
            return Str::slug($suggestion, '-');
        });

        if ($this->suggestionOptions->unique) {
            $uniqueGenerator = new SlugUniqueGenerator();

            $slugs->map(function (string $slug) use ($model, $uniqueGenerator) {
                return $uniqueGenerator->makeSlugUnique($model, $slug);
            });
        }

        return $slugs;
    }

    public function makeSuggestion(): Collection
    {
        $suggestion = new Collection();

        $sources = $this->getSlugSources();
        $prefix = new Collection($this->suggestionOptions->prefix);

        $suggestion = $sources->crossJoin($sources)->map(function (array $row) {
            return implode(' ', array_unique($row));
        });

        if ($prefix->isNotEmpty()) {
            $suggestionWithPrefix = $prefix->crossJoin($suggestion->slice(0, $prefix->count()))->map(function (array $row) {
                return implode(' ', array_unique($row));
            });

            $suggestion = $suggestion->merge($suggestionWithPrefix);
        }

        return $suggestion;
    }

    protected function getSlugSources(): Collection
    {
        $collection = new Collection();

        collect(
            array_merge($this->options->source, $this->suggestionOptions->source)
        )->filter()->unique()->each(function (string $source) use ($collection) {
            $collection->push(...explode(' ', $this->model->{$source}));
        });

        return $collection->filter()->unique();
    }
}
