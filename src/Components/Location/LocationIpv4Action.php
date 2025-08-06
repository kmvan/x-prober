<?php

namespace InnStudio\Prober\Components\Location;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsLocation;

final class LocationIpv4Action extends LocationConstants
{
    public function render($action)
    {
        if (self::$ID !== $action) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled($this->FEATURE_LOCATION)) {
            $response
                ->setStatus(StatusCode::$FORBIDDEN)
                ->end();
        }
        $ip = filter_input(\INPUT_GET, 'ip', \FILTER_VALIDATE_IP, [
            'flags' => \FILTER_FLAG_IPV4,
        ]);
        if ( ! $ip) {
            $response
                ->setStatus(StatusCode::$BAD_REQUEST)
                ->end();
        }
        $response
            ->setData(UtilsLocation::getLocation($ip))
            ->end();
    }
}
