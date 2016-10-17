<?php

namespace Rebel\Component\Setting;

use Illuminate\Support\ServiceProvider;
use Rebel\Component\Setting\Contracts\Setting as SettingInterface;
use Rebel\Component\Setting\SettingManager;

class RebelSettingServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $configPath = __DIR__ . '/../config/setting.php';
        $this->publishes([
            $configPath => config_path('setting.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        $this->mergeConfigFrom($configPath, 'setting');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SettingInterface::class, function($app) {
            return new SettingManager($app->config->get('setting'), $app['cache.store']);
        });
    }
}
