<?php

namespace InnStudio\Prober\Components\PhpInfo;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class PhpInfoLatestPhpVersionAction extends PhpInfoConstants
{
    public function render($action)
    {
        if ('latestPhpVersion' !== $action) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled($this->ID)) {
            $response
                ->setStatus(StatusCode::$FORBIDDEN)
                ->end();
        }
        $content = file_get_contents('https://www.php.net/releases/?json');
        if ( ! $content) {
            $response
                ->setStatus(StatusCode::$NO_CONTENT)
                ->end();
        }
        $versions = json_decode($content, true);
        if ( ! $versions) {
            $response
                ->setStatus(StatusCode::$NO_CONTENT)
                ->end();
        }
        $version = isset($versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['version']) ? $versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['version'] : '';
        if ( ! $version) {
            $response
                ->setStatus(StatusCode::$NO_CONTENT)
                ->end();
        }
        $response
            ->setData([
                'version' => $version,
                'date' => $versions[ConfigApi::$LATEST_PHP_STABLE_VERSION]['date'],
            ])
            ->end();
    }
}
