<?php

namespace InnStudio\Prober\Components\Xconfig;

use InnStudio\Prober\Components\Utils\UtilsApi;

final class XconfigApi
{
    private static $conf;

    private static $filename = 'xconfig.json';

    public static function isDisabled($id)
    {
        return \in_array($id, self::get('disabled') ?: array(), true);
    }

    public static function getNodes()
    {
        return self::get('nodes') ?: array();
    }

    public static function get($id = null)
    {
        self::setConf();

        if ($id) {
            return isset(self::$conf[$id]) ? self::$conf[$id] : null;
        }

        return self::$conf;
    }

    private static function getFilePath()
    {
        if ( ! \defined('\\XPROBER_DIR')) {
            return '';
        }

        if (\defined('\\XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            return \dirname(XPROBER_DIR) . '/' . self::$filename;
        }

        return XPROBER_DIR . '/' . self::$filename;
    }

    private static function setConf()
    {
        if (null !== self::$conf) {
            return;
        }

        if ( ! is_readable(self::getFilePath())) {
            self::$conf = null;

            return;
        }

        $conf = UtilsApi::jsonDecode(file_get_contents(self::getFilePath()));

        if ( ! $conf) {
            self::$conf = null;

            return;
        }

        self::$conf = $conf;
    }
}
