<?php

namespace InnStudio\Prober\Components\Script;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Script
{
    private $ID = 'script';

    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter()
    {
        switch (true) {
        case true === HelperApi::isAction('getScript'):
            $this->displayDefault();
        }
    }

    private function displayDefault()
    {
        HelperApi::setFileCacheHeader();

        \header('Content-type: application/javascript');
        echo <<<'HTML'
{INN_SCRIPT}
HTML;
        die;
    }
}
