<?php
namespace ybrenLib\registerCenter\core;

class FileStorgeDriver implements RegisterCenterCacheInterface {

    private $fileName;

    public function __construct(){
        $this->fileName = ROOT_PATH . "runtime".DIRECTORY_SEPARATOR."registerCenter";
        if(!is_dir($this->fileName)){
            mkdir($this->fileName , 0777 , true);
        }
    }

    public function get($key){
        $fileName = $this->fileName . DIRECTORY_SEPARATOR . $key;
        if(!file_exists($fileName)){
            return null;
        }else{
            return unserialize(file_get_contents($fileName));
        }
    }

    public function set($key , $data){
        $fileName = $this->fileName . DIRECTORY_SEPARATOR . $key;
        file_put_contents($fileName , serialize($data));
    }
}