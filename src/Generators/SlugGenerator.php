<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Database\Eloquent\Model;
use Yuges\Sluggable\Options\SlugOptions;
use Yuges\Sluggable\Sluggable;

class SlugGenerator
{
    protected Model $model;

    protected SlugOptions $options;

    public function getSlug(Sluggable $model): string
    {
        return 'lol';
    }


    protected function addSlug(): void
    {
        $options = $this->sluggable();
        $slug = $this->generateNonUniqueSlug();

        if ($options->unique) {
            $slug = $this->makeSlugUnique($slug);
        }

        $this->{$options->column} = $slug;
    }

    protected function makeSlugUnique(string $slug): string
    {
        $options = $this->sluggable();

        $originalSlug = $slug;
        $i = 1;

        while ($this->otherRecordExistsWithSlug($slug) || $slug === '') {
            $slug = $originalSlug.$options->separator.$i++;
        }

        return $slug;
    }

    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        $options = $this->sluggable();

        $query = static::where($options->column, $slug);

        if ($this->usesSoftDeletes()) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this), true);
    }

    protected function generateNonUniqueSlug(): string
    {
        $options = $this->sluggable();

        return Str::slug($this->getSlugSourceString(), $options->separator);
    }

    protected function getSlugSourceString(): string
    {
        $options = $this->sluggable();

        return $this->{$options->source[0]};
    }
}
