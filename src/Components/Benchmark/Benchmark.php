<?php

namespace InnStudio\Prober\Components\Benchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class Benchmark
{
    private $EXPIRED = 60;

    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'));
    }

    public function filter()
    {
        if ( ! HelperApi::isAction('benchmark')) {
            return;
        }

        $this->display();
    }

    private function getTmpRecorderPath()
    {
        return \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'xproberBenchmarkTimer';
    }

    private function saveTmpRecorder()
    {
        return (bool) \file_put_contents($this->getTmpRecorderPath(), \json_encode(array(
            'expired' => (int) $_SERVER['REQUEST_TIME'] + $this->EXPIRED,
        )));
    }

    private function getRemainingSeconds()
    {
        $path = $this->getTmpRecorderPath();

        if ( ! \is_readable($path)) {
            return 0;
        }

        $data = (string) \file_get_contents($this->getTmpRecorderPath());

        if ( ! $data) {
            return 0;
        }

        $data = \json_decode($data, true);

        if ( ! $data) {
            return 0;
        }

        $expired = isset($data['expired']) ? (int) $data['expired'] : 0;

        if ( ! $expired) {
            return 0;
        }

        return $expired > (int) $_SERVER['REQUEST_TIME'] ? $expired - (int) $_SERVER['REQUEST_TIME'] : 0;
    }

    private function getPointsByTime($time)
    {
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
            $filePath = "{$tmpDir}/innStudioIoBenchmark:{$i}";
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
        $remainingSeconds = $this->getRemainingSeconds();

        if ($remainingSeconds) {
            HelperApi::dieJson(array(
                'code' => -1,
                'msg'  => '⏳ ' . \sprintf(I18nApi::_('Please wait %d seconds'), $remainingSeconds),
            ));
        }

        $this->saveTmpRecorder();

        \set_time_limit(0);

        $points = $this->getPoints();

        HelperApi::dieJson(array(
            'code' => 0,
            'data' => array(
                'points'     => $points,
                'total'      => \array_sum($points),
                'totalHuman' => \number_format(\array_sum($points)),
            ),
        ));
    }
}
