<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsServerIp;

final class ServerInfoPublicIpv6Action
{
    public function render($action)
    {
        if ('serverPublicIpv6' !== $action) {
            return;
        }
        if (UserConfigApi::isDisabled(ServerInfoConstants::ID) || UserConfigApi::isDisabled(ServerInfoConstants::FEATURE_SERVER_IP)) {
            (new RestResponse())
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        (new RestResponse())
            ->setData([
                'ip' => UtilsServerIp::getPublicIpV6(),
            ])
            ->end();
    }
}
