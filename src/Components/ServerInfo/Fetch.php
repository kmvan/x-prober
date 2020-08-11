<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Fetch extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('fetch', [$this, 'filter']);
    }

    public function filter(array $items)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $items;
        }

        $items[$this->ID] = [
            'serverTime'    => HelperApi::getServerTime(),
            'serverUptime'  => HelperApi::getServerUptime(),
            'serverUtcTime' => HelperApi::getServerUtcTime(),
            'diskUsage'     => [
                'value' => HelperApi::getDiskTotalSpace() - HelperApi::getDiskFreeSpace(),
                'max'   => HelperApi::getDiskTotalSpace(),
            ],
        ];

        return $items;
    }
}
