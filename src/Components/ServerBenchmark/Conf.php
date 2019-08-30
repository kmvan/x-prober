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
        $lang = '<span class="inn-emoji">💡</span> ' . I18nApi::_('Higher is better. This result is only used as reference data for author testing. Note: the benchmark marks are not the only criterion for evaluating the quality of a host/server.');

        return <<<HTML
<p class="inn-mod__description">{$lang}</p>
<div class="inn-row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getContent()
    {
        $items = \unserialize(ServerBenchmarkMarks::$marks);

        // order
        $sort = array();

        foreach ($items as &$item) {
            $item['groupId'] = $this->ID;

            if (isset($item['detail']) && \is_array($item['detail'])) {
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
                'groupId' => $this->ID,
                'label'   => I18nApi::_('My server'),
                'content' => <<<HTML
<div id="inn-benchmark__container"></div>
HTML
            )
        );

        $items = \array_map(function (array $item) {
            // set aff url
            if (isset($item['url'])) {
                $lang = I18nApi::_('Go to service provider homepage');
                $item['label'] = <<<HTML
<a href="{$item['url']}" title="{$lang}" target="_blank">{$item['label']}</a>
HTML;
            }

            // check prober url
            $proberUrl = isset($item['proberUrl']) && $item['proberUrl'] ? $item['proberUrl'] : '';

            if (isset($item['content']) && \is_numeric($item['content'])) {
                $item['content'] = \number_format((float) $item['content']);

                // set date
                if (isset($item['date'])) {
                    $item['content'] .= <<<HTML
&nbsp;<small class="inn-group__content__small">({$item['date']})</small>
HTML;
                }

                // set x prober url
                if ($proberUrl) {
                    $lang = I18nApi::_('Go to prober page');
                    $item['content'] .= <<<HTML
&nbsp;<a href="{$item['proberUrl']}" title="{$lang}" class="inn-emoji" target="_blank">🔗</a>
HTML;
                }

                // set bin url
                if (isset($item['binUrl']) && $item['binUrl']) {
                    $lang = I18nApi::_('Download file for network speed testing');
                    $item['content'] .= <<<HTML
&nbsp;<a href="{$item['binUrl']}" title="{$lang}" class="inn-emoji" target="_blank">⬇️</a>
HTML;
                }
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
