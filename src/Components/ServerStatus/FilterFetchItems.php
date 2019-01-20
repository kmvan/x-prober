<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class FilterFetchItems
{
    public function __construct()
    {
        EventsApi::on('fetchItems', array($this, 'filterSwapUsage'));
        EventsApi::on('fetchItems', array($this, 'filterMemoryUsage'));
        EventsApi::on('fetchItems', array($this, 'filterDiskUsage'));
    }

    public function filterSwapUsage(array $items)
    {
        $swapTotal = HelperApi::getMemoryUsage('SwapTotal');

        if ( ! $swapTotal) {
            return $items;
        }

        $items['swapRealUsage'] = array(
            'percent'  => HelperApi::getMemoryUsage('SwapRealUsage') ? \sprintf('%01.2f', HelperApi::getMemoryUsage('SwapRealUsage') / $swapTotal * 100) : 0,
            'overview' => HelperApi::getHumamMemUsage('SwapRealUsage') . ' / ' . HelperApi::getHumamMemUsage('SwapTotal'),
            'current'  => HelperApi::getMemoryUsage('SwapRealUsage'),
        );

        return $items;
    }

    public function filterMemoryUsage(array $items)
    {
        $items['memRealUsage'] = array(
            'percent'  => HelperApi::getMemoryUsage('MemRealUsage') ? \sprintf('%01.2f', HelperApi::getMemoryUsage('MemRealUsage') / HelperApi::getMemoryUsage('MemTotal') * 100) : 0,
            'overview' => HelperApi::getHumamMemUsage('MemRealUsage') . ' / ' . HelperApi::getHumamMemUsage('MemTotal'),
            'current'  => HelperApi::getMemoryUsage('MemRealUsage'),
        );

        return $items;
    }

    public function filterDiskUsage(array $items)
    {
        $items['diskUsage'] = array(
            'percent'  => HelperApi::getDiskFreeSpace() ? \sprintf('%01.2f', (1 - (HelperApi::getDiskFreeSpace() / HelperApi::getDiskTotalSpace())) * 100) : 0,
            'overview' => HelperApi::formatBytes(HelperApi::getDiskTotalSpace() - HelperApi::getDiskFreeSpace()) . ' / ' . HelperApi::getDiskTotalSpace(true),
            'current'  => HelperApi::getDiskFreeSpace() ? HelperApi::getDiskFreeSpace() / HelperApi::getDiskTotalSpace() : 0,
        );

        return $items;
    }
}
