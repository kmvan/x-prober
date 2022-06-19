<?php

namespace InnStudio\Prober\Components\Fetch;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;

final class Fetch
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('fetch' === $action) {
                EventsApi::emit('fetchBefore');
                $response = new RestResponse(EventsApi::emit('fetch', array()));
                $response->json()->end();
            }

            return $action;
        }, 100);
    }
}
