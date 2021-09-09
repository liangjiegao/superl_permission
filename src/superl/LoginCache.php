<?php


namespace App\Http\Service\Cache;


use Predis\Client;

class LoginCache
{
    // 获取用户信息
    public static function getUserByUserToken($token){
        $redisConfig =  [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ];
        $prefix = 'universal_database_';
        $redis = new Client($redisConfig);
        return json_decode($redis->get( $prefix . 'user_token' . $token), true);
    }

}
