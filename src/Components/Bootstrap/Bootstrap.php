<?php

namespace InnStudio\Prober\Components\Bootstrap;

class Bootstrap
{
    public function __construct()
    {
        new Action();
        new Conf();
        new Render();
    }
}
