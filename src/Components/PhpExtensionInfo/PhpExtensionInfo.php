<?php

namespace InnStudio\Prober\Components\PhpExtensionInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class PhpExtensionInfo
{
    private $ID = 'phpExtensionInfo';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 400);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('PHP extensions'),
            'tinyTitle' => I18nApi::_('Ext'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        return <<<HTML
<div class="inn-row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Redis'),
                'content' => HelperApi::getIni(0, \extension_loaded('redis') && \class_exists('\\Redis')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Memcache'),
                'content' => HelperApi::getIni(0, \extension_loaded('memcache') && \class_exists('\\Memcache')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Memcached'),
                'content' => HelperApi::getIni(0, \extension_loaded('memcached') && \class_exists('\\Memcached')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Opcache'),
                'content' => HelperApi::getIni(0, \function_exists('\\opcache_get_configuration')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s enabled'), 'Opcache'),
                'content' => HelperApi::getIni(0, $this->isOpcEnabled()),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Swoole'),
                'content' => HelperApi::getIni(0, \extension_loaded('swoole') && \function_exists('\\swoole_version')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Image Magic'),
                'content' => HelperApi::getIni(0, \extension_loaded('imagick') && \class_exists('\\Imagick')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Graphics Magick'),
                'content' => HelperApi::getIni(0, \extension_loaded('gmagick')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Exif'),
                'content' => HelperApi::getIni(0, \extension_loaded('Exif') && \function_exists('\\exif_imagetype')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Fileinfo'),
                'content' => HelperApi::getIni(0, \extension_loaded('fileinfo')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Sockets'),
                'content' => HelperApi::getIni(0, \extension_loaded('Sockets') && \function_exists('\\socket_accept')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'MySQLi'),
                'content' => HelperApi::getIni(0, \extension_loaded('MySQLi') && \class_exists('\\mysqli')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Zip'),
                'content' => HelperApi::getIni(0, \extension_loaded('zip') && \class_exists('\\ZipArchive')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Multibyte String'),
                'content' => HelperApi::getIni(0, \extension_loaded('mbstring') && \function_exists('\\mb_substr')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Phalcon'),
                'content' => HelperApi::getIni(0, \extension_loaded('phalcon')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Xdebug'),
                'content' => HelperApi::getIni(0, \extension_loaded('xdebug')),
            ),
            array(
                'label'   => I18nApi::_('Zend Optimizer'),
                'content' => HelperApi::getIni(0, \function_exists('zend_optimizer_version')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'ionCube'),
                'content' => HelperApi::getIni(0, \extension_loaded('ioncube loader')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s extension'), 'SourceGuardian'),
                'content' => HelperApi::getIni(0, \extension_loaded('sourceguardian')),
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('Loaded extensions'),
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

        return \implode('', \array_map(array(HelperApi::class, 'getGroup'), $items));
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
