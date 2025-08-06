<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

final class ServerBenchmarkDelay extends ServerBenchmarkApi
{
    public function delay()
    {
        while ($this->isRunning()) {
            sleep(2);
        }
    }
}
