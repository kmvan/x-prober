<?php

namespace InnStudio\Prober\Components\Bootstrap;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class Bootstrap
{
    public function __construct()
    {
        EventsApi::emit('fetch');
        EventsApi::emit('init');

        echo $this->getDisplay();
    }

    private function getFieldsets()
    {
        $html = '';

        foreach (EventsApi::emit('mods', array()) as $id => $item) {
            $content = \call_user_func($item['display']);
            $html .= <<<HTML
<fieldset class="inn-mod inn-{$id}-mod" id="inn-{$id}-mod">
    <legend class="inn-mod__title inn-{$id}-mod__title">
        <span class="inn-mod__title__text is-full">{$item['title']}</span>
        <span class="inn-mod__title__text is-tiny">{$item['tinyTitle']}</span>
    </legend>
    <div class="inn-mod__body inn-{$id}-mod__body">
        {$content}
    </div>
</fieldset>
HTML;
        }

        return $html;
    }

    private function getDisplay()
    {
        $appName       = I18nApi::_(ConfigApi::$APP_NAME);
        $appUrl        = I18nApi::_(ConfigApi::$APP_URL);
        $version       = ConfigApi::$APP_VERSION;
        $scriptConf    = \json_encode(EventsApi::emit('conf', array()));
        $footer        = EventsApi::emit('footer', '');
        $footerOutline = EventsApi::emit('footerOutline', '');
        $scriptUrl     = \defined('\XPROBER_IS_DEV') && \XPROBER_IS_DEV ? "../tmp/app.js?v={$_SERVER['REQUEST_TIME']}" : "?action=getScript&amp;v={$version}";
        $styleUrl      = \defined('\XPROBER_IS_DEV') && \XPROBER_IS_DEV ? "../tmp/app.css?v={$_SERVER['REQUEST_TIME']}" : "?action=getStyle&amp;v={$version}";

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="renderer" content="webkit">
    <title>{$appName} v{$version}</title>
    <link rel="stylesheet" href="{$styleUrl}">
    <script>var CONF = {$scriptConf};</script>
    <script src="{$scriptUrl}" async></script>
</head>
<body>
<h1 class="inn-title" id="inn-title">
    <a class="inn-title__link" href="{$appUrl}" target="_blank">{$appName} v{$version}</a>
</h1>
<div class="inn-app">
    <div class="inn-container">
        {$this->getFieldsets()}
    </div>
    {$footer}
</div>
{$footerOutline}
</body>
</html>
HTML;
    }
}
