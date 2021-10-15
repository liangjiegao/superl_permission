<?php

namespace Superl\Permission;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

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
        $this->envChange($request->input('token'));

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

    private function envChange($token){
        $_REQUEST['USER_DOMAIN'] = env('USER_DOMAIN', 'http://127.0.0.1');

        $list = explode('_', $token);
        if (count($list) === 1){
            return;
        }
        if ($list[0] === 'mc'){
            if ($list[1] === 'boao'){
                $prefix = 'BOAO';
                $this->changeMCDBConnect($prefix);
                $this->changeMCRedisConnect($prefix);
                $this->changeMCOutDomain();
            }elseif($list[1] === 'lutai'){
                $prefix = 'LUTAI';
                $this->changeMCDBConnect($prefix);
                $this->changeMCRedisConnect($prefix);
                $this->changeMCOutDomain();
            }elseif($list[1] === 'test'){
                $prefix = 'TEST';
                $this->changeMCDBConnect( $prefix);
                $this->changeMCRedisConnect( $prefix);
                $this->changeMCOutDomain();
            }

        }
    }

    private function checkUserToken($token){
        $user = LoginCache::getUserByUserToken($token);
        return $user;
    }

    public function changeMCDBConnect($prefix){
        \Config::set('database.connections.mysql.database', env($prefix . '_DB_DATABASE'));
        \Config::set('database.connections.mysql.username', env($prefix . '_DB_USERNAME'));
        \Config::set('database.connections.mysql.password', env($prefix . '_DB_PASSWORD'));
        \Config::set('database.connections.mysql.host', env($prefix . '_DB_HOST'));
        \Config::set('database.connections.mysql.port', env($prefix . '_DB_PORT'));
    }
    public function changeMCRedisConnect($prefix){
        \Config::set('database.redis.default.prefix',   env($prefix . '_REDIS_PREFIX'));
        \Config::set('database.redis.default.host',     env($prefix . '_REDIS_HOST'));
        \Config::set('database.redis.default.password', env($prefix . '_REDIS_PASSWORD'));
        \Config::set('database.redis.default.port',     env($prefix . '_REDIS_PORT'));
    }

    public function changeMCOutDomain(){
        $_REQUEST['USER_DOMAIN'] = env('MC_DOMAIN', 'http://127.0.0.1');
    }
}
