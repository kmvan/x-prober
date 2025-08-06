<?php

namespace InnStudio\Prober\Components\Style;

use InnStudio\Prober\Components\Utils\UtilsApi;

final class StyleAction
{
    public function render($action)
    {
        if ('style' !== $action) {
            return;
        }
        $this->output();
    }

    private function output()
    {
        UtilsApi::setFileCacheHeader();
        header('Content-type: text/css');
        echo <<<'CODE'
{{X_STYLE}}
CODE;
        exit;
    }
}
