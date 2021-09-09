<?php


namespace Superl\Permission;

class Rpc
{

    public static function rpcGet(string $url, array $params){
        $params['secret_key'] = md5(env('SECRET_KEY'));

        if (!empty($params)){
            $url .= '?';
        }
        foreach ($params as $key => $val) {
            $url .= "{$key}={$val}&";
        }
        $url = substr($url, 0, strlen($url) - 1);

        return json_decode(UtilsClass::curlGet($url), true);
    }

    public static function rpcPost(string $url, array $params){
        $params['secret_key'] = md5(env('SECRET_KEY'));

        return json_decode(UtilsClass::curlPost($url, json_encode($params)));
    }

}
