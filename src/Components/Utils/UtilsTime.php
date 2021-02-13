<?php

namespace InnStudio\Prober\Components\Utils;

class UtilsTime
{
    public static function getTime()
    {
        return \date('Y-m-d H:i:s');
    }

    public static function getUtcTime()
    {
        return \gmdate('Y/m/d H:i:s');
    }

    public static function getUptime()
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
}
