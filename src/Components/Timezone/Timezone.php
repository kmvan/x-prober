<?php

namespace InnStudio\Prober\Components\Timezone;

use InnStudio\Prober\Components\Events\EventsApi;

final class Timezone
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ( ! \ini_get('date.timezone')) {
                date_default_timezone_set('GMT');
            }

            return $action;
        }, 1);
    }
}
