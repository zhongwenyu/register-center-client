<?php
namespace ybrenLib\registerCenter\core;

interface RegisterCenterCacheInterface{

    function get($key);

    function set($key , $data);
}