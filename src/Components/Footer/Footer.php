<?php

namespace InnStudio\Prober\Components\Footer;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class Footer
{
    private $ID = 'footer';

    public function __construct()
    {
        EventsApi::on('footer', array($this, 'filter'));
    }

    public function filter($content)
    {
        $authorUrl  = ConfigApi::$AUTHOR_URL;
        $authorName = I18nApi::_(ConfigApi::$AUTHOR_NAME);
        $appName    = I18nApi::_(ConfigApi::$APP_NAME);

        $timer      = (\microtime(true) - TIMER) * 1000;
        $appUrl     = I18nApi::_(ConfigApi::$APP_URL);
        $footerName = \sprintf(
            I18nApi::_('Generator %s'),
            <<<HTML
<a href="{$appUrl}" target="_blank">{$appName}</a>
HTML
);
        $footerAuthor = \sprintf(
            I18nApi::_('Author %s'),
            <<<HTML
<a href="{$authorUrl}" target="_blank">{$authorName}</a>
HTML
);
        $memUsage = HelperApi::formatBytes(\memory_get_usage());
        $time     = \sprintf('%01.2f', $timer);

        return $content .= <<<HTML
<div class="inn-container">
    <div class="inn-footer">
        {$footerName} / {$footerAuthor} / {$memUsage} / {$time}ms
    </div>
</div>
<a href="{$appUrl}" target="_blank" class="inn-forkme__link">
    <img class="inn-forkme__img" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" />
</a>
HTML;
    }
}
