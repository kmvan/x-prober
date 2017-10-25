<?php

namespace InnStudio\Prober\Database;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\I18n\Api as I18n;

class Database
{
    private $ID = 'database';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 500);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('Database'),
            'tinyTitle' => I18n::_('DB'),
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
        $sqlite3Version = \class_exists('\SQLite3') ? \SQLite3::version() : false;
        $sqlite3Version = $sqlite3Version ? $sqlite3Version['versionString'] : Helper::getIni(0, false);

        $items = array(
            array(
                'label'   => I18n::_('SQLite3'),
                'content' => $sqlite3Version,
            ),
        );

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';

            echo <<<EOT
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$id} {$title}>{$item['content']}</div>
    </div> 
</div>
EOT;
        }
    }
}
