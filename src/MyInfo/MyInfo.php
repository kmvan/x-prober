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

    public function filter(array $mods)
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
        return \implode('', \array_map(function (array $item) {
            return <<<HTML
<div class="form-group">
    <div class="group-label">{$item[0]}</div>
    <div class="group-content">{$item[1]}</div>
</div>
HTML;
        }, array(
            array(I18nApi::_('My IP'), HelperApi::getClientIp()),
            array(I18nApi::_('My browser UA'), isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
            array(I18nApi::_('My browser language'), isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : ''),
        )));
    }
}
