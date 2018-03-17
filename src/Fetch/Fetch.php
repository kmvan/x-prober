<?php

namespace InnStudio\Prober\Fetch;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;

class Fetch
{
    public function __construct()
    {
        if (Helper::isAction('fetch')) {
            Events::emit('fetch');
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
                'time'   => Helper::getServerTime(),
                'upTime' => Helper::getServerUpTime(),
            ),
            'cpuUsage'     => Helper::getHumanCpuUsage(),
            'sysLoadAvg'   => Helper::getSysLoadAvg(),
            'memTotal'     => Helper::getMemoryUsage('MemTotal'),
            'memRealUsage' => array(
                'percent' => Helper::getMemoryUsage('MemRealUsage') ? \sprintf('%01.2f', Helper::getMemoryUsage('MemRealUsage') / Helper::getMemoryUsage('MemTotal') * 100) : 0,
                'number'  => Helper::getHumamMemUsage('MemRealUsage') . ' / ' . Helper::getHumamMemUsage('MemTotal'),
                'current' => Helper::getMemoryUsage('MemRealUsage'),
            ),
            'swapRealUsage' => array(
                'percent' => Helper::getMemoryUsage('SwapRealUsage') ? \sprintf('%01.2f', Helper::getMemoryUsage('SwapRealUsage') / Helper::getMemoryUsage('SwapTotal') * 100) : 0,
                'number'  => Helper::getHumamMemUsage('SwapRealUsage') . ' / ' . Helper::getHumamMemUsage('SwapTotal'),
                'current' => Helper::getMemoryUsage('SwapRealUsage'),
            ),
            'networkStats' => Helper::getNetworkStats(),
        );
    }

    private function outputItems()
    {
        Helper::dieJson(array(
            'code' => 0,
            'data' => $this->getItems(),
        ));
    }
}
