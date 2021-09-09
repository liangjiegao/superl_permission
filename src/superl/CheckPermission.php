<?php

namespace Superl\Permission;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckPermission
{
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
        $user = LoginCache::getUserByUserToken($request->input('token'));
        $roles = RoleCache::getRoleCache($user['user_key']);

        if (empty($roles)){
            $response = new Response();
            $response->setContent(UtilsClass::getCallbackJson(CodeConf::NOT_PERMISSION));
            return $response;
        }
        $permissions = [];
        foreach ($roles as $role) {

            // 获取当前用户的所有权限
            $ps = PermissionCache::getPermissionCache($role['r_key'], $user['comp_key']);

            if (!empty($ps)){
                $permissions = array_merge($permissions, $ps);
            }
        }

        // 对比当前用户是否有权限访问
        // 获取当前路由
        $route = $request->path();
        $details = explode('/', $route);
        $modular    = $details[0] ?? '';
        $interface  = $details[1] ?? '';
        foreach ($permissions as $permission) {
            // 有权限
            if ($permission['module'] === $modular && $permission['action'] === $interface){
                return $next($request);
            }
        }

        // 没有权限
        $response = new Response();
        $response->setContent(UtilsClass::getCallbackJson(CodeConf::NOT_PERMISSION));
        return $response;
    }


}
