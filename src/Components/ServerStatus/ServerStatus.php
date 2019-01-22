<?php

namespace InnStudio\Prober\Components\ServerStatus;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class ServerStatus extends ServerStatusApi
{
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
            $avg = \sprintf('%.2f', $avg);

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
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('CPU usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl(array(
                'id'       => 'cpuUsage',
                'usage'    => 10,
                'total'    => 100,
                'overview' => '10% / 100%',
            )),
        ));
    }

    private function getDisplayMemoryUsage()
    {
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('Memory usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl(array(
                'id'      => 'memRealUsage',
                'usage'   => HelperApi::getMemoryUsage('MemRealUsage'),
                'total'   => HelperApi::getMemoryUsage('MemTotal'),
            )),
        ));
    }

    private function getDisplaySwapUsage()
    {
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('SWAP usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl(array(
                'id'      => 'swapRealUsage',
                'usage'   => HelperApi::getMemoryUsage('SwapRealUsage'),
                'total'   => HelperApi::getMemoryUsage('SwapTotal'),
            )),
        ));
    }
}
