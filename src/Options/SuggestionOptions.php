<?php

namespace Yuges\Sluggable\Options;

class SuggestionOptions
{
    /**
     * Slug source
     *
     * @var array
     */
    public array $source = ['name', 'title'];

    /**
     * Slug separator
     *
     * @var array
     */
    public array $separators = ['_', '-'];

    /**
     * Slug prefix
     *
     * @var array
     */
    public array $prefix = ['mr', 'sir'];

    /**
     * Slug suffix
     *
     * @var array
     */
    public array $suffix = [];

    /**
     * Defines uniqueness slug
     *
     * @var boolean
     */
    public bool $unique = true;

    public function __construct() {

    }
}
