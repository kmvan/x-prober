<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Utils\UtilsNetwork;

final class NetworkStatsPoll
{
    public function render()
    {
        $id = NetworkStatsConstants::ID;
        if (UtilsApi::isWin() || UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }

        return [
            $id => [
                'networks' => UtilsNetwork::getStats(),
                'timestamp' => time(),
            ],
        ];
    }
}
