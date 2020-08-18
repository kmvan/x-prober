<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class Fetch
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'), 100);
    }

    public function filter($action)
    {
        if ('nodes' === $action) {
            EventsApi::emit('nodeBefore');
            $response = new RestfulResponse(EventsApi::emit('nodes', array()));
            $response->dieJson();
        }

        return $action;
    }
}
