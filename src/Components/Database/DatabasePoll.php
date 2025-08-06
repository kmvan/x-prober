<?php

namespace InnStudio\Prober\Components\Database;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use PDO;
use SQLite3;

final class DatabasePoll extends DatabaseConstants
{
    public function render()
    {
        if (UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }
        $sqlite3Version = class_exists('SQLite3') ? SQLite3::version() : false;

        return [
            $this->ID => [
                'sqlite3' => $sqlite3Version ? $sqlite3Version['versionString'] : false,
                'mysqliClientVersion' => \function_exists('mysqli_get_client_version') ? mysqli_get_client_version() : false,
                'mongo' => class_exists('Mongo'),
                'mongoDb' => class_exists('MongoDB'),
                'postgreSql' => \function_exists('pg_connect'),
                'paradox' => \function_exists('px_new'),
                'msSql' => \function_exists('sqlsrv_server_info'),
                'pdo' => class_exists('PDO') ? implode(',', PDO::getAvailableDrivers()) : false,
            ],
        ];
    }
}
