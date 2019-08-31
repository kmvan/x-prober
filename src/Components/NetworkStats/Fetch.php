<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Fetch extends NetworkStatsConstants
{
    public function __construct()
    {
        HelperApi::isWin() || EventsApi::on('fetch', array($this, 'filter'));
    }

    public function filter(array $items)
    {
        $items[$this->ID] = array(
            'networks' => HelperApi::getNetworkStats(),
        );

        return $items;
    }
}
