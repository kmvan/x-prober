<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\Events\EventsApi;

class FetchBefore extends ServerBenchmarkApi
{
    public function __construct()
    {
        EventsApi::on('fetchBefore', [$this, 'filter']);
    }

    public function filter()
    {
        while ($this->isRunning()) {
            \sleep(2);
        }
    }
}
