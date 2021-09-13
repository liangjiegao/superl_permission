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
        $this->checkTarget($token);
        $user = LoginCache::getUserByUserToken($token);
        return $user;
    }
    private function checkTarget($token){
        $list = explode('_', $token);
        if (count($list) === 1){
            return;
        }

        if ($list[0] === 'mc'){
            $data = [
                'DB_DATABASE' => 'yiyu_mcglv2',
                'DB_USERNAME' => 'yiyu',
                'DB_PASSWORD' => 'Yi134=&156yU',
                'DB_HOST' => '42.192.223.252',
                'DB_PORT'=> '3306',
                'REDIS_HOST'=> '42.192.223.252',
                'REDIS_PORT'=>'6379',
                'REDIS_PASSWORD' => '66gJYuyi66',
            ];
            // 使用函数更新
            UtilsClass::modifyEnv($data);
        }

    }

}
