<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsDisk;
use InnStudio\Prober\Components\Utils\UtilsTime;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Fetch extends ServerInfoConstants
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
            'serverUtcTime' => UtilsTime::getUtcTime(),
            'serverTime'    => UtilsTime::getTime(),
            'serverUptime'  => UtilsTime::getUptime(),
            'diskUsage'     => array(
                'value' => UtilsDisk::getTotal() - UtilsDisk::getFree(),
                'max'   => UtilsDisk::getTotal(),
            ),
        );

        return $items;
    }
}
