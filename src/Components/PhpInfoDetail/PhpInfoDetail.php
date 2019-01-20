<?php

namespace InnStudio\Prober\Components\PhpInfoDetail;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class PhpInfoDetail
{
    public function __construct()
    {
        EventsApi::on('fetch', array($this, 'filter'));
    }

    public function filter()
    {
        if (HelperApi::isAction('phpInfo')) {
            \phpinfo();

            die;
        }
    }
}
