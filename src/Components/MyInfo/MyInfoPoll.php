<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsClientIp;

final class MyInfoPoll extends MyInfoConstants
{
    public function render()
    {
        if (UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }

        return [
            $this->ID => [
                'phpLanguage' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '-',
                'ipv4' => UtilsClientIp::getV4(),
                'ipv6' => UtilsClientIp::getV6(),
            ],
        ];
    }
}
