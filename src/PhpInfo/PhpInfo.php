<?php

namespace InnStudio\Prober\PhpInfo;

use InnStudio\Prober\Config\ConfigApi;
use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\Helper\HelperApi;
use InnStudio\Prober\I18n\I18nApi;

class PhpInfo
{
    private $ID = 'phpInfo';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 300);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('PHP information'),
            'tinyTitle' => I18nApi::_('PHP'),
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

    private function getContent()
    {
        $errLevels = HelperApi::getErrNameByCode(\ini_get('error_reporting'));

        $items = array(
            array(
                'label'   => $this->_('PHP info detail'),
                'content' => HelperApi::getBtn("👆 {$this->_('Click to check')}", '?action=phpInfo'),
            ),
            array(
                'label'   => $this->_('Version'),
                'title'   => \sprintf($this->_('X Prober buildin latest PHP stable version: %s'), '7.2.12'),
                'content' => \PHP_VERSION . ' ' . (\version_compare(\PHP_VERSION, ConfigApi::$LATEST_PHP_STABLE_VERSION, '<') ? $this->_('(Old)') : $this->_('(Up to date)')),
            ),
            array(
                'label'   => $this->_('SAPI interface'),
                'content' => \PHP_SAPI,
            ),
            array(
                'label'   => $this->_('Error reporting'),
                'title'   => "error_reporting: {$errLevels}",
                'content' => HelperApi::strcut($errLevels),
            ),
            array(
                'label'   => $this->_('Max memory limit'),
                'title'   => 'memory_limit',
                'content' => \ini_get('memory_limit'),
            ),
            array(
                'label'   => $this->_('Max POST size'),
                'title'   => 'post_max_size',
                'content' => \ini_get('post_max_size'),
            ),
            array(
                'label'   => $this->_('Max upload size'),
                'title'   => 'upload_max_filesize',
                'content' => \ini_get('upload_max_filesize'),
            ),
            array(
                'label'   => $this->_('Max input variables'),
                'title'   => 'max_input_vars',
                'content' => \ini_get('max_input_vars'),
            ),
            array(
                'label'   => $this->_('Max execution time'),
                'title'   => 'max_execution_time',
                'content' => \ini_get('max_execution_time'),
            ),
            array(
                'label'   => $this->_('Timeout for socket'),
                'title'   => 'default_socket_timeout',
                'content' => \ini_get('default_socket_timeout'),
            ),
            array(
                'label'   => $this->_('Display errors'),
                'title'   => 'display_errors',
                'content' => HelperApi::getIni('display_errors'),
            ),
            array(
                'label'   => $this->_('Treatment URLs file'),
                'title'   => 'allow_url_fopen',
                'content' => HelperApi::getIni('allow_url_fopen'),
            ),
            array(
                'label'   => $this->_('SMTP support'),
                'title'   => 'SMTP',
                'content' => HelperApi::getIni('SMTP') ?: HelperApi::getIni(0, false),
            ),
            array(
                'col'     => '1-1',
                'label'   => $this->_('Disabled functions'),
                'title'   => 'disable_functions',
                'content' => \implode(', ', \explode(',', HelperApi::getIni('disable_functions'))) ?: '-',
            ),
            array(
                'col'     => '1-1',
                'label'   => $this->_('Disabled classes'),
                'title'   => 'disable_classes',
                'content' => \implode(', ', \explode(',', HelperApi::getIni('disable_classes'))) ?: '-',
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
        <div class="group-content" {$title} {$id}>{$item['content']}</div>
    </div>
</div>
HTML;
        }

        return $content;
    }

    private function _($str)
    {
        return I18nApi::_($str);
    }
}
