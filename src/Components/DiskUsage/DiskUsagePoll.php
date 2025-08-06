<?php

namespace InnStudio\Prober\Components\DiskUsage;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsDisk;

final class DiskUsagePoll extends DiskUsageConstants
{
    public function render()
    {
        if (UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }

        return [
            $this->ID => [
                'items' => UtilsDisk::getItems(),
            ],
        ];
    }
}
