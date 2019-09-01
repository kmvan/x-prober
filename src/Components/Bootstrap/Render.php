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
<div style="display:flex;height:calc(100vh - 16px);width:calc(100vw - 16px);align-items:center;justify-content:center;flex-wrap:wrap;">
    <div style="font-size:15px;background:#333;color:#fff;padding:0.5rem 1rem;border-radius:10rem;box-shadow: 0 5px 10px rgba(0,0,0,0.3);">⏳ Loading...</div>
</div>
</body>
</html>
HTML;
    }
}
