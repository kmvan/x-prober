<?php

namespace InnStudio\Prober\ServerBenchmark;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\I18n\Api as I18n;

class ServerBenchmark
{
    private $ID = 'serverBenchmark';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 600);
        Events::on('script', array($this, 'filterJs'));
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('Server Benchmark'),
            'tinyTitle' => I18n::_('Benchmark'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        $lang = I18n::_('💡 Hight is better.');
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
    var errTx = '❌ <?php echo I18n::_('Error, click to retry'); ?>';

    if (!el) {
        return;
    }

    function getPoints() {
        el.innerHTML = '⏳ <?php echo I18n::_('Loading...'); ?>';
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

    private function I18n($str)
    {
        return I18n::_($str);
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => I18n::_('My server'),
                'content' => '<a id="benchmark-btn" href="javascript:;">👆 ' . I18n::_('Click to test') . '</a>',
            ),
            array(
                'label'   => '<a href="https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii">' . I18n::_('Aliyun/ECS/PHP7') . '</a>',
                'content' => 3302,
            ),
            array(
                'label'   => '<a href="https://www.vultr.com/?ref=7256513" target="_blank">' . I18n::_('Vultr/PHP7') . '</a>',
                'content' => 3182,
            ),
            array(
                'label'   => '<a href="https://www.linode.com/?r=2edf930598b4165760c1da9e77b995bac72f8ad1" target="_blank">' . I18n::_('Linode/PHP7') . '</a>',
                'content' => 3091,
            ),
            array(
                'label'   => I18n::_('Tencent/PHP7'),
                'content' => 3055,
            ),
            array(
                'label'   => '<a href="https://billing.anynode.net/aff.php?aff=511"  target="_blank">' . I18n::_('AnyNode/HDD/PHP7') . '</a>',
                'content' => 2641,
            ),
            array(
                'label'   => '<a href="https://www.vultr.com/?ref=7256513" target="_blank">' . I18n::_('Vultr/PHP5') . '</a>',
                'content' => 2420,
            ),
            array(
                'label'   => '<a href="https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii">' . I18n::_('Aliyun/Int/PHP5') . '</a>',
                'content' => -7686,
            ),
        );

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';

            $content .= <<<HTML
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$id} {$title}>{$item['content']}</div>
    </div> 
</div>
HTML;
        }

        return $content;
    }
}
