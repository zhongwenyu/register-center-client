<?php
namespace ybrenLib\registerCenter\driver\eureka;

use GuzzleHttp\Client;
use ybrenLib\registerCenter\core\bean\Instance;
use ybrenLib\registerCenter\core\exception\InstanceNotFoundException;
use ybrenLib\registerCenter\driver\eureka\utlis\InstanceUtil;
use ybrenLib\registerCenter\core\RegisterCenterDriverInteface;
use ybrenLib\registerCenter\core\RegisterCenterConfig;

class EurekaRegisterCenter implements RegisterCenterDriverInteface{

    /**
     * @var Client
     */
    private $httpClient;

    private $registerCenterAddressList;

    /**
     * 请求超时设置
     * @var int
     */
    private $timeout = 2;

    public function __construct(){
        $this->httpClient = new Client();
        $this->registerCenterAddressList = explode("," , RegisterCenterConfig::getAddress());
    }

    function fetchInstances($serviceName)
    {
        // eureka 节点随机排序
        shuffle($this->registerCenterAddressList);
        $length = count($this->registerCenterAddressList);
        $responseContents = "";
        for ($i = 0;$i < $length;$i++){
            $eurekaUrl = $this->registerCenterAddressList[$i];
            $url = $eurekaUrl . 'apps/' . $serviceName;
            try{
                $response = $this->httpClient->get($url , [
                    'timeout' => $this->timeout,
                    'headers' => [
                        'Accept' => 'application/json',
                    ]
                ]);
                if($response != null && $response->getStatusCode() < 400){
                    $responseContents = $response->getBody()->getContents();
                    break;
                }
            }catch (\Exception $e){
                if($i == ($length - 1)){
                    throw $e;
                }
            }
        }

        $responseContentsArray = json_decode($responseContents , true);
        $instances = $responseContentsArray['application']['instance'];
        if(empty($instances)){
            throw new InstanceNotFoundException("eureka[".$url."]服务[".$serviceName."]节点不存在,返回信息[".$responseContents."]");
        }

        $list = [];
        if(!empty($instances)){
            foreach ($instances as $instance){
                $list[] = InstanceUtil::formatToInstance($instance);
            }
        }

        return $list;
    }

    function register(Instance $instance){
        $serviceName = $instance->getName();
        $registerData = [
            "instance" => InstanceUtil::formatToMetedata($instance)
        ];
        foreach ($this->registerCenterAddressList as $eurekaUrl){
            $url = $eurekaUrl . 'apps/'.$serviceName;
            $this->httpClient->post($url , [
                'timeout' => $this->timeout,
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => $registerData
            ]);
        }
    }

    function heartbeat(Instance $instance){
        $serviceName = $instance->getName();
        $instanceId = $this->getInstanceId($instance);
        foreach ($this->registerCenterAddressList as $eurekaUrl){
            $url = $eurekaUrl . 'apps/'.$serviceName.'/' . $instanceId;
            $this->httpClient->put($url , [
                'timeout' => $this->timeout,
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
            ]);
        }
    }

    function up(Instance $instance){
        $serviceName = $instance->getName();
        $instanceId = $this->getInstanceId($instance);
        foreach ($this->registerCenterAddressList as $eurekaUrl){
            $url = $eurekaUrl . 'apps/'.$serviceName.'/' . $instanceId."/status?value=UP";
            $this->httpClient->put($url , [
                'timeout' => $this->timeout,
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
            ]);
        }
    }

    function down(Instance $instance){
        $serviceName = $instance->getName();
        $instanceId = $this->getInstanceId($instance);
        foreach ($this->registerCenterAddressList as $eurekaUrl){
            $url = $eurekaUrl . 'apps/'.$serviceName.'/' . $instanceId."/status?value=OUT_OF_SERVICE";
            $this->httpClient->put($url , [
                'timeout' => $this->timeout,
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
            ]);
        }
    }

    function delete(Instance $instance)
    {
        $serviceName = $instance->getName();
        $instanceId = $this->getInstanceId($instance);
        foreach ($this->registerCenterAddressList as $eurekaUrl){
            $url = $eurekaUrl . 'apps/'.$serviceName.'/' . $instanceId;
            $this->httpClient->delete($url , [
                'timeout' => $this->timeout,
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
            ]);
        }
    }


    /**
     * @param Instance $instance
     * @return string
     */
    private function getInstanceId(Instance $instance){
        return $instance->getIp() . ":" . $instance->getPort();
    }
}