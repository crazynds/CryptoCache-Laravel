<?php


namespace Crazynds\CryptoCache;

use Illuminate\Support\Facades\Facade;

class CryptoCache extends Facade{

    protected static function getFacadeAccessor()
    {
        return 'crypto-cache';
    }

}


