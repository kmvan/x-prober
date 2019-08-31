<?php

namespace InnStudio\Prober\Components\Benchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class Benchmark extends BenchmarkApi
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
        new FetchBefore();
    }

    public function filter($action)
    {
        if ('benchmark' !== $action) {
            return $action;
        }

        $this->display();
    }

    public function display()
    {
        $remainingSeconds = $this->getRemainingSeconds();
        $response         = new RestfulResponse();

        if ($remainingSeconds) {
            $response->setStatus(HttpStatus::$TOO_MANY_REQUESTS);
            $response->setData(array(
                'seconds' => $remainingSeconds,
            ));
            $response->dieJson();
        }

        \set_time_limit(0);

        $this->setExpired();
        $this->setIsRunning(true);

        // start benchmark
        $marks = $this->getPoints();
        // end benchmark

        $this->setIsRunning(false);

        $response->setData(array(
            'marks' => $marks,
        ));
        $response->dieJson();
    }
}
