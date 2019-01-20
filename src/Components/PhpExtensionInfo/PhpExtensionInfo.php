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
        EventsApi::on('mods', [$this, 'filter'], 400);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = [
            'title'     => I18nApi::_('PHP extensions'),
            'tinyTitle' => I18nApi::_('Ext'),
            'display'   => [$this, 'display'],
        ];

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
        $items = [
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Redis'),
                'content' => HelperApi::getIni(0, \extension_loaded('redis') && \class_exists('\\Redis')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Memcache'),
                'content' => HelperApi::getIni(0, \extension_loaded('memcache') && \class_exists('\\Memcache')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Memcached'),
                'content' => HelperApi::getIni(0, \extension_loaded('memcached') && \class_exists('\\Memcached')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Opcache'),
                'content' => HelperApi::getIni(0, \function_exists('\\opcache_get_configuration')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s enabled'), 'Opcache'),
                'content' => HelperApi::getIni(0, $this->isOpcEnabled()),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Swoole'),
                'content' => HelperApi::getIni(0, \extension_loaded('swoole') && \function_exists('\\swoole_version')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Image Magic'),
                'content' => HelperApi::getIni(0, \extension_loaded('imagick') && \class_exists('\\Imagick')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Graphics Magick'),
                'content' => HelperApi::getIni(0, \extension_loaded('gmagick')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Exif'),
                'content' => HelperApi::getIni(0, \extension_loaded('Exif') && \function_exists('\\exif_imagetype')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Fileinfo'),
                'content' => HelperApi::getIni(0, \extension_loaded('fileinfo')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Sockets'),
                'content' => HelperApi::getIni(0, \extension_loaded('Sockets') && \function_exists('\\socket_accept')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'MySQLi'),
                'content' => HelperApi::getIni(0, \extension_loaded('MySQLi') && \class_exists('\\mysqli')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Zip'),
                'content' => HelperApi::getIni(0, \extension_loaded('zip') && \class_exists('\\ZipArchive')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Multibyte String'),
                'content' => HelperApi::getIni(0, \extension_loaded('mbstring') && \function_exists('\\mb_substr')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Phalcon'),
                'content' => HelperApi::getIni(0, \extension_loaded('phalcon')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'Xdebug'),
                'content' => HelperApi::getIni(0, \extension_loaded('xdebug')),
            ],
            [
                'label'   => I18nApi::_('Zend Optimizer'),
                'content' => HelperApi::getIni(0, \function_exists('zend_optimizer_version')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'ionCube'),
                'content' => HelperApi::getIni(0, \extension_loaded('ioncube loader')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'SourceGuardian'),
                'content' => HelperApi::getIni(0, \extension_loaded('sourceguardian')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'LDAP'),
                'content' => HelperApi::getIni(0, \function_exists('\\ldap_connect')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s extension'), 'cURL'),
                'content' => HelperApi::getIni(0, \function_exists('\\curl_init')),
            ],
            [
                'col'     => '1-1',
                'label'   => I18nApi::_('Loaded extensions'),
                'title'   => 'loaded_extensions',
                'content' => \implode(', ', $this->getLoadedExtensions(true)) ?: '-',
            ],
        ];

        // order
        $itemsOrder = [];

        foreach ($items as $item) {
            $itemsOrder[] = $item['label'];
        }

        \array_multisort($items, $itemsOrder);

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
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
