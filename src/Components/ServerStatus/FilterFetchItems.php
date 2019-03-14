<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Benchmark\BenchmarkApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class FilterFetchItems extends ServerStatusApi
{
    public function __construct()
    {
        EventsApi::on('fetchItems', array($this, 'filterFetchItems'));
    }

    public function filterFetchItems(array $items)
    {
        $benchmark = new BenchmarkApi();

        while ($benchmark->isRunning()) {
            \sleep(2);
        }

        return \array_merge(
            $items,
            $this->filterMemCached($items),
            $this->filterMemBuffers($items),
            $this->filterSwapUsage($items),
            $this->filterSwapCached($items),
            $this->filterMemRealUsage($items),
            $this->filterDiskUsage($items)
        );
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
        $total = HelperApi::getMemoryUsage('SwapUsage');

        $items['swapCached'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('SwapCached') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemRealUsage(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemTotal');

        $items['memRealUsage'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('MemRealUsage') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemBuffers(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemUsage');

        $items['memBuffers'] = array(
            'usage' => $total ? HelperApi::getMemoryUsage('Buffers') : 0,
            'total' => $total,
        );

        return $items;
    }

    public function filterMemCached(array $items)
    {
        $total = HelperApi::getMemoryUsage('MemUsage');

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
            'usage' => $total ? $total - HelperApi::getDiskFreeSpace() : 0,
            'total' => $total,
        );

        return $items;
    }
}
