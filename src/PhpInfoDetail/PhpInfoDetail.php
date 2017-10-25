<?php

namespace InnStudio\Prober\PhpInfoDetail;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;

class PhpInfoDetail
{
    public function __construct()
    {
        Events::on('init', array($this, 'filter'));
    }

    public function filter()
    {
        if (Helper::isAction('phpInfo')) {
            \phpinfo();

            die;
        }
    }
}
