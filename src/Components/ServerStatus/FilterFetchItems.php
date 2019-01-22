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

        if ( ! $total) {
            return $items;
        }

        $items['swapRealUsage'] = array(
            'usage' => HelperApi::getMemoryUsage('SwapRealUsage'),
            'total' => $total,
        );

        return $items;
    }

    public function filterMemoryUsage(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemTotal');

        if ( ! $total) {
            return $items;
        }

        $items['memRealUsage'] = array(
            'usage' => HelperApi::getMemoryUsage('MemRealUsage'),
            'total' => $total,
        );

        return $items;
    }

    public function filterDiskUsage(array $items)
    {
        if ( ! HelperApi::getDiskTotalSpace()) {
            return $items;
        }

        $items['diskUsage'] = array(
            'usage' => (int) \bcsub(HelperApi::getDiskTotalSpace(), HelperApi::getDiskFreeSpace()),
            'total' => HelperApi::getDiskTotalSpace(),
        );

        return $items;
    }
}
