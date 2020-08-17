<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Events\EventsApi;

class Action
{
    public function __construct()
    {
        \header('Access-Control-Allow-Origin: *');

        EventsApi::emit('init', (string) \filter_input(\INPUT_GET, 'action', \FILTER_SANITIZE_STRING));
    }
}
