<?php

namespace InnStudio\Prober\Components\Benchmark;

use InnStudio\Prober\Components\Events\EventsApi;

class FetchBefore extends BenchmarkApi
{
    public function __construct()
    {
        EventsApi::on('fetchBefore', array($this, 'filter'));
    }

    public function filter()
    {
        while ($this->isRunning()) {
            \sleep(2);
        }
    }
}
