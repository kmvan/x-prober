<?php

namespace InnStudio\Prober\Components\Utils;

class UtilsCpu
{
    public static function getLoadAvg()
    {
        if (UtilsApi::isWin()) {
            return array(0, 0, 0);
        }

        return \array_map(function ($load) {
            return (float) \sprintf('%.2f', $load);
        }, \sys_getloadavg());
    }

    public static function getModel()
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

    public static function getWinUsage()
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

            $total         = (float) $total / \count($server);
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
}
