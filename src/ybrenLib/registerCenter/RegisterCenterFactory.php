<?php
namespace ybrenLib\registerCenter;

use ybrenLib\registerCenter\core\RegisterCenterClient;

class RegisterCenterFactory{

    /**
     * @return RegisterCenterClient
     */
    public static function build(){
        return new RegisterCenterClient();
    }
}