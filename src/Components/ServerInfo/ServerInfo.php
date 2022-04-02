<?php

namespace InnStudio\Prober\Components\ServerInfo;

final class ServerInfo
{
    public function __construct()
    {
        new Conf();
        new Fetch();
        new ServerInitIpv4();
        new ServerInitIpv6();
        new ServerLocationIpv4();
    }
}
