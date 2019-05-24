<?php

namespace InnStudio\Prober\Components\Nav;

use InnStudio\Prober\Components\Events\EventsApi;

class Nav
{
    private $ID = 'nav';

    public function __construct()
    {
        EventsApi::on('footerOutline', array($this, 'filter'));
    }

    public function filter($content)
    {
        $content .= <<<HTML
<nav class="inn-nav"></nav>
HTML;

        return $content;
    }
}
