<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Conf extends MyInfoConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $conf;
        }

        $conf[$this->ID] = array(
            'phpLanguage' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '-',
        );

        return $conf;
    }
}
