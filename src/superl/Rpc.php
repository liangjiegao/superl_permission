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

        $tStart = UtilsClass::msectime();

        $response = json_decode(UtilsClass::curlGet($url), true);

        $tEnd = UtilsClass::msectime();

        $t = $tEnd - $tStart;

//        if ($t > 200){
            UtilsClass::log($url . '耗费：' . $t . 'ms', 'rpc_time');
//        }
//        $response = self::chCode($response);

        return $response;
    }

    public static function rpcPost(string $url, array $params){
        $params['secret_key'] = md5(env('SECRET_KEY'));

        return json_decode(UtilsClass::curlPost($url, json_encode($params)));
    }

    public static function chCode(array $list){
        $result = [];
        foreach ($list as $key => $value) {
//            $keyCode = mb_detect_encoding($key, array("ASCII", "UTF-8", "GBK", "GB2312", "BIG5"));
//            if ($keyCode !== "UTF-8"){
//                $key = iconv("UTF-8", $keyCode, $key);
//            }
            if (is_array($value)){
                $result[$key] = self::chCode($value);
            }else{
                $valueCode = mb_detect_encoding($value, array("ASCII", "UTF-8", "GBK", "GB2312", "BIG5"));
                if ($valueCode !== "UTF-8"){
                    $result[$key] = iconv("UTF-8", $valueCode, $value);
                }else{
                    $result[$key] = $value;
                }
            }
        }
        return $result;
    }
}
