<?php

namespace InnStudio\Prober\Components\Bootstrap;

final class Bootstrap
{
    public function __construct()
    {
        new Action();
        new Conf();
        new Render();
    }
}
