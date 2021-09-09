<?php


namespace App\Http\Service\Cache;


use App\Http\Config\RedisHeaderRulesConf;
use Predis\Client;

class LoginCache
{
    // 获取用户信息
    public static function getUserByUserToken($token){
        $redisConfig = config('database.redis.default');
        $prefix = 'universal_database_';
        $redis = new Client($redisConfig);
        return json_decode($redis->get( $prefix . RedisHeaderRulesConf::TOKEN_HEAD . $token), true);
    }

}
