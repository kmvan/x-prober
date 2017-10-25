<?php

namespace InnStudio\Prober\Awesome;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\I18n\Api as I18n;

class Awesome
{
    private $ID          = 'awesome';
    private $ZH_CN_URL   = 'https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css';
    private $DEFAULT_URL = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';

    public function __construct()
    {
        // Events::on('style', array($this, 'filter'), 1);
    }

    public function filter()
    {
        ?>
<link rel="stylesheet" href="<?php echo $this->getUrl(); ?>">
        <?php
    }

    private function getUrl()
    {
        switch (I18n::getClientLang()) {
            case 'zh-CN':
                return $this->ZH_CN_URL;
        }

        return $this->DEFAULT_URL;
    }
}
