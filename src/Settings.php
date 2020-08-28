<?php

namespace Logisticdesign\Mailup;

use Illuminate\Support\Collection;
use Statamic\Facades\File;
use Statamic\Facades\YAML;

class Settings extends Collection
{
    /**
     * Load defaults collection.
     *
     * @param  mixed  $items
     */
    public function __construct($items = [])
    {
        $items = $items + $this->defaults();

        parent::__construct($items);
    }

    /**
     * Load defaults collection.
     *
     * @param  mixed  $items
     */
    public static function load($items = [])
    {
        return new static($items);
    }

    /**
     * Get settings yaml path.
     *
     * @return string
     */
    protected function path()
    {
        return base_path('content/mailup.yaml');
    }

    /**
     * Get defaults yaml path.
     *
     * @return string
     */
    protected function defaultsPath()
    {
        return __DIR__.'/../content/mailup.yaml';
    }

    /**
     * Save settings to yaml.
     *
     * @return void
     */
    public function save()
    {
        File::put($this->path(), YAML::dump($this->items));
    }

    /**
     * Get defaults from yaml.
     *
     * @return array
     */
    public function defaults()
    {
        $settings = YAML::file($this->path())->parse();
        $defaults = YAML::file($this->defaultsPath())->parse();

        return collect($defaults)->merge($settings)->all();
    }
}
