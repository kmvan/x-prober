<?php

namespace InnStudio\Prober\Components\Utils;

final class UtilsDisk
{
    public static function getTotal()
    {
        if ( ! \function_exists('disk_total_space')) {
            return 0;
        }

        static $space = null;

        if (null === $space) {
            $space = (float) disk_total_space(__DIR__);
        }

        return $space;
    }

    public static function getFree()
    {
        if ( ! \function_exists('disk_total_space')) {
            return 0;
        }

        static $space = null;

        if (null === $space) {
            $space = (float) disk_free_space(__DIR__);
        }

        return $space;
    }
}
