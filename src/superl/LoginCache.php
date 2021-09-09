<?php


namespace  Superl\Permission;


use Predis\Client;

class LoginCache
{
    const TOKEN_TIME = 604800;    // 7天

    // 获取用户信息
    public static function getUserByUserToken($token){
        $redisConfig = config('database.redis.default');
        $prefix = 'universal_database_';
        $redis = new Client($redisConfig);
        return json_decode($redis->get( $prefix . RedisHeaderRulesConf::TOKEN_HEAD . $token), true);
    }
    // token延期
    public static function resetTokenRedis( $token ){
        // 获取uid
        $user = self::getUserByUserToken($token);

        $redisConfig = config('database.redis.default');
        $prefix = 'universal_database_';
        $redis = new Client($redisConfig);

        $tKey = $prefix . RedisHeaderRulesConf::TOKEN_HEAD . $token;

        $re = $redis->setex($tKey,  self::TOKEN_TIME, json_encode($user));
        if (!$re){
            return CodeConf::REDIS_WRITE_FAIL;
        }

        return CodeConf::SUCCESS;
    }

}
