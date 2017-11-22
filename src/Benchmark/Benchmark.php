<?php

namespace InnStudio\Prober\Benchmark;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;

class Benchmark
{
    public function __construct()
    {
        Events::on('init', array($this, 'filter'));
    }

    public function filter()
    {
        if ( ! Helper::isAction('benchmark')) {
            return;
        }

        $this->display();
    }

    private function getPointsByTime($time)
    {
        \error_log($time);

        return \pow(10, 3) - (int) ($time * \pow(10, 3));
    }

    private function getHashPoints()
    {
        $data  = 'inn-studio.com';
        $hash  = array('md5', 'sha512', 'sha256', 'crc32');
        $count = \pow(10, 5);
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            foreach ($hash as $v) {
                \hash($v, $data);
            }
        }

        return $this->getPointsByTime(\microtime(true) - $start);
    }

    private function getIntLoopPoints()
    {
        $j     = 0;
        $count = \pow(10, 7);
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            ++$j;
        }

        return $this->getPointsByTime(\microtime(true) - $start);
    }

    private function getFloatLoopPoints()
    {
        $j     = 1 / 3;
        $count = \pow(10, 7);
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            ++$j;
        }

        return $this->getPointsByTime(\microtime(true) - $start);
    }

    private function getIoLoopPoints()
    {
        $tmpDir = \sys_get_temp_dir();

        if ( ! \is_writable($tmpDir)) {
            return 0;
        }

        $count = \pow(10, 4);
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            $filePath = "{$tmpDir}/innStudioIoBenchmark-{$i}";
            \file_put_contents($filePath, $filePath);
            \unlink($filePath);
        }

        return $this->getPointsByTime(\microtime(true) - $start);
    }

    private function getPoints()
    {
        return array(
            'hash'      => $this->getHashPoints(),
            'intLoop'   => $this->getIntLoopPoints(),
            'floatLoop' => $this->getFloatLoopPoints(),
            'ioLoop'    => $this->getIoLoopPoints(),
        );
    }

    private function display()
    {
        \set_time_limit(0);

        Helper::dieJson(array(
            'code' => 0,
            'data' => array(
                'points' => $this->getPoints(),
            ),
        ));
    }
}
