<?php


namespace App\Http\Service\Cache;


use Predis\Client;

class RoleCache
{

    public static function getRoleCache(string $userKey){
        $redisConfig =  [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ];
        $redis = new Client($redisConfig);
        return json_decode($redis->hget( 'role' , $userKey), true);
    }
}
