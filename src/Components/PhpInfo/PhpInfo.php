<?php

namespace InnStudio\Prober\Components\PhpInfo;

class PhpInfo
{
    public function __construct()
    {
        new Conf();
        new FetchLatestPhpVersion();
    }
}
