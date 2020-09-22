<?php
namespace ybrenLib\registerCenter\driver\eureka\utlis;

class StringUtil{

    public static function pwdHttp($url){
        $array = [];
        foreach (explode(",",$url) as $str){
            $pattern = "/(http|https):\/\/(.*):(.*)@(.*)/";
            $match = [];
            preg_match($pattern, $str, $match);
            if(empty($match)){
                $array[] = $str;
            }else{
                $array[] = str_replace($match[3] , "***" , $str);
            }
        }
        return implode(",",$array);
    }
}