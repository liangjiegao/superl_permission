<?php


namespace  Superl\Permission;

use Predis\Client;

class RoleCache
{

    public static function getRoleCache(string $userKey){
        $redisConfig = config('database.redis.default');
        $redis = new Client($redisConfig);
        return json_decode($redis->hget( RedisHeaderRulesConf::ROLE , $userKey), true);
    }
}
