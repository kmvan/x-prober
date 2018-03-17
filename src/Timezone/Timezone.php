<?php

namespace InnStudio\Prober\Timezone;

use InnStudio\Prober\Events\Api as Events;

class Timezone
{
    public function __construct()
    {
        Events::on('init', array($this, 'filter'));
        Events::on('fetch', array($this, 'filter'));
    }

    public function filter()
    {
        if ( ! \ini_get('date.timezone')) {
            \date_default_timezone_set('GMT');
        }
    }
}
