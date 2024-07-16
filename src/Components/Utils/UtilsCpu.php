<?php

namespace InnStudio\Prober\Components\Utils;

use COM;

final class UtilsCpu
{
    public static function getLoadAvg()
    {
        if (UtilsApi::isWin()) {
            return array(0, 0, 0);
        }

        return array_map(function ($load) {
            return (float) sprintf('%.2f', $load);
        }, sys_getloadavg());
    }

    public static function isArm($content)
    {
        return false !== mb_stripos($content, 'CPU architecture');
    }

    public static function match($content, $search)
    {
        preg_match_all("/{$search}\\s*:\\s*(.+)/i", $content, $matches);

        return 2 === \count($matches) ? $matches[1] : array();
    }

    public static function getModel()
    {
        $filePath = '/proc/cpuinfo';
        if ( ! is_readable($filePath)) {
            return '';
        }
        $content = file_get_contents($filePath);
        if ( ! $content) {
            return '';
        }
        if (self::isArm($content)) {
            $cores = substr_count($content, 'processor');
            $searchArchitecture = self::match($content, 'CPU architecture');
            // CPU variant
            $searchVariant = self::match($content, 'CPU variant');
            // CPU part
            $searchPart = self::match($content, 'CPU part');
            // CPU revision
            $searchRevision = self::match($content, 'CPU revision');
            if ( ! $cores) {
                return '';
            }

            return "{$cores} x " . implode(' / ', array_filter(array(
                \count($searchArchitecture) ? "ARMv{$searchArchitecture[0]}" : 'ARM',
                \count($searchVariant) ? "variant {$searchVariant[0]}" : '',
                \count($searchPart) ? "part {$searchPart[0]}" : '',
                \count($searchRevision) ? "revision {$searchRevision[0]}" : '',
            )));
        }
        // cpu cores
        $cores = \count(self::match($content, 'cpu cores')) ?: substr_count($content, 'vendor_id');
        // cpu model name
        $searchModelName = self::match($content, 'model name');
        // cpu MHz
        $searchMHz = self::match($content, 'cpu MHz');
        // cache size
        $searchCache = self::match($content, 'cache size');
        if ( ! $cores) {
            return '';
        }

        return "{$cores} x " . implode(' / ', array_filter(array(
            \count($searchModelName) ? $searchModelName[0] : '',
            \count($searchMHz) ? "{$searchMHz[0]}MHz" : '',
            \count($searchCache) ? "{$searchCache[0]} cache" : '',
        )));
    }

    public static function getWinUsage()
    {
        $usage = array(
            'idle' => 100,
            'user' => 0,
            'sys' => 0,
            'nice' => 0,
        );
        // com
        if (class_exists('COM')) {
            // need help
            $wmi = new COM('Winmgmts://');
            $server = $wmi->execquery('SELECT LoadPercentage FROM Win32_Processor');
            $total = 0;
            foreach ($server as $cpu) {
                $total += (int) $cpu->loadpercentage;
            }
            $total = (float) $total / \count($server);
            $usage['idle'] = 100 - $total;
            $usage['user'] = $total;
        // exec
        } else {
            if ( ! \function_exists('exec')) {
                return $usage;
            }
            $p = array();
            exec('wmic cpu get LoadPercentage', $p);
            if (isset($p[1])) {
                $percent = (int) $p[1];
                $usage['idle'] = 100 - $percent;
                $usage['user'] = $percent;
            }
        }

        return $usage;
    }

    public static function getUsage()
    {
        static $cpu = null;
        if (null !== $cpu) {
            return $cpu;
        }
        if (UtilsApi::isWin()) {
            $cpu = self::getWinUsage();

            return $cpu;
        }
        $filePath = '/proc/stat';
        if ( ! @is_readable($filePath)) {
            $cpu = array();

            return array(
                'user' => 0,
                'nice' => 0,
                'sys' => 0,
                'idle' => 100,
            );
        }
        $stat1 = file($filePath);
        sleep(1);
        $stat2 = file($filePath);
        $info1 = explode(' ', preg_replace('!cpu +!', '', $stat1[0]));
        $info2 = explode(' ', preg_replace('!cpu +!', '', $stat2[0]));
        $dif = array();
        $dif['user'] = $info2[0] - $info1[0];
        $dif['nice'] = $info2[1] - $info1[1];
        $dif['sys'] = $info2[2] - $info1[2];
        $dif['idle'] = $info2[3] - $info1[3];
        $total = array_sum($dif);
        $cpu = array();
        foreach ($dif as $x => $y) {
            $cpu[$x] = round($y / $total * 100, 1);
        }

        return $cpu;
    }
}
