<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;

class Conf extends BootstrapConstants
{
    public function __construct()
    {
        EventsApi::on('conf', [$this, 'conf']);
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = [
            'version'      => ConfigApi::$APP_VERSION,
            'appName'      => ConfigApi::$APP_NAME,
            'appUrl'       => ConfigApi::$APP_URL,
            'appConfigUrl' => ConfigApi::$APP_CONFIG_URL,
            'authorUrl'    => ConfigApi::$AUTHOR_URL,
            'authorName'   => ConfigApi::$AUTHOR_NAME,
        ];

        return $conf;
    }
}
