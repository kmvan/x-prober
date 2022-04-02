<?php

namespace InnStudio\Prober\Components\PhpInfo;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class FetchLatestPhpVersion extends PhpInfoConstants
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $action;
            }

            if ('latest-php-version' !== $action) {
                return $action;
            }

            $response = new RestResponse();
            $content  = file_get_contents('https://www.php.net/releases/?json');

            if ( ! $content) {
                $response->setStatus(StatusCode::$NOT_FOUND)->end();
            }

            $versions = json_decode($content, true);

            if ( ! $versions) {
                $response->setStatus(StatusCode::$NOT_FOUND)->end();
            }

            $version = isset($versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['version']) ? $versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['version'] : '';

            if ( ! $version) {
                $response->setStatus(StatusCode::$NOT_FOUND)->end();
            }

            $response->setData(array(
                'version' => $version,
                'date'    => $versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['date'],
            ))->json()->end();
        });
    }
}
