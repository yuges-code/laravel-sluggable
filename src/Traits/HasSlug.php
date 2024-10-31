<?php

namespace Yuges\Sluggable\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Yuges\Sluggable\Options\SlugOptions;

trait HasSlug
{
    abstract public function sluggable(): SlugOptions;

    protected static function bootHasSlug(): void
    {
        static::creating(function (Model $model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function (Model $model) {
            $model->generateSlugOnUpdate();
        });
    }


    protected function generateSlugOnCreate(): void
    {
        $options = $this->sluggable();

        if ($options->skip) {
            return;
        }

        if ($this->{$options->column}) {
            return;
        }

        $this->addSlug();
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

    public static function whereSlug(string $slug = null): Builder
    {
        $instance = new static();
        $column = $instance->sluggable()->column;

        return static::where($column, $slug);
    }

    public static function findBySlug(string $slug = null, array $columns = ['*']): ?self
    {
        return static::whereSlug($slug)->first($columns);
    }

    public static function findBySlugOrFail(string $slug = null, array $columns = ['*']): Model
    {
        return static::whereSlug($slug)->firstOrFail($columns);
    }
}
