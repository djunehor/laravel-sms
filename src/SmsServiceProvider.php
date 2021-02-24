<?php

namespace Djunehor\Sms;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Filesystem $filesystem
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
        $publishTag = 'laravel-sms';
        if (app() instanceof \Illuminate\Foundation\Application) {
            $this->publishes([
                __DIR__ . '/config/laravel-sms.php' => config_path('laravel-sms.php'),
            ], $publishTag);
        }
    }
}
