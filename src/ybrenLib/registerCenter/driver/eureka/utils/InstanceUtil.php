<?php
namespace ybrenLib\registerCenter\driver\eureka\utlis;

use ybrenLib\registerCenter\core\bean\Instance;

class InstanceUtil{

    public static function formatToMetedata(Instance $instance){
        $host = $instance->getIp() . ":" . $instance->getPort();
        $name = $instance->getName();
        $microTime = intval(microtime(true) * 1000);
        $metaData = $instance->getMetaData();
        $data = [
            'instanceId' => $host,
            'hostName' => $instance->getIp(),
            'app' => $name,
            'ipAddr' => $instance->getIp(),
            'status' => 'UP',
            'overriddenstatus' => 'UNKNOWN',
            'vipAddress' => $name,
            'secureVipAddress' => $name,
            'statusPageUrl' => 'http://' . $host . "/info",
            'homePageUrl' => 'http://' . $host .  "/",
            'healthCheckUrl' => 'http://' . $host . "/Eureka/health",
            'port' => [
                '$' => $instance->getPort(),
                '@enabled' => true,
            ],
            'securePort' => [
                '$' => 443,
                '@enabled' => false,
            ],
            'dataCenterInfo' => [
                "@class"=> 'com.netflix.appinfo.InstanceInfo$DefaultDataCenterInfo',
                'name' => 'MyOwn',
            ],
            /*'metadata' => [
                "@class"=> 'java.util.Collections$EmptyMap',
            ],*/
            'metadata' => [
                "management.port"=> $instance->getPort(),
            ],
            'isCoordinatingDiscoveryServer' => false,
            'lastUpdatedTimestamp' => $microTime,
            'lastDirtyTimestamp' => $microTime,
            'countryId' => 1,
        ];

        if(!empty($metaData)){
            foreach ($metaData as $key => $value){
                $data['metadata'][$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param $metedata
     * @return Instance
     */
    public static function formatToInstance($metedata){
        $status = $metedata['status'] == "UP" ? "UP" : "DOWN";
        $address = $metedata['ipAddr'] . ":" . $metedata['port']['$'];
        $instance = new Instance();
        //    $instance->setIp($metedata['ipAddr']);
        $instance->setIp($metedata['hostName']);
        $instance->setPort($metedata['port']['$']);
        $instance->setAddress($address);
        $instance->setStatus($status);
        $instance->setName($metedata['app']);
        $instance->setMetaData($metedata);
        return $instance;
    }
}