<?php

namespace InnStudio\Prober\Components\Timezone;

use InnStudio\Prober\Components\Events\EventsApi;

class Timezone
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
        EventsApi::on('fetch', array($this, 'filter'));
    }

    public function filter()
    {
        if ( ! \ini_get('date.timezone')) {
            \date_default_timezone_set('GMT');
        }
    }
}
