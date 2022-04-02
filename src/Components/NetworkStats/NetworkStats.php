<?php

namespace InnStudio\Prober\Components\NetworkStats;

final class NetworkStats
{
    public function __construct()
    {
        new Conf();
        new Fetch();
    }
}
