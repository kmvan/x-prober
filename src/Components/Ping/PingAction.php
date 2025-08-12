<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class PingAction
{
    public function render($action)
    {
        $id = PingConstants::ID;
        if ($action !== $id) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled($id)) {
            $response
                ->setStatus(StatusCode::NOT_IMPLEMENTED)
                ->end();
        }
        $response
            ->setData([
                'time' => \defined('XPROBER_TIMER') ? microtime(true) - XPROBER_TIMER : 0,
            ])
            ->end();
    }
}
