<?php

namespace InnStudio\Prober\Components\Utils;

final class UtilsNetwork
{
    public static function getStats()
    {
        $filePath = '/proc/net/dev';

        if ( ! @is_readable($filePath)) {
            return;
        }

        static $eths = null;

        if (null !== $eths) {
            return $eths;
        }

        $lines = file($filePath);
        unset($lines[0], $lines[1]);
        $eths = array();

        foreach ($lines as $line) {
            $line      = preg_replace('/\\s+/', ' ', trim($line));
            $lineArr   = explode(':', $line);
            $numberArr = explode(' ', trim($lineArr[1]));
            $rx        = (float) $numberArr[0];
            $tx        = (float) $numberArr[8];

            if ( ! $rx && ! $tx) {
                continue;
            }

            $eths[] = array(
                'id' => $lineArr[0],
                'rx' => $rx,
                'tx' => $tx,
            );
        }

        return $eths;
    }
}
