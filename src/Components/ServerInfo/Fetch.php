<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Fetch extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('fetch', array($this, 'filter'));
    }

    public function filter(array $items)
    {
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
