<?php
namespace ybrenLib\registerCenter\core\bean;

class Instance implements \JsonSerializable{

    private $name;

    private $ip;

    private $port;

    private $status;

    private $address;

    private $metaData;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * @param mixed $metaData
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
    }

    public function toArray(){
        $data = [];
        foreach ($this as $key=>$val){
            $getMethodName = $this->getGetMethodName($key);
            if ($val !== null) $data[$key] = method_exists($this , $getMethodName) ? $this->$getMethodName() : $this->$key;
        }
        return $data;
    }

    public function jsonSerialize() {
        return $this->toArray();
    }

    /**
     * 获取get方法名称
     * @param $key
     * @return string
     */
    protected function getGetMethodName($key){
        return "get".ucfirst($key);
    }
}