<?php

namespace InnStudio\Prober\Components\Helper;

class HelperApi
{
    public static function setFileCacheHeader()
    {
        // 1 year expired
        $seconds = 3600 * 24 * 30 * 12;
        $ts      = \gmdate('D, d M Y H:i:s', (int) $_SERVER['REQUEST_TIME'] + $seconds) . ' GMT';
        \header("Expires: {$ts}");
        \header('Pragma: cache');
        \header("Cache-Control: public, max-age={$seconds}");
    }

    public static function getWinCpuUsage()
    {
        $usage = array(
            'idle' => 100,
            'user' => 0,
            'sys'  => 0,
            'nice' => 0,
        );

        // com
        if (\class_exists('\\COM')) {
            // need help
            $wmi    = new \COM('Winmgmts://');
            $server = $wmi->execquery('SELECT LoadPercentage FROM Win32_Processor');
            $total  = 0;

            foreach ($server as $cpu) {
                $total += (int) $cpu->loadpercentage;
            }

            $total         = (int) $total / \count($server);
            $usage['idle'] = 100 - $total;
            $usage['user'] = $total;
        // exec
        } else {
            if ( ! \function_exists('\exec')) {
                return $usage;
            }

            $p = array();
            \exec('wmic cpu get LoadPercentage', $p);

            if (isset($p[1])) {
                $percent       = (int) $p[1];
                $usage['idle'] = 100 - $percent;
                $usage['user'] = $percent;
            }
        }

        return $usage;
    }

    public static function getNetworkStats()
    {
        $filePath = '/proc/net/dev';

        if ( ! @\is_readable($filePath)) {
            return null;
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

    public static function getDiskTotalSpace()
    {
        if ( ! \function_exists('\disk_total_space')) {
            return 0;
        }

        static $space = null;

        if (null === $space) {
            $space = (float) \disk_total_space(__DIR__);
        }

        return $space;
    }

    public static function getDiskFreeSpace()
    {
        if ( ! \function_exists('\disk_total_space')) {
            return 0;
        }

        static $space = null;

        if (null === $space) {
            $space = (float) \disk_free_space(__DIR__);
        }

        return $space;
    }

    public static function getCpuModel()
    {
        $filePath = '/proc/cpuinfo';

        if ( ! @\is_readable($filePath)) {
            return '';
        }

        $content = \file_get_contents($filePath);
        $cores   = \substr_count($content, 'cache size');

        $lines     = \explode("\n", $content);
        $modelName = \explode(':', $lines[4]);
        $modelName = \trim($modelName[1]);
        $cacheSize = \explode(':', $lines[8]);
        $cacheSize = \trim($cacheSize[1]);

        return "{$cores} x {$modelName} / " . \sprintf('%s cache', $cacheSize);
    }

    public static function getServerTime()
    {
        return \date('Y-m-d H:i:s');
    }

    public static function getServerUtcTime()
    {
        return \gmdate('Y/m/d H:i:s');
    }

    public static function getServerUptime()
    {
        $filePath = '/proc/uptime';

        if ( ! @\is_file($filePath)) {
            return array(
                'days'  => 0,
                'hours' => 0,
                'mins'  => 0,
                'secs'  => 0,
            );
        }

        $str   = \file_get_contents($filePath);
        $num   = (float) $str;
        $secs  = (int) \fmod($num, 60);
        $num   = (int) ($num / 60);
        $mins  = (int) $num % 60;
        $num   = (int) ($num / 60);
        $hours = (int) $num % 24;
        $num   = (int) ($num / 24);
        $days  = (int) $num;

        return array(
            'days'  => $days,
            'hours' => $hours,
            'mins'  => $mins,
            'secs'  => $secs,
        );
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

    public static function getClientIp()
    {
        $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

        foreach ($keys as $key) {
            if ( ! isset($_SERVER[$key])) {
                continue;
            }

            $ip = \array_filter(\explode(',', $_SERVER[$key]));
            $ip = \filter_var(\end($ip), \FILTER_VALIDATE_IP);

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

        if (self::isWin()) {
            $cpu = self::getWinCpuUsage();

            return $cpu;
        }

        $filePath = ('/proc/stat');

        if ( ! @\is_readable($filePath)) {
            $cpu = array();

            return array(
                'user' => 0,
                'nice' => 0,
                'sys'  => 0,
                'idle' => 100,
            );
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

    public static function getHumanCpuUsage()
    {
        $cpu = self::getCpuUsage();

        return $cpu ?: array();
    }

    public static function getSysLoadAvg()
    {
        if (self::isWin()) {
            return array(0, 0, 0);
        }

        return \array_map(function ($load) {
            return (float) \sprintf('%.2f', $load);
        }, \sys_getloadavg());
    }

    public static function getMemoryUsage($key)
    {
        $key = \ucfirst($key);

        if (self::isWin()) {
            return 0;
        }

        static $memInfo = null;

        if (null === $memInfo) {
            $memInfoFile = '/proc/meminfo';

            if ( ! @\is_readable($memInfoFile)) {
                $memInfo = 0;

                return 0;
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
                $lines[$line[0]] = (float) $line[1] * 1024;
            }

            $memInfo = $lines;
        }

        if ( ! isset($memInfo['MemTotal'])) {
            return 0;
        }

        switch ($key) {
        case 'MemRealUsage':
            if (isset($memInfo['MemAvailable'])) {
                return $memInfo['MemTotal'] - $memInfo['MemAvailable'];
            }

            if (isset($memInfo['MemFree'])) {
                if (isset($memInfo['Buffers'], $memInfo['Cached'])) {
                    return $memInfo['MemTotal'] - $memInfo['MemFree'] - $memInfo['Buffers'] - $memInfo['Cached'];
                }

                return $memInfo['MemTotal'] - $memInfo['Buffers'];
            }

            return 0;
        case 'MemUsage':
            return isset($memInfo['MemFree']) ? $memInfo['MemTotal'] - $memInfo['MemFree'] : 0;
        case 'SwapUsage':
            if ( ! isset($memInfo['SwapTotal']) || ! isset($memInfo['SwapFree'])) {
                return 0;
            }

            return $memInfo['SwapTotal'] - $memInfo['SwapFree'];
        }

        return isset($memInfo[$key]) ? $memInfo[$key] : 0;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        if ( ! $bytes) {
            return 0;
        }

        $base     = \log($bytes, 1024);
        $suffixes = array('', ' K', ' M', ' G', ' T');

        return \round(\pow(1024, ($base - \floor($base))), $precision) . $suffixes[\floor($base)];
    }

    public static function getHumamMemUsage($key)
    {
        return self::formatBytes(self::getMemoryUsage($key));
    }

    public static function strcut($str, $len = 20)
    {
        if (\strlen($str) > $len) {
            return \mb_strcut($str, 0, $len) . '...';
        }

        return $str;
    }
}
