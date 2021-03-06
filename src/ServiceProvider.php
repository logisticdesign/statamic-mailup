<?php

namespace Logisticdesign\Mailup;

use Logisticdesign\Mailup\Events\Subscribe;
use Logisticdesign\Mailup\Tags\MailupTags;
use Statamic\Events\FormSubmitted;
use Statamic\Facades\CP\Nav;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];

    protected $listen = [
        FormSubmitted::class => [
            Subscribe::class,
        ],
    ];

    protected $tags = [
        MailupTags::class,
    ];

    public function boot()
    {
        parent::boot();

        $this
            ->bootAddonViews()
            ->bootAddonTranslations()
            ->bootAddonNav();
    }

    public function bootAddonViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/', 'mailup');

        return $this;
    }

    public function bootAddonTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mailup');

        return $this;
    }

    public function bootAddonNav()
    {
        Nav::extend(function ($nav) {
            $nav->tools('MailUp')
                ->route('mailup.settings.edit')
                ->icon('drawer-file');
        });

        return $this;
    }
}
