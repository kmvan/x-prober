<?php

namespace InnStudio\Prober\Components\Utils;

final class UtilsMemory
{
    public static function getMemoryUsage($key)
    {
        $key = ucfirst($key);

        if (UtilsApi::isWin()) {
            return 0;
        }

        static $memInfo = null;

        if (null === $memInfo) {
            $memInfoFile = '/proc/meminfo';

            if ( ! @is_readable($memInfoFile)) {
                $memInfo = 0;

                return 0;
            }

            $memInfo = file_get_contents($memInfoFile);
            $memInfo = str_replace(array(
                ' kB',
                '  ',
            ), '', $memInfo);

            $lines = array();

            foreach (explode("\n", $memInfo) as $line) {
                if ( ! $line) {
                    continue;
                }

                $line            = explode(':', $line);
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
}
