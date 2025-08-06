<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Utils\UtilsNetwork;

final class NetworkStatsPoll extends NetworkStatsConstants
{
    public function render()
    {
        if (UtilsApi::isWin() || UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }

        return [
            $this->ID => [
                'networks' => UtilsNetwork::getStats(),
                'timestamp' => time(),
            ],
        ];
    }
}
