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
            'changelogUrl' => ConfigApi::$CHANGELOG_URL,
            'appName'      => ConfigApi::$APP_NAME,
            'appUrl'       => ConfigApi::$APP_URL,
            'authorUrl'    => ConfigApi::$AUTHOR_URL,
            'authorName'   => ConfigApi::$AUTHOR_NAME,
        ];

        return $conf;
    }
}
