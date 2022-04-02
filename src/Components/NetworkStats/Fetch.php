<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Utils\UtilsNetwork;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Fetch extends NetworkStatsConstants
{
    public function __construct()
    {
        if ( ! UtilsApi::isWin()) {
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
            'networks'  => UtilsNetwork::getStats(),
            'timestamp' => time(),
        );

        return $items;
    }
}
