<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsClientIp;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Conf extends MyInfoConstants
{
    public function __construct()
    {
        EventsApi::on('conf', function (array $conf) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $conf;
            }

            $ip   = UtilsClientIp::getV4();
            $ipv4 = filter_var($ip, \FILTER_VALIDATE_IP, array(
                'flags' => \FILTER_FLAG_IPV4,
            )) ?: '';
            $ipv6 = filter_var($ip, \FILTER_VALIDATE_IP, array(
                'flags' => \FILTER_FLAG_IPV6,
            )) ?: '';
            $conf[$this->ID] = array(
                'phpLanguage' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '-',
                'ipv4'        => $ipv4,
                'ipv6'        => $ipv6,
            );

            return $conf;
        });
    }
}
