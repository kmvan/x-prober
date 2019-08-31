<?php

namespace InnStudio\Prober\Components\PhpInfo;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class FetchLatestPhpVersion extends PhpInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('latest-php-version' !== $action) {
            return $action;
        }

        $response = new RestfulResponse();
        $content  = \file_get_contents('https://www.php.net/releases/?json');

        if ( ! $content) {
            $response->setStatus(HttpStatus::$NOT_FOUND);
            $response->dieJson();
        }

        $versions = \json_decode($content, true);

        if ( ! $versions) {
            $response->setStatus(HttpStatus::$NOT_FOUND);
            $response->dieJson();
        }

        $version = isset($versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['version']) ? $versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['version'] : '';

        if ( ! $version) {
            $response->setStatus(HttpStatus::$NOT_FOUND);
            $response->dieJson();
        }

        $response->setData(array(
            'version' => $version,
            'date'    => $versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['date'],
        ));
        $response->dieJson();
    }
}
