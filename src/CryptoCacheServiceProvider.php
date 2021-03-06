<?php

namespace Crazynds\CryptoCache;

use Crazynds\CryptoCache\Cache\CryptoCache;
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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::extend('crypto-cache', function ($app) {
            $value = config('cache.stores.crypto-cache');
            if(isset($value) && isset($value['cache']))
                $cacheName = $value['cache'];
            else
                $cacheName = 'file';
            return Cache::repository(new CryptoCache($cacheName));
        });
    }
}
