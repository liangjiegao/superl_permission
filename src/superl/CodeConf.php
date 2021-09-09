<?php

namespace Superl\Permission;

class CodeConf
{

    const NOT_PERMISSION  = 50029;


    public static function getConf($code, $other = array())
    {
        $config =  array(
            self::NOT_PERMISSION      => '您没有权限访问该接口！',

        );
        if (is_array($other) && count($other) > 0) {
            return (array('code'=> $code, 'msg'=> $config[$code]) + $other);
        }
        return array('code'=> $code, 'msg'=> $config[$code]);
    }
}
