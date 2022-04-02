<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsCpu;
use InnStudio\Prober\Components\Utils\UtilsMemory;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Fetch extends ServerStatusConstants
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
            'sysLoad'      => UtilsCpu::getLoadAvg(),
            'cpuUsage'     => UtilsCpu::getUsage(),
            'memRealUsage' => array(
                'value' => UtilsMemory::getMemoryUsage('MemRealUsage'),
                'max'   => UtilsMemory::getMemoryUsage('MemTotal'),
            ),
            'memBuffers' => array(
                'value' => UtilsMemory::getMemoryUsage('Buffers'),
                'max'   => UtilsMemory::getMemoryUsage('MemUsage'),
            ),
            'memCached' => array(
                'value' => UtilsMemory::getMemoryUsage('Cached'),
                'max'   => UtilsMemory::getMemoryUsage('MemUsage'),
            ),
            'swapUsage' => array(
                'value' => UtilsMemory::getMemoryUsage('SwapUsage'),
                'max'   => UtilsMemory::getMemoryUsage('SwapTotal'),
            ),
            'swapCached' => array(
                'value' => UtilsMemory::getMemoryUsage('SwapCached'),
                'max'   => UtilsMemory::getMemoryUsage('SwapUsage'),
            ),
        );

        return $items;
    }
}
