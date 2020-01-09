<?php

namespace InnStudio\Prober\Components\TemperatureSensor;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class TemperatureSensor
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('temperature-sensor' !== $action) {
            return $action;
        }

        $response = new RestfulResponse();
        $items    = $this->getItems();

        if ($items) {
            $response->setData($items);
        } else {
            $response->setStatus(HttpStatus::$NO_CONTENT);
        }

        $response->dieJson();
    }

    private function curl($url)
    {
        if ( ! \function_exists('\\curl_init')) {
            return null;
        }

        $ch = \curl_init();
        \curl_setopt_array($ch, array(
            \CURLOPT_URL            => $url,
            \CURLOPT_RETURNTRANSFER => true,
        ));
        $res = \curl_exec($ch);
        \curl_close($ch);

        return (string) $res;
    }

    private function getItems()
    {
        $items = array();

        foreach (ConfigApi::$APP_TEMPERATURE_SENSOR_PORTS as $port) {
            // check curl
            $res = $this->curl(ConfigApi::$APP_TEMPERATURE_SENSOR_URL . ":{$port}");

            if ( ! $res) {
                continue;
            }

            $item = \json_decode($res, true);

            if ( ! $item || ! \is_array($item)) {
                continue;
            }

            $items = $item;

            break;
        }

        return $items;
    }
}
