<?php

namespace InnStudio\Prober\Components\ServerStatus;

class ServerStatus
{
    public function __construct()
    {
        new Conf();
        new Fetch();
    }
}
