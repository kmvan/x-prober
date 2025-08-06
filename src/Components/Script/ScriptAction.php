<?php

namespace InnStudio\Prober\Components\Script;

use InnStudio\Prober\Components\Utils\UtilsApi;

final class ScriptAction
{
    public function render($action)
    {
        if ('script' !== $action) {
            return;
        }
        $this->output();
    }

    private function output()
    {
        UtilsApi::setFileCacheHeader();
        header('Content-type: application/javascript');
        echo <<<'CODE'
{{X_SCRIPT}}
CODE;
        exit;
    }
}
