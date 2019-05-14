<?php

namespace InnStudio\Prober\Components\Helper;

use InnStudio\Prober\Components\I18n\I18nApi;

class HelperApi
{
    public static function getClassNames(array $classNames)
    {
        return \implode(' ', \array_keys(\array_filter($classNames)));
    }

    public static function getGroupItemLists(array $items, $sorted = false)
    {
        if ( ! \array_filter($items)) {
            return '';
        }

        if ($sorted) {
            \sort($items);
        }

        $items = \implode('', \array_map(function ($item) {
            $item = \trim($item);

            if ( ! $item) {
                return '';
            }

            $kw = \urlencode($item);

            return <<<HTML
<a href="https://www.google.com/search?q=PHP+{$kw}" target="_blank" class="inn-group__item-list" title="Google: PHP {$kw}">{$item}</a>
HTML;
        }, $items));

        return <<<HTML
<div class="inn-group__item-list__container">{$items}</div>
HTML;
    }

    public static function getProgressTpl(array $args)
    {
        $args = \array_merge(array(
            'id'       => '',
            'usage'    => 0,
            'total'    => 0,
            'overview' => '',
        ), $args);

        if ( ! $args['total']) {
            $percent    = 0;
            $totalHuman = 0;
            $usageHuman = 0;
            $overview   = '0 / 0';
        } else {
            $percent    = \round($args['usage'] / $args['total'], 2) * 100;
            $totalHuman = self::formatBytes($args['total']);
            $usageHuman = self::formatBytes($args['usage']);
            $overview   = $args['overview'] ? $args['overview'] : "{$usageHuman} / {$totalHuman}";
        }

        return <<<HTML
<div class="inn-progress__container">
    <div class="inn-progress__percent" id="inn-{$args['id']}Percent">{$percent}%</div>
    <div class="inn-progress__overview" id="inn-{$args['id']}Overview">
        {$overview}
    </div>
    <div class="inn-progress" id="inn-{$args['id']}Progress">
        <div id="inn-{$args['id']}ProgressValue" class="inn-progress__value" style="width: {$percent}%"></div>
    </div>
</div>
HTML;
    }

    public static function setFileCacheHeader()
    {
        // 1 year expired
        $seconds = 3600 * 24 * 30 * 12;
        $ts      = \gmdate('D, d M Y H:i:s', (int) $_SERVER['REQUEST_TIME'] + $seconds) . ' GMT';
        \header("Expires: {$ts}");
        \header('Pragma: cache');
        \header("Cache-Control: public, max-age={$seconds}");
    }

    public static function getGroup(array $item)
    {
        $item = \array_merge(array(
            'groupId' => '',
            'id'      => '',
            'label'   => '',
            'title'   => '',
            'content' => '',
            'col'     => '',
        ), $item);

        $title = $item['title'] ? <<<HTML
title="{$item['title']}"
HTML
        : '';

        $hasTitleClassName = $title ? 'inn-tooltip is-top' : '';

        if (null === $item['col']) {
            $col = '';
        } else {
            $col = $item['col'] ?: '1-3';
            $col = "inn-g_lg-{$col}";
        }

        $idClassNameGroup          = $item['id'] ? "inn-{$item['id']}-group" : '';
        $idClassNameGroupContainer = $item['id'] ? "inn-{$item['id']}-group__container" : '';
        $idClassNameGroupLabel     = $item['id'] ? "inn-{$item['id']}-group__label" : '';
        $idClassNameGroupContent   = $item['id'] ? "inn-{$item['id']}-group__content" : '';
        $groupClassNameLabel       = $item['groupId'] ? "inn-group__label_{$item['groupId']}" : '';
        $groupContainerClassNames  = self::getClassNames(array(
            'inn-group__container'     => true,
            $col                       => (bool) $col,
            $idClassNameGroupContainer => (bool) $idClassNameGroupContainer,
        ));
        $groupClassNames = self::getClassNames(array(
            'inn-group'       => true,
            $idClassNameGroup => (bool) $idClassNameGroup,
        ));
        $groupLabelClassNames = self::getClassNames(array(
            'inn-group__label'     => true,
            $groupClassNameLabel   => (bool) $groupClassNameLabel,
            $idClassNameGroupLabel => (bool) $idClassNameGroupLabel,
            $hasTitleClassName     => (bool) $hasTitleClassName,
        ));
        $groupContentClassNames = self::getClassNames(array(
            'inn-group__content'     => true,
            $idClassNameGroupContent => (bool) $idClassNameGroupContent,
            $hasTitleClassName       => (bool) $hasTitleClassName,
        ));

        return <<<HTML
<div class="{$groupContainerClassNames}">
    <div class="{$groupClassNames}">
        <div class="{$groupLabelClassNames}" {$title}>{$item['label']}</div>
        <div class="{$groupContentClassNames}" {$title}>{$item['content']}</div>
    </div>
</div>
HTML;
    }

    public static function dieJson(array $data)
    {
        \header('Content-Type: application/json');
        \header('Expires: 0');
        \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s') . ' GMT');
        \header('Cache-Control: no-store, no-cache, must-revalidate');
        \header('Pragma: no-cache');

        die(\json_encode(\array_merge(array(
            'code' => 0,
            'data' => null,
        ), $data)));
    }

    public static function isAction($action)
    {
        return \filter_input(\INPUT_GET, 'action', \FILTER_SANITIZE_STRING) === $action;
    }

    public static function getWinCpuUsage()
    {
        $cpus = array();

        // com
        if (\class_exists('\\COM')) {
            $wmi    = new \COM('Winmgmts://');
            $server = $wmi->execquery('SELECT LoadPercentage FROM Win32_Processor');

            $cpus = array();

            $total = 0;

            foreach ($server as $cpu) {
                $total += (int) $cpu->loadpercentage;
            }

            $total        = (int) $total / \count($server);
            $cpus['idle'] = 100 - $total;
            $cpus['user'] = $total;
        // exec
        } else {
            $p = array();
            \exec('wmic cpu get LoadPercentage', $p);

            if (isset($p[1])) {
                $percent      = (int) $p[1];
                $cpus['idle'] = 100 - $percent;
                $cpus['user'] = $percent;
            }
        }

        return $cpus;
    }

    public static function getNetworkStats()
    {
        $filePath = '/proc/net/dev';

        if ( ! \is_readable($filePath)) {
            return I18nApi::_('Unavailable');
        }

        static $eths = null;

        if (null !== $eths) {
            return $eths;
        }

        $lines = \file($filePath);
        unset($lines[0], $lines[1]);
        $eths = array();

        foreach ($lines as $line) {
            $line              = \preg_replace('/\s+/', ' ', \trim($line));
            $lineArr           = \explode(':', $line);
            $numberArr         = \explode(' ', \trim($lineArr[1]));
            $eths[$lineArr[0]] = array(
                'rx' => (int) $numberArr[0],
                'tx' => (int) $numberArr[8],
            );
        }

        return $eths;
    }

    public static function getBtn($tx, $url)
    {
        return <<<HTML
<a href="{$url}" target="_blank" class="btn">{$tx}</a>
HTML;
    }

    public static function getDiskTotalSpace($human = false)
    {
        static $space = null;

        if (null === $space) {
            $space = (float) \disk_total_space(__DIR__);
        }

        if (true === $human) {
            return self::formatBytes($space);
        }

        return $space;
    }

    public static function getDiskFreeSpace($human = false)
    {
        static $space = null;

        if (null === $space) {
            $space = (float) \disk_free_space(__DIR__);
        }

        if (true === $human) {
            return self::formatBytes($space);
        }

        return $space;
    }

    public static function getCpuModel()
    {
        $filePath = '/proc/cpuinfo';

        if ( ! @\is_readable($filePath)) {
            return I18nApi::_('Unavailable');
        }

        $content = \file_get_contents($filePath);
        $cores   = \substr_count($content, 'cache size');

        $lines     = \explode("\n", $content);
        $modelName = \explode(':', $lines[4]);
        $modelName = \trim($modelName[1]);
        $cacheSize = \explode(':', $lines[8]);
        $cacheSize = \trim($cacheSize[1]);

        return "{$cores} x {$modelName} / " . \sprintf(I18nApi::_('%s cache'), $cacheSize);
    }

    public static function getServerTime()
    {
        return \date('Y-m-d H:i:s');
    }

    public static function getServerUpTime()
    {
        $filePath = '/proc/uptime';

        if ( ! @\is_file($filePath)) {
            return I18nApi::_('Unavailable');
        }

        $str   = \file_get_contents($filePath);
        $num   = (float) $str;
        $secs  = \fmod($num, 60);
        $num   = (int) ($num / 60);
        $mins  = $num % 60;
        $num   = (int) ($num / 60);
        $hours = $num % 24;
        $num   = (int) ($num / 24);
        $days  = $num;

        return \sprintf(
            I18nApi::_('%1$dd %2$dh %3$dm %4$ds'),
            $days,
            $hours,
            $mins,
            $secs
        );
    }

    public static function getErrNameByCode($code)
    {
        if (0 === (int) $code) {
            return '';
        }

        $levels = array(
            \E_ALL               => 'E_ALL',
            \E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
            \E_DEPRECATED        => 'E_DEPRECATED',
            \E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            \E_STRICT            => 'E_STRICT',
            \E_USER_NOTICE       => 'E_USER_NOTICE',
            \E_USER_WARNING      => 'E_USER_WARNING',
            \E_USER_ERROR        => 'E_USER_ERROR',
            \E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
            \E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
            \E_CORE_WARNING      => 'E_CORE_WARNING',
            \E_CORE_ERROR        => 'E_CORE_ERROR',
            \E_NOTICE            => 'E_NOTICE',
            \E_PARSE             => 'E_PARSE',
            \E_WARNING           => 'E_WARNING',
            \E_ERROR             => 'E_ERROR',
        );

        $result = '';

        foreach ($levels as $number => $name) {
            if (($code & $number) == $number) {
                $result .= ('' != $result ? ', ' : '') . $name;
            }
        }

        return $result;
    }

    public static function alert($isOk, $text = '')
    {
        $isOk = (bool) $isOk;

        switch ($isOk) {
        case true:
            $status = 'ok';
            $icon   = '&check;';
            break;
        case false:
            $status = 'error';
            $icon   = '&times;';
            break;
        default:
            $icon = '';
        }

        if ($text) {
            $text = <<<HTML
<div class="inn-alert__text">{$text}</div>
HTML;
        }

        return <<<HTML
<div class="inn-alert is-{$status}">
    <div class="inn-alert__icon">{$icon}</div>
    {$text}
</div>
HTML;
    }

    public static function isWin()
    {
        return \PHP_OS === 'WINNT';
    }

    public static function getClientIp()
    {
        $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

        foreach ($keys as $key) {
            if ( ! isset($_SERVER[$key])) {
                continue;
            }

            $ip = \array_filter(\explode(',', $_SERVER[$key]));
            $ip = \filter_var(\end($ip), \FILTER_VALIDATE_IP);

            if ($ip) {
                return $ip;
            }
        }

        return '';
    }

    public static function getCpuUsage()
    {
        static $cpu = null;

        if (null !== $cpu) {
            return $cpu;
        }

        if (self::isWin()) {
            $cpu = self::getWinCpuUsage();

            return $cpu;
        }

        $filePath = ('/proc/stat');

        if ( ! @\is_readable($filePath)) {
            $cpu = array();

            return $cpu;
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

    public static function getHumanCpuUsageDetail()
    {
        $cpu = self::getCpuUsage();

        if ( ! $cpu) {
            return '';
        }

        $html = '';

        foreach ($cpu as $k => $v) {
            $html .= <<<HTML
<span class="inn-small-group"><span class="item-name">{$k}</span>
<span class="item-value">{$v}</span></span>
HTML;
        }

        return $html;
    }

    public static function getHumanCpuUsage()
    {
        $cpu = self::getCpuUsage();

        return $cpu ?: array();
    }

    public static function getSysLoadAvg()
    {
        if (self::isWin()) {
            return array(0, 0, 0);
        }

        return \array_map(function ($load) {
            return (float) \sprintf('%.2f', $load);
        }, \sys_getloadavg());
    }

    public static function getMemoryUsage($key)
    {
        $key = \ucfirst($key);

        if (self::isWin()) {
            return 0;
        }

        static $memInfo = null;

        if (null === $memInfo) {
            $memInfoFile = '/proc/meminfo';

            if ( ! @\is_readable($memInfoFile)) {
                $memInfo = 0;

                return 0;
            }

            $memInfo = \file_get_contents($memInfoFile);
            $memInfo = \str_replace(array(
                ' kB',
                '  ',
            ), '', $memInfo);

            $lines = array();

            foreach (\explode("\n", $memInfo) as $line) {
                if ( ! $line) {
                    continue;
                }

                $line            = \explode(':', $line);
                $lines[$line[0]] = (float) $line[1] * 1024;
            }

            $memInfo = $lines;
        }

        if ( ! isset($memInfo['MemTotal'])) {
            return 0;
        }

        switch ($key) {
        case 'MemRealUsage':
            if (isset($memInfo['MemAvailable'])) {
                return $memInfo['MemTotal'] - $memInfo['MemAvailable'];
            }

            if (isset($memInfo['MemFree'])) {
                if (isset($memInfo['Buffers'], $memInfo['Cached'])) {
                    return $memInfo['MemTotal'] - $memInfo['MemFree'] - $memInfo['Buffers'] - $memInfo['Cached'];
                }

                return $memInfo['MemTotal'] - $memInfo['Buffers'];
            }

            return 0;
        case 'MemUsage':
            return isset($memInfo['MemFree']) ? $memInfo['MemTotal'] - $memInfo['MemFree'] : 0;
        case 'SwapUsage':
            if ( ! isset($memInfo['SwapTotal']) || ! isset($memInfo['SwapFree'])) {
                return 0;
            }

            return $memInfo['SwapTotal'] - $memInfo['SwapFree'];
        }

        return isset($memInfo[$key]) ? $memInfo[$key] : 0;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        if ( ! $bytes) {
            return 0;
        }

        $base     = \log($bytes, 1024);
        $suffixes = array('', ' K', ' M', ' G', ' T');

        return \round(\pow(1024, ($base - \floor($base))), $precision) . $suffixes[\floor($base)];
    }

    public static function getHumamMemUsage($key)
    {
        return self::formatBytes(self::getMemoryUsage($key));
    }

    public static function strcut($str, $len = 20)
    {
        if (\strlen($str) > $len) {
            return \mb_strcut($str, 0, $len) . '...';
        }

        return $str;
    }
}
