<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Fetch extends ServerStatusConstants
{
    public function __construct()
    {
        EventsApi::on('fetch', [$this, 'filter']);
    }

    public function filter(array $items)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $items;
        }

        $items[$this->ID] = [
            'sysLoad'      => HelperApi::getSysLoadAvg(),
            'cpuUsage'     => HelperApi::getCpuUsage(),
            'memRealUsage' => [
                'value' => HelperApi::getMemoryUsage('MemRealUsage'),
                'max'   => HelperApi::getMemoryUsage('MemTotal'),
            ],
            'memBuffers' => [
                'value' => HelperApi::getMemoryUsage('Buffers'),
                'max'   => HelperApi::getMemoryUsage('MemUsage'),
            ],
            'memCached' => [
                'value' => HelperApi::getMemoryUsage('Cached'),
                'max'   => HelperApi::getMemoryUsage('MemUsage'),
            ],
            'swapUsage' => [
                'value' => HelperApi::getMemoryUsage('SwapUsage'),
                'max'   => HelperApi::getMemoryUsage('SwapTotal'),
            ],
            'swapCached' => [
                'value' => HelperApi::getMemoryUsage('SwapCached'),
                'max'   => HelperApi::getMemoryUsage('SwapUsage'),
            ],
        ];

        return $items;
    }
}
