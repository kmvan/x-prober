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
        EventsApi::on('mods', [$this, 'filter']);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = [
            'title'     => I18nApi::_('Server status'),
            'tinyTitle' => I18nApi::_('Status'),
            'display'   => [$this, 'display'],
        ];

        return $mods;
    }

    public function display()
    {
        return <<<HTML
<div class="inn-row">
    {$this->getDisplaySysLoad()}
    {$this->getDisplayCpuUsage()}
    {$this->getDisplayMemoryUsage()}
    {$this->getDisplayMemCached()}
    {$this->getDisplayMemBuffers()}
    {$this->getDisplaySwapUsage()}
    {$this->getDisplaySwapCached()}
</div>
HTML;
    }

    private function getDisplaySysLoad()
    {
        return HelperApi::getGroup([
            'label'   => I18nApi::_('System load'),
            'id'      => 'systemLoadAvg',
            'col'     => '1-1',
            'content' => \implode('', \array_map(function ($avg) {
                $avg = \sprintf('%.2f', $avg);

                return <<<HTML
<div class="inn-system-load-avg__group">{$avg}</div>
HTML;
            }, HelperApi::getSysLoadAvg())),
        ]);
    }

    private function getDisplayCpuUsage()
    {
        return HelperApi::getGroup([
            'label'   => I18nApi::_('CPU usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl([
                'id'       => 'cpuUsage',
                'usage'    => 10,
                'total'    => 100,
                'overview' => '10% / 100%',
            ]),
        ]);
    }

    private function getDisplayMemoryUsage()
    {
        return HelperApi::getGroup([
            'label'   => I18nApi::_('Memory usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl([
                'id'      => 'memUsage',
                'usage'   => HelperApi::getMemoryUsage('MemUsage'),
                'total'   => HelperApi::getMemoryUsage('MemTotal'),
            ]),
        ]);
    }

    private function getDisplaySwapUsage()
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        if ( ! $total) {
            return '';
        }

        return HelperApi::getGroup([
            'label'   => I18nApi::_('SWAP usage'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl([
                'id'      => 'swapUsage',
                'usage'   => HelperApi::getMemoryUsage('SwapUsage'),
                'total'   => $total,
            ]),
        ]);
    }

    private function getDisplaySwapCached()
    {
        $total = HelperApi::getMemoryUsage('SwapTotal');

        if ( ! $total) {
            return '';
        }

        return HelperApi::getGroup([
            'label'   => I18nApi::_('SWAP cached'),
            'col'     => '1-1',
            'content' => HelperApi::getProgressTpl([
                'id'      => 'swapCached',
                'usage'   => HelperApi::getMemoryUsage('SwapCached'),
                'total'   => $total,
            ]),
        ]);
    }

    private function getDisplayMemCached()
    {
        return HelperApi::getGroup([
            'label'   => I18nApi::_('Memory cached'),
            'col'     => '1-2',
            'content' => HelperApi::getProgressTpl([
                'id'      => 'memCached',
                'usage'   => HelperApi::getMemoryUsage('Cached'),
                'total'   => HelperApi::getMemoryUsage('MemTotal'),
            ]),
        ]);
    }

    private function getDisplayMemBuffers()
    {
        return HelperApi::getGroup([
            'label'   => I18nApi::_('Memory buffers'),
            'col'     => '1-2',
            'content' => HelperApi::getProgressTpl([
                'id'      => 'memBuffers',
                'usage'   => HelperApi::getMemoryUsage('Buffers'),
                'total'   => HelperApi::getMemoryUsage('MemTotal'),
            ]),
        ]);
    }
}
