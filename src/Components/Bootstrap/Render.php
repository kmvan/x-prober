<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;

class Render
{
    public function __construct()
    {
        $appName    = ConfigApi::$APP_NAME;
        $version    = ConfigApi::$APP_VERSION;
        $scriptConf = \json_encode(EventsApi::emit('conf', array()));
        $scriptUrl  = \defined('\XPROBER_IS_DEV') && \XPROBER_IS_DEV ? "../.tmp/app.js?v={$_SERVER['REQUEST_TIME']}" : "?action=script&amp;v={$version}";

        echo <<<HTML
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
