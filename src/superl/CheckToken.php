<?php

namespace Superl\Permission;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckToken
{
    private $userInfo = null;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $this->checkUserToken($request->input('token', ''));
        if (empty($user)){
            $response = new Response();
            $response->setContent(UtilsClass::getCallbackJson(CodeConf::LOGIN_EXPIRE));
            return $response;
        }
        // token延期
        LoginCache::resetTokenRedis($request->input('token'));

        $request['user'] = $user;

        $response = $next($request);

        return $response;
    }

    private function checkUserToken($token){
        $this->changeDBConnect($token);
        $this->changeRedisConnect($token);
        $user = LoginCache::getUserByUserToken($token);
        return $user;
    }

    public function changeDBConnect($token){
        $list = explode('_', $token);
        if (count($list) === 1){
            return;
        }
        if ($list[0] === 'mc'){
            \Config::set('database.connections.mysql.database', env('MC_DB_DATABASE'));
            \Config::set('database.connections.mysql.username', env('MC_DB_USERNAME'));
            \Config::set('database.connections.mysql.password', env('MC_DB_PASSWORD'));
            \Config::set('database.connections.mysql.host', env('MC_DB_HOST'));
            \Config::set('database.connections.mysql.port', env('MC_DB_PORT'));
        }
    }
    public function changeRedisConnect($token){
        \Config::set('database.redis.default.prefix', 'universal_database_');
        $list = explode('_', $token);
        if (count($list) === 1){
            return;
        }
        if ($list[0] === 'mc'){
            \Config::set('database.redis.default.prefix', 'yiyu_mc_php_database_mcgl_');
            \Config::set('database.redis.default.host', env('MC_REDIS_HOST'));
            \Config::set('database.redis.default.password', env('MC_REDIS_PASSWORD'));
            \Config::set('database.redis.default.port', env('MC_REDIS_PORT'));
        }
    }
}
