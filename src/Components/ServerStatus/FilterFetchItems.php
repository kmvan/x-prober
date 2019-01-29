<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class FilterFetchItems extends ServerStatusApi
{
    public function __construct()
    {
        EventsApi::on('fetchItems', array($this, 'filterMemCached'));
        EventsApi::on('fetchItems', array($this, 'filterMemBuffers'));
        EventsApi::on('fetchItems', array($this, 'filterSwapUsage'));
        EventsApi::on('fetchItems', array($this, 'filterSwapCached'));
        EventsApi::on('fetchItems', array($this, 'filterMemUsage'));
        EventsApi::on('fetchItems', array($this, 'filterDiskUsage'));
    }

    public function filterSwapUsage(array $items)
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        $items['swapUsage'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('SwapUsage') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterSwapCached(array $items)
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        $items['swapCached'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('SwapCached') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemUsage(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemTotal');

        $items['memUsage'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('MemUsage') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemBuffers(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemTotal');

        $items['memBuffers'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('Buffers') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemCached(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemTotal');

        $items['memCached'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('Cached') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterDiskUsage(array $items)
    {
        $total = HelperApi::getDiskTotalSpace();

        $items['diskUsage'] = array(
            'usage' => $total ? (int) HelperApi::getDiskTotalSpace() - (int) HelperApi::getDiskFreeSpace() : 0,
            'total' => $total,
        );

        return $items;
    }
}
