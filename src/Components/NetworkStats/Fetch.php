<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Fetch extends NetworkStatsConstants
{
    public function __construct()
    {
        HelperApi::isWin() || EventsApi::on('fetch', [$this, 'filter']);
    }

    public function filter(array $items)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $items;
        }

        $items[$this->ID] = [
            'networks' => HelperApi::getNetworkStats(),
        ];

        return $items;
    }
}
