<?php

namespace InnStudio\Prober\Components\Style;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;

final class Style
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('style' !== $action) {
                return $action;
            }

            $this->output();
        });
    }

    private function output()
    {
        UtilsApi::setFileCacheHeader();

        header('Content-type: text/css');
        echo <<<'HTML'
{INN_STYLE}
HTML;

        exit;
    }
}
