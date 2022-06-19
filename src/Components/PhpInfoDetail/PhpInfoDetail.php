<?php

namespace InnStudio\Prober\Components\PhpInfoDetail;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Xconfig\XconfigApi;

final class PhpInfoDetail extends PhpInfoDetailConstants
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if (XconfigApi::isDisabled($this->ID)) {
                return $action;
            }

            if ($this->ID !== $action) {
                return $action;
            }

            phpinfo();

            exit;
        });
    }
}
