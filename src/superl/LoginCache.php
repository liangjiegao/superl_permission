<?php


namespace  Superl\Permission;

use Predis\Client;

class LoginCache
{
    const TOKEN_TIME = 604800;    // 7天

    // 获取用户信息
    public static function getUserByUserToken($token){
        $redisConfig = config('database.redis.default');
        $prefix = config('database.redis.options.prefix', 'universal_database_');
        $redis = new Client($redisConfig);

        $key = $prefix . self::getTokenHead() . $token;
        echo $key;
        return json_decode($redis->get( $key), true);
    }
    // token延期
    public static function resetTokenRedis( $token ){
        // 获取uid
        $user = self::getUserByUserToken($token);

        $redisConfig = config('database.redis.default');
        $prefix = config('database.redis.options.prefix', 'universal_database_');
        $redis = new Client($redisConfig);

        $tKey = $prefix . self::getTokenHead() . $token;

        $re = $redis->setex($tKey,  self::TOKEN_TIME, json_encode($user));
        if (!$re){
            return CodeConf::REDIS_WRITE_FAIL;
        }

        return CodeConf::SUCCESS;
    }
    public static function getUserToken($userKey){
        $redisConfig = config('database.redis.default');
        $prefix = config('database.redis.options.prefix', 'universal_database_');
        $redis = new Client($redisConfig);
        $key = $prefix . RedisHeaderRulesConf::USER_TOKEN;
        return $redis->hget($key, $userKey);
    }

    public static function getTokenHead(){
        try {
            $head = app_path('Http\Config\RedisHeaderRulesConf')::getConf('userToken');
        }catch (\Exception $e){

        }
        if (!empty($head)){
            $head = RedisHeaderRulesConf::TOKEN_HEAD;
        }

        return $head;
    }
}
