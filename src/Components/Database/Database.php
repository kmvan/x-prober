<?php

namespace InnStudio\Prober\Components\Database;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class Database
{
    private $ID = 'database';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 500);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('Database'),
            'tinyTitle' => I18nApi::_('DB'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        return <<<HTML
<div class="inn-row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getContent()
    {
        $sqlite3Version = \class_exists('\\SQLite3') ? \SQLite3::version() : false;

        $items = array(
            array(
                'label'   => I18nApi::_('SQLite3'),
                'content' => $sqlite3Version ? HelperApi::alert(
                    true,
                    $sqlite3Version['versionString']
                ) : HelperApi::alert(false),
            ),
            array(
                'title'   => 'sqlite_libversion',
                'label'   => I18nApi::_('SQLite'),
                'content' => HelperApi::alert(\function_exists('\\sqlite_libversion'), \function_exists('\\sqlite_libversion') ? \sqlite_libversion() : ''),
            ),
            array(
                'title'   => 'mysqli_get_client_version',
                'label'   => I18nApi::_('MySQLi client'),
                'content' => HelperApi::alert(\function_exists('\\mysqli_get_client_version'), \function_exists('\\mysqli_get_client_version') ? \mysqli_get_client_version() : ''),
            ),
            array(
                'label'   => I18nApi::_('Mongo'),
                'content' => HelperApi::alert(\class_exists('\\Mongo')),
            ),
            array(
                'label'   => I18nApi::_('MongoDB'),
                'content' => HelperApi::alert(\class_exists('\\MongoDB')),
            ),
            array(
                'label'   => I18nApi::_('PostgreSQL'),
                'content' => HelperApi::alert(\function_exists('\\pg_connect')),
            ),
            array(
                'label'   => I18nApi::_('Paradox'),
                'content' => HelperApi::alert(\function_exists('\\px_new')),
            ),
            array(
                'title'   => I18nApi::_('Microsoft SQL Server Driver for PHP'),
                'label'   => I18nApi::_('MS SQL'),
                'content' => HelperApi::alert(\function_exists('\\sqlsrv_server_info')),
            ),
            array(
                'label'   => I18nApi::_('File Pro'),
                'content' => HelperApi::alert(\function_exists('\\filepro')),
            ),
            array(
                'label'   => I18nApi::_('MaxDB client'),
                'content' => HelperApi::alert(\function_exists('\\maxdb_get_client_version'), \function_exists('\\maxdb_get_client_version') ? \maxdb_get_client_version() : ''),
            ),
            array(
                'label'   => I18nApi::_('MaxDB server'),
                'content' => HelperApi::alert(\function_exists('\\maxdb_get_server_version'), \function_exists('\\maxdb_get_server_version') ? \maxdb_get_server_version() : ''),
            ),
        );

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }
}
