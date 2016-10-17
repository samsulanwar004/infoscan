<?php

namespace Rebel\Component\Util;

use Illuminate\Support\ServiceProvider;
use Rebel\Component\Util\Supports\Robber;

class UtilServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerScrapper();
    }

    protected function registerScrapper()
    {
        $this->app->singleton('rebel.util.robber', function($app) {
            return new Robber;
        });
    }
}