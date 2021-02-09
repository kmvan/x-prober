<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Conf extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $conf;
        }

        $conf[$this->ID] = array(
            'serverName'     => $this->getServerInfo('SERVER_NAME'),
            'serverUtcTime'  => UtilsApi::getServerUtcTime(),
            'serverTime'     => UtilsApi::getServerTime(),
            'serverUptime'   => UtilsApi::getServerUptime(),
            'serverIp'       => XconfigApi::isDisabled('serverIp') ? '-' : $this->getServerInfo('SERVER_ADDR'),
            'serverSoftware' => $this->getServerInfo('SERVER_SOFTWARE'),
            'phpVersion'     => \PHP_VERSION,
            'cpuModel'       => UtilsApi::getCpuModel(),
            'serverOs'       => \php_uname(),
            'scriptPath'     => __FILE__,
            'diskUsage'      => array(
                'value' => UtilsApi::getDiskTotalSpace() - UtilsApi::getDiskFreeSpace(),
                'max'   => UtilsApi::getDiskTotalSpace(),
            ),
        );

        return $conf;
    }

    private function getServerInfo($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}
