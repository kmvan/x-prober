<?php

namespace InnStudio\Prober\ServerBenchmark;

use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\I18n\I18nApi;

class ServerBenchmark
{
    private $ID = 'serverBenchmark';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 600);
        EventsApi::on('script', array($this, 'filterJs'));
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('Server Benchmark'),
            'tinyTitle' => I18nApi::_('Benchmark'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        $lang = I18nApi::_('💡 Higher is better. Note: the benchmark marks are not the only criterion for evaluating the quality of a host/server.');
        echo <<<HTML
<p class="description">{$lang}</p>
<div class="row">
    {$this->getContent()}
</div>
HTML;
    }

    public function filterJs()
    {
        ?>
<script>
(function(){
    var el = document.getElementById('benchmark-btn');
    var errTx = '❌ <?php echo I18nApi::_('Error, click to retry'); ?>';

    if (!el) {
        return;
    }

    function getPoints() {
        el.innerHTML = '⏳ <?php echo I18nApi::_('Loading...'); ?>';
        var xhr = new XMLHttpRequest();
        xhr.onload = load;
        xhr.open('get', '?action=benchmark');
        xhr.send();
    }

    function load() {
        if (this.readyState !== 4) {
            return;
        }

        if (this.status >= 200 && this.status < 400) {
            var res = JSON.parse(this.responseText);
            var points = 0;

            if (res && res.code === 0) {
                for (var k in res.data.points) {
                    points += res.data.points[k];
                }

                el.innerHTML = '✔️ ' + points.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            } else if (res && res.code) {
                el.innerHTML = '⏳ ' + res.msg;
            } else {
                el.innerHTML = res;
            }
        } else {
            el.innerHTML = errTx;
        }
    }

    el.addEventListener('click', getPoints);
})()
</script>
        <?php
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => I18nApi::_('Amazon/EC2'),
                'url'     => 'https://aws.amazon.com/',
                'content' => 3150,
            ),
            array(
                'label'   => I18nApi::_('VPSSERVER/KVM'),
                'url'     => 'https://www.vpsserver.com/?affcode=32d56f2dd1b6',
                'content' => 3125,
            ),
            array(
                'label'   => I18nApi::_('SpartanHost/KVM'),
                'url'     => 'https://billing.spartanhost.net/aff.php?aff=801',
                'content' => 3174,
            ),
            array(
                'label'   => I18nApi::_('Aliyun/ECS'),
                'url'     => 'https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii',
                'content' => 3302,
            ),
            array(
                'label'   => I18nApi::_('Vultr'),
                'url'     => 'https://www.vultr.com/?ref=7256513',
                'content' => 3182,
            ),
            array(
                'label'   => I18nApi::_('RamNode'),
                'url'     => 'https://clientarea.ramnode.com/aff.php?aff=4143',
                'content' => 3131,
            ),
            array(
                'label'   => I18nApi::_('Linode'),
                'url'     => 'https://www.linode.com/?r=2edf930598b4165760c1da9e77b995bac72f8ad1',
                'content' => 3091,
            ),
            array(
                'label'   => I18nApi::_('Tencent'),
                'url'     => 'https://cloud.tencent.com/',
                'content' => 3055,
            ),
            array(
                'label'   => I18nApi::_('AnyNode/HDD'),
                'url'     => 'https://billing.anynode.net/aff.php?aff=511',
                'content' => 2641,
            ),
            array(
                'label'   => I18nApi::_('BandwagonHOST/SSD'),
                'url'     => 'https://bandwagonhost.com/aff.php?aff=34116',
                'content' => 2181,
            ),
        );

        // order
        $itemsOrder = array();

        foreach ($items as $item) {
            $itemsOrder[] = (int) $item['content'];
        }

        \array_multisort(
            $items,
            \SORT_DESC,
            \SORT_NUMERIC,
            $itemsOrder,
            \SORT_DESC,
            \SORT_NUMERIC
        );
        \array_unshift(
            $items,
            array(
                'label'   => I18nApi::_('My server'),
                'content' => '<a id="benchmark-btn">👆 ' . I18nApi::_('Click to test') . '</a>',
            )
        );

        $content = '';

        foreach ($items as $item) {
            $title = ! isset($item['title']) ? '' : <<<HTML
title="{$item['title']}"
HTML;
            $col = isset($item['col']) ? $item['col'] : '1-3';
            $id  = ! isset($item['id']) ? '' : <<<HTML
id="{$item['id']}"
HTML;
            $label = ! isset($item['url']) ? $item['label'] : <<<HTML
<a href="{$item['url']}" target="_blank">{$item['label']}</a>
HTML;
            $marks = \is_numeric($item['content']) ? \number_format((float) $item['content']) : $item['content'];
            $content .= <<<HTML
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$label}</div>
        <div class="group-content" {$id} {$title}>{$marks}</div>
    </div>
</div>
HTML;
        }

        return $content;
    }
}
