<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

final class ServerBenchmarkDelay
{
    public function delay()
    {
        while (ServerBenchmarkApi::isRunning()) {
            sleep(2);
        }
    }
}
