<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Conf extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'serverName'     => $this->getServerInfo('SERVER_NAME'),
            'serverUtcTime'  => HelperApi::getServerUtcTime(),
            'serverTime'     => HelperApi::getServerTime(),
            'serverUptime'   => HelperApi::getServerUptime(),
            'serverIp'       => $this->getServerInfo('SERVER_ADDR'),
            'serverSoftware' => $this->getServerInfo('SERVER_SOFTWARE'),
            'phpVersion'     => \PHP_VERSION,
            'cpuModel'       => HelperApi::getCpuModel(),
            'serverOs'       => \php_uname(),
            'scriptPath'     => __FILE__,
            'diskUsage'      => array(
                'value' => HelperApi::getDiskTotalSpace() - HelperApi::getDiskFreeSpace(),
                'max'   => HelperApi::getDiskTotalSpace(),
            ),
        );

        return $conf;
    }

    private function getServerInfo($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}
