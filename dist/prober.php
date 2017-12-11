<?php
namespace InnStudio\Prober\PreDefine;
\define('TIMER', \microtime(true));
\define('DEBUG', false);
\define('LANG', 'eyJodHRwczpcL1wvZ2l0aHViLmNvbVwva212YW5cL3gtcHJvYmVyIjp7InpoLUNOIjoiaHR0cDpcL1wvZ2l0aHViLmNvbVwva212YW5cL3gtcHJvYmVyIn0sIklOTiBTVFVESU8iOnsiemgtQ04iOiJJTk4gU1RVRElPIn0sImh0dHBzOlwvXC9pbm4tc3R1ZGlvLmNvbVwvcHJvYmVyIjp7InpoLUNOIjoiaHR0cHM6XC9cL2lubi1zdHVkaW8uY29tXC9wcm9iZXIifSwiWCBQcm9iZXIiOnsiemgtQ04iOiJYIFx1NjNhMlx1OTQ4OCJ9LCJGb3VuZCB1cGRhdGUhIHtBUFBfTkFNRX0gaGFzIG5ldyB2ZXJzaW9uIHZ7QVBQX05FV19WRVJTSU9OfSI6eyJ6aC1DTiI6Ilx1NTNkMVx1NzNiMFx1NjZmNFx1NjViMFx1ZmYwMXtBUFBfTkFNRX0gXHU2NzA5XHU2NWIwXHU3MjQ4XHU2NzJjXHVmZjFhdntBUFBfTkVXX1ZFUlNJT059In0sIlNlcnZlciBzdGF0dXMiOnsiemgtQ04iOiJcdTY3MGRcdTUyYTFcdTU2NjhcdTcyYjZcdTYwMDEifSwiU3lzdGVtIGxvYWQiOnsiemgtQ04iOiJcdTdjZmJcdTdlZGZcdThkMWZcdThmN2QifSwiTm90IHN1cHBvcnQgb24gV2luZG93cyI6eyJ6aC1DTiI6IldpbmRvd3MgXHU3Y2ZiXHU3ZWRmXHU1YzFhXHU2NzJhXHU2NTJmXHU2MzAxXHU4YmU1XHU1MjlmXHU4MGZkIn0sIkNQVSB1c2FnZSI6eyJ6aC1DTiI6IkNQVSBcdTRmN2ZcdTc1MjhcdTczODcifSwiUmVhbCBtZW1vcnkgdXNhZ2UiOnsiemgtQ04iOiJcdTc3MWZcdTViOWVcdTUxODVcdTViNThcdTRmN2ZcdTc1MjgifSwiUmVhbCBzd2FwIHVzYWdlIjp7InpoLUNOIjoiU1dBUCBcdTRmN2ZcdTc1MjgifSwiTmV0d29yayBzdGF0cyI6eyJ6aC1DTiI6Ilx1NmQ0MVx1OTFjZlx1N2VkZlx1OGJhMSJ9LCJTZXJ2ZXIgaW5mb3JtYXRpb24iOnsiemgtQ04iOiJcdTY3MGRcdTUyYTFcdTU2NjhcdTRmZTFcdTYwNmYifSwiU2VydmVyIHRpbWUiOnsiemgtQ04iOiJcdTY3MGRcdTUyYTFcdTU2NjhcdTY1ZjZcdTk1ZjQifSwiU2VydmVyIHVwdGltZSI6eyJ6aC1DTiI6Ilx1NjMwMVx1N2VlZFx1OGZkMFx1ODg0Y1x1NjVmNlx1OTVmNCJ9LCJTZXJ2ZXIgSVAiOnsiemgtQ04iOiJcdTY3MGRcdTUyYTFcdTU2NjggSVAifSwiU2VydmVyIG5hbWUiOnsiemgtQ04iOiJcdTY3MGRcdTUyYTFcdTU2NjhcdTU0MGQifSwiU2VydmVyIHNvZnR3YXJlIjp7InpoLUNOIjoiXHU2NzBkXHU1MmExXHU1NjY4XHU4ZjZmXHU0ZWY2In0sIkNQVSBtb2RlbCI6eyJ6aC1DTiI6IkNQVSBcdTU3OGJcdTUzZjcifSwiU2VydmVyIE9TIjp7InpoLUNOIjoiXHU2NzBkXHU1MmExXHU1NjY4XHU3Y2ZiXHU3ZWRmIn0sIlNjcmlwdCBwYXRoIjp7InpoLUNOIjoiXHU4MTFhXHU2NzJjXHU4ZGVmXHU1Zjg0In0sIkRpc2sgdXNhZ2UiOnsiemgtQ04iOiJcdTc4YzFcdTc2ZDhcdTRmN2ZcdTc1MjgifSwiUEhQIHZlcnNpb24iOnsiemgtQ04iOiJQSFAgXHU3MjQ4XHU2NzJjIn0sIlBIUCBpbmZvcm1hdGlvbiI6eyJ6aC1DTiI6IlBIUCBcdTRmZTFcdTYwNmYifSwiUEhQIGluZm8gZGV0YWlsIjp7InpoLUNOIjoiUEhQIFx1OGJlNlx1N2VjNlx1NGZlMVx1NjA2ZiJ9LCJDbGljayB0byBjaGVjayI6eyJ6aC1DTiI6Ilx1NzBiOVx1NTFmYlx1NjdlNVx1NzcwYiJ9LCJWZXJzaW9uIjp7InpoLUNOIjoiXHU3MjQ4XHU2NzJjIn0sIlNBUEkgaW50ZXJmYWNlIjp7InpoLUNOIjoiU0FQSSBcdTYzYTVcdTUzZTMifSwiRXJyb3IgcmVwb3J0aW5nIjp7InpoLUNOIjoiXHU5NTE5XHU4YmVmXHU2MmE1XHU1NDRhIn0sIk1heCBtZW1vcnkgbGltaXQiOnsiemgtQ04iOiJcdThmZDBcdTg4NGNcdTUxODVcdTViNThcdTk2NTBcdTUyMzYifSwiTWF4IFBPU1Qgc2l6ZSI6eyJ6aC1DTiI6IlBPU1QgXHU2M2QwXHU0ZWE0XHU5NjUwXHU1MjM2In0sIk1heCB1cGxvYWQgc2l6ZSI6eyJ6aC1DTiI6Ilx1NGUwYVx1NGYyMFx1NjU4N1x1NGVmNlx1OTY1MFx1NTIzNiJ9LCJNYXggaW5wdXQgdmFyaWFibGVzIjp7InpoLUNOIjoiXHU2M2QwXHU0ZWE0XHU4ODY4XHU1MzU1XHU5NjUwXHU1MjM2In0sIk1heCBleGVjdXRpb24gdGltZSI6eyJ6aC1DTiI6Ilx1OGZkMFx1ODg0Y1x1OGQ4NVx1NjVmNlx1NzlkMlx1NjU3MCJ9LCJUaW1lb3V0IGZvciBzb2NrZXQiOnsiemgtQ04iOiJTb2NrZXQgXHU4ZDg1XHU2NWY2XHU3OWQyXHU2NTcwIn0sIkRpc3BsYXkgZXJyb3JzIjp7InpoLUNOIjoiXHU2NjNlXHU3OTNhXHU5NTE5XHU4YmVmIn0sIlRyZWF0bWVudCBVUkxzIGZpbGUiOnsiemgtQ04iOiJcdTY1ODdcdTRlZjZcdThmZGNcdTdhZWZcdTYyNTNcdTVmMDAifSwiU01UUCBzdXBwb3J0Ijp7InpoLUNOIjoiU01UUCBcdTY1MmZcdTYzMDEifSwiRGlzYWJsZWQgZnVuY3Rpb25zIjp7InpoLUNOIjoiXHU3OTgxXHU3NTI4XHU3Njg0XHU1MWZkXHU2NTcwIn0sIlBIUCBleHRlbnNpb25zIjp7InpoLUNOIjoiUEhQIFx1NjI2OVx1NWM1NSJ9LCIlcyBleHRlbnNpb24iOnsiemgtQ04iOiIlcyBcdTYyNjlcdTVjNTUifSwiJXMgZW5hYmxlZCI6eyJ6aC1DTiI6IiVzIFx1NTQyZlx1NzUyOCJ9LCJEYXRhYmFzZSI6eyJ6aC1DTiI6Ilx1NjU3MFx1NjM2ZVx1NWU5MyJ9LCJTZXJ2ZXIgQmVuY2htYXJrIjp7InpoLUNOIjoiXHU2NzBkXHU1MmExXHU1NjY4XHU2MDI3XHU4MGZkXHU4ZGQxXHU1MjA2In0sIkJlbmNobWFyayI6eyJ6aC1DTiI6Ilx1OGRkMVx1NTIwNiJ9LCJcdWQ4M2RcdWRjYTEgSGlnaHQgaXMgYmV0dGVyLiI6eyJ6aC1DTiI6Ilx1ZDgzZFx1ZGNhMSBcdTUyMDZcdTY1NzBcdThkOGFcdTlhZDhcdThkOGFcdTU5N2RcdTMwMDIifSwiRXJyb3IsIGNsaWNrIHRvIHJldHJ5LiI6eyJ6aC1DTiI6Ilx1OTUxOVx1OGJlZlx1ZmYwY1x1NzBiOVx1NTFmYlx1OTFjZFx1OGJkNSJ9LCJMb2FkaW5nLi4uIjp7InpoLUNOIjoiXHU1MmEwXHU4ZjdkXHU0ZTJkXHUyMDI2XHUyMDI2In0sIk15IHNlcnZlciI6eyJ6aC1DTiI6Ilx1NjIxMVx1NzY4NFx1NjcwZFx1NTJhMVx1NTY2OCJ9LCJDbGljayB0byB0ZXN0Ijp7InpoLUNOIjoiXHU3MGI5XHU1MWZiXHU2ZDRiXHU4YmQ1In0sIkFsaXl1blwvRUNTXC9QSFA3Ijp7InpoLUNOIjoiXHU5NjNmXHU5MWNjXHU0ZTkxXC9FQ1NcL1BIUDcifSwiVnVsdHJcL1BIUDciOnsiemgtQ04iOiJWdWx0clwvUEhQNyJ9LCJBbnlOb2RlXC9IRERcL1BIUDciOnsiemgtQ04iOiJBbnlOb2RlXC9IRERcL1BIUDcifSwiQWxpeXVuXC9JbnRcL1BIUDUiOnsiemgtQ04iOiJcdTk2M2ZcdTkxY2NcdTRlOTFcL1x1NTZmZFx1OTY0NVx1NzI0OFwvUEhQNSJ9LCJUZW5jZW50XC9QSFA3Ijp7InpoLUNOIjoiXHU4MTdlXHU4YmFmXHU0ZTkxXC9QSFA3In0sIk15IGluZm9ybWF0aW9uIjp7InpoLUNOIjoiXHU2MjExXHU3Njg0XHU0ZmUxXHU2MDZmIn0sIk15IElQIjp7InpoLUNOIjoiXHU2MjExXHU3Njg0IElQIn0sIk15IFVBIjp7InpoLUNOIjoiXHU2MjExXHU3Njg0IFVBIn0sIkdlbmVyYXRvciAlcyI6eyJ6aC1DTiI6Ilx1OGJlNVx1OTg3NVx1OTc2Mlx1NzUzMSAlcyBcdTc1MWZcdTYyMTAifSwiQXV0aG9yICVzIjp7InpoLUNOIjoiXHU0ZjVjXHU4MDA1XHU0ZTNhICVzIn19');
namespace InnStudio\Prober\Awesome; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\I18n\Api as I18n; class Awesome { private $ID = 'awesome'; private $ZH_CN_URL = 'https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css'; private $DEFAULT_URL = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'; public function __construct() { } public function filter() { ?>
<link rel="stylesheet" href="<?php echo $this->getUrl(); ?>">
<?php
} private function getUrl() { switch (I18n::getClientLang()) { case 'zh-CN': return $this->ZH_CN_URL; } return $this->DEFAULT_URL; } }
namespace InnStudio\Prober\Benchmark; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; class Benchmark { public function __construct() { Events::on('init', array($this, 'filter')); } public function filter() { if ( ! Helper::isAction('benchmark')) { return; } $this->display(); } private function getPointsByTime($time) { \error_log($time); return \pow(10, 3) - (int) ($time * \pow(10, 3)); } private function getHashPoints() { $data = 'inn-studio.com'; $hash = array('md5', 'sha512', 'sha256', 'crc32'); $count = \pow(10, 5); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { foreach ($hash as $v) { \hash($v, $data); } } return $this->getPointsByTime(\microtime(true) - $start); } private function getIntLoopPoints() { $j = 0; $count = \pow(10, 7); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { ++$j; } return $this->getPointsByTime(\microtime(true) - $start); } private function getFloatLoopPoints() { $j = 1 / 3; $count = \pow(10, 7); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { ++$j; } return $this->getPointsByTime(\microtime(true) - $start); } private function getIoLoopPoints() { $tmpDir = \sys_get_temp_dir(); if ( ! \is_writable($tmpDir)) { return 0; } $count = \pow(10, 4); $start = \microtime(true); for ($i = 0; $i < $count; ++$i) { $filePath = "{$tmpDir}/innStudioIoBenchmark-{$i}"; \file_put_contents($filePath, $filePath); \unlink($filePath); } return $this->getPointsByTime(\microtime(true) - $start); } private function getPoints() { return array( 'hash' => $this->getHashPoints(), 'intLoop' => $this->getIntLoopPoints(), 'floatLoop' => $this->getFloatLoopPoints(), 'ioLoop' => $this->getIoLoopPoints(), ); } private function display() { \set_time_limit(0); Helper::dieJson(array( 'code' => 0, 'data' => array( 'points' => $this->getPoints(), ), )); } }
namespace InnStudio\Prober\Config; class Api { public static $APP_VERSION = '1.3.0'; public static $APP_NAME = 'X Prober'; public static $APP_URL = 'https://github.com/kmvan/x-prober'; public static $AUTHOR_URL = 'https://inn-studio.com/prober'; public static $AUTHOR_NAME = 'INN STUDIO'; public static $CHANGELOG_URL = 'https://raw.githubusercontent.com/kmvan/x-prober/master/CHANGELOG.md'; }
namespace InnStudio\Prober\Database; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class Database { private $ID = 'database'; public function __construct() { Events::patch('mods', array($this, 'filter'), 500); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('Database'), 'tinyTitle' => I18n::_('DB'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} private function getContent() { $sqlite3Version = \class_exists('\SQLite3') ? \SQLite3::version() : false; $sqlite3Version = $sqlite3Version ? Helper::getIni(0, true) . ' ' . $sqlite3Version['versionString'] : Helper::getIni(0, false); $items = array( array( 'label' => I18n::_('SQLite3'), 'content' => $sqlite3Version, ), array( 'title' => 'sqlite_libversion', 'label' => I18n::_('SQLite'), 'content' => \function_exists('\sqlite_libversion') ? Helper::getIni(0, true) . ' ' . \sqlite_libversion() : Helper::getIni(0, false), ), array( 'title' => 'mysqli_get_client_version', 'label' => I18n::_('MySQLi client'), 'content' => Helper::getIni(0, true) . ' ' . \mysqli_get_client_version(), ), array( 'label' => I18n::_('Mongo'), 'content' => \class_exists('\Mongo') ? \MongoClient::VERSION : Helper::getIni(0, false), ), array( 'label' => I18n::_('MongoDB'), 'content' => \class_exists('\MongoDB') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18n::_('PostgreSQL'), 'content' => \function_exists('\pg_connect') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18n::_('Paradox'), 'content' => \function_exists('\px_new') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'title' => I18n::_('Microsoft SQL Server Driver for PHP'), 'label' => I18n::_('MS SQL'), 'content' => \function_exists('\sqlsrv_server_info') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18n::_('File Pro'), 'content' => \function_exists('\filepro') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), array( 'label' => I18n::_('MaxDB client'), 'content' => \function_exists('\maxdb_get_client_version') ? \maxdb_get_client_version() : Helper::getIni(0, false), ), array( 'label' => I18n::_('MaxDB server'), 'content' => \function_exists('\maxdb_get_server_version') ? Helper::getIni(0, true) : Helper::getIni(0, false), ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; echo <<<EOT
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$id} {$title}>{$item['content']}</div>
</div>
</div>
EOT;
} } }
namespace InnStudio\Prober\Entry; use InnStudio\Prober\Config\Api as Config; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class Entry { public function __construct() { Events::emit('init'); if (DEBUG === true) { $this->display(); } else { \ob_start(); $this->display(); $content = \ob_get_contents(); \ob_end_clean(); echo Helper::htmlMinify($content); } } private function displayContent() { $mods = Events::apply('mods', array()); if ( ! $mods) { return; } foreach ($mods as $id => $mod) { ?>
<fieldset id="<?php echo $id; ?>">
<legend >
<span class="long-title"><?php echo $mod['title']; ?></span>
<span class="tiny-title"><?php echo $mod['tinyTitle']; ?></span>
</legend>
<?php \call_user_func($mod['display']); ?>
</fieldset>
<?php
} } private function display() { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title><?php echo I18n::_(Config::$APP_NAME); ?> v<?php echo Config::$APP_VERSION; ?></title>
<?php Events::emit('style'); ?>
</head>
<body>
<div class="poi-container">
<h1><a href="<?php echo I18n::_(Config::$APP_URL); ?>" target="_blank"><?php echo I18n::_(Config::$APP_NAME); ?> v<?php echo Config::$APP_VERSION; ?></a></h1>
<?php $this->displayContent(); ?>
</div>
<?php Events::emit('footer'); ?>
<?php Events::emit('script'); ?>
</body>
</html>
<?php
} }
namespace InnStudio\Prober\Events; class Api { private static $filters = array(); private static $actions = array(); private static $PRIORITY_ID = 'priority'; private static $CALLBACK_ID = 'callback'; public static function on($name, $callback, $priority = 10) { if ( ! isset(self::$actions[$name])) { self::$actions[$name] = array(); } self::$actions[$name][] = array( self::$PRIORITY_ID => $priority, self::$CALLBACK_ID => $callback, ); } public static function emit() { $args = \func_get_args(); $name = $args[0]; unset($args[0]); $actions = isset(self::$actions[$name]) ? self::$actions[$name] : false; if ( ! $actions) { return; } $sortArr = array(); foreach ($actions as $k => $action) { $sortArr[$k] = $action[self::$PRIORITY_ID]; } \array_multisort($sortArr, $actions); foreach ($actions as $action) { \call_user_func_array($action[self::$CALLBACK_ID], $args); } } public static function patch($name, $callback, $priority = 10) { if ( ! isset(self::$filters[$name])) { self::$filters[$name] = array(); } self::$filters[$name][] = array( self::$PRIORITY_ID => $priority, self::$CALLBACK_ID => $callback, ); } public static function apply() { $args = \func_get_args(); $name = $args[0]; $return = $args[1]; unset($args[0],$args[1]); $filters = isset(self::$filters[$name]) ? self::$filters[$name] : false; if ( ! $filters) { return $return; } $sortArr = array(); foreach ($filters as $k => $filter) { $sortArr[$k] = $filter[self::$PRIORITY_ID]; } \array_multisort($sortArr, $filters); foreach ($filters as $filter) { $return = \call_user_func_array($filter[self::$CALLBACK_ID], array($return, $args)); } return $return; } }
namespace InnStudio\Prober\Fetch; use InnStudio\Prober\Helper\Api as Helper; class Fetch { public function __construct() { if (Helper::isAction('fetch')) { $this->outputItems(); } } private function getServerUtcTime() { return \gmdate('Y/m/d H:i:s'); } private function getServerLocalTime() { return \date('Y/m/d H:i:s'); } private function getItems() { return array( 'utcTime' => $this->getServerUtcTime(), 'serverInfo' => array( 'time' => Helper::getServerTime(), 'upTime' => Helper::getServerUpTime(), ), 'cpuUsage' => Helper::getHumanCpuUsage(), 'sysLoadAvg' => Helper::getSysLoadAvg(), 'memTotal' => Helper::getMemoryUsage('MemTotal'), 'memRealUsage' => array( 'percent' => Helper::getMemoryUsage('MemRealUsage') ? \sprintf('%01.2f', Helper::getMemoryUsage('MemRealUsage') / Helper::getMemoryUsage('MemTotal') * 100) : 0, 'number' => Helper::getHumamMemUsage('MemRealUsage') . ' / ' . Helper::getHumamMemUsage('MemTotal'), 'current' => Helper::getMemoryUsage('MemRealUsage'), ), 'swapRealUsage' => array( 'percent' => Helper::getMemoryUsage('SwapRealUsage') ? \sprintf('%01.2f', Helper::getMemoryUsage('SwapRealUsage') / Helper::getMemoryUsage('SwapTotal') * 100) : 0, 'number' => Helper::getHumamMemUsage('SwapRealUsage') . ' / ' . Helper::getHumamMemUsage('SwapTotal'), 'current' => Helper::getMemoryUsage('SwapRealUsage'), ), 'networkStats' => Helper::getNetworkStats(), ); } private function outputItems() { Helper::dieJson(array( 'code' => 0, 'data' => $this->getItems(), )); } }
namespace InnStudio\Prober\Footer; use InnStudio\Prober\Config\Api as Config; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class Footer { private $ID = 'footer'; public function __construct() { Events::on('footer', array($this, 'filter')); Events::on('style', array($this, 'filterStyle')); } public function filter() { $timer = (\microtime(true) - TIMER) * 1000; ?>
<a href="<?php echo I18n::_(Config::$APP_URL); ?>" target="_blank"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"></a>
<div class="poi-container">
<div class="footer">
<?php echo \sprintf(I18n::_('Generator %s'), '<a href="' . I18n::_(Config::$APP_URL) . '" target="_blank">' . I18n::_(Config::$APP_NAME) . '</a>'); ?>
/
<?php echo \sprintf(I18n::_('Author %s'), '<a href="' . I18n::_(Config::$AUTHOR_URL) . '" target="_blank">' . I18n::_(Config::$AUTHOR_NAME) . '</a>'); ?>
/
<?php echo Helper::formatBytes(\memory_get_usage()); ?>
/
<?php echo \sprintf('%01.2f', $timer); ?>ms
</div>
</div>
<?php
} public function filterStyle() { ?>
<style>
.footer{
text-align: center;
margin: 2rem auto 5rem;
padding: .5rem 1rem;
}
@media (min-width: 768px) {
.footer{
background: #333;
color: #ccc;
width: 60%;
border-radius: 10rem;
}
.footer a{
color: #fff;
}
}
.footer a:hover{
text-decoration: underline;
}
</style>
<?php
} }
namespace InnStudio\Prober\Helper; use InnStudio\Prober\I18n\Api as I18n; class Api { public static function dieJson($data) { \header('Content-Type: application/json'); die(\json_encode($data)); } public static function isAction($action) { return \filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) === $action; } public static function getWinCpuUsage() { $cpus = array(); if (\class_exists('\COM')) { $wmi = new \COM('Winmgmts://'); $server = $wmi->execquery('SELECT LoadPercentage FROM Win32_Processor'); $cpus = array(); foreach ($server as $cpu) { $total += (int) $cpu->loadpercentage; } $total = (int) $total / \count($server); $cpus['idle'] = 100 - $total; $cpus['user'] = $total; } else { \exec('wmic cpu get LoadPercentage', $p); if (isset($p[1])) { $percent = (int) $p[1]; $cpus['idle'] = 100 - $percent; $cpus['user'] = $percent; } } return $cpus; } public static function getNetworkStats() { $filePath = '/proc/net/dev'; if ( ! \is_readable($filePath)) { return I18n::_('Unavailable'); } static $eths = null; if (null !== $eths) { return $eths; } $lines = \file($filePath); unset($lines[0], $lines[1]); $eths = array(); foreach ($lines as $line) { $line = \preg_replace('/\s+/', ' ', \trim($line)); $lineArr = \explode(':', $line); $numberArr = \explode(' ', \trim($lineArr[1])); $eths[$lineArr[0]] = array( 'rx' => (int) $numberArr[0], 'tx' => (int) $numberArr[8], ); } return $eths; } public static function getBtn($tx, $url) { return '<a href="' . $url . '" target="_blank" class="btn">' . $tx . '</a>'; } public static function getDiskTotalSpace($human = false) { static $space = null; if (null === $space) { $space = \disk_total_space('/'); } if ( ! $space) { return 0; } if (true === $human) { return self::formatBytes($space); } return $space; } public static function getDiskFreeSpace($human = false) { static $space = null; if (null === $space) { $space = \disk_free_space('/'); } if ( ! $space) { return 0; } if (true === $human) { return self::formatBytes($space); } return $space; } public static function getCpuModel() { $filePath = '/proc/cpuinfo'; if ( ! \is_readable($filePath)) { return I18n::_('Unavailable'); } $content = \file_get_contents($filePath); $cores = \substr_count($content, 'cache size'); $lines = \explode("\n", $content); $modelName = \explode(':', $lines[4]); $modelName = \trim($modelName[1]); $cacheSize = \explode(':', $lines[8]); $cacheSize = \trim($cacheSize[1]); return "{$cores} x {$modelName} / " . \sprintf(I18n::_('%s cache'), $cacheSize); } public static function getServerTime() { return \date('Y-m-d H:i:s'); } public static function getServerUpTime() { $filePath = '/proc/uptime'; if ( ! \is_readable($filePath)) { return I18n::_('Unavailable'); } $str = \file_get_contents($filePath); $num = (float) $str; $secs = \fmod($num, 60); $num = (int) ($num / 60); $mins = $num % 60; $num = (int) ($num / 60); $hours = $num % 24; $num = (int) ($num / 24); $days = $num; return \sprintf( I18n::_('%1$dd %2$dh %3$dm %4$ds'), $days, $hours, $mins, $secs ); } public static function getErrNameByCode($code) { switch ($code) { case E_ERROR: return 'E_ERROR'; case E_WARNING: return 'E_WARNING'; case E_PARSE: return 'E_PARSE'; case E_NOTICE: return 'E_NOTICE'; case E_CORE_ERROR: return 'E_CORE_ERROR'; case E_CORE_WARNING: return 'E_CORE_WARNING'; case E_COMPILE_ERROR: return 'E_COMPILE_ERROR'; case E_COMPILE_WARNING: return 'E_COMPILE_WARNING'; case E_USER_ERROR: return 'E_USER_ERROR'; case E_USER_WARNING: return 'E_USER_WARNING'; case E_USER_NOTICE: return 'E_USER_NOTICE'; case E_STRICT: return 'E_STRICT'; case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR'; case E_DEPRECATED: return 'E_DEPRECATED'; case E_USER_DEPRECATED: return 'E_USER_DEPRECATED'; case E_ALL: return 'E_ALL'; } return $code; } public static function getIni($id, $forceSet = null) { if (true === $forceSet) { $ini = 1; } elseif (false === $forceSet) { $ini = 0; } else { $ini = \ini_get($id); } if ( ! \is_numeric($ini) && '' !== (string) $ini) { return $ini; } if (1 === (int) $ini) { return '<span class="ini-ok">&check;</span>'; } elseif (0 === (int) $ini) { return '<span class="ini-error">&times;</span>'; } return $ini; } public static function isWin() { return PHP_OS === 'WINNT'; } public static function htmlMinify($buffer) { \preg_match_all('#\<textarea.*\>.*\<\/textarea\>#Uis', $buffer, $foundTxt); \preg_match_all('#\<pre.*\>.*\<\/pre\>#Uis', $buffer, $foundPre); $textareas = array(); foreach (\array_keys($foundTxt[0]) as $item) { $textareas[] = '<textarea>' . $item . '</textarea>'; } $pres = array(); foreach (\array_keys($foundPre[0]) as $item) { $pres[] = '<pre>' . $item . '</pre>'; } $buffer = \str_replace($foundTxt[0], $textareas, $buffer); $buffer = \str_replace($foundPre[0], $pres, $buffer); $search = array( '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', ); $replace = array( '>', '<', '\\1', ); $buffer = \preg_replace($search, $replace, $buffer); $textareas = array(); foreach (\array_keys($foundTxt[0]) as $item) { $textareas[] = '<textarea>' . $item . '</textarea>'; } $pres = array(); foreach (\array_keys($foundPre[0]) as $item) { $pres[] = '<pre>' . $item . '</pre>'; } $buffer = \str_replace($textareas, $foundTxt[0], $buffer); $buffer = \str_replace($pres, $foundPre[0], $buffer); return $buffer; } public static function getClientIp() { $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'); foreach ($keys as $key) { if ( ! isset($_SERVER[$key])) { continue; } $ip = \array_filter(\explode(',', $_SERVER[$key])); $ip = \filter_var(\end($ip), FILTER_VALIDATE_IP); if ($ip) { return $ip; } } return ''; } public static function getCpuUsage() { static $cpu = null; if (null !== $cpu) { return $cpu; } if (self::isWin()) { $cpu = self::getWinCpuUsage(); return $cpu; } $filePath = ('/proc/stat'); if ( ! \is_readable($filePath)) { $cpu = array(); return $cpu; } $stat1 = \file($filePath); \sleep(1); $stat2 = \file($filePath); $info1 = \explode(' ', \preg_replace('!cpu +!', '', $stat1[0])); $info2 = \explode(' ', \preg_replace('!cpu +!', '', $stat2[0])); $dif = array(); $dif['user'] = $info2[0] - $info1[0]; $dif['nice'] = $info2[1] - $info1[1]; $dif['sys'] = $info2[2] - $info1[2]; $dif['idle'] = $info2[3] - $info1[3]; $total = \array_sum($dif); $cpu = array(); foreach ($dif as $x => $y) { $cpu[$x] = \round($y / $total * 100, 1); } return $cpu; } public static function getHumanCpuUsageDetail() { $cpu = self::getCpuUsage(); if ( ! $cpu) { return ''; } $html = ''; foreach ($cpu as $k => $v) { $html .= '<span class="small-group"><span class="item-name">' . $k . '</span> <span class="item-value">' . $v . '</span></span>'; } return $html; } public static function getHumanCpuUsage() { $cpu = self::getCpuUsage(); return $cpu ?: array(); } public static function getSysLoadAvg() { if (self::isWin()) { return I18n::_('Not support on Windows'); } $avg = \sys_getloadavg(); $avg[0] = '<span class="small-group"><span class="item-name">' . I18n::_('1 min:') . "</span> {$avg[0]}</span>"; $avg[1] = '<span class="small-group"><span class="item-name">' . I18n::_('5 min:') . "</span> {$avg[1]}</span>"; $avg[2] = '<span class="small-group"><span class="item-name">' . I18n::_('15 min:') . "</span> {$avg[2]}</span>"; return \implode('', $avg); } public static function getMemoryUsage($key) { $key = \ucfirst($key); if (self::isWin()) { return 0; } static $memInfo = null; if (null === $memInfo) { $memInfoFile = '/proc/meminfo'; if ( ! \is_readable($memInfoFile)) { $memInfo = 0; return 0; } $memInfo = \file_get_contents($memInfoFile); $memInfo = \str_replace(array( ' kB', '  ', ), '', $memInfo); $lines = array(); foreach (\explode("\n", $memInfo) as $line) { if ( ! $line) { continue; } $line = \explode(':', $line); $lines[$line[0]] = (int) $line[1]; } $memInfo = $lines; } switch ($key) { case 'MemRealUsage': $memAvailable = 0; if (isset($memInfo['MemAvailable'])) { $memAvailable = $memInfo['MemAvailable']; } elseif (isset($memInfo['MemFree'])) { $memAvailable = $memInfo['MemFree']; } return $memInfo['MemTotal'] - $memAvailable; case 'SwapRealUsage': if ( ! isset($memInfo['SwapTotal']) || ! isset($memInfo['SwapFree']) || ! isset($memInfo['SwapCached'])) { return 0; } return $memInfo['SwapTotal'] - $memInfo['SwapFree'] - $memInfo['SwapCached']; } return isset($memInfo[$key]) ? (int) $memInfo[$key] : 0; } public static function formatBytes($bytes, $precision = 2) { if ( ! $bytes) { return 0; } $base = \log($bytes, 1024); $suffixes = array('', ' K', ' M', ' G', ' T'); return \round(\pow(1024, $base - \floor($base)), $precision) . $suffixes[\floor($base)]; } public static function getHumamMemUsage($key) { return self::formatBytes(self::getMemoryUsage($key) * 1024); } }
namespace InnStudio\Prober\I18n; class Api { public static function _($str) { static $preDefineLang = null; if (null === $preDefineLang) { $preDefineLang = \json_decode(\base64_decode(LANG), true); } if ( ! isset($preDefineLang[$str])) { return $str; } $lang = $preDefineLang[$str]; $clientLang = self::getClientLang(); return isset($lang[$clientLang]) ? $lang[$clientLang] : $str; } public static function getClientLang() { static $cache = null; if (null !== $cache) { return $cache; } if ( ! isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) { $cache = ''; return $cache; } $client = \explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']); if (isset($client[0])) { $cache = $client[0]; } else { $cache = ''; } return $cache; } }
namespace InnStudio\Prober\MyInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class MyInfo { private $ID = 'myInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 900); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('My information'), 'tinyTitle' => I18n::_('Mine'), 'display' => array($this, 'display'), ); return $mods; } public function display() { echo $this->getContent(); } public function getContent() { $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''; return <<<EOT
<div class="form-group">
<div class="group-label">{$this->_('My IP')}</div>
<div class="group-content">{$this->getClientIp()}</div>
</div>
<div class="form-group">
<div class="group-label">{$this->_('My UA')}</div>
<div class="group-content">{$ua}</div>
</div>
EOT;
} private function getClientIp() { return Helper::getClientIp(); } private function _($str) { return I18n::_($str); } }
namespace InnStudio\Prober\Nav; use InnStudio\Prober\Events\Api as Events; class Nav { private $ID = 'nav'; public function __construct() { Events::on('script', array($this, 'filterScript')); Events::on('style', array($this, 'filterStyle')); } public function filterStyle() { ?>
<style>
.nav {
position: fixed;
bottom: 0;
background: #333;
padding: 0 1rem;
left: 0;
right: 0;
text-align: center;
z-index: 10;
}
.nav a{
display: inline-block;
color: #eee;
padding: .3rem .5rem;
border-left: 1px solid rgba(255,255,255,.05);
}
.nav a:first-child{
border: none;
}
.nav a:hover,
.nav a:focus,
.nav a:active{
background: #f8f8f8;
color: #333;
text-decoration: none;
}
.nav .long-title{
display: none;
}
.nav .tiny-title{
display: block;
}
@media (min-width: 579px) {
.nav .tiny-title{
display: none;
}
.nav .long-title{
display: block;
}
.nav a{
padding: .3rem 1rem;
}
}
</style>
<?php
} public function filterScript() { ?>
<script>
(function(){
var fieldsets = document.querySelectorAll('fieldset');
if (! fieldsets.length) {
return;
}
var nav = document.createElement('div');
nav.className = 'nav';
for(var i = 0; i < fieldsets.length; i++) {
var fieldset = fieldsets[i];
var a = document.createElement('a');
a.href = '#' + encodeURIComponent(fieldset.id);
a.innerHTML = fieldset.querySelector('legend').innerHTML;
nav.appendChild(a);
}
document.body.appendChild(nav);
})()
</script>
<?php
} }
namespace InnStudio\Prober\NetworkStats; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class NetworkStats { private $ID = 'networkStats'; public function __construct() { Helper::isWin() || Events::on('style', array($this, 'filterStyle')); Helper::isWin() || Events::patch('mods', array($this, 'filter'), 100); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('Network stats'), 'tinyTitle' => I18n::_('Net'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} public function filterStyle() { ?>
<style>
.network-stats-container > *{
float: left;
width: 50%;
text-align: center;
}
</style>
<?php
} private function getContent() { $items = array(); $stats = Helper::getNetworkStats(); if ( ! \is_array($stats)) { return '<div>' . Helper::getNetworkStats() . '</div>'; } foreach (Helper::getNetworkStats() as $ethName => $item) { $rxHuman = Helper::formatBytes($item['rx']); $txHuman = Helper::formatBytes($item['tx']); $items[] = array( 'label' => $ethName, 'content' => "<div class=\"network-stats-container\">
<div class=\"rx\">
<div><span id=\"network-{$ethName}-rx-total\">{$rxHuman}</span></div>
<div><span class=\"icon\">▼</span><span id=\"network-{$ethName}-rx-rate\">0</span><span class=\"second\">/s</span></div>
</div>
<div class=\"tx\">
<div><span id=\"network-{$ethName}-tx-total\">{$txHuman}</span></div>
<div><span class=\"icon\">▲</span><span id=\"network-{$ethName}-tx-rate\">0</span><span class=\"second\">/s</span></div>
</div>
</div>", ); } $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-1'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<EOT
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$title} {$id}>{$item['content']}</div>
</div>
</div>
EOT;
} return $content; } }
namespace InnStudio\Prober\PhpExtensionInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class PhpExtensionInfo { private $ID = 'phpExtensionInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 400); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('PHP extensions'), 'tinyTitle' => I18n::_('Ext'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} private function getContent() { $items = array( array( 'label' => \sprintf(I18n::_('%s extension'), 'Memcache'), 'content' => Helper::getIni(0, \extension_loaded('memcache') && \class_exists('\Memcache')), ), array( 'label' => \sprintf(I18n::_('%s extension'), 'Memcached'), 'content' => Helper::getIni(0, \extension_loaded('memcached') && \class_exists('\Memcached')), ), array( 'label' => \sprintf(I18n::_('%s extension'), 'Redis'), 'content' => Helper::getIni(0, \extension_loaded('redis') && \class_exists('\Redis')), ), array( 'label' => \sprintf(I18n::_('%s extension'), 'Opcache'), 'content' => Helper::getIni(0, \function_exists('\opcache_get_configuration')), ), array( 'label' => \sprintf(I18n::_('%s enabled'), 'Opcache'), 'content' => Helper::getIni(0, $this->isOpcEnabled()), ), array( 'label' => I18n::_('Zend Optimizer'), 'content' => Helper::getIni(0, \function_exists('zend_optimizer_version')), ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<EOT
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$title} {$id}>{$item['content']}</div>
</div>
</div>
EOT;
} return $content; } private function isOpcEnabled() { $isOpcEnabled = \function_exists('\opcache_get_configuration'); if ($isOpcEnabled) { $isOpcEnabled = \opcache_get_configuration(); $isOpcEnabled = isset($isOpcEnabled['directives']['opcache.enable']) && $isOpcEnabled['directives']['opcache.enable'] === true; } return $isOpcEnabled; } }
namespace InnStudio\Prober\PhpInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class PhpInfo { private $ID = 'phpInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 300); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('PHP information'), 'tinyTitle' => I18n::_('PHP'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} private function getContent() { $items = array( array( 'label' => $this->_('PHP info detail'), 'content' => '👆 ' . Helper::getBtn($this->_('Click to check'), '?action=phpInfo'), ), array( 'label' => $this->_('Version'), 'content' => PHP_VERSION, ), array( 'label' => $this->_('SAPI interface'), 'content' => PHP_SAPI, ), array( 'label' => $this->_('Error reporting'), 'title' => 'error_reporting', 'content' => Helper::getErrNameByCode(\ini_get('error_reporting')), ), array( 'label' => $this->_('Max memory limit'), 'title' => 'memory_limit', 'content' => \ini_get('memory_limit'), ), array( 'label' => $this->_('Max POST size'), 'title' => 'post_max_size', 'content' => \ini_get('post_max_size'), ), array( 'label' => $this->_('Max upload size'), 'title' => 'upload_max_filesize', 'content' => \ini_get('upload_max_filesize'), ), array( 'label' => $this->_('Max input variables'), 'title' => 'max_input_vars', 'content' => \ini_get('max_input_vars'), ), array( 'label' => $this->_('Max execution time'), 'title' => 'max_execution_time', 'content' => \ini_get('max_execution_time'), ), array( 'label' => $this->_('Timeout for socket'), 'title' => 'default_socket_timeout', 'content' => \ini_get('default_socket_timeout'), ), array( 'label' => $this->_('Display errors'), 'title' => 'display_errors', 'content' => Helper::getIni('display_errors'), ), array( 'label' => $this->_('Treatment URLs file'), 'title' => 'allow_url_fopen', 'content' => Helper::getIni('allow_url_fopen'), ), array( 'label' => $this->_('SMTP support'), 'title' => 'SMTP', 'content' => Helper::getIni('SMTP') ?: Helper::getIni(0, false), ), array( 'col' => '1-1', 'label' => $this->_('Disabled functions'), 'title' => 'disable_functions', 'content' => \implode(', ', \explode(',', Helper::getIni('disable_functions'))) ?: '-', ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; $content .= <<<EOT
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$title} {$id}>{$item['content']}</div>
</div>
</div>
EOT;
} return $content; } private function _($str) { return I18n::_($str); } }
namespace InnStudio\Prober\PhpInfoDetail; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; class PhpInfoDetail { public function __construct() { Events::on('init', array($this, 'filter')); } public function filter() { if (Helper::isAction('phpInfo')) { \phpinfo(); die; } } }
namespace InnStudio\Prober\Script; use InnStudio\Prober\Events\Api as Events; class Script { private $ID = 'script'; public function __construct() { Events::on('script', array($this, 'filter')); } public function filter() { ?>
<script>
(function () {
var xhr = new XMLHttpRequest();
xhr.onload = load;
var cache = {};
function addClassName(el,className){
if (el.classList){
el.classList.add(className);
} else {
el.className += ' ' + className;
}
}
function removeClassName(el, className){
if (el.classList){
el.classList.remove(className);
} else {
el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
}
}
function formatBytes(bytes, decimals) {
if (bytes == 0) {
return '0';
}
var k = 1024,
dm = decimals || 2,
sizes = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
i = Math.floor(Math.log(bytes) / Math.log(k));
return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
function I(el) {
if (cache[el]) {
return cache[el];
}
cache[el] = document.getElementById(el);
return cache[el];
}
function setColor(progress, percent) {
if (percent >= 80) {
addClassName(progress,'high');
removeClassName(progress,'medium');
removeClassName(progress,'medium-low');
} else if (percent >= 50) {
addClassName(progress,'medium');
removeClassName(progress,'high');
removeClassName(progress,'medium-low');
} else if (percent >= 30) {
addClassName(progress,'medium-low');
removeClassName(progress,'medium');
removeClassName(progress,'high');
} else {
removeClassName(progress,'high');
removeClassName(progress,'medium');
removeClassName(progress,'medium-low');
}
}
function request() {
xhr.open('get', '?action=fetch');
xhr.send();
}
function load() {
if (xhr.readyState !== 4) {
return;
}
if (xhr.status >= 200 && xhr.status < 400) {
var res = JSON.parse(xhr.responseText);
if (res && res.code === 0) {
var data = res.data;
fillCpuUsage(data);
fillSysLoadAvg(data);
fillMemRealUsage(data);
fillServerInfo(data);
fillNetworkStats(data);
}
} else {}
setTimeout(function () {
request();
}, 1000);
}
function fillCpuUsage(data) {
var progress = I('cpuUsageProgress');
var value = I('cpuUsageProgressValue');
var percent = 100 - Math.round(data.cpuUsage.idle);
var title = [];
for (var i in data.cpuUsage) {
title.push(i + ': ' + data.cpuUsage[i]);
}
progress.title = title.join(' / ');
value.style.width = percent + '%';
setColor(progress, percent);
I('cpuUsagePercent').innerHTML = percent + '%';
}
function fillSysLoadAvg(data) {
I('systemLoadAvg').innerHTML = data.sysLoadAvg;
}
function fillMemRealUsage(data) {
var progress = I('memRealUsageProgress');
var value = I('memRealUsageProgressValue');
var percent = data.memRealUsage.percent;
value.style.width = percent + '%';
setColor(progress, percent);
I('memRealUsagePercent').innerHTML = percent + '%';
}
function fillSwapRealUsage(data) {
var progress = I('swapRealUsageProgress');
var value = I('swapUsageProgressValue');
var percent = data.swapRealUsage.percent;
value.style.width = percent + '%';
setColor(progress, percent);
I('swapRealUsagePercent').innerHTML = percent + '%';
}
function fillServerInfo(data) {
I('serverInfoTime').innerHTML = data.serverInfo.time;
I('serverUpTime').innerHTML = data.serverInfo.upTime;
}
var lastNetworkStats = {};
function fillNetworkStats(data) {
if (typeof data.networkStats !== 'object') {
return;
}
var keys = Object.keys(data.networkStats);
if (keys.length === 0) {
return;
}
keys.map(function (k) {
var item = data.networkStats[k];
['rx', 'tx'].map(function (type) {
var total = data.networkStats[k][type];
var last = lastNetworkStats[k] && lastNetworkStats[k][type] || 0;
I('network-' + k + '-' + type + '-rate').innerHTML = last ? formatBytes((total - last) / 2) : 0;
I('network-' + k + '-' + type + '-total').innerHTML = formatBytes(total);
if (!lastNetworkStats[k]) {
lastNetworkStats[k] = {};
}
lastNetworkStats[k][type] = total;
});
});
}
request();
})();
</script>
<?php
} }
namespace InnStudio\Prober\ServerBenchmark; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\I18n\Api as I18n; class ServerBenchmark { private $ID = 'serverBenchmark'; public function __construct() { Events::patch('mods', array($this, 'filter'), 600); Events::on('script', array($this, 'filterJs')); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('Server Benchmark'), 'tinyTitle' => I18n::_('Benchmark'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<p class="description"><?= I18n::_('💡 Hight is better.'); ?></p>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} public function filterJs() { ?>
<script>
(function(){
var el = document.getElementById('benchmark-btn');
console.log(el)
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
} else {
el.innerHTML = errTx;
}
} else {
el.innerHTML = errTx;
}
}
el.addEventListener('click', getPoints);
})()
</script>
<?php
} private function I18n($str) { return I18n::_($str); } private function getContent() { $items = array( array( 'label' => I18n::_('My server'), 'content' => '<a id="benchmark-btn" href="javascript:;">👆 ' . I18n::_('Click to test') . '</a>', ), array( 'label' => '<a href="https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii">' . I18n::_('Aliyun/ECS/PHP7') . '</a>', 'content' => 3302, ), array( 'label' => '<a href="https://www.vultr.com/?ref=7256513" target="_blank">' . I18n::_('Vultr/PHP7') . '</a>', 'content' => 3182, ), array( 'label' => '<a href="https://www.linode.com/?r=2edf930598b4165760c1da9e77b995bac72f8ad1" target="_blank">' . I18n::_('Linode/PHP7') . '</a>', 'content' => 3091, ), array( 'label' => I18n::_('Tencent/PHP7'), 'content' => 3055, ), array( 'label' => '<a href="https://billing.anynode.net/aff.php?aff=511"  target="_blank">' . I18n::_('AnyNode/HDD/PHP7') . '</a>', 'content' => 2641, ), array( 'label' => '<a href="https://www.vultr.com/?ref=7256513" target="_blank">' . I18n::_('Vultr/PHP5') . '</a>', 'content' => 2420, ), array( 'label' => '<a href="https://promotion.aliyun.com/ntms/act/ambassador/sharetouser.html?userCode=0nry1oii&amp;utm_source=0nry1oii">' . I18n::_('Aliyun/Int/PHP5') . '</a>', 'content' => -7686, ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; echo <<<EOT
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$id} {$title}>{$item['content']}</div>
</div>
</div>
EOT;
} } }
namespace InnStudio\Prober\ServerInfo; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class ServerInfo { private $ID = 'serverInfo'; public function __construct() { Events::patch('mods', array($this, 'filter'), 200); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('Server information'), 'tinyTitle' => I18n::_('Info'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="row">
<?php echo $this->getContent(); ?>
</div>
<?php
} private function getDiskInfo() { if ( ! Helper::getDiskTotalSpace()) { return I18n::_('Unavailable'); } $percent = \sprintf('%01.2f', (1 - (Helper::getDiskFreeSpace() / Helper::getDiskTotalSpace())) * 100); $hunamUsed = Helper::formatBytes(Helper::getDiskTotalSpace() - Helper::getDiskFreeSpace()); $hunamTotal = Helper::getDiskTotalSpace(true); return <<<EOT
<div class="progress-container">
<div class="percent" id="diskUsagePercent">{$percent}%</div>
<div class="number">
<span id="diskUsage">
{$hunamUsed}
/
{$hunamTotal}
</span>
</div>
<div class="progress" id="diskUsageProgress">
<div id="diskUsageProgressValue" class="progress-value" style="width: {$percent}%"></div>
</div>
</div>
EOT;
} private function getContent() { $items = array( array( 'label' => $this->_('Server name'), 'content' => $this->getServerInfo('SERVER_NAME'), ), array( 'id' => 'serverInfoTime', 'label' => $this->_('Server time'), 'content' => Helper::getServerTime(), ), array( 'id' => 'serverUpTime', 'label' => $this->_('Server uptime'), 'content' => Helper::getServerUpTime(), ), array( 'label' => $this->_('Server IP'), 'content' => $this->getServerInfo('SERVER_ADDR'), ), array( 'label' => $this->_('Server software'), 'content' => $this->getServerInfo('SERVER_SOFTWARE'), ), array( 'label' => $this->_('PHP version'), 'content' => PHP_VERSION, ), array( 'col' => '1-1', 'label' => $this->_('CPU model'), 'content' => Helper::getCpuModel(), ), array( 'col' => '1-1', 'label' => $this->_('Server OS'), 'content' => \php_uname(), ), array( 'id' => 'scriptPath', 'col' => '1-1', 'label' => $this->_('Script path'), 'content' => __FILE__, ), array( 'col' => '1-1', 'label' => $this->_('Disk usage'), 'content' => $this->getDiskInfo(), ), ); $content = ''; foreach ($items as $item) { $title = isset($item['title']) ? "title=\"{$item['title']}\"" : ''; $col = isset($item['col']) ? $item['col'] : '1-3'; $id = isset($item['id']) ? "id=\"{$item['id']}\"" : ''; echo <<<EOT
<div class="poi-g-lg-{$col}">
<div class="form-group">
<div class="group-label" {$title}>{$item['label']}</div>
<div class="group-content" {$id} {$title}>{$item['content']}</div>
</div>
</div>
EOT;
} } private function _($str) { return I18n::_($str); } private function getServerInfo($key) { return isset($_SERVER[$key]) ? $_SERVER[$key] : ''; } }
namespace InnStudio\Prober\ServerStatus; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\Helper\Api as Helper; use InnStudio\Prober\I18n\Api as I18n; class ServerStatus { private $ID = 'serverStatus'; public function __construct() { Events::patch('mods', array($this, 'filter')); Events::on('style', array($this, 'filterStyle')); } public function filter($mods) { $mods[$this->ID] = array( 'title' => I18n::_('Server status'), 'tinyTitle' => I18n::_('Status'), 'display' => array($this, 'display'), ); return $mods; } public function display() { ?>
<div class="form-group">
<div class="group-label"><?php echo I18n::_('System load'); ?></div>
<div class="group-content small-group-container" id="systemLoadAvg"><?php echo Helper::getSysLoadAvg(); ?></div>
</div>
<div class="form-group">
<div class="group-label"><?php echo I18n::_('CPU usage'); ?></div>
<div class="group-content small-group-container" id="cpuUsage">
<div class="progress-container">
<div class="number">
<span id="cpuUsagePercent">
10%
</span>
</div>
<div class="progress" id="cpuUsageProgress">
<div id="cpuUsageProgressValue" class="progress-value" style="width: 10%"></div>
</div>
</div>
</div>
</div>
<div class="form-group memory-usage">
<div class="group-label"><?php echo I18n::_('Real memory usage'); ?></div>
<div class="group-content">
<div class="progress-container">
<div class="percent" id="memRealUsagePercent"><?php echo $this->getMemUsage('MemRealUsage', true); ?>%</div>
<div class="number">
<span id="memRealUsage">
<?php echo Helper::getHumamMemUsage('MemRealUsage'); ?>
/
<?php echo Helper::getHumamMemUsage('MemTotal'); ?>
</span>
</div>
<div class="progress" id="memRealUsageProgress">
<div id="memRealUsageProgressValue" class="progress-value" style="width: <?php echo $this->getMemUsage('MemRealUsage', true); ?>%"></div>
</div>
</div>
</div>
</div>
<div class="form-group swap-usage">
<div class="group-label"><?php echo I18n::_('Real swap usage'); ?></div>
<div class="group-content">
<div class="progress-container">
<div class="percent" id="swapRealUsagePercent"><?php echo $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'); ?>%</div>
<div class="number">
<span id="swapRealUsage">
<?php echo Helper::getHumamMemUsage('SwapRealUsage'); ?>
/
<?php echo Helper::getHumamMemUsage('SwapTotal'); ?>
</span>
</div>
<div class="progress" id="swapRealUsageProgress">
<div id="swapRealUsageProgressValue" class="progress-value" style="width: <?php echo $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'); ?>%"></div>
</div>
</div>
</div>
</div>
<?php
} public function filterStyle() { ?>
<style>
.small-group{
display: inline-block;
background: #eee;
border-radius: 1rem;
margin: 0 .2rem;
padding: 0 .5rem;
width: 7rem;
text-align: center;
}
#scriptPath.group-content{
word-break: break-all;
}
</style>
<?php
} private function getMemUsage($key, $precent = false, $totalKey = 'MemTotal') { if (false === $precent) { return Helper::getMemoryUsage($key); } return Helper::getMemoryUsage($key) ? \sprintf('%01.2f', Helper::getMemoryUsage($key) / Helper::getMemoryUsage($totalKey) * 100) : 0; } }
namespace InnStudio\Prober\Style; use InnStudio\Prober\Events\Api as Events; class Style { private $ID = 'style'; public function __construct() { Events::on('style', array($this, 'filter')); } public function filter() { $this->styleProgress(); $this->styleGlobal(); $this->stylePoiContainer(); $this->stylePoiGrid(); $this->styleTitle(); } private function styleTitle() { ?>
<style>
.long-title{
text-transform: capitalize;
}
.tiny-title{
display: none;
}
</style>
<?php
} private function styleProgress() { ?>
<style>
.progress-container{
position: relative;
}
.progress-container .percent,
.progress-container .number{
position: absolute;
right: 1rem;
bottom: 0;
z-index: 1;
font-weight: bold;
color: #fff;
text-shadow: 0 1px 1px #000;
line-height: 2rem;
}
.progress-container .percent{
left: 1rem;
right: auto;
}
.progress {
position: relative;
display: block;
width: 100%;
height: 2rem;
background: #444;
border-radius: 1rem;
box-shadow: inset 0px 10px 20px rgba(0,0,0,0.3);
}
.progress .progress-value{
position: absolute;
top: .35rem;
bottom: .35rem;
left: .35rem;
right: .35rem;
-webkit-transition: 2s all;
transition: 2s all;
border-radius: 1rem;
background: #00cc00;
box-shadow: inset 0 -5px 10px rgba(0,0,0,0.4), 0 5px 10px 0px rgba(0,0,0,0.3)
}
.progress.medium-low .progress-value{
background: #009999;
}
.progress.medium .progress-value{
background: #f07746;
}
.progress.high .progress-value{
background: #a80000;
}
</style>
<?php
} private function styleGlobal() { ?>
<style>
*{
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
vertical-align: middle;
}
html{
font-size: 75%;
background: #333;
}
body{
background: #f8f8f8;
color: #666;
font-family: "Microsoft YaHei UI", "Microsoft YaHei", sans-serif;
border: 10px solid #333;
margin: 0;
border-radius: 2rem;
line-height: 2rem;
}
a{
color: #333;
text-decoration: none;
}
a:hover,
a:active{
color: #999;
text-decoration: underline;
}
.ini-ok{
color: green;
font-weight: bold;
}
.ini-error{
color: red;
font-weight: bold;
}
h1{
text-align: center;
font-size: 1rem;
background: #333;
border-radius: 0 0 10rem 10rem;
width: 60%;
line-height: 1.5rem;
margin: 0 auto 1rem;
}
h1 a{
display: block;
color: #fff;
padding: 0 0 10px;
}
.form-group{
overflow: hidden;
display: table;
width: 100%;
border-bottom: 1px solid #eee;
min-height: 3.5rem;
}
.form-group:hover{
background: rgba(0,0,0,.03);
}
.group-label,
.group-content{
display: table-cell;
padding: .5rem 1rem;
}
.group-label{
width: 10rem;
text-align: left;
font-weight: normal;
}
.group-content a{
line-height: 1;
display: inline-block;
}
@media (min-width:768px){
.group-label{
width: 15rem;
}
}
fieldset{
position: relative;
border: 5px solid #eee;
border-radius: .5rem;
padding: 0;
background: rgba(255,255,255,.5);
margin-bottom: 1rem;
padding: .5rem 0;
}
legend{
background: #333;
margin-left: 1rem;
padding: .5rem 2rem;
border-radius: 5rem;
color: #fff;
margin: 0 auto;
}
p{
margin: 0 0 1rem;
}
.description{
margin: 0;
padding-left: 1rem;
font-style: italic;
}
</style>
<?php
} private function stylePoiContainer() { ?>
<style>
@media (min-width:768px){.poi-container{margin-left:auto;margin-right:auto;padding-left:.5rem;padding-right:.5rem}}
@media (min-width:579px){.poi-container{width:559px}}
@media (min-width:768px){.poi-container{width:748px}}
@media (min-width:992px){.poi-container{width:940px;padding-left:1rem;padding-right:1rem}}
@media (min-width:1200px){.poi-container{width:1180px}}
@media (min-width:992px){.row{margin-left:-.5rem;margin-right:-.5rem}}
</style>
<?php
} private function stylePoiGrid() { ?>
<style>
.row:after{
content: '';
display: block;
clear: both;
}
.row>*{max-width:100%;float:left;width:100%;box-sizing:border-box;padding-left:.25rem;padding-right:.25rem;min-height: 3.5rem;}
@media (min-width:992px){.row>*{padding-left:.5rem;padding-right:.5rem}}
.poi-g-1-1{width:100%}.poi-g-1-2{width:50%}.poi-g-2-2{width:100%}.poi-g-1-3{width:33.33333%}.poi-g-2-3{width:66.66667%}.poi-g-3-3{width:100%}.poi-g-1-4{width:25%}.poi-g-2-4{width:50%}.poi-g-3-4{width:75%}.poi-g-4-4{width:100%}.poi-g-1-5{width:20%}.poi-g-2-5{width:40%}.poi-g-3-5{width:60%}.poi-g-4-5{width:80%}.poi-g-5-5{width:100%}.poi-g-1-6{width:16.66667%}.poi-g-2-6{width:33.33333%}.poi-g-3-6{width:50%}.poi-g-4-6{width:66.66667%}.poi-g-5-6{width:83.33333%}.poi-g-6-6{width:100%}.poi-g-1-7{width:14.28571%}.poi-g-2-7{width:28.57143%}.poi-g-3-7{width:42.85714%}.poi-g-4-7{width:57.14286%}.poi-g-5-7{width:71.42857%}.poi-g-6-7{width:85.71429%}.poi-g-7-7{width:100%}.poi-g-1-8{width:12.5%}.poi-g-2-8{width:25%}.poi-g-3-8{width:37.5%}.poi-g-4-8{width:50%}.poi-g-5-8{width:62.5%}.poi-g-6-8{width:75%}.poi-g-7-8{width:87.5%}.poi-g-8-8{width:100%}.poi-g-1-9{width:11.11111%}.poi-g-2-9{width:22.22222%}.poi-g-3-9{width:33.33333%}.poi-g-4-9{width:44.44444%}.poi-g-5-9{width:55.55556%}.poi-g-6-9{width:66.66667%}.poi-g-7-9{width:77.77778%}.poi-g-8-9{width:88.88889%}.poi-g-9-9{width:100%}.poi-g-1-10{width:10%}.poi-g-2-10{width:20%}.poi-g-3-10{width:30%}.poi-g-4-10{width:40%}.poi-g-5-10{width:50%}.poi-g-6-10{width:60%}.poi-g-7-10{width:70%}.poi-g-8-10{width:80%}.poi-g-9-10{width:90%}.poi-g-10-10{width:100%}.poi-g-1-11{width:9.09091%}.poi-g-2-11{width:18.18182%}.poi-g-3-11{width:27.27273%}.poi-g-4-11{width:36.36364%}.poi-g-5-11{width:45.45455%}.poi-g-6-11{width:54.54545%}.poi-g-7-11{width:63.63636%}.poi-g-8-11{width:72.72727%}.poi-g-9-11{width:81.81818%}.poi-g-10-11{width:90.90909%}.poi-g-11-11{width:100%}.poi-g-1-12{width:8.33333%}.poi-g-2-12{width:16.66667%}.poi-g-3-12{width:25%}.poi-g-4-12{width:33.33333%}.poi-g-5-12{width:41.66667%}.poi-g-6-12{width:50%}.poi-g-7-12{width:58.33333%}.poi-g-8-12{width:66.66667%}.poi-g-9-12{width:75%}.poi-g-10-12{width:83.33333%}.poi-g-11-12{width:91.66667%}.poi-g-12-12{width:100%}@media (min-width:579px){.poi-g-sm-1-1{width:100%}.poi-g-sm-1-2{width:50%}.poi-g-sm-2-2{width:100%}.poi-g-sm-1-3{width:33.33333%}.poi-g-sm-2-3{width:66.66667%}.poi-g-sm-3-3{width:100%}.poi-g-sm-1-4{width:25%}.poi-g-sm-2-4{width:50%}.poi-g-sm-3-4{width:75%}.poi-g-sm-4-4{width:100%}.poi-g-sm-1-5{width:20%}.poi-g-sm-2-5{width:40%}.poi-g-sm-3-5{width:60%}.poi-g-sm-4-5{width:80%}.poi-g-sm-5-5{width:100%}.poi-g-sm-1-6{width:16.66667%}.poi-g-sm-2-6{width:33.33333%}.poi-g-sm-3-6{width:50%}.poi-g-sm-4-6{width:66.66667%}.poi-g-sm-5-6{width:83.33333%}.poi-g-sm-6-6{width:100%}.poi-g-sm-1-7{width:14.28571%}.poi-g-sm-2-7{width:28.57143%}.poi-g-sm-3-7{width:42.85714%}.poi-g-sm-4-7{width:57.14286%}.poi-g-sm-5-7{width:71.42857%}.poi-g-sm-6-7{width:85.71429%}.poi-g-sm-7-7{width:100%}.poi-g-sm-1-8{width:12.5%}.poi-g-sm-2-8{width:25%}.poi-g-sm-3-8{width:37.5%}.poi-g-sm-4-8{width:50%}.poi-g-sm-5-8{width:62.5%}.poi-g-sm-6-8{width:75%}.poi-g-sm-7-8{width:87.5%}.poi-g-sm-8-8{width:100%}.poi-g-sm-1-9{width:11.11111%}.poi-g-sm-2-9{width:22.22222%}.poi-g-sm-3-9{width:33.33333%}.poi-g-sm-4-9{width:44.44444%}.poi-g-sm-5-9{width:55.55556%}.poi-g-sm-6-9{width:66.66667%}.poi-g-sm-7-9{width:77.77778%}.poi-g-sm-8-9{width:88.88889%}.poi-g-sm-9-9{width:100%}.poi-g-sm-1-10{width:10%}.poi-g-sm-2-10{width:20%}.poi-g-sm-3-10{width:30%}.poi-g-sm-4-10{width:40%}.poi-g-sm-5-10{width:50%}.poi-g-sm-6-10{width:60%}.poi-g-sm-7-10{width:70%}.poi-g-sm-8-10{width:80%}.poi-g-sm-9-10{width:90%}.poi-g-sm-10-10{width:100%}.poi-g-sm-1-11{width:9.09091%}.poi-g-sm-2-11{width:18.18182%}.poi-g-sm-3-11{width:27.27273%}.poi-g-sm-4-11{width:36.36364%}.poi-g-sm-5-11{width:45.45455%}.poi-g-sm-6-11{width:54.54545%}.poi-g-sm-7-11{width:63.63636%}.poi-g-sm-8-11{width:72.72727%}.poi-g-sm-9-11{width:81.81818%}.poi-g-sm-10-11{width:90.90909%}.poi-g-sm-11-11{width:100%}.poi-g-sm-1-12{width:8.33333%}.poi-g-sm-2-12{width:16.66667%}.poi-g-sm-3-12{width:25%}.poi-g-sm-4-12{width:33.33333%}.poi-g-sm-5-12{width:41.66667%}.poi-g-sm-6-12{width:50%}.poi-g-sm-7-12{width:58.33333%}.poi-g-sm-8-12{width:66.66667%}.poi-g-sm-9-12{width:75%}.poi-g-sm-10-12{width:83.33333%}.poi-g-sm-11-12{width:91.66667%}.poi-g-sm-12-12{width:100%}}@media (min-width:768px){.poi-g-md-1-1{width:100%}.poi-g-md-1-2{width:50%}.poi-g-md-2-2{width:100%}.poi-g-md-1-3{width:33.33333%}.poi-g-md-2-3{width:66.66667%}.poi-g-md-3-3{width:100%}.poi-g-md-1-4{width:25%}.poi-g-md-2-4{width:50%}.poi-g-md-3-4{width:75%}.poi-g-md-4-4{width:100%}.poi-g-md-1-5{width:20%}.poi-g-md-2-5{width:40%}.poi-g-md-3-5{width:60%}.poi-g-md-4-5{width:80%}.poi-g-md-5-5{width:100%}.poi-g-md-1-6{width:16.66667%}.poi-g-md-2-6{width:33.33333%}.poi-g-md-3-6{width:50%}.poi-g-md-4-6{width:66.66667%}.poi-g-md-5-6{width:83.33333%}.poi-g-md-6-6{width:100%}.poi-g-md-1-7{width:14.28571%}.poi-g-md-2-7{width:28.57143%}.poi-g-md-3-7{width:42.85714%}.poi-g-md-4-7{width:57.14286%}.poi-g-md-5-7{width:71.42857%}.poi-g-md-6-7{width:85.71429%}.poi-g-md-7-7{width:100%}.poi-g-md-1-8{width:12.5%}.poi-g-md-2-8{width:25%}.poi-g-md-3-8{width:37.5%}.poi-g-md-4-8{width:50%}.poi-g-md-5-8{width:62.5%}.poi-g-md-6-8{width:75%}.poi-g-md-7-8{width:87.5%}.poi-g-md-8-8{width:100%}.poi-g-md-1-9{width:11.11111%}.poi-g-md-2-9{width:22.22222%}.poi-g-md-3-9{width:33.33333%}.poi-g-md-4-9{width:44.44444%}.poi-g-md-5-9{width:55.55556%}.poi-g-md-6-9{width:66.66667%}.poi-g-md-7-9{width:77.77778%}.poi-g-md-8-9{width:88.88889%}.poi-g-md-9-9{width:100%}.poi-g-md-1-10{width:10%}.poi-g-md-2-10{width:20%}.poi-g-md-3-10{width:30%}.poi-g-md-4-10{width:40%}.poi-g-md-5-10{width:50%}.poi-g-md-6-10{width:60%}.poi-g-md-7-10{width:70%}.poi-g-md-8-10{width:80%}.poi-g-md-9-10{width:90%}.poi-g-md-10-10{width:100%}.poi-g-md-1-11{width:9.09091%}.poi-g-md-2-11{width:18.18182%}.poi-g-md-3-11{width:27.27273%}.poi-g-md-4-11{width:36.36364%}.poi-g-md-5-11{width:45.45455%}.poi-g-md-6-11{width:54.54545%}.poi-g-md-7-11{width:63.63636%}.poi-g-md-8-11{width:72.72727%}.poi-g-md-9-11{width:81.81818%}.poi-g-md-10-11{width:90.90909%}.poi-g-md-11-11{width:100%}.poi-g-md-1-12{width:8.33333%}.poi-g-md-2-12{width:16.66667%}.poi-g-md-3-12{width:25%}.poi-g-md-4-12{width:33.33333%}.poi-g-md-5-12{width:41.66667%}.poi-g-md-6-12{width:50%}.poi-g-md-7-12{width:58.33333%}.poi-g-md-8-12{width:66.66667%}.poi-g-md-9-12{width:75%}.poi-g-md-10-12{width:83.33333%}.poi-g-md-11-12{width:91.66667%}.poi-g-md-12-12{width:100%}}@media (min-width:992px){.poi-g-lg-1-1{width:100%}.poi-g-lg-1-2{width:50%}.poi-g-lg-2-2{width:100%}.poi-g-lg-1-3{width:33.33333%}.poi-g-lg-2-3{width:66.66667%}.poi-g-lg-3-3{width:100%}.poi-g-lg-1-4{width:25%}.poi-g-lg-2-4{width:50%}.poi-g-lg-3-4{width:75%}.poi-g-lg-4-4{width:100%}.poi-g-lg-1-5{width:20%}.poi-g-lg-2-5{width:40%}.poi-g-lg-3-5{width:60%}.poi-g-lg-4-5{width:80%}.poi-g-lg-5-5{width:100%}.poi-g-lg-1-6{width:16.66667%}.poi-g-lg-2-6{width:33.33333%}.poi-g-lg-3-6{width:50%}.poi-g-lg-4-6{width:66.66667%}.poi-g-lg-5-6{width:83.33333%}.poi-g-lg-6-6{width:100%}.poi-g-lg-1-7{width:14.28571%}.poi-g-lg-2-7{width:28.57143%}.poi-g-lg-3-7{width:42.85714%}.poi-g-lg-4-7{width:57.14286%}.poi-g-lg-5-7{width:71.42857%}.poi-g-lg-6-7{width:85.71429%}.poi-g-lg-7-7{width:100%}.poi-g-lg-1-8{width:12.5%}.poi-g-lg-2-8{width:25%}.poi-g-lg-3-8{width:37.5%}.poi-g-lg-4-8{width:50%}.poi-g-lg-5-8{width:62.5%}.poi-g-lg-6-8{width:75%}.poi-g-lg-7-8{width:87.5%}.poi-g-lg-8-8{width:100%}.poi-g-lg-1-9{width:11.11111%}.poi-g-lg-2-9{width:22.22222%}.poi-g-lg-3-9{width:33.33333%}.poi-g-lg-4-9{width:44.44444%}.poi-g-lg-5-9{width:55.55556%}.poi-g-lg-6-9{width:66.66667%}.poi-g-lg-7-9{width:77.77778%}.poi-g-lg-8-9{width:88.88889%}.poi-g-lg-9-9{width:100%}.poi-g-lg-1-10{width:10%}.poi-g-lg-2-10{width:20%}.poi-g-lg-3-10{width:30%}.poi-g-lg-4-10{width:40%}.poi-g-lg-5-10{width:50%}.poi-g-lg-6-10{width:60%}.poi-g-lg-7-10{width:70%}.poi-g-lg-8-10{width:80%}.poi-g-lg-9-10{width:90%}.poi-g-lg-10-10{width:100%}.poi-g-lg-1-11{width:9.09091%}.poi-g-lg-2-11{width:18.18182%}.poi-g-lg-3-11{width:27.27273%}.poi-g-lg-4-11{width:36.36364%}.poi-g-lg-5-11{width:45.45455%}.poi-g-lg-6-11{width:54.54545%}.poi-g-lg-7-11{width:63.63636%}.poi-g-lg-8-11{width:72.72727%}.poi-g-lg-9-11{width:81.81818%}.poi-g-lg-10-11{width:90.90909%}.poi-g-lg-11-11{width:100%}.poi-g-lg-1-12{width:8.33333%}.poi-g-lg-2-12{width:16.66667%}.poi-g-lg-3-12{width:25%}.poi-g-lg-4-12{width:33.33333%}.poi-g-lg-5-12{width:41.66667%}.poi-g-lg-6-12{width:50%}.poi-g-lg-7-12{width:58.33333%}.poi-g-lg-8-12{width:66.66667%}.poi-g-lg-9-12{width:75%}.poi-g-lg-10-12{width:83.33333%}.poi-g-lg-11-12{width:91.66667%}.poi-g-lg-12-12{width:100%}}@media (min-width:1200px){.poi-g-xl-1-1{width:100%}.poi-g-xl-1-2{width:50%}.poi-g-xl-2-2{width:100%}.poi-g-xl-1-3{width:33.33333%}.poi-g-xl-2-3{width:66.66667%}.poi-g-xl-3-3{width:100%}.poi-g-xl-1-4{width:25%}.poi-g-xl-2-4{width:50%}.poi-g-xl-3-4{width:75%}.poi-g-xl-4-4{width:100%}.poi-g-xl-1-5{width:20%}.poi-g-xl-2-5{width:40%}.poi-g-xl-3-5{width:60%}.poi-g-xl-4-5{width:80%}.poi-g-xl-5-5{width:100%}.poi-g-xl-1-6{width:16.66667%}.poi-g-xl-2-6{width:33.33333%}.poi-g-xl-3-6{width:50%}.poi-g-xl-4-6{width:66.66667%}.poi-g-xl-5-6{width:83.33333%}.poi-g-xl-6-6{width:100%}.poi-g-xl-1-7{width:14.28571%}.poi-g-xl-2-7{width:28.57143%}.poi-g-xl-3-7{width:42.85714%}.poi-g-xl-4-7{width:57.14286%}.poi-g-xl-5-7{width:71.42857%}.poi-g-xl-6-7{width:85.71429%}.poi-g-xl-7-7{width:100%}.poi-g-xl-1-8{width:12.5%}.poi-g-xl-2-8{width:25%}.poi-g-xl-3-8{width:37.5%}.poi-g-xl-4-8{width:50%}.poi-g-xl-5-8{width:62.5%}.poi-g-xl-6-8{width:75%}.poi-g-xl-7-8{width:87.5%}.poi-g-xl-8-8{width:100%}.poi-g-xl-1-9{width:11.11111%}.poi-g-xl-2-9{width:22.22222%}.poi-g-xl-3-9{width:33.33333%}.poi-g-xl-4-9{width:44.44444%}.poi-g-xl-5-9{width:55.55556%}.poi-g-xl-6-9{width:66.66667%}.poi-g-xl-7-9{width:77.77778%}.poi-g-xl-8-9{width:88.88889%}.poi-g-xl-9-9{width:100%}.poi-g-xl-1-10{width:10%}.poi-g-xl-2-10{width:20%}.poi-g-xl-3-10{width:30%}.poi-g-xl-4-10{width:40%}.poi-g-xl-5-10{width:50%}.poi-g-xl-6-10{width:60%}.poi-g-xl-7-10{width:70%}.poi-g-xl-8-10{width:80%}.poi-g-xl-9-10{width:90%}.poi-g-xl-10-10{width:100%}.poi-g-xl-1-11{width:9.09091%}.poi-g-xl-2-11{width:18.18182%}.poi-g-xl-3-11{width:27.27273%}.poi-g-xl-4-11{width:36.36364%}.poi-g-xl-5-11{width:45.45455%}.poi-g-xl-6-11{width:54.54545%}.poi-g-xl-7-11{width:63.63636%}.poi-g-xl-8-11{width:72.72727%}.poi-g-xl-9-11{width:81.81818%}.poi-g-xl-10-11{width:90.90909%}.poi-g-xl-11-11{width:100%}.poi-g-xl-1-12{width:8.33333%}.poi-g-xl-2-12{width:16.66667%}.poi-g-xl-3-12{width:25%}.poi-g-xl-4-12{width:33.33333%}.poi-g-xl-5-12{width:41.66667%}.poi-g-xl-6-12{width:50%}.poi-g-xl-7-12{width:58.33333%}.poi-g-xl-8-12{width:66.66667%}.poi-g-xl-9-12{width:75%}.poi-g-xl-10-12{width:83.33333%}.poi-g-xl-11-12{width:91.66667%}.poi-g-xl-12-12{width:100%}}
</style>
<?php
} }
namespace InnStudio\Prober\Updater; use InnStudio\Prober\Config\Api as Config; use InnStudio\Prober\Events\Api as Events; use InnStudio\Prober\I18n\Api as I18n; class Updater { private $ID = 'updater'; public function __construct() { Events::on('script', array($this, 'filter')); } public function filter() { ?>
<script>
(function(){
var versionCompare = function(left, right) {
if (typeof left + typeof right != 'stringstring')
return false;
var a = left.split('.')
,   b = right.split('.')
,   i = 0, len = Math.max(a.length, b.length);
for (; i < len; i++) {
if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
return 1;
} else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
return -1;
}
}
return 0;
}
var version = "<?php echo Config::$APP_VERSION; ?>";
var xhr = new XMLHttpRequest();
try {
xhr.open('get', '<?php echo Config::$CHANGELOG_URL; ?>');
xhr.send();
xhr.onload = load;
} catch (err) {}
function load(){
if (xhr.readyState !== 4) {
return;
}
if (xhr.status >= 200 && xhr.status < 400) {
var data = xhr.responseText;
if (! data) {
return;
}
var versionInfo = getVersionInfo(data);
if (!versionInfo.length) {
return;
}
if (versionCompare(version, versionInfo[0]) === -1) {
var lang = '<?php echo I18n::_('Found update! {APP_NAME} has new version v{APP_NEW_VERSION}'); ?>';
lang = lang.replace('{APP_NAME}', '<?php echo I18n::_(Config::$APP_NAME); ?>');
lang = lang.replace('{APP_NEW_VERSION}', versionInfo[0]);
document.querySelector('h1').innerHTML = '<a href="<?php echo Config::$AUTHOR_URL; ?>" target="_blank">' + lang + '</a>';
}
}
}
function getVersionInfo(data){
var reg = /^#{2}\s+(\d+\.\d+\.\d+)\s+\-\s+(\d{4}\-\d+\-\d+)/mg;
return reg.test(data) ? [RegExp.$1,RegExp.$2]: [];
}
})()
</script>
<?php
} }new \InnStudio\Prober\Awesome\Awesome();
new \InnStudio\Prober\Benchmark\Benchmark();
new \InnStudio\Prober\Database\Database();
new \InnStudio\Prober\Fetch\Fetch();
new \InnStudio\Prober\Footer\Footer();
new \InnStudio\Prober\MyInfo\MyInfo();
new \InnStudio\Prober\Nav\Nav();
new \InnStudio\Prober\NetworkStats\NetworkStats();
new \InnStudio\Prober\PhpExtensionInfo\PhpExtensionInfo();
new \InnStudio\Prober\PhpInfo\PhpInfo();
new \InnStudio\Prober\PhpInfoDetail\PhpInfoDetail();
new \InnStudio\Prober\Script\Script();
new \InnStudio\Prober\ServerBenchmark\ServerBenchmark();
new \InnStudio\Prober\ServerInfo\ServerInfo();
new \InnStudio\Prober\ServerStatus\ServerStatus();
new \InnStudio\Prober\Style\Style();
new \InnStudio\Prober\Updater\Updater();
new \InnStudio\Prober\Entry\Entry();