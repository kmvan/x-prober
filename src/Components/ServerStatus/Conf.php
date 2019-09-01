<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Conf extends ServerStatusConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'sysLoad'      => HelperApi::getSysLoadAvg(),
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

        return $conf;
    }
}
