<?php

namespace InnStudio\Prober\Components\PhpInfoDetail;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

class PhpInfoDetail extends PhpInfoDetailConstants
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter($action)
    {
        if (XconfigApi::isDisabled($this->ID)) {
            return $action;
        }

        if ($this->ID !== $action) {
            return $action;
        }

        \phpinfo();

        exit;
    }
}
