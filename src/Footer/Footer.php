<?php

namespace InnStudio\Prober\Footer;

use InnStudio\Prober\Config\Api as Config;
use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class Footer
{
    private $ID = 'footer';

    public function __construct()
    {
        Events::on('footer', array($this, 'filter'));
        Events::on('style', array($this, 'filterStyle'));
    }

    public function filter()
    {
        $timer = (\microtime(true) - TIMER) * 1000; ?>
<div class="poi-container">
    <div class="footer">
        <?php echo \sprintf(I18n::_('Generator %s'), '<a href="' . I18n::_(Config::$APP_URL) . '" target="_blank">' . I18n::_(Config::$APP_NAME) . '</a>'); ?> 
        /
        <?php echo \sprintf(I18n::_('Author %s'), '<a href="' . I18n::_(Config::$AUTHOR_URL) . '" target="_blank">' . I18n::_(Config::$AUTHOR_NAME) . '</a>'); ?> 
        /
        <?php echo Helper::formatBytes(\memory_get_usage()); ?>
        /
        <?php echo \sprintf('%01.2f', $timer); ?>ms
    </div>
</div>
        <?php
    }

    public function filterStyle()
    {
        ?>
<style>
.footer{
    text-align: center;
    margin: 2rem auto 5rem;
    padding: .5rem 1rem;
}
@media (min-width: 768px) {
    .footer{
        background: #333;
        color: #ccc;
        width: 60%;
        border-radius: 10rem;
    }
    .footer a{
        color: #fff;
    }
}
    .footer a:hover{
        text-decoration: underline;
    }
</style>
        <?php
    }
}
