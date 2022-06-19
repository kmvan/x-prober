<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class Conf extends NodesApi
{
    public function __construct()
    {
        EventsApi::on('conf', function (array $conf) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $conf;
            }

            $conf[$this->ID] = array(
                'items' => $this->getNodes(),
            );

            return $conf;
        });
    }
}
