<?php
namespace ybrenLib\registerCenter\core\driver\eureka\discovery;

use ybrenLib\registerCenter\core\bean\Instance;

interface DiscoveryStrategy {

    /**
     * @param $instances array
     * @return Instance
     */
    public function getInstance($instances);

}