<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class ServerBenchmarkPerformanceAction extends ServerBenchmarkApi
{
    public function render($action)
    {
        if ('benchmarkPerformance' !== $action) {
            return;
        }
        if (UserConfigApi::isDisabled('myServerBenchmark')) {
            (new RestResponse())
                ->setStatus(StatusCode::$FORBIDDEN)
                ->end();
        }
        $this->renderMarks();
    }

    private function renderMarks()
    {
        set_time_limit(0);
        $remainingSeconds = $this->getRemainingSeconds();
        $response = new RestResponse();
        if ($remainingSeconds) {
            $response
                ->setStatus(StatusCode::$TOO_MANY_REQUESTS)
                ->setData([
                    'seconds' => $remainingSeconds,
                ])
                ->end();
        }
        $this->setExpired();
        $this->setIsRunning(true);
        // start benchmark
        $marks = $this->getPoints();
        // end benchmark
        $this->setIsRunning(false);
        $response
            ->setData([
                'marks' => $marks,
            ])
            ->end();
    }
}
