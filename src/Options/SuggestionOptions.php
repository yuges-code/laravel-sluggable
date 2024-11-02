<?php

namespace Yuges\Sluggable\Options;

class SuggestionOptions
{
    /**
     * Slug source
     *
     * @var array
     */
    public array $source = [];

    /**
     * Slug separator
     *
     * @var array
     */
    public array $separators = ['_', '-'];

    /**
     * Defines uniqueness slug
     *
     * @var boolean
     */
    public bool $unique = true;

    public function __construct() {

    }
}
