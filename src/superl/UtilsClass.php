<?php


namespace Superl\Permission;


class UtilsClass
{
    /**
     * 写入日志
     * @param $content string 内容
     * @param $filename string 文件名
     * @param string $type
     * @param bool $isName
     * @return bool
     */
    public static function log($content, $filename, $type = 'info', $isName = true) {
        $logPath = dirname(dirname(dirname(__FILE__))).'/Logs/';

        if( !file_exists($logPath) ){
            mkdir($logPath, 0777);
            chmod($logPath, 0777);
        }

        if(is_array($content) || is_object($content)){
            $content = json_encode($content,JSON_UNESCAPED_UNICODE);
        }

        if ($isName) {
            $log_file = $logPath . $filename . "-" . date("Ymd") . ".log";
        } else {
            $log_file = $logPath . $filename . "-" . ".log";
        }
        $date = date("Y-m-d H:i:s");
        return file_put_contents($log_file, $date . " [$type] " . $content . "\n", FILE_APPEND);
    }

    /**
     * 发送get请求
     * @param string $url 链接
     * @return bool|mixed
     */
    public static function curlGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'ERROR ' . curl_error($curl);
        }
        curl_close($curl);
        return $output;
    }

    /**
     * 发送post请求
     * @param string $url 链接
     * @param string $data 数据
     * @return bool|mixed
     */
    public static function curlPost($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    static function getCallbackJson($code, $other = array()){
        return json_encode(CodeConf::getConf($code, $other), JSON_UNESCAPED_UNICODE);
    }
    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    static function objectToArray($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }

        return $obj;
    }
    public static function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        return $msectime;
    }
}
