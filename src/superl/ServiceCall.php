<?php


namespace Superl\Permission;

class ServiceCall
{

    protected $params;


//    public function __construct($userKey, $params){
//        $this->token = LoginCache::getUserToken($userKey);
//        $this->params = $params;
//        $this->params['token'] = $this->token;
//    }

    public function __construct(){

    }

    public function post(){
        $url = '完整的url';

        $response = Rpc::rpcPost($url, $this->params);

        return $response;
    }
    public function get(){
        $url = '完整的url';

        $response = Rpc::rpcGet($url, $this->params);

        return $response;
    }

    public function setParams(array $params){
        $this->params = $params;
    }
}
