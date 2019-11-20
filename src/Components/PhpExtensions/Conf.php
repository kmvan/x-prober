<?php

namespace InnStudio\Prober\Components\PhpExtensions;

use InnStudio\Prober\Components\Events\EventsApi;

class Conf extends PhpExtensionsConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'redis'            => \extension_loaded('redis') && \class_exists('\\Redis'),
            'sqlite3'          => \extension_loaded('sqlite3') && \class_exists('\\Sqlite3'),
            'memcache'         => \extension_loaded('memcache') && \class_exists('\\Memcache'),
            'memcached'        => \extension_loaded('memcached') && \class_exists('\\Memcached'),
            'opcache'          => \function_exists('\opcache_get_configuration'),
            'opcacheEnabled'   => $this->isOpcEnabled(),
            'swoole'           => \extension_loaded('swoole') && \function_exists('\\swoole_version'),
            'imagick'          => \extension_loaded('imagick') && \class_exists('\\Imagick'),
            'gmagick'          => \extension_loaded('gmagick'),
            'exif'             => \extension_loaded('exif') && \function_exists('\\exif_imagetype'),
            'fileinfo'         => \extension_loaded('fileinfo'),
            'simplexml'        => \extension_loaded('simplexml'),
            'sockets'          => \extension_loaded('sockets') && \function_exists('\\socket_accept'),
            'mysqli'           => \extension_loaded('mysqli') && \class_exists('\\mysqli'),
            'zip'              => \extension_loaded('zip') && \class_exists('\\ZipArchive'),
            'mbstring'         => \extension_loaded('mbstring') && \function_exists('\\mb_substr'),
            'phalcon'          => \extension_loaded('phalcon'),
            'xdebug'           => \extension_loaded('xdebug'),
            'zendOtimizer'     => \function_exists('\\zend_optimizer_version'),
            'ionCube'          => \extension_loaded('ioncube loader'),
            'sourceGuardian'   => \extension_loaded('sourceguardian'),
            'ldap'             => \function_exists('\\ldap_connect'),
            'curl'             => \function_exists('\\curl_init'),
            'loadedExtensions' => \get_loaded_extensions(),
        );

        return $conf;
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
