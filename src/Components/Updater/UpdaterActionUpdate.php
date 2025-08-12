<?php

namespace InnStudio\Prober\Components\Updater;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;

final class UpdaterActionUpdate
{
    public function render($action)
    {
        if ('update' !== $action) {
            return $action;
        }
        $response = new RestResponse();
        // prevent update file on dev mode
        if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            $response
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        // check file writable
        if ( ! is_writable(__FILE__)) {
            $response
                ->setStatus(StatusCode::INSUFFICIENT_STORAGE)
                ->end();
        }
        $code = '';
        foreach (ConfigApi::$config['UPDATE_PHP_URLS'] as $url) {
            $curl = curl_init($url);
            curl_setopt($curl, \CURLOPT_RETURNTRANSFER, true);
            $status = (int) curl_getinfo($curl, \CURLINFO_HTTP_CODE);
            $code = (string) curl_exec($curl);
            curl_close($curl);
            if (StatusCode::OK !== $status || '' !== trim($code)) {
                break;
            }
        }
        if ( ! $code) {
            $response
                ->setStatus(StatusCode::NOT_FOUND)
                ->end();
        }
        if ((bool) file_put_contents(__FILE__, $code)) {
            if (\function_exists('opcache_invalidate')) {
                opcache_invalidate(__FILE__, true) || opcache_reset();
            }
            $response
                ->setStatus(StatusCode::CREATED)
                ->end();
        }
        $response
            ->setStatus(StatusCode::INTERNAL_SERVER_ERROR)
            ->end();
    }
}
