# CryptoCache for Laravel

[![Latest Stable Version](http://poser.pugx.org/crazynds/crypto-cache/v)](https://packagist.org/packages/crazynds/crypto-cache)
[![Total Downloads](http://poser.pugx.org/crazynds/crypto-cache/downloads)](https://packagist.org/packages/crazynds/crypto-cache)
[![License](http://poser.pugx.org/crazynds/crypto-cache/license)](https://packagist.org/packages/crazynds/crypto-cache)

This cache encrypts the data using the standard laravel library and saves it in another cache, allowing you to cache sensitive data, preventing any intrusion from leaking that data.

## Instation

1.  Install the package

```shell
composer require crazynds/crypto-cache
```

2. 

``` php
// Change the CACHE_DRIVER in .env or change 'default' value in config/cache.php to 'crypto-cache'

'default' => env('CACHE_DRIVER', 'crypto-cache'),


// Add these lines in config/cache.php in stores array:

'stores' =>[
    // ...

    'crypto-cache'=>[
        'driver' => 'crypto-cache',
        'cache' => '<cache to store the data>'
    ],

],
```

## Tested and supported versions of Laravel

-   9.x
-   8.x




