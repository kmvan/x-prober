<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Fetch extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('fetch', array($this, 'filter'));
        EventsApi::on('nodes', array($this, 'filter'));
    }

    public function filter(array $items)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $items;
        }

        $items[$this->ID] = array(
            'serverTime'    => UtilsApi::getServerTime(),
            'serverUptime'  => UtilsApi::getServerUptime(),
            'serverUtcTime' => UtilsApi::getServerUtcTime(),
            'diskUsage'     => array(
                'value' => UtilsApi::getDiskTotalSpace() - UtilsApi::getDiskFreeSpace(),
                'max'   => UtilsApi::getDiskTotalSpace(),
            ),
        );

        return $items;
    }
}
