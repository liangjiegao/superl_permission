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

        $this->reductionTarget();

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
                'TEMP_DB_DATABASE'   => env('DB_DATABASE'   ),
                'TEMP_DB_USERNAME'   => env('DB_USERNAME'   ),
                'TEMP_DB_PASSWORD'   => env('DB_PASSWORD'   ),
                'TEMP_DB_HOST'       => env('DB_HOST'       ),
                'TEMP_DB_PORT'       => env('DB_PORT'       ),
                'TEMP_REDIS_HOST'    => env('REDIS_HOST'    ),
                'TEMP_REDIS_PORT'    => env('REDIS_PORT'    ),
                'TEMP_REDIS_PASSWORD'=> env('REDIS_PASSWORD'),
                'DB_DATABASE'   => env('MC_DB_DATABASE'   ),
                'DB_USERNAME'   => env('MC_DB_USERNAME'   ),
                'DB_PASSWORD'   => env('MC_DB_PASSWORD'   ),
                'DB_HOST'       => env('MC_DB_HOST'       ),
                'DB_PORT'       => env('MC_DB_PORT'       ),
                'REDIS_HOST'    => env('MC_REDIS_HOST'    ),
                'REDIS_PORT'    => env('MC_REDIS_PORT'    ),
                'REDIS_PASSWORD'=> env('MC_REDIS_PASSWORD'),
                'prefix'        => 'yiyu_mc_php_database_mcgl_',
            ];
            // 使用函数更新
            UtilsClass::modifyEnv($data);
        }
    }

    public function reductionTarget(){
        $data = [
            'DB_DATABASE'   => env('TEMP_DB_DATABASE'   ),
            'DB_USERNAME'   => env('TEMP_DB_USERNAME'   ),
            'DB_PASSWORD'   => env('TEMP_DB_PASSWORD'   ),
            'DB_HOST'       => env('TEMP_DB_HOST'       ),
            'DB_PORT'       => env('TEMP_DB_PORT'       ),
            'REDIS_HOST'    => env('TEMP_REDIS_HOST'    ),
            'REDIS_PORT'    => env('TEMP_REDIS_PORT'    ),
            'REDIS_PASSWORD'=> env('TEMP_REDIS_PASSWORD'),
            'prefix'        => '',
        ];
        // 使用函数更新
        UtilsClass::modifyEnv($data);
    }

}
