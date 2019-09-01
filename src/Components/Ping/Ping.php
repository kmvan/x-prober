<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class Ping
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('ping' !== $action) {
            return $action;
        }

        $response = new RestfulResponse(array(
            'time' => \microtime(true) - \XPROBER_TIMER,
        ));
        $response->dieJson();
    }
}
