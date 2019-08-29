<?php

namespace InnStudio\Prober\Components\NetworkStats;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;

class Fetch extends NetworkStatsConstants
{
    public function __construct()
    {
        HelperApi::isWin() || EventsApi::on('fetch', [$this, 'filter']);
    }

    public function filter(array $items)
    {
        $items[$this->ID] = [
            'networks' => HelperApi::getNetworkStats(),
        ];

        return $items;
    }

    public function display()
    {
        $items = [];
        $stats = HelperApi::getNetworkStats();

        if ( ! \is_array($stats)) {
            return '<div>' . HelperApi::getNetworkStats() . '</div>';
        }

        foreach (HelperApi::getNetworkStats() as $ethName => $item) {
            if ( ! $item['rx'] && ! $item['tx']) {
                continue;
            }

            $rxHuman       = HelperApi::formatBytes($item['rx']);
            $txHuman       = HelperApi::formatBytes($item['tx']);
            $ethNameEncode = \urlencode($ethName);
            $items[]       = [
                'label'   => $ethName,
                'id'      => $this->ID,
                'col'     => null,
                'content' => <<<HTML
<div class="inn-network-stats__container">
    <div class="inn-network-stats">
        <div class="inn-network-stats__rx__total" id="inn-network-stats__rx__total__{$ethNameEncode}">{$rxHuman}</div>
        <div class="inn-network-stats__rx__rate">
            <span class="inn-network-stats__icon is-rx">&#x25BC; </span>
            <span class="inn-network-stats__rx__rate" id="inn-network-stats__rx__rate__{$ethNameEncode}">0</span>
            <span class="inn-network-stats__rx__rate__second">/s</span>
        </div>
    </div>
    <div class="inn-network-stats">
        <div class="inn-network-stats__tx__total" id="inn-network-stats__tx__total__{$ethNameEncode}">{$txHuman}</div>
        <div class="inn-network-stats__tx__rate">
            <span class="inn-network-stats__icon is-tx">&#x25B2; </span>
            <span class="inn-network-stats__tx__rate" id="inn-network-stats__tx__rate__{$ethNameEncode}">0</span>
            <span class="inn-network-stats__tx__rate__second">/s</span>
        </div>
    </div>
</div>
HTML
            ];
        }

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }
}
