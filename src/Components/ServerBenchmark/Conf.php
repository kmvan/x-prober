<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Conf extends ServerBenchmarkConstants
{
    public function __construct()
    {
        EventsApi::on('conf', function (array $conf) {
            $conf[$this->ID] = array(
                'disabledMyServerBenchmark' => XconfigApi::isDisabled('myServerBenchmark'),
            );

            return $conf;
        });
    }
}
