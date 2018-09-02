<?php

namespace InnStudio\Prober\PhpExtensionInfo;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class PhpExtensionInfo
{
    private $ID = 'phpExtensionInfo';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 400);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
        'title'     => I18n::_('PHP extensions'),
        'tinyTitle' => I18n::_('Ext'),
        'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        echo <<<HTML
<div class="row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Memcache'),
                'content' => Helper::getIni(0, \extension_loaded('memcache') && \class_exists('\\Memcache')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Memcached'),
                'content' => Helper::getIni(0, \extension_loaded('memcached') && \class_exists('\\Memcached')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Redis'),
                'content' => Helper::getIni(0, \extension_loaded('redis') && \class_exists('\\Redis')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Opcache'),
                'content' => Helper::getIni(0, \function_exists('\\opcache_get_configuration')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s enabled'), 'Opcache'),
                'content' => Helper::getIni(0, $this->isOpcEnabled()),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Swoole'),
                'content' => Helper::getIni(0, \extension_loaded('Swoole') && \function_exists('\\swoole_version')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Imagick'),
                'content' => Helper::getIni(0, \extension_loaded('Imagick') && \class_exists('\\Imagick')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Exif'),
                'content' => Helper::getIni(0, \extension_loaded('Exif') && \function_exists('\\exif_imagetype')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Sockets'),
                'content' => Helper::getIni(0, \extension_loaded('Sockets') && \function_exists('\\socket_accept')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'MySQLi'),
                'content' => Helper::getIni(0, \extension_loaded('MySQLi') && \class_exists('\\mysqli')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Zip'),
                'content' => Helper::getIni(0, \extension_loaded('Zip') && \class_exists('\\ZipArchive')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Multibyte String'),
                'content' => Helper::getIni(0, \extension_loaded('mbstring') && \function_exists('\\mb_substr')),
            ),
            array(
                'label'   => I18n::_('Zend Optimizer'),
                'content' => Helper::getIni(0, \function_exists('zend_optimizer_version')),
            ),
            array(
                'col'     => '1-1',
                'label'   => I18n::_('Loaded extensions'),
                'title'   => 'loaded_extensions',
                'content' => \implode(', ', $this->getLoadedExtensions(true)) ?: '-',
            ),
        );

        // order
        $itemsOrder = array();

        foreach ($items as $item) {
            $itemsOrder[] = $item['label'];
        }

        \array_multisort($items, $itemsOrder);

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';
            $content .= <<<HTML
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$title} {$id}>{$item['content']}</div>
    </div>
</div>
HTML;
        }

        return $content;
    }

    private function getLoadedExtensions($sorted = false)
    {
        $exts = \get_loaded_extensions();

        if ($sorted) {
            \sort($exts);
        }

        return $exts;
    }

    private function isOpcEnabled()
    {
        $isOpcEnabled = \function_exists('\\opcache_get_configuration');

        if ($isOpcEnabled) {
            $isOpcEnabled = \opcache_get_configuration();
            $isOpcEnabled = isset($isOpcEnabled['directives']['opcache.enable']) && true === $isOpcEnabled['directives']['opcache.enable'];
        }

        return $isOpcEnabled;
    }
}
