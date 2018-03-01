<?php

namespace InnStudio\Prober\MyInfo;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class MyInfo
{
    private $ID = 'myInfo';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 900);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('My information'),
            'tinyTitle' => I18n::_('Mine'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        echo $this->getContent();
    }

    public function getContent()
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        return <<<HTML
<div class="form-group">
    <div class="group-label">{$this->_('My IP')}</div>
    <div class="group-content">{$this->getClientIp()}</div>
</div> 
<div class="form-group">
    <div class="group-label">{$this->_('My UA')}</div>
    <div class="group-content">{$ua}</div>
</div> 
HTML;
    }

    private function getClientIp()
    {
        return Helper::getClientIp();
    }

    private function _($str)
    {
        return I18n::_($str);
    }
}
