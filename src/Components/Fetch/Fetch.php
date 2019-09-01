<?php

namespace InnStudio\Prober\Components\Fetch;

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
        if ('fetch' === $action) {
            EventsApi::emit('fetchBefore');
            $response = new RestfulResponse(EventsApi::emit('fetch', array()));
            $response->dieJson();
        }

        return $action;
    }
}
