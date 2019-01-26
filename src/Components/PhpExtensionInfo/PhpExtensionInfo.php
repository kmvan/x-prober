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
                'label'   => 'Redis',
                'content' => HelperApi::alert(\extension_loaded('redis') && \class_exists('\\Redis')),
            ],
            [
                'label'   => 'Memcache',
                'content' => HelperApi::alert(\extension_loaded('memcache') && \class_exists('\\Memcache')),
            ],
            [
                'label'   => 'Memcached',
                'content' => HelperApi::alert(\extension_loaded('memcached') && \class_exists('\\Memcached')),
            ],
            [
                'label'   => 'Opcache',
                'content' => HelperApi::alert(\function_exists('\\opcache_get_configuration')),
            ],
            [
                'label'   => \sprintf(I18nApi::_('%s enabled'), 'Opcache'),
                'content' => HelperApi::alert($this->isOpcEnabled()),
            ],
            [
                'label'   => 'Swoole',
                'content' => HelperApi::alert(\extension_loaded('swoole') && \function_exists('\\swoole_version')),
            ],
            [
                'label'   => 'Image Magic',
                'content' => HelperApi::alert(\extension_loaded('imagick') && \class_exists('\\Imagick')),
            ],
            [
                'label'   => 'Graphics Magick',
                'content' => HelperApi::alert(\extension_loaded('gmagick')),
            ],
            [
                'label'   => 'Exif',
                'content' => HelperApi::alert(\extension_loaded('Exif') && \function_exists('\\exif_imagetype')),
            ],
            [
                'label'   => 'Fileinfo',
                'content' => HelperApi::alert(\extension_loaded('fileinfo')),
            ],
            [
                'label'   => 'Sockets',
                'content' => HelperApi::alert(\extension_loaded('Sockets') && \function_exists('\\socket_accept')),
            ],
            [
                'label'   => 'MySQLi',
                'content' => HelperApi::alert(\extension_loaded('MySQLi') && \class_exists('\\mysqli')),
            ],
            [
                'label'   => 'Zip',
                'content' => HelperApi::alert(\extension_loaded('zip') && \class_exists('\\ZipArchive')),
            ],
            [
                'label'   => 'Multibyte String',
                'content' => HelperApi::alert(\extension_loaded('mbstring') && \function_exists('\\mb_substr')),
            ],
            [
                'label'   => 'Phalcon',
                'content' => HelperApi::alert(\extension_loaded('phalcon')),
            ],
            [
                'label'   => 'Xdebug',
                'content' => HelperApi::alert(\extension_loaded('xdebug')),
            ],
            [
                'label'   => I18nApi::_('Zend Optimizer'),
                'content' => HelperApi::alert(\function_exists('zend_optimizer_version')),
            ],
            [
                'label'   => 'ionCube',
                'content' => HelperApi::alert(\extension_loaded('ioncube loader')),
            ],
            [
                'label'   => 'SourceGuardian',
                'content' => HelperApi::alert(\extension_loaded('sourceguardian')),
            ],
            [
                'label'   => 'LDAP',
                'content' => HelperApi::alert(\function_exists('\\ldap_connect')),
            ],
            [
                'label'   => 'cURL',
                'content' => HelperApi::alert(\function_exists('\\curl_init')),
            ],
            [
                'col'     => '1-1',
                'label'   => I18nApi::_('Loaded extensions'),
                'title'   => 'loaded_extensions',
                'id'      => 'break-normal',
                'content' => HelperApi::getGroupItemLists(\get_loaded_extensions(), true) ?: HelperApi::alert(false),
            ],
        ];

        // order
        $itemsOrder = [];

        foreach ($items as $item) {
            $itemsOrder[] = \strtolower($item['label']);
        }

        \array_multisort($itemsOrder, $items);

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

        return \array_map(function ($ext) {
            return <<<HTML
<span class="inn-group__item-list__container">{$ext}</span>
HTML;
        }, $exts);
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
