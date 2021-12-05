<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

class ServerBenchmarkApi
{
    private $EXPIRED = 60;

    public function getTmpRecorderPath()
    {
        return sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'xproberBenchmarkTimer';
    }

    public function setRecorder(array $data)
    {
        return (bool) file_put_contents($this->getTmpRecorderPath(), json_encode(array_merge($this->getRecorder(), $data)));
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
        return pow(10, 3) - (int) ($time * pow(10, 3));
    }

    public function getCpuPoints()
    {
        $data  = 'inn-studio.com';
        $hash  = array('md5', 'sha512', 'sha256', 'crc32');
        $start = microtime(true);
        $i     = 0;

        while (microtime(true) - $start < .5) {
            foreach ($hash as $v) {
                hash($v, $data);
            }
            ++$i;
        }

        return $i;
    }

    public function getWritePoints()
    {
        $tmpDir = sys_get_temp_dir();

        if ( ! is_writable($tmpDir)) {
            return 0;
        }

        $i     = 0;
        $start = microtime(true);

        while (microtime(true) - $start < .5) {
            $filePath = "{$tmpDir}/innStudioWriteBenchmark:{$i}";
            clearstatcache(true, $filePath);
            file_put_contents($filePath, $filePath);
            unlink($filePath);
            ++$i;
        }

        return $i;
    }

    public function getReadPoints()
    {
        $tmpDir = sys_get_temp_dir();

        if ( ! is_readable($tmpDir)) {
            return 0;
        }

        $i        = 0;
        $start    = microtime(true);
        $filePath = "{$tmpDir}/innStudioIoBenchmark";

        if ( ! file_exists($filePath)) {
            file_put_contents($filePath, 'innStudioReadBenchmark');
        }

        while (microtime(true) - $start < .5) {
            clearstatcache(true, $filePath);
            file_get_contents($filePath);
            ++$i;
        }

        return $i;
    }

    public function getPoints()
    {
        return array(
            'cpu' => $this->getMedian(array(
                $this->getCpuPoints(),
                $this->getCpuPoints(),
                $this->getCpuPoints(),
            )),
            'write' => $this->getMedian(array(
                $this->getWritePoints(),
                $this->getWritePoints(),
                $this->getWritePoints(),
            )),
            'read' => $this->getMedian(array(
                $this->getReadPoints(),
                $this->getReadPoints(),
                $this->getReadPoints(),
            )),
        );
    }

    private function getRecorder()
    {
        $path     = $this->getTmpRecorderPath();
        $defaults = array(
            'expired' => 0,
            'running' => 0,
        );

        if ( ! @is_readable($path)) {
            return $defaults;
        }

        $data = (string) file_get_contents($path);

        if ( ! $data) {
            return $defaults;
        }

        $data = json_decode($data, true);

        if ( ! $data) {
            return $defaults;
        }

        return array_merge($defaults, $data);
    }

    private function getMedian(array $arr)
    {
        $count = \count($arr);
        sort($arr);
        $mid = floor(($count - 1) / 2);

        return ($arr[$mid] + $arr[$mid + 1 - $count % 2]) / 2;
    }
}
