<?php

namespace InnStudio\Prober\Components\Style;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Style
{
    private $ID = 'style';

    public function __construct()
    {
        // EventsApi::on('init', [$this, 'filter']);
    }

    public function filter($action)
    {
        switch ($action) {
        case 'style':
            $this->output();
        }

        return $action;
    }

    private function output()
    {
        HelperApi::setFileCacheHeader();

        \header('Content-type: text/css');
        echo <<<'HTML'
{INN_STYLE}
HTML;
        die;
    }
}
