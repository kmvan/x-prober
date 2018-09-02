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
<a href="<?php echo I18n::_(Config::$APP_URL); ?>" target="_blank"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"></a>
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
