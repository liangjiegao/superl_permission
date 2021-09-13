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
        $user = LoginCache::getUserByUserToken($token);
        return $user;
    }
}
