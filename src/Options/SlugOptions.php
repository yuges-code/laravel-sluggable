<?php

namespace Yuges\Sluggable\Options;

class SlugOptions
{
    /**
     * Slug column
     *
     * @var string|null
     */
    public ?string $column = 'slug';

    /**
     * Slug union models
     *
     * @var array
     */
    public array $union;

    /**
     * Slug source
     *
     * @var array
     */
    public array $source = [];

    /**
     * Slug separator
     *
     * @var string
     */
    public string $separator = '-';

    /**
     * Defines uniqueness slug
     *
     * @var boolean
     */
    public bool $unique = true;

    /**
     * Skip slug generate
     *
     * @var boolean
     */
    public bool $skip = false;

    public function __construct() {

    }
}
