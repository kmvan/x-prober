<?php

namespace InnStudio\Prober\Components\MyInfo;

final class MyInfo
{
    public function __construct()
    {
        new Conf();
        new ClientLocationIpv4();
    }
}
