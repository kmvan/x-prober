<?php

namespace InnStudio\Prober\Timezone;

use InnStudio\Prober\Events\EventsApi;

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
