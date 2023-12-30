<?php

namespace InnStudio\Prober\Components\Utils;

final class UtilsDisk
{
    public static function getItems()
    {
        switch (\PHP_OS) {
            case 'Linux':
                return self::getLinuxItems();

            default:
                return;
        }
    }

    private static function getLinuxItems()
    {
        $path = '/proc/mounts';

        if ( ! file_exists($path) || ! \function_exists('shell_exec')) {
            return array(
                array(
                    'id' => __DIR__,
                    'free' => disk_free_space(__DIR__),
                    'total' => disk_total_space(__DIR__),
                ),
            );
        }

        $availableFs = array('ext2', 'ext3', 'ext4', 'xfs', 'btrfs', 'jfs', 'reiserfs', 'zfs', 'ufs', 'fat', 'ntfs', 'f2fs', 'fat32', 'exfat');
        $items = array();
        $lines = file($path);
        $df = shell_exec('df -k');
        $dfLines = explode("\n", $df);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }
            $line = explode(' ', $line);

            if ( ! \in_array($line[2], $availableFs, true)) {
                continue;
            }
            $mount = $line[1];
            $free = 0;
            $total = 0;
            foreach ($dfLines as $dfLine) {
                $dfObj = preg_replace('/\\s+/', ' ', $dfLine);
                $dfObj = explode(' ', $dfObj);
                if (\count($dfObj) < 6) {
                    continue;
                }
                if ($dfObj[5] !== $mount) {
                    continue;
                }
                $free = $dfObj[3] * 1024;
                $total = $dfObj[1] * 1024;
            }
            $items[] = array(
                'id' => $line[1],
                'free' => $free,
                'total' => $total,
            );
        }
        // sort by total desc
        usort($items, function ($a, $b) {
            return $b['total'] - $a['total'];
        });

        return $items;
    }
}
