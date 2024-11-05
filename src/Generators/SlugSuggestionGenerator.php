<?php

namespace Yuges\Sluggable\Generators;

use Illuminate\Support\Collection;
use Yuges\Sluggable\Traits\HasSluggableModel;

class SlugSuggestionGenerator
{
    use HasSluggableModel;

    public function getSlugs(): Collection
    {
        $collection = new Collection();

        return $collection;
    }
}
