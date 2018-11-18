<?php

namespace InnStudio\Prober\PhpInfoDetail;

use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\Helper\HelperApi;

class PhpInfoDetail
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter()
    {
        if (HelperApi::isAction('phpInfo')) {
            \phpinfo();

            die;
        }
    }
}
