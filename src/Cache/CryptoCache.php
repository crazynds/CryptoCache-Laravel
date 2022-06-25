<?php

namespace Crazynds\CryptoCache\Cache;

use Illuminate\Cache\CacheLock;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\InteractsWithTime;

class CryptoCache implements Store,LockProvider{
    use InteractsWithTime;

    private $cacheName;


    public function __construct($cacheName){
        $this->cacheName = $cacheName;
    }

    private function decrypt($data,$many){
        try{
            if($many){
                foreach($data as $key=>$val){
                    if(isset($val)){
                        $d = explode(';',$val);
                        $data[$key] = unserialize(Crypt::decryptString($d[1]));
                    }
                }
            }else{
                if(isset($data)){
                    $d = explode(';',$data);
                    $data= unserialize(Crypt::decryptString($d[1]));
                }
            }
        }catch(DecryptException $e){
            $data = null;
        }
        return $data;
    }

    private function encrypt($data,$many,$time=-1){
        try{
            if($many){
                foreach($data as $key=>$val){
                    if(isset($val))
                        $data[$key] = $time.';'.Crypt::encryptString(serialize($val));
                }
            }else{
                if(isset($data))
                    $data=$time.';'.Crypt::encryptString(serialize($data));
            }
        }catch(EncryptException $e){}
        return $data;
    }

    public function get($key) {
        $data = Cache::store($this->cacheName)->get($key);
        return $this->decrypt($data,false);
    }
    public function many(array $keys) {
        $data = Cache::store($this->cacheName)->many($keys);
        return $this->decrypt($data,true);
    }
    public function put($key, $value, $seconds) {
        return Cache::store($this->cacheName)->put($key,$this->encrypt($value,false,$this->expiration($seconds)), $seconds);
    }
    public function putMany(array $values, $seconds) {
        return Cache::store($this->cacheName)->put($this->encrypt($values,true,$this->expiration($seconds)), $seconds);
    }
    public function increment($key, $value = 1) {
        $data = Cache::store($this->cacheName)->get($key);
        $time = explode(';',$data)[0];
        $data = $this->decrypt($data,false);
        $data = ((int) $data) + $value;
        if($time==-1)$seconds = -1;
        else $seconds = Carbon::now()->diffInSeconds(Carbon::parse((int)$time));
        $this->put($key,$data,$seconds);
    }
    public function decrement($key, $value = 1) {
        $this->increment($key,$value*-1);
    }
    public function forever($key, $value) {
        return Cache::store($this->cacheName)->forever($key, $this->encrypt($value,false));
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

    public function lock($name, $seconds = 0, $owner = null)
    {
        $cache = Cache::store($this->cacheName);
        if(method_exists($cache,'lock'))
            return $cache->lock($name,$owner);
        return new CacheLock($this, $name, $seconds, $owner);
    }

    public function restoreLock($name, $owner)
    {
        $cache = Cache::store($this->cacheName);
        if(method_exists($cache,'restoreLock'))
            return $cache->restoreLock($name,$owner);
        return $this->lock($name, 0, $owner);
    }

    protected function expiration($seconds)
    {
        $time = $this->availableAt($seconds);

        return $seconds === 0 || $time > 9999999999 ? 9999999999 : $time;
    }

}

