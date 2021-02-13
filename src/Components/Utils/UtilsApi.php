<?php

namespace InnStudio\Prober\Components\Utils;

class UtilsApi
{
    public static function jsonDecode($json, $depth = 512, $options = 0)
    {
        // search and remove comments like /* */ and //
        $json = \preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);

        if (\PHP_VERSION_ID >= 50400) {
            return \json_decode($json, true, $depth, $options);
        }

        if (\PHP_VERSION_ID >= 50300) {
            return \json_decode($json, true, $depth);
        }

        return \json_decode($json, true);
    }

    public static function setFileCacheHeader()
    {
        // 1 year expired
        $seconds = 3600 * 24 * 30 * 12;
        $ts      = \gmdate('D, d M Y H:i:s', (int) $_SERVER['REQUEST_TIME'] + $seconds) . ' GMT';
        \header("Expires: {$ts}");
        \header('Pragma: cache');
        \header("Cache-Control: public, max-age={$seconds}");
    }

    public static function getErrNameByCode($code)
    {
        if (0 === (int) $code) {
            return '';
        }

        $levels = array(
            \E_ALL               => 'E_ALL',
            \E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
            \E_DEPRECATED        => 'E_DEPRECATED',
            \E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            \E_STRICT            => 'E_STRICT',
            \E_USER_NOTICE       => 'E_USER_NOTICE',
            \E_USER_WARNING      => 'E_USER_WARNING',
            \E_USER_ERROR        => 'E_USER_ERROR',
            \E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
            \E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
            \E_CORE_WARNING      => 'E_CORE_WARNING',
            \E_CORE_ERROR        => 'E_CORE_ERROR',
            \E_NOTICE            => 'E_NOTICE',
            \E_PARSE             => 'E_PARSE',
            \E_WARNING           => 'E_WARNING',
            \E_ERROR             => 'E_ERROR',
        );

        $result = '';

        foreach ($levels as $number => $name) {
            if (($code & $number) == $number) {
                $result .= ('' != $result ? ', ' : '') . $name;
            }
        }

        return $result;
    }

    public static function isWin()
    {
        return \PHP_OS === 'WINNT';
    }
}
