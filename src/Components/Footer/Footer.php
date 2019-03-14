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
        EventsApi::on('footerOutline', array($this, 'filterfooterOutline'));
    }

    public function filter($content)
    {
        $authorUrl  = ConfigApi::$AUTHOR_URL;
        $authorName = I18nApi::_(ConfigApi::$AUTHOR_NAME);
        $appName    = I18nApi::_(ConfigApi::$APP_NAME);

        $timer      = (\microtime(true) - (\defined('\XPROBER_TIMER') ? \XPROBER_TIMER : 0)) * 1000;
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
HTML;
    }

    public function filterfooterOutline($content)
    {
        $appUrl = I18nApi::_(ConfigApi::$APP_URL);
        $lang   = I18nApi::_('STAR 🌟 ME');

        return $content .= <<<HTML
<a href="{$appUrl}" target="_blank" class="inn-forkme__link">{$lang}</a>
HTML;
    }
}
