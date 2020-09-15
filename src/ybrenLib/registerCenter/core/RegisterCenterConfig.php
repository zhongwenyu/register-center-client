<?php
namespace ybrenLib\registerCenter\core;

use ybrenLib\registerCenter\core\exception\ConfigException;

class RegisterCenterConfig{

    public static function getAddress(){
        if(defined("REGISTER_CENTER_ADDRESS")){
            return REGISTER_CENTER_ADDRESS;
        }else if(defined("ROOT_PATH") && file_exists(ROOT_PATH . "registerCenter.json")){
            $registerCenterData = json_decode(ROOT_PATH . "registerCenter.json" , true);
            if(!isset($registerCenterData['address']) || empty($registerCenterData['address'])){
                throw new ConfigException("register center address is not found in registerCenter.json");
            }
            return $registerCenterData['address'];
        }else if(class_exists("Yaconf")){
            $yaconf = new \Yaconf();
            $eurekaUrls = $yaconf::get("database.eureka_server_url" , null);
            if(is_null($eurekaUrls) || empty($eurekaUrls)){
                throw new ConfigException("eureka_server_url is not found in yaconf");
            }
            return $eurekaUrls;
        }
        throw new ConfigException("register center address is not config");
    }
}