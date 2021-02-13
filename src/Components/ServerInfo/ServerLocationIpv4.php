<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;
use InnStudio\Prober\Components\Utils\UtilsLocation;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class ServerLocationIpv4 extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('serverLocationIpv4' !== $action) {
            return $action;
        }

        if (XconfigApi::isDisabled($this->ID)) {
            return $action;
        }

        if (XconfigApi::isDisabled($this->FEATURE_SERVER_IP)) {
            return $action;
        }

        $response = new RestfulResponse();
        $ip       = UtilsServerIp::getV4();

        if ( ! $ip) {
            $response->setStatus(HttpStatus::$BAD_REQUEST)->dieJson();
        }

        $response->setData(UtilsLocation::getLocation($ip))->dieJson();
    }
}
