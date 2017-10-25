<?php

namespace InnStudio\Prober\NetworkStats;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class NetworkStats
{
    private $ID = 'networkStats';

    public function __construct()
    {
        Events::on('style', array($this, 'filterStyle'));
        Events::patch('mods', array($this, 'filter'), 100);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('Network stats'),
            'tinyTitle' => I18n::_('Net'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        ?>
<div class="row">
    <?php echo $this->getContent(); ?>
</div>
        <?php
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

        foreach (Helper::getNetworkStats() as $ethName => $item) {
            $rxHuman = Helper::formatBytes($item['rx']);
            $txHuman = Helper::formatBytes($item['tx']);
            $items[] = array(
                'label'   => $ethName,
                'content' => "<div class=\"network-stats-container\">
                    <div class=\"rx\">
                        <div><span id=\"network-{$ethName}-rx-total\">{$rxHuman}</span></div>
                        <div><span class=\"icon\">▼</span><span id=\"network-{$ethName}-rx-rate\">0</span><span class=\"second\">/s</span></div>
                    </div>
                    <div class=\"tx\">
                        <div><span id=\"network-{$ethName}-tx-total\">{$rxHuman}</span></div>
                        <div><span class=\"icon\">▲</span><span id=\"network-{$ethName}-tx-rate\">0</span><span class=\"second\">/s</span></div>
                    </div>
                </div>",
            );
        }

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-1';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';
            $content .= <<<EOT
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$title} {$id}>{$item['content']}</div>
    </div> 
</div>
EOT;
        }

        return $content;
    }
}
