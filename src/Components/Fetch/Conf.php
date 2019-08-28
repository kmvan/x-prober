<?php

namespace InnStudio\Prober\Components\Fetch;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Conf
{
    public function __construct()
    {
        EventsApi::on('fetch', [$this, 'filter']);
    }

    public function filter()
    {
        if (HelperApi::isAction('fetch')) {
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
        $items = [
            'utcTime'    => $this->getServerUtcTime(),
            'serverInfo' => [
                'time'   => HelperApi::getServerTime(),
                'upTime' => HelperApi::getServerUpTime(),
            ],
            'cpuUsage'     => HelperApi::getHumanCpuUsage(),
            'sysLoadAvg'   => HelperApi::getSysLoadAvg(),
            'memTotal'     => HelperApi::getMemoryUsage('MemTotal'),
            'memBuffers'   => HelperApi::getMemoryUsage('Buffers'),
            'memCached'    => HelperApi::getMemoryUsage('Cached'),
            'networkStats' => HelperApi::getNetworkStats(),
        ];

        return EventsApi::emit('fetchItems', $items);
    }

    private function outputItems()
    {
        HelperApi::dieJson([
            'code' => 0,
            'data' => $this->getItems(),
        ]);
    }
}
