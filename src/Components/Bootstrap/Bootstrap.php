<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class Bootstrap
{
    public function __construct()
    {
        EventsApi::emit('init', (string) \filter_input(\INPUT_GET, 'action', \FILTER_SANITIZE_STRING));
        new Conf();

        echo $this->getDisplay();
    }

    private function getDisplay()
    {
        $appName    = I18nApi::_(ConfigApi::$APP_NAME);
        $appUrl     = I18nApi::_(ConfigApi::$APP_URL);
        $version    = ConfigApi::$APP_VERSION;
        $scriptConf = \json_encode(EventsApi::emit('conf', []));
        $scriptUrl  = \defined('\XPROBER_IS_DEV') && \XPROBER_IS_DEV ? "../tmp/app.js?v={$_SERVER['REQUEST_TIME']}" : "?action=script&amp;v={$version}";

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="renderer" content="webkit">
    <title>{$appName} v{$version}</title>
    <script>var CONF = {$scriptConf};</script>
    <script src="{$scriptUrl}" async></script>
</head>
<body>
</body>
</html>
HTML;
    }
}
