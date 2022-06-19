<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\Utils\UtilsLocation;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class ClientLocationIpv4 extends MyInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('clientLocationIpv4' !== $action) {
                return $action;
            }

            if (XconfigApi::isDisabled($this->ID)) {
                return $action;
            }

            $response = new RestResponse();
            $ip       = filter_input(\INPUT_GET, 'ip', \FILTER_VALIDATE_IP, array(
                'flags' => \FILTER_FLAG_IPV4,
            ));

            if ( ! $ip) {
                $response->setStatus(StatusCode::$BAD_REQUEST)->json()->end();
            }

            $response->setData(UtilsLocation::getLocation($ip))->json()->end();
        });
    }
}
