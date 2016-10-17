<?php

namespace Rebel\Component\Rbac\Traits;

use Rebel\Component\Rbac\Bootstraper;

trait CacheRefresher
{
    public static function bootCacheRefresher()
    {
        static::created(function ($model) {
            $model->forgetCachePermissions();
        });

        static::updated(function ($model) {
            $model->forgetCachePermissions();
        });

        static::deleted(function ($model) {
            $model->forgetCachePermissions();
        });
    }

    public function forgetCachePermissions()
    {
        return app(Bootstraper::class)->forgetCachedPermissions();
    }
}