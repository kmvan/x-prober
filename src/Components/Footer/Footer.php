<?php

namespace InnStudio\Prober\Components\Footer;

use InnStudio\Prober\Components\Events\EventsApi;

class Footer
{
    private $ID = 'footer';

    public function __construct()
    {
        EventsApi::on('conf', [$this, 'conf'], \PHP_INT_MAX);
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = [
            'memUsage' => \memory_get_usage(),
            'time'     => \microtime(true) - (\defined('\XPROBER_TIMER') ? \XPROBER_TIMER : 0),
        ];

        return $conf;
    }
}
