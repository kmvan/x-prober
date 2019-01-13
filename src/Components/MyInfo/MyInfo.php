<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

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
        $items = array(
            array(
                'label'   => I18nApi::_('My IP'),
                'col'     => null,
                'content' => HelperApi::getClientIp(),
            ),
            array(
                'label'   => I18nApi::_('My browser UA'),
                'col'     => null,
                'content' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ),
            array(
                'label'   => I18nApi::_('My browser language'),
                'col'     => null,
                'content' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            ),
        );

        return \implode('', \array_map(array(HelperApi::class, 'getGroup'), $items));
    }
}
