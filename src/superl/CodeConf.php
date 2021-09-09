<?php

namespace Superl\Permission;

class CodeConf
{

    const SUCCESS           = 10000;
    const NOT_PERMISSION    = 50001;
    const LOGIN_EXPIRE      = 50002;
    const REDIS_WRITE_FAIL  = 50003;


    public static function getConf($code, $other = array())
    {
        $config =  array(
            self::SUCCESS               => '成功！',
            self::NOT_PERMISSION        => '您没有权限访问该接口！',
            self::LOGIN_EXPIRE          => '登录过期！',
            self::REDIS_WRITE_FAIL      => 'Redis写入失败！',

        );
        if (is_array($other) && count($other) > 0) {
            return (array('code'=> $code, 'msg'=> $config[$code]) + $other);
        }
        return array('code'=> $code, 'msg'=> $config[$code]);
    }
}
