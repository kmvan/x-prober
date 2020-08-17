<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Fetch extends NetworkStatsConstants
{
    public function __construct()
    {
        if ( ! HelperApi::isWin()) {
            EventsApi::on('fetch', array($this, 'filter'));
            EventsApi::on('nodes', array($this, 'filter'));
        }
    }

    public function filter(array $items)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $items;
        }

        $items[$this->ID] = array(
            'networks'  => HelperApi::getNetworkStats(),
            'timestmap' => \time(),
        );

        return $items;
    }
}
