<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

class ServerBenchmark extends ServerBenchmarkApi
{
    public function __construct()
    {
        new Init();
        new Conf();
        new FetchBefore();
    }
}
