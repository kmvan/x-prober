<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Action\Action;
use InnStudio\Prober\Components\Timezone\Timezone;

final class Bootstrap
{
    public static $dir;

    public function __construct($dir)
    {
        error_reporting(\E_ALL);
        self::$dir = $dir;
        new Timezone();
        new Action();
        new Render();
    }
}
