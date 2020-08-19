<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
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
            'serverTime'    => HelperApi::getServerTime(),
            'serverUptime'  => HelperApi::getServerUptime(),
            'serverUtcTime' => HelperApi::getServerUtcTime(),
            'diskUsage'     => array(
                'value' => HelperApi::getDiskTotalSpace() - HelperApi::getDiskFreeSpace(),
                'max'   => HelperApi::getDiskTotalSpace(),
            ),
        );

        return $items;
    }
}
