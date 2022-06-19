<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Init extends ServerBenchmarkApi
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if (XconfigApi::isDisabled('myServerBenchmark')) {
                return $action;
            }

            if ('benchmark' !== $action) {
                return $action;
            }

            $this->render();
        });
    }

    private function render()
    {
        $remainingSeconds = $this->getRemainingSeconds();
        $response         = new RestResponse();

        if ($remainingSeconds) {
            $response->setStatus(StatusCode::$TOO_MANY_REQUESTS);
            $response->setData(array(
                'seconds' => $remainingSeconds,
            ))->json()->end();
        }

        set_time_limit(0);

        $this->setExpired();
        $this->setIsRunning(true);

        // start benchmark
        $marks = $this->getPoints();
        // end benchmark

        $this->setIsRunning(false);

        $response->setData(array(
            'marks' => $marks,
        ))->json()->end();
    }
}
