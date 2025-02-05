<?php

namespace Yuges\Sluggable\Traits;

use Yuges\Sluggable\Interfaces\Sluggable;
use Yuges\Sluggable\Options\SlugOptions;

trait HasSluggableModel
{
    protected Sluggable $model;

    protected SlugOptions $options;

    public function setModel(Sluggable $model): self
    {
        $this->model = $model;
        $this->options = $model->sluggable();

        return $this;
    }
}
