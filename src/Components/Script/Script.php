<?php

namespace InnStudio\Prober\Components\Script;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Utils\UtilsApi;

final class Script
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            if ('script' !== $action) {
                return $action;
            }

            $this->output();
        });
    }

    private function output()
    {
        UtilsApi::setFileCacheHeader();

        header('Content-type: application/javascript');
        echo <<<'HTML'
{INN_SCRIPT}
HTML;

        exit;
    }
}
