<?php

namespace InnStudio\Prober\Components\TemperatureSensor;

use Exception;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class TemperatureSensorPoll extends TemperatureSensorConstants
{
    public function render()
    {
        if (UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }
        $items = $this->getItems();
        if ( ! $items) {
            return [
                $this->ID => null,
            ];
        }
        if ($items) {
            return [
                $this->ID => $items,
            ];
        }
        $cpuTemp = $this->getCpuTemp();
        if ( ! $cpuTemp) {
            return [
                $this->ID => null,
            ];
        }
        $items[] = [
            'id' => 'cpu',
            'name' => 'CPU',
            'celsius' => round((float) $cpuTemp / 1000, 2),
        ];

        return [
            $this->ID => $items,
        ];
    }

    private function curl($url)
    {
        if ( ! \function_exists('curl_init')) {
            return (string) file_get_contents($url);
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            \CURLOPT_URL => $url,
            \CURLOPT_RETURNTRANSFER => true,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return (string) $res;
    }

    private function getItems()
    {
        $items = [];
        foreach (ConfigApi::$config['APP_TEMPERATURE_SENSOR_PORTS'] as $port) {
            // check curl
            $res = $this->curl(ConfigApi::$config['APP_TEMPERATURE_SENSOR_URL'] . ":{$port}");
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
