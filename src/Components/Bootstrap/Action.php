<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Events\EventsApi;

final class Action
{
    public function __construct()
    {
        $action = (string) filter_input(\INPUT_GET, 'action', \FILTER_DEFAULT);
        EventsApi::emit('init', $action);

        if ($action) {
            http_response_code(400);

            exit;
        }
    }
}
