<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

final class ServerBenchmark
{
    public function __construct()
    {
        new Init();
        new Conf();
        new FetchBefore();
    }
}
