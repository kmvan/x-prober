<?php

namespace InnStudio\Prober\Components\Style;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Style
{
    private $ID = 'style';

    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter()
    {
        switch (true) {
        case true === HelperApi::isAction('getStyle'):
            $this->displayDefault();
        }
    }

    private function displayDefault()
    {
        HelperApi::setFileCacheHeader();

        \header('Content-type: text/css');
        echo <<<'HTML'
{INN_STYLE}
HTML;
        die;
    }
}
