<?php

namespace InnStudio\Prober\Components\ServerStatus;

final class ServerStatus
{
    public function __construct()
    {
        new Conf();
        new Fetch();
    }
}
