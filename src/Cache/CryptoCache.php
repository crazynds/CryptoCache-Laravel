<?php

namespace Crazynds\CryptoCache\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\InteractsWithTime;
use Nette\NotSupportedException;

class CryptoCache implements Store{
    use InteractsWithTime;

    private $cacheName;


    public function __construct($cacheName){
        $this->cacheName = $cacheName;
    }

    private function decrypt($data){
        if(typeOf($data)=='array'){
            foreach($data as $key=>$val){
                $data[$key] = Crypt::decryptString($val);
            }
        }else{
            return Crypt::decryptString($data);
        }
    }

    private function encrypt($data){
        if(typeOf($data)=='array'){
            foreach($data as $key=>$val){
                if(is_set($val))
                    $data[$key] = Crypt::encryptString($val);
            }
        }else{
            if(is_set($data))
                return Crypt::encryptString($data);
        }
    }

    public function get($key) {
        $data = Cache::store($this->cacheName)->get($key);
        return $this->decrypt($data);
    }
    public function many(array $keys) {
        $data = Cache::store($this->cacheName)->many($keys);
        return $this->decrypt($data);
    }
    public function put($key, $value, $seconds) {
        Cache::store($this->cacheName)->put($key.'_time_',expiration($seconds));
        return Cache::store($this->cacheName)->put($key,$this->encrypt($value), $seconds);
    }
    public function putMany(array $values, $seconds) {
        foreach($values as $key=>$val){
            Cache::store($this->cacheName)->put($key.'_time_',expiration($seconds));
        }
        return Cache::store($this->cacheName)->put($this->encrypt($values), $seconds);
    }
    public function increment($key, $value = 1) {
        $data = $this->get($key);
        $time = Cache::store($this->cacheName)->get($key.'_time_');
        $data = ((int) $data) + $value;
        $seconds = Carbon::now()->diffInSeconds(Carbon::parse($time));
        $this->put($key,$data,$seconds);
    }
    public function decrement($key, $value = 1) {
        $this->increment($key,$value*-1);
    }
    public function forever($key, $value) {
        Cache::store($this->cacheName)->put($key.'_time_',-1);
        return Cache::store($this->cacheName)->forever($key, $this->encrypt($value));
    }
    public function forget($key) {
        return Cache::store($this->cacheName)->forget($key);
    }
    public function flush() {
        return Cache::store($this->cacheName)->flush();
    }
    public function getPrefix() {
        return Cache::store($this->cacheName)->getPrefix();
    }



    protected function expiration($seconds)
    {
        $time = $this->availableAt($seconds);

        return $seconds === 0 || $time > 9999999999 ? 9999999999 : $time;
    }

}

