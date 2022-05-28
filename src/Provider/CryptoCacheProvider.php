<?php

namespace Crazynds\CryptoCache\Provider;

use CryptoCache\Cache\CryptoCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class CryptoCacheServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function () {
            Cache::extend('crypto-cache', function ($app) {
                $value = config('cache.stores.crypto-cache');
                if(isset($value) && isset($value['cache']))
                    $cacheName = $value['cache'];
                else
                    $cacheName = 'file';
                return Cache::repository(new CryptoCache($cacheName));
            });
         });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
