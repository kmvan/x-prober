<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class ServerInitIpv6 extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('serverIpv6' !== $action) {
                return $action;
            }

            if (XconfigApi::isDisabled($this->ID)) {
                return $action;
            }

            if (XconfigApi::isDisabled($this->FEATURE_SERVER_IP)) {
                return $action;
            }

            $response = new RestResponse();
            $response->setData(array(
                'ip' => UtilsServerIp::getV6(),
            ))->json()->end();
        });
    }
}
