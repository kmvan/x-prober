<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Events\EventsApi;

class Action
{
    public function __construct()
    {
        $action = (string) \filter_input(\INPUT_GET, 'action', \FILTER_SANITIZE_STRING);
        EventsApi::emit('init', $action);

        if ($action) {
            \http_response_code(400);
            exit;
        }
    }
}
