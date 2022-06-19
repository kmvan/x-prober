<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\Utils\UtilsLocation;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class ServerLocationIpv4 extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('serverLocationIpv4' !== $action) {
                return $action;
            }

            if (XconfigApi::isDisabled($this->ID)) {
                return $action;
            }

            if (XconfigApi::isDisabled($this->FEATURE_SERVER_IP)) {
                return $action;
            }

            $response = new RestResponse();
            $ip       = UtilsServerIp::getV4();

            if ( ! $ip) {
                $response->setStatus(StatusCode::$BAD_REQUEST)->json()->end();
            }

            $response->setData(UtilsLocation::getLocation($ip))->json()->end();
        });
    }
}
