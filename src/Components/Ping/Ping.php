<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Ping extends PingConstants
{
    public function __construct()
    {
        new Conf();
        EventsApi::on('init', function ($action) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $action;
            }

            if ($this->ID !== $action) {
                return $action;
            }

            $response = new RestResponse(array(
                'time' => \defined('XPROBER_TIMER') ? microtime(true) - XPROBER_TIMER : 0,
            ));
            $response->json()->end();
        });
    }
}
