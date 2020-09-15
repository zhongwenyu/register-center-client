<?php
namespace ybrenLib\registerCenter\core;

class YacStorgeDriver implements RegisterCenterCacheInterface {

    private $yac;

    private $key = "~registerCenterCache";

    public function __construct(){
        $this->yac = new \Yac();
    }

    public function get($key){
        $val = $this->yac->get($this->key . $key);
        return is_null($val) ? null : unserialize($val);
    }

    public function set($key , $data){
        return $this->yac->set($this->key . $key , serialize($data));
    }
}