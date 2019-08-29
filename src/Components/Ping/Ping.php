<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class Ping
{
    public function __construct()
    {
        EventsApi::on('init', [$this, 'filter']);
    }

    public function filter($action)
    {
        if ('ping' !== $action) {
            return $action;
        }

        (new RestfulResponse([
            'time' => \microtime(true) - \XPROBER_TIMER,
        ]))->dieJson();
    }
}
