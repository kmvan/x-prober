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
        new FilterFetchItems();
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
{$this->getDisplayCpuUsage()}
{$this->getDisplayMemoryUsage()}
{$this->getDisplaySwapUsage()}
HTML;
    }

    private function getDisplayCpuUsage()
    {
        return $this->getProgressTpl(array(
            'title'   => I18nApi::_('CPU usage'),
            'id'      => 'cpuUsage',
            'usage'   => 1,
            'percent' => 1,
            'total'   => '100%',
        ));
    }

    private function getDisplayMemoryUsage()
    {
        return $this->getProgressTpl(array(
            'title'   => I18nApi::_('Memory usage'),
            'id'      => 'memRealUsage',
            'usage'   => $this->getHumamMemUsage('MemRealUsage'),
            'percent' => $this->getMemUsage('MemRealUsage', true, 'SwapTotal'),
            'total'   => $this->getHumamMemUsage('MemRealUsage'),
        ));
    }

    private function getDisplaySwapUsage()
    {
        return $this->getProgressTpl(array(
            'title'   => I18nApi::_('SWAP usage'),
            'id'      => 'swapRealUsage',
            'usage'   => $this->getHumamMemUsage('SwapRealUsage'),
            'percent' => $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'),
            'total'   => $this->getHumamMemUsage('SwapTotal'),
        ));
    }

    private function getProgressTpl(array $args)
    {
        $args = \array_merge(array(
            'id'      => '',
            'title'   => '',
            'percent' => 0,
            'usage'   => 0,
            'total'   => 0,
        ), $args);

        if ( ! $args['total']) {
            return '';
        }

        return <<<HTML
<div class="inn-group inn-swap-usage">
    <div class="inn-group__label">{$args['title']}</div>
    <div class="inn-group__content">
        <div class="inn-progress__container">
            <div class="inn-progress__percent" id="inn-{$args['id']}Percent">{$args['percent']}%</div>
            <div class="inn-progress__number">
                <span id="inn-{$args['id']}">
                    {$args['usage']} / {$args['total']}
                </span>
            </div>
            <div class="inn-progress" id="inn-{$args['id']}Progress">
                <div id="inn-{$args['id']}ProgressValue" class="inn-progress__value" style="width: {$args['percent']}%"></div>
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

        $total = HelperApi::getMemoryUsage($totalKey);

        if ( ! $total) {
            return 0;
        }

        return HelperApi::getMemoryUsage($key) ? \sprintf('%01.2f', HelperApi::getMemoryUsage($key) / $total * 100) : 0;
    }
}
