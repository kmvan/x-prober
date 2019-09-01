<?php

namespace InnStudio\Prober\Components\Updater;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class Updater
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('update' !== $action) {
            return $action;
        }

        $response = new RestfulResponse();

        // check file writable
        if ( ! \is_writable(__FILE__)) {
            $response->setStatus(HttpStatus::$INSUFFICIENT_STORAGE);
            $response->dieJson();
        }

        $code = '';

        foreach (ConfigApi::$UPDATE_PHP_URLS as $url) {
            $code = (string) \file_get_contents($url);

            if ('' !== \trim($code)) {
                break;
            }
        }

        if ( ! $code) {
            $response->setStatus(HttpStatus::$NOT_FOUND);
            $response->dieJson();
        }

        // prevent update file on dev mode
        if (\XPROBER_IS_DEV) {
            $response->dieJson();
        }

        if ((bool) \file_put_contents(__FILE__, $code)) {
            if (\function_exists('\\opcache_compile_file')) {
                @\opcache_compile_file(__FILE__) || \opcache_reset();
            }

            $response->dieJson();
        }

        $response->setStatus(HttpStatus::$INTERNAL_SERVER_ERROR);
        $response->dieJson();
    }
}
