<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Fetch extends ServerStatusConstants
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
            'sysLoad'      => UtilsApi::getSysLoadAvg(),
            'cpuUsage'     => UtilsApi::getCpuUsage(),
            'memRealUsage' => array(
                'value' => UtilsApi::getMemoryUsage('MemRealUsage'),
                'max'   => UtilsApi::getMemoryUsage('MemTotal'),
            ),
            'memBuffers' => array(
                'value' => UtilsApi::getMemoryUsage('Buffers'),
                'max'   => UtilsApi::getMemoryUsage('MemUsage'),
            ),
            'memCached' => array(
                'value' => UtilsApi::getMemoryUsage('Cached'),
                'max'   => UtilsApi::getMemoryUsage('MemUsage'),
            ),
            'swapUsage' => array(
                'value' => UtilsApi::getMemoryUsage('SwapUsage'),
                'max'   => UtilsApi::getMemoryUsage('SwapTotal'),
            ),
            'swapCached' => array(
                'value' => UtilsApi::getMemoryUsage('SwapCached'),
                'max'   => UtilsApi::getMemoryUsage('SwapUsage'),
            ),
        );

        return $items;
    }
}
