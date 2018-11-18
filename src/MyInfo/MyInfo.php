<?php

namespace InnStudio\Prober\MyInfo;

use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\Helper\HelperApi;
use InnStudio\Prober\I18n\I18nApi;

class MyInfo
{
    private $ID = 'myInfo';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 900);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('My information'),
            'tinyTitle' => I18nApi::_('Mine'),
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
        $ua   = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';

        return <<<HTML
<div class="form-group">
    <div class="group-label">{$this->_('My IP')}</div>
    <div class="group-content">{$this->getClientIp()}</div>
</div>
<div class="form-group">
    <div class="group-label">{$this->_('My browser UA')}</div>
    <div class="group-content">{$ua}</div>
</div>
<div class="form-group">
    <div class="group-label">{$this->_('My browser language')}</div>
    <div class="group-content">{$lang}</div>
</div>
HTML;
    }

    private function getClientIp()
    {
        return HelperApi::getClientIp();
    }

    private function _($str)
    {
        return I18nApi::_($str);
    }
}
