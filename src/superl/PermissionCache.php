<?php


namespace  Superl\Permission;


use App\Http\Config\RedisHeaderRulesConf;
use Predis\Client;

class PermissionCache
{

    public static function getPermissionCache(string $rKey, string $compKey){
        $redisConfig = config('database.redis.default');
        $redis = new Client($redisConfig);
        return json_decode($redis->hget( RedisHeaderRulesConf::PERMISSION . $compKey , $rKey), true);
    }
}
