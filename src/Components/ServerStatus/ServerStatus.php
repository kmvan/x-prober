<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class ServerStatus
{
    private $ID = 'serverStatus';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'));
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('Server status'),
            'tinyTitle' => I18nApi::_('Status'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        $sysLoadAvg    = HelperApi::getSysLoadAvg();
        $langSysLoad   = I18nApi::_('System load');
        $cpuUsage      = I18nApi::_('CPU usage');
        $realMemUsage  = I18nApi::_('Memory usage');
        $realSwapUsage = I18nApi::_('SWAP usage');

        $sysLoadAvg = \implode('', \array_map(function ($avg) {
            return <<<HTML
<div class="inn-system-load-avg__group">{$avg}</div>
HTML;
        }, $sysLoadAvg));

        return <<<HTML
<div class="inn-group">
    <div class="inn-group__label">{$langSysLoad}</div>
    <div class="inn-group__content" id="inn-systemLoadAvg">{$sysLoadAvg}</div>
</div>
<div class="inn-group">
    <div class="inn-group__label">{$cpuUsage}</div>
    <div class="inn-group__content" id="inn-cpuUsage">
        <div class="inn-progress__container">
            <div class="inn-progress__number">
                <div id="inn-cpuUsagePercent">10%</div>
            </div>
            <div class="inn-progress" id="inn-cpuUsageProgress">
                <div id="inn-cpuUsageProgressValue" class="inn-progress__value" style="width: 10%"></div>
            </div>
        </div>
    </div>
</div>
<div class="inn-group inn-memory-usage">
    <div class="inn-group__label">{$realMemUsage}</div>
    <div class="inn-group__content">
        <div class="inn-progress__container">
            <div class="inn-progress__percent" id="inn-memRealUsagePercent">{$this->getMemUsage('MemRealUsage', true)}%</div>
            <div class="inn-progress__number">
                <div id="inn-memRealUsageOverview">
                    {$this->getHumamMemUsage('MemRealUsage')}
                    /
                    {$this->getHumamMemUsage('MemTotal')}
                </div>
            </div>
            <div class="inn-progress" id="inn-memRealUsageProgress">
                <div id="inn-memRealUsageProgressValue" class="inn-progress__value" style="width: {$this->getMemUsage('MemRealUsage', true)}%"></div>
            </div>
        </div>
    </div>
</div>
<div class="inn-group inn-swap-usage">
    <div class="inn-group__label">{$realSwapUsage}</div>
    <div class="inn-group__content">
        <div class="inn-progress__container">
            <div class="inn-progress__percent" id="inn-swapRealUsagePercent">{$this->getMemUsage('SwapRealUsage', true, 'SwapTotal')}%</div>
            <div class="inn-progress__number">
                <span id="inn-swapRealUsage">
                    {$this->getHumamMemUsage('SwapRealUsage')}
                    /
                    {$this->getHumamMemUsage('SwapTotal')}
                </span>
            </div>
            <div class="inn-progress" id="inn-swapRealUsageProgress">
                <div id="inn-swapRealUsageProgressValue" class="inn-progress__value" style="width: {$this->getMemUsage('SwapRealUsage', true, 'SwapTotal')}%"></div>
            </div>
        </div>
    </div>
</div>
HTML;
    }

    private function getHumamMemUsage($type)
    {
        return HelperApi::getHumamMemUsage($type);
    }

    private function _($str)
    {
        return I18nApi::_($str);
    }

    private function getMemUsage($key, $precent = false, $totalKey = 'MemTotal')
    {
        if (false === $precent) {
            return HelperApi::getMemoryUsage($key);
        }

        return HelperApi::getMemoryUsage($key) ? \sprintf('%01.2f', HelperApi::getMemoryUsage($key) / HelperApi::getMemoryUsage($totalKey) * 100) : 0;
    }
}
