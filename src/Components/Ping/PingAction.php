<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class PingAction extends PingConstants
{
    public function render($action)
    {
        if ($action !== $this->ID) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled($this->ID)) {
            $response
                ->setStatus(StatusCode::$NOT_IMPLEMENTED)
                ->end();
        }
        $response
            ->setData([
                'time' => \defined('XPROBER_TIMER') ? microtime(true) - XPROBER_TIMER : 0,
            ])
            ->end();
    }
}
