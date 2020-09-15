<?php
namespace ybrenLib\registerCenter\core\driver\eureka\discovery;

use ybrenLib\registerCenter\core\bean\Instance;

class RandomStrategy implements DiscoveryStrategy{

    /**
     * @param array $instances
     * @return Instance
     */
    public function getInstance($instances){
        $upInstances = $this->getUpInstances($instances);
        if(empty($upInstances)){
            // 无视下线服务
            $upInstances = $instances;
        }
        return $upInstances[rand(0 , (count($upInstances) - 1))];
    }

    private function getUpInstances($instances){
        $result = [];
        if(!empty($instances)){
            foreach ($instances as $instance){
                if($instance->getStatus() != "UP"){
                    continue;
                }
                $result[] = $instance;
            }
        }
        return $result;
    }
}