<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class FilterFetchItems extends ServerStatusApi
{
    public function __construct()
    {
        EventsApi::on('fetchItems', array($this, 'filterSwapUsage'));
        EventsApi::on('fetchItems', array($this, 'filterMemoryUsage'));
        EventsApi::on('fetchItems', array($this, 'filterDiskUsage'));
    }

    public function filterSwapUsage(array $items)
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        $items['swapRealUsage'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('SwapRealUsage') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemoryUsage(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemTotal');

        $items['memRealUsage'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('MemRealUsage') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterDiskUsage(array $items)
    {
        $total = HelperApi::getDiskTotalSpace();

        $items['diskUsage'] = array(
            'usage' => $total ? (int) \bcsub(HelperApi::getDiskTotalSpace(), HelperApi::getDiskFreeSpace()) : 0,
            'total' => $total,
        );

        return $items;
    }
}
