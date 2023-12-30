<?php

namespace InnStudio\Prober\Components\DiskUsage;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsDisk;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Conf extends DiskUsageConstants
{
    public function __construct()
    {
        EventsApi::on('conf', function (array $conf) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $conf;
            }

            $conf[$this->ID] = array(
                'items' => UtilsDisk::getItems(),
            );

            return $conf;
        });
    }
}
