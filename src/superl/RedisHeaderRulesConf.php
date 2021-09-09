<?php


namespace Superl\Permission;


/**
 * WriteBy: superl
 * Date: 2021/6/15 15:01
 * Class RedisHeaderRulesConf
 * @package App\Http\Config
 * redis前缀配置
 */
class RedisHeaderRulesConf
{
    const USER_TOKEN = 'user_token';
    const TOKEN_HEAD = 'token_head';  // token的前缀
    const PERMISSION = 'permission';  // 权限
    const ROLE = 'role';  // 角色

}
