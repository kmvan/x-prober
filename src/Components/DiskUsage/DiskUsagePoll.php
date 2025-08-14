<?php

namespace InnStudio\Prober\Components\DiskUsage;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsDisk;

final class DiskUsagePoll
{
    public function render()
    {
        $id = DiskUsageConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }

        return [
            $id => [
                'items' => UtilsDisk::getItems(),
            ],
        ];
    }
}
