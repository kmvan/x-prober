<?php

namespace InnStudio\Prober\PhpInfo;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class PhpInfo
{
    private $ID = 'phpInfo';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 300);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('PHP information'),
            'tinyTitle' => I18n::_('PHP'),
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

    private function getContent()
    {
        $items = array(
            array(
                'label'   => $this->_('PHP info detail'),
                'content' => Helper::getBtn($this->_('Click to check'), '?action=phpInfo'),
            ),
            array(
                'label'   => $this->_('Version'),
                'content' => PHP_VERSION,
            ),
            array(
                'label'   => $this->_('SAPI interface'),
                'content' => PHP_SAPI,
            ),
            array(
                'label'   => $this->_('Error reporting'),
                'title'   => 'error_reporting',
                'content' => Helper::getErrNameByCode(\ini_get('error_reporting')),
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
                'content' => Helper::getIni('display_errors'),
            ),
            array(
                'label'   => $this->_('Treatment URLs file'),
                'title'   => 'allow_url_fopen',
                'content' => Helper::getIni('allow_url_fopen'),
            ),
            array(
                'label'   => $this->_('SMTP support'),
                'title'   => 'SMTP',
                'content' => Helper::getIni('SMTP') ?: Helper::getIni(0, false),
            ),
            array(
                'col'     => '1-1',
                'label'   => $this->_('Disabled functions'),
                'title'   => 'disable_functions',
                'content' => \implode(', ', \explode(',', Helper::getIni('disable_functions'))) ?: '-',
            ),
        );

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
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

    private function _($str)
    {
        return I18n::_($str);
    }
}
