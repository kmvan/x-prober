<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

class ServerBenchmark
{
    public function __construct()
    {
        new Init();
        new Conf();
        new FetchBefore();
    }
}
