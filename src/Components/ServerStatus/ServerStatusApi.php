<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Helper\HelperApi;

class ServerStatusApi
{
    public $ID = 'serverStatus';

    public function getMemUsage($key, $precent = false, $totalKey = 'MemTotal')
    {
        if (false === $precent) {
            return HelperApi::getMemoryUsage($key);
        }

        $total = HelperApi::getMemoryUsage($totalKey);

        if ( ! $total) {
            return 0;
        }

        return HelperApi::getMemoryUsage($key) ? \sprintf('%01.2f', HelperApi::getMemoryUsage($key) / $total * 100) : 0;
    }
}
