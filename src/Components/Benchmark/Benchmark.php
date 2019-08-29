<?php

namespace InnStudio\Prober\Components\Benchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class Benchmark extends BenchmarkApi
{
    public function __construct()
    {
        EventsApi::on('init', [$this, 'filter']);
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

        if ($remainingSeconds) {
            HelperApi::dieJson([
                'code' => -1,
                'msg'  => '⏳ ' . \sprintf(I18nApi::_('Please wait %ds'), $remainingSeconds),
            ]);
        }

        \set_time_limit(0);

        $this->setExpired();
        $this->setIsRunning(true);

        // start benchmark
        $points = $this->getPoints();
        // end benchmark

        $this->setIsRunning(false);

        HelperApi::dieJson([
            'code' => 0,
            'data' => [
                'points'     => $points,
                'total'      => \array_sum($points),
                'totalHuman' => \number_format(\array_sum($points)),
            ],
        ]);
    }
}
