<?php

namespace InnStudio\Prober\Components\Benchmark;

class BenchmarkApi
{
    private $EXPIRED = 60;

    public function getTmpRecorderPath()
    {
        return \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'xproberBenchmarkTimer';
    }

    public function saveTmpRecorder()
    {
        return (bool) \file_put_contents($this->getTmpRecorderPath(), \json_encode(array(
            'expired' => (int) $_SERVER['REQUEST_TIME'] + $this->EXPIRED,
        )));
    }

    public function getRemainingSeconds()
    {
        $path = $this->getTmpRecorderPath();

        if ( ! @\is_readable($path)) {
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

    public function getPointsByTime($time)
    {
        return \pow(10, 3) - (int) ($time * \pow(10, 3));
    }

    public function getHashPoints()
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

    public function getIntLoopPoints()
    {
        $j     = 0;
        $count = \pow(10, 7);
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            ++$j;
        }

        return $this->getPointsByTime(\microtime(true) - $start);
    }

    public function getFloatLoopPoints()
    {
        $j     = 1 / 3;
        $count = \pow(10, 7);
        $start = \microtime(true);

        for ($i = 0; $i < $count; ++$i) {
            ++$j;
        }

        return $this->getPointsByTime(\microtime(true) - $start);
    }

    public function getIoLoopPoints()
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

    public function getPoints()
    {
        return array(
            'hash'      => $this->getHashPoints(),
            'intLoop'   => $this->getIntLoopPoints(),
            'floatLoop' => $this->getFloatLoopPoints(),
            'ioLoop'    => $this->getIoLoopPoints(),
        );
    }
}
