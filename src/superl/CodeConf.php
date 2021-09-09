<?php

namespace Superl\Permission;

class CodeConf
{

    const SUCCESS           = 10000;

    const PARAM_TYPE_ERROR  = 40001;
    const PARAM_OVER_LENGTH = 40002;
    const PARAM_EMPTY       = 40003;

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
            self::PARAM_TYPE_ERROR      => '参数类型异常！',
            self::PARAM_OVER_LENGTH     => '参数长度超出限制！',
            self::PARAM_EMPTY           => '参数异常！',

        );
        $msg = $config[$code] ?? null;

        return (array('code'=> $code, 'msg'=> $msg) + $other);
    }
}
