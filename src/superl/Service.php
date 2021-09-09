<?php


namespace Superl\Permission;


abstract class Service
{
    protected $compKey;
    protected $userKey;


    protected $_code = CodeConf::SUCCESS;

    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return mixed
     */
    public function getCompKey()
    {
        return $this->compKey;
    }

    /**
     * @param mixed $compKey
     */
    public function setCompKey($compKey): void
    {
        $this->compKey = $compKey;
    }

    /**
     * @return mixed
     */
    public function getUserKey()
    {
        return $this->userKey;
    }

    /**
     * @param mixed $userKey
     */
    public function setUserKey($userKey): void
    {
        $this->userKey = $userKey;
    }



    public function buildKey(string $salt){
        return md5($salt . rand(10000, 99999) . time());
    }

    public function getServiceInstance($clazz){
        $service = new $clazz();
        $service->setCompKey($this->compKey);
        $service->setUserKey($this->userKey);
        return $service;
    }

    public $serviceMap;

    public function getSingleServiceInstance($clazz){
        if (!isset($serviceMap[$clazz])){
            $serviceMap[$clazz] = new $clazz();
            $serviceMap[$clazz]->setCompKey($this->compKey);
            $serviceMap[$clazz]->setUserKey($this->userKey);
        }
        return $serviceMap[$clazz];
    }




}
