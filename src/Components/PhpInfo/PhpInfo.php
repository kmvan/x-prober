<?php

namespace InnStudio\Prober\Components\PhpInfo;

final class PhpInfo
{
    public function __construct()
    {
        new Conf();
        new FetchLatestPhpVersion();
    }
}
