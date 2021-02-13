<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsCpu;
use InnStudio\Prober\Components\Utils\UtilsMemory;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Conf extends ServerStatusConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $conf;
        }

        $conf[$this->ID] = array(
            'sysLoad'      => UtilsCpu::getLoadAvg(),
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

        return $conf;
    }
}
