<?php

namespace InnStudio\Prober\NetworkStats;

use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\Helper\HelperApi;
use InnStudio\Prober\I18n\I18nApi;

class NetworkStats
{
    private $ID = 'networkStats';

    public function __construct()
    {
        HelperApi::isWin() || EventsApi::on('style', array($this, 'filterStyle'));
        HelperApi::isWin() || EventsApi::on('mods', array($this, 'filter'), 100);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('Network stats'),
            'tinyTitle' => I18nApi::_('Net'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        return <<<HTML
<div class="row">
    {$this->getContent()}
</div>
HTML;
    }

    public function filterStyle()
    {
        ?>
<style>
.network-stats-container > *{
    float: left;
    width: 50%;
    text-align: center;
}
</style>
        <?php
    }

    private function getContent()
    {
        $items = array();
        $stats = HelperApi::getNetworkStats();

        if ( ! \is_array($stats)) {
            return '<div>' . HelperApi::getNetworkStats() . '</div>';
        }

        foreach (HelperApi::getNetworkStats() as $ethName => $item) {
            $rxHuman = HelperApi::formatBytes($item['rx']);
            $txHuman = HelperApi::formatBytes($item['tx']);
            $items[] = array(
                'label'   => $ethName,
                'content' => <<<HTML
<div class="network-stats-container">
    <div class="rx">
        <div><span id="network-{$ethName}-rx-total">{$rxHuman}</span></div>
        <div><span class="icon">▼</span><span id="network-{$ethName}-rx-rate">0</span><span class="second">/s</span></div>
    </div>
    <div class="tx">
        <div><span id="network-{$ethName}-tx-total">{$txHuman}</span></div>
        <div><span class="icon">▲</span><span id="network-{$ethName}-tx-rate">0</span><span class="second">/s</span></div>
    </div>
</div>
HTML
            );
        }

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-1';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';
            $content .= <<<HTML
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$title} {$id}>{$item['content']}</div>
    </div>
</div>
HTML;
        }

        return $content;
    }
}
