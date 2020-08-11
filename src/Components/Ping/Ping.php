<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\RestfulResponse;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Ping extends PingConstants
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $action;
        }

        if ($this->ID !== $action) {
            return $action;
        }

        $response = new RestfulResponse(array(
            'time' => \microtime(true) - \XPROBER_TIMER,
        ));
        $response->dieJson();
    }
}
