<?php

namespace InnStudio\Prober\Components\DiskUsage;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsDisk;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Fetch extends DiskUsageConstants
{
    public function __construct()
    {
        EventsApi::on('fetch', array($this, 'filter'));
        EventsApi::on('nodes', array($this, 'filter'));
    }

    public function filter(array $items)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $items;
        }

        $items[$this->ID] = array(
            'diskUsage' => UtilsDisk::getItems(),
        );

        return $items;
    }
}
