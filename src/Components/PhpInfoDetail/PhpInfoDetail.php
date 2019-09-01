<?php

namespace InnStudio\Prober\Components\PhpInfoDetail;

use InnStudio\Prober\Components\Events\EventsApi;

class PhpInfoDetail
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if ('phpInfo' !== $action) {
            return $action;
        }

        \phpinfo();

        die;
    }
}
