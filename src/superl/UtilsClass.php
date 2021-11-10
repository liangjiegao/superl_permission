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
    public static $codeConf;

    static function getCallbackJson($code, $other = array()){
        if (empty(self::$codeConf)){
            return json_encode(CodeConf::getConf($code, $other), JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(self::$codeConf::getConf($code, $other), JSON_UNESCAPED_UNICODE);
        }
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

    /**
     * 根据规则，过滤不需要的数据字段
     * @param array $data    数据源
     * @param array $rules   过滤规则
     *
     * eg：
     * data的json数据格式：
     * {
            "list":[
                {
     *              "user":{
                        "nickname":"superl",
     *                  "cars":[
     *                      {
                                "name":"保时捷",
     *                          ”price“:"300w"
     *                      }
     *                  ]
     *              }
     *          },
     *          {
     *              "user":{
                        "nickname":"Kobe",
     *                  "cars":[
     *                      {
                                "name":"奔驰",
     *                          ”price“:"300w"
     *                      }
     *                  ]
     *              }
     *          }
     *      ]
     * }
     *
     * rules数据格式
     * [
     *      "list[].user.nickname",       // 去除list数组中每一个元素的user下的nickname字段
     *      "list[0].user.nickname",       // 去除list数组中第一个素下的user下的nickname字段
     *      "list[0].user.nickname",       // 去除list数组中第一个素下的user下的cars字段
     * ]
     *
     */
    private function unsetFields(array &$data, array $rules){
        foreach ($rules as $rule) {
            // 解析规则， 得到的第一个是本次需要用到的字段，第二个是递归要用的子规则
            $fields = explode('.', $rule, 2);
            // 没有符合的，结束
            if (empty($fields)){
                continue;
            }

            // 获取第一个符合的规则
            $field = $fields[0];

            // 判断是不是数组
            preg_match('[\[(.*)\]]', $field, $matches);
            // 数组
            if (!empty($matches)){
                // 字段
                $f = str_replace($matches[0], '', $field);

                // 数据中没有这个字段，结束处理
                if (!isset($data[$f])){
                    continue;
                }

                $index = $matches[1];                               // 下标

                // 如果整个数组
                if ($index === ''){
                    // 如果没有后续字段，结束处理
                    if (!isset($fields[1])){
                        continue;
                    }
                    // 递归处理整个数据的每一个元素
                    foreach ($data[$f] as &$v) {
                        $this->unsetFields($v, [$fields[1]]);
                    }
                    break;
                }
                // 如果只拿数据的某个元素
                else{
                    if (!isset($data[$f][$index])){
                        continue;
                    }
                    if (isset($fields[1])){
                        $this->unsetFields($data[$f][$index], [$fields[1]]);
                    }else{
                        unset($data[$f][$index]);
                    }
                }

            }else{  // 普通字段
                if (isset($fields[1])){
                    $this->unsetFields($data[$field], [$fields[1]]);
                }else{
                    unset($data[$field]);
                }
            }
        }
    }

}
