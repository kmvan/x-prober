<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

class ServerBenchmarkApi
{
    private $EXPIRED = 60;

    public function getTmpRecorderPath()
    {
        return \sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'xproberBenchmarkTimer';
    }

    public function setRecorder(array $data)
    {
        return (bool) \file_put_contents($this->getTmpRecorderPath(), \json_encode(\array_merge($this->getRecorder(), $data)));
    }

    public function setExpired()
    {
        return (bool) $this->setRecorder(array(
            'expired' => (int) $_SERVER['REQUEST_TIME'] + $this->EXPIRED,
        ));
    }

    public function setIsRunning($isRunning)
    {
        return (bool) $this->setRecorder(array(
            'isRunning' => true === (bool) $isRunning ? 1 : 0,
        ));
    }

    public function isRunning()
    {
        $recorder = $this->getRecorder();

        return isset($recorder['isRunning']) ? 1 === (int) $recorder['isRunning'] : false;
    }

    public function getRemainingSeconds()
    {
        $recorder = $this->getRecorder();

        $expired = isset($recorder['expired']) ? (int) $recorder['expired'] : 0;

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

    private function getRecorder()
    {
        $path     = $this->getTmpRecorderPath();
        $defaults = array(
            'expired' => 0,
            'running' => 0,
        );

        if ( ! @\is_readable($path)) {
            return $defaults;
        }

        $data = (string) \file_get_contents($path);

        if ( ! $data) {
            return $defaults;
        }

        $data = \json_decode($data, true);

        if ( ! $data) {
            return $defaults;
        }

        return \array_merge($defaults, $data);
    }
}
