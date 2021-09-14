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
    public static function getUserToken($userKey){
//        $redisConfig = self::getEnv($token);
//        $prefix = $redisConfig['prefix'];
//        $redis = new Client($redisConfig);
        $key = self::getUserTokenHead();
        return Redis::hget($key, $userKey);
    }

    public static function getTokenHead(){
        try {
            $head = Rhfc::getConf('userToken');
        }catch (\Exception $e){

        }
        if (empty($head)){
            $head = RedisHeaderRulesConf::TOKEN_HEAD;
        }

        return $head;
    }
    public static function getUserTokenHead(){
        try {
            $head = Rhfc::getConf('user_uid_token');
        }catch (\Exception $e){

        }
        if (empty($head)){
            $head = RedisHeaderRulesConf::USER_TOKEN;
        }

        return $head;
    }
    private static function getEnv($token){
        $data = [
            'prefix' => 'universal_database_',
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ];

        $list = explode('_', $token);
        if (count($list) === 1){
            return $data;
        }
        if ($list[0] === 'mc'){
            $data = [
                'prefix' => 'yiyu_mc_php_database_mcgl_',
                'host' => env('MC_REDIS_HOST', '127.0.0.1'),
                'password' => env('MC_REDIS_PASSWORD', null),
                'port' => env('MC_REDIS_PORT', '6379'),
                'database' => env('REDIS_DB', '0'),
            ];
        }

        return $data;
    }
}
