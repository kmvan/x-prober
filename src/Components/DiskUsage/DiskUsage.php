<?php

namespace InnStudio\Prober\Components\DiskUsage;

final class DiskUsage
{
    public function __construct()
    {
        new Conf();
        new Fetch();
    }
}
