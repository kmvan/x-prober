<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Conf extends NetworkStatsConstants
{
    public function __construct()
    {
        HelperApi::isWin() || EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'networks' => HelperApi::getNetworkStats(),
        );

        return $conf;
    }
}
