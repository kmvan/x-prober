<?php

namespace InnStudio\Prober\I18n;

class Api
{
    public static function _($str)
    {
        static $preDefineLang = null;

        if (null === $preDefineLang) {
            $preDefineLang = \json_decode(\base64_decode(LANG), true);
        }

        if ( ! isset($preDefineLang[$str])) {
            return $str;
        }

        $lang       = $preDefineLang[$str];
        $clientLang = self::getClientLang();

        return isset($lang[$clientLang]) ? $lang[$clientLang] : $str;
    }

    public static function getClientLang()
    {
        static $cache = null;

        if (null !== $cache) {
            return $cache;
        }

        if ( ! isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $cache = '';

            return $cache;
        }

        $client = \explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

        if (isset($client[0])) {
            $cache = $client[0];
        } else {
            $cache = '';
        }

        return $cache;
    }
}
