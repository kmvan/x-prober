<?php

namespace InnStudio\Prober\Components\Fetch;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;

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
            $response = new RestResponse(EventsApi::emit('fetch', array()));
            $response->json()->end();
        }

        return $action;
    }
}
