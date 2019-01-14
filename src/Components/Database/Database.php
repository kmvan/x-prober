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
        $sqlite3Version = $sqlite3Version ? HelperApi::getIni(0, true) . ' ' . $sqlite3Version['versionString'] : HelperApi::getIni(0, false);

        $items = array(
            array(
                'label'   => I18nApi::_('SQLite3'),
                'content' => $sqlite3Version,
            ),
            array(
                'title'   => 'sqlite_libversion',
                'label'   => I18nApi::_('SQLite'),
                'content' => \function_exists('\\sqlite_libversion') ? HelperApi::getIni(0, true) . ' ' . \sqlite_libversion() : HelperApi::getIni(0, false),
            ),
            array(
                'title'   => 'mysqli_get_client_version',
                'label'   => I18nApi::_('MySQLi client'),
                'content' => \function_exists('\\mysqli_get_client_version') ? HelperApi::getIni(0, true) . ' ' . \mysqli_get_client_version() : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('Mongo'),
                'content' => \class_exists('\\Mongo') ? \MongoClient::VERSION : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('MongoDB'),
                'content' => \class_exists('\\MongoDB') ? HelperApi::getIni(0, true) : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('PostgreSQL'),
                'content' => \function_exists('\\pg_connect') ? HelperApi::getIni(0, true) : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('Paradox'),
                'content' => \function_exists('\\px_new') ? HelperApi::getIni(0, true) : HelperApi::getIni(0, false),
            ),
            array(
                'title'   => I18nApi::_('Microsoft SQL Server Driver for PHP'),
                'label'   => I18nApi::_('MS SQL'),
                'content' => \function_exists('\\sqlsrv_server_info') ? HelperApi::getIni(0, true) : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('File Pro'),
                'content' => \function_exists('\\filepro') ? HelperApi::getIni(0, true) : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('MaxDB client'),
                'content' => \function_exists('\\maxdb_get_client_version') ? \maxdb_get_client_version() : HelperApi::getIni(0, false),
            ),
            array(
                'label'   => I18nApi::_('MaxDB server'),
                'content' => \function_exists('\\maxdb_get_server_version') ? HelperApi::getIni(0, true) : HelperApi::getIni(0, false),
            ),
        );

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }
}
