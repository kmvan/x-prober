<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class Conf extends ServerBenchmarkConstants
{
    public function __construct()
    {
        EventsApi::on('conf', [$this, 'conf']);
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = [
            'disabledMyServerBenchmark' => XconfigApi::isDisabled('myServerBenchmark'),
        ];

        return $conf;
    }
}
