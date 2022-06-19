<?php

namespace InnStudio\Prober\Components\Updater;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;

final class Updater
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('update' !== $action) {
                return $action;
            }

            $response = new RestResponse();

            // check file writable
            if ( ! is_writable(__FILE__)) {
                $response->setStatus(StatusCode::$INSUFFICIENT_STORAGE)->end();
            }

            $code = '';

            foreach (ConfigApi::$UPDATE_PHP_URLS as $url) {
                $code = (string) file_get_contents($url);

                if ('' !== trim($code)) {
                    break;
                }
            }

            if ( ! $code) {
                $response->setStatus(StatusCode::$NOT_FOUND)->end();
            }

            // prevent update file on dev mode
            if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
                $response->end();
            }

            if ((bool) file_put_contents(__FILE__, $code)) {
                if (\function_exists('opcache_invalidate')) {
                    opcache_invalidate(__FILE__, true) || opcache_reset();
                }

                $response->end();
            }

            $response->setStatus(StatusCode::$INTERNAL_SERVER_ERROR)->end();
        });
    }
}
