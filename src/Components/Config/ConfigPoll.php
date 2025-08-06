<?php

namespace InnStudio\Prober\Components\Config;

final class ConfigPoll extends ConfigConstants
{
    public function render()
    {
        return [
            $this->ID => [
                'IS_DEV' => XPROBER_IS_DEV,
                'APP_VERSION' => ConfigApi::$APP_VERSION,
                'APP_NAME' => ConfigApi::$APP_NAME,
                'APP_URL' => ConfigApi::$APP_URL,
                'AUTHOR_URL' => ConfigApi::$AUTHOR_URL,
                'UPDATE_PHP_URLS' => ConfigApi::$UPDATE_PHP_URLS,
                'APP_CONFIG_URLS' => ConfigApi::$APP_CONFIG_URLS,
                'APP_CONFIG_URL_DEV' => ConfigApi::$APP_CONFIG_URL_DEV,
                'APP_TEMPERATURE_SENSOR_URL' => ConfigApi::$APP_TEMPERATURE_SENSOR_URL,
                'APP_TEMPERATURE_SENSOR_PORTS' => ConfigApi::$APP_TEMPERATURE_SENSOR_PORTS,
                'AUTHOR_NAME' => ConfigApi::$AUTHOR_NAME,
                'LATEST_PHP_STABLE_VERSION' => ConfigApi::$LATEST_PHP_STABLE_VERSION,
                'LATEST_NGINX_STABLE_VERSION' => ConfigApi::$LATEST_NGINX_STABLE_VERSION,
            ],
        ];
    }
}
