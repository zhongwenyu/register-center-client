<?php
namespace ybrenLib\registerCenter\core;

use ybrenLib\registerCenter\core\bean\Instance;

interface RegisterCenterDriverInteface{

    /**
     * 获取所有节点
     * @param string $serviceName
     * @return array
     */
    function fetchInstances($serviceName);

    /**
     * 注册节点
     * @param Instance $instance
     */
    function register(Instance $instance);

    /**
     * 保持心跳
     * @param Instance $instance
     */
    function heartbeat(Instance $instance);

    /**
     * 节点上线
     * @param Instance $instance
     */
    function up(Instance $instance);

    /**
     * 节点下线
     * @param Instance $instance
     */
    function down(Instance $instance);

    /**
     * 节点移除
     * @param Instance $instance
     * @return mixed
     */
    function delete(Instance $instance);
}