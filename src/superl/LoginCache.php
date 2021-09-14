<?php


namespace  Superl\Permission;

use Illuminate\Support\Facades\Redis;
use Predis\Client;
use App\Http\Config\RedisHeaderRulesConf as Rhfc;

class LoginCache
{
    const TOKEN_TIME = 604800;    // 7天

    // 获取用户信息
    public static function getUserByUserToken($token){
//        $redisConfig = self::getEnv($token);
//        $prefix = $redisConfig['prefix'];
//        $redis = new Client($redisConfig);
        $key = self::getTokenHead() . $token;
        return json_decode(Redis::get( $key), true);
    }
    // token延期
    public static function resetTokenRedis( $token ){
        // 获取uid
        $user = self::getUserByUserToken($token);

//        $redisConfig = self::getEnv($token);
//        $prefix = $redisConfig['prefix'];
//        $redis = new Client($redisConfig);

        $tKey = self::getTokenHead() . $token;

        $re = Redis::setex($tKey,  self::TOKEN_TIME, json_encode($user));
        if (!$re){
            return CodeConf::REDIS_WRITE_FAIL;
        }

        return CodeConf::SUCCESS;
    }
    public static function getTokenHead($token){
        if (self::getEnv($token) === 'mc'){
            $head = Rhfc::getConf('userToken');

        }else{
            $head = RedisHeaderRulesConf::TOKEN_HEAD;
        }
        return $head;
    }

    private static function getEnv($token){
        $list = explode('_', $token);
        if (count($list) === 1){
            return 'universal';
        }
        if ($list[0] === 'mc'){
            return 'mc';
        }

        return 'universal';
    }
}
