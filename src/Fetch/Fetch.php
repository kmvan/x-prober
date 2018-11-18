<?php

namespace InnStudio\Prober\Fetch;

use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\Helper\HelperApi;

class Fetch
{
    public function __construct()
    {
        if (HelperApi::isAction('fetch')) {
            EventsApi::emit('fetch');
            $this->outputItems();
        }
    }

    private function getServerUtcTime()
    {
        return \gmdate('Y/m/d H:i:s');
    }

    private function getServerLocalTime()
    {
        return \date('Y/m/d H:i:s');
    }

    private function getItems()
    {
        return array(
            'utcTime'    => $this->getServerUtcTime(),
            'serverInfo' => array(
                'time'   => HelperApi::getServerTime(),
                'upTime' => HelperApi::getServerUpTime(),
            ),
            'cpuUsage'     => HelperApi::getHumanCpuUsage(),
            'sysLoadAvg'   => HelperApi::getSysLoadAvg(),
            'memTotal'     => HelperApi::getMemoryUsage('MemTotal'),
            'memRealUsage' => array(
                'percent' => HelperApi::getMemoryUsage('MemRealUsage') ? \sprintf('%01.2f', HelperApi::getMemoryUsage('MemRealUsage') / HelperApi::getMemoryUsage('MemTotal') * 100) : 0,
                'number'  => HelperApi::getHumamMemUsage('MemRealUsage') . ' / ' . HelperApi::getHumamMemUsage('MemTotal'),
                'current' => HelperApi::getMemoryUsage('MemRealUsage'),
            ),
            'swapRealUsage' => array(
                'percent' => HelperApi::getMemoryUsage('SwapRealUsage') ? \sprintf('%01.2f', HelperApi::getMemoryUsage('SwapRealUsage') / HelperApi::getMemoryUsage('SwapTotal') * 100) : 0,
                'number'  => HelperApi::getHumamMemUsage('SwapRealUsage') . ' / ' . HelperApi::getHumamMemUsage('SwapTotal'),
                'current' => HelperApi::getMemoryUsage('SwapRealUsage'),
            ),
            'networkStats' => HelperApi::getNetworkStats(),
        );
    }

    private function outputItems()
    {
        HelperApi::dieJson(array(
            'code' => 0,
            'data' => $this->getItems(),
        ));
    }
}
