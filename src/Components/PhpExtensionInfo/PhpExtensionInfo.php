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

    public function filter(array $mods)
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
                'label'   => 'Redis',
                'content' => HelperApi::alert(\extension_loaded('redis') && \class_exists('\\Redis')),
            ),
            array(
                'label'   => 'SQLite3',
                'content' => HelperApi::alert(\extension_loaded('sqlite3') && \class_exists('\\SQLite3')),
            ),
            array(
                'label'   => 'Memcache',
                'content' => HelperApi::alert(\extension_loaded('memcache') && \class_exists('\\Memcache')),
            ),
            array(
                'label'   => 'Memcached',
                'content' => HelperApi::alert(\extension_loaded('memcached') && \class_exists('\\Memcached')),
            ),
            array(
                'label'   => 'Opcache',
                'content' => HelperApi::alert(\function_exists('\\opcache_get_configuration')),
            ),
            array(
                'label'   => \sprintf(I18nApi::_('%s enabled'), 'Opcache'),
                'content' => HelperApi::alert($this->isOpcEnabled()),
            ),
            array(
                'label'   => 'Swoole',
                'content' => HelperApi::alert(\extension_loaded('swoole') && \function_exists('\\swoole_version')),
            ),
            array(
                'label'   => 'Image Magic',
                'content' => HelperApi::alert(\extension_loaded('imagick') && \class_exists('\\Imagick')),
            ),
            array(
                'label'   => 'Graphics Magick',
                'content' => HelperApi::alert(\extension_loaded('gmagick')),
            ),
            array(
                'label'   => 'Exif',
                'content' => HelperApi::alert(\extension_loaded('exif') && \function_exists('\\exif_imagetype')),
            ),
            array(
                'label'   => 'Fileinfo',
                'content' => HelperApi::alert(\extension_loaded('fileinfo')),
            ),
            array(
                'label'   => 'SimpleXML',
                'content' => HelperApi::alert(\extension_loaded('simplexml')),
            ),
            array(
                'label'   => 'Sockets',
                'content' => HelperApi::alert(\extension_loaded('sockets') && \function_exists('\\socket_accept')),
            ),
            array(
                'label'   => 'MySQLi',
                'content' => HelperApi::alert(\extension_loaded('mysqli') && \class_exists('\\mysqli')),
            ),
            array(
                'label'   => 'Zip',
                'content' => HelperApi::alert(\extension_loaded('zip') && \class_exists('\\ZipArchive')),
            ),
            array(
                'label'   => 'Multibyte String',
                'content' => HelperApi::alert(\extension_loaded('mbstring') && \function_exists('\\mb_substr')),
            ),
            array(
                'label'   => 'Phalcon',
                'content' => HelperApi::alert(\extension_loaded('phalcon')),
            ),
            array(
                'label'   => 'Xdebug',
                'content' => HelperApi::alert(\extension_loaded('xdebug')),
            ),
            array(
                'label'   => I18nApi::_('Zend Optimizer'),
                'content' => HelperApi::alert(\function_exists('zend_optimizer_version')),
            ),
            array(
                'label'   => 'ionCube',
                'content' => HelperApi::alert(\extension_loaded('ioncube loader')),
            ),
            array(
                'label'   => 'SourceGuardian',
                'content' => HelperApi::alert(\extension_loaded('sourceguardian')),
            ),
            array(
                'label'   => 'LDAP',
                'content' => HelperApi::alert(\function_exists('\\ldap_connect')),
            ),
            array(
                'label'   => 'cURL',
                'content' => HelperApi::alert(\function_exists('\\curl_init')),
            ),
        );

        // order
        $itemsOrder = array();

        foreach ($items as &$item) {
            $itemsOrder[] = \strtolower($item['label']);
        }

        \array_multisort($itemsOrder, $items);

        $items[] = array(
            'col'     => '1-1',
            'label'   => I18nApi::_('Loaded extensions'),
            'title'   => 'loaded_extensions',
            'id'      => 'break-normal',
            'content' => HelperApi::getGroupItemLists(\get_loaded_extensions(), true) ?: HelperApi::alert(false),
        );

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
