<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Fetch extends ServerStatusConstants
{
    public function __construct()
    {
        EventsApi::on('fetch', array($this, 'filter'));
    }

    public function filter(array $items)
    {
        $items[$this->ID] = array(
            'sysLoad'      => HelperApi::getSysLoadAvg(),
            'cpuUsage'     => HelperApi::getCpuUsage(),
            'memRealUsage' => array(
                'value' => HelperApi::getMemoryUsage('MemRealUsage'),
                'max'   => HelperApi::getMemoryUsage('MemTotal'),
            ),
            'memBuffers' => array(
                'value' => HelperApi::getMemoryUsage('Buffers'),
                'max'   => HelperApi::getMemoryUsage('MemUsage'),
            ),
            'memCached' => array(
                'value' => HelperApi::getMemoryUsage('Cached'),
                'max'   => HelperApi::getMemoryUsage('MemUsage'),
            ),
            'swapUsage' => array(
                'value' => HelperApi::getMemoryUsage('SwapUsage'),
                'max'   => HelperApi::getMemoryUsage('SwapTotal'),
            ),
            'swapCached' => array(
                'value' => HelperApi::getMemoryUsage('SwapCached'),
                'max'   => HelperApi::getMemoryUsage('SwapUsage'),
            ),
        );

        return $items;
    }
}
