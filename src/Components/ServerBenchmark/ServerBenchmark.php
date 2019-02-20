<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class ServerBenchmark
{
    private $ID = 'serverBenchmark';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 600);
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'lang' => array(
                'loading' => I18nApi::_('⏳ Loading...'),
                'retry'   => I18nApi::_('❌ Error, click to retry'),
                'goTest'  => I18nApi::_('👆 Click to test'),
            ),
        );

        return $conf;
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
        $lang = I18nApi::_('💡 Higher is better. This result is only used as reference data for author testing. Note: the benchmark marks are not the only criterion for evaluating the quality of a host/server.');

        return <<<HTML
<p class="inn-mod__description">{$lang}</p>
<div class="inn-row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => 'Monovm/PHP7.3',
                'url'     => 'https://monovm.com/affiliate/ref/528',
                'detail'  => array(
                    'hash'      => 778,
                    'intLoop'   => 942,
                    'floatLoop' => 943,
                    'ioLoop'    => 725,
                ),
            ),
            array(
                'label'   => 'Vultr/PHP7.3',
                'url'     => 'https://www.vultr.com/?ref=7826363-4F',
                'content' => 3305,
            ),
            array(
                'label'   => 'Amazon/EC2/PHP7.2',
                'url'     => 'https://aws.amazon.com/',
                'content' => 3150,
            ),
            array(
                'label'   => 'VPSSERVER/KVM/PHP7.2',
                'url'     => 'https://www.vpsserver.com/?affcode=32d56f2dd1b6',
                'content' => 3125,
            ),
            array(
                'label'   => 'SpartanHost/KVM/PHP7.2',
                'url'     => 'https://billing.spartanhost.net/aff.php?aff=801',
                'content' => 3174,
            ),
            array(
                'label'   => 'Aliyun/ECS/PHP7.2',
                'url'     => 'https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii',
                'content' => 3302,
            ),
            array(
                'label'   => 'Vultr/PHP7.2',
                'url'     => 'https://www.vultr.com/?ref=7256513',
                'content' => 3182,
            ),
            array(
                'label'   => 'RamNode/PHP7.2',
                'url'     => 'https://clientarea.ramnode.com/aff.php?aff=4143',
                'content' => 3131,
            ),
            array(
                'label'   => 'Linode/PHP7.2',
                'url'     => 'https://www.linode.com/?r=2edf930598b4165760c1da9e77b995bac72f8ad1',
                'content' => 3091,
            ),
            array(
                'label'   => 'Tencent/PHP7.2',
                'url'     => 'https://cloud.tencent.com/',
                'content' => 3055,
            ),
            array(
                'label'   => 'BandwagonHOST/KVM/PHP7.2',
                'url'     => 'https://bandwagonhost.com/aff.php?aff=34116',
                'content' => 2181,
            ),
        );

        // order
        $sort = array();

        foreach ($items as &$item) {
            if (isset($item['detail'])) {
                $item['content'] = \array_sum($item['detail']);
            }

            $sort[] = (int) $item['content'];
        }

        \array_multisort(
            $items,
            \SORT_DESC,
            \SORT_NUMERIC,
            $sort,
            \SORT_DESC,
            \SORT_NUMERIC
        );
        \array_unshift(
            $items,
            array(
                'label'   => I18nApi::_('My server'),
                'content' => '<div id="inn-benchmark__container"></div>',
            )
        );

        $items = \array_map(function (array $item) {
            if (isset($item['url'])) {
                $item['label'] = <<<HTML
<a href="{$item['url']}" target="_blank">{$item['label']}</a>
HTML;
            }

            if (isset($item['content']) && \is_numeric($item['content'])) {
                $item['content'] = \number_format((float) $item['content']);
            }

            $item['title'] = isset($item['detail']) ? \implode(', ', \array_map(function ($id, $v) {
                return "{$id}: {$v}";
            }, \array_keys($item['detail']), $item['detail'])) : '';

            return $item;
        }, $items);

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }
}
