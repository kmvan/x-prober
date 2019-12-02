<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;

class Conf extends BootstrapConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'isDev'           => \XPROBER_IS_DEV,
            'version'         => ConfigApi::$APP_VERSION,
            'appName'         => ConfigApi::$APP_NAME,
            'appUrl'          => ConfigApi::$APP_URL,
            'appConfigUrls'   => ConfigApi::$APP_CONFIG_URLS,
            'appConfigUrlDev' => ConfigApi::$APP_CONFIG_URL_DEV,
            'authorUrl'       => ConfigApi::$AUTHOR_URL,
            'authorName'      => ConfigApi::$AUTHOR_NAME,
            'authorization'   => isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '',
        );

        return $conf;
    }
}
