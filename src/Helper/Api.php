<?php

namespace InnStudio\Prober\Helper;

use InnStudio\Prober\I18n\Api as I18n;

class Api
{
    public static function dieJson($data)
    {
        \header('Content-Type: application/json');

        die(\json_encode($data));
    }

    public static function isAction($action)
    {
        return \filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) === $action;
    }

    public static function getNetworkStats()
    {
        $filePath = '/proc/net/dev';

        if ( ! \is_readable($filePath)) {
            return I18n::_('Unavailable');
        }

        static $eths = null;

        if (null !== $eths) {
            return $eths;
        }

        $lines = \file($filePath);
        unset($lines[0], $lines[1]);
        $eths = array();

        foreach ($lines as $line) {
            $line              = \preg_replace('/\s+/', ' ', \trim($line));
            $lineArr           = \explode(':', $line);
            $numberArr         = \explode(' ', \trim($lineArr[1]));
            $eths[$lineArr[0]] = array(
                'rx' => (int) $numberArr[0],
                'tx' => (int) $numberArr[8],
            );
        }

        return $eths;
    }

    public static function getBtn($tx, $url)
    {
        return '<a href="' . $url . '" target="_blank" class="btn">' . $tx . '</a>';
    }

    public static function getDiskTotalSpace($human = false)
    {
        static $space = null;

        if (null === $space) {
            $space = \disk_total_space('/');
        }

        if ( ! $space) {
            return 0;
        }

        if (true === $human) {
            return self::formatBytes($space);
        }

        return $space;
    }

    public static function getDiskFreeSpace($human = false)
    {
        static $space = null;

        if (null === $space) {
            $space = \disk_free_space('/');
        }

        if ( ! $space) {
            return 0;
        }

        if (true === $human) {
            return self::formatBytes($space);
        }

        return $space;
    }

    public static function getCpuModel()
    {
        $filePath = '/proc/cpuinfo';

        if ( ! \is_readable($filePath)) {
            return I18n::_('Unavailable');
        }

        $content = \file_get_contents($filePath);
        $cores   = \mb_substr_count($content, 'cache size');

        $lines     = \explode("\n", $content);
        $modelName = \trim(\explode(':', $lines[4])[1]);
        $cacheSize = \trim(\explode(':', $lines[8])[1]);

        return "{$cores} x {$modelName} / " . \sprintf(I18n::_('%s cache'), $cacheSize);
    }

    public static function getServerTime()
    {
        return \date('Y-m-d H:i:s');
    }

    public static function getServerUpTime()
    {
        $filePath = '/proc/uptime';

        if ( ! \is_readable($filePath)) {
            return I18n::_('Unavailable');
        }

        $str   = \file_get_contents($filePath);
        $num   = (float) $str;
        $secs  = \fmod($num, 60);
        $num   = (int) ($num / 60);
        $mins  = $num % 60;
        $num   = (int) ($num / 60);
        $hours = $num % 24;
        $num   = (int) ($num / 24);
        $days  = $num;

        return \sprintf(
            I18n::_('%1$dd %2$dh %3$dm %4$ds'),
            $days,
            $hours,
            $mins,
            $secs
        );
    }

    public static function getErrNameByCode($code)
    {
        switch ($code) {
            case E_ERROR: return 'E_ERROR';
            case E_WARNING: return 'E_WARNING';
            case E_PARSE: return 'E_PARSE';
            case E_NOTICE: return 'E_NOTICE';
            case E_CORE_ERROR: return 'E_CORE_ERROR';
            case E_CORE_WARNING: return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: return 'E_COMPILE_WARNING';
            case E_USER_ERROR: return 'E_USER_ERROR';
            case E_USER_WARNING: return 'E_USER_WARNING';
            case E_USER_NOTICE: return 'E_USER_NOTICE';
            case E_STRICT: return 'E_STRICT';
            case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: return 'E_DEPRECATED';
            case E_USER_DEPRECATED: return 'E_USER_DEPRECATED';
            case E_ALL: return 'E_ALL';
        }

        return $code;
    }

    public static function getIni($id, $forceSet = null)
    {
        if (true === $forceSet) {
            $ini = 1;
        } elseif (false === $forceSet) {
            $ini = 0;
        } else {
            $ini = \ini_get($id);
        }

        if ( ! \is_numeric($ini)) {
            return $ini;
        }

        if (1 === (int) $ini) {
            return '<span class="ini-ok">&check;</span>';
        } elseif (0 === (int) $ini) {
            return '<span class="ini-error">&times;</span>';
        }

        return $ini;
    }

    public static function isWin()
    {
        return PHP_OS === 'WINNT';
    }

    public static function htmlMinify($buffer)
    {
        // @see https://stackoverflow.com/questions/27878158/php-bufffer-output-minify-not-textarea-pre
        \preg_match_all('#\<textarea.*\>.*\<\/textarea\>#Uis', $buffer, $foundTxt);
        \preg_match_all('#\<pre.*\>.*\<\/pre\>#Uis', $buffer, $foundPre);

        // replacing both with <textarea>$index</textarea> / <pre>$index</pre>
        $textareas = array();

        foreach (\array_keys($foundTxt[0]) as $item) {
            $textareas[] = '<textarea>' . $item . '</textarea>';
        }

        $pres = array();

        foreach (\array_keys($foundPre[0]) as $item) {
            $pres[] = '<pre>' . $item . '</pre>';
        }

        $buffer = \str_replace($foundTxt[0], $textareas, $buffer);
        $buffer = \str_replace($foundPre[0], $pres, $buffer);

        // your stuff
        $search = array(
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s', // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1',
        );

        $buffer = \preg_replace($search, $replace, $buffer);

        // Replacing back with content
        $textareas = array();

        foreach (\array_keys($foundTxt[0]) as $item) {
            $textareas[] = '<textarea>' . $item . '</textarea>';
        }

        $pres = array();

        foreach (\array_keys($foundPre[0]) as $item) {
            $pres[] = '<pre>' . $item . '</pre>';
        }

        $buffer = \str_replace($textareas, $foundTxt[0], $buffer);
        $buffer = \str_replace($pres, $foundPre[0], $buffer);

        return $buffer;
    }

    public static function getClientIp()
    {
        $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

        foreach ($keys as $key) {
            if ( ! isset($_SERVER[$key])) {
                continue;
            }

            $ip = \array_filter(\explode(',', $_SERVER[$key]));
            $ip = \filter_var(\end($ip), FILTER_VALIDATE_IP);

            if ($ip) {
                return $ip;
            }
        }

        return '';
    }

    public static function getCpuUsage()
    {
        static $cpu = null;

        if (null !== $cpu) {
            return $cpu;
        }

        $filePath = ('/proc/stat');

        if ( ! \is_readable($filePath)) {
            $cpu = array();

            return $cpu;
        }

        $stat1 = \file($filePath);
        \sleep(1);
        $stat2       = \file($filePath);
        $info1       = \explode(' ', \preg_replace('!cpu +!', '', $stat1[0]));
        $info2       = \explode(' ', \preg_replace('!cpu +!', '', $stat2[0]));
        $dif         = array();
        $dif['user'] = $info2[0] - $info1[0];
        $dif['nice'] = $info2[1] - $info1[1];
        $dif['sys']  = $info2[2] - $info1[2];
        $dif['idle'] = $info2[3] - $info1[3];
        $total       = \array_sum($dif);
        $cpu         = array();

        foreach ($dif as $x => $y) {
            $cpu[$x] = \round($y / $total * 100, 1);
        }

        return $cpu;
    }

    public static function getHumanCpuUsageDetail()
    {
        $cpu = self::getCpuUsage();

        if ( ! $cpu) {
            return '';
        }

        $html = '';

        foreach ($cpu as $k => $v) {
            $html .= '<span class="small-group"><span class="item-name">' . $k . '</span> <span class="item-value">' . $v . '</span></span>';
        }

        return $html;
    }

    public static function getHumanCpuUsage()
    {
        $cpu = self::getCpuUsage();

        return $cpu ?: array();
    }

    public static function getSysLoadAvg()
    {
        $avg = \sys_getloadavg();

        $avg[0] = '<span class="small-group"><span class="item-name">' . I18n::_('1 min:') . "</span> {$avg[0]}</span>";
        $avg[1] = '<span class="small-group"><span class="item-name">' . I18n::_('5 min:') . "</span> {$avg[1]}</span>";
        $avg[2] = '<span class="small-group"><span class="item-name">' . I18n::_('15 min:') . "</span> {$avg[2]}</span>";

        return \implode('', $avg);
    }

    public static function getHumanSysLoadAvg()
    {
        $avg = \sys_getloadavg();
    }

    public static function getMemoryUsage($key)
    {
        $key = \ucfirst($key);

        if (self::isWin()) {
            return array();
        }

        static $memInfo = null;

        if (null === $memInfo) {
            $memInfoFile = '/proc/meminfo';

            if ( ! \is_readable($memInfoFile)) {
                $memInfo = array();

                return array();
            }

            $memInfo = \file_get_contents($memInfoFile);
            $memInfo = \str_replace(array(
                ' kB',
                '  ',
            ), '', $memInfo);

            $lines = array();

            foreach (\explode("\n", $memInfo) as $line) {
                if ( ! $line) {
                    continue;
                }

                $line            = \explode(':', $line);
                $lines[$line[0]] = (int) $line[1];
            }

            $memInfo = $lines;
        }

        switch ($key) {
            case 'MemRealUsage':
                if ( ! isset($memInfo['MemAvailable']) || ! isset($memInfo['MemTotal'])) {
                    return 0;
                }

                return $memInfo['MemTotal'] - $memInfo['MemAvailable'];
            case 'SwapRealUsage':
                if ( ! isset($memInfo['SwapTotal']) || ! isset($memInfo['SwapFree']) || ! isset($memInfo['SwapCached'])) {
                    return 0;
                }

                return $memInfo['SwapTotal'] - $memInfo['SwapFree'] - $memInfo['SwapCached'];
        }

        return isset($memInfo[$key]) ? (int) $memInfo[$key] : 0;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        if ( ! $bytes) {
            return 0;
        }

        $base     = \log($bytes, 1024);
        $suffixes = array('', ' K', ' M', ' G', ' T');

        return \round(\pow(1024, $base - \floor($base)), $precision) . $suffixes[\floor($base)];
    }

    public static function getHumamMemUsage($key)
    {
        return self::formatBytes(self::getMemoryUsage($key) * 1024);
    }
}
