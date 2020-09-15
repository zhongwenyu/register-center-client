<?php
namespace ybrenLib\registerCenter\core;

use ybren\eureka\discovery\DiscoveryStrategy;
use ybrenLib\registerCenter\core\bean\Instance;
use ybrenLib\registerCenter\core\driver\eureka\discovery\RandomStrategy;
use ybrenLib\registerCenter\core\exception\InstanceNotFoundException;
use ybrenLib\registerCenter\driver\eureka\EurekaRegisterCenter;

class RegisterCenterClient{

    public $config = [
        // 驱动
        'driver' => EurekaRegisterCenter::class,
        // 服务选择策略
        'discoveryStrategy' => RandomStrategy::class,
        // 是否使用缓存
        'cache' => true,
        // 缓存有效期
        'cacheExpire' => 3,
    ];

    /**
     * @var RegisterCenterDriverInteface
     */
    private $registerCenterDriver;

    /**
     * @var RegisterCenterCacheInterface
     */
    private $registerCenterCacheDriver;

    /**
     * @var DiscoveryStrategy
     */
    private $discoveryStrategy;

    public function __construct(){
        $this->registerCenterDriver = new $this->config['driver']();
        $this->discoveryStrategy = new $this->config['discoveryStrategy'];
        $this->setCacheDriver();
    }

    private function setCacheDriver(){
        if(class_exists("Yac")){
            $this->registerCenterCacheDriver = new YacStorgeDriver();
        }else{
            $this->registerCenterCacheDriver = new FileStorgeDriver();
        }
    }

    /**
     * 获取单个可用节点
     * @param $serviceName
     * @return Instance
     */
    public function getInstance($serviceName){
        $instances = $this->fetchInstances($serviceName);
        return $this->discoveryStrategy->getInstance($instances);
    }

    /**
     * 获取所有节点
     * @param string $serviceName
     * @return array
     */
    public function fetchInstances($serviceName){
        $useCache = $this->config['cache'];

        $instances = null;
        if($useCache){
            $cacheData = $this->registerCenterCacheDriver->get($serviceName);
            if(!empty($cacheData) && $cacheData['expire'] > time()){
                $instances = $cacheData['data'];
            }
        }

        if(empty($instances)){
            $instances = $this->registerCenterDriver->fetchInstances($serviceName);
            if($useCache && !empty($instances)){
                $cacheData = [
                    'expire' => (time() + $this->config['cacheExpire']),
                    'data' => $instances
                ];
                $this->registerCenterCacheDriver->set($serviceName , $cacheData);
            }
        }

        return $instances;
    }

    /**
     * 注册节点
     * @param Instance $instance
     */
    public function register(Instance $instance){
        $this->registerCenterDriver->register($instance);
    }

    /**
     * 保持心跳
     * @param Instance $instance
     */
    public function heartbeat(Instance $instance){
        $this->registerCenterDriver->heartbeat($instance);
    }

    /**
     * 节点上线
     * @param Instance $instance
     */
    public function up(Instance $instance){
        $this->registerCenterDriver->up($instance);
    }

    /**
     * 节点下线
     * @param Instance $instance
     */
    public function down(Instance $instance){
        $this->registerCenterDriver->down($instance);
    }

    /**
     * 节点移除
     * @param Instance $instance
     */
    public function delete(Instance $instance){
        $this->registerCenterDriver->delete($instance);
    }
}