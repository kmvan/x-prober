<?php

namespace InnStudio\Prober\Components\Poll;

use InnStudio\Prober\Components\Rest\RestResponse;

final class PollAction extends PoolConstants
{
    public function render($action)
    {
        if ($action !== $this->ID) {
            return;
        }
        $data = [];
        foreach ([
            'Config\\ConfigPoll',
            'UserConfig\\UserConfigPoll',
            'PhpInfo\\PhpInfoPoll',
            'Database\\DatabasePoll',
            'MyInfo\\MyInfoPoll',
            'DiskUsage\\DiskUsagePoll',
            'PhpExtensions\\PhpExtensionsPoll',
            'NetworkStats\\NetworkStatsPoll',
            'ServerStatus\\ServerStatusPoll',
            'ServerInfo\\ServerInfoPoll',
            'Nodes\\NodesPoll',
            'TemperatureSensor\\TemperatureSensorPoll',
            'ServerBenchmark\\ServerBenchmarkPoll',
        ] as $fn) {
            $class = "\\InnStudio\\Prober\\Components\\{$fn}";
            $data = array_merge($data, (new $class())->render());
        }
        (new RestResponse())
            ->setData($data)
            ->end();
    }
}
