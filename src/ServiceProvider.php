<?php

namespace Logisticdesign\Mailup;

use Logisticdesign\Mailup\Events\Subscribe;
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

    public function boot()
    {
        parent::boot();

        Nav::extend(function ($nav) {
            $nav->tools('MailUp')
                ->route('mailup.settings.edit')
                ->icon('drawer-file');
        });
    }
}
