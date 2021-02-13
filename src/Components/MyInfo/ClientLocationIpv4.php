<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;
use InnStudio\Prober\Components\Utils\UtilsLocation;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class ClientLocationIpv4 extends MyInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('clientLocationIpv4' !== $action) {
            return $action;
        }

        if (XconfigApi::isDisabled($this->ID)) {
            return $action;
        }

        $response = new RestfulResponse();
        $ip       = \filter_input(\INPUT_GET, 'ip', \FILTER_VALIDATE_IP, array(
            'flags' => \FILTER_FLAG_IPV4,
        ));

        if ( ! $ip) {
            $response->setStatus(HttpStatus::$BAD_REQUEST)->dieJson();
        }

        $response->setData(UtilsLocation::getLocation($ip))->dieJson();
    }
}
