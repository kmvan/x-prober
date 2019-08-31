<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Conf extends MyInfoConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'ip'          => HelperApi::getClientIp(),
            'phpLanguage' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '-',
        );

        return $conf;
    }
}
