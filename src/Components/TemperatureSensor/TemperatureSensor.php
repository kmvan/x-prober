<?php

namespace InnStudio\Prober\Components\TemperatureSensor;

use Exception;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;

final class TemperatureSensor
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('temperature-sensor' !== $action) {
                return $action;
            }

            $response = new RestResponse();
            $items    = $this->getItems();

            if ($items) {
                $response->setData($items)->json()->end();
            }

            $cpuTemp = $this->getCpuTemp();

            if ( ! $cpuTemp) {
                $response->setStatus(StatusCode::$NO_CONTENT);
            }

            $items[] = array(
                'id'      => 'cpu',
                'name'    => 'CPU',
                'celsius' => round((float) $cpuTemp / 1000, 2),
            );

            $response->setData($items)->json()->end();
        });
    }

    private function curl($url)
    {
        if ( ! \function_exists('curl_init')) {
            return;
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
            \CURLOPT_URL            => $url,
            \CURLOPT_RETURNTRANSFER => true,
        ));
        $res = curl_exec($ch);
        curl_close($ch);

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

            $item = json_decode($res, true);

            if ( ! $item || ! \is_array($item)) {
                continue;
            }

            $items = $item;

            break;
        }

        return $items;
    }

    private function getCpuTemp()
    {
        try {
            $path = '/sys/class/thermal/thermal_zone0/temp';

            return file_exists($path) ? (int) file_get_contents($path) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}
