<?php

namespace InnStudio\Prober\Components\NetworkStats;

class NetworkStats
{
    public function __construct()
    {
        new Conf();
        new Fetch();
    }
}
