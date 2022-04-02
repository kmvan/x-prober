<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Utils\UtilsNetwork;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Conf extends NetworkStatsConstants
{
    public function __construct()
    {
        UtilsApi::isWin() || EventsApi::on('conf', function (array $conf) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $conf;
            }

            $conf[$this->ID] = array(
                'networks'  => UtilsNetwork::getStats(),
                'timestamp' => time(),
            );

            return $conf;
        });
    }
}
