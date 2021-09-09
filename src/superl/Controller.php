<?php

namespace Superl\Permission;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function paramsFilter(array $formatList, Request $request, Service $service){
        $resultTemp = ['code' => CodeConf::SUCCESS, 'result' => []];
        $userInfo = $request->input('user', []);

        $result = [];
        foreach ($formatList as $format) {

            $inputParam     = $format['input'];
            $type           = $format['type'];
            $outputParam    = $format['output'] ?? $format['input'];
            $empty          = $format['empty']  ?? false;
            $length         = $format['length'] ?? 0;

            // uid
            if ($inputParam == 'uid'){
                $result['uid'] = isset($userInfo['uid']) ? intval($userInfo['uid']) : 0;
                $service->setUid($result['uid']);

                continue;
            }
            // comp_id
            if ($inputParam == 'comp_id'){
                $result['comp_id'] = isset($userInfo['comp_id']) ? intval($userInfo['comp_id']) : 0;
                $service->setCompId($result['comp_id']);

                continue;
            }

            if ($inputParam == 'role'){
                $result['role'] = isset($userInfo['role']) ? intval($userInfo['role']) : 0;
                continue;
            }

            if ($inputParam == 'user_key'){
                $result['user_key'] = isset($userInfo['user_key']) ? $userInfo['user_key'] : '';
                continue;
            }

            $getParam = $request->input($inputParam);

            // 可空值
            if (!isset($getParam)){

                if ($empty){
                    // 数据类型限制
                    $result[$outputParam] = null;

                    continue;
                }

                $resultTemp['code'] = CodeConf::PARAM_EMPTY;
                return $resultTemp;

            }
            // 数据类型限制
            switch ($type){

                case 'string':
                    if (!is_string($getParam) && !is_int($getParam)){

                        $resultTemp['code'] = CodeConf::PARAM_TYPE_ERROR;
                        return $resultTemp;
                    }
                    if ($length !== 0 && mb_strlen($getParam) > $length){
                        $resultTemp['code'] = CodeConf::PARAM_OVER_LENGTH;
                        return $resultTemp;
                    }
                    break;
                case 'array':
                    //兼容 json 格式
                    $getParam = !is_array($getParam) ? json_decode($getParam, true) : $getParam;
                    if (!is_array($getParam)){
                        $resultTemp['code'] = CodeConf::PARAM_TYPE_ERROR;
                        return $resultTemp;
                    }
                    break;
            }

            $result[$outputParam] = $getParam;

        }
        $resultTemp['result'] = $result;

        return $resultTemp;
    }

}
