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
        return <<<HTML
<div class="inn-row">
    {$this->getDisplaySysLoad()}
    {$this->getDisplayCpuUsage()}
    {$this->getDisplayMemRealUsage()}
    {$this->getDisplayMemCached()}
    {$this->getDisplayMemBuffers()}
    {$this->getDisplaySwapUsage()}
    {$this->getDisplaySwapCached()}
</div>
HTML;
    }

    private function getDisplaySysLoad()
    {
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('System load'),
            'id'      => 'systemLoadAvg',
            'col'     => '1-1',
            'content' => \implode('', \array_map(function ($avg) {
                $avg = \sprintf('%.2f', $avg);

                return <<<HTML
<div class="inn-system-load-avg__group">{$avg}</div>
HTML;
            }, HelperApi::getSysLoadAvg())),
        ));
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

    private function getDisplayMemRealUsage()
    {
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('Memory real usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl(array(
                'id'    => 'memRealUsage',
                'usage' => HelperApi::getMemoryUsage('MemRealUsage'),
                'total' => HelperApi::getMemoryUsage('MemTotal'),
            )),
        ));
    }

    private function getDisplaySwapUsage()
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        if ( ! $total) {
            return '';
        }

        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('SWAP usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl(array(
                'id'    => 'swapUsage',
                'usage' => HelperApi::getMemoryUsage('SwapUsage'),
                'total' => $total,
            )),
        ));
    }

    private function getDisplaySwapCached()
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        if ( ! $total) {
            return '';
        }

        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('SWAP cached'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl(array(
                'id'    => 'swapCached',
                'usage' => HelperApi::getMemoryUsage('SwapCached'),
                'total' => HelperApi::getMemoryUsage('SwapUsage'),
            )),
        ));
    }

    private function getDisplayMemCached()
    {
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('Memory cached'),
            'col'     => '1-2',
            'content' => HelperApi::getProgressTpl(array(
                'id'    => 'memCached',
                'usage' => HelperApi::getMemoryUsage('Cached'),
                'total' => HelperApi::getMemoryUsage('MemUsage'),
            )),
        ));
    }

    private function getDisplayMemBuffers()
    {
        return HelperApi::getGroup(array(
            'label'   => I18nApi::_('Memory buffers'),
            'col'     => '1-2',
            'content' => HelperApi::getProgressTpl(array(
                'id'    => 'memBuffers',
                'usage' => HelperApi::getMemoryUsage('Buffers'),
                'total' => HelperApi::getMemoryUsage('MemUsage'),
            )),
        ));
    }
}
