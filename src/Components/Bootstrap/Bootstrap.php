<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Action\Action;

final class Bootstrap
{
    public static $dir;

    public function __construct($dir)
    {
        error_reporting(\E_ALL);
        self::$dir = $dir;
        new Action();
        new Render();
    }
}
