<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\RestfulResponse;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class ServerInitIpv4 extends ServerInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('serverIpv4' !== $action) {
            return $action;
        }

        if (XconfigApi::isDisabled($this->ID)) {
            return $action;
        }

        if (XconfigApi::isDisabled($this->FEATURE_SERVER_IP)) {
            return $action;
        }

        $response = new RestfulResponse();
        $response->setData(array(
            'ip' => UtilsServerIp::getV4(),
        ))->dieJson();
    }
}
