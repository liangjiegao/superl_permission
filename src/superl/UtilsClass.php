<?php


namespace Superl\Permission;


class UtilsClass
{


    static function getCallbackJson($code, $other = array()){
        return json_encode(CodeConf::getConf($code, $other), JSON_UNESCAPED_UNICODE);
    }

}
