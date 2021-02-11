<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsCpu;
use InnStudio\Prober\Components\Utils\UtilsDisk;
use InnStudio\Prober\Components\Utils\UtilsTime;
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
            'serverUtcTime'  => UtilsTime::getUtcTime(),
            'serverTime'     => UtilsTime::getTime(),
            'serverUptime'   => UtilsTime::getUptime(),
            'serverIp'       => XconfigApi::isDisabled('serverIp') ? '-' : $this->getServerInfo('SERVER_ADDR'),
            'serverSoftware' => $this->getServerInfo('SERVER_SOFTWARE'),
            'phpVersion'     => \PHP_VERSION,
            'cpuModel'       => UtilsCpu::getModel(),
            'serverOs'       => \php_uname(),
            'scriptPath'     => __FILE__,
            'diskUsage'      => array(
                'value' => UtilsDisk::getTotal() - UtilsDisk::getFree(),
                'max'   => UtilsDisk::getTotal(),
            ),
        );

        return $conf;
    }

    private function getServerInfo($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}
