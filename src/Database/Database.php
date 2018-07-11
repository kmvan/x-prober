<?php

namespace InnStudio\Prober\Database;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
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
        $sqlite3Version = \class_exists('\\SQLite3') ? \SQLite3::version() : false;
        $sqlite3Version = $sqlite3Version ? Helper::getIni(0, true) . ' ' . $sqlite3Version['versionString'] : Helper::getIni(0, false);

        $items = array(
            array(
                'label'   => I18n::_('SQLite3'),
                'content' => $sqlite3Version,
            ),
            array(
                'title'   => 'sqlite_libversion',
                'label'   => I18n::_('SQLite'),
                'content' => \function_exists('\\sqlite_libversion') ? Helper::getIni(0, true) . ' ' . \sqlite_libversion() : Helper::getIni(0, false),
            ),
            array(
                'title'   => 'mysqli_get_client_version',
                'label'   => I18n::_('MySQLi client'),
                'content' => \function_exists('\\mysqli_get_client_version') ? Helper::getIni(0, true) . ' ' . \mysqli_get_client_version() : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('Mongo'),
                'content' => \class_exists('\\Mongo') ? \MongoClient::VERSION : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('MongoDB'),
                'content' => \class_exists('\\MongoDB') ? Helper::getIni(0, true) : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('PostgreSQL'),
                'content' => \function_exists('\\pg_connect') ? Helper::getIni(0, true) : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('Paradox'),
                'content' => \function_exists('\\px_new') ? Helper::getIni(0, true) : Helper::getIni(0, false),
            ),
            array(
                'title'   => I18n::_('Microsoft SQL Server Driver for PHP'),
                'label'   => I18n::_('MS SQL'),
                'content' => \function_exists('\\sqlsrv_server_info') ? Helper::getIni(0, true) : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('File Pro'),
                'content' => \function_exists('\\filepro') ? Helper::getIni(0, true) : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('MaxDB client'),
                'content' => \function_exists('\\maxdb_get_client_version') ? \maxdb_get_client_version() : Helper::getIni(0, false),
            ),
            array(
                'label'   => I18n::_('MaxDB server'),
                'content' => \function_exists('\\maxdb_get_server_version') ? Helper::getIni(0, true) : Helper::getIni(0, false),
            ),
        );

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';

            echo <<<HTML
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$id} {$title}>{$item['content']}</div>
    </div> 
</div>
HTML;
        }
    }
}
