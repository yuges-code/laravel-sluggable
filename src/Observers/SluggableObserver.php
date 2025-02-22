<?php

namespace Yuges\Sluggable\Observers;

use Yuges\Sluggable\Interfaces\Sluggable;
use Yuges\Sluggable\Generators\SlugGenerator;

class SluggableObserver
{
    public function __construct(
        protected SlugGenerator $generator
    ) {
    }

    public function creating(Sluggable $model): void
    {
        $this->generateSlug($model, 'creating');
    }

    public function updating(Sluggable $model): void
    {
        $this->generateSlug($model, 'updating');
    }

    protected function generateSlug(Sluggable $model, string $event): void
    {
        $options = $model->sluggable();

        if ($options->skip) {
            return;
        }

        if ($model->{$options->column}) {
            return;
        }

        $slug = $this->generator->getSlug($model);

        $model->{$options->column} = $slug;
    }
}
