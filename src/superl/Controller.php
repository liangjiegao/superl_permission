<?php

namespace Superl\Permission;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function paramsFilter(array $formatList, Request $request, Service $service){
        $resultTemp = ['code' => CodeConf::SUCCESS, 'result' => []];
        $userInfo = $request->input('user');
        if (!is_array($userInfo)){
            $userInfo = DB::table('user') -> where(['uid' => $userInfo])  -> first() ;
            $userInfo = UtilsClass::objectToArray($userInfo);
        }

        $result = [];
        foreach ($formatList as $format) {

            $inputParam     = $format['input'];
            $type           = $format['type'];
            $outputParam    = $format['output'] ?? $format['input'];
            $empty          = $format['empty']  ?? false;
            $length         = $format['length'] ?? 0;

            // uid
            if ($inputParam == 'uid'){
                $result['uid'] = $userInfo['uid'] ?? null;
                continue;
            }
            // user_key
            if ($inputParam == 'user_key'){
                $result['user_key'] = $userInfo['user_key'] ?? null;
                $service->setUserKey($result['user_key']);

                continue;
            }
            // comp_id
            if ($inputParam == 'comp_id'){
                $result['comp_uid'] = $userInfo['comp_uid'] ?? null;

                continue;
            }
            // comp_key
            if ($inputParam == 'comp_key'){
                $result['comp_key'] = $userInfo['comp_key'] ?? null;
                $service->setCompKey($result['comp_key']);

                continue;
            }

            if ($inputParam == 'role'){
                $result['role'] = isset($userInfo['role']) ? intval($userInfo['role']) : 0;
                continue;
            }


            $getParam = $request->input($inputParam);

            // ?????????
            if (!isset($getParam)){

                if ($empty){
                    // ??????????????????
                    $result[$outputParam] = null;

                    continue;
                }

                $resultTemp['code'] = CodeConf::PARAM_EMPTY;
                return $resultTemp;

            }
            // ??????????????????
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
                    //?????? json ??????
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
