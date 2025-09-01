<?php
namespace InnStudio\Prober\Components\PreDefine;
$version = phpversion();
version_compare($version, '5.4.0','<') && exit("PHP 5.4+ is required. Currently installed version is: {$version}");
\define('XPROBER_TIMER', \microtime(true));
\define('XPROBER_IS_DEV', false);
\define('XPROBER_DIR', __DIR__);
namespace InnStudio\Prober\Components\Action;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\ServerBenchmark\ServerBenchmarkDelay;
final class Action
{
    public function __construct()
    {
        // delay for server benchmark
        (new ServerBenchmarkDelay())->delay();
        $action = (string) filter_input(\INPUT_GET, 'action', \FILTER_DEFAULT);
        if ( ! $action) {
            return;
        }
        // for php54
        foreach ([
            'Poll\\PollAction',
            'Script\\ScriptAction',
            'Style\\StyleAction',
            'Ping\\PingAction',
            'ServerInfo\\ServerInfoPublicIpv4Action',
            'ServerInfo\\ServerInfoPublicIpv6Action',
            'PhpInfo\\PhpInfoLatestPhpVersionAction',
            'PhpInfoDetail\\PhpInfoDetailAction',
            'Updater\\UpdaterActionVersion',
            'Updater\\UpdaterActionUpdate',
            'ServerBenchmark\\ServerBenchmarkPerformanceAction',
            'ServerBenchmark\\ServerBenchmarkServersAction',
            'Location\\LocationIpv4Action',
            'Nodes\\NodesAction',
        ] as $fn) {
            $class = "\\InnStudio\\Prober\\Components\\{$fn}";
            (new $class())->render($action);
        }
        (new RestResponse())
            ->setStatus(StatusCode::BAD_REQUEST)
            ->end();
    }
}
namespace InnStudio\Prober\Components\PhpInfo;
class PhpInfoConstants
{
    const ID = 'phpInfo';
}
namespace InnStudio\Prober\Components\PhpInfo;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class PhpInfoLatestPhpVersionAction
{
    public function render($action)
    {
        $id = PhpInfoConstants::ID;
        if ('latestPhpVersion' !== $action) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled($id)) {
            $response
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        $content = file_get_contents('https://www.php.net/releases/?json');
        if ( ! $content) {
            $response
                ->setStatus(StatusCode::NO_CONTENT)
                ->end();
        }
        $versions = json_decode($content, true);
        if ( ! $versions) {
            $response
                ->setStatus(StatusCode::NO_CONTENT)
                ->end();
        }
        $version = isset($versions[ConfigApi::$config['LATEST_PHP_STABLE_VERSION']]['version']) ? $versions[ConfigApi::$config['LATEST_PHP_STABLE_VERSION']]['version'] : '';
        if ( ! $version) {
            $response
                ->setStatus(StatusCode::NO_CONTENT)
                ->end();
        }
        $response
            ->setData([
                'version' => $version,
                'date' => $versions[ConfigApi::$config['LATEST_PHP_STABLE_VERSION']]['date'],
            ])
            ->end();
    }
}
namespace InnStudio\Prober\Components\PhpInfo;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class PhpInfoPoll
{
    public function render()
    {
        $id = PhpInfoConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'phpVersion' => \PHP_VERSION,
                'sapi' => \PHP_SAPI,
                'displayErrors' => (bool) \ini_get('display_errors'),
                'errorReporting' => (int) \ini_get('error_reporting'),
                'memoryLimit' => (string) \ini_get('memory_limit'),
                'postMaxSize' => (string) \ini_get('post_max_size'),
                'uploadMaxFilesize' => (string) \ini_get('upload_max_filesize'),
                'maxInputVars' => (int) \ini_get('max_input_vars'),
                'maxExecutionTime' => (int) \ini_get('max_execution_time'),
                'defaultSocketTimeout' => (int) \ini_get('default_socket_timeout'),
                'allowUrlFopen' => (bool) \ini_get('allow_url_fopen'),
                'smtp' => (bool) \ini_get('SMTP'),
                'disableFunctions' => UserConfigApi::isDisabled('phpDisabledFunctions') ? [] : array_filter(explode(',', (string) \ini_get('disable_functions'))),
                'disableClasses' => UserConfigApi::isDisabled('phpDisabledClasses') ? [] : array_filter(explode(',', (string) \ini_get('disable_classes'))),
            ],
        ];
    }
}
namespace InnStudio\Prober\Components\Events;
final class EventsApi
{
    private static $events = [];
    private static $PRIORITY_ID = 'priority';
    private static $CALLBACK_ID = 'callback';
    public static function on($name, $callback, $priority = 10)
    {
        if ( ! isset(self::$events[$name])) {
            self::$events[$name] = [];
        }
        self::$events[$name][] = [
            self::$PRIORITY_ID => $priority,
            self::$CALLBACK_ID => $callback,
        ];
    }
    public static function emit()
    {
        $args = \func_get_args();
        $name = $args[0];
        $return = isset($args[1]) ? $args[1] : null;
        unset($args[0], $args[1]);
        $events = isset(self::$events[$name]) ? self::$events[$name] : false;
        if ( ! $events) {
            return $return;
        }
        $sortArr = [];
        foreach ($events as $k => $filter) {
            $sortArr[$k] = $filter[self::$PRIORITY_ID];
        }
        array_multisort($sortArr, $events);
        foreach ($events as $filter) {
            $return = \call_user_func_array($filter[self::$CALLBACK_ID], [$return, $args]);
        }
        return $return;
    }
}
namespace InnStudio\Prober\Components\PhpInfoDetail;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class PhpInfoDetailAction
{
    public function render($action)
    {
        $id = PhpInfoDetailConstants::ID;
        if ($action !== $id) {
            return;
        }
        if (UserConfigApi::isDisabled($id)) {
            (new RestResponse())
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        phpinfo();
        exit;
    }
}
namespace InnStudio\Prober\Components\PhpInfoDetail;
class PhpInfoDetailConstants
{
    const ID = 'phpInfoDetail';
}
namespace InnStudio\Prober\Components\Ping;
class PingConstants
{
    const ID = 'ping';
}
namespace InnStudio\Prober\Components\Ping;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class PingAction
{
    public function render($action)
    {
        $id = PingConstants::ID;
        if ($action !== $id) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled($id)) {
            $response
                ->setStatus(StatusCode::NOT_IMPLEMENTED)
                ->end();
        }
        $response
            ->setData([
                'id' => (string) microtime(true),
                'time' => \defined('XPROBER_TIMER') ? microtime(true) - XPROBER_TIMER : 0,
            ])
            ->end();
    }
}
namespace InnStudio\Prober\Components\Ping;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class PingPoll
{
    public function render()
    {
        $id = PingConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => true,
        ];
    }
}
namespace InnStudio\Prober\Components\PhpExtensions;
class PhpExtensionsConstants
{
    const ID = 'phpExtensions';
}
namespace InnStudio\Prober\Components\PhpExtensions;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class PhpExtensionsPoll
{
    public function render()
    {
        $id = PhpExtensionsConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        $jitEnabled = false;
        if (\function_exists('opcache_get_status')) {
            $status = opcache_get_status();
            if (isset($status['jit']['enabled']) && true === $status['jit']['enabled']) {
                $jitEnabled = true;
            }
        }
        return [
            $id => [
                'redis' => \extension_loaded('redis') && class_exists('Redis'),
                'sqlite3' => \extension_loaded('sqlite3') && class_exists('Sqlite3'),
                'memcache' => \extension_loaded('memcache') && class_exists('Memcache'),
                'memcached' => \extension_loaded('memcached') && class_exists('Memcached'),
                'opcache' => \function_exists('opcache_get_status'),
                'opcacheEnabled' => $this->isOpcEnabled(),
                'opcacheJitEnabled' => $jitEnabled,
                'swoole' => \extension_loaded('swoole') && \function_exists('swoole_version'),
                'imagick' => \extension_loaded('imagick') && class_exists('Imagick'),
                'gmagick' => \extension_loaded('gmagick'),
                'exif' => \extension_loaded('exif') && \function_exists('exif_imagetype'),
                'fileinfo' => \extension_loaded('fileinfo'),
                'simplexml' => \extension_loaded('simplexml'),
                'sockets' => \extension_loaded('sockets') && \function_exists('socket_accept'),
                'mysqli' => \extension_loaded('mysqli') && class_exists('mysqli'),
                'zip' => \extension_loaded('zip') && class_exists('ZipArchive'),
                'mbstring' => \extension_loaded('mbstring') && \function_exists('mb_substr'),
                'phalcon' => \extension_loaded('phalcon'),
                'xdebug' => \extension_loaded('xdebug'),
                'zendOptimizer' => \function_exists('zend_optimizer_version'),
                'ionCube' => \extension_loaded('ioncube loader'),
                'sourceGuardian' => \extension_loaded('sourceguardian'),
                'ldap' => \function_exists('ldap_connect'),
                'curl' => \function_exists('curl_init'),
                'loadedExtensions' => UserConfigApi::isDisabled('phpExtensionsLoaded') ? [] : get_loaded_extensions(),
            ],
        ];
    }
    private function isOpcEnabled()
    {
        $isOpcEnabled = \function_exists('opcache_get_configuration');
        if ($isOpcEnabled) {
            $isOpcEnabled = opcache_get_configuration();
            $isOpcEnabled = isset($isOpcEnabled['directives']['opcache.enable']) && true === $isOpcEnabled['directives']['opcache.enable'];
        }
        return $isOpcEnabled;
    }
}
namespace InnStudio\Prober\Components\Footer;
use InnStudio\Prober\Components\Events\EventsApi;
final class Footer
{
    private $ID = 'footer';
    public function __construct()
    {
        EventsApi::on('conf', function (array $conf) {
            $conf[$this->ID] = [
                'memUsage' => memory_get_usage(),
                'time' => microtime(true) - (\defined('XPROBER_TIMER') ? XPROBER_TIMER : 0),
            ];
            return $conf;
        }, \PHP_INT_MAX);
    }
}
namespace InnStudio\Prober\Components\DiskUsage;
class DiskUsageConstants
{
    const ID = 'diskUsage';
}
namespace InnStudio\Prober\Components\DiskUsage;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsDisk;
final class DiskUsagePoll
{
    public function render()
    {
        $id = DiskUsageConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'items' => UtilsDisk::getItems(),
            ],
        ];
    }
}
namespace InnStudio\Prober\Components\Location;
class LocationConstants
{
    const ID = 'locationIpv4';
    const FEATURE_LOCATION = 'locationIpv4';
}
namespace InnStudio\Prober\Components\Location;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsLocation;
final class LocationIpv4Action
{
    public function render($action)
    {
        if (LocationConstants::ID !== $action) {
            return;
        }
        $response = new RestResponse();
        if (UserConfigApi::isDisabled(LocationConstants::FEATURE_LOCATION)) {
            $response
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        $ip = filter_input(\INPUT_GET, 'ip', \FILTER_VALIDATE_IP, [
            'flags' => \FILTER_FLAG_IPV4,
        ]);
        if ( ! $ip) {
            $response
                ->setStatus(StatusCode::BAD_REQUEST)
                ->end();
        }
        $response
            ->setData(UtilsLocation::getLocation($ip))
            ->end();
    }
}
namespace InnStudio\Prober\Components\ServerStatus;
class ServerStatusConstants
{
    const ID = 'serverStatus';
}
namespace InnStudio\Prober\Components\ServerStatus;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsCpu;
use InnStudio\Prober\Components\Utils\UtilsMemory;
final class ServerStatusPoll
{
    public function render()
    {
        $id = ServerStatusConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'sysLoad' => UtilsCpu::getLoadAvg(),
                'cpuUsage' => UtilsCpu::getUsage(),
                'memRealUsage' => [
                    'value' => UtilsMemory::getMemoryUsage('MemRealUsage'),
                    'max' => UtilsMemory::getMemoryUsage('MemTotal'),
                ],
                'memBuffers' => [
                    'value' => UtilsMemory::getMemoryUsage('Buffers'),
                    'max' => UtilsMemory::getMemoryUsage('MemUsage'),
                ],
                'memCached' => [
                    'value' => UtilsMemory::getMemoryUsage('Cached'),
                    'max' => UtilsMemory::getMemoryUsage('MemUsage'),
                ],
                'swapUsage' => [
                    'value' => UtilsMemory::getMemoryUsage('SwapUsage'),
                    'max' => UtilsMemory::getMemoryUsage('SwapTotal'),
                ],
                'swapCached' => [
                    'value' => UtilsMemory::getMemoryUsage('SwapCached'),
                    'max' => UtilsMemory::getMemoryUsage('SwapUsage'),
                ],
            ],
        ];
    }
}
namespace InnStudio\Prober\Components\Timezone;
final class Timezone
{
    public function __construct()
    {
        if ( ! \ini_get('date.timezone')) {
            date_default_timezone_set('GMT');
        }
    }
}
namespace InnStudio\Prober\Components\Rest;
final class RestResponse
{
    private $data;
    private $headers = [];
    private $status = StatusCode::OK;
    public function __construct($data = null, $status = StatusCode::OK, array $headers = [])
    {
        $this->setData($data);
        $this->setStatus($status);
        $this->setHeaders($headers);
    }
    public function setHeader($key, $value, $replace = true)
    {
        if ($replace || ! isset($this->headers[$key])) {
            $this->headers[$key] = $value;
        } else {
            $this->headers[$key] .= ", {$value}";
        }
    }
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
    public function getHeaders()
    {
        return $this->headers;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    public function getData()
    {
        return $this->data;
    }
    public function json()
    {
        // header('Content-Type: application/json');
        // header('Expires: 0');
        // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        // header('Cache-Control: no-store, no-cache, must-revalidate');
        // header('Pragma: no-cache');
        // echo $this->toJson();
        return $this;
    }
    public function end()
    {
        $this->httpResponseCode($this->status);
        header('Content-Type: application/json');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        if (null !== $this->data) {
            echo $this->toJson();
        }
        exit;
    }
    private function toJson()
    {
        $data = $this->getData();
        if (null === $data) {
            return '';
        }
        return json_encode($data, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }
    private function httpResponseCode($code)
    {
        if (\function_exists('http_response_code')) {
            return http_response_code($code);
        }
        $statusCode = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => "I'm a teapot",
            419 => 'Authentication Timeout',
            420 => 'Enhance Your Calm',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            424 => 'Method Failure',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'No Response',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            451 => 'Unavailable For Legal Reasons',
            494 => 'Request Header Too Large',
            495 => 'Cert Error',
            496 => 'No Cert',
            497 => 'HTTP to HTTPS',
            499 => 'Client Closed Request',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
            598 => 'Network read timeout error',
            599 => 'Network connect timeout error',
        ];
        $msg = isset($statusCode[$code]) ? $statusCode[$code] : 'Unknow error';
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header("{$protocol} {$code} {$msg}");
    }
}
namespace InnStudio\Prober\Components\Rest;
final class StatusCode
{
    const CONTINUE = 100;
    const SWITCHING_PROTOCOLS = 101;
    const PROCESSING = 102; // WEBDAV;_RFC_2518
    const OK = 200;
    const CREATED = 201;
    const ACCEPTED = 202;
    const NON_AUTHORITATIVE_INFORMATION = 203; // SINCE_HTTP/1.1
    const NO_CONTENT = 204;
    const RESET_CONTENT = 205;
    const PARTIAL_CONTENT = 206;
    const MULTI_STATUS = 207; // WEBDAV;_RFC_4918
    const ALREADY_REPORTED = 208; // WEBDAV;_RFC_5842
    const IM_USED = 226; // RFC_3229
    const MULTIPLE_CHOICES = 300;
    const MOVED_PERMANENTLY = 301;
    const FOUND = 302;
    const SEE_OTHER = 303; // SINCE_HTTP/1.1
    const NOT_MODIFIED = 304;
    const USE_PROXY = 305; // SINCE_HTTP/1.1
    const SWITCH_PROXY = 306;
    const TEMPORARY_REDIRECT = 307; // SINCE_HTTP/1.1
    const PERMANENT_REDIRECT = 308; // APPROVED_AS_EXPERIMENTAL_RFC
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const PAYMENT_REQUIRED = 402;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const PROXY_AUTHENTICATION_REQUIRED = 407;
    const REQUEST_TIMEOUT = 408;
    const CONFLICT = 409;
    const GONE = 410;
    const LENGTH_REQUIRED = 411;
    const PRECONDITION_FAILED = 412;
    const REQUEST_ENTITY_TOO_LARGE = 413;
    const REQUEST_URI_TOO_LONG = 414;
    const UNSUPPORTED_MEDIA_TYPE = 415;
    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const EXPECTATION_FAILED = 417;
    const I_AM_A_TEAPOT = 418;
    const AUTHENTICATION_TIMEOUT = 419; // NOT_IN_RFC_2616
    const ENHANCE_YOUR_CALM = 420; // TWITTER
    const METHOD_FAILURE = 420; // SPRING_FRAMEWORK
    const UNPROCESSABLE_ENTITY = 422; // WEBDAV;_RFC_4918
    const LOCKED = 423; // WEBDAV;_RFC_4918
    const FAILED_DEPENDENCY = 424; // WEBDAV
    const UNORDERED_COLLECTION = 425; // INTERNET_DRAFT
    const UPGRADE_REQUIRED = 426; // RFC_2817
    const PRECONDITION_REQUIRED = 428; // RFC_6585
    const TOO_MANY_REQUESTS = 429; // RFC_6585
    const REQUEST_HEADER_FIELDS_TOO_LARGE = 431; // RFC_6585
    const NO_RESPONSE = 444; // NGINX
    const RETRY_WITH = 449; // MICROSOFT
    const BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS = 450; // MICROSOFT
    const REDIRECT = 451; // MICROSOFT
    const UNAVAILABLE_FOR_LEGAL_REASONS = 451; // INTERNET_DRAFT
    const REQUEST_HEADER_TOO_LARGE = 494; // NGINX
    const CERT_ERROR = 495; // NGINX
    const NO_CERT = 496; // NGINX
    const HTTP_TO_HTTPS = 497; // NGINX
    const CLIENT_CLOSED_REQUEST = 499; // NGINX
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED = 501;
    const BAD_GATEWAY = 502;
    const SERVICE_UNAVAILABLE = 503;
    const GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const VARIANT_ALSO_NEGOTIATES = 506; // RFC_2295
    const INSUFFICIENT_STORAGE = 507; // WEBDAV;_RFC_4918
    const LOOP_DETECTED = 508; // WEBDAV;_RFC_5842
    const BANDWIDTH_LIMIT_EXCEEDED = 509; // APACHE_BW/LIMITED_EXTENSION
    const NOT_EXTENDED = 510; // RFC_2774
    const NETWORK_AUTHENTICATION_REQUIRED = 511; // RFC_6585
    const NETWORK_READ_TIMEOUT_ERROR = 598; // UNKNOWN
    const NETWORK_CONNECT_TIMEOUT_ERROR = 599; // Unknown
}
namespace InnStudio\Prober\Components\Poll;
use InnStudio\Prober\Components\Rest\RestResponse;
final class PollAction extends PoolConstants
{
    public function render($action)
    {
        if (PoolConstants::ID !== $action) {
            return;
        }
        $data = [];
        foreach ([
            'Config\\ConfigPoll',
            'UserConfig\\UserConfigPoll',
            'PhpInfo\\PhpInfoPoll',
            'Database\\DatabasePoll',
            'MyInfo\\MyInfoPoll',
            'DiskUsage\\DiskUsagePoll',
            'PhpExtensions\\PhpExtensionsPoll',
            'NetworkStats\\NetworkStatsPoll',
            'ServerStatus\\ServerStatusPoll',
            'ServerInfo\\ServerInfoPoll',
            'Nodes\\NodesPoll',
            'TemperatureSensor\\TemperatureSensorPoll',
            'ServerBenchmark\\ServerBenchmarkPoll',
            'BrowserBenchmark\\BrowserBenchmarkPoll',
            'Ping\\PingPoll',
        ] as $fn) {
            $class = "\\InnStudio\\Prober\\Components\\{$fn}";
            $data = array_merge($data, (new $class())->render());
        }
        (new RestResponse())
            ->setData($data)
            ->end();
    }
}
namespace InnStudio\Prober\Components\Poll;
class PoolConstants
{
    const ID = 'poll';
}
namespace InnStudio\Prober\Components\MyInfo;
class MyInfoConstants
{
    const ID = 'myInfo';
}
namespace InnStudio\Prober\Components\MyInfo;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsClientIp;
final class MyInfoPoll
{
    public function render()
    {
        $id = MyInfoConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'phpLanguage' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '-',
                'ipv4' => UtilsClientIp::getV4(),
                'ipv6' => UtilsClientIp::getV6(),
            ],
        ];
    }
}
namespace InnStudio\Prober\Components\Bootstrap;
use InnStudio\Prober\Components\Action\Action;
use InnStudio\Prober\Components\Timezone\Timezone;
final class Bootstrap
{
    public static $dir;
    public function __construct($dir)
    {
        error_reporting(\E_ALL);
        self::$dir = $dir;
        new Timezone();
        new Action();
        new Render();
    }
}
namespace InnStudio\Prober\Components\Bootstrap;
class BootstrapConstants
{
    const ID = 'bootstrap';
}
namespace InnStudio\Prober\Components\Bootstrap;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\WindowConfig\WindowConfigApi;
final class Render
{
    public function __construct()
    {
        if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            return;
        }
        $appName = ConfigApi::$config['APP_NAME'];
        $version = ConfigApi::$config['APP_VERSION'];
        $loadScript = \defined('XPROBER_IS_DEV') && XPROBER_IS_DEV ? '' : "<script src='?action=script&amp;v={$version}'></script>";
        $loadStyle = \defined('XPROBER_IS_DEV') && XPROBER_IS_DEV ? '' : "<link rel='stylesheet' href='?action=style&amp;v={$version}'>";
        $globalConfig = WindowConfigApi::getGlobalConfig();
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="renderer" content="webkit">
<title>{$appName} {$version}</title>
{$globalConfig}
{$loadScript}
<style>
:root {
    --x-init-fg: hsl(0 0% 10%);
    --x-init-body-fg: hsl(0 0% 10%);
    --x-init-body-bg: hsl(0 0% 90%);
    --x-init-loading-bg: hsl(0 0% 90%);
    --x-init-loading-fg: hsl(0 0% 10%);
    @media (prefers-color-scheme: dark) {
        --x-init-fg: hsl(0 0% 90%);
        --x-init-body-fg: hsl(0 0% 90%);
        --x-init-body-bg: hsl(0 0% 0%);
        --x-init-loading-bg: hsl(0 0% 0%);
        --x-init-loading-fg: hsl(0 0% 90%);
    }
}
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
body {
    gap: var(--x-init-gutter);
    background: var(--x-init-body-bg);
    color: var(--x-init-body-fg);
    line-height: 1.5;
    padding:0;
    margin:0;
}
#loading {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5em;
    height: 100svh;
    font-family: monospace;
}
#loading::before {
    animation: spin 1s linear infinite;
    box-sizing: border-box;
    border: 1px solid var(--x-init-loading-bg);
    border-top-color: var(--x-init-loading-fg);
    border-radius: 50%;
    width: 16px;
    height: 16px;
    content: "";
}
</style>
{$loadStyle}
</head>
<body>
<div id=loading>Loading...</div>
</div>
</body>
</html>
HTML;
    }
}
namespace InnStudio\Prober\Components\Style;
use InnStudio\Prober\Components\Utils\UtilsApi;
final class StyleAction
{
    public function render($action)
    {
        if ('style' !== $action) {
            return;
        }
        $this->output();
    }
    private function output()
    {
        UtilsApi::setFileCacheHeader();
        header('Content-type: text/css');
        echo <<<'CODE'
@charset "UTF-8";:root{--x-max-width: 1680px;--x-radius: .5rem;--x-fg: hsl(0, 0%, 20%);--x-bg: hsl(0, 0%, 97%);--x-text-font-family: Verdana, Geneva, Tahoma, sans-serif;--x-code-font-family: monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New";--x-app-border-color: var(--x-fg);--x-app-bg: var(--x-bg);--x-star-me-fg: var(--x-bg);--x-star-me-bg: var(--x-fg);--x-star-me-hover-fg: hsl(0, 0%, 100%);--x-star-me-hover-bg: var(--x-fg);--x-star-me-border-color: linear-gradient(90deg, transparent, hsl(0, 0%, 100%), transparent);--x-status-ok-fg: hsl(0, 0%, 100%);--x-status-ok-bg: linear-gradient(hsl(120, 100%, 30%), hsl(120, 100%, 45%));--x-status-error-fg: hsl(0, 0%, 100%);--x-status-error-bg: linear-gradient(hsl(0, 0%, 50%), hsl(0, 0%, 73%));--x-network-node-fg: var(--x-fg);--x-network-node-bg: hsla(132, 4%, 23%, .1);--x-network-node-border-color: var(--x-card-split-color);--x-network-node-row-bg: linear-gradient(to right, transparent, hsla(0, 0%, 100%, .5), transparent)}:root{--x-gutter: 1rem;--x-gutter-sm: .5rem}@media (prefers-color-scheme: dark){:root{--x-fg: hsl(0, 0%, 80%);--x-bg: hsl(0, 0%, 0%);--x-app-border-color: var(--x-bg);--x-app-bg: hsl(0, 0%, 13%);--x-star-me-fg: var(--x-fg);--x-star-me-bg: var(--x-bg);--x-star-me-hover-fg: hsl(0, 0%, 100%);--x-star-me-hover-bg: var(--x-bg);--x-star-me-border-color: linear-gradient(90deg, transparent, hsl(0, 0%, 100%), transparent);--x-status-ok-fg: hsl(0, 0%, 100%);--x-status-ok-bg: linear-gradient(hsl(120, 100%, 20%), hsl(120, 100%, 25%));--x-status-error-fg: hsl(0, 0%, 100%);--x-status-error-bg: linear-gradient(hsl(0, 0%, 27%), hsl(0, 0%, 33%));--x-network-node-fg: var(--x-fg);--x-network-node-bg: hsla(0, 0%, 100%, .05);--x-network-node-border-color: var(--x-card-split-color);--x-network-node-row-bg: var(--x-card-bg-hover)}}:root{--x-footer-bg: hsl(0 0% 0% / .05);--x-footer-fg: hsl(0 0% 0% / .5)}@media (prefers-color-scheme: dark){:root{--x-footer-bg: hsl(0 0% 100% / .1);--x-footer-fg: hsl(0 0% 100% / .5)}}._main_17cch_12{width:100%;color:var(--x-footer-fg);text-align:center;word-break:normal}._main_17cch_12 a,._main_17cch_12 a:hover{color:var(--x-footer-fg)}:root{--x-header-fg: hsl(0 0% 0% / .9);--x-header-bg: transparent;--x-header-link-bg: hsl(0 0% 0% / .1);--x-header-link-bg-hover: hsl(0 0% 0% / .15)}@media (prefers-color-scheme: dark){:root{--x-header-fg: hsl(0 0% 100% / .9);--x-header-bg: hsl(0 0% 100% / .1);--x-header-link-bg: hsl(0 0% 100% / .1);--x-header-link-bg-hover: hsl(0 0% 100% / .15)}}._main_1jpdc_16{display:flex;justify-content:center;padding-top:var(--x-gutter)}:root{--x-link-fg: hsl(0 0% 95% / .95);--x-link-bg: hsl(0 0% 15% / .95);--x-link-bg-hover: hsl(0 0% 20% / .95);--x-link-bg-active: hsl(0 0% 25% / .95)}@media (prefers-color-scheme: dark){:root{--x-link-fg: hsl(0 0% 100% / .95);--x-link-bg: hsl(0 0% 10% / .95);--x-link-bg-hover: hsl(0 0% 15% / .95);--x-link-bg-active: hsl(0 0% 20% / .95)}}._main_p5526_16{display:flex;align-items:center;gap:var(--x-gutter-sm);cursor:pointer;border:none;border-radius:10rem;background:var(--x-link-bg);padding:var(--x-gutter-sm) var(--x-gutter);color:var(--x-link-fg);text-decoration:none}._main_p5526_16:hover{background:var(--x-link-bg-hover);color:var(--x-link-fg);text-decoration:none}._main_p5526_16:active{background:var(--x-link-bg-active);color:var(--x-link-fg);text-decoration:none}._main_1k8xz_1{font-weight:400;font-size:1rem}._name_1k8xz_6{font-weight:700}._version_1k8xz_10{opacity:.75;font-weight:400;font-size:.8em}:root{--x-toast-fg: hsl(0 0% 100% / .95);--x-toast-bg: hsl(0 0% 0% / .75)}@media (prefers-color-scheme: dark){:root{--x-toast-fg: hsl(0 0% 100% / .95);--x-toast-bg: hsl(0 0% 100% / .15)}}._main_17sik_12{position:fixed;bottom:4rem;left:50%;transform:translate(-50%);z-index:20;backdrop-filter:blur(5px);cursor:pointer;border:none;border-radius:var(--x-gutter);background:var(--x-toast-bg);padding:var(--x-gutter);max-width:80vw;color:var(--x-toast-fg);text-align:justify}:root{--x-fg: hsl(0 0% 10%);--x-body-fg: hsl(0 0% 10%);--x-body-bg: hsl(0 0% 90%)}@media (prefers-color-scheme: dark){:root{--x-fg: hsl(0 0% 90%);--x-body-fg: hsl(0 0% 90%);--x-body-bg: hsl(0 0% 0%)}}*{box-sizing:border-box;margin:0;padding:0;word-break:break-word}html{scroll-behavior:smooth;font-size:85%}body{display:grid;place-content:safe center;vertical-align:middle;gap:var(--x-gutter);margin:0;background:var(--x-body-bg);padding:0;color:var(--x-body-fg);line-height:1.5;font-family:var(--x-code-font-family)}a{cursor:pointer;color:var(--x-fg);text-decoration:none}a:hover,a:active{color:var(--x-fg);text-decoration:underline}._container_30sck_1{display:grid;gap:var(--x-gutter);max-width:1200px}:root{--x-card-legend-arrow-fg: var(--x-card-legend-fg);--x-card-legend-arrow-bg-hover: hsl(0 0% 0% / .05);--x-card-legend-arrow-bg-active: hsl(0 0% 0% / .1)}@media (prefers-color-scheme: dark){:root{--x-card-legend-arrow-fg: var(--x-card-legend-fg);--x-card-legend-arrow-bg-hover: hsl(0 0% 100% / .05);--x-card-legend-arrow-bg-active: hsl(0 0% 100% / .1)}}._arrow_1qtu9_14{display:flex;align-items:center;cursor:pointer;border:none;border-radius:var(--x-radius);background:transparent;padding:var(--x-gutter-sm);color:var(--x-card-legend-arrow-fg)}._arrow_1qtu9_14:hover{background:var(--x-card-legend-arrow-bg-hover);color:var(--x-card-legend-arrow-fg)}._arrow_1qtu9_14:active{background:var(--x-card-legend-arrow-bg-active);color:var(--x-card-legend-arrow-fg)}._arrow_1qtu9_14[data-disabled],._arrow_1qtu9_14[data-disabled]:hover{opacity:.5;cursor:not-allowed}._arrow_1qtu9_14 svg{width:1rem;height:1rem}:root{--x-module-bg: hsl(0 0% 0% / .95);--x-module-header-bg: hsl(0 0% 100% / .75);--x-module-header-fg: hsl(0 0% 0%);--x-module-header-title-fg: hsl(0 0% 0% / .7);--x-module-header-title-bg: hsl(0 0% 0% / .1);--x-module-body-bg: var(--x-module-header-bg);--x-module-box-shadow: hsla(0 0% 20% .3) 0px -1px 0px hsl(0 0% 100%) 0px 1px 0px inset, hsla(0 0% 20% .3) 0px -1px 0px inset hsl(0 0% 100%) 0px 1px 0px}@media (prefers-color-scheme: dark){:root{--x-module-bg: hsl(0 0% 15% / .95);--x-module-header-bg: hsl(0 0% 100% / .1);--x-module-header-fg: hsl(0 0% 100% / .7);--x-module-header-title-fg: hsl(0 0% 100% / .7);--x-module-header-title-bg: hsl(0 0% 100% / .1);--x-module-body-bg: var(--x-module-header-bg);--x-module-box-shadow: 0px 0px 0px 1px hsl(0 0% 0%) inset}}._main_60fl9_23{position:relative;flex-grow:1;scroll-margin-top:0}._header_60fl9_29{display:flex;align-items:center;border-radius:var(--x-radius) var(--x-radius) 0 0;background:var(--x-module-header-bg);padding:1px;width:fit-content;color:var(--x-module-header-fg);font-size:1rem;white-space:nowrap}._title_60fl9_41{font-weight:400}._body_60fl9_45{display:grid;gap:var(--x-gutter-sm);border-radius:0 var(--x-radius) var(--x-radius) var(--x-radius);background:var(--x-module-body-bg);padding:var(--x-gutter)}:root{--x-card-des-fg: var(--x-fg);--x-card-des-bg: hsl(0 0% 100% / .1);--x-card-des-accent: hsl(0 0% 0% / .5)}@media (prefers-color-scheme: dark){:root{--x-card-des-fg: var(--x-fg);--x-card-des-bg: hsl(0 0% 100% / .1);--x-card-des-accent: hsl(209, 100%, 63%)}}._main_1hf64_14{display:grid;border-radius:var(--x-radius);color:var(--x-card-des-fg);font-family:var(--x-text-font-family);list-style-type:none}._item_1hf64_22{display:flex;align-items:center;gap:var(--x-gutter-sm)}._item_1hf64_22:before{border-radius:var(--x-radius);background:var(--x-card-des-accent);width:2px;height:50%;content:""}:root{--x-nav-fg: hsl(0 0% 100% / .9);--x-nav-bg: hsl(0 0% 15% / .95);--x-nav-bg-hover: hsl(0 0% 100% / .05);--x-nav-bg-active: hsl(0 0% 100% / .1);--x-nav-border-color: hsl(0 0% 100% / .05)}@media (prefers-color-scheme: dark){:root{--x-nav-fg: hsl(0 0% 95% / .95);--x-nav-bg: hsl(0 0% 20% / .95);--x-nav-bg-hover: hsl(0 0% 25% / .95);--x-nav-bg-active: hsl(0 0% 30% / .95);--x-nav-border-color: hsl(0 0% 100% / .05)}}._main_1ygx7_18{display:flex;position:sticky;bottom:0;justify-content:flex-start;align-items:center;z-index:10;background:var(--x-nav-bg);overflow-x:auto}@media (min-width: 768px){._main_1ygx7_18{justify-content:center;border-radius:var(--x-radius) var(--x-radius) 0 0}}._link_1ygx7_35{position:relative;border-right:1px solid var(--x-nav-border-color);padding:var(--x-gutter);color:var(--x-nav-fg);white-space:nowrap}._link_1ygx7_35:hover{background:var(--x-nav-bg-hover);color:var(--x-nav-fg);text-decoration:none}._link_1ygx7_35:focus,._link_1ygx7_35:active,._link_1ygx7_35[data-active]{background:var(--x-nav-bg-active);color:var(--x-nav-fg);text-decoration:none}._link_1ygx7_35:last-child{border-right:0}:root{--x-card-group-label-fg: var(--x-fg);--x-card-group-split-color: hsl(0 0% 0% / .1);--x-card-group-bg-hover: hsl(0 0% 0% / .05)}@media (prefers-color-scheme: dark){:root{--x-card-group-label-fg: var(--x-fg);--x-card-group-split-color: hsl(0 0% 100% / .1);--x-card-group-bg-hover: hsl(0 0% 100% / .05)}}._main_11zmy_14{display:grid;grid-template-columns:minmax(var(--min-width),var(--max-width)) 1fr;gap:var(--x-gutter-sm);border-radius:var(--x-radius)}._main_11zmy_14:hover{background:var(--x-card-group-bg-hover)}._label_11zmy_24{color:var(--x-card-group-label-fg);font-family:var(--x-text-font-family);text-align:right;word-break:normal}._label_11zmy_24:after{content:":"}._content_11zmy_34{display:flex;flex-wrap:wrap;align-items:flex-start;gap:var(--x-gutter-sm)}._main_z8p86_1{display:grid;grid-template-columns:repeat(auto-fill,minmax(var(--min-width),1fr));gap:var(--x-gutter-sm)}._main_xo4z4_2{display:inline-flex;border-radius:var(--x-radius);align-items:center;justify-content:center;font-family:Arial Black,sans-serif;font-weight:bolder;min-width:2em;padding:0 .5rem;white-space:nowrap;cursor:pointer;text-shadow:0 1px 1px #000}._main_xo4z4_2:active{transform:scale3d(.95,.95,1)}._main_xo4z4_2[data-ok]{background:var(--x-status-ok-bg);color:var(--x-status-ok-fg)}._main_xo4z4_2[data-error]{background:var(--x-status-error-bg);color:var(--x-status-error-fg)}._main_xo4z4_2[data-ok][data-icon]:before{content:"✓"}._main_xo4z4_2[data-error][data-icon]:before{content:"×"}:root{--x-meter-height: 2px;--x-meter-bar-bg: hsl(0 0% 0% / .1);--x-meter-value-bg: hsl(120 100% 40%);--x-meter-value-optimum-bg: hsl(120 100% 30%);--x-meter-value-suboptimum-bg: hsl(36 77% 64%);--x-meter-value-even-less-good-bg: hsl(12 100% 39%)}@media (prefers-color-scheme: dark){:root{--x-meter-bar-bg: hsl(0 0% 100% / .1);--x-meter-value-optimum-bg: hsl(120 100% 30%);--x-meter-value-suboptimum-bg: hsl(36 77% 54%);--x-meter-value-even-less-good-bg: hsl(12 100% 39%)}}._main_1isor_18{display:grid;grid-template-columns:1fr auto;grid-template-areas:"x-meter-name x-meter-percent" "x-meter-name x-meter-overview" "x-meter-core x-meter-core ";gap:var(--x-gutter-sm)}._percent_1isor_25{grid-area:x-meter-percent;text-align:right}._name_1isor_30{display:flex;grid-area:x-meter-name;align-items:center;border:none;background:none;color:var(--x-bg-fg);font-weight:700;text-align:center}._nameText_1isor_41{display:-webkit-box;-webkit-box-orient:vertical;max-width:15rem;-webkit-line-clamp:2;overflow:hidden}._overview_1isor_49{grid-area:x-meter-overview}._core_1isor_53{grid-area:x-meter-core;background:none;width:100%;height:var(--x-meter-height)}._core_1isor_53::-webkit-meter-bar{border-radius:10rem;background:var(--x-meter-bar-bg);height:var(--x-meter-height)}._core_1isor_53::-webkit-meter-optimum-value{border-radius:10rem;background:var(--x-meter-value-optimum-bg)}._core_1isor_53::-webkit-meter-suboptimum-value{border-radius:10rem;background:var(--x-meter-value-suboptimum-bg)}._core_1isor_53::-webkit-meter-even-less-good-value{border-radius:10rem;background:var(--x-meter-value-even-less-good-bg)}._main_b4lx8_1{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter)}:root{--x-button-fg: var(--x-fg);--x-button-bg: hsl(0 0% 0% / .1);--x-button-fg-hover: var(--x-fg);--x-button-bg-hover: hsl(0 0% 0% / .15);--x-button-fg-active: var(--x-fg);--x-button-bg-active: hsl(0 0% 0% / .2)}@media (prefers-color-scheme: dark){:root{--x-button-fg: var(--x-fg);--x-button-bg: hsl(0 0% 100% / .1);--x-button-fg-hover: var(--x-fg);--x-button-bg-hover: hsl(0 0% 100% / .15);--x-button-fg-active: var(--x-fg);--x-button-bg-active: hsl(0 0% 100% / .2)}}@keyframes _spin_1shxn_1{to{transform:rotate(360deg)}}._button_1shxn_25{display:flex;align-items:center;gap:.25em;cursor:pointer;border:none;border-radius:var(--x-radius);background:var(--x-button-bg);padding:0 var(--x-gutter-sm);color:var(--x-button-fg);font-family:var(--x-text-font-family);text-decoration:none}._button_1shxn_25:hover{background:var(--x-button-bg-hover);color:var(--x-button-fg-hover);text-decoration:none}._button_1shxn_25:active{background:var(--x-button-bg-active);color:var(--x-button-fg-active);text-decoration:none}._icon_1shxn_49{display:grid;place-content:center;aspect-ratio:1/1;width:1rem}._icon_1shxn_49 svg{width:1rem;height:1rem}._icon_1shxn_49[data-status=loading]{animation:_spin_1shxn_1 1s linear infinite}._main_mc2kq_1{display:grid;gap:var(--x-gutter-sm)}._container_1i47d_2{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter)}._item_1i47d_8{display:grid}._id_1i47d_12{text-align:center;text-decoration:underline}._idRow_1i47d_17{display:grid;align-items:center}._dataContainer_1i47d_22{display:flex;justify-content:center;align-items:center;text-align:center}._data_1i47d_22{flex:0 0 50%}._data_1i47d_22[data-rx]{color:var(--x-network-stats-rx-fg)}._data_1i47d_22[data-tx]{color:var(--x-network-stats-tx-fg)}._rate_1i47d_39{font-family:Arial Black,sans-serif}._rate_1i47d_39:before{margin-right:.5rem}._rateRx_1i47d_46:before{content:"▼"}._rateTx_1i47d_50:before{content:"▲"}:root{--x-network-stats-tx-fg: hsl(23 100% 38%);--x-network-stats-tx-bg: hsl(23 100% 38% / .1);--x-network-stats-rx-fg: hsl(120 100% 23%);--x-network-stats-rx-bg: hsl(120 100% 23% / .1)}@media (prefers-color-scheme: dark){:root{--x-network-stats-tx-fg: hsl(23 100% 58%);--x-network-stats-tx-bg: hsl(23 100% 58% /.15);--x-network-stats-rx-fg: hsl(120 100% 43%);--x-network-stats-rx-bg: hsl(120 100% 43% / .15)}}._main_1cyw0_17{display:grid;grid-template-areas:"network-stats-item-id network-stats-item-id" "network-stats-item-rx network-stats-item-tx";gap:1px;font-family:Arial Black,sans-serif}._id_1cyw0_24{grid-area:network-stats-item-id;text-align:center}._type_1cyw0_29:before{opacity:.5;content:"▼";font-size:1rem}._rx_1cyw0_35,._tx_1cyw0_36{display:grid;position:relative;grid-area:network-stats-item-rx;border-radius:var(--x-radius) 0 0 var(--x-radius);background:var(--x-network-stats-rx-bg);padding:var(--x-gutter-sm);color:var(--x-network-stats-rx-fg);text-align:center}._tx_1cyw0_36{grid-area:network-stats-item-tx;border-radius:0 var(--x-radius) var(--x-radius) 0;background:var(--x-network-stats-tx-bg);color:var(--x-network-stats-tx-fg)}._tx_1cyw0_36 ._type_1cyw0_29:before{content:"▲"}._rateRx_1cyw0_57,._rateTx_1cyw0_58{font-weight:700;font-size:1.5rem}._main_zmhfm_1{display:grid;grid-template-columns:repeat(auto-fill,minmax(20rem,1fr));gap:var(--x-gutter)}._groupId_zmhfm_7{display:block;margin-bottom:calc(var(--x-gutter) * .5);text-align:center;text-decoration:underline}._groupId_zmhfm_7:hover{text-decoration:none}._group_zmhfm_7{margin-bottom:calc(var(--x-gutter) * .5)}._groupMsg_zmhfm_21{display:flex;justify-content:center}._groupNetworks_zmhfm_26{margin-bottom:var(--x-gutter);border-radius:var(--x-radius);background:var(--x-network-node-bg);padding:var(--x-gutter);color:var(--x-network-node-fg)}._groupNetwork_zmhfm_26{margin-bottom:calc(var(--x-gutter) * .5);border-bottom:1px dashed var(--x-network-node-border-color);padding-bottom:calc(var(--x-gutter) * .5)}._groupNetwork_zmhfm_26:last-child{margin-bottom:0;border-bottom:0;padding-bottom:0}._groupNetwork_zmhfm_26:hover{background:var(--x-network-node-row-bg)}:root{--x-placeholder-bg: linear-gradient(to right, hsl(0 0% 0% / .1) 46%, hsl(0 0% 0% / .15) 50%, hsl(0 0% 0% / .1) 54%) 50% 50%}@media (prefers-color-scheme: dark){:root{--x-placeholder-bg: linear-gradient( to right, hsl(0 0% 100% / .1) 46%, hsl(0 0% 100% / .15) 50%, hsl(0 0% 100% / .1) 54% ) 50% 50%}}@keyframes _animation_vvbro_1{0%{transform:translate3d(-30%,0,0)}to{transform:translate3d(30%,0,0)}}._main_vvbro_25{position:relative;border-radius:var(--x-radius);overflow:hidden}._main_vvbro_25:before{position:absolute;inset:0 0 0 50%;z-index:1;animation:_animation_vvbro_1 1s linear infinite;margin-left:-250%;background:var(--x-placeholder-bg);width:500%;pointer-events:none;content:" "}:root{--x-error-fg: hsl(0 100% 50%);--x-error-bg: hsl(0 100% 30%);--x-error-icon-fg: hsl(0 100% 50%);--x-error-icon-bg: hsl(0 100% 97%)}@media (prefers-color-scheme: dark){:root{--x-error-fg: hsl(0 0% 100% / .9);--x-error-bg: hsl(0, 100%, 50%);--x-error-icon-fg: var(--x-error-bg);--x-error-icon-bg: hsl(0 0% 100% / .5)}}._main_1ogv8_16{display:flex;position:relative;align-items:center;gap:var(--x-gutter-sm);border-radius:var(--x-radius);color:var(--x-error-fg);font-family:var(--x-text-font-family)}._main_1ogv8_16:before{border-radius:var(--x-radius);background:var(--x-error-bg);width:2px;height:50%;content:""}:root{--x-sys-load-fg: var(--x-fg);--x-sys-load-bg: transparent;--x-sys-load-interval-bg: hsl(0 0% 0% / .1)}@media (prefers-color-scheme: dark){:root{--x-sys-load-fg: var(--x-fg);--x-sys-load-interval-bg: hsl(0 0% 100% / .1)}}._main_1xqpo_13{display:grid;grid-template-columns:1fr auto;grid-template-areas:"x-server-stats-system-load-label x-server-stats-system-load-usage" "x-server-stats-system-load-label x-server-stats-system-load-group" "x-server-stats-system-load-meter x-server-stats-system-load-meter";gap:var(--x-gutter-sm)}._label_1xqpo_20{display:grid;grid-area:x-server-stats-system-load-label;align-items:center;font-weight:700}._meter_1xqpo_27{display:grid;grid-template-areas:"x-meter-core";grid-area:x-server-stats-system-load-meter}._usage_1xqpo_33{grid-area:x-server-stats-system-load-usage;text-align:right}._group_1xqpo_38{display:flex;grid-area:x-server-stats-system-load-group;align-items:center;gap:var(--x-gutter-sm)}._groupItem_1xqpo_45{border-radius:var(--x-radius);background:var(--x-sys-load-interval-bg);padding:0 var(--x-gutter);color:var(--x-sys-load-fg);font-weight:700;font-family:Arial Black,sans-serif,monospace}._sysLoad_mqy5s_1{display:flex;gap:var(--x-gutter-sm)}._main_66xvd_1{display:grid;grid-template-columns:1fr auto;grid-template-areas:"x-nodes-usage-label x-nodes-usage-label" "x-nodes-usage-overview x-nodes-usage-percent" "x-nodes-usage-meter x-nodes-usage-meter";column-gap:var(--x-gutter-sm);row-gap:0;gap:var(--x-gutter-sm)}._meter_66xvd_10{display:flex;grid-area:x-nodes-usage-meter;height:var(--x-meter-height)}._label_66xvd_16{grid-area:x-nodes-usage-label}._overview_66xvd_20{display:flex;grid-area:x-nodes-usage-overview;gap:var(--x-gutter-sm)}._chart_66xvd_26{display:none;grid-area:x-nodes-usage-chart}._percent_66xvd_31{grid-area:x-nodes-usage-percent}._main_1gdd5_1{display:grid;gap:var(--x-gutter-sm);container-type:inline-size;max-height:calc(100px + var(--x-gutter-sm));overflow-y:auto;overscroll-behavior:contain;scroll-snap-type:y mandatory;scrollbar-color:hsla(0,0%,50%,.5) transparent}._item_1gdd5_12{scroll-snap-align:start}._main_mc2kq_1,._main_18siw_1{display:grid;gap:var(--x-gutter-sm)}._name_18siw_6{text-align:center}._loading_18siw_10{display:grid;place-content:center center;height:10rem}:root{--x-search-fg: var(--x-fg);--x-search-bg: hsl(0 0% 0% / .1);--x-search-bg-hover: hsl(0 0% 0% / .15);--x-search-bg-active: hsl(0 0% 0% / .2)}@media (prefers-color-scheme: dark){:root{--x-search-fg: var(--x-fg);--x-search-bg: hsl(0 0% 100% / .1);--x-search-bg-hover: hsl(0 0% 100% / .15);--x-search-bg-active: hsl(0 0% 100% / .2)}}._main_uj7jp_16{border-radius:var(--x-radius);background:var(--x-search-bg);padding:calc(var(--x-gutter-sm) * .5) var(--x-gutter-sm);color:var(--x-search-fg);font-family:monospace}._main_uj7jp_16:hover{background:var(--x-search-bg-hover);text-decoration:none}._main_uj7jp_16:active{background:var(--x-search-bg-active)}:root{--x-ping-result-scrollbar-bg: hsl(0 0% 0% / .5);--x-ping-item-bg: hsl(0 0% 0% / .1)}@media (prefers-color-scheme: dark){:root{--x-ping-result-scrollbar-bg: hsl(0 0% 100% / .5);--x-ping-item-bg: hsl(0 0% 100% / .1)}}._itemContainer_y6c35_12{display:grid;grid-template-columns:repeat(auto-fill,minmax(5rem,1fr));grid-auto-flow:row;flex-grow:1;gap:.15em;border-radius:var(--x-radius);background:var(--x-ping-item-bg);padding:var(--x-gutter-sm) var(--x-gutter);height:7rem;overflow-y:auto;scrollbar-color:var(--x-ping-result-scrollbar-bg) transparent;list-style-type:none}._resultContainer_y6c35_27{display:grid;flex-grow:1;gap:var(--x-gutter-sm)}._result_y6c35_27{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center}._btn_1dtle_1{display:block}._serversLoading_1dtle_5{display:grid;justify-content:center;align-items:center;height:5rem}._servers_1dtle_5{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter-sm)}:root{--x-benchmark-ruby-bg: hsl(0 0% 0% / .05);--x-benchmark-ruby-bg-hover: hsl(0 0% 0% / .05)}@media (prefers-color-scheme: dark){:root{--x-benchmark-ruby-bg: hsl(0 0% 100% / .05);--x-benchmark-ruby-bg-hover: hsl(0 0% 100% / .1)}}._main_1j8ow_12 rt{opacity:.5;font-weight:400;font-size:1rem}._main_1j8ow_12[data-is-result]{font-weight:700}._main_fajqi_1{display:flex}:root{--x-server-benchmark-bg: transparent;--x-server-benchmark-link-bg: hsl(0 0% 0% / .05);--x-server-benchmark-link-fg: hsl(0 0% 0% / .95)}@media (prefers-color-scheme: dark){:root{--x-server-benchmark-link-fg: hsl(0 0% 100% / .95);--x-server-benchmark-link-bg: hsl(0 0% 100% / .05)}}._main_1e6oe_13{display:grid;gap:var(--x-gutter-sm);border-radius:var(--x-radius);background:var(--x-server-benchmark-bg);padding:var(--x-gutter-sm);text-align:center}._header_1e6oe_22{display:flex;justify-content:center;align-items:center}._link_1e6oe_28{opacity:.75;cursor:pointer;border:none;border-radius:var(--x-radius);background:none;padding:0 var(--x-gutter-sm)}._link_1e6oe_28:hover,._link_1e6oe_28:active{opacity:1;background:var(--x-server-benchmark-link-bg);text-decoration:none}._link_1e6oe_28 svg{width:1rem;height:1rem}._marks_1e6oe_46{display:flex;justify-content:center;align-items:center;gap:var(--x-gutter-sm);cursor:pointer;border:none;border-radius:var(--x-radius);background-color:transparent;color:var(--x-server-benchmark-link-fg);font-size:1.25rem}._marks_1e6oe_46:hover{background:var(--x-server-benchmark-link-bg)}._sign_1e6oe_62{opacity:.5}._main_raw5t_1{display:grid;gap:var(--x-gutter-sm)}._modules_raw5t_6{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:var(--x-gutter)}@keyframes _spin_nuyl9_1{to{transform:rotate(360deg)}}._main_nuyl9_6{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:.5em;height:100svh}._main_nuyl9_6:before{animation:_spin_nuyl9_1 1s linear infinite;box-sizing:border-box;border:1px solid var(--x-button-bg);border-top-color:var(--x-button-fg);border-radius:50%;width:16px;height:16px;content:""}

CODE;
        exit;
    }
}
namespace InnStudio\Prober\Components\TemperatureSensor;
class TemperatureSensorConstants
{
    const ID = 'temperatureSensor';
}
namespace InnStudio\Prober\Components\TemperatureSensor;
use Exception;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class TemperatureSensorPoll
{
    public function render()
    {
        $id = TemperatureSensorConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        $items = $this->getItems();
        if ( ! $items) {
            return [
                $id => null,
            ];
        }
        if ($items) {
            return [
                $id => $items,
            ];
        }
        $cpuTemp = $this->getCpuTemp();
        if ( ! $cpuTemp) {
            return [
                $id => null,
            ];
        }
        $items[] = [
            'id' => 'cpu',
            'name' => 'CPU',
            'celsius' => round((float) $cpuTemp / 1000, 2),
        ];
        return [
            $id => $items,
        ];
    }
    private function curl($url)
    {
        if ( ! \function_exists('curl_init')) {
            return (string) file_get_contents($url);
        }
        $ch = curl_init();
        curl_setopt_array($ch, [
            \CURLOPT_URL => $url,
            \CURLOPT_RETURNTRANSFER => true,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return (string) $res;
    }
    private function getItems()
    {
        $items = [];
        foreach (ConfigApi::$config['APP_TEMPERATURE_SENSOR_PORTS'] as $port) {
            // check curl
            $res = $this->curl(ConfigApi::$config['APP_TEMPERATURE_SENSOR_URL'] . ":{$port}");
            if ( ! $res) {
                continue;
            }
            $item = json_decode($res, true);
            if ( ! $item || ! \is_array($item)) {
                continue;
            }
            $items = $item;
            break;
        }
        return $items;
    }
    private function getCpuTemp()
    {
        try {
            $path = '/sys/class/thermal/thermal_zone0/temp';
            return file_exists($path) ? (int) file_get_contents($path) : 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}
namespace InnStudio\Prober\Components\NetworkStats;
class NetworkStatsConstants
{
    const ID = 'networkStats';
}
namespace InnStudio\Prober\Components\NetworkStats;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsApi;
use InnStudio\Prober\Components\Utils\UtilsNetwork;
final class NetworkStatsPoll
{
    public function render()
    {
        $id = NetworkStatsConstants::ID;
        if (UtilsApi::isWin() || UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'networks' => UtilsNetwork::getStats(),
                'timestamp' => time(),
            ],
        ];
    }
}
namespace InnStudio\Prober\Components\Script;
use InnStudio\Prober\Components\Utils\UtilsApi;
final class ScriptAction
{
    public function render($action)
    {
        if ('script' !== $action) {
            return;
        }
        $this->output();
    }
    private function output()
    {
        UtilsApi::setFileCacheHeader();
        header('Content-type: application/javascript');
        echo <<<'CODE'
(function(mr){typeof define=="function"&&define.amd?define(mr):mr()})(function(){"use strict";function mr(i){return i&&i.__esModule&&Object.prototype.hasOwnProperty.call(i,"default")?i.default:i}var Ff={exports:{}},vr={},ed={exports:{}},yr={exports:{}};yr.exports;var zv;function Y1(){return zv||(zv=1,function(i,r){(function(){function u(m,w){Object.defineProperty(h.prototype,m,{get:function(){console.warn("%s(...) is deprecated in plain JavaScript React classes. %s",w[0],w[1])}})}function s(m){return m===null||typeof m!="object"?null:(m=vi&&m[vi]||m["@@iterator"],typeof m=="function"?m:null)}function f(m,w){m=(m=m.constructor)&&(m.displayName||m.name)||"ReactClass";var P=m+"."+w;Ur[P]||(console.error("Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",w,m),Ur[P]=!0)}function h(m,w,P){this.props=m,this.context=w,this.refs=Nr,this.updater=P||Eo}function p(){}function b(m,w,P){this.props=m,this.context=w,this.refs=Nr,this.updater=P||Eo}function g(m){return""+m}function N(m){try{g(m);var w=!1}catch{w=!0}if(w){w=console;var P=w.error,G=typeof Symbol=="function"&&Symbol.toStringTag&&m[Symbol.toStringTag]||m.constructor.name||"Object";return P.call(w,"The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",G),g(m)}}function U(m){if(m==null)return null;if(typeof m=="function")return m.$$typeof===ah?null:m.displayName||m.name||null;if(typeof m=="string")return m;switch(m){case H:return"Fragment";case F:return"Profiler";case ae:return"StrictMode";case Ce:return"Suspense";case mi:return"SuspenseList";case Nn:return"Activity"}if(typeof m=="object")switch(typeof m.tag=="number"&&console.error("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),m.$$typeof){case De:return"Portal";case Ke:return(m.displayName||"Context")+".Provider";case Le:return(m._context.displayName||"Context")+".Consumer";case ut:var w=m.render;return m=m.displayName,m||(m=w.displayName||w.name||"",m=m!==""?"ForwardRef("+m+")":"ForwardRef"),m;case et:return w=m.displayName||null,w!==null?w:U(m.type)||"Memo";case xt:w=m._payload,m=m._init;try{return U(m(w))}catch{}}return null}function x(m){if(m===H)return"<>";if(typeof m=="object"&&m!==null&&m.$$typeof===xt)return"<...>";try{var w=U(m);return w?"<"+w+">":"<...>"}catch{return"<...>"}}function O(){var m=_e.A;return m===null?null:m.getOwner()}function L(){return Error("react-stack-top-frame")}function I(m){if(yi.call(m,"key")){var w=Object.getOwnPropertyDescriptor(m,"key").get;if(w&&w.isReactWarning)return!1}return m.key!==void 0}function te(m,w){function P(){Ao||(Ao=!0,console.error("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",w))}P.isReactWarning=!0,Object.defineProperty(m,"key",{get:P,configurable:!0})}function ne(){var m=U(this.type);return kr[m]||(kr[m]=!0,console.error("Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release.")),m=this.props.ref,m!==void 0?m:null}function ie(m,w,P,G,J,ye,pe,Ae){return P=ye.ref,m={$$typeof:oe,type:m,key:w,props:ye,_owner:J},(P!==void 0?P:null)!==null?Object.defineProperty(m,"ref",{enumerable:!1,get:ne}):Object.defineProperty(m,"ref",{enumerable:!1,value:null}),m._store={},Object.defineProperty(m._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:0}),Object.defineProperty(m,"_debugInfo",{configurable:!1,enumerable:!1,writable:!0,value:null}),Object.defineProperty(m,"_debugStack",{configurable:!1,enumerable:!1,writable:!0,value:pe}),Object.defineProperty(m,"_debugTask",{configurable:!1,enumerable:!1,writable:!0,value:Ae}),Object.freeze&&(Object.freeze(m.props),Object.freeze(m)),m}function Qe(m,w){return w=ie(m.type,w,void 0,void 0,m._owner,m.props,m._debugStack,m._debugTask),m._store&&(w._store.validated=m._store.validated),w}function $e(m){return typeof m=="object"&&m!==null&&m.$$typeof===oe}function Ne(m){var w={"=":"=0",":":"=2"};return"$"+m.replace(/[=:]/g,function(P){return w[P]})}function Ze(m,w){return typeof m=="object"&&m!==null&&m.key!=null?(N(m.key),Ne(""+m.key)):w.toString(36)}function Fe(){}function ke(m){switch(m.status){case"fulfilled":return m.value;case"rejected":throw m.reason;default:switch(typeof m.status=="string"?m.then(Fe,Fe):(m.status="pending",m.then(function(w){m.status==="pending"&&(m.status="fulfilled",m.value=w)},function(w){m.status==="pending"&&(m.status="rejected",m.reason=w)})),m.status){case"fulfilled":return m.value;case"rejected":throw m.reason}}throw m}function Vt(m,w,P,G,J){var ye=typeof m;(ye==="undefined"||ye==="boolean")&&(m=null);var pe=!1;if(m===null)pe=!0;else switch(ye){case"bigint":case"string":case"number":pe=!0;break;case"object":switch(m.$$typeof){case oe:case De:pe=!0;break;case xt:return pe=m._init,Vt(pe(m._payload),w,P,G,J)}}if(pe){pe=m,J=J(pe);var Ae=G===""?"."+Ze(pe,0):G;return wo(J)?(P="",Ae!=null&&(P=Ae.replace(Br,"$&/")+"/"),Vt(J,w,P,"",function(_t){return _t})):J!=null&&($e(J)&&(J.key!=null&&(pe&&pe.key===J.key||N(J.key)),P=Qe(J,P+(J.key==null||pe&&pe.key===J.key?"":(""+J.key).replace(Br,"$&/")+"/")+Ae),G!==""&&pe!=null&&$e(pe)&&pe.key==null&&pe._store&&!pe._store.validated&&(P._store.validated=2),J=P),w.push(J)),1}if(pe=0,Ae=G===""?".":G+":",wo(m))for(var me=0;me<m.length;me++)G=m[me],ye=Ae+Ze(G,me),pe+=Vt(G,w,P,ye,J);else if(me=s(m),typeof me=="function")for(me===m.entries&&(Lr||console.warn("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),Lr=!0),m=me.call(m),me=0;!(G=m.next()).done;)G=G.value,ye=Ae+Ze(G,me++),pe+=Vt(G,w,P,ye,J);else if(ye==="object"){if(typeof m.then=="function")return Vt(ke(m),w,P,G,J);throw w=String(m),Error("Objects are not valid as a React child (found: "+(w==="[object Object]"?"object with keys {"+Object.keys(m).join(", ")+"}":w)+"). If you meant to render a collection of children, use an array instead.")}return pe}function ee(m,w,P){if(m==null)return m;var G=[],J=0;return Vt(m,G,"","",function(ye){return w.call(P,ye,J++)}),G}function at(m){if(m._status===-1){var w=m._result;w=w(),w.then(function(P){(m._status===0||m._status===-1)&&(m._status=1,m._result=P)},function(P){(m._status===0||m._status===-1)&&(m._status=2,m._result=P)}),m._status===-1&&(m._status=0,m._result=w)}if(m._status===1)return w=m._result,w===void 0&&console.error(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))

Did you accidentally put curly braces around the import?`,w),"default"in w||console.error(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))`,w),w.default;throw m._result}function ue(){var m=_e.H;return m===null&&console.error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`),m}function Re(){}function st(m){if(pl===null)try{var w=("require"+Math.random()).slice(0,7);pl=(i&&i[w]).call(i,"timers").setImmediate}catch{pl=function(G){Vr===!1&&(Vr=!0,typeof MessageChannel>"u"&&console.error("This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."));var J=new MessageChannel;J.port1.onmessage=G,J.port2.postMessage(void 0)}}return pl(m)}function At(m){return 1<m.length&&typeof AggregateError=="function"?new AggregateError(m):m[0]}function kt(m,w){w!==xo-1&&console.error("You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "),xo=w}function q(m,w,P){var G=_e.actQueue;if(G!==null)if(G.length!==0)try{se(G),st(function(){return q(m,w,P)});return}catch(J){_e.thrownErrors.push(J)}else _e.actQueue=null;0<_e.thrownErrors.length?(G=At(_e.thrownErrors),_e.thrownErrors.length=0,P(G)):w(m)}function se(m){if(!la){la=!0;var w=0;try{for(;w<m.length;w++){var P=m[w];do{_e.didUsePromise=!1;var G=P(!1);if(G!==null){if(_e.didUsePromise){m[w]=P,m.splice(0,w);return}P=G}else break}while(!0)}m.length=0}catch(J){m.splice(0,w+1),_e.thrownErrors.push(J)}finally{la=!1}}}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var oe=Symbol.for("react.transitional.element"),De=Symbol.for("react.portal"),H=Symbol.for("react.fragment"),ae=Symbol.for("react.strict_mode"),F=Symbol.for("react.profiler"),Le=Symbol.for("react.consumer"),Ke=Symbol.for("react.context"),ut=Symbol.for("react.forward_ref"),Ce=Symbol.for("react.suspense"),mi=Symbol.for("react.suspense_list"),et=Symbol.for("react.memo"),xt=Symbol.for("react.lazy"),Nn=Symbol.for("react.activity"),vi=Symbol.iterator,Ur={},Eo={isMounted:function(){return!1},enqueueForceUpdate:function(m){f(m,"forceUpdate")},enqueueReplaceState:function(m){f(m,"replaceState")},enqueueSetState:function(m){f(m,"setState")}},tc=Object.assign,Nr={};Object.freeze(Nr),h.prototype.isReactComponent={},h.prototype.setState=function(m,w){if(typeof m!="object"&&typeof m!="function"&&m!=null)throw Error("takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,m,w,"setState")},h.prototype.forceUpdate=function(m){this.updater.enqueueForceUpdate(this,m,"forceUpdate")};var ct={isMounted:["isMounted","Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."],replaceState:["replaceState","Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."]},$a;for($a in ct)ct.hasOwnProperty($a)&&u($a,ct[$a]);p.prototype=h.prototype,ct=b.prototype=new p,ct.constructor=b,tc(ct,h.prototype),ct.isPureReactComponent=!0;var wo=Array.isArray,ah=Symbol.for("react.client.reference"),_e={H:null,A:null,T:null,S:null,V:null,actQueue:null,isBatchingLegacy:!1,didScheduleLegacyUpdate:!1,didUsePromise:!1,thrownErrors:[],getCurrentStack:null,recentlyCreatedOwnerStacks:0},yi=Object.prototype.hasOwnProperty,dl=console.createTask?console.createTask:function(){return null};ct={react_stack_bottom_frame:function(m){return m()}};var Ao,nc,kr={},ac=ct.react_stack_bottom_frame.bind(ct,L)(),Hr=dl(x(L)),Lr=!1,Br=/\/+/g,hl=typeof reportError=="function"?reportError:function(m){if(typeof window=="object"&&typeof window.ErrorEvent=="function"){var w=new window.ErrorEvent("error",{bubbles:!0,cancelable:!0,message:typeof m=="object"&&m!==null&&typeof m.message=="string"?String(m.message):String(m),error:m});if(!window.dispatchEvent(w))return}else if(typeof process=="object"&&typeof process.emit=="function"){process.emit("uncaughtException",m);return}console.error(m)},Vr=!1,pl=null,xo=0,ia=!1,la=!1,Ro=typeof queueMicrotask=="function"?function(m){queueMicrotask(function(){return queueMicrotask(m)})}:st;ct=Object.freeze({__proto__:null,c:function(m){return ue().useMemoCache(m)}}),r.Children={map:ee,forEach:function(m,w,P){ee(m,function(){w.apply(this,arguments)},P)},count:function(m){var w=0;return ee(m,function(){w++}),w},toArray:function(m){return ee(m,function(w){return w})||[]},only:function(m){if(!$e(m))throw Error("React.Children.only expected to receive a single React element child.");return m}},r.Component=h,r.Fragment=H,r.Profiler=F,r.PureComponent=b,r.StrictMode=ae,r.Suspense=Ce,r.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE=_e,r.__COMPILER_RUNTIME=ct,r.act=function(m){var w=_e.actQueue,P=xo;xo++;var G=_e.actQueue=w!==null?w:[],J=!1;try{var ye=m()}catch(me){_e.thrownErrors.push(me)}if(0<_e.thrownErrors.length)throw kt(w,P),m=At(_e.thrownErrors),_e.thrownErrors.length=0,m;if(ye!==null&&typeof ye=="object"&&typeof ye.then=="function"){var pe=ye;return Ro(function(){J||ia||(ia=!0,console.error("You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"))}),{then:function(me,_t){J=!0,pe.then(function(qa){if(kt(w,P),P===0){try{se(G),st(function(){return q(qa,me,_t)})}catch(ih){_e.thrownErrors.push(ih)}if(0<_e.thrownErrors.length){var oh=At(_e.thrownErrors);_e.thrownErrors.length=0,_t(oh)}}else me(qa)},function(qa){kt(w,P),0<_e.thrownErrors.length&&(qa=At(_e.thrownErrors),_e.thrownErrors.length=0),_t(qa)})}}}var Ae=ye;if(kt(w,P),P===0&&(se(G),G.length!==0&&Ro(function(){J||ia||(ia=!0,console.error("A component suspended inside an `act` scope, but the `act` call was not awaited. When testing React components that depend on asynchronous data, you must await the result:\n\nawait act(() => ...)"))}),_e.actQueue=null),0<_e.thrownErrors.length)throw m=At(_e.thrownErrors),_e.thrownErrors.length=0,m;return{then:function(me,_t){J=!0,P===0?(_e.actQueue=G,st(function(){return q(Ae,me,_t)})):me(Ae)}}},r.cache=function(m){return function(){return m.apply(null,arguments)}},r.captureOwnerStack=function(){var m=_e.getCurrentStack;return m===null?null:m()},r.cloneElement=function(m,w,P){if(m==null)throw Error("The argument must be a React element, but you passed "+m+".");var G=tc({},m.props),J=m.key,ye=m._owner;if(w!=null){var pe;e:{if(yi.call(w,"ref")&&(pe=Object.getOwnPropertyDescriptor(w,"ref").get)&&pe.isReactWarning){pe=!1;break e}pe=w.ref!==void 0}pe&&(ye=O()),I(w)&&(N(w.key),J=""+w.key);for(Ae in w)!yi.call(w,Ae)||Ae==="key"||Ae==="__self"||Ae==="__source"||Ae==="ref"&&w.ref===void 0||(G[Ae]=w[Ae])}var Ae=arguments.length-2;if(Ae===1)G.children=P;else if(1<Ae){pe=Array(Ae);for(var me=0;me<Ae;me++)pe[me]=arguments[me+2];G.children=pe}for(G=ie(m.type,J,void 0,void 0,ye,G,m._debugStack,m._debugTask),J=2;J<arguments.length;J++)ye=arguments[J],$e(ye)&&ye._store&&(ye._store.validated=1);return G},r.createContext=function(m){return m={$$typeof:Ke,_currentValue:m,_currentValue2:m,_threadCount:0,Provider:null,Consumer:null},m.Provider=m,m.Consumer={$$typeof:Le,_context:m},m._currentRenderer=null,m._currentRenderer2=null,m},r.createElement=function(m,w,P){for(var G=2;G<arguments.length;G++){var J=arguments[G];$e(J)&&J._store&&(J._store.validated=1)}if(G={},J=null,w!=null)for(me in nc||!("__self"in w)||"key"in w||(nc=!0,console.warn("Your app (or one of its dependencies) is using an outdated JSX transform. Update to the modern JSX transform for faster performance: https://react.dev/link/new-jsx-transform")),I(w)&&(N(w.key),J=""+w.key),w)yi.call(w,me)&&me!=="key"&&me!=="__self"&&me!=="__source"&&(G[me]=w[me]);var ye=arguments.length-2;if(ye===1)G.children=P;else if(1<ye){for(var pe=Array(ye),Ae=0;Ae<ye;Ae++)pe[Ae]=arguments[Ae+2];Object.freeze&&Object.freeze(pe),G.children=pe}if(m&&m.defaultProps)for(me in ye=m.defaultProps,ye)G[me]===void 0&&(G[me]=ye[me]);J&&te(G,typeof m=="function"?m.displayName||m.name||"Unknown":m);var me=1e4>_e.recentlyCreatedOwnerStacks++;return ie(m,J,void 0,void 0,O(),G,me?Error("react-stack-top-frame"):ac,me?dl(x(m)):Hr)},r.createRef=function(){var m={current:null};return Object.seal(m),m},r.forwardRef=function(m){m!=null&&m.$$typeof===et?console.error("forwardRef requires a render function but received a `memo` component. Instead of forwardRef(memo(...)), use memo(forwardRef(...))."):typeof m!="function"?console.error("forwardRef requires a render function but was given %s.",m===null?"null":typeof m):m.length!==0&&m.length!==2&&console.error("forwardRef render functions accept exactly two parameters: props and ref. %s",m.length===1?"Did you forget to use the ref parameter?":"Any additional parameter will be undefined."),m!=null&&m.defaultProps!=null&&console.error("forwardRef render functions do not support defaultProps. Did you accidentally pass a React component?");var w={$$typeof:ut,render:m},P;return Object.defineProperty(w,"displayName",{enumerable:!1,configurable:!0,get:function(){return P},set:function(G){P=G,m.name||m.displayName||(Object.defineProperty(m,"name",{value:G}),m.displayName=G)}}),w},r.isValidElement=$e,r.lazy=function(m){return{$$typeof:xt,_payload:{_status:-1,_result:m},_init:at}},r.memo=function(m,w){m==null&&console.error("memo: The first argument must be a component. Instead received: %s",m===null?"null":typeof m),w={$$typeof:et,type:m,compare:w===void 0?null:w};var P;return Object.defineProperty(w,"displayName",{enumerable:!1,configurable:!0,get:function(){return P},set:function(G){P=G,m.name||m.displayName||(Object.defineProperty(m,"name",{value:G}),m.displayName=G)}}),w},r.startTransition=function(m){var w=_e.T,P={};_e.T=P,P._updatedFibers=new Set;try{var G=m(),J=_e.S;J!==null&&J(P,G),typeof G=="object"&&G!==null&&typeof G.then=="function"&&G.then(Re,hl)}catch(ye){hl(ye)}finally{w===null&&P._updatedFibers&&(m=P._updatedFibers.size,P._updatedFibers.clear(),10<m&&console.warn("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table.")),_e.T=w}},r.unstable_useCacheRefresh=function(){return ue().useCacheRefresh()},r.use=function(m){return ue().use(m)},r.useActionState=function(m,w,P){return ue().useActionState(m,w,P)},r.useCallback=function(m,w){return ue().useCallback(m,w)},r.useContext=function(m){var w=ue();return m.$$typeof===Le&&console.error("Calling useContext(Context.Consumer) is not supported and will cause bugs. Did you mean to call useContext(Context) instead?"),w.useContext(m)},r.useDebugValue=function(m,w){return ue().useDebugValue(m,w)},r.useDeferredValue=function(m,w){return ue().useDeferredValue(m,w)},r.useEffect=function(m,w,P){m==null&&console.warn("React Hook useEffect requires an effect callback. Did you forget to pass a callback to the hook?");var G=ue();if(typeof P=="function")throw Error("useEffect CRUD overload is not enabled in this build of React.");return G.useEffect(m,w)},r.useId=function(){return ue().useId()},r.useImperativeHandle=function(m,w,P){return ue().useImperativeHandle(m,w,P)},r.useInsertionEffect=function(m,w){return m==null&&console.warn("React Hook useInsertionEffect requires an effect callback. Did you forget to pass a callback to the hook?"),ue().useInsertionEffect(m,w)},r.useLayoutEffect=function(m,w){return m==null&&console.warn("React Hook useLayoutEffect requires an effect callback. Did you forget to pass a callback to the hook?"),ue().useLayoutEffect(m,w)},r.useMemo=function(m,w){return ue().useMemo(m,w)},r.useOptimistic=function(m,w){return ue().useOptimistic(m,w)},r.useReducer=function(m,w,P){return ue().useReducer(m,w,P)},r.useRef=function(m){return ue().useRef(m)},r.useState=function(m){return ue().useState(m)},r.useSyncExternalStore=function(m,w,P){return ue().useSyncExternalStore(m,w,P)},r.useTransition=function(){return ue().useTransition()},r.version="19.1.1",typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())})()}(yr,yr.exports)),yr.exports}var Dv;function gr(){return Dv||(Dv=1,ed.exports=Y1()),ed.exports}var Cv;function X1(){if(Cv)return vr;Cv=1;return function(){function i(H){if(H==null)return null;if(typeof H=="function")return H.$$typeof===at?null:H.displayName||H.name||null;if(typeof H=="string")return H;switch(H){case te:return"Fragment";case ie:return"Profiler";case ne:return"StrictMode";case Ze:return"Suspense";case Fe:return"SuspenseList";case ee:return"Activity"}if(typeof H=="object")switch(typeof H.tag=="number"&&console.error("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),H.$$typeof){case I:return"Portal";case $e:return(H.displayName||"Context")+".Provider";case Qe:return(H._context.displayName||"Context")+".Consumer";case Ne:var ae=H.render;return H=H.displayName,H||(H=ae.displayName||ae.name||"",H=H!==""?"ForwardRef("+H+")":"ForwardRef"),H;case ke:return ae=H.displayName||null,ae!==null?ae:i(H.type)||"Memo";case Vt:ae=H._payload,H=H._init;try{return i(H(ae))}catch{}}return null}function r(H){return""+H}function u(H){try{r(H);var ae=!1}catch{ae=!0}if(ae){ae=console;var F=ae.error,Le=typeof Symbol=="function"&&Symbol.toStringTag&&H[Symbol.toStringTag]||H.constructor.name||"Object";return F.call(ae,"The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",Le),r(H)}}function s(H){if(H===te)return"<>";if(typeof H=="object"&&H!==null&&H.$$typeof===Vt)return"<...>";try{var ae=i(H);return ae?"<"+ae+">":"<...>"}catch{return"<...>"}}function f(){var H=ue.A;return H===null?null:H.getOwner()}function h(){return Error("react-stack-top-frame")}function p(H){if(Re.call(H,"key")){var ae=Object.getOwnPropertyDescriptor(H,"key").get;if(ae&&ae.isReactWarning)return!1}return H.key!==void 0}function b(H,ae){function F(){kt||(kt=!0,console.error("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",ae))}F.isReactWarning=!0,Object.defineProperty(H,"key",{get:F,configurable:!0})}function g(){var H=i(this.type);return q[H]||(q[H]=!0,console.error("Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release.")),H=this.props.ref,H!==void 0?H:null}function N(H,ae,F,Le,Ke,ut,Ce,mi){return F=ut.ref,H={$$typeof:L,type:H,key:ae,props:ut,_owner:Ke},(F!==void 0?F:null)!==null?Object.defineProperty(H,"ref",{enumerable:!1,get:g}):Object.defineProperty(H,"ref",{enumerable:!1,value:null}),H._store={},Object.defineProperty(H._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:0}),Object.defineProperty(H,"_debugInfo",{configurable:!1,enumerable:!1,writable:!0,value:null}),Object.defineProperty(H,"_debugStack",{configurable:!1,enumerable:!1,writable:!0,value:Ce}),Object.defineProperty(H,"_debugTask",{configurable:!1,enumerable:!1,writable:!0,value:mi}),Object.freeze&&(Object.freeze(H.props),Object.freeze(H)),H}function U(H,ae,F,Le,Ke,ut,Ce,mi){var et=ae.children;if(et!==void 0)if(Le)if(st(et)){for(Le=0;Le<et.length;Le++)x(et[Le]);Object.freeze&&Object.freeze(et)}else console.error("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");else x(et);if(Re.call(ae,"key")){et=i(H);var xt=Object.keys(ae).filter(function(vi){return vi!=="key"});Le=0<xt.length?"{key: someKey, "+xt.join(": ..., ")+": ...}":"{key: someKey}",De[et+Le]||(xt=0<xt.length?"{"+xt.join(": ..., ")+": ...}":"{}",console.error(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,Le,et,xt,et),De[et+Le]=!0)}if(et=null,F!==void 0&&(u(F),et=""+F),p(ae)&&(u(ae.key),et=""+ae.key),"key"in ae){F={};for(var Nn in ae)Nn!=="key"&&(F[Nn]=ae[Nn])}else F=ae;return et&&b(F,typeof H=="function"?H.displayName||H.name||"Unknown":H),N(H,et,ut,Ke,f(),F,Ce,mi)}function x(H){typeof H=="object"&&H!==null&&H.$$typeof===L&&H._store&&(H._store.validated=1)}var O=gr(),L=Symbol.for("react.transitional.element"),I=Symbol.for("react.portal"),te=Symbol.for("react.fragment"),ne=Symbol.for("react.strict_mode"),ie=Symbol.for("react.profiler"),Qe=Symbol.for("react.consumer"),$e=Symbol.for("react.context"),Ne=Symbol.for("react.forward_ref"),Ze=Symbol.for("react.suspense"),Fe=Symbol.for("react.suspense_list"),ke=Symbol.for("react.memo"),Vt=Symbol.for("react.lazy"),ee=Symbol.for("react.activity"),at=Symbol.for("react.client.reference"),ue=O.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,Re=Object.prototype.hasOwnProperty,st=Array.isArray,At=console.createTask?console.createTask:function(){return null};O={react_stack_bottom_frame:function(H){return H()}};var kt,q={},se=O.react_stack_bottom_frame.bind(O,h)(),oe=At(s(h)),De={};vr.Fragment=te,vr.jsx=function(H,ae,F,Le,Ke){var ut=1e4>ue.recentlyCreatedOwnerStacks++;return U(H,ae,F,!1,Le,Ke,ut?Error("react-stack-top-frame"):se,ut?At(s(H)):oe)},vr.jsxs=function(H,ae,F,Le,Ke){var ut=1e4>ue.recentlyCreatedOwnerStacks++;return U(H,ae,F,!0,Le,Ke,ut?Error("react-stack-top-frame"):se,ut?At(s(H)):oe)}}(),vr}var Mv;function I1(){return Mv||(Mv=1,Ff.exports=X1()),Ff.exports}var _=I1(),td={exports:{}},nd={exports:{}},ad={},jv;function Q1(){return jv||(jv=1,function(i){(function(){function r(){if(Ze=!1,ee){var q=i.unstable_now();Re=q;var se=!0;try{e:{$e=!1,Ne&&(Ne=!1,ke(at),at=-1),Qe=!0;var oe=ie;try{t:{for(p(q),ne=s(L);ne!==null&&!(ne.expirationTime>q&&g());){var De=ne.callback;if(typeof De=="function"){ne.callback=null,ie=ne.priorityLevel;var H=De(ne.expirationTime<=q);if(q=i.unstable_now(),typeof H=="function"){ne.callback=H,p(q),se=!0;break t}ne===s(L)&&f(L),p(q)}else f(L);ne=s(L)}if(ne!==null)se=!0;else{var ae=s(I);ae!==null&&N(b,ae.startTime-q),se=!1}}break e}finally{ne=null,ie=oe,Qe=!1}se=void 0}}finally{se?st():ee=!1}}}function u(q,se){var oe=q.length;q.push(se);e:for(;0<oe;){var De=oe-1>>>1,H=q[De];if(0<h(H,se))q[De]=se,q[oe]=H,oe=De;else break e}}function s(q){return q.length===0?null:q[0]}function f(q){if(q.length===0)return null;var se=q[0],oe=q.pop();if(oe!==se){q[0]=oe;e:for(var De=0,H=q.length,ae=H>>>1;De<ae;){var F=2*(De+1)-1,Le=q[F],Ke=F+1,ut=q[Ke];if(0>h(Le,oe))Ke<H&&0>h(ut,Le)?(q[De]=ut,q[Ke]=oe,De=Ke):(q[De]=Le,q[F]=oe,De=F);else if(Ke<H&&0>h(ut,oe))q[De]=ut,q[Ke]=oe,De=Ke;else break e}}return se}function h(q,se){var oe=q.sortIndex-se.sortIndex;return oe!==0?oe:q.id-se.id}function p(q){for(var se=s(I);se!==null;){if(se.callback===null)f(I);else if(se.startTime<=q)f(I),se.sortIndex=se.expirationTime,u(L,se);else break;se=s(I)}}function b(q){if(Ne=!1,p(q),!$e)if(s(L)!==null)$e=!0,ee||(ee=!0,st());else{var se=s(I);se!==null&&N(b,se.startTime-q)}}function g(){return Ze?!0:!(i.unstable_now()-Re<ue)}function N(q,se){at=Fe(function(){q(i.unstable_now())},se)}if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error()),i.unstable_now=void 0,typeof performance=="object"&&typeof performance.now=="function"){var U=performance;i.unstable_now=function(){return U.now()}}else{var x=Date,O=x.now();i.unstable_now=function(){return x.now()-O}}var L=[],I=[],te=1,ne=null,ie=3,Qe=!1,$e=!1,Ne=!1,Ze=!1,Fe=typeof setTimeout=="function"?setTimeout:null,ke=typeof clearTimeout=="function"?clearTimeout:null,Vt=typeof setImmediate<"u"?setImmediate:null,ee=!1,at=-1,ue=5,Re=-1;if(typeof Vt=="function")var st=function(){Vt(r)};else if(typeof MessageChannel<"u"){var At=new MessageChannel,kt=At.port2;At.port1.onmessage=r,st=function(){kt.postMessage(null)}}else st=function(){Fe(r,0)};i.unstable_IdlePriority=5,i.unstable_ImmediatePriority=1,i.unstable_LowPriority=4,i.unstable_NormalPriority=3,i.unstable_Profiling=null,i.unstable_UserBlockingPriority=2,i.unstable_cancelCallback=function(q){q.callback=null},i.unstable_forceFrameRate=function(q){0>q||125<q?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):ue=0<q?Math.floor(1e3/q):5},i.unstable_getCurrentPriorityLevel=function(){return ie},i.unstable_next=function(q){switch(ie){case 1:case 2:case 3:var se=3;break;default:se=ie}var oe=ie;ie=se;try{return q()}finally{ie=oe}},i.unstable_requestPaint=function(){Ze=!0},i.unstable_runWithPriority=function(q,se){switch(q){case 1:case 2:case 3:case 4:case 5:break;default:q=3}var oe=ie;ie=q;try{return se()}finally{ie=oe}},i.unstable_scheduleCallback=function(q,se,oe){var De=i.unstable_now();switch(typeof oe=="object"&&oe!==null?(oe=oe.delay,oe=typeof oe=="number"&&0<oe?De+oe:De):oe=De,q){case 1:var H=-1;break;case 2:H=250;break;case 5:H=1073741823;break;case 4:H=1e4;break;default:H=5e3}return H=oe+H,q={id:te++,callback:se,priorityLevel:q,startTime:oe,expirationTime:H,sortIndex:-1},oe>De?(q.sortIndex=oe,u(I,q),s(L)===null&&q===s(I)&&(Ne?(ke(at),at=-1):Ne=!0,N(b,oe-De))):(q.sortIndex=H,u(L,q),$e||Qe||($e=!0,ee||(ee=!0,st()))),q},i.unstable_shouldYield=g,i.unstable_wrapCallback=function(q){var se=ie;return function(){var oe=ie;ie=se;try{return q.apply(this,arguments)}finally{ie=oe}}},typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())})()}(ad)),ad}var Uv;function Z1(){return Uv||(Uv=1,nd.exports=Q1()),nd.exports}var od={exports:{}},Nt={},Nv;function K1(){if(Nv)return Nt;Nv=1;return function(){function i(){}function r(x){return""+x}function u(x,O,L){var I=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;try{r(I);var te=!1}catch{te=!0}return te&&(console.error("The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",typeof Symbol=="function"&&Symbol.toStringTag&&I[Symbol.toStringTag]||I.constructor.name||"Object"),r(I)),{$$typeof:N,key:I==null?null:""+I,children:x,containerInfo:O,implementation:L}}function s(x,O){if(x==="font")return"";if(typeof O=="string")return O==="use-credentials"?O:""}function f(x){return x===null?"`null`":x===void 0?"`undefined`":x===""?"an empty string":'something with type "'+typeof x+'"'}function h(x){return x===null?"`null`":x===void 0?"`undefined`":x===""?"an empty string":typeof x=="string"?JSON.stringify(x):typeof x=="number"?"`"+x+"`":'something with type "'+typeof x+'"'}function p(){var x=U.H;return x===null&&console.error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`),x}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var b=gr(),g={d:{f:i,r:function(){throw Error("Invalid form element. requestFormReset must be passed a form that was rendered by React.")},D:i,C:i,L:i,m:i,X:i,S:i,M:i},p:0,findDOMNode:null},N=Symbol.for("react.portal"),U=b.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;typeof Map=="function"&&Map.prototype!=null&&typeof Map.prototype.forEach=="function"&&typeof Set=="function"&&Set.prototype!=null&&typeof Set.prototype.clear=="function"&&typeof Set.prototype.forEach=="function"||console.error("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"),Nt.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE=g,Nt.createPortal=function(x,O){var L=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!O||O.nodeType!==1&&O.nodeType!==9&&O.nodeType!==11)throw Error("Target container is not a DOM element.");return u(x,O,null,L)},Nt.flushSync=function(x){var O=U.T,L=g.p;try{if(U.T=null,g.p=2,x)return x()}finally{U.T=O,g.p=L,g.d.f()&&console.error("flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task.")}},Nt.preconnect=function(x,O){typeof x=="string"&&x?O!=null&&typeof O!="object"?console.error("ReactDOM.preconnect(): Expected the `options` argument (second) to be an object but encountered %s instead. The only supported option at this time is `crossOrigin` which accepts a string.",h(O)):O!=null&&typeof O.crossOrigin!="string"&&console.error("ReactDOM.preconnect(): Expected the `crossOrigin` option (second argument) to be a string but encountered %s instead. Try removing this option or passing a string value instead.",f(O.crossOrigin)):console.error("ReactDOM.preconnect(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",f(x)),typeof x=="string"&&(O?(O=O.crossOrigin,O=typeof O=="string"?O==="use-credentials"?O:"":void 0):O=null,g.d.C(x,O))},Nt.prefetchDNS=function(x){if(typeof x!="string"||!x)console.error("ReactDOM.prefetchDNS(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",f(x));else if(1<arguments.length){var O=arguments[1];typeof O=="object"&&O.hasOwnProperty("crossOrigin")?console.error("ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. It looks like the you are attempting to set a crossOrigin property for this DNS lookup hint. Browsers do not perform DNS queries using CORS and setting this attribute on the resource hint has no effect. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",h(O)):console.error("ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",h(O))}typeof x=="string"&&g.d.D(x)},Nt.preinit=function(x,O){if(typeof x=="string"&&x?O==null||typeof O!="object"?console.error("ReactDOM.preinit(): Expected the `options` argument (second) to be an object with an `as` property describing the type of resource to be preinitialized but encountered %s instead.",h(O)):O.as!=="style"&&O.as!=="script"&&console.error('ReactDOM.preinit(): Expected the `as` property in the `options` argument (second) to contain a valid value describing the type of resource to be preinitialized but encountered %s instead. Valid values for `as` are "style" and "script".',h(O.as)):console.error("ReactDOM.preinit(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",f(x)),typeof x=="string"&&O&&typeof O.as=="string"){var L=O.as,I=s(L,O.crossOrigin),te=typeof O.integrity=="string"?O.integrity:void 0,ne=typeof O.fetchPriority=="string"?O.fetchPriority:void 0;L==="style"?g.d.S(x,typeof O.precedence=="string"?O.precedence:void 0,{crossOrigin:I,integrity:te,fetchPriority:ne}):L==="script"&&g.d.X(x,{crossOrigin:I,integrity:te,fetchPriority:ne,nonce:typeof O.nonce=="string"?O.nonce:void 0})}},Nt.preinitModule=function(x,O){var L="";if(typeof x=="string"&&x||(L+=" The `href` argument encountered was "+f(x)+"."),O!==void 0&&typeof O!="object"?L+=" The `options` argument encountered was "+f(O)+".":O&&"as"in O&&O.as!=="script"&&(L+=" The `as` option encountered was "+h(O.as)+"."),L)console.error("ReactDOM.preinitModule(): Expected up to two arguments, a non-empty `href` string and, optionally, an `options` object with a valid `as` property.%s",L);else switch(L=O&&typeof O.as=="string"?O.as:"script",L){case"script":break;default:L=h(L),console.error('ReactDOM.preinitModule(): Currently the only supported "as" type for this function is "script" but received "%s" instead. This warning was generated for `href` "%s". In the future other module types will be supported, aligning with the import-attributes proposal. Learn more here: (https://github.com/tc39/proposal-import-attributes)',L,x)}typeof x=="string"&&(typeof O=="object"&&O!==null?(O.as==null||O.as==="script")&&(L=s(O.as,O.crossOrigin),g.d.M(x,{crossOrigin:L,integrity:typeof O.integrity=="string"?O.integrity:void 0,nonce:typeof O.nonce=="string"?O.nonce:void 0})):O==null&&g.d.M(x))},Nt.preload=function(x,O){var L="";if(typeof x=="string"&&x||(L+=" The `href` argument encountered was "+f(x)+"."),O==null||typeof O!="object"?L+=" The `options` argument encountered was "+f(O)+".":typeof O.as=="string"&&O.as||(L+=" The `as` option encountered was "+f(O.as)+"."),L&&console.error('ReactDOM.preload(): Expected two arguments, a non-empty `href` string and an `options` object with an `as` property valid for a `<link rel="preload" as="..." />` tag.%s',L),typeof x=="string"&&typeof O=="object"&&O!==null&&typeof O.as=="string"){L=O.as;var I=s(L,O.crossOrigin);g.d.L(x,L,{crossOrigin:I,integrity:typeof O.integrity=="string"?O.integrity:void 0,nonce:typeof O.nonce=="string"?O.nonce:void 0,type:typeof O.type=="string"?O.type:void 0,fetchPriority:typeof O.fetchPriority=="string"?O.fetchPriority:void 0,referrerPolicy:typeof O.referrerPolicy=="string"?O.referrerPolicy:void 0,imageSrcSet:typeof O.imageSrcSet=="string"?O.imageSrcSet:void 0,imageSizes:typeof O.imageSizes=="string"?O.imageSizes:void 0,media:typeof O.media=="string"?O.media:void 0})}},Nt.preloadModule=function(x,O){var L="";typeof x=="string"&&x||(L+=" The `href` argument encountered was "+f(x)+"."),O!==void 0&&typeof O!="object"?L+=" The `options` argument encountered was "+f(O)+".":O&&"as"in O&&typeof O.as!="string"&&(L+=" The `as` option encountered was "+f(O.as)+"."),L&&console.error('ReactDOM.preloadModule(): Expected two arguments, a non-empty `href` string and, optionally, an `options` object with an `as` property valid for a `<link rel="modulepreload" as="..." />` tag.%s',L),typeof x=="string"&&(O?(L=s(O.as,O.crossOrigin),g.d.m(x,{as:typeof O.as=="string"&&O.as!=="script"?O.as:void 0,crossOrigin:L,integrity:typeof O.integrity=="string"?O.integrity:void 0})):g.d.m(x))},Nt.requestFormReset=function(x){g.d.r(x)},Nt.unstable_batchedUpdates=function(x,O){return x(O)},Nt.useFormState=function(x,O,L){return p().useFormState(x,O,L)},Nt.useFormStatus=function(){return p().useHostTransitionStatus()},Nt.version="19.1.1",typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())}(),Nt}var kv;function Hv(){return kv||(kv=1,od.exports=K1()),od.exports}var br={},Lv;function J1(){if(Lv)return br;Lv=1;return function(){function i(e,t){for(e=e.memoizedState;e!==null&&0<t;)e=e.next,t--;return e}function r(e,t,n,a){if(n>=t.length)return a;var o=t[n],l=Ct(e)?e.slice():ge({},e);return l[o]=r(e[o],t,n+1,a),l}function u(e,t,n){if(t.length!==n.length)console.warn("copyWithRename() expects paths of the same length");else{for(var a=0;a<n.length-1;a++)if(t[a]!==n[a]){console.warn("copyWithRename() expects paths to be the same except for the deepest key");return}return s(e,t,n,0)}}function s(e,t,n,a){var o=t[a],l=Ct(e)?e.slice():ge({},e);return a+1===t.length?(l[n[a]]=l[o],Ct(l)?l.splice(o,1):delete l[o]):l[o]=s(e[o],t,n,a+1),l}function f(e,t,n){var a=t[n],o=Ct(e)?e.slice():ge({},e);return n+1===t.length?(Ct(o)?o.splice(a,1):delete o[a],o):(o[a]=f(e[a],t,n+1),o)}function h(){return!1}function p(){return null}function b(){}function g(){console.error("Do not call Hooks inside useEffect(...), useMemo(...), or other built-in Hooks. You can only call Hooks at the top level of your React function. For more information, see https://react.dev/link/rules-of-hooks")}function N(){console.error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().")}function U(){}function x(e){var t=[];return e.forEach(function(n){t.push(n)}),t.sort().join(", ")}function O(e,t,n,a){return new mR(e,t,n,a)}function L(e,t){e.context===Go&&(tm(e.current,2,t,e,null,null),wl())}function I(e,t){if(qn!==null){var n=t.staleFamilies;t=t.updatedFamilies,ds(),_h(e.current,t,n),wl()}}function te(e){qn=e}function ne(e){return!(!e||e.nodeType!==1&&e.nodeType!==9&&e.nodeType!==11)}function ie(e){var t=e,n=e;if(e.alternate)for(;t.return;)t=t.return;else{e=t;do t=e,(t.flags&4098)!==0&&(n=t.return),e=t.return;while(e)}return t.tag===3?n:null}function Qe(e){if(e.tag===13){var t=e.memoizedState;if(t===null&&(e=e.alternate,e!==null&&(t=e.memoizedState)),t!==null)return t.dehydrated}return null}function $e(e){if(ie(e)!==e)throw Error("Unable to find node on an unmounted component.")}function Ne(e){var t=e.alternate;if(!t){if(t=ie(e),t===null)throw Error("Unable to find node on an unmounted component.");return t!==e?null:e}for(var n=e,a=t;;){var o=n.return;if(o===null)break;var l=o.alternate;if(l===null){if(a=o.return,a!==null){n=a;continue}break}if(o.child===l.child){for(l=o.child;l;){if(l===n)return $e(o),e;if(l===a)return $e(o),t;l=l.sibling}throw Error("Unable to find node on an unmounted component.")}if(n.return!==a.return)n=o,a=l;else{for(var c=!1,d=o.child;d;){if(d===n){c=!0,n=o,a=l;break}if(d===a){c=!0,a=o,n=l;break}d=d.sibling}if(!c){for(d=l.child;d;){if(d===n){c=!0,n=l,a=o;break}if(d===a){c=!0,a=l,n=o;break}d=d.sibling}if(!c)throw Error("Child was not found in either parent set. This indicates a bug in React related to the return pointer. Please file an issue.")}}if(n.alternate!==a)throw Error("Return fibers should always be each others' alternates. This error is likely caused by a bug in React. Please file an issue.")}if(n.tag!==3)throw Error("Unable to find node on an unmounted component.");return n.stateNode.current===n?e:t}function Ze(e){var t=e.tag;if(t===5||t===26||t===27||t===6)return e;for(e=e.child;e!==null;){if(t=Ze(e),t!==null)return t;e=e.sibling}return null}function Fe(e){return e===null||typeof e!="object"?null:(e=hS&&e[hS]||e["@@iterator"],typeof e=="function"?e:null)}function ke(e){if(e==null)return null;if(typeof e=="function")return e.$$typeof===Cz?null:e.displayName||e.name||null;if(typeof e=="string")return e;switch(e){case Ml:return"Fragment";case sm:return"Profiler";case of:return"StrictMode";case cm:return"Suspense";case fm:return"SuspenseList";case dm:return"Activity"}if(typeof e=="object")switch(typeof e.tag=="number"&&console.error("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),e.$$typeof){case Cl:return"Portal";case xa:return(e.displayName||"Context")+".Provider";case um:return(e._context.displayName||"Context")+".Consumer";case Ss:var t=e.render;return e=e.displayName,e||(e=t.displayName||t.name||"",e=e!==""?"ForwardRef("+e+")":"ForwardRef"),e;case lf:return t=e.displayName||null,t!==null?t:ke(e.type)||"Memo";case _n:t=e._payload,e=e._init;try{return ke(e(t))}catch{}}return null}function Vt(e){return typeof e.tag=="number"?ee(e):typeof e.name=="string"?e.name:null}function ee(e){var t=e.type;switch(e.tag){case 31:return"Activity";case 24:return"Cache";case 9:return(t._context.displayName||"Context")+".Consumer";case 10:return(t.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return e=t.render,e=e.displayName||e.name||"",t.displayName||(e!==""?"ForwardRef("+e+")":"ForwardRef");case 7:return"Fragment";case 26:case 27:case 5:return t;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return ke(t);case 8:return t===of?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 14:case 15:if(typeof t=="function")return t.displayName||t.name||null;if(typeof t=="string")return t;break;case 29:if(t=e._debugInfo,t!=null){for(var n=t.length-1;0<=n;n--)if(typeof t[n].name=="string")return t[n].name}if(e.return!==null)return ee(e.return)}return null}function at(e){return{current:e}}function ue(e,t){0>Fa?console.error("Unexpected pop."):(t!==pm[Fa]&&console.error("Unexpected Fiber popped."),e.current=hm[Fa],hm[Fa]=null,pm[Fa]=null,Fa--)}function Re(e,t,n){Fa++,hm[Fa]=e.current,pm[Fa]=n,e.current=t}function st(e){return e===null&&console.error("Expected host context to exist. This error is likely caused by a bug in React. Please file an issue."),e}function At(e,t){Re(Vo,t,e),Re(Ts,e,e),Re(Bo,null,e);var n=t.nodeType;switch(n){case 9:case 11:n=n===9?"#document":"#fragment",t=(t=t.documentElement)&&(t=t.namespaceURI)?P_(t):fo;break;default:if(n=t.tagName,t=t.namespaceURI)t=P_(t),t=$_(t,n);else switch(n){case"svg":t=hr;break;case"math":t=Xf;break;default:t=fo}}n=n.toLowerCase(),n=Jg(null,n),n={context:t,ancestorInfo:n},ue(Bo,e),Re(Bo,n,e)}function kt(e){ue(Bo,e),ue(Ts,e),ue(Vo,e)}function q(){return st(Bo.current)}function se(e){e.memoizedState!==null&&Re(rf,e,e);var t=st(Bo.current),n=e.type,a=$_(t.context,n);n=Jg(t.ancestorInfo,n),a={context:a,ancestorInfo:n},t!==a&&(Re(Ts,e,e),Re(Bo,a,e))}function oe(e){Ts.current===e&&(ue(Bo,e),ue(Ts,e)),rf.current===e&&(ue(rf,e),lu._currentValue=Fi)}function De(e){return typeof Symbol=="function"&&Symbol.toStringTag&&e[Symbol.toStringTag]||e.constructor.name||"Object"}function H(e){try{return ae(e),!1}catch{return!0}}function ae(e){return""+e}function F(e,t){if(H(e))return console.error("The provided `%s` attribute is an unsupported type %s. This value must be coerced to a string before using it here.",t,De(e)),ae(e)}function Le(e,t){if(H(e))return console.error("The provided `%s` CSS property is an unsupported type %s. This value must be coerced to a string before using it here.",t,De(e)),ae(e)}function Ke(e){if(H(e))return console.error("Form field values (value, checked, defaultValue, or defaultChecked props) must be strings, not %s. This value must be coerced to a string before using it here.",De(e)),ae(e)}function ut(e){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u")return!1;var t=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(t.isDisabled)return!0;if(!t.supportsFiber)return console.error("The installed version of React DevTools is too old and will not work with the current version of React. Please update React DevTools. https://react.dev/link/react-devtools"),!0;try{Ul=t.inject(e),Ht=t}catch(n){console.error("React instrumentation encountered an error: %s.",n)}return!!t.checkDCE}function Ce(e){if(typeof Lz=="function"&&Bz(e),Ht&&typeof Ht.setStrictMode=="function")try{Ht.setStrictMode(Ul,e)}catch(t){za||(za=!0,console.error("React instrumentation encountered an error: %s",t))}}function mi(e){Y=e}function et(){Y!==null&&typeof Y.markCommitStopped=="function"&&Y.markCommitStopped()}function xt(e){Y!==null&&typeof Y.markComponentRenderStarted=="function"&&Y.markComponentRenderStarted(e)}function Nn(){Y!==null&&typeof Y.markComponentRenderStopped=="function"&&Y.markComponentRenderStopped()}function vi(e){Y!==null&&typeof Y.markRenderStarted=="function"&&Y.markRenderStarted(e)}function Ur(){Y!==null&&typeof Y.markRenderStopped=="function"&&Y.markRenderStopped()}function Eo(e,t){Y!==null&&typeof Y.markStateUpdateScheduled=="function"&&Y.markStateUpdateScheduled(e,t)}function tc(e){return e>>>=0,e===0?32:31-(Vz(e)/Pz|0)|0}function Nr(e){if(e&1)return"SyncHydrationLane";if(e&2)return"Sync";if(e&4)return"InputContinuousHydration";if(e&8)return"InputContinuous";if(e&16)return"DefaultHydration";if(e&32)return"Default";if(e&128)return"TransitionHydration";if(e&4194048)return"Transition";if(e&62914560)return"Retry";if(e&67108864)return"SelectiveHydration";if(e&134217728)return"IdleHydration";if(e&268435456)return"Idle";if(e&536870912)return"Offscreen";if(e&1073741824)return"Deferred"}function ct(e){var t=e&42;if(t!==0)return t;switch(e&-e){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:return 64;case 128:return 128;case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return e&4194048;case 4194304:case 8388608:case 16777216:case 33554432:return e&62914560;case 67108864:return 67108864;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 0;default:return console.error("Should have found matching lanes. This is a bug in React."),e}}function $a(e,t,n){var a=e.pendingLanes;if(a===0)return 0;var o=0,l=e.suspendedLanes,c=e.pingedLanes;e=e.warmLanes;var d=a&134217727;return d!==0?(a=d&~l,a!==0?o=ct(a):(c&=d,c!==0?o=ct(c):n||(n=d&~e,n!==0&&(o=ct(n))))):(d=a&~l,d!==0?o=ct(d):c!==0?o=ct(c):n||(n=a&~e,n!==0&&(o=ct(n)))),o===0?0:t!==0&&t!==o&&(t&l)===0&&(l=o&-o,n=t&-t,l>=n||l===32&&(n&4194048)!==0)?t:o}function wo(e,t){return(e.pendingLanes&~(e.suspendedLanes&~e.pingedLanes)&t)===0}function ah(e,t){switch(e){case 1:case 2:case 4:case 8:case 64:return t+250;case 16:case 32:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return t+5e3;case 4194304:case 8388608:case 16777216:case 33554432:return-1;case 67108864:case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return console.error("Should have found matching lanes. This is a bug in React."),-1}}function _e(){var e=sf;return sf<<=1,(sf&4194048)===0&&(sf=256),e}function yi(){var e=uf;return uf<<=1,(uf&62914560)===0&&(uf=4194304),e}function dl(e){for(var t=[],n=0;31>n;n++)t.push(e);return t}function Ao(e,t){e.pendingLanes|=t,t!==268435456&&(e.suspendedLanes=0,e.pingedLanes=0,e.warmLanes=0)}function nc(e,t,n,a,o,l){var c=e.pendingLanes;e.pendingLanes=n,e.suspendedLanes=0,e.pingedLanes=0,e.warmLanes=0,e.expiredLanes&=n,e.entangledLanes&=n,e.errorRecoveryDisabledLanes&=n,e.shellSuspendCounter=0;var d=e.entanglements,v=e.expirationTimes,y=e.hiddenUpdates;for(n=c&~n;0<n;){var R=31-Pt(n),C=1<<R;d[R]=0,v[R]=-1;var A=y[R];if(A!==null)for(y[R]=null,R=0;R<A.length;R++){var M=A[R];M!==null&&(M.lane&=-536870913)}n&=~C}a!==0&&kr(e,a,0),l!==0&&o===0&&e.tag!==0&&(e.suspendedLanes|=l&~(c&~t))}function kr(e,t,n){e.pendingLanes|=t,e.suspendedLanes&=~t;var a=31-Pt(t);e.entangledLanes|=t,e.entanglements[a]=e.entanglements[a]|1073741824|n&4194090}function ac(e,t){var n=e.entangledLanes|=t;for(e=e.entanglements;n;){var a=31-Pt(n),o=1<<a;o&t|e[a]&t&&(e[a]|=t),n&=~o}}function Hr(e){switch(e){case 2:e=1;break;case 8:e=4;break;case 32:e=16;break;case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:e=128;break;case 268435456:e=134217728;break;default:e=0}return e}function Lr(e,t,n){if(ua)for(e=e.pendingUpdatersLaneMap;0<n;){var a=31-Pt(n),o=1<<a;e[a].add(t),n&=~o}}function Br(e,t){if(ua)for(var n=e.pendingUpdatersLaneMap,a=e.memoizedUpdaters;0<t;){var o=31-Pt(t);e=1<<o,o=n[o],0<o.size&&(o.forEach(function(l){var c=l.alternate;c!==null&&a.has(c)||a.add(l)}),o.clear()),t&=~e}}function hl(e){return e&=-e,Pn<e?Da<e?(e&134217727)!==0?to:cf:Da:Pn}function Vr(){var e=Me.p;return e!==0?e:(e=window.event,e===void 0?to:rS(e.type))}function pl(e,t){var n=Me.p;try{return Me.p=e,t()}finally{Me.p=n}}function xo(e){delete e[Lt],delete e[en],delete e[bm],delete e[$z],delete e[qz]}function ia(e){var t=e[Lt];if(t)return t;for(var n=e.parentNode;n;){if(t=n[$o]||n[Lt]){if(n=t.alternate,t.child!==null||n!==null&&n.child!==null)for(e=Q_(e);e!==null;){if(n=e[Lt])return n;e=Q_(e)}return t}e=n,n=e.parentNode}return null}function la(e){if(e=e[Lt]||e[$o]){var t=e.tag;if(t===5||t===6||t===13||t===26||t===27||t===3)return e}return null}function Ro(e){var t=e.tag;if(t===5||t===26||t===27||t===6)return e.stateNode;throw Error("getNodeFromInstance: Invalid argument.")}function m(e){var t=e[pS];return t||(t=e[pS]={hoistableStyles:new Map,hoistableScripts:new Map}),t}function w(e){e[Os]=!0}function P(e,t){G(e,t),G(e+"Capture",t)}function G(e,t){Ui[e]&&console.error("EventRegistry: More than one plugin attempted to publish the same registration name, `%s`.",e),Ui[e]=t;var n=e.toLowerCase();for(_m[n]=e,e==="onDoubleClick"&&(_m.ondblclick=e),e=0;e<t.length;e++)mS.add(t[e])}function J(e,t){Gz[t.type]||t.onChange||t.onInput||t.readOnly||t.disabled||t.value==null||console.error(e==="select"?"You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set `onChange`.":"You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set either `onChange` or `readOnly`."),t.onChange||t.readOnly||t.disabled||t.checked==null||console.error("You provided a `checked` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultChecked`. Otherwise, set either `onChange` or `readOnly`.")}function ye(e){return eo.call(yS,e)?!0:eo.call(vS,e)?!1:Yz.test(e)?yS[e]=!0:(vS[e]=!0,console.error("Invalid attribute name: `%s`",e),!1)}function pe(e,t,n){if(ye(t)){if(!e.hasAttribute(t)){switch(typeof n){case"symbol":case"object":return n;case"function":return n;case"boolean":if(n===!1)return n}return n===void 0?void 0:null}return e=e.getAttribute(t),e===""&&n===!0?!0:(F(n,t),e===""+n?n:e)}}function Ae(e,t,n){if(ye(t))if(n===null)e.removeAttribute(t);else{switch(typeof n){case"undefined":case"function":case"symbol":e.removeAttribute(t);return;case"boolean":var a=t.toLowerCase().slice(0,5);if(a!=="data-"&&a!=="aria-"){e.removeAttribute(t);return}}F(n,t),e.setAttribute(t,""+n)}}function me(e,t,n){if(n===null)e.removeAttribute(t);else{switch(typeof n){case"undefined":case"function":case"symbol":case"boolean":e.removeAttribute(t);return}F(n,t),e.setAttribute(t,""+n)}}function _t(e,t,n,a){if(a===null)e.removeAttribute(n);else{switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":e.removeAttribute(n);return}F(a,n),e.setAttributeNS(t,n,""+a)}}function qa(){}function oh(){if(Es===0){gS=console.log,bS=console.info,_S=console.warn,SS=console.error,TS=console.group,OS=console.groupCollapsed,ES=console.groupEnd;var e={configurable:!0,enumerable:!0,value:qa,writable:!0};Object.defineProperties(console,{info:e,log:e,warn:e,error:e,group:e,groupCollapsed:e,groupEnd:e})}Es++}function ih(){if(Es--,Es===0){var e={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:ge({},e,{value:gS}),info:ge({},e,{value:bS}),warn:ge({},e,{value:_S}),error:ge({},e,{value:SS}),group:ge({},e,{value:TS}),groupCollapsed:ge({},e,{value:OS}),groupEnd:ge({},e,{value:ES})})}0>Es&&console.error("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}function kn(e){if(Sm===void 0)try{throw Error()}catch(n){var t=n.stack.trim().match(/\n( *(at )?)/);Sm=t&&t[1]||"",wS=-1<n.stack.indexOf(`
    at`)?" (<anonymous>)":-1<n.stack.indexOf("@")?"@unknown:0:0":""}return`
`+Sm+e+wS}function lh(e,t){if(!e||Tm)return"";var n=Om.get(e);if(n!==void 0)return n;Tm=!0,n=Error.prepareStackTrace,Error.prepareStackTrace=void 0;var a=null;a=D.H,D.H=null,oh();try{var o={DetermineComponentFrameRoot:function(){try{if(t){var A=function(){throw Error()};if(Object.defineProperty(A.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(A,[])}catch(Q){var M=Q}Reflect.construct(e,[],A)}else{try{A.call()}catch(Q){M=Q}e.call(A.prototype)}}else{try{throw Error()}catch(Q){M=Q}(A=e())&&typeof A.catch=="function"&&A.catch(function(){})}}catch(Q){if(Q&&M&&typeof Q.stack=="string")return[Q.stack,M.stack]}return[null,null]}};o.DetermineComponentFrameRoot.displayName="DetermineComponentFrameRoot";var l=Object.getOwnPropertyDescriptor(o.DetermineComponentFrameRoot,"name");l&&l.configurable&&Object.defineProperty(o.DetermineComponentFrameRoot,"name",{value:"DetermineComponentFrameRoot"});var c=o.DetermineComponentFrameRoot(),d=c[0],v=c[1];if(d&&v){var y=d.split(`
`),R=v.split(`
`);for(c=l=0;l<y.length&&!y[l].includes("DetermineComponentFrameRoot");)l++;for(;c<R.length&&!R[c].includes("DetermineComponentFrameRoot");)c++;if(l===y.length||c===R.length)for(l=y.length-1,c=R.length-1;1<=l&&0<=c&&y[l]!==R[c];)c--;for(;1<=l&&0<=c;l--,c--)if(y[l]!==R[c]){if(l!==1||c!==1)do if(l--,c--,0>c||y[l]!==R[c]){var C=`
`+y[l].replace(" at new "," at ");return e.displayName&&C.includes("<anonymous>")&&(C=C.replace("<anonymous>",e.displayName)),typeof e=="function"&&Om.set(e,C),C}while(1<=l&&0<=c);break}}}finally{Tm=!1,D.H=a,ih(),Error.prepareStackTrace=n}return y=(y=e?e.displayName||e.name:"")?kn(y):"",typeof e=="function"&&Om.set(e,y),y}function Ug(e){var t=Error.prepareStackTrace;if(Error.prepareStackTrace=void 0,e=e.stack,Error.prepareStackTrace=t,e.startsWith(`Error: react-stack-top-frame
`)&&(e=e.slice(29)),t=e.indexOf(`
`),t!==-1&&(e=e.slice(t+1)),t=e.indexOf("react_stack_bottom_frame"),t!==-1&&(t=e.lastIndexOf(`
`,t)),t!==-1)e=e.slice(0,t);else return"";return e}function Qx(e){switch(e.tag){case 26:case 27:case 5:return kn(e.type);case 16:return kn("Lazy");case 13:return kn("Suspense");case 19:return kn("SuspenseList");case 0:case 15:return lh(e.type,!1);case 11:return lh(e.type.render,!1);case 1:return lh(e.type,!0);case 31:return kn("Activity");default:return""}}function Ng(e){try{var t="";do{t+=Qx(e);var n=e._debugInfo;if(n)for(var a=n.length-1;0<=a;a--){var o=n[a];if(typeof o.name=="string"){var l=t,c=o.env,d=kn(o.name+(c?" ["+c+"]":""));t=l+d}}e=e.return}while(e);return t}catch(v){return`
Error generating stack: `+v.message+`
`+v.stack}}function kg(e){return(e=e?e.displayName||e.name:"")?kn(e):""}function oc(){if(Sn===null)return null;var e=Sn._debugOwner;return e!=null?Vt(e):null}function Zx(){if(Sn===null)return"";var e=Sn;try{var t="";switch(e.tag===6&&(e=e.return),e.tag){case 26:case 27:case 5:t+=kn(e.type);break;case 13:t+=kn("Suspense");break;case 19:t+=kn("SuspenseList");break;case 31:t+=kn("Activity");break;case 30:case 0:case 15:case 1:e._debugOwner||t!==""||(t+=kg(e.type));break;case 11:e._debugOwner||t!==""||(t+=kg(e.type.render))}for(;e;)if(typeof e.tag=="number"){var n=e;e=n._debugOwner;var a=n._debugStack;e&&a&&(typeof a!="string"&&(n._debugStack=a=Ug(a)),a!==""&&(t+=`
`+a))}else if(e.debugStack!=null){var o=e.debugStack;(e=e.owner)&&o&&(t+=`
`+Ug(o))}else break;var l=t}catch(c){l=`
Error generating stack: `+c.message+`
`+c.stack}return l}function W(e,t,n,a,o,l,c){var d=Sn;rh(e);try{return e!==null&&e._debugTask?e._debugTask.run(t.bind(null,n,a,o,l,c)):t(n,a,o,l,c)}finally{rh(d)}throw Error("runWithFiberInDEV should never be called in production. This is a bug in React.")}function rh(e){D.getCurrentStack=e===null?null:Zx,Ca=!1,Sn=e}function Hn(e){switch(typeof e){case"bigint":case"boolean":case"number":case"string":case"undefined":return e;case"object":return Ke(e),e;default:return""}}function Hg(e){var t=e.type;return(e=e.nodeName)&&e.toLowerCase()==="input"&&(t==="checkbox"||t==="radio")}function Kx(e){var t=Hg(e)?"checked":"value",n=Object.getOwnPropertyDescriptor(e.constructor.prototype,t);Ke(e[t]);var a=""+e[t];if(!e.hasOwnProperty(t)&&typeof n<"u"&&typeof n.get=="function"&&typeof n.set=="function"){var o=n.get,l=n.set;return Object.defineProperty(e,t,{configurable:!0,get:function(){return o.call(this)},set:function(c){Ke(c),a=""+c,l.call(this,c)}}),Object.defineProperty(e,t,{enumerable:n.enumerable}),{getValue:function(){return a},setValue:function(c){Ke(c),a=""+c},stopTracking:function(){e._valueTracker=null,delete e[t]}}}}function ic(e){e._valueTracker||(e._valueTracker=Kx(e))}function Lg(e){if(!e)return!1;var t=e._valueTracker;if(!t)return!0;var n=t.getValue(),a="";return e&&(a=Hg(e)?e.checked?"true":"false":e.value),e=a,e!==n?(t.setValue(e),!0):!1}function lc(e){if(e=e||(typeof document<"u"?document:void 0),typeof e>"u")return null;try{return e.activeElement||e.body}catch{return e.body}}function Ln(e){return e.replace(Xz,function(t){return"\\"+t.charCodeAt(0).toString(16)+" "})}function Bg(e,t){t.checked===void 0||t.defaultChecked===void 0||xS||(console.error("%s contains an input of type %s with both checked and defaultChecked props. Input elements must be either controlled or uncontrolled (specify either the checked prop, or the defaultChecked prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://react.dev/link/controlled-components",oc()||"A component",t.type),xS=!0),t.value===void 0||t.defaultValue===void 0||AS||(console.error("%s contains an input of type %s with both value and defaultValue props. Input elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://react.dev/link/controlled-components",oc()||"A component",t.type),AS=!0)}function sh(e,t,n,a,o,l,c,d){e.name="",c!=null&&typeof c!="function"&&typeof c!="symbol"&&typeof c!="boolean"?(F(c,"type"),e.type=c):e.removeAttribute("type"),t!=null?c==="number"?(t===0&&e.value===""||e.value!=t)&&(e.value=""+Hn(t)):e.value!==""+Hn(t)&&(e.value=""+Hn(t)):c!=="submit"&&c!=="reset"||e.removeAttribute("value"),t!=null?uh(e,c,Hn(t)):n!=null?uh(e,c,Hn(n)):a!=null&&e.removeAttribute("value"),o==null&&l!=null&&(e.defaultChecked=!!l),o!=null&&(e.checked=o&&typeof o!="function"&&typeof o!="symbol"),d!=null&&typeof d!="function"&&typeof d!="symbol"&&typeof d!="boolean"?(F(d,"name"),e.name=""+Hn(d)):e.removeAttribute("name")}function Vg(e,t,n,a,o,l,c,d){if(l!=null&&typeof l!="function"&&typeof l!="symbol"&&typeof l!="boolean"&&(F(l,"type"),e.type=l),t!=null||n!=null){if(!(l!=="submit"&&l!=="reset"||t!=null))return;n=n!=null?""+Hn(n):"",t=t!=null?""+Hn(t):n,d||t===e.value||(e.value=t),e.defaultValue=t}a=a??o,a=typeof a!="function"&&typeof a!="symbol"&&!!a,e.checked=d?e.checked:!!a,e.defaultChecked=!!a,c!=null&&typeof c!="function"&&typeof c!="symbol"&&typeof c!="boolean"&&(F(c,"name"),e.name=c)}function uh(e,t,n){t==="number"&&lc(e.ownerDocument)===e||e.defaultValue===""+n||(e.defaultValue=""+n)}function Pg(e,t){t.value==null&&(typeof t.children=="object"&&t.children!==null?rm.Children.forEach(t.children,function(n){n==null||typeof n=="string"||typeof n=="number"||typeof n=="bigint"||zS||(zS=!0,console.error("Cannot infer the option value of complex children. Pass a `value` prop or use a plain string as children to <option>."))}):t.dangerouslySetInnerHTML==null||DS||(DS=!0,console.error("Pass a `value` prop if you set dangerouslyInnerHTML so React knows which value should be selected."))),t.selected==null||RS||(console.error("Use the `defaultValue` or `value` props on <select> instead of setting `selected` on <option>."),RS=!0)}function $g(){var e=oc();return e?`

Check the render method of \``+e+"`.":""}function ml(e,t,n,a){if(e=e.options,t){t={};for(var o=0;o<n.length;o++)t["$"+n[o]]=!0;for(n=0;n<e.length;n++)o=t.hasOwnProperty("$"+e[n].value),e[n].selected!==o&&(e[n].selected=o),o&&a&&(e[n].defaultSelected=!0)}else{for(n=""+Hn(n),t=null,o=0;o<e.length;o++){if(e[o].value===n){e[o].selected=!0,a&&(e[o].defaultSelected=!0);return}t!==null||e[o].disabled||(t=e[o])}t!==null&&(t.selected=!0)}}function qg(e,t){for(e=0;e<MS.length;e++){var n=MS[e];if(t[n]!=null){var a=Ct(t[n]);t.multiple&&!a?console.error("The `%s` prop supplied to <select> must be an array if `multiple` is true.%s",n,$g()):!t.multiple&&a&&console.error("The `%s` prop supplied to <select> must be a scalar value if `multiple` is false.%s",n,$g())}}t.value===void 0||t.defaultValue===void 0||CS||(console.error("Select elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled select element and remove one of these props. More info: https://react.dev/link/controlled-components"),CS=!0)}function Gg(e,t){t.value===void 0||t.defaultValue===void 0||jS||(console.error("%s contains a textarea with both value and defaultValue props. Textarea elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled textarea and remove one of these props. More info: https://react.dev/link/controlled-components",oc()||"A component"),jS=!0),t.children!=null&&t.value==null&&console.error("Use the `defaultValue` or `value` props instead of setting children on <textarea>.")}function Yg(e,t,n){if(t!=null&&(t=""+Hn(t),t!==e.value&&(e.value=t),n==null)){e.defaultValue!==t&&(e.defaultValue=t);return}e.defaultValue=n!=null?""+Hn(n):""}function Xg(e,t,n,a){if(t==null){if(a!=null){if(n!=null)throw Error("If you supply `defaultValue` on a <textarea>, do not pass children.");if(Ct(a)){if(1<a.length)throw Error("<textarea> can only have at most one child.");a=a[0]}n=a}n==null&&(n=""),t=n}n=Hn(t),e.defaultValue=n,a=e.textContent,a===n&&a!==""&&a!==null&&(e.value=a)}function Ig(e,t){return e.serverProps===void 0&&e.serverTail.length===0&&e.children.length===1&&3<e.distanceFromLeaf&&e.distanceFromLeaf>15-t?Ig(e.children[0],t):e}function mn(e){return"  "+"  ".repeat(e)}function vl(e){return"+ "+"  ".repeat(e)}function gi(e){return"- "+"  ".repeat(e)}function Qg(e){switch(e.tag){case 26:case 27:case 5:return e.type;case 16:return"Lazy";case 13:return"Suspense";case 19:return"SuspenseList";case 0:case 15:return e=e.type,e.displayName||e.name||null;case 11:return e=e.type.render,e.displayName||e.name||null;case 1:return e=e.type,e.displayName||e.name||null;default:return null}}function Pr(e,t){return US.test(e)?(e=JSON.stringify(e),e.length>t-2?8>t?'{"..."}':"{"+e.slice(0,t-7)+'..."}':"{"+e+"}"):e.length>t?5>t?'{"..."}':e.slice(0,t-3)+"...":e}function rc(e,t,n){var a=120-2*n;if(t===null)return vl(n)+Pr(e,a)+`
`;if(typeof t=="string"){for(var o=0;o<t.length&&o<e.length&&t.charCodeAt(o)===e.charCodeAt(o);o++);return o>a-8&&10<o&&(e="..."+e.slice(o-8),t="..."+t.slice(o-8)),vl(n)+Pr(e,a)+`
`+gi(n)+Pr(t,a)+`
`}return mn(n)+Pr(e,a)+`
`}function ch(e){return Object.prototype.toString.call(e).replace(/^\[object (.*)\]$/,function(t,n){return n})}function $r(e,t){switch(typeof e){case"string":return e=JSON.stringify(e),e.length>t?5>t?'"..."':e.slice(0,t-4)+'..."':e;case"object":if(e===null)return"null";if(Ct(e))return"[...]";if(e.$$typeof===Lo)return(t=ke(e.type))?"<"+t+">":"<...>";var n=ch(e);if(n==="Object"){n="",t-=2;for(var a in e)if(e.hasOwnProperty(a)){var o=JSON.stringify(a);if(o!=='"'+a+'"'&&(a=o),t-=a.length-2,o=$r(e[a],15>t?t:15),t-=o.length,0>t){n+=n===""?"...":", ...";break}n+=(n===""?"":",")+a+":"+o}return"{"+n+"}"}return n;case"function":return(t=e.displayName||e.name)?"function "+t:"function";default:return String(e)}}function yl(e,t){return typeof e!="string"||US.test(e)?"{"+$r(e,t-2)+"}":e.length>t-2?5>t?'"..."':'"'+e.slice(0,t-5)+'..."':'"'+e+'"'}function fh(e,t,n){var a=120-n.length-e.length,o=[],l;for(l in t)if(t.hasOwnProperty(l)&&l!=="children"){var c=yl(t[l],120-n.length-l.length-1);a-=l.length+c.length+2,o.push(l+"="+c)}return o.length===0?n+"<"+e+`>
`:0<a?n+"<"+e+" "+o.join(" ")+`>
`:n+"<"+e+`
`+n+"  "+o.join(`
`+n+"  ")+`
`+n+`>
`}function Jx(e,t,n){var a="",o=ge({},t),l;for(l in e)if(e.hasOwnProperty(l)){delete o[l];var c=120-2*n-l.length-2,d=$r(e[l],c);t.hasOwnProperty(l)?(c=$r(t[l],c),a+=vl(n)+l+": "+d+`
`,a+=gi(n)+l+": "+c+`
`):a+=vl(n)+l+": "+d+`
`}for(var v in o)o.hasOwnProperty(v)&&(e=$r(o[v],120-2*n-v.length-2),a+=gi(n)+v+": "+e+`
`);return a}function Wx(e,t,n,a){var o="",l=new Map;for(y in n)n.hasOwnProperty(y)&&l.set(y.toLowerCase(),y);if(l.size===1&&l.has("children"))o+=fh(e,t,mn(a));else{for(var c in t)if(t.hasOwnProperty(c)&&c!=="children"){var d=120-2*(a+1)-c.length-1,v=l.get(c.toLowerCase());if(v!==void 0){l.delete(c.toLowerCase());var y=t[c];v=n[v];var R=yl(y,d);d=yl(v,d),typeof y=="object"&&y!==null&&typeof v=="object"&&v!==null&&ch(y)==="Object"&&ch(v)==="Object"&&(2<Object.keys(y).length||2<Object.keys(v).length||-1<R.indexOf("...")||-1<d.indexOf("..."))?o+=mn(a+1)+c+`={{
`+Jx(y,v,a+2)+mn(a+1)+`}}
`:(o+=vl(a+1)+c+"="+R+`
`,o+=gi(a+1)+c+"="+d+`
`)}else o+=mn(a+1)+c+"="+yl(t[c],d)+`
`}l.forEach(function(C){if(C!=="children"){var A=120-2*(a+1)-C.length-1;o+=gi(a+1)+C+"="+yl(n[C],A)+`
`}}),o=o===""?mn(a)+"<"+e+`>
`:mn(a)+"<"+e+`
`+o+mn(a)+`>
`}return e=n.children,t=t.children,typeof e=="string"||typeof e=="number"||typeof e=="bigint"?(l="",(typeof t=="string"||typeof t=="number"||typeof t=="bigint")&&(l=""+t),o+=rc(l,""+e,a+1)):(typeof t=="string"||typeof t=="number"||typeof t=="bigint")&&(o=e==null?o+rc(""+t,null,a+1):o+rc(""+t,void 0,a+1)),o}function Zg(e,t){var n=Qg(e);if(n===null){for(n="",e=e.child;e;)n+=Zg(e,t),e=e.sibling;return n}return mn(t)+"<"+n+`>
`}function dh(e,t){var n=Ig(e,t);if(n!==e&&(e.children.length!==1||e.children[0]!==n))return mn(t)+`...
`+dh(n,t+1);n="";var a=e.fiber._debugInfo;if(a)for(var o=0;o<a.length;o++){var l=a[o].name;typeof l=="string"&&(n+=mn(t)+"<"+l+`>
`,t++)}if(a="",o=e.fiber.pendingProps,e.fiber.tag===6)a=rc(o,e.serverProps,t),t++;else if(l=Qg(e.fiber),l!==null)if(e.serverProps===void 0){a=t;var c=120-2*a-l.length-2,d="";for(y in o)if(o.hasOwnProperty(y)&&y!=="children"){var v=yl(o[y],15);if(c-=y.length+v.length+2,0>c){d+=" ...";break}d+=" "+y+"="+v}a=mn(a)+"<"+l+d+`>
`,t++}else e.serverProps===null?(a=fh(l,o,vl(t)),t++):typeof e.serverProps=="string"?console.error("Should not have matched a non HostText fiber to a Text node. This is a bug in React."):(a=Wx(l,o,e.serverProps,t),t++);var y="";for(o=e.fiber.child,l=0;o&&l<e.children.length;)c=e.children[l],c.fiber===o?(y+=dh(c,t),l++):y+=Zg(o,t),o=o.sibling;for(o&&0<e.children.length&&(y+=mn(t)+`...
`),o=e.serverTail,e.serverProps===null&&t--,e=0;e<o.length;e++)l=o[e],y=typeof l=="string"?y+(gi(t)+Pr(l,120-2*t)+`
`):y+fh(l.type,l.props,gi(t));return n+a+y}function hh(e){try{return`

`+dh(e,0)}catch{return""}}function Kg(e,t,n){for(var a=t,o=null,l=0;a;)a===e&&(l=0),o={fiber:a,children:o!==null?[o]:[],serverProps:a===t?n:a===e?null:void 0,serverTail:[],distanceFromLeaf:l},l++,a=a.return;return o!==null?hh(o).replaceAll(/^[+-]/gm,">"):""}function Jg(e,t){var n=ge({},e||kS),a={tag:t};return NS.indexOf(t)!==-1&&(n.aTagInScope=null,n.buttonTagInScope=null,n.nobrTagInScope=null),Qz.indexOf(t)!==-1&&(n.pTagInButtonScope=null),Iz.indexOf(t)!==-1&&t!=="address"&&t!=="div"&&t!=="p"&&(n.listItemTagAutoclosing=null,n.dlItemTagAutoclosing=null),n.current=a,t==="form"&&(n.formTag=a),t==="a"&&(n.aTagInScope=a),t==="button"&&(n.buttonTagInScope=a),t==="nobr"&&(n.nobrTagInScope=a),t==="p"&&(n.pTagInButtonScope=a),t==="li"&&(n.listItemTagAutoclosing=a),(t==="dd"||t==="dt")&&(n.dlItemTagAutoclosing=a),t==="#document"||t==="html"?n.containerTagInScope=null:n.containerTagInScope||(n.containerTagInScope=a),e!==null||t!=="#document"&&t!=="html"&&t!=="body"?n.implicitRootScope===!0&&(n.implicitRootScope=!1):n.implicitRootScope=!0,n}function Wg(e,t,n){switch(t){case"select":return e==="hr"||e==="option"||e==="optgroup"||e==="script"||e==="template"||e==="#text";case"optgroup":return e==="option"||e==="#text";case"option":return e==="#text";case"tr":return e==="th"||e==="td"||e==="style"||e==="script"||e==="template";case"tbody":case"thead":case"tfoot":return e==="tr"||e==="style"||e==="script"||e==="template";case"colgroup":return e==="col"||e==="template";case"table":return e==="caption"||e==="colgroup"||e==="tbody"||e==="tfoot"||e==="thead"||e==="style"||e==="script"||e==="template";case"head":return e==="base"||e==="basefont"||e==="bgsound"||e==="link"||e==="meta"||e==="title"||e==="noscript"||e==="noframes"||e==="style"||e==="script"||e==="template";case"html":if(n)break;return e==="head"||e==="body"||e==="frameset";case"frameset":return e==="frame";case"#document":if(!n)return e==="html"}switch(e){case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t!=="h1"&&t!=="h2"&&t!=="h3"&&t!=="h4"&&t!=="h5"&&t!=="h6";case"rp":case"rt":return Zz.indexOf(t)===-1;case"caption":case"col":case"colgroup":case"frameset":case"frame":case"tbody":case"td":case"tfoot":case"th":case"thead":case"tr":return t==null;case"head":return n||t===null;case"html":return n&&t==="#document"||t===null;case"body":return n&&(t==="#document"||t==="html")||t===null}return!0}function Fx(e,t){switch(e){case"address":case"article":case"aside":case"blockquote":case"center":case"details":case"dialog":case"dir":case"div":case"dl":case"fieldset":case"figcaption":case"figure":case"footer":case"header":case"hgroup":case"main":case"menu":case"nav":case"ol":case"p":case"section":case"summary":case"ul":case"pre":case"listing":case"table":case"hr":case"xmp":case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t.pTagInButtonScope;case"form":return t.formTag||t.pTagInButtonScope;case"li":return t.listItemTagAutoclosing;case"dd":case"dt":return t.dlItemTagAutoclosing;case"button":return t.buttonTagInScope;case"a":return t.aTagInScope;case"nobr":return t.nobrTagInScope}return null}function Fg(e,t){for(;e;){switch(e.tag){case 5:case 26:case 27:if(e.type===t)return e}e=e.return}return null}function ph(e,t){t=t||kS;var n=t.current;if(t=(n=Wg(e,n&&n.tag,t.implicitRootScope)?null:n)?null:Fx(e,t),t=n||t,!t)return!0;var a=t.tag;if(t=String(!!n)+"|"+e+"|"+a,ff[t])return!1;ff[t]=!0;var o=(t=Sn)?Fg(t.return,a):null,l=t!==null&&o!==null?Kg(o,t,null):"",c="<"+e+">";return n?(n="",a==="table"&&e==="tr"&&(n+=" Add a <tbody>, <thead> or <tfoot> to your code to match the DOM tree generated by the browser."),console.error(`In HTML, %s cannot be a child of <%s>.%s
This will cause a hydration error.%s`,c,a,n,l)):console.error(`In HTML, %s cannot be a descendant of <%s>.
This will cause a hydration error.%s`,c,a,l),t&&(e=t.return,o===null||e===null||o===e&&e._debugOwner===t._debugOwner||W(o,function(){console.error(`<%s> cannot contain a nested %s.
See this log for the ancestor stack trace.`,a,c)})),!1}function sc(e,t,n){if(n||Wg("#text",t,!1))return!0;if(n="#text|"+t,ff[n])return!1;ff[n]=!0;var a=(n=Sn)?Fg(n,t):null;return n=n!==null&&a!==null?Kg(a,n,n.tag!==6?{children:null}:null):"",/\S/.test(e)?console.error(`In HTML, text nodes cannot be a child of <%s>.
This will cause a hydration error.%s`,t,n):console.error(`In HTML, whitespace text nodes cannot be a child of <%s>. Make sure you don't have any extra whitespace between tags on each line of your source code.
This will cause a hydration error.%s`,t,n),!1}function qr(e,t){if(t){var n=e.firstChild;if(n&&n===e.lastChild&&n.nodeType===3){n.nodeValue=t;return}}e.textContent=t}function eR(e){return e.replace(Wz,function(t,n){return n.toUpperCase()})}function eb(e,t,n){var a=t.indexOf("--")===0;a||(-1<t.indexOf("-")?Nl.hasOwnProperty(t)&&Nl[t]||(Nl[t]=!0,console.error("Unsupported style property %s. Did you mean %s?",t,eR(t.replace(Jz,"ms-")))):Kz.test(t)?Nl.hasOwnProperty(t)&&Nl[t]||(Nl[t]=!0,console.error("Unsupported vendor-prefixed style property %s. Did you mean %s?",t,t.charAt(0).toUpperCase()+t.slice(1))):!BS.test(n)||wm.hasOwnProperty(n)&&wm[n]||(wm[n]=!0,console.error(`Style property values shouldn't contain a semicolon. Try "%s: %s" instead.`,t,n.replace(BS,""))),typeof n=="number"&&(isNaN(n)?VS||(VS=!0,console.error("`NaN` is an invalid value for the `%s` css style property.",t)):isFinite(n)||PS||(PS=!0,console.error("`Infinity` is an invalid value for the `%s` css style property.",t)))),n==null||typeof n=="boolean"||n===""?a?e.setProperty(t,""):t==="float"?e.cssFloat="":e[t]="":a?e.setProperty(t,n):typeof n!="number"||n===0||$S.has(t)?t==="float"?e.cssFloat=n:(Le(n,t),e[t]=(""+n).trim()):e[t]=n+"px"}function tb(e,t,n){if(t!=null&&typeof t!="object")throw Error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.");if(t&&Object.freeze(t),e=e.style,n!=null){if(t){var a={};if(n){for(var o in n)if(n.hasOwnProperty(o)&&!t.hasOwnProperty(o))for(var l=Em[o]||[o],c=0;c<l.length;c++)a[l[c]]=o}for(var d in t)if(t.hasOwnProperty(d)&&(!n||n[d]!==t[d]))for(o=Em[d]||[d],l=0;l<o.length;l++)a[o[l]]=d;d={};for(var v in t)for(o=Em[v]||[v],l=0;l<o.length;l++)d[o[l]]=v;v={};for(var y in a)if(o=a[y],(l=d[y])&&o!==l&&(c=o+","+l,!v[c])){v[c]=!0,c=console;var R=t[o];c.error.call(c,"%s a style property during rerender (%s) when a conflicting property is set (%s) can lead to styling bugs. To avoid this, don't mix shorthand and non-shorthand properties for the same value; instead, replace the shorthand with separate values.",R==null||typeof R=="boolean"||R===""?"Removing":"Updating",o,l)}}for(var C in n)!n.hasOwnProperty(C)||t!=null&&t.hasOwnProperty(C)||(C.indexOf("--")===0?e.setProperty(C,""):C==="float"?e.cssFloat="":e[C]="");for(var A in t)y=t[A],t.hasOwnProperty(A)&&n[A]!==y&&eb(e,A,y)}else for(a in t)t.hasOwnProperty(a)&&eb(e,a,t[a])}function Gr(e){if(e.indexOf("-")===-1)return!1;switch(e){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}function nb(e){return Fz.get(e)||e}function tR(e,t){if(eo.call(Hl,t)&&Hl[t])return!0;if(tD.test(t)){if(e="aria-"+t.slice(4).toLowerCase(),e=qS.hasOwnProperty(e)?e:null,e==null)return console.error("Invalid ARIA attribute `%s`. ARIA attributes follow the pattern aria-* and must be lowercase.",t),Hl[t]=!0;if(t!==e)return console.error("Invalid ARIA attribute `%s`. Did you mean `%s`?",t,e),Hl[t]=!0}if(eD.test(t)){if(e=t.toLowerCase(),e=qS.hasOwnProperty(e)?e:null,e==null)return Hl[t]=!0,!1;t!==e&&(console.error("Unknown ARIA attribute `%s`. Did you mean `%s`?",t,e),Hl[t]=!0)}return!0}function nR(e,t){var n=[],a;for(a in t)tR(e,a)||n.push(a);t=n.map(function(o){return"`"+o+"`"}).join(", "),n.length===1?console.error("Invalid aria prop %s on <%s> tag. For details, see https://react.dev/link/invalid-aria-props",t,e):1<n.length&&console.error("Invalid aria props %s on <%s> tag. For details, see https://react.dev/link/invalid-aria-props",t,e)}function aR(e,t,n,a){if(eo.call($t,t)&&$t[t])return!0;var o=t.toLowerCase();if(o==="onfocusin"||o==="onfocusout")return console.error("React uses onFocus and onBlur instead of onFocusIn and onFocusOut. All React events are normalized to bubble, so onFocusIn and onFocusOut are not needed/supported by React."),$t[t]=!0;if(typeof n=="function"&&(e==="form"&&t==="action"||e==="input"&&t==="formAction"||e==="button"&&t==="formAction"))return!0;if(a!=null){if(e=a.possibleRegistrationNames,a.registrationNameDependencies.hasOwnProperty(t))return!0;if(a=e.hasOwnProperty(o)?e[o]:null,a!=null)return console.error("Invalid event handler property `%s`. Did you mean `%s`?",t,a),$t[t]=!0;if(YS.test(t))return console.error("Unknown event handler property `%s`. It will be ignored.",t),$t[t]=!0}else if(YS.test(t))return nD.test(t)&&console.error("Invalid event handler property `%s`. React events use the camelCase naming convention, for example `onClick`.",t),$t[t]=!0;if(aD.test(t)||oD.test(t))return!0;if(o==="innerhtml")return console.error("Directly setting property `innerHTML` is not permitted. For more information, lookup documentation on `dangerouslySetInnerHTML`."),$t[t]=!0;if(o==="aria")return console.error("The `aria` attribute is reserved for future use in React. Pass individual `aria-` attributes instead."),$t[t]=!0;if(o==="is"&&n!==null&&n!==void 0&&typeof n!="string")return console.error("Received a `%s` for a string attribute `is`. If this is expected, cast the value to a string.",typeof n),$t[t]=!0;if(typeof n=="number"&&isNaN(n))return console.error("Received NaN for the `%s` attribute. If this is expected, cast the value to a string.",t),$t[t]=!0;if(hf.hasOwnProperty(o)){if(o=hf[o],o!==t)return console.error("Invalid DOM property `%s`. Did you mean `%s`?",t,o),$t[t]=!0}else if(t!==o)return console.error("React does not recognize the `%s` prop on a DOM element. If you intentionally want it to appear in the DOM as a custom attribute, spell it as lowercase `%s` instead. If you accidentally passed it from a parent component, remove it from the DOM element.",t,o),$t[t]=!0;switch(t){case"dangerouslySetInnerHTML":case"children":case"style":case"suppressContentEditableWarning":case"suppressHydrationWarning":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":return!0;case"innerText":case"textContent":return!0}switch(typeof n){case"boolean":switch(t){case"autoFocus":case"checked":case"multiple":case"muted":case"selected":case"contentEditable":case"spellCheck":case"draggable":case"value":case"autoReverse":case"externalResourcesRequired":case"focusable":case"preserveAlpha":case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":case"capture":case"download":case"inert":return!0;default:return o=t.toLowerCase().slice(0,5),o==="data-"||o==="aria-"?!0:(n?console.error('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.',n,t,t,n,t):console.error('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.',n,t,t,n,t,t,t),$t[t]=!0)}case"function":case"symbol":return $t[t]=!0,!1;case"string":if(n==="false"||n==="true"){switch(t){case"checked":case"selected":case"multiple":case"muted":case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":case"inert":break;default:return!0}console.error("Received the string `%s` for the boolean attribute `%s`. %s Did you mean %s={%s}?",n,t,n==="false"?"The browser will interpret it as a truthy value.":'Although this works, it will not work as expected if you pass the string "false".',t,n),$t[t]=!0}}return!0}function oR(e,t,n){var a=[],o;for(o in t)aR(e,o,t[o],n)||a.push(o);t=a.map(function(l){return"`"+l+"`"}).join(", "),a.length===1?console.error("Invalid value for prop %s on <%s> tag. Either remove it from the element, or pass a string or number value to keep it in the DOM. For details, see https://react.dev/link/attribute-behavior ",t,e):1<a.length&&console.error("Invalid values for props %s on <%s> tag. Either remove them from the element, or pass a string or number value to keep them in the DOM. For details, see https://react.dev/link/attribute-behavior ",t,e)}function Yr(e){return iD.test(""+e)?"javascript:throw new Error('React has blocked a javascript: URL as a security precaution.')":e}function mh(e){return e=e.target||e.srcElement||window,e.correspondingUseElement&&(e=e.correspondingUseElement),e.nodeType===3?e.parentNode:e}function ab(e){var t=la(e);if(t&&(e=t.stateNode)){var n=e[en]||null;e:switch(e=t.stateNode,t.type){case"input":if(sh(e,n.value,n.defaultValue,n.defaultValue,n.checked,n.defaultChecked,n.type,n.name),t=n.name,n.type==="radio"&&t!=null){for(n=e;n.parentNode;)n=n.parentNode;for(F(t,"name"),n=n.querySelectorAll('input[name="'+Ln(""+t)+'"][type="radio"]'),t=0;t<n.length;t++){var a=n[t];if(a!==e&&a.form===e.form){var o=a[en]||null;if(!o)throw Error("ReactDOMInput: Mixing React and non-React radio inputs with the same `name` is not supported.");sh(a,o.value,o.defaultValue,o.defaultValue,o.checked,o.defaultChecked,o.type,o.name)}}for(t=0;t<n.length;t++)a=n[t],a.form===e.form&&Lg(a)}break e;case"textarea":Yg(e,n.value,n.defaultValue);break e;case"select":t=n.value,t!=null&&ml(e,!!n.multiple,t,!1)}}}function ob(e,t,n){if(Am)return e(t,n);Am=!0;try{var a=e(t);return a}finally{if(Am=!1,(Ll!==null||Bl!==null)&&(wl(),Ll&&(t=Ll,e=Bl,Bl=Ll=null,ab(t),e)))for(t=0;t<e.length;t++)ab(e[t])}}function Xr(e,t){var n=e.stateNode;if(n===null)return null;var a=n[en]||null;if(a===null)return null;n=a[t];e:switch(t){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(a=!a.disabled)||(e=e.type,a=!(e==="button"||e==="input"||e==="select"||e==="textarea")),e=!a;break e;default:e=!1}if(e)return null;if(n&&typeof n!="function")throw Error("Expected `"+t+"` listener to be a function, instead got a value of `"+typeof n+"` type.");return n}function ib(){if(pf)return pf;var e,t=Rm,n=t.length,a,o="value"in qo?qo.value:qo.textContent,l=o.length;for(e=0;e<n&&t[e]===o[e];e++);var c=n-e;for(a=1;a<=c&&t[n-a]===o[l-a];a++);return pf=o.slice(e,1<a?1-a:void 0)}function uc(e){var t=e.keyCode;return"charCode"in e?(e=e.charCode,e===0&&t===13&&(e=13)):e=t,e===10&&(e=13),32<=e||e===13?e:0}function cc(){return!0}function lb(){return!1}function Kt(e){function t(n,a,o,l,c){this._reactName=n,this._targetInst=o,this.type=a,this.nativeEvent=l,this.target=c,this.currentTarget=null;for(var d in e)e.hasOwnProperty(d)&&(n=e[d],this[d]=n?n(l):l[d]);return this.isDefaultPrevented=(l.defaultPrevented!=null?l.defaultPrevented:l.returnValue===!1)?cc:lb,this.isPropagationStopped=lb,this}return ge(t.prototype,{preventDefault:function(){this.defaultPrevented=!0;var n=this.nativeEvent;n&&(n.preventDefault?n.preventDefault():typeof n.returnValue!="unknown"&&(n.returnValue=!1),this.isDefaultPrevented=cc)},stopPropagation:function(){var n=this.nativeEvent;n&&(n.stopPropagation?n.stopPropagation():typeof n.cancelBubble!="unknown"&&(n.cancelBubble=!0),this.isPropagationStopped=cc)},persist:function(){},isPersistent:cc}),t}function iR(e){var t=this.nativeEvent;return t.getModifierState?t.getModifierState(e):(e=gD[e])?!!t[e]:!1}function vh(){return iR}function rb(e,t){switch(e){case"keyup":return DD.indexOf(t.keyCode)!==-1;case"keydown":return t.keyCode!==ZS;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function sb(e){return e=e.detail,typeof e=="object"&&"data"in e?e.data:null}function lR(e,t){switch(e){case"compositionend":return sb(t);case"keypress":return t.which!==JS?null:(FS=!0,WS);case"textInput":return e=t.data,e===WS&&FS?null:e;default:return null}}function rR(e,t){if(Vl)return e==="compositionend"||!Mm&&rb(e,t)?(e=ib(),pf=Rm=qo=null,Vl=!1,e):null;switch(e){case"paste":return null;case"keypress":if(!(t.ctrlKey||t.altKey||t.metaKey)||t.ctrlKey&&t.altKey){if(t.char&&1<t.char.length)return t.char;if(t.which)return String.fromCharCode(t.which)}return null;case"compositionend":return KS&&t.locale!=="ko"?null:t.data;default:return null}}function ub(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t==="input"?!!MD[e.type]:t==="textarea"}function sR(e){if(!Ma)return!1;e="on"+e;var t=e in document;return t||(t=document.createElement("div"),t.setAttribute(e,"return;"),t=typeof t[e]=="function"),t}function cb(e,t,n,a){Ll?Bl?Bl.push(a):Bl=[a]:Ll=a,t=Qc(t,"onChange"),0<t.length&&(n=new mf("onChange","change",null,n,a),e.push({event:n,listeners:t}))}function uR(e){C_(e,0)}function fc(e){var t=Ro(e);if(Lg(t))return e}function fb(e,t){if(e==="change")return t}function db(){Ds&&(Ds.detachEvent("onpropertychange",hb),Cs=Ds=null)}function hb(e){if(e.propertyName==="value"&&fc(Cs)){var t=[];cb(t,Cs,e,mh(e)),ob(uR,t)}}function cR(e,t,n){e==="focusin"?(db(),Ds=t,Cs=n,Ds.attachEvent("onpropertychange",hb)):e==="focusout"&&db()}function fR(e){if(e==="selectionchange"||e==="keyup"||e==="keydown")return fc(Cs)}function dR(e,t){if(e==="click")return fc(t)}function hR(e,t){if(e==="input"||e==="change")return fc(t)}function pR(e,t){return e===t&&(e!==0||1/e===1/t)||e!==e&&t!==t}function Ir(e,t){if(qt(e,t))return!0;if(typeof e!="object"||e===null||typeof t!="object"||t===null)return!1;var n=Object.keys(e),a=Object.keys(t);if(n.length!==a.length)return!1;for(a=0;a<n.length;a++){var o=n[a];if(!eo.call(t,o)||!qt(e[o],t[o]))return!1}return!0}function pb(e){for(;e&&e.firstChild;)e=e.firstChild;return e}function mb(e,t){var n=pb(e);e=0;for(var a;n;){if(n.nodeType===3){if(a=e+n.textContent.length,e<=t&&a>=t)return{node:n,offset:t-e};e=a}e:{for(;n;){if(n.nextSibling){n=n.nextSibling;break e}n=n.parentNode}n=void 0}n=pb(n)}}function vb(e,t){return e&&t?e===t?!0:e&&e.nodeType===3?!1:t&&t.nodeType===3?vb(e,t.parentNode):"contains"in e?e.contains(t):e.compareDocumentPosition?!!(e.compareDocumentPosition(t)&16):!1:!1}function yb(e){e=e!=null&&e.ownerDocument!=null&&e.ownerDocument.defaultView!=null?e.ownerDocument.defaultView:window;for(var t=lc(e.document);t instanceof e.HTMLIFrameElement;){try{var n=typeof t.contentWindow.location.href=="string"}catch{n=!1}if(n)e=t.contentWindow;else break;t=lc(e.document)}return t}function yh(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t&&(t==="input"&&(e.type==="text"||e.type==="search"||e.type==="tel"||e.type==="url"||e.type==="password")||t==="textarea"||e.contentEditable==="true")}function gb(e,t,n){var a=n.window===n?n.document:n.nodeType===9?n:n.ownerDocument;Um||Pl==null||Pl!==lc(a)||(a=Pl,"selectionStart"in a&&yh(a)?a={start:a.selectionStart,end:a.selectionEnd}:(a=(a.ownerDocument&&a.ownerDocument.defaultView||window).getSelection(),a={anchorNode:a.anchorNode,anchorOffset:a.anchorOffset,focusNode:a.focusNode,focusOffset:a.focusOffset}),Ms&&Ir(Ms,a)||(Ms=a,a=Qc(jm,"onSelect"),0<a.length&&(t=new mf("onSelect","select",null,t,n),e.push({event:t,listeners:a}),t.target=Pl)))}function bi(e,t){var n={};return n[e.toLowerCase()]=t.toLowerCase(),n["Webkit"+e]="webkit"+t,n["Moz"+e]="moz"+t,n}function _i(e){if(Nm[e])return Nm[e];if(!$l[e])return e;var t=$l[e],n;for(n in t)if(t.hasOwnProperty(n)&&n in tT)return Nm[e]=t[n];return e}function ra(e,t){lT.set(e,t),P(t,[e])}function vn(e,t){if(typeof e=="object"&&e!==null){var n=Hm.get(e);return n!==void 0?n:(t={value:e,source:t,stack:Ng(t)},Hm.set(e,t),t)}return{value:e,source:t,stack:Ng(t)}}function dc(){for(var e=ql,t=Lm=ql=0;t<e;){var n=$n[t];$n[t++]=null;var a=$n[t];$n[t++]=null;var o=$n[t];$n[t++]=null;var l=$n[t];if($n[t++]=null,a!==null&&o!==null){var c=a.pending;c===null?o.next=o:(o.next=c.next,c.next=o),a.pending=o}l!==0&&bb(n,o,l)}}function hc(e,t,n,a){$n[ql++]=e,$n[ql++]=t,$n[ql++]=n,$n[ql++]=a,Lm|=a,e.lanes|=a,e=e.alternate,e!==null&&(e.lanes|=a)}function gh(e,t,n,a){return hc(e,t,n,a),pc(e)}function Jt(e,t){return hc(e,null,null,t),pc(e)}function bb(e,t,n){e.lanes|=n;var a=e.alternate;a!==null&&(a.lanes|=n);for(var o=!1,l=e.return;l!==null;)l.childLanes|=n,a=l.alternate,a!==null&&(a.childLanes|=n),l.tag===22&&(e=l.stateNode,e===null||e._visibility&yf||(o=!0)),e=l,l=l.return;return e.tag===3?(l=e.stateNode,o&&t!==null&&(o=31-Pt(n),e=l.hiddenUpdates,a=e[o],a===null?e[o]=[t]:a.push(t),t.lane=n|536870912),l):null}function pc(e){if(eu>tC)throw Qi=eu=0,tu=pv=null,Error("Maximum update depth exceeded. This can happen when a component repeatedly calls setState inside componentWillUpdate or componentDidUpdate. React limits the number of nested updates to prevent infinite loops.");Qi>nC&&(Qi=0,tu=null,console.error("Maximum update depth exceeded. This can happen when a component calls setState inside useEffect, but useEffect either doesn't have a dependency array, or one of the dependencies changes on every render.")),e.alternate===null&&(e.flags&4098)!==0&&E_(e);for(var t=e,n=t.return;n!==null;)t.alternate===null&&(t.flags&4098)!==0&&E_(e),t=n,n=t.return;return t.tag===3?t.stateNode:null}function Si(e){if(qn===null)return e;var t=qn(e);return t===void 0?e:t.current}function bh(e){if(qn===null)return e;var t=qn(e);return t===void 0?e!=null&&typeof e.render=="function"&&(t=Si(e.render),e.render!==t)?(t={$$typeof:Ss,render:t},e.displayName!==void 0&&(t.displayName=e.displayName),t):e:t.current}function _b(e,t){if(qn===null)return!1;var n=e.elementType;t=t.type;var a=!1,o=typeof t=="object"&&t!==null?t.$$typeof:null;switch(e.tag){case 1:typeof t=="function"&&(a=!0);break;case 0:(typeof t=="function"||o===_n)&&(a=!0);break;case 11:(o===Ss||o===_n)&&(a=!0);break;case 14:case 15:(o===lf||o===_n)&&(a=!0);break;default:return!1}return!!(a&&(e=qn(n),e!==void 0&&e===qn(t)))}function Sb(e){qn!==null&&typeof WeakSet=="function"&&(Gl===null&&(Gl=new WeakSet),Gl.add(e))}function _h(e,t,n){var a=e.alternate,o=e.child,l=e.sibling,c=e.tag,d=e.type,v=null;switch(c){case 0:case 15:case 1:v=d;break;case 11:v=d.render}if(qn===null)throw Error("Expected resolveFamily to be set during hot reload.");var y=!1;d=!1,v!==null&&(v=qn(v),v!==void 0&&(n.has(v)?d=!0:t.has(v)&&(c===1?d=!0:y=!0))),Gl!==null&&(Gl.has(e)||a!==null&&Gl.has(a))&&(d=!0),d&&(e._debugNeedsRemount=!0),(d||y)&&(a=Jt(e,2),a!==null&&it(a,e,2)),o===null||d||_h(o,t,n),l!==null&&_h(l,t,n)}function mR(e,t,n,a){this.tag=e,this.key=n,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.refCleanup=this.ref=null,this.pendingProps=t,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=a,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null,this.actualDuration=-0,this.actualStartTime=-1.1,this.treeBaseDuration=this.selfBaseDuration=-0,this._debugTask=this._debugStack=this._debugOwner=this._debugInfo=null,this._debugNeedsRemount=!1,this._debugHookTypes=null,sT||typeof Object.preventExtensions!="function"||Object.preventExtensions(this)}function Sh(e){return e=e.prototype,!(!e||!e.isReactComponent)}function Ga(e,t){var n=e.alternate;switch(n===null?(n=O(e.tag,t,e.key,e.mode),n.elementType=e.elementType,n.type=e.type,n.stateNode=e.stateNode,n._debugOwner=e._debugOwner,n._debugStack=e._debugStack,n._debugTask=e._debugTask,n._debugHookTypes=e._debugHookTypes,n.alternate=e,e.alternate=n):(n.pendingProps=t,n.type=e.type,n.flags=0,n.subtreeFlags=0,n.deletions=null,n.actualDuration=-0,n.actualStartTime=-1.1),n.flags=e.flags&65011712,n.childLanes=e.childLanes,n.lanes=e.lanes,n.child=e.child,n.memoizedProps=e.memoizedProps,n.memoizedState=e.memoizedState,n.updateQueue=e.updateQueue,t=e.dependencies,n.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext,_debugThenableState:t._debugThenableState},n.sibling=e.sibling,n.index=e.index,n.ref=e.ref,n.refCleanup=e.refCleanup,n.selfBaseDuration=e.selfBaseDuration,n.treeBaseDuration=e.treeBaseDuration,n._debugInfo=e._debugInfo,n._debugNeedsRemount=e._debugNeedsRemount,n.tag){case 0:case 15:n.type=Si(e.type);break;case 1:n.type=Si(e.type);break;case 11:n.type=bh(e.type)}return n}function Tb(e,t){e.flags&=65011714;var n=e.alternate;return n===null?(e.childLanes=0,e.lanes=t,e.child=null,e.subtreeFlags=0,e.memoizedProps=null,e.memoizedState=null,e.updateQueue=null,e.dependencies=null,e.stateNode=null,e.selfBaseDuration=0,e.treeBaseDuration=0):(e.childLanes=n.childLanes,e.lanes=n.lanes,e.child=n.child,e.subtreeFlags=0,e.deletions=null,e.memoizedProps=n.memoizedProps,e.memoizedState=n.memoizedState,e.updateQueue=n.updateQueue,e.type=n.type,t=n.dependencies,e.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext,_debugThenableState:t._debugThenableState},e.selfBaseDuration=n.selfBaseDuration,e.treeBaseDuration=n.treeBaseDuration),e}function Th(e,t,n,a,o,l){var c=0,d=e;if(typeof e=="function")Sh(e)&&(c=1),d=Si(d);else if(typeof e=="string")c=q(),c=mz(e,n,c)?26:e==="html"||e==="head"||e==="body"?27:5;else e:switch(e){case dm:return t=O(31,n,t,o),t.elementType=dm,t.lanes=l,t;case Ml:return Ti(n.children,o,l,t);case of:c=8,o|=Bt,o|=ca;break;case sm:return e=n,a=o,typeof e.id!="string"&&console.error('Profiler must specify an "id" of type `string` as a prop. Received the type `%s` instead.',typeof e.id),t=O(12,e,t,a|Mt),t.elementType=sm,t.lanes=l,t.stateNode={effectDuration:0,passiveEffectDuration:0},t;case cm:return t=O(13,n,t,o),t.elementType=cm,t.lanes=l,t;case fm:return t=O(19,n,t,o),t.elementType=fm,t.lanes=l,t;default:if(typeof e=="object"&&e!==null)switch(e.$$typeof){case zz:case xa:c=10;break e;case um:c=9;break e;case Ss:c=11,d=bh(d);break e;case lf:c=14;break e;case _n:c=16,d=null;break e}d="",(e===void 0||typeof e=="object"&&e!==null&&Object.keys(e).length===0)&&(d+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports."),e===null?n="null":Ct(e)?n="array":e!==void 0&&e.$$typeof===Lo?(n="<"+(ke(e.type)||"Unknown")+" />",d=" Did you accidentally export a JSX literal instead of a component?"):n=typeof e,(c=a?Vt(a):null)&&(d+=`

Check the render method of \``+c+"`."),c=29,n=Error("Element type is invalid: expected a string (for built-in components) or a class/function (for composite components) but got: "+(n+"."+d)),d=null}return t=O(c,n,t,o),t.elementType=e,t.type=d,t.lanes=l,t._debugOwner=a,t}function mc(e,t,n){return t=Th(e.type,e.key,e.props,e._owner,t,n),t._debugOwner=e._owner,t._debugStack=e._debugStack,t._debugTask=e._debugTask,t}function Ti(e,t,n,a){return e=O(7,e,a,t),e.lanes=n,e}function Oh(e,t,n){return e=O(6,e,null,t),e.lanes=n,e}function Eh(e,t,n){return t=O(4,e.children!==null?e.children:[],e.key,t),t.lanes=n,t.stateNode={containerInfo:e.containerInfo,pendingChildren:null,implementation:e.implementation},t}function Oi(e,t){Ei(),Yl[Xl++]=bf,Yl[Xl++]=gf,gf=e,bf=t}function Ob(e,t,n){Ei(),Gn[Yn++]=ao,Gn[Yn++]=oo,Gn[Yn++]=ki,ki=e;var a=ao;e=oo;var o=32-Pt(a)-1;a&=~(1<<o),n+=1;var l=32-Pt(t)+o;if(30<l){var c=o-o%5;l=(a&(1<<c)-1).toString(32),a>>=c,o-=c,ao=1<<32-Pt(t)+o|n<<o|a,oo=l+e}else ao=1<<l|n<<o|a,oo=e}function wh(e){Ei(),e.return!==null&&(Oi(e,1),Ob(e,1,0))}function Ah(e){for(;e===gf;)gf=Yl[--Xl],Yl[Xl]=null,bf=Yl[--Xl],Yl[Xl]=null;for(;e===ki;)ki=Gn[--Yn],Gn[Yn]=null,oo=Gn[--Yn],Gn[Yn]=null,ao=Gn[--Yn],Gn[Yn]=null}function Ei(){xe||console.error("Expected to be hydrating. This is a bug in React. Please file an issue.")}function wi(e,t){if(e.return===null){if(Xn===null)Xn={fiber:e,children:[],serverProps:void 0,serverTail:[],distanceFromLeaf:t};else{if(Xn.fiber!==e)throw Error("Saw multiple hydration diff roots in a pass. This is a bug in React.");Xn.distanceFromLeaf>t&&(Xn.distanceFromLeaf=t)}return Xn}var n=wi(e.return,t+1).children;return 0<n.length&&n[n.length-1].fiber===e?(n=n[n.length-1],n.distanceFromLeaf>t&&(n.distanceFromLeaf=t),n):(t={fiber:e,children:[],serverProps:void 0,serverTail:[],distanceFromLeaf:t},n.push(t),t)}function xh(e,t){io||(e=wi(e,0),e.serverProps=null,t!==null&&(t=Y_(t),e.serverTail.push(t)))}function Ai(e){var t="",n=Xn;throw n!==null&&(Xn=null,t=hh(n)),Kr(vn(Error(`Hydration failed because the server rendered HTML didn't match the client. As a result this tree will be regenerated on the client. This can happen if a SSR-ed Client Component used:

- A server/client branch \`if (typeof window !== 'undefined')\`.
- Variable input such as \`Date.now()\` or \`Math.random()\` which changes each time it's called.
- Date formatting in a user's locale which doesn't match the server.
- External changing data without sending a snapshot of it along with the HTML.
- Invalid HTML tag nesting.

It can also happen if the client has a browser extension installed which messes with the HTML before React loaded.

https://react.dev/link/hydration-mismatch`+t),e)),Bm}function Eb(e){var t=e.stateNode,n=e.type,a=e.memoizedProps;switch(t[Lt]=e,t[en]=a,Gp(n,a),n){case"dialog":Ee("cancel",t),Ee("close",t);break;case"iframe":case"object":case"embed":Ee("load",t);break;case"video":case"audio":for(n=0;n<nu.length;n++)Ee(nu[n],t);break;case"source":Ee("error",t);break;case"img":case"image":case"link":Ee("error",t),Ee("load",t);break;case"details":Ee("toggle",t);break;case"input":J("input",a),Ee("invalid",t),Bg(t,a),Vg(t,a.value,a.defaultValue,a.checked,a.defaultChecked,a.type,a.name,!0),ic(t);break;case"option":Pg(t,a);break;case"select":J("select",a),Ee("invalid",t),qg(t,a);break;case"textarea":J("textarea",a),Ee("invalid",t),Gg(t,a),Xg(t,a.value,a.defaultValue,a.children),ic(t)}n=a.children,typeof n!="string"&&typeof n!="number"&&typeof n!="bigint"||t.textContent===""+n||a.suppressHydrationWarning===!0||N_(t.textContent,n)?(a.popover!=null&&(Ee("beforetoggle",t),Ee("toggle",t)),a.onScroll!=null&&Ee("scroll",t),a.onScrollEnd!=null&&Ee("scrollend",t),a.onClick!=null&&(t.onclick=Zc),t=!0):t=!1,t||Ai(e)}function wb(e){for(Gt=e.return;Gt;)switch(Gt.tag){case 5:case 13:ja=!1;return;case 27:case 3:ja=!0;return;default:Gt=Gt.return}}function Qr(e){if(e!==Gt)return!1;if(!xe)return wb(e),xe=!0,!1;var t=e.tag,n;if((n=t!==3&&t!==27)&&((n=t===5)&&(n=e.type,n=!(n!=="form"&&n!=="button")||Zp(e.type,e.memoizedProps)),n=!n),n&&tt){for(n=tt;n;){var a=wi(e,0),o=Y_(n);a.serverTail.push(o),n=o.type==="Suspense"?I_(n):Vn(n.nextSibling)}Ai(e)}if(wb(e),t===13){if(e=e.memoizedState,e=e!==null?e.dehydrated:null,!e)throw Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");tt=I_(e)}else t===27?(t=tt,Ho(e.type)?(e=Av,Av=null,tt=e):tt=t):tt=Gt?Vn(e.stateNode.nextSibling):null;return!0}function Zr(){tt=Gt=null,io=xe=!1}function Ab(){var e=Hi;return e!==null&&(It===null?It=e:It.push.apply(It,e),Hi=null),e}function Kr(e){Hi===null?Hi=[e]:Hi.push(e)}function xb(){var e=Xn;if(e!==null){Xn=null;for(var t=hh(e);0<e.children.length;)e=e.children[0];W(e.fiber,function(){console.error(`A tree hydrated but some attributes of the server rendered HTML didn't match the client properties. This won't be patched up. This can happen if a SSR-ed Client Component used:

- A server/client branch \`if (typeof window !== 'undefined')\`.
- Variable input such as \`Date.now()\` or \`Math.random()\` which changes each time it's called.
- Date formatting in a user's locale which doesn't match the server.
- External changing data without sending a snapshot of it along with the HTML.
- Invalid HTML tag nesting.

It can also happen if the client has a browser extension installed which messes with the HTML before React loaded.

%s%s`,"https://react.dev/link/hydration-mismatch",t)})}}function vc(){Il=_f=null,Ql=!1}function zo(e,t,n){Re(Vm,t._currentValue,e),t._currentValue=n,Re(Pm,t._currentRenderer,e),t._currentRenderer!==void 0&&t._currentRenderer!==null&&t._currentRenderer!==dT&&console.error("Detected multiple renderers concurrently rendering the same context provider. This is currently unsupported."),t._currentRenderer=dT}function Ya(e,t){e._currentValue=Vm.current;var n=Pm.current;ue(Pm,t),e._currentRenderer=n,ue(Vm,t)}function Rh(e,t,n){for(;e!==null;){var a=e.alternate;if((e.childLanes&t)!==t?(e.childLanes|=t,a!==null&&(a.childLanes|=t)):a!==null&&(a.childLanes&t)!==t&&(a.childLanes|=t),e===n)break;e=e.return}e!==n&&console.error("Expected to find the propagation root when scheduling context work. This error is likely caused by a bug in React. Please file an issue.")}function zh(e,t,n,a){var o=e.child;for(o!==null&&(o.return=e);o!==null;){var l=o.dependencies;if(l!==null){var c=o.child;l=l.firstContext;e:for(;l!==null;){var d=l;l=o;for(var v=0;v<t.length;v++)if(d.context===t[v]){l.lanes|=n,d=l.alternate,d!==null&&(d.lanes|=n),Rh(l.return,n,e),a||(c=null);break e}l=d.next}}else if(o.tag===18){if(c=o.return,c===null)throw Error("We just came from a parent so we must have had a parent. This is a bug in React.");c.lanes|=n,l=c.alternate,l!==null&&(l.lanes|=n),Rh(c,n,e),c=null}else c=o.child;if(c!==null)c.return=o;else for(c=o;c!==null;){if(c===e){c=null;break}if(o=c.sibling,o!==null){o.return=c.return,c=o;break}c=c.return}o=c}}function Jr(e,t,n,a){e=null;for(var o=t,l=!1;o!==null;){if(!l){if((o.flags&524288)!==0)l=!0;else if((o.flags&262144)!==0)break}if(o.tag===10){var c=o.alternate;if(c===null)throw Error("Should have a current fiber. This is a bug in React.");if(c=c.memoizedProps,c!==null){var d=o.type;qt(o.pendingProps.value,c.value)||(e!==null?e.push(d):e=[d])}}else if(o===rf.current){if(c=o.alternate,c===null)throw Error("Should have a current fiber. This is a bug in React.");c.memoizedState.memoizedState!==o.memoizedState.memoizedState&&(e!==null?e.push(lu):e=[lu])}o=o.return}e!==null&&zh(t,e,n,a),t.flags|=262144}function yc(e){for(e=e.firstContext;e!==null;){if(!qt(e.context._currentValue,e.memoizedValue))return!0;e=e.next}return!1}function xi(e){_f=e,Il=null,e=e.dependencies,e!==null&&(e.firstContext=null)}function Je(e){return Ql&&console.error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo()."),Rb(_f,e)}function gc(e,t){return _f===null&&xi(e),Rb(e,t)}function Rb(e,t){var n=t._currentValue;if(t={context:t,memoizedValue:n,next:null},Il===null){if(e===null)throw Error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");Il=t,e.dependencies={lanes:0,firstContext:t,_debugThenableState:null},e.flags|=524288}else Il=Il.next=t;return n}function Dh(){return{controller:new VD,data:new Map,refCount:0}}function Ri(e){e.controller.signal.aborted&&console.warn("A cache instance was retained after it was already freed. This likely indicates a bug in React."),e.refCount++}function Wr(e){e.refCount--,0>e.refCount&&console.warn("A cache instance was released after it was already freed. This likely indicates a bug in React."),e.refCount===0&&PD($D,function(){e.controller.abort()})}function Xa(){var e=Li;return Li=0,e}function bc(e){var t=Li;return Li=e,t}function Fr(e){var t=Li;return Li+=e,t}function Ch(e){tn=Zl(),0>e.actualStartTime&&(e.actualStartTime=tn)}function Mh(e){if(0<=tn){var t=Zl()-tn;e.actualDuration+=t,e.selfBaseDuration=t,tn=-1}}function zb(e){if(0<=tn){var t=Zl()-tn;e.actualDuration+=t,tn=-1}}function _a(){if(0<=tn){var e=Zl()-tn;tn=-1,Li+=e}}function Sa(){tn=Zl()}function _c(e){for(var t=e.child;t;)e.actualDuration+=t.actualDuration,t=t.sibling}function vR(e,t){if(js===null){var n=js=[];$m=0,Bi=Vp(),Kl={status:"pending",value:void 0,then:function(a){n.push(a)}}}return $m++,t.then(Db,Db),t}function Db(){if(--$m===0&&js!==null){Kl!==null&&(Kl.status="fulfilled");var e=js;js=null,Bi=0,Kl=null;for(var t=0;t<e.length;t++)(0,e[t])()}}function yR(e,t){var n=[],a={status:"pending",value:null,reason:null,then:function(o){n.push(o)}};return e.then(function(){a.status="fulfilled",a.value=t;for(var o=0;o<n.length;o++)(0,n[o])(t)},function(o){for(a.status="rejected",a.reason=o,o=0;o<n.length;o++)(0,n[o])(void 0)}),a}function jh(){var e=Vi.current;return e!==null?e:qe.pooledCache}function Sc(e,t){t===null?Re(Vi,Vi.current,e):Re(Vi,t.pool,e)}function Cb(){var e=jh();return e===null?null:{parent:vt._currentValue,pool:e}}function Mb(){return{didWarnAboutUncachedPromise:!1,thenables:[]}}function jb(e){return e=e.status,e==="fulfilled"||e==="rejected"}function Tc(){}function Ub(e,t,n){D.actQueue!==null&&(D.didUsePromise=!0);var a=e.thenables;switch(n=a[n],n===void 0?a.push(t):n!==t&&(e.didWarnAboutUncachedPromise||(e.didWarnAboutUncachedPromise=!0,console.error("A component was suspended by an uncached promise. Creating promises inside a Client Component or hook is not yet supported, except via a Suspense-compatible library or framework.")),t.then(Tc,Tc),t=n),t.status){case"fulfilled":return t.value;case"rejected":throw e=t.reason,kb(e),e;default:if(typeof t.status=="string")t.then(Tc,Tc);else{if(e=qe,e!==null&&100<e.shellSuspendCounter)throw Error("An unknown Component is an async Client Component. Only Server Components can be async at the moment. This error is often caused by accidentally adding `'use client'` to a module that was originally written for the server.");e=t,e.status="pending",e.then(function(o){if(t.status==="pending"){var l=t;l.status="fulfilled",l.value=o}},function(o){if(t.status==="pending"){var l=t;l.status="rejected",l.reason=o}})}switch(t.status){case"fulfilled":return t.value;case"rejected":throw e=t.reason,kb(e),e}throw Ps=t,Af=!0,Vs}}function Nb(){if(Ps===null)throw Error("Expected a suspended thenable. This is a bug in React. Please file an issue.");var e=Ps;return Ps=null,Af=!1,e}function kb(e){if(e===Vs||e===wf)throw Error("Hooks are not supported inside an async component. This error is often caused by accidentally adding `'use client'` to a module that was originally written for the server.")}function Uh(e){e.updateQueue={baseState:e.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,lanes:0,hiddenCallbacks:null},callbacks:null}}function Nh(e,t){e=e.updateQueue,t.updateQueue===e&&(t.updateQueue={baseState:e.baseState,firstBaseUpdate:e.firstBaseUpdate,lastBaseUpdate:e.lastBaseUpdate,shared:e.shared,callbacks:null})}function Do(e){return{lane:e,tag:yT,payload:null,callback:null,next:null}}function Co(e,t,n){var a=e.updateQueue;if(a===null)return null;if(a=a.shared,Ym===a&&!_T){var o=ee(e);console.error(`An update (setState, replaceState, or forceUpdate) was scheduled from inside an update function. Update functions should be pure, with zero side-effects. Consider using componentDidUpdate or a callback.

Please update the following component: %s`,o),_T=!0}return(je&Xt)!==Tn?(o=a.pending,o===null?t.next=t:(t.next=o.next,o.next=t),a.pending=t,t=pc(e),bb(e,null,n),t):(hc(e,a,t,n),pc(e))}function es(e,t,n){if(t=t.updateQueue,t!==null&&(t=t.shared,(n&4194048)!==0)){var a=t.lanes;a&=e.pendingLanes,n|=a,t.lanes=n,ac(e,n)}}function Oc(e,t){var n=e.updateQueue,a=e.alternate;if(a!==null&&(a=a.updateQueue,n===a)){var o=null,l=null;if(n=n.firstBaseUpdate,n!==null){do{var c={lane:n.lane,tag:n.tag,payload:n.payload,callback:null,next:null};l===null?o=l=c:l=l.next=c,n=n.next}while(n!==null);l===null?o=l=t:l=l.next=t}else o=l=t;n={baseState:a.baseState,firstBaseUpdate:o,lastBaseUpdate:l,shared:a.shared,callbacks:a.callbacks},e.updateQueue=n;return}e=n.lastBaseUpdate,e===null?n.firstBaseUpdate=t:e.next=t,n.lastBaseUpdate=t}function ts(){if(Xm){var e=Kl;if(e!==null)throw e}}function ns(e,t,n,a){Xm=!1;var o=e.updateQueue;Yo=!1,Ym=o.shared;var l=o.firstBaseUpdate,c=o.lastBaseUpdate,d=o.shared.pending;if(d!==null){o.shared.pending=null;var v=d,y=v.next;v.next=null,c===null?l=y:c.next=y,c=v;var R=e.alternate;R!==null&&(R=R.updateQueue,d=R.lastBaseUpdate,d!==c&&(d===null?R.firstBaseUpdate=y:d.next=y,R.lastBaseUpdate=v))}if(l!==null){var C=o.baseState;c=0,R=y=v=null,d=l;do{var A=d.lane&-536870913,M=A!==d.lane;if(M?(Oe&A)===A:(a&A)===A){A!==0&&A===Bi&&(Xm=!0),R!==null&&(R=R.next={lane:0,tag:d.tag,payload:d.payload,callback:null,next:null});e:{A=e;var Q=d,le=t,Ge=n;switch(Q.tag){case gT:if(Q=Q.payload,typeof Q=="function"){Ql=!0;var we=Q.call(Ge,C,le);if(A.mode&Bt){Ce(!0);try{Q.call(Ge,C,le)}finally{Ce(!1)}}Ql=!1,C=we;break e}C=Q;break e;case Gm:A.flags=A.flags&-65537|128;case yT:if(we=Q.payload,typeof we=="function"){if(Ql=!0,Q=we.call(Ge,C,le),A.mode&Bt){Ce(!0);try{we.call(Ge,C,le)}finally{Ce(!1)}}Ql=!1}else Q=we;if(Q==null)break e;C=ge({},C,Q);break e;case bT:Yo=!0}}A=d.callback,A!==null&&(e.flags|=64,M&&(e.flags|=8192),M=o.callbacks,M===null?o.callbacks=[A]:M.push(A))}else M={lane:A,tag:d.tag,payload:d.payload,callback:d.callback,next:null},R===null?(y=R=M,v=C):R=R.next=M,c|=A;if(d=d.next,d===null){if(d=o.shared.pending,d===null)break;M=d,d=M.next,M.next=null,o.lastBaseUpdate=M,o.shared.pending=null}}while(!0);R===null&&(v=C),o.baseState=v,o.firstBaseUpdate=y,o.lastBaseUpdate=R,l===null&&(o.shared.lanes=0),Zo|=c,e.lanes=c,e.memoizedState=C}Ym=null}function Hb(e,t){if(typeof e!="function")throw Error("Invalid argument passed as callback. Expected a function. Instead received: "+e);e.call(t)}function gR(e,t){var n=e.shared.hiddenCallbacks;if(n!==null)for(e.shared.hiddenCallbacks=null,e=0;e<n.length;e++)Hb(n[e],t)}function Lb(e,t){var n=e.callbacks;if(n!==null)for(e.callbacks=null,e=0;e<n.length;e++)Hb(n[e],t)}function Bb(e,t){var n=ka;Re(xf,n,e),Re(Jl,t,e),ka=n|t.baseLanes}function kh(e){Re(xf,ka,e),Re(Jl,Jl.current,e)}function Hh(e){ka=xf.current,ue(Jl,e),ue(xf,e)}function Te(){var e=z;Zn===null?Zn=[e]:Zn.push(e)}function V(){var e=z;if(Zn!==null&&(ro++,Zn[ro]!==e)){var t=ee(ce);if(!ST.has(t)&&(ST.add(t),Zn!==null)){for(var n="",a=0;a<=ro;a++){var o=Zn[a],l=a===ro?e:o;for(o=a+1+". "+o;30>o.length;)o+=" ";o+=l+`
`,n+=o}console.error(`React has detected a change in the order of Hooks called by %s. This will lead to bugs and errors if not fixed. For more information, read the Rules of Hooks: https://react.dev/link/rules-of-hooks

   Previous render            Next render
   ------------------------------------------------------
%s   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
`,t,n)}}}function gl(e){e==null||Ct(e)||console.error("%s received a final argument that is not an array (instead, received `%s`). When specified, the final argument must be an array.",z,typeof e)}function Ec(){var e=ee(ce);OT.has(e)||(OT.add(e),console.error("ReactDOM.useFormState has been renamed to React.useActionState. Please update %s to use React.useActionState.",e))}function ot(){throw Error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`)}function Lh(e,t){if(qs)return!1;if(t===null)return console.error("%s received a final argument during this render, but not during the previous render. Even though the final argument is optional, its type cannot change between renders.",z),!1;e.length!==t.length&&console.error(`The final argument passed to %s changed size between renders. The order and size of this array must remain constant.

Previous: %s
Incoming: %s`,z,"["+t.join(", ")+"]","["+e.join(", ")+"]");for(var n=0;n<t.length&&n<e.length;n++)if(!qt(e[n],t[n]))return!1;return!0}function Bh(e,t,n,a,o,l){Xo=l,ce=t,Zn=e!==null?e._debugHookTypes:null,ro=-1,qs=e!==null&&e.type!==t.type,(Object.prototype.toString.call(n)==="[object AsyncFunction]"||Object.prototype.toString.call(n)==="[object AsyncGeneratorFunction]")&&(l=ee(ce),Im.has(l)||(Im.add(l),console.error("%s is an async Client Component. Only Server Components can be async at the moment. This error is often caused by accidentally adding `'use client'` to a module that was originally written for the server.",l===null?"An unknown Component":"<"+l+">"))),t.memoizedState=null,t.updateQueue=null,t.lanes=0,D.H=e!==null&&e.memoizedState!==null?Zm:Zn!==null?ET:Qm,$i=l=(t.mode&Bt)!==Xe;var c=Km(n,a,o);if($i=!1,Fl&&(c=Vh(t,n,a,o)),l){Ce(!0);try{c=Vh(t,n,a,o)}finally{Ce(!1)}}return Vb(e,t),c}function Vb(e,t){t._debugHookTypes=Zn,t.dependencies===null?lo!==null&&(t.dependencies={lanes:0,firstContext:null,_debugThenableState:lo}):t.dependencies._debugThenableState=lo,D.H=Df;var n=Pe!==null&&Pe.next!==null;if(Xo=0,Zn=z=dt=Pe=ce=null,ro=-1,e!==null&&(e.flags&65011712)!==(t.flags&65011712)&&console.error("Internal React error: Expected static flag was missing. Please notify the React team."),Rf=!1,$s=0,lo=null,n)throw Error("Rendered fewer hooks than expected. This may be caused by an accidental early return statement.");e===null||Tt||(e=e.dependencies,e!==null&&yc(e)&&(Tt=!0)),Af?(Af=!1,e=!0):e=!1,e&&(t=ee(t)||"Unknown",TT.has(t)||Im.has(t)||(TT.add(t),console.error("`use` was called from inside a try/catch block. This is not allowed and can lead to unexpected behavior. To handle errors triggered by `use`, wrap your component in a error boundary.")))}function Vh(e,t,n,a){ce=e;var o=0;do{if(Fl&&(lo=null),$s=0,Fl=!1,o>=GD)throw Error("Too many re-renders. React limits the number of renders to prevent an infinite loop.");if(o+=1,qs=!1,dt=Pe=null,e.updateQueue!=null){var l=e.updateQueue;l.lastEffect=null,l.events=null,l.stores=null,l.memoCache!=null&&(l.memoCache.index=0)}ro=-1,D.H=wT,l=Km(t,n,a)}while(Fl);return l}function bR(){var e=D.H,t=e.useState()[0];return t=typeof t.then=="function"?as(t):t,e=e.useState()[0],(Pe!==null?Pe.memoizedState:null)!==e&&(ce.flags|=1024),t}function Ph(){var e=zf!==0;return zf=0,e}function $h(e,t,n){t.updateQueue=e.updateQueue,t.flags=(t.mode&ca)!==Xe?t.flags&-402655237:t.flags&-2053,e.lanes&=~n}function qh(e){if(Rf){for(e=e.memoizedState;e!==null;){var t=e.queue;t!==null&&(t.pending=null),e=e.next}Rf=!1}Xo=0,Zn=dt=Pe=ce=null,ro=-1,z=null,Fl=!1,$s=zf=0,lo=null}function Wt(){var e={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return dt===null?ce.memoizedState=dt=e:dt=dt.next=e,dt}function He(){if(Pe===null){var e=ce.alternate;e=e!==null?e.memoizedState:null}else e=Pe.next;var t=dt===null?ce.memoizedState:dt.next;if(t!==null)dt=t,Pe=e;else{if(e===null)throw ce.alternate===null?Error("Update hook called on initial render. This is likely a bug in React. Please file an issue."):Error("Rendered more hooks than during the previous render.");Pe=e,e={memoizedState:Pe.memoizedState,baseState:Pe.baseState,baseQueue:Pe.baseQueue,queue:Pe.queue,next:null},dt===null?ce.memoizedState=dt=e:dt=dt.next=e}return dt}function Gh(){return{lastEffect:null,events:null,stores:null,memoCache:null}}function as(e){var t=$s;return $s+=1,lo===null&&(lo=Mb()),e=Ub(lo,e,t),t=ce,(dt===null?t.memoizedState:dt.next)===null&&(t=t.alternate,D.H=t!==null&&t.memoizedState!==null?Zm:Qm),e}function Mo(e){if(e!==null&&typeof e=="object"){if(typeof e.then=="function")return as(e);if(e.$$typeof===xa)return Je(e)}throw Error("An unsupported type was passed to use(): "+String(e))}function zi(e){var t=null,n=ce.updateQueue;if(n!==null&&(t=n.memoCache),t==null){var a=ce.alternate;a!==null&&(a=a.updateQueue,a!==null&&(a=a.memoCache,a!=null&&(t={data:a.data.map(function(o){return o.slice()}),index:0})))}if(t==null&&(t={data:[],index:0}),n===null&&(n=Gh(),ce.updateQueue=n),n.memoCache=t,n=t.data[t.index],n===void 0||qs)for(n=t.data[t.index]=Array(e),a=0;a<e;a++)n[a]=Dz;else n.length!==e&&console.error("Expected a constant size argument for each invocation of useMemoCache. The previous cache was allocated with size %s but size %s was requested.",n.length,e);return t.index++,n}function sa(e,t){return typeof t=="function"?t(e):t}function Yh(e,t,n){var a=Wt();if(n!==void 0){var o=n(t);if($i){Ce(!0);try{n(t)}finally{Ce(!1)}}}else o=t;return a.memoizedState=a.baseState=o,e={pending:null,lanes:0,dispatch:null,lastRenderedReducer:e,lastRenderedState:o},a.queue=e,e=e.dispatch=OR.bind(null,ce,e),[a.memoizedState,e]}function bl(e){var t=He();return Xh(t,Pe,e)}function Xh(e,t,n){var a=e.queue;if(a===null)throw Error("Should have a queue. You are likely calling Hooks conditionally, which is not allowed. (https://react.dev/link/invalid-hook-call)");a.lastRenderedReducer=n;var o=e.baseQueue,l=a.pending;if(l!==null){if(o!==null){var c=o.next;o.next=l.next,l.next=c}t.baseQueue!==o&&console.error("Internal error: Expected work-in-progress queue to be a clone. This is a bug in React."),t.baseQueue=o=l,a.pending=null}if(l=e.baseState,o===null)e.memoizedState=l;else{t=o.next;var d=c=null,v=null,y=t,R=!1;do{var C=y.lane&-536870913;if(C!==y.lane?(Oe&C)===C:(Xo&C)===C){var A=y.revertLane;if(A===0)v!==null&&(v=v.next={lane:0,revertLane:0,action:y.action,hasEagerState:y.hasEagerState,eagerState:y.eagerState,next:null}),C===Bi&&(R=!0);else if((Xo&A)===A){y=y.next,A===Bi&&(R=!0);continue}else C={lane:0,revertLane:y.revertLane,action:y.action,hasEagerState:y.hasEagerState,eagerState:y.eagerState,next:null},v===null?(d=v=C,c=l):v=v.next=C,ce.lanes|=A,Zo|=A;C=y.action,$i&&n(l,C),l=y.hasEagerState?y.eagerState:n(l,C)}else A={lane:C,revertLane:y.revertLane,action:y.action,hasEagerState:y.hasEagerState,eagerState:y.eagerState,next:null},v===null?(d=v=A,c=l):v=v.next=A,ce.lanes|=C,Zo|=C;y=y.next}while(y!==null&&y!==t);if(v===null?c=l:v.next=d,!qt(l,e.memoizedState)&&(Tt=!0,R&&(n=Kl,n!==null)))throw n;e.memoizedState=l,e.baseState=c,e.baseQueue=v,a.lastRenderedState=l}return o===null&&(a.lanes=0),[e.memoizedState,a.dispatch]}function os(e){var t=He(),n=t.queue;if(n===null)throw Error("Should have a queue. You are likely calling Hooks conditionally, which is not allowed. (https://react.dev/link/invalid-hook-call)");n.lastRenderedReducer=e;var a=n.dispatch,o=n.pending,l=t.memoizedState;if(o!==null){n.pending=null;var c=o=o.next;do l=e(l,c.action),c=c.next;while(c!==o);qt(l,t.memoizedState)||(Tt=!0),t.memoizedState=l,t.baseQueue===null&&(t.baseState=l),n.lastRenderedState=l}return[l,a]}function Ih(e,t,n){var a=ce,o=Wt();if(xe){if(n===void 0)throw Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");var l=n();Wl||l===n()||(console.error("The result of getServerSnapshot should be cached to avoid an infinite loop"),Wl=!0)}else{if(l=t(),Wl||(n=t(),qt(l,n)||(console.error("The result of getSnapshot should be cached to avoid an infinite loop"),Wl=!0)),qe===null)throw Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");(Oe&124)!==0||Pb(a,t,l)}return o.memoizedState=l,n={value:l,getSnapshot:t},o.queue=n,zc(qb.bind(null,a,n,e),[e]),a.flags|=2048,Sl(Qn|yt,Rc(),$b.bind(null,a,n,l,t),null),l}function wc(e,t,n){var a=ce,o=He(),l=xe;if(l){if(n===void 0)throw Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");n=n()}else if(n=t(),!Wl){var c=t();qt(n,c)||(console.error("The result of getSnapshot should be cached to avoid an infinite loop"),Wl=!0)}(c=!qt((Pe||o).memoizedState,n))&&(o.memoizedState=n,Tt=!0),o=o.queue;var d=qb.bind(null,a,o,e);if(Ft(2048,yt,d,[e]),o.getSnapshot!==t||c||dt!==null&&dt.memoizedState.tag&Qn){if(a.flags|=2048,Sl(Qn|yt,Rc(),$b.bind(null,a,o,n,t),null),qe===null)throw Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");l||(Xo&124)!==0||Pb(a,t,n)}return n}function Pb(e,t,n){e.flags|=16384,e={getSnapshot:t,value:n},t=ce.updateQueue,t===null?(t=Gh(),ce.updateQueue=t,t.stores=[e]):(n=t.stores,n===null?t.stores=[e]:n.push(e))}function $b(e,t,n,a){t.value=n,t.getSnapshot=a,Gb(t)&&Yb(e)}function qb(e,t,n){return n(function(){Gb(t)&&Yb(e)})}function Gb(e){var t=e.getSnapshot;e=e.value;try{var n=t();return!qt(e,n)}catch{return!0}}function Yb(e){var t=Jt(e,2);t!==null&&it(t,e,2)}function Qh(e){var t=Wt();if(typeof e=="function"){var n=e;if(e=n(),$i){Ce(!0);try{n()}finally{Ce(!1)}}}return t.memoizedState=t.baseState=e,t.queue={pending:null,lanes:0,dispatch:null,lastRenderedReducer:sa,lastRenderedState:e},t}function Zh(e){e=Qh(e);var t=e.queue,n=c0.bind(null,ce,t);return t.dispatch=n,[e.memoizedState,n]}function Kh(e){var t=Wt();t.memoizedState=t.baseState=e;var n={pending:null,lanes:0,dispatch:null,lastRenderedReducer:null,lastRenderedState:null};return t.queue=n,t=up.bind(null,ce,!0,n),n.dispatch=t,[e,t]}function Xb(e,t){var n=He();return Ib(n,Pe,e,t)}function Ib(e,t,n,a){return e.baseState=n,Xh(e,Pe,typeof a=="function"?a:sa)}function Qb(e,t){var n=He();return Pe!==null?Ib(n,Pe,e,t):(n.baseState=e,[e,n.queue.dispatch])}function _R(e,t,n,a,o){if(jc(e))throw Error("Cannot update form state while rendering.");if(e=t.action,e!==null){var l={payload:o,action:e,next:null,isTransition:!0,status:"pending",value:null,reason:null,listeners:[],then:function(c){l.listeners.push(c)}};D.T!==null?n(!0):l.isTransition=!1,a(l),n=t.pending,n===null?(l.next=t.pending=l,Zb(t,l)):(l.next=n.next,t.pending=n.next=l)}}function Zb(e,t){var n=t.action,a=t.payload,o=e.state;if(t.isTransition){var l=D.T,c={};D.T=c,D.T._updatedFibers=new Set;try{var d=n(o,a),v=D.S;v!==null&&v(c,d),Kb(e,t,d)}catch(y){Jh(e,t,y)}finally{D.T=l,l===null&&c._updatedFibers&&(e=c._updatedFibers.size,c._updatedFibers.clear(),10<e&&console.warn("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."))}}else try{c=n(o,a),Kb(e,t,c)}catch(y){Jh(e,t,y)}}function Kb(e,t,n){n!==null&&typeof n=="object"&&typeof n.then=="function"?(n.then(function(a){Jb(e,t,a)},function(a){return Jh(e,t,a)}),t.isTransition||console.error("An async function with useActionState was called outside of a transition. This is likely not what you intended (for example, isPending will not update correctly). Either call the returned function inside startTransition, or pass it to an `action` or `formAction` prop.")):Jb(e,t,n)}function Jb(e,t,n){t.status="fulfilled",t.value=n,Wb(t),e.state=n,t=e.pending,t!==null&&(n=t.next,n===t?e.pending=null:(n=n.next,t.next=n,Zb(e,n)))}function Jh(e,t,n){var a=e.pending;if(e.pending=null,a!==null){a=a.next;do t.status="rejected",t.reason=n,Wb(t),t=t.next;while(t!==a)}e.action=null}function Wb(e){e=e.listeners;for(var t=0;t<e.length;t++)(0,e[t])()}function Fb(e,t){return t}function _l(e,t){if(xe){var n=qe.formState;if(n!==null){e:{var a=ce;if(xe){if(tt){t:{for(var o=tt,l=ja;o.nodeType!==8;){if(!l){o=null;break t}if(o=Vn(o.nextSibling),o===null){o=null;break t}}l=o.data,o=l===Tv||l===O1?o:null}if(o){tt=Vn(o.nextSibling),a=o.data===Tv;break e}}Ai(a)}a=!1}a&&(t=n[0])}}return n=Wt(),n.memoizedState=n.baseState=t,a={pending:null,lanes:0,dispatch:null,lastRenderedReducer:Fb,lastRenderedState:t},n.queue=a,n=c0.bind(null,ce,a),a.dispatch=n,a=Qh(!1),l=up.bind(null,ce,!1,a.queue),a=Wt(),o={state:t,dispatch:null,action:e,pending:null},a.queue=o,n=_R.bind(null,ce,o,l,n),o.dispatch=n,a.memoizedState=e,[t,n,!1]}function Ac(e){var t=He();return e0(t,Pe,e)}function e0(e,t,n){if(t=Xh(e,t,Fb)[0],e=bl(sa)[0],typeof t=="object"&&t!==null&&typeof t.then=="function")try{var a=as(t)}catch(c){throw c===Vs?wf:c}else a=t;t=He();var o=t.queue,l=o.dispatch;return n!==t.memoizedState&&(ce.flags|=2048,Sl(Qn|yt,Rc(),SR.bind(null,o,n),null)),[a,l,e]}function SR(e,t){e.action=t}function xc(e){var t=He(),n=Pe;if(n!==null)return e0(t,n,e);He(),t=t.memoizedState,n=He();var a=n.queue.dispatch;return n.memoizedState=e,[t,a,!1]}function Sl(e,t,n,a){return e={tag:e,create:n,deps:a,inst:t,next:null},t=ce.updateQueue,t===null&&(t=Gh(),ce.updateQueue=t),n=t.lastEffect,n===null?t.lastEffect=e.next=e:(a=n.next,n.next=e,e.next=a,t.lastEffect=e),e}function Rc(){return{destroy:void 0,resource:void 0}}function Wh(e){var t=Wt();return e={current:e},t.memoizedState=e}function Di(e,t,n,a){var o=Wt();a=a===void 0?null:a,ce.flags|=e,o.memoizedState=Sl(Qn|t,Rc(),n,a)}function Ft(e,t,n,a){var o=He();a=a===void 0?null:a;var l=o.memoizedState.inst;Pe!==null&&a!==null&&Lh(a,Pe.memoizedState.deps)?o.memoizedState=Sl(t,l,n,a):(ce.flags|=e,o.memoizedState=Sl(Qn|t,l,n,a))}function zc(e,t){(ce.mode&ca)!==Xe&&(ce.mode&rT)===Xe?Di(276826112,yt,e,t):Di(8390656,yt,e,t)}function Fh(e,t){var n=4194308;return(ce.mode&ca)!==Xe&&(n|=134217728),Di(n,jt,e,t)}function t0(e,t){if(typeof t=="function"){e=e();var n=t(e);return function(){typeof n=="function"?n():t(null)}}if(t!=null)return t.hasOwnProperty("current")||console.error("Expected useImperativeHandle() first argument to either be a ref callback or React.createRef() object. Instead received: %s.","an object with keys {"+Object.keys(t).join(", ")+"}"),e=e(),t.current=e,function(){t.current=null}}function ep(e,t,n){typeof t!="function"&&console.error("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null"),n=n!=null?n.concat([e]):null;var a=4194308;(ce.mode&ca)!==Xe&&(a|=134217728),Di(a,jt,t0.bind(null,t,e),n)}function Dc(e,t,n){typeof t!="function"&&console.error("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null"),n=n!=null?n.concat([e]):null,Ft(4,jt,t0.bind(null,t,e),n)}function tp(e,t){return Wt().memoizedState=[e,t===void 0?null:t],e}function Cc(e,t){var n=He();t=t===void 0?null:t;var a=n.memoizedState;return t!==null&&Lh(t,a[1])?a[0]:(n.memoizedState=[e,t],e)}function np(e,t){var n=Wt();t=t===void 0?null:t;var a=e();if($i){Ce(!0);try{e()}finally{Ce(!1)}}return n.memoizedState=[a,t],a}function Mc(e,t){var n=He();t=t===void 0?null:t;var a=n.memoizedState;if(t!==null&&Lh(t,a[1]))return a[0];if(a=e(),$i){Ce(!0);try{e()}finally{Ce(!1)}}return n.memoizedState=[a,t],a}function ap(e,t){var n=Wt();return op(n,e,t)}function n0(e,t){var n=He();return o0(n,Pe.memoizedState,e,t)}function a0(e,t){var n=He();return Pe===null?op(n,e,t):o0(n,Pe.memoizedState,e,t)}function op(e,t,n){return n===void 0||(Xo&1073741824)!==0?e.memoizedState=t:(e.memoizedState=n,e=i_(),ce.lanes|=e,Zo|=e,n)}function o0(e,t,n,a){return qt(n,t)?n:Jl.current!==null?(e=op(e,n,a),qt(e,t)||(Tt=!0),e):(Xo&42)===0?(Tt=!0,e.memoizedState=n):(e=i_(),ce.lanes|=e,Zo|=e,t)}function i0(e,t,n,a,o){var l=Me.p;Me.p=l!==0&&l<Da?l:Da;var c=D.T,d={};D.T=d,up(e,!1,t,n),d._updatedFibers=new Set;try{var v=o(),y=D.S;if(y!==null&&y(d,v),v!==null&&typeof v=="object"&&typeof v.then=="function"){var R=yR(v,a);is(e,t,R,bn(e))}else is(e,t,a,bn(e))}catch(C){is(e,t,{then:function(){},status:"rejected",reason:C},bn(e))}finally{Me.p=l,D.T=c,c===null&&d._updatedFibers&&(e=d._updatedFibers.size,d._updatedFibers.clear(),10<e&&console.warn("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."))}}function ip(e,t,n,a){if(e.tag!==5)throw Error("Expected the form instance to be a HostComponent. This is a bug in React.");var o=l0(e).queue;i0(e,o,t,Fi,n===null?U:function(){return r0(e),n(a)})}function l0(e){var t=e.memoizedState;if(t!==null)return t;t={memoizedState:Fi,baseState:Fi,baseQueue:null,queue:{pending:null,lanes:0,dispatch:null,lastRenderedReducer:sa,lastRenderedState:Fi},next:null};var n={};return t.next={memoizedState:n,baseState:n,baseQueue:null,queue:{pending:null,lanes:0,dispatch:null,lastRenderedReducer:sa,lastRenderedState:n},next:null},e.memoizedState=t,e=e.alternate,e!==null&&(e.memoizedState=t),t}function r0(e){D.T===null&&console.error("requestFormReset was called outside a transition or action. To fix, move to an action, or wrap with startTransition.");var t=l0(e).next.queue;is(e,t,{},bn(e))}function lp(){var e=Qh(!1);return e=i0.bind(null,ce,e.queue,!0,!1),Wt().memoizedState=e,[!1,e]}function s0(){var e=bl(sa)[0],t=He().memoizedState;return[typeof e=="boolean"?e:as(e),t]}function u0(){var e=os(sa)[0],t=He().memoizedState;return[typeof e=="boolean"?e:as(e),t]}function Ci(){return Je(lu)}function rp(){var e=Wt(),t=qe.identifierPrefix;if(xe){var n=oo,a=ao;n=(a&~(1<<32-Pt(a)-1)).toString(32)+n,t="«"+t+"R"+n,n=zf++,0<n&&(t+="H"+n.toString(32)),t+="»"}else n=qD++,t="«"+t+"r"+n.toString(32)+"»";return e.memoizedState=t}function sp(){return Wt().memoizedState=TR.bind(null,ce)}function TR(e,t){for(var n=e.return;n!==null;){switch(n.tag){case 24:case 3:var a=bn(n);e=Do(a);var o=Co(n,e,a);o!==null&&(it(o,n,a),es(o,n,a)),n=Dh(),t!=null&&o!==null&&console.error("The seed argument is not enabled outside experimental channels."),e.payload={cache:n};return}n=n.return}}function OR(e,t,n){var a=arguments;typeof a[3]=="function"&&console.error("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect()."),a=bn(e);var o={lane:a,revertLane:0,action:n,hasEagerState:!1,eagerState:null,next:null};jc(e)?f0(t,o):(o=gh(e,t,o,a),o!==null&&(it(o,e,a),d0(o,t,a))),Eo(e,a)}function c0(e,t,n){var a=arguments;typeof a[3]=="function"&&console.error("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect()."),a=bn(e),is(e,t,n,a),Eo(e,a)}function is(e,t,n,a){var o={lane:a,revertLane:0,action:n,hasEagerState:!1,eagerState:null,next:null};if(jc(e))f0(t,o);else{var l=e.alternate;if(e.lanes===0&&(l===null||l.lanes===0)&&(l=t.lastRenderedReducer,l!==null)){var c=D.H;D.H=da;try{var d=t.lastRenderedState,v=l(d,n);if(o.hasEagerState=!0,o.eagerState=v,qt(v,d))return hc(e,t,o,0),qe===null&&dc(),!1}catch{}finally{D.H=c}}if(n=gh(e,t,o,a),n!==null)return it(n,e,a),d0(n,t,a),!0}return!1}function up(e,t,n,a){if(D.T===null&&Bi===0&&console.error("An optimistic state update occurred outside a transition or action. To fix, move the update to an action, or wrap with startTransition."),a={lane:2,revertLane:Vp(),action:a,hasEagerState:!1,eagerState:null,next:null},jc(e)){if(t)throw Error("Cannot update optimistic state while rendering.");console.error("Cannot call startTransition while rendering.")}else t=gh(e,n,a,2),t!==null&&it(t,e,2);Eo(e,2)}function jc(e){var t=e.alternate;return e===ce||t!==null&&t===ce}function f0(e,t){Fl=Rf=!0;var n=e.pending;n===null?t.next=t:(t.next=n.next,n.next=t),e.pending=t}function d0(e,t,n){if((n&4194048)!==0){var a=t.lanes;a&=e.pendingLanes,n|=a,t.lanes=n,ac(e,n)}}function Rt(e){var t=ve;return e!=null&&(ve=t===null?e:t.concat(e)),t}function Uc(e,t,n){for(var a=Object.keys(e.props),o=0;o<a.length;o++){var l=a[o];if(l!=="children"&&l!=="key"){t===null&&(t=mc(e,n.mode,0),t._debugInfo=ve,t.return=n),W(t,function(c){console.error("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",c)},l);break}}}function Nc(e){var t=Gs;return Gs+=1,er===null&&(er=Mb()),Ub(er,e,t)}function ls(e,t){t=t.props.ref,e.ref=t!==void 0?t:null}function kc(e,t){throw t.$$typeof===Rz?Error(`A React Element from an older version of React was rendered. This is not supported. It can happen if:
- Multiple copies of the "react" package is used.
- A library pre-bundled an old copy of "react" or "react/jsx-runtime".
- A compiler tries to "inline" JSX instead of using the runtime.`):(e=Object.prototype.toString.call(t),Error("Objects are not valid as a React child (found: "+(e==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":e)+"). If you meant to render a collection of children, use an array instead."))}function Hc(e,t){var n=ee(e)||"Component";VT[n]||(VT[n]=!0,t=t.displayName||t.name||"Component",e.tag===3?console.error(`Functions are not valid as a React child. This may happen if you return %s instead of <%s /> from render. Or maybe you meant to call this function rather than return it.
  root.render(%s)`,t,t,t):console.error(`Functions are not valid as a React child. This may happen if you return %s instead of <%s /> from render. Or maybe you meant to call this function rather than return it.
  <%s>{%s}</%s>`,t,t,n,t,n))}function Lc(e,t){var n=ee(e)||"Component";PT[n]||(PT[n]=!0,t=String(t),e.tag===3?console.error(`Symbols are not valid as a React child.
  root.render(%s)`,t):console.error(`Symbols are not valid as a React child.
  <%s>%s</%s>`,n,t,n))}function h0(e){function t(S,T){if(e){var E=S.deletions;E===null?(S.deletions=[T],S.flags|=16):E.push(T)}}function n(S,T){if(!e)return null;for(;T!==null;)t(S,T),T=T.sibling;return null}function a(S){for(var T=new Map;S!==null;)S.key!==null?T.set(S.key,S):T.set(S.index,S),S=S.sibling;return T}function o(S,T){return S=Ga(S,T),S.index=0,S.sibling=null,S}function l(S,T,E){return S.index=E,e?(E=S.alternate,E!==null?(E=E.index,E<T?(S.flags|=67108866,T):E):(S.flags|=67108866,T)):(S.flags|=1048576,T)}function c(S){return e&&S.alternate===null&&(S.flags|=67108866),S}function d(S,T,E,j){return T===null||T.tag!==6?(T=Oh(E,S.mode,j),T.return=S,T._debugOwner=S,T._debugTask=S._debugTask,T._debugInfo=ve,T):(T=o(T,E),T.return=S,T._debugInfo=ve,T)}function v(S,T,E,j){var $=E.type;return $===Ml?(T=R(S,T,E.props.children,j,E.key),Uc(E,T,S),T):T!==null&&(T.elementType===$||_b(T,E)||typeof $=="object"&&$!==null&&$.$$typeof===_n&&Io($)===T.type)?(T=o(T,E.props),ls(T,E),T.return=S,T._debugOwner=E._owner,T._debugInfo=ve,T):(T=mc(E,S.mode,j),ls(T,E),T.return=S,T._debugInfo=ve,T)}function y(S,T,E,j){return T===null||T.tag!==4||T.stateNode.containerInfo!==E.containerInfo||T.stateNode.implementation!==E.implementation?(T=Eh(E,S.mode,j),T.return=S,T._debugInfo=ve,T):(T=o(T,E.children||[]),T.return=S,T._debugInfo=ve,T)}function R(S,T,E,j,$){return T===null||T.tag!==7?(T=Ti(E,S.mode,j,$),T.return=S,T._debugOwner=S,T._debugTask=S._debugTask,T._debugInfo=ve,T):(T=o(T,E),T.return=S,T._debugInfo=ve,T)}function C(S,T,E){if(typeof T=="string"&&T!==""||typeof T=="number"||typeof T=="bigint")return T=Oh(""+T,S.mode,E),T.return=S,T._debugOwner=S,T._debugTask=S._debugTask,T._debugInfo=ve,T;if(typeof T=="object"&&T!==null){switch(T.$$typeof){case Lo:return E=mc(T,S.mode,E),ls(E,T),E.return=S,S=Rt(T._debugInfo),E._debugInfo=ve,ve=S,E;case Cl:return T=Eh(T,S.mode,E),T.return=S,T._debugInfo=ve,T;case _n:var j=Rt(T._debugInfo);return T=Io(T),S=C(S,T,E),ve=j,S}if(Ct(T)||Fe(T))return E=Ti(T,S.mode,E,null),E.return=S,E._debugOwner=S,E._debugTask=S._debugTask,S=Rt(T._debugInfo),E._debugInfo=ve,ve=S,E;if(typeof T.then=="function")return j=Rt(T._debugInfo),S=C(S,Nc(T),E),ve=j,S;if(T.$$typeof===xa)return C(S,gc(S,T),E);kc(S,T)}return typeof T=="function"&&Hc(S,T),typeof T=="symbol"&&Lc(S,T),null}function A(S,T,E,j){var $=T!==null?T.key:null;if(typeof E=="string"&&E!==""||typeof E=="number"||typeof E=="bigint")return $!==null?null:d(S,T,""+E,j);if(typeof E=="object"&&E!==null){switch(E.$$typeof){case Lo:return E.key===$?($=Rt(E._debugInfo),S=v(S,T,E,j),ve=$,S):null;case Cl:return E.key===$?y(S,T,E,j):null;case _n:return $=Rt(E._debugInfo),E=Io(E),S=A(S,T,E,j),ve=$,S}if(Ct(E)||Fe(E))return $!==null?null:($=Rt(E._debugInfo),S=R(S,T,E,j,null),ve=$,S);if(typeof E.then=="function")return $=Rt(E._debugInfo),S=A(S,T,Nc(E),j),ve=$,S;if(E.$$typeof===xa)return A(S,T,gc(S,E),j);kc(S,E)}return typeof E=="function"&&Hc(S,E),typeof E=="symbol"&&Lc(S,E),null}function M(S,T,E,j,$){if(typeof j=="string"&&j!==""||typeof j=="number"||typeof j=="bigint")return S=S.get(E)||null,d(T,S,""+j,$);if(typeof j=="object"&&j!==null){switch(j.$$typeof){case Lo:return E=S.get(j.key===null?E:j.key)||null,S=Rt(j._debugInfo),T=v(T,E,j,$),ve=S,T;case Cl:return S=S.get(j.key===null?E:j.key)||null,y(T,S,j,$);case _n:var fe=Rt(j._debugInfo);return j=Io(j),T=M(S,T,E,j,$),ve=fe,T}if(Ct(j)||Fe(j))return E=S.get(E)||null,S=Rt(j._debugInfo),T=R(T,E,j,$,null),ve=S,T;if(typeof j.then=="function")return fe=Rt(j._debugInfo),T=M(S,T,E,Nc(j),$),ve=fe,T;if(j.$$typeof===xa)return M(S,T,E,gc(T,j),$);kc(T,j)}return typeof j=="function"&&Hc(T,j),typeof j=="symbol"&&Lc(T,j),null}function Q(S,T,E,j){if(typeof E!="object"||E===null)return j;switch(E.$$typeof){case Lo:case Cl:b(S,T,E);var $=E.key;if(typeof $!="string")break;if(j===null){j=new Set,j.add($);break}if(!j.has($)){j.add($);break}W(T,function(){console.error("Encountered two children with the same key, `%s`. Keys should be unique so that components maintain their identity across updates. Non-unique keys may cause children to be duplicated and/or omitted — the behavior is unsupported and could change in a future version.",$)});break;case _n:E=Io(E),Q(S,T,E,j)}return j}function le(S,T,E,j){for(var $=null,fe=null,Z=null,de=T,he=T=0,Ie=null;de!==null&&he<E.length;he++){de.index>he?(Ie=de,de=null):Ie=de.sibling;var rt=A(S,de,E[he],j);if(rt===null){de===null&&(de=Ie);break}$=Q(S,rt,E[he],$),e&&de&&rt.alternate===null&&t(S,de),T=l(rt,T,he),Z===null?fe=rt:Z.sibling=rt,Z=rt,de=Ie}if(he===E.length)return n(S,de),xe&&Oi(S,he),fe;if(de===null){for(;he<E.length;he++)de=C(S,E[he],j),de!==null&&($=Q(S,de,E[he],$),T=l(de,T,he),Z===null?fe=de:Z.sibling=de,Z=de);return xe&&Oi(S,he),fe}for(de=a(de);he<E.length;he++)Ie=M(de,S,he,E[he],j),Ie!==null&&($=Q(S,Ie,E[he],$),e&&Ie.alternate!==null&&de.delete(Ie.key===null?he:Ie.key),T=l(Ie,T,he),Z===null?fe=Ie:Z.sibling=Ie,Z=Ie);return e&&de.forEach(function(po){return t(S,po)}),xe&&Oi(S,he),fe}function Ge(S,T,E,j){if(E==null)throw Error("An iterable object provided no iterator.");for(var $=null,fe=null,Z=T,de=T=0,he=null,Ie=null,rt=E.next();Z!==null&&!rt.done;de++,rt=E.next()){Z.index>de?(he=Z,Z=null):he=Z.sibling;var po=A(S,Z,rt.value,j);if(po===null){Z===null&&(Z=he);break}Ie=Q(S,po,rt.value,Ie),e&&Z&&po.alternate===null&&t(S,Z),T=l(po,T,de),fe===null?$=po:fe.sibling=po,fe=po,Z=he}if(rt.done)return n(S,Z),xe&&Oi(S,de),$;if(Z===null){for(;!rt.done;de++,rt=E.next())Z=C(S,rt.value,j),Z!==null&&(Ie=Q(S,Z,rt.value,Ie),T=l(Z,T,de),fe===null?$=Z:fe.sibling=Z,fe=Z);return xe&&Oi(S,de),$}for(Z=a(Z);!rt.done;de++,rt=E.next())he=M(Z,S,de,rt.value,j),he!==null&&(Ie=Q(S,he,rt.value,Ie),e&&he.alternate!==null&&Z.delete(he.key===null?de:he.key),T=l(he,T,de),fe===null?$=he:fe.sibling=he,fe=he);return e&&Z.forEach(function(vC){return t(S,vC)}),xe&&Oi(S,de),$}function we(S,T,E,j){if(typeof E=="object"&&E!==null&&E.type===Ml&&E.key===null&&(Uc(E,null,S),E=E.props.children),typeof E=="object"&&E!==null){switch(E.$$typeof){case Lo:var $=Rt(E._debugInfo);e:{for(var fe=E.key;T!==null;){if(T.key===fe){if(fe=E.type,fe===Ml){if(T.tag===7){n(S,T.sibling),j=o(T,E.props.children),j.return=S,j._debugOwner=E._owner,j._debugInfo=ve,Uc(E,j,S),S=j;break e}}else if(T.elementType===fe||_b(T,E)||typeof fe=="object"&&fe!==null&&fe.$$typeof===_n&&Io(fe)===T.type){n(S,T.sibling),j=o(T,E.props),ls(j,E),j.return=S,j._debugOwner=E._owner,j._debugInfo=ve,S=j;break e}n(S,T);break}else t(S,T);T=T.sibling}E.type===Ml?(j=Ti(E.props.children,S.mode,j,E.key),j.return=S,j._debugOwner=S,j._debugTask=S._debugTask,j._debugInfo=ve,Uc(E,j,S),S=j):(j=mc(E,S.mode,j),ls(j,E),j.return=S,j._debugInfo=ve,S=j)}return S=c(S),ve=$,S;case Cl:e:{for($=E,E=$.key;T!==null;){if(T.key===E)if(T.tag===4&&T.stateNode.containerInfo===$.containerInfo&&T.stateNode.implementation===$.implementation){n(S,T.sibling),j=o(T,$.children||[]),j.return=S,S=j;break e}else{n(S,T);break}else t(S,T);T=T.sibling}j=Eh($,S.mode,j),j.return=S,S=j}return c(S);case _n:return $=Rt(E._debugInfo),E=Io(E),S=we(S,T,E,j),ve=$,S}if(Ct(E))return $=Rt(E._debugInfo),S=le(S,T,E,j),ve=$,S;if(Fe(E)){if($=Rt(E._debugInfo),fe=Fe(E),typeof fe!="function")throw Error("An object is not an iterable. This error is likely caused by a bug in React. Please file an issue.");var Z=fe.call(E);return Z===E?(S.tag!==0||Object.prototype.toString.call(S.type)!=="[object GeneratorFunction]"||Object.prototype.toString.call(Z)!=="[object Generator]")&&(LT||console.error("Using Iterators as children is unsupported and will likely yield unexpected results because enumerating a generator mutates it. You may convert it to an array with `Array.from()` or the `[...spread]` operator before rendering. You can also use an Iterable that can iterate multiple times over the same items."),LT=!0):E.entries!==fe||Wm||(console.error("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),Wm=!0),S=Ge(S,T,Z,j),ve=$,S}if(typeof E.then=="function")return $=Rt(E._debugInfo),S=we(S,T,Nc(E),j),ve=$,S;if(E.$$typeof===xa)return we(S,T,gc(S,E),j);kc(S,E)}return typeof E=="string"&&E!==""||typeof E=="number"||typeof E=="bigint"?($=""+E,T!==null&&T.tag===6?(n(S,T.sibling),j=o(T,$),j.return=S,S=j):(n(S,T),j=Oh($,S.mode,j),j.return=S,j._debugOwner=S,j._debugTask=S._debugTask,j._debugInfo=ve,S=j),c(S)):(typeof E=="function"&&Hc(S,E),typeof E=="symbol"&&Lc(S,E),n(S,T))}return function(S,T,E,j){var $=ve;ve=null;try{Gs=0;var fe=we(S,T,E,j);return er=null,fe}catch(Ie){if(Ie===Vs||Ie===wf)throw Ie;var Z=O(29,Ie,null,S.mode);Z.lanes=j,Z.return=S;var de=Z._debugInfo=ve;if(Z._debugOwner=S._debugOwner,Z._debugTask=S._debugTask,de!=null){for(var he=de.length-1;0<=he;he--)if(typeof de[he].stack=="string"){Z._debugOwner=de[he],Z._debugTask=de[he].debugTask;break}}return Z}finally{ve=$}}}function jo(e){var t=e.alternate;Re(gt,gt.current&nr,e),Re(Kn,e,e),Na===null&&(t===null||Jl.current!==null||t.memoizedState!==null)&&(Na=e)}function p0(e){if(e.tag===22){if(Re(gt,gt.current,e),Re(Kn,e,e),Na===null){var t=e.alternate;t!==null&&t.memoizedState!==null&&(Na=e)}}else Uo(e)}function Uo(e){Re(gt,gt.current,e),Re(Kn,Kn.current,e)}function Ia(e){ue(Kn,e),Na===e&&(Na=null),ue(gt,e)}function Bc(e){for(var t=e;t!==null;){if(t.tag===13){var n=t.memoizedState;if(n!==null&&(n=n.dehydrated,n===null||n.data===co||Jp(n)))return t}else if(t.tag===19&&t.memoizedProps.revealOrder!==void 0){if((t.flags&128)!==0)return t}else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break;for(;t.sibling===null;){if(t.return===null||t.return===e)return null;t=t.return}t.sibling.return=t.return,t=t.sibling}return null}function cp(e){if(e!==null&&typeof e!="function"){var t=String(e);FT.has(t)||(FT.add(t),console.error("Expected the last optional `callback` argument to be a function. Instead received: %s.",e))}}function fp(e,t,n,a){var o=e.memoizedState,l=n(a,o);if(e.mode&Bt){Ce(!0);try{l=n(a,o)}finally{Ce(!1)}}l===void 0&&(t=ke(t)||"Component",ZT.has(t)||(ZT.add(t),console.error("%s.getDerivedStateFromProps(): A valid state object (or null) must be returned. You have returned undefined.",t))),o=l==null?o:ge({},o,l),e.memoizedState=o,e.lanes===0&&(e.updateQueue.baseState=o)}function m0(e,t,n,a,o,l,c){var d=e.stateNode;if(typeof d.shouldComponentUpdate=="function"){if(n=d.shouldComponentUpdate(a,l,c),e.mode&Bt){Ce(!0);try{n=d.shouldComponentUpdate(a,l,c)}finally{Ce(!1)}}return n===void 0&&console.error("%s.shouldComponentUpdate(): Returned undefined instead of a boolean value. Make sure to return true or false.",ke(t)||"Component"),n}return t.prototype&&t.prototype.isPureReactComponent?!Ir(n,a)||!Ir(o,l):!0}function v0(e,t,n,a){var o=t.state;typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps(n,a),typeof t.UNSAFE_componentWillReceiveProps=="function"&&t.UNSAFE_componentWillReceiveProps(n,a),t.state!==o&&(e=ee(e)||"Component",GT.has(e)||(GT.add(e),console.error("%s.componentWillReceiveProps(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",e)),Fm.enqueueReplaceState(t,t.state,null))}function Mi(e,t){var n=t;if("ref"in t){n={};for(var a in t)a!=="ref"&&(n[a]=t[a])}if(e=e.defaultProps){n===t&&(n=ge({},n));for(var o in e)n[o]===void 0&&(n[o]=e[o])}return n}function y0(e){ev(e),console.warn(`%s

%s
`,ar?"An error occurred in the <"+ar+"> component.":"An error occurred in one of your React components.",`Consider adding an error boundary to your tree to customize error handling behavior.
Visit https://react.dev/link/error-boundaries to learn more about error boundaries.`)}function g0(e){var t=ar?"The above error occurred in the <"+ar+"> component.":"The above error occurred in one of your React components.",n="React will try to recreate this component tree from scratch using the error boundary you provided, "+((tv||"Anonymous")+".");if(typeof e=="object"&&e!==null&&typeof e.environmentName=="string"){var a=e.environmentName;e=[`%o

%s

%s
`,e,t,n].slice(0),typeof e[0]=="string"?e.splice(0,1,C1+e[0],M1,Zf+a+Zf,j1):e.splice(0,0,C1,M1,Zf+a+Zf,j1),e.unshift(console),a=pC.apply(console.error,e),a()}else console.error(`%o

%s

%s
`,e,t,n)}function b0(e){ev(e)}function Vc(e,t){try{ar=t.source?ee(t.source):null,tv=null;var n=t.value;if(D.actQueue!==null)D.thrownErrors.push(n);else{var a=e.onUncaughtError;a(n,{componentStack:t.stack})}}catch(o){setTimeout(function(){throw o})}}function _0(e,t,n){try{ar=n.source?ee(n.source):null,tv=ee(t);var a=e.onCaughtError;a(n.value,{componentStack:n.stack,errorBoundary:t.tag===1?t.stateNode:null})}catch(o){setTimeout(function(){throw o})}}function dp(e,t,n){return n=Do(n),n.tag=Gm,n.payload={element:null},n.callback=function(){W(t.source,Vc,e,t)},n}function hp(e){return e=Do(e),e.tag=Gm,e}function pp(e,t,n,a){var o=n.type.getDerivedStateFromError;if(typeof o=="function"){var l=a.value;e.payload=function(){return o(l)},e.callback=function(){Sb(n),W(a.source,_0,t,n,a)}}var c=n.stateNode;c!==null&&typeof c.componentDidCatch=="function"&&(e.callback=function(){Sb(n),W(a.source,_0,t,n,a),typeof o!="function"&&(Jo===null?Jo=new Set([this]):Jo.add(this)),YD(this,a),typeof o=="function"||(n.lanes&2)===0&&console.error("%s: Error boundaries should implement getDerivedStateFromError(). In that method, return a state update to display an error message or fallback UI.",ee(n)||"Unknown")})}function ER(e,t,n,a,o){if(n.flags|=32768,ua&&hs(e,o),a!==null&&typeof a=="object"&&typeof a.then=="function"){if(t=n.alternate,t!==null&&Jr(t,n,o,!0),xe&&(io=!0),n=Kn.current,n!==null){switch(n.tag){case 13:return Na===null?jp():n.alternate===null&&nt===uo&&(nt=iv),n.flags&=-257,n.flags|=65536,n.lanes=o,a===qm?n.flags|=16384:(t=n.updateQueue,t===null?n.updateQueue=new Set([a]):t.add(a),kp(e,a,o)),!1;case 22:return n.flags|=65536,a===qm?n.flags|=16384:(t=n.updateQueue,t===null?(t={transitions:null,markerInstances:null,retryQueue:new Set([a])},n.updateQueue=t):(n=t.retryQueue,n===null?t.retryQueue=new Set([a]):n.add(a)),kp(e,a,o)),!1}throw Error("Unexpected Suspense handler tag ("+n.tag+"). This is a bug in React.")}return kp(e,a,o),jp(),!1}if(xe)return io=!0,t=Kn.current,t!==null?((t.flags&65536)===0&&(t.flags|=256),t.flags|=65536,t.lanes=o,a!==Bm&&Kr(vn(Error("There was an error while hydrating but React was able to recover by instead client rendering from the nearest Suspense boundary.",{cause:a}),n))):(a!==Bm&&Kr(vn(Error("There was an error while hydrating but React was able to recover by instead client rendering the entire root.",{cause:a}),n)),e=e.current.alternate,e.flags|=65536,o&=-o,e.lanes|=o,a=vn(a,n),o=dp(e.stateNode,a,o),Oc(e,o),nt!==qi&&(nt=rr)),!1;var l=vn(Error("There was an error during concurrent rendering but React was able to recover by instead synchronously rendering the entire root.",{cause:a}),n);if(Ws===null?Ws=[l]:Ws.push(l),nt!==qi&&(nt=rr),t===null)return!0;a=vn(a,n),n=t;do{switch(n.tag){case 3:return n.flags|=65536,e=o&-o,n.lanes|=e,e=dp(n.stateNode,a,e),Oc(n,e),!1;case 1:if(t=n.type,l=n.stateNode,(n.flags&128)===0&&(typeof t.getDerivedStateFromError=="function"||l!==null&&typeof l.componentDidCatch=="function"&&(Jo===null||!Jo.has(l))))return n.flags|=65536,o&=-o,n.lanes|=o,o=hp(o),pp(o,e,n,a),Oc(n,o),!1}n=n.return}while(n!==null);return!1}function zt(e,t,n,a){t.child=e===null?$T(t,null,n,a):tr(t,e.child,n,a)}function S0(e,t,n,a,o){n=n.render;var l=t.ref;if("ref"in a){var c={};for(var d in a)d!=="ref"&&(c[d]=a[d])}else c=a;return xi(t),xt(t),a=Bh(e,t,n,c,l,o),d=Ph(),Nn(),e!==null&&!Tt?($h(e,t,o),Qa(e,t,o)):(xe&&d&&wh(t),t.flags|=1,zt(e,t,a,o),t.child)}function T0(e,t,n,a,o){if(e===null){var l=n.type;return typeof l=="function"&&!Sh(l)&&l.defaultProps===void 0&&n.compare===null?(n=Si(l),t.tag=15,t.type=n,vp(t,l),O0(e,t,n,a,o)):(e=Th(n.type,null,a,t,t.mode,o),e.ref=t.ref,e.return=t,t.child=e)}if(l=e.child,!Tp(e,o)){var c=l.memoizedProps;if(n=n.compare,n=n!==null?n:Ir,n(c,a)&&e.ref===t.ref)return Qa(e,t,o)}return t.flags|=1,e=Ga(l,a),e.ref=t.ref,e.return=t,t.child=e}function O0(e,t,n,a,o){if(e!==null){var l=e.memoizedProps;if(Ir(l,a)&&e.ref===t.ref&&t.type===e.type)if(Tt=!1,t.pendingProps=a=l,Tp(e,o))(e.flags&131072)!==0&&(Tt=!0);else return t.lanes=e.lanes,Qa(e,t,o)}return mp(e,t,n,a,o)}function E0(e,t,n){var a=t.pendingProps,o=a.children,l=e!==null?e.memoizedState:null;if(a.mode==="hidden"){if((t.flags&128)!==0){if(a=l!==null?l.baseLanes|n:n,e!==null){for(o=t.child=e.child,l=0;o!==null;)l=l|o.lanes|o.childLanes,o=o.sibling;t.childLanes=l&~a}else t.childLanes=0,t.child=null;return w0(e,t,a,n)}if((n&536870912)!==0)t.memoizedState={baseLanes:0,cachePool:null},e!==null&&Sc(t,l!==null?l.cachePool:null),l!==null?Bb(t,l):kh(t),p0(t);else return t.lanes=t.childLanes=536870912,w0(e,t,l!==null?l.baseLanes|n:n,n)}else l!==null?(Sc(t,l.cachePool),Bb(t,l),Uo(t),t.memoizedState=null):(e!==null&&Sc(t,null),kh(t),Uo(t));return zt(e,t,o,n),t.child}function w0(e,t,n,a){var o=jh();return o=o===null?null:{parent:vt._currentValue,pool:o},t.memoizedState={baseLanes:n,cachePool:o},e!==null&&Sc(t,null),kh(t),p0(t),e!==null&&Jr(e,t,a,!0),null}function Pc(e,t){var n=t.ref;if(n===null)e!==null&&e.ref!==null&&(t.flags|=4194816);else{if(typeof n!="function"&&typeof n!="object")throw Error("Expected ref to be a function, an object returned by React.createRef(), or undefined/null.");(e===null||e.ref!==n)&&(t.flags|=4194816)}}function mp(e,t,n,a,o){if(n.prototype&&typeof n.prototype.render=="function"){var l=ke(n)||"Unknown";t1[l]||(console.error("The <%s /> component appears to have a render method, but doesn't extend React.Component. This is likely to cause errors. Change %s to extend React.Component instead.",l,l),t1[l]=!0)}return t.mode&Bt&&fa.recordLegacyContextWarning(t,null),e===null&&(vp(t,t.type),n.contextTypes&&(l=ke(n)||"Unknown",a1[l]||(a1[l]=!0,console.error("%s uses the legacy contextTypes API which was removed in React 19. Use React.createContext() with React.useContext() instead. (https://react.dev/link/legacy-context)",l)))),xi(t),xt(t),n=Bh(e,t,n,a,void 0,o),a=Ph(),Nn(),e!==null&&!Tt?($h(e,t,o),Qa(e,t,o)):(xe&&a&&wh(t),t.flags|=1,zt(e,t,n,o),t.child)}function A0(e,t,n,a,o,l){return xi(t),xt(t),ro=-1,qs=e!==null&&e.type!==t.type,t.updateQueue=null,n=Vh(t,a,n,o),Vb(e,t),a=Ph(),Nn(),e!==null&&!Tt?($h(e,t,l),Qa(e,t,l)):(xe&&a&&wh(t),t.flags|=1,zt(e,t,n,l),t.child)}function x0(e,t,n,a,o){switch(p(t)){case!1:var l=t.stateNode,c=new t.type(t.memoizedProps,l.context).state;l.updater.enqueueSetState(l,c,null);break;case!0:t.flags|=128,t.flags|=65536,l=Error("Simulated error coming from DevTools");var d=o&-o;if(t.lanes|=d,c=qe,c===null)throw Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");d=hp(d),pp(d,c,t,vn(l,t)),Oc(t,d)}if(xi(t),t.stateNode===null){if(c=Go,l=n.contextType,"contextType"in n&&l!==null&&(l===void 0||l.$$typeof!==xa)&&!WT.has(n)&&(WT.add(n),d=l===void 0?" However, it is set to undefined. This can be caused by a typo or by mixing up named and default imports. This can also happen due to a circular dependency, so try moving the createContext() call to a separate file.":typeof l!="object"?" However, it is set to a "+typeof l+".":l.$$typeof===um?" Did you accidentally pass the Context.Consumer instead?":" However, it is set to an object with keys {"+Object.keys(l).join(", ")+"}.",console.error("%s defines an invalid contextType. contextType should point to the Context object returned by React.createContext().%s",ke(n)||"Component",d)),typeof l=="object"&&l!==null&&(c=Je(l)),l=new n(a,c),t.mode&Bt){Ce(!0);try{l=new n(a,c)}finally{Ce(!1)}}if(c=t.memoizedState=l.state!==null&&l.state!==void 0?l.state:null,l.updater=Fm,t.stateNode=l,l._reactInternals=t,l._reactInternalInstance=qT,typeof n.getDerivedStateFromProps=="function"&&c===null&&(c=ke(n)||"Component",YT.has(c)||(YT.add(c),console.error("`%s` uses `getDerivedStateFromProps` but its initial state is %s. This is not recommended. Instead, define the initial state by assigning an object to `this.state` in the constructor of `%s`. This ensures that `getDerivedStateFromProps` arguments have a consistent shape.",c,l.state===null?"null":"undefined",c))),typeof n.getDerivedStateFromProps=="function"||typeof l.getSnapshotBeforeUpdate=="function"){var v=d=c=null;if(typeof l.componentWillMount=="function"&&l.componentWillMount.__suppressDeprecationWarning!==!0?c="componentWillMount":typeof l.UNSAFE_componentWillMount=="function"&&(c="UNSAFE_componentWillMount"),typeof l.componentWillReceiveProps=="function"&&l.componentWillReceiveProps.__suppressDeprecationWarning!==!0?d="componentWillReceiveProps":typeof l.UNSAFE_componentWillReceiveProps=="function"&&(d="UNSAFE_componentWillReceiveProps"),typeof l.componentWillUpdate=="function"&&l.componentWillUpdate.__suppressDeprecationWarning!==!0?v="componentWillUpdate":typeof l.UNSAFE_componentWillUpdate=="function"&&(v="UNSAFE_componentWillUpdate"),c!==null||d!==null||v!==null){l=ke(n)||"Component";var y=typeof n.getDerivedStateFromProps=="function"?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";IT.has(l)||(IT.add(l),console.error(`Unsafe legacy lifecycles will not be called for components using new component APIs.

%s uses %s but also contains the following legacy lifecycles:%s%s%s

The above lifecycles should be removed. Learn more about this warning here:
https://react.dev/link/unsafe-component-lifecycles`,l,y,c!==null?`
  `+c:"",d!==null?`
  `+d:"",v!==null?`
  `+v:""))}}l=t.stateNode,c=ke(n)||"Component",l.render||(n.prototype&&typeof n.prototype.render=="function"?console.error("No `render` method found on the %s instance: did you accidentally return an object from the constructor?",c):console.error("No `render` method found on the %s instance: you may have forgotten to define `render`.",c)),!l.getInitialState||l.getInitialState.isReactClassApproved||l.state||console.error("getInitialState was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Did you mean to define a state property instead?",c),l.getDefaultProps&&!l.getDefaultProps.isReactClassApproved&&console.error("getDefaultProps was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Use a static property to define defaultProps instead.",c),l.contextType&&console.error("contextType was defined as an instance property on %s. Use a static property to define contextType instead.",c),n.childContextTypes&&!JT.has(n)&&(JT.add(n),console.error("%s uses the legacy childContextTypes API which was removed in React 19. Use React.createContext() instead. (https://react.dev/link/legacy-context)",c)),n.contextTypes&&!KT.has(n)&&(KT.add(n),console.error("%s uses the legacy contextTypes API which was removed in React 19. Use React.createContext() with static contextType instead. (https://react.dev/link/legacy-context)",c)),typeof l.componentShouldUpdate=="function"&&console.error("%s has a method called componentShouldUpdate(). Did you mean shouldComponentUpdate()? The name is phrased as a question because the function is expected to return a value.",c),n.prototype&&n.prototype.isPureReactComponent&&typeof l.shouldComponentUpdate<"u"&&console.error("%s has a method called shouldComponentUpdate(). shouldComponentUpdate should not be used when extending React.PureComponent. Please extend React.Component if shouldComponentUpdate is used.",ke(n)||"A pure component"),typeof l.componentDidUnmount=="function"&&console.error("%s has a method called componentDidUnmount(). But there is no such lifecycle method. Did you mean componentWillUnmount()?",c),typeof l.componentDidReceiveProps=="function"&&console.error("%s has a method called componentDidReceiveProps(). But there is no such lifecycle method. If you meant to update the state in response to changing props, use componentWillReceiveProps(). If you meant to fetch data or run side-effects or mutations after React has updated the UI, use componentDidUpdate().",c),typeof l.componentWillRecieveProps=="function"&&console.error("%s has a method called componentWillRecieveProps(). Did you mean componentWillReceiveProps()?",c),typeof l.UNSAFE_componentWillRecieveProps=="function"&&console.error("%s has a method called UNSAFE_componentWillRecieveProps(). Did you mean UNSAFE_componentWillReceiveProps()?",c),d=l.props!==a,l.props!==void 0&&d&&console.error("When calling super() in `%s`, make sure to pass up the same props that your component's constructor was passed.",c),l.defaultProps&&console.error("Setting defaultProps as an instance property on %s is not supported and will be ignored. Instead, define defaultProps as a static property on %s.",c,c),typeof l.getSnapshotBeforeUpdate!="function"||typeof l.componentDidUpdate=="function"||XT.has(n)||(XT.add(n),console.error("%s: getSnapshotBeforeUpdate() should be used with componentDidUpdate(). This component defines getSnapshotBeforeUpdate() only.",ke(n))),typeof l.getDerivedStateFromProps=="function"&&console.error("%s: getDerivedStateFromProps() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof l.getDerivedStateFromError=="function"&&console.error("%s: getDerivedStateFromError() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof n.getSnapshotBeforeUpdate=="function"&&console.error("%s: getSnapshotBeforeUpdate() is defined as a static method and will be ignored. Instead, declare it as an instance method.",c),(d=l.state)&&(typeof d!="object"||Ct(d))&&console.error("%s.state: must be set to an object or null",c),typeof l.getChildContext=="function"&&typeof n.childContextTypes!="object"&&console.error("%s.getChildContext(): childContextTypes must be defined in order to use getChildContext().",c),l=t.stateNode,l.props=a,l.state=t.memoizedState,l.refs={},Uh(t),c=n.contextType,l.context=typeof c=="object"&&c!==null?Je(c):Go,l.state===a&&(c=ke(n)||"Component",QT.has(c)||(QT.add(c),console.error("%s: It is not recommended to assign props directly to state because updates to props won't be reflected in state. In most cases, it is better to use props directly.",c))),t.mode&Bt&&fa.recordLegacyContextWarning(t,l),fa.recordUnsafeLifecycleWarnings(t,l),l.state=t.memoizedState,c=n.getDerivedStateFromProps,typeof c=="function"&&(fp(t,n,c,a),l.state=t.memoizedState),typeof n.getDerivedStateFromProps=="function"||typeof l.getSnapshotBeforeUpdate=="function"||typeof l.UNSAFE_componentWillMount!="function"&&typeof l.componentWillMount!="function"||(c=l.state,typeof l.componentWillMount=="function"&&l.componentWillMount(),typeof l.UNSAFE_componentWillMount=="function"&&l.UNSAFE_componentWillMount(),c!==l.state&&(console.error("%s.componentWillMount(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",ee(t)||"Component"),Fm.enqueueReplaceState(l,l.state,null)),ns(t,a,l,o),ts(),l.state=t.memoizedState),typeof l.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&ca)!==Xe&&(t.flags|=134217728),l=!0}else if(e===null){l=t.stateNode;var R=t.memoizedProps;d=Mi(n,R),l.props=d;var C=l.context;v=n.contextType,c=Go,typeof v=="object"&&v!==null&&(c=Je(v)),y=n.getDerivedStateFromProps,v=typeof y=="function"||typeof l.getSnapshotBeforeUpdate=="function",R=t.pendingProps!==R,v||typeof l.UNSAFE_componentWillReceiveProps!="function"&&typeof l.componentWillReceiveProps!="function"||(R||C!==c)&&v0(t,l,a,c),Yo=!1;var A=t.memoizedState;l.state=A,ns(t,a,l,o),ts(),C=t.memoizedState,R||A!==C||Yo?(typeof y=="function"&&(fp(t,n,y,a),C=t.memoizedState),(d=Yo||m0(t,n,d,a,A,C,c))?(v||typeof l.UNSAFE_componentWillMount!="function"&&typeof l.componentWillMount!="function"||(typeof l.componentWillMount=="function"&&l.componentWillMount(),typeof l.UNSAFE_componentWillMount=="function"&&l.UNSAFE_componentWillMount()),typeof l.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&ca)!==Xe&&(t.flags|=134217728)):(typeof l.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&ca)!==Xe&&(t.flags|=134217728),t.memoizedProps=a,t.memoizedState=C),l.props=a,l.state=C,l.context=c,l=d):(typeof l.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&ca)!==Xe&&(t.flags|=134217728),l=!1)}else{l=t.stateNode,Nh(e,t),c=t.memoizedProps,v=Mi(n,c),l.props=v,y=t.pendingProps,A=l.context,C=n.contextType,d=Go,typeof C=="object"&&C!==null&&(d=Je(C)),R=n.getDerivedStateFromProps,(C=typeof R=="function"||typeof l.getSnapshotBeforeUpdate=="function")||typeof l.UNSAFE_componentWillReceiveProps!="function"&&typeof l.componentWillReceiveProps!="function"||(c!==y||A!==d)&&v0(t,l,a,d),Yo=!1,A=t.memoizedState,l.state=A,ns(t,a,l,o),ts();var M=t.memoizedState;c!==y||A!==M||Yo||e!==null&&e.dependencies!==null&&yc(e.dependencies)?(typeof R=="function"&&(fp(t,n,R,a),M=t.memoizedState),(v=Yo||m0(t,n,v,a,A,M,d)||e!==null&&e.dependencies!==null&&yc(e.dependencies))?(C||typeof l.UNSAFE_componentWillUpdate!="function"&&typeof l.componentWillUpdate!="function"||(typeof l.componentWillUpdate=="function"&&l.componentWillUpdate(a,M,d),typeof l.UNSAFE_componentWillUpdate=="function"&&l.UNSAFE_componentWillUpdate(a,M,d)),typeof l.componentDidUpdate=="function"&&(t.flags|=4),typeof l.getSnapshotBeforeUpdate=="function"&&(t.flags|=1024)):(typeof l.componentDidUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=4),typeof l.getSnapshotBeforeUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=1024),t.memoizedProps=a,t.memoizedState=M),l.props=a,l.state=M,l.context=d,l=v):(typeof l.componentDidUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=4),typeof l.getSnapshotBeforeUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=1024),l=!1)}if(d=l,Pc(e,t),c=(t.flags&128)!==0,d||c){if(d=t.stateNode,rh(t),c&&typeof n.getDerivedStateFromError!="function")n=null,tn=-1;else{if(xt(t),n=RT(d),t.mode&Bt){Ce(!0);try{RT(d)}finally{Ce(!1)}}Nn()}t.flags|=1,e!==null&&c?(t.child=tr(t,e.child,null,o),t.child=tr(t,null,n,o)):zt(e,t,n,o),t.memoizedState=d.state,e=t.child}else e=Qa(e,t,o);return o=t.stateNode,l&&o.props!==a&&(or||console.error("It looks like %s is reassigning its own `this.props` while rendering. This is not supported and can lead to confusing bugs.",ee(t)||"a component"),or=!0),e}function R0(e,t,n,a){return Zr(),t.flags|=256,zt(e,t,n,a),t.child}function vp(e,t){t&&t.childContextTypes&&console.error(`childContextTypes cannot be defined on a function component.
  %s.childContextTypes = ...`,t.displayName||t.name||"Component"),typeof t.getDerivedStateFromProps=="function"&&(e=ke(t)||"Unknown",o1[e]||(console.error("%s: Function components do not support getDerivedStateFromProps.",e),o1[e]=!0)),typeof t.contextType=="object"&&t.contextType!==null&&(t=ke(t)||"Unknown",n1[t]||(console.error("%s: Function components do not support contextType.",t),n1[t]=!0))}function yp(e){return{baseLanes:e,cachePool:Cb()}}function gp(e,t,n){return e=e!==null?e.childLanes&~n:0,t&&(e|=En),e}function z0(e,t,n){var a,o=t.pendingProps;h(t)&&(t.flags|=128);var l=!1,c=(t.flags&128)!==0;if((a=c)||(a=e!==null&&e.memoizedState===null?!1:(gt.current&Ys)!==0),a&&(l=!0,t.flags&=-129),a=(t.flags&32)!==0,t.flags&=-33,e===null){if(xe){if(l?jo(t):Uo(t),xe){var d=tt,v;if(!(v=!d)){e:{var y=d;for(v=ja;y.nodeType!==8;){if(!v){v=null;break e}if(y=Vn(y.nextSibling),y===null){v=null;break e}}v=y}v!==null?(Ei(),t.memoizedState={dehydrated:v,treeContext:ki!==null?{id:ao,overflow:oo}:null,retryLane:536870912,hydrationErrors:null},y=O(18,null,null,Xe),y.stateNode=v,y.return=t,t.child=y,Gt=t,tt=null,v=!0):v=!1,v=!v}v&&(xh(t,d),Ai(t))}if(d=t.memoizedState,d!==null&&(d=d.dehydrated,d!==null))return Jp(d)?t.lanes=32:t.lanes=536870912,null;Ia(t)}return d=o.children,o=o.fallback,l?(Uo(t),l=t.mode,d=$c({mode:"hidden",children:d},l),o=Ti(o,l,n,null),d.return=t,o.return=t,d.sibling=o,t.child=d,l=t.child,l.memoizedState=yp(n),l.childLanes=gp(e,a,n),t.memoizedState=av,o):(jo(t),bp(t,d))}var R=e.memoizedState;if(R!==null&&(d=R.dehydrated,d!==null)){if(c)t.flags&256?(jo(t),t.flags&=-257,t=_p(e,t,n)):t.memoizedState!==null?(Uo(t),t.child=e.child,t.flags|=128,t=null):(Uo(t),l=o.fallback,d=t.mode,o=$c({mode:"visible",children:o.children},d),l=Ti(l,d,n,null),l.flags|=2,o.return=t,l.return=t,o.sibling=l,t.child=o,tr(t,e.child,null,n),o=t.child,o.memoizedState=yp(n),o.childLanes=gp(e,a,n),t.memoizedState=av,t=l);else if(jo(t),xe&&console.error("We should not be hydrating here. This is a bug in React. Please file a bug."),Jp(d)){if(a=d.nextSibling&&d.nextSibling.dataset,a){v=a.dgst;var C=a.msg;y=a.stck;var A=a.cstck}d=C,a=v,o=y,v=l=A,l=Error(d||"The server could not finish this Suspense boundary, likely due to an error during server rendering. Switched to client rendering."),l.stack=o||"",l.digest=a,a=v===void 0?null:v,o={value:l,source:null,stack:a},typeof a=="string"&&Hm.set(l,o),Kr(o),t=_p(e,t,n)}else if(Tt||Jr(e,t,n,!1),a=(n&e.childLanes)!==0,Tt||a){if(a=qe,a!==null&&(o=n&-n,o=(o&42)!==0?1:Hr(o),o=(o&(a.suspendedLanes|n))!==0?0:o,o!==0&&o!==R.retryLane))throw R.retryLane=o,Jt(e,o),it(a,e,o),e1;d.data===co||jp(),t=_p(e,t,n)}else d.data===co?(t.flags|=192,t.child=e.child,t=null):(e=R.treeContext,tt=Vn(d.nextSibling),Gt=t,xe=!0,Hi=null,io=!1,Xn=null,ja=!1,e!==null&&(Ei(),Gn[Yn++]=ao,Gn[Yn++]=oo,Gn[Yn++]=ki,ao=e.id,oo=e.overflow,ki=t),t=bp(t,o.children),t.flags|=4096);return t}return l?(Uo(t),l=o.fallback,d=t.mode,v=e.child,y=v.sibling,o=Ga(v,{mode:"hidden",children:o.children}),o.subtreeFlags=v.subtreeFlags&65011712,y!==null?l=Ga(y,l):(l=Ti(l,d,n,null),l.flags|=2),l.return=t,o.return=t,o.sibling=l,t.child=o,o=l,l=t.child,d=e.child.memoizedState,d===null?d=yp(n):(v=d.cachePool,v!==null?(y=vt._currentValue,v=v.parent!==y?{parent:y,pool:y}:v):v=Cb(),d={baseLanes:d.baseLanes|n,cachePool:v}),l.memoizedState=d,l.childLanes=gp(e,a,n),t.memoizedState=av,o):(jo(t),n=e.child,e=n.sibling,n=Ga(n,{mode:"visible",children:o.children}),n.return=t,n.sibling=null,e!==null&&(a=t.deletions,a===null?(t.deletions=[e],t.flags|=16):a.push(e)),t.child=n,t.memoizedState=null,n)}function bp(e,t){return t=$c({mode:"visible",children:t},e.mode),t.return=e,e.child=t}function $c(e,t){return e=O(22,e,null,t),e.lanes=0,e.stateNode={_visibility:yf,_pendingMarkers:null,_retryCache:null,_transitions:null},e}function _p(e,t,n){return tr(t,e.child,null,n),e=bp(t,t.pendingProps.children),e.flags|=2,t.memoizedState=null,e}function D0(e,t,n){e.lanes|=t;var a=e.alternate;a!==null&&(a.lanes|=t),Rh(e.return,t,n)}function C0(e,t){var n=Ct(e);return e=!n&&typeof Fe(e)=="function",n||e?(n=n?"array":"iterable",console.error("A nested %s was passed to row #%s in <SuspenseList />. Wrap it in an additional SuspenseList to configure its revealOrder: <SuspenseList revealOrder=...> ... <SuspenseList revealOrder=...>{%s}</SuspenseList> ... </SuspenseList>",n,t,n),!1):!0}function Sp(e,t,n,a,o){var l=e.memoizedState;l===null?e.memoizedState={isBackwards:t,rendering:null,renderingStartTime:0,last:a,tail:n,tailMode:o}:(l.isBackwards=t,l.rendering=null,l.renderingStartTime=0,l.last=a,l.tail=n,l.tailMode=o)}function M0(e,t,n){var a=t.pendingProps,o=a.revealOrder,l=a.tail;if(a=a.children,o!==void 0&&o!=="forwards"&&o!=="backwards"&&o!=="together"&&!i1[o])if(i1[o]=!0,typeof o=="string")switch(o.toLowerCase()){case"together":case"forwards":case"backwards":console.error('"%s" is not a valid value for revealOrder on <SuspenseList />. Use lowercase "%s" instead.',o,o.toLowerCase());break;case"forward":case"backward":console.error('"%s" is not a valid value for revealOrder on <SuspenseList />. React uses the -s suffix in the spelling. Use "%ss" instead.',o,o.toLowerCase());break;default:console.error('"%s" is not a supported revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',o)}else console.error('%s is not a supported value for revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',o);l===void 0||nv[l]||(l!=="collapsed"&&l!=="hidden"?(nv[l]=!0,console.error('"%s" is not a supported value for tail on <SuspenseList />. Did you mean "collapsed" or "hidden"?',l)):o!=="forwards"&&o!=="backwards"&&(nv[l]=!0,console.error('<SuspenseList tail="%s" /> is only valid if revealOrder is "forwards" or "backwards". Did you mean to specify revealOrder="forwards"?',l)));e:if((o==="forwards"||o==="backwards")&&a!==void 0&&a!==null&&a!==!1)if(Ct(a)){for(var c=0;c<a.length;c++)if(!C0(a[c],c))break e}else if(c=Fe(a),typeof c=="function"){if(c=c.call(a))for(var d=c.next(),v=0;!d.done;d=c.next()){if(!C0(d.value,v))break e;v++}}else console.error('A single row was passed to a <SuspenseList revealOrder="%s" />. This is not useful since it needs multiple rows. Did you mean to pass multiple children or an array?',o);if(zt(e,t,a,n),a=gt.current,(a&Ys)!==0)a=a&nr|Ys,t.flags|=128;else{if(e!==null&&(e.flags&128)!==0)e:for(e=t.child;e!==null;){if(e.tag===13)e.memoizedState!==null&&D0(e,n,t);else if(e.tag===19)D0(e,n,t);else if(e.child!==null){e.child.return=e,e=e.child;continue}if(e===t)break e;for(;e.sibling===null;){if(e.return===null||e.return===t)break e;e=e.return}e.sibling.return=e.return,e=e.sibling}a&=nr}switch(Re(gt,a,t),o){case"forwards":for(n=t.child,o=null;n!==null;)e=n.alternate,e!==null&&Bc(e)===null&&(o=n),n=n.sibling;n=o,n===null?(o=t.child,t.child=null):(o=n.sibling,n.sibling=null),Sp(t,!1,o,n,l);break;case"backwards":for(n=null,o=t.child,t.child=null;o!==null;){if(e=o.alternate,e!==null&&Bc(e)===null){t.child=o;break}e=o.sibling,o.sibling=n,n=o,o=e}Sp(t,!0,n,null,l);break;case"together":Sp(t,!1,null,null,void 0);break;default:t.memoizedState=null}return t.child}function Qa(e,t,n){if(e!==null&&(t.dependencies=e.dependencies),tn=-1,Zo|=t.lanes,(n&t.childLanes)===0)if(e!==null){if(Jr(e,t,n,!1),(n&t.childLanes)===0)return null}else return null;if(e!==null&&t.child!==e.child)throw Error("Resuming work not yet implemented.");if(t.child!==null){for(e=t.child,n=Ga(e,e.pendingProps),t.child=n,n.return=t;e.sibling!==null;)e=e.sibling,n=n.sibling=Ga(e,e.pendingProps),n.return=t;n.sibling=null}return t.child}function Tp(e,t){return(e.lanes&t)!==0?!0:(e=e.dependencies,!!(e!==null&&yc(e)))}function wR(e,t,n){switch(t.tag){case 3:At(t,t.stateNode.containerInfo),zo(t,vt,e.memoizedState.cache),Zr();break;case 27:case 5:se(t);break;case 4:At(t,t.stateNode.containerInfo);break;case 10:zo(t,t.type,t.memoizedProps.value);break;case 12:(n&t.childLanes)!==0&&(t.flags|=4),t.flags|=2048;var a=t.stateNode;a.effectDuration=-0,a.passiveEffectDuration=-0;break;case 13:if(a=t.memoizedState,a!==null)return a.dehydrated!==null?(jo(t),t.flags|=128,null):(n&t.child.childLanes)!==0?z0(e,t,n):(jo(t),e=Qa(e,t,n),e!==null?e.sibling:null);jo(t);break;case 19:var o=(e.flags&128)!==0;if(a=(n&t.childLanes)!==0,a||(Jr(e,t,n,!1),a=(n&t.childLanes)!==0),o){if(a)return M0(e,t,n);t.flags|=128}if(o=t.memoizedState,o!==null&&(o.rendering=null,o.tail=null,o.lastEffect=null),Re(gt,gt.current,t),a)break;return null;case 22:case 23:return t.lanes=0,E0(e,t,n);case 24:zo(t,vt,e.memoizedState.cache)}return Qa(e,t,n)}function Op(e,t,n){if(t._debugNeedsRemount&&e!==null){n=Th(t.type,t.key,t.pendingProps,t._debugOwner||null,t.mode,t.lanes),n._debugStack=t._debugStack,n._debugTask=t._debugTask;var a=t.return;if(a===null)throw Error("Cannot swap the root fiber.");if(e.alternate=null,t.alternate=null,n.index=t.index,n.sibling=t.sibling,n.return=t.return,n.ref=t.ref,n._debugInfo=t._debugInfo,t===a.child)a.child=n;else{var o=a.child;if(o===null)throw Error("Expected parent to have a child.");for(;o.sibling!==t;)if(o=o.sibling,o===null)throw Error("Expected to find the previous sibling.");o.sibling=n}return t=a.deletions,t===null?(a.deletions=[e],a.flags|=16):t.push(e),n.flags|=2,n}if(e!==null)if(e.memoizedProps!==t.pendingProps||t.type!==e.type)Tt=!0;else{if(!Tp(e,n)&&(t.flags&128)===0)return Tt=!1,wR(e,t,n);Tt=(e.flags&131072)!==0}else Tt=!1,(a=xe)&&(Ei(),a=(t.flags&1048576)!==0),a&&(a=t.index,Ei(),Ob(t,bf,a));switch(t.lanes=0,t.tag){case 16:e:if(a=t.pendingProps,e=Io(t.elementType),t.type=e,typeof e=="function")Sh(e)?(a=Mi(e,a),t.tag=1,t.type=e=Si(e),t=x0(null,t,e,a,n)):(t.tag=0,vp(t,e),t.type=e=Si(e),t=mp(null,t,e,a,n));else{if(e!=null){if(o=e.$$typeof,o===Ss){t.tag=11,t.type=e=bh(e),t=S0(null,t,e,a,n);break e}else if(o===lf){t.tag=14,t=T0(null,t,e,a,n);break e}}throw t="",e!==null&&typeof e=="object"&&e.$$typeof===_n&&(t=" Did you wrap a component in React.lazy() more than once?"),e=ke(e)||e,Error("Element type is invalid. Received a promise that resolves to: "+e+". Lazy element type must resolve to a class or function."+t)}return t;case 0:return mp(e,t,t.type,t.pendingProps,n);case 1:return a=t.type,o=Mi(a,t.pendingProps),x0(e,t,a,o,n);case 3:e:{if(At(t,t.stateNode.containerInfo),e===null)throw Error("Should have a current fiber. This is a bug in React.");a=t.pendingProps;var l=t.memoizedState;o=l.element,Nh(e,t),ns(t,a,null,n);var c=t.memoizedState;if(a=c.cache,zo(t,vt,a),a!==l.cache&&zh(t,[vt],n,!0),ts(),a=c.element,l.isDehydrated)if(l={element:a,isDehydrated:!1,cache:c.cache},t.updateQueue.baseState=l,t.memoizedState=l,t.flags&256){t=R0(e,t,a,n);break e}else if(a!==o){o=vn(Error("This root received an early update, before anything was able hydrate. Switched the entire root to client rendering."),t),Kr(o),t=R0(e,t,a,n);break e}else{switch(e=t.stateNode.containerInfo,e.nodeType){case 9:e=e.body;break;default:e=e.nodeName==="HTML"?e.ownerDocument.body:e}for(tt=Vn(e.firstChild),Gt=t,xe=!0,Hi=null,io=!1,Xn=null,ja=!0,e=$T(t,null,a,n),t.child=e;e;)e.flags=e.flags&-3|4096,e=e.sibling}else{if(Zr(),a===o){t=Qa(e,t,n);break e}zt(e,t,a,n)}t=t.child}return t;case 26:return Pc(e,t),e===null?(e=J_(t.type,null,t.pendingProps,null))?t.memoizedState=e:xe||(e=t.type,n=t.pendingProps,a=st(Vo.current),a=Kc(a).createElement(e),a[Lt]=t,a[en]=n,Dt(a,e,n),w(a),t.stateNode=a):t.memoizedState=J_(t.type,e.memoizedProps,t.pendingProps,e.memoizedState),null;case 27:return se(t),e===null&&xe&&(a=st(Vo.current),o=q(),a=t.stateNode=Z_(t.type,t.pendingProps,a,o,!1),io||(o=V_(a,t.type,t.pendingProps,o),o!==null&&(wi(t,0).serverProps=o)),Gt=t,ja=!0,o=tt,Ho(t.type)?(Av=o,tt=Vn(a.firstChild)):tt=o),zt(e,t,t.pendingProps.children,n),Pc(e,t),e===null&&(t.flags|=4194304),t.child;case 5:return e===null&&xe&&(l=q(),a=ph(t.type,l.ancestorInfo),o=tt,(c=!o)||(c=sz(o,t.type,t.pendingProps,ja),c!==null?(t.stateNode=c,io||(l=V_(c,t.type,t.pendingProps,l),l!==null&&(wi(t,0).serverProps=l)),Gt=t,tt=Vn(c.firstChild),ja=!1,l=!0):l=!1,c=!l),c&&(a&&xh(t,o),Ai(t))),se(t),o=t.type,l=t.pendingProps,c=e!==null?e.memoizedProps:null,a=l.children,Zp(o,l)?a=null:c!==null&&Zp(o,c)&&(t.flags|=32),t.memoizedState!==null&&(o=Bh(e,t,bR,null,null,n),lu._currentValue=o),Pc(e,t),zt(e,t,a,n),t.child;case 6:return e===null&&xe&&(e=t.pendingProps,n=q(),a=n.ancestorInfo.current,e=a!=null?sc(e,a.tag,n.ancestorInfo.implicitRootScope):!0,n=tt,(a=!n)||(a=uz(n,t.pendingProps,ja),a!==null?(t.stateNode=a,Gt=t,tt=null,a=!0):a=!1,a=!a),a&&(e&&xh(t,n),Ai(t))),null;case 13:return z0(e,t,n);case 4:return At(t,t.stateNode.containerInfo),a=t.pendingProps,e===null?t.child=tr(t,null,a,n):zt(e,t,a,n),t.child;case 11:return S0(e,t,t.type,t.pendingProps,n);case 7:return zt(e,t,t.pendingProps,n),t.child;case 8:return zt(e,t,t.pendingProps.children,n),t.child;case 12:return t.flags|=4,t.flags|=2048,a=t.stateNode,a.effectDuration=-0,a.passiveEffectDuration=-0,zt(e,t,t.pendingProps.children,n),t.child;case 10:return a=t.type,o=t.pendingProps,l=o.value,"value"in o||l1||(l1=!0,console.error("The `value` prop is required for the `<Context.Provider>`. Did you misspell it or forget to pass it?")),zo(t,a,l),zt(e,t,o.children,n),t.child;case 9:return o=t.type._context,a=t.pendingProps.children,typeof a!="function"&&console.error("A context consumer was rendered with multiple children, or a child that isn't a function. A context consumer expects a single child that is a function. If you did pass a function, make sure there is no trailing or leading whitespace around it."),xi(t),o=Je(o),xt(t),a=Km(a,o,void 0),Nn(),t.flags|=1,zt(e,t,a,n),t.child;case 14:return T0(e,t,t.type,t.pendingProps,n);case 15:return O0(e,t,t.type,t.pendingProps,n);case 19:return M0(e,t,n);case 31:return a=t.pendingProps,n=t.mode,a={mode:a.mode,children:a.children},e===null?(e=$c(a,n),e.ref=t.ref,t.child=e,e.return=t,t=e):(e=Ga(e.child,a),e.ref=t.ref,t.child=e,e.return=t,t=e),t;case 22:return E0(e,t,n);case 24:return xi(t),a=Je(vt),e===null?(o=jh(),o===null&&(o=qe,l=Dh(),o.pooledCache=l,Ri(l),l!==null&&(o.pooledCacheLanes|=n),o=l),t.memoizedState={parent:a,cache:o},Uh(t),zo(t,vt,o)):((e.lanes&n)!==0&&(Nh(e,t),ns(t,null,null,n),ts()),o=e.memoizedState,l=t.memoizedState,o.parent!==a?(o={parent:a,cache:a},t.memoizedState=o,t.lanes===0&&(t.memoizedState=t.updateQueue.baseState=o),zo(t,vt,a)):(a=l.cache,zo(t,vt,a),a!==o.cache&&zh(t,[vt],n,!0))),zt(e,t,t.pendingProps.children,n),t.child;case 29:throw t.pendingProps}throw Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function Za(e){e.flags|=4}function j0(e,t){if(t.type!=="stylesheet"||(t.state.loading&Jn)!==Wi)e.flags&=-16777217;else if(e.flags|=16777216,!nS(t)){if(t=Kn.current,t!==null&&((Oe&4194048)===Oe?Na!==null:(Oe&62914560)!==Oe&&(Oe&536870912)===0||t!==Na))throw Ps=qm,vT;e.flags|=8192}}function qc(e,t){t!==null&&(e.flags|=4),e.flags&16384&&(t=e.tag!==22?yi():536870912,e.lanes|=t,Xi|=t)}function rs(e,t){if(!xe)switch(e.tailMode){case"hidden":t=e.tail;for(var n=null;t!==null;)t.alternate!==null&&(n=t),t=t.sibling;n===null?e.tail=null:n.sibling=null;break;case"collapsed":n=e.tail;for(var a=null;n!==null;)n.alternate!==null&&(a=n),n=n.sibling;a===null?t||e.tail===null?e.tail=null:e.tail.sibling=null:a.sibling=null}}function We(e){var t=e.alternate!==null&&e.alternate.child===e.child,n=0,a=0;if(t)if((e.mode&Mt)!==Xe){for(var o=e.selfBaseDuration,l=e.child;l!==null;)n|=l.lanes|l.childLanes,a|=l.subtreeFlags&65011712,a|=l.flags&65011712,o+=l.treeBaseDuration,l=l.sibling;e.treeBaseDuration=o}else for(o=e.child;o!==null;)n|=o.lanes|o.childLanes,a|=o.subtreeFlags&65011712,a|=o.flags&65011712,o.return=e,o=o.sibling;else if((e.mode&Mt)!==Xe){o=e.actualDuration,l=e.selfBaseDuration;for(var c=e.child;c!==null;)n|=c.lanes|c.childLanes,a|=c.subtreeFlags,a|=c.flags,o+=c.actualDuration,l+=c.treeBaseDuration,c=c.sibling;e.actualDuration=o,e.treeBaseDuration=l}else for(o=e.child;o!==null;)n|=o.lanes|o.childLanes,a|=o.subtreeFlags,a|=o.flags,o.return=e,o=o.sibling;return e.subtreeFlags|=a,e.childLanes=n,t}function AR(e,t,n){var a=t.pendingProps;switch(Ah(t),t.tag){case 31:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return We(t),null;case 1:return We(t),null;case 3:return n=t.stateNode,a=null,e!==null&&(a=e.memoizedState.cache),t.memoizedState.cache!==a&&(t.flags|=2048),Ya(vt,t),kt(t),n.pendingContext&&(n.context=n.pendingContext,n.pendingContext=null),(e===null||e.child===null)&&(Qr(t)?(xb(),Za(t)):e===null||e.memoizedState.isDehydrated&&(t.flags&256)===0||(t.flags|=1024,Ab())),We(t),null;case 26:return n=t.memoizedState,e===null?(Za(t),n!==null?(We(t),j0(t,n)):(We(t),t.flags&=-16777217)):n?n!==e.memoizedState?(Za(t),We(t),j0(t,n)):(We(t),t.flags&=-16777217):(e.memoizedProps!==a&&Za(t),We(t),t.flags&=-16777217),null;case 27:oe(t),n=st(Vo.current);var o=t.type;if(e!==null&&t.stateNode!=null)e.memoizedProps!==a&&Za(t);else{if(!a){if(t.stateNode===null)throw Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return We(t),null}e=q(),Qr(t)?Eb(t):(e=Z_(o,a,n,e,!0),t.stateNode=e,Za(t))}return We(t),null;case 5:if(oe(t),n=t.type,e!==null&&t.stateNode!=null)e.memoizedProps!==a&&Za(t);else{if(!a){if(t.stateNode===null)throw Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return We(t),null}if(o=q(),Qr(t))Eb(t);else{switch(e=st(Vo.current),ph(n,o.ancestorInfo),o=o.context,e=Kc(e),o){case hr:e=e.createElementNS(kl,n);break;case Xf:e=e.createElementNS(df,n);break;default:switch(n){case"svg":e=e.createElementNS(kl,n);break;case"math":e=e.createElementNS(df,n);break;case"script":e=e.createElement("div"),e.innerHTML="<script><\/script>",e=e.removeChild(e.firstChild);break;case"select":e=typeof a.is=="string"?e.createElement("select",{is:a.is}):e.createElement("select"),a.multiple?e.multiple=!0:a.size&&(e.size=a.size);break;default:e=typeof a.is=="string"?e.createElement(n,{is:a.is}):e.createElement(n),n.indexOf("-")===-1&&(n!==n.toLowerCase()&&console.error("<%s /> is using incorrect casing. Use PascalCase for React components, or lowercase for HTML elements.",n),Object.prototype.toString.call(e)!=="[object HTMLUnknownElement]"||eo.call(w1,n)||(w1[n]=!0,console.error("The tag <%s> is unrecognized in this browser. If you meant to render a React component, start its name with an uppercase letter.",n)))}}e[Lt]=t,e[en]=a;e:for(o=t.child;o!==null;){if(o.tag===5||o.tag===6)e.appendChild(o.stateNode);else if(o.tag!==4&&o.tag!==27&&o.child!==null){o.child.return=o,o=o.child;continue}if(o===t)break e;for(;o.sibling===null;){if(o.return===null||o.return===t)break e;o=o.return}o.sibling.return=o.return,o=o.sibling}t.stateNode=e;e:switch(Dt(e,n,a),n){case"button":case"input":case"select":case"textarea":e=!!a.autoFocus;break e;case"img":e=!0;break e;default:e=!1}e&&Za(t)}}return We(t),t.flags&=-16777217,null;case 6:if(e&&t.stateNode!=null)e.memoizedProps!==a&&Za(t);else{if(typeof a!="string"&&t.stateNode===null)throw Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");if(e=st(Vo.current),n=q(),Qr(t)){e=t.stateNode,n=t.memoizedProps,o=!io,a=null;var l=Gt;if(l!==null)switch(l.tag){case 3:o&&(o=X_(e,n,a),o!==null&&(wi(t,0).serverProps=o));break;case 27:case 5:a=l.memoizedProps,o&&(o=X_(e,n,a),o!==null&&(wi(t,0).serverProps=o))}e[Lt]=t,e=!!(e.nodeValue===n||a!==null&&a.suppressHydrationWarning===!0||N_(e.nodeValue,n)),e||Ai(t)}else o=n.ancestorInfo.current,o!=null&&sc(a,o.tag,n.ancestorInfo.implicitRootScope),e=Kc(e).createTextNode(a),e[Lt]=t,t.stateNode=e}return We(t),null;case 13:if(a=t.memoizedState,e===null||e.memoizedState!==null&&e.memoizedState.dehydrated!==null){if(o=Qr(t),a!==null&&a.dehydrated!==null){if(e===null){if(!o)throw Error("A dehydrated suspense component was completed without a hydrated node. This is probably a bug in React.");if(o=t.memoizedState,o=o!==null?o.dehydrated:null,!o)throw Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");o[Lt]=t,We(t),(t.mode&Mt)!==Xe&&a!==null&&(o=t.child,o!==null&&(t.treeBaseDuration-=o.treeBaseDuration))}else xb(),Zr(),(t.flags&128)===0&&(t.memoizedState=null),t.flags|=4,We(t),(t.mode&Mt)!==Xe&&a!==null&&(o=t.child,o!==null&&(t.treeBaseDuration-=o.treeBaseDuration));o=!1}else o=Ab(),e!==null&&e.memoizedState!==null&&(e.memoizedState.hydrationErrors=o),o=!0;if(!o)return t.flags&256?(Ia(t),t):(Ia(t),null)}return Ia(t),(t.flags&128)!==0?(t.lanes=n,(t.mode&Mt)!==Xe&&_c(t),t):(n=a!==null,e=e!==null&&e.memoizedState!==null,n&&(a=t.child,o=null,a.alternate!==null&&a.alternate.memoizedState!==null&&a.alternate.memoizedState.cachePool!==null&&(o=a.alternate.memoizedState.cachePool.pool),l=null,a.memoizedState!==null&&a.memoizedState.cachePool!==null&&(l=a.memoizedState.cachePool.pool),l!==o&&(a.flags|=2048)),n!==e&&n&&(t.child.flags|=8192),qc(t,t.updateQueue),We(t),(t.mode&Mt)!==Xe&&n&&(e=t.child,e!==null&&(t.treeBaseDuration-=e.treeBaseDuration)),null);case 4:return kt(t),e===null&&$p(t.stateNode.containerInfo),We(t),null;case 10:return Ya(t.type,t),We(t),null;case 19:if(ue(gt,t),o=t.memoizedState,o===null)return We(t),null;if(a=(t.flags&128)!==0,l=o.rendering,l===null)if(a)rs(o,!1);else{if(nt!==uo||e!==null&&(e.flags&128)!==0)for(e=t.child;e!==null;){if(l=Bc(e),l!==null){for(t.flags|=128,rs(o,!1),e=l.updateQueue,t.updateQueue=e,qc(t,e),t.subtreeFlags=0,e=n,n=t.child;n!==null;)Tb(n,e),n=n.sibling;return Re(gt,gt.current&nr|Ys,t),t.child}e=e.sibling}o.tail!==null&&Ra()>Uf&&(t.flags|=128,a=!0,rs(o,!1),t.lanes=4194304)}else{if(!a)if(e=Bc(l),e!==null){if(t.flags|=128,a=!0,e=e.updateQueue,t.updateQueue=e,qc(t,e),rs(o,!0),o.tail===null&&o.tailMode==="hidden"&&!l.alternate&&!xe)return We(t),null}else 2*Ra()-o.renderingStartTime>Uf&&n!==536870912&&(t.flags|=128,a=!0,rs(o,!1),t.lanes=4194304);o.isBackwards?(l.sibling=t.child,t.child=l):(e=o.last,e!==null?e.sibling=l:t.child=l,o.last=l)}return o.tail!==null?(e=o.tail,o.rendering=e,o.tail=e.sibling,o.renderingStartTime=Ra(),e.sibling=null,n=gt.current,n=a?n&nr|Ys:n&nr,Re(gt,n,t),e):(We(t),null);case 22:case 23:return Ia(t),Hh(t),a=t.memoizedState!==null,e!==null?e.memoizedState!==null!==a&&(t.flags|=8192):a&&(t.flags|=8192),a?(n&536870912)!==0&&(t.flags&128)===0&&(We(t),t.subtreeFlags&6&&(t.flags|=8192)):We(t),n=t.updateQueue,n!==null&&qc(t,n.retryQueue),n=null,e!==null&&e.memoizedState!==null&&e.memoizedState.cachePool!==null&&(n=e.memoizedState.cachePool.pool),a=null,t.memoizedState!==null&&t.memoizedState.cachePool!==null&&(a=t.memoizedState.cachePool.pool),a!==n&&(t.flags|=2048),e!==null&&ue(Vi,t),null;case 24:return n=null,e!==null&&(n=e.memoizedState.cache),t.memoizedState.cache!==n&&(t.flags|=2048),Ya(vt,t),We(t),null;case 25:return null;case 30:return null}throw Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function xR(e,t){switch(Ah(t),t.tag){case 1:return e=t.flags,e&65536?(t.flags=e&-65537|128,(t.mode&Mt)!==Xe&&_c(t),t):null;case 3:return Ya(vt,t),kt(t),e=t.flags,(e&65536)!==0&&(e&128)===0?(t.flags=e&-65537|128,t):null;case 26:case 27:case 5:return oe(t),null;case 13:if(Ia(t),e=t.memoizedState,e!==null&&e.dehydrated!==null){if(t.alternate===null)throw Error("Threw in newly mounted dehydrated component. This is likely a bug in React. Please file an issue.");Zr()}return e=t.flags,e&65536?(t.flags=e&-65537|128,(t.mode&Mt)!==Xe&&_c(t),t):null;case 19:return ue(gt,t),null;case 4:return kt(t),null;case 10:return Ya(t.type,t),null;case 22:case 23:return Ia(t),Hh(t),e!==null&&ue(Vi,t),e=t.flags,e&65536?(t.flags=e&-65537|128,(t.mode&Mt)!==Xe&&_c(t),t):null;case 24:return Ya(vt,t),null;case 25:return null;default:return null}}function U0(e,t){switch(Ah(t),t.tag){case 3:Ya(vt,t),kt(t);break;case 26:case 27:case 5:oe(t);break;case 4:kt(t);break;case 13:Ia(t);break;case 19:ue(gt,t);break;case 10:Ya(t.type,t);break;case 22:case 23:Ia(t),Hh(t),e!==null&&ue(Vi,t);break;case 24:Ya(vt,t)}}function Ta(e){return(e.mode&Mt)!==Xe}function N0(e,t){Ta(e)?(Sa(),ss(t,e),_a()):ss(t,e)}function Ep(e,t,n){Ta(e)?(Sa(),Tl(n,e,t),_a()):Tl(n,e,t)}function ss(e,t){try{var n=t.updateQueue,a=n!==null?n.lastEffect:null;if(a!==null){var o=a.next;n=o;do{if((n.tag&e)===e&&((e&yt)!==In?Y!==null&&typeof Y.markComponentPassiveEffectMountStarted=="function"&&Y.markComponentPassiveEffectMountStarted(t):(e&jt)!==In&&Y!==null&&typeof Y.markComponentLayoutEffectMountStarted=="function"&&Y.markComponentLayoutEffectMountStarted(t),a=void 0,(e&Yt)!==In&&(fr=!0),a=W(t,XD,n),(e&Yt)!==In&&(fr=!1),(e&yt)!==In?Y!==null&&typeof Y.markComponentPassiveEffectMountStopped=="function"&&Y.markComponentPassiveEffectMountStopped():(e&jt)!==In&&Y!==null&&typeof Y.markComponentLayoutEffectMountStopped=="function"&&Y.markComponentLayoutEffectMountStopped(),a!==void 0&&typeof a!="function")){var l=void 0;l=(n.tag&jt)!==0?"useLayoutEffect":(n.tag&Yt)!==0?"useInsertionEffect":"useEffect";var c=void 0;c=a===null?" You returned null. If your effect does not require clean up, return undefined (or nothing).":typeof a.then=="function"?`

It looks like you wrote `+l+`(async () => ...) or returned a Promise. Instead, write the async function inside your effect and call it immediately:

`+l+`(() => {
  async function fetchData() {
    // You can await here
    const response = await MyAPI.getData(someId);
    // ...
  }
  fetchData();
}, [someId]); // Or [] if effect doesn't need props or state

Learn more about data fetching with Hooks: https://react.dev/link/hooks-data-fetching`:" You returned: "+a,W(t,function(d,v){console.error("%s must not return anything besides a function, which is used for clean-up.%s",d,v)},l,c)}n=n.next}while(n!==o)}}catch(d){Be(t,t.return,d)}}function Tl(e,t,n){try{var a=t.updateQueue,o=a!==null?a.lastEffect:null;if(o!==null){var l=o.next;a=l;do{if((a.tag&e)===e){var c=a.inst,d=c.destroy;d!==void 0&&(c.destroy=void 0,(e&yt)!==In?Y!==null&&typeof Y.markComponentPassiveEffectUnmountStarted=="function"&&Y.markComponentPassiveEffectUnmountStarted(t):(e&jt)!==In&&Y!==null&&typeof Y.markComponentLayoutEffectUnmountStarted=="function"&&Y.markComponentLayoutEffectUnmountStarted(t),(e&Yt)!==In&&(fr=!0),o=t,W(o,ID,o,n,d),(e&Yt)!==In&&(fr=!1),(e&yt)!==In?Y!==null&&typeof Y.markComponentPassiveEffectUnmountStopped=="function"&&Y.markComponentPassiveEffectUnmountStopped():(e&jt)!==In&&Y!==null&&typeof Y.markComponentLayoutEffectUnmountStopped=="function"&&Y.markComponentLayoutEffectUnmountStopped())}a=a.next}while(a!==l)}}catch(v){Be(t,t.return,v)}}function k0(e,t){Ta(e)?(Sa(),ss(t,e),_a()):ss(t,e)}function wp(e,t,n){Ta(e)?(Sa(),Tl(n,e,t),_a()):Tl(n,e,t)}function H0(e){var t=e.updateQueue;if(t!==null){var n=e.stateNode;e.type.defaultProps||"ref"in e.memoizedProps||or||(n.props!==e.memoizedProps&&console.error("Expected %s props to match memoized props before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",ee(e)||"instance"),n.state!==e.memoizedState&&console.error("Expected %s state to match memoized state before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",ee(e)||"instance"));try{W(e,Lb,t,n)}catch(a){Be(e,e.return,a)}}}function RR(e,t,n){return e.getSnapshotBeforeUpdate(t,n)}function zR(e,t){var n=t.memoizedProps,a=t.memoizedState;t=e.stateNode,e.type.defaultProps||"ref"in e.memoizedProps||or||(t.props!==e.memoizedProps&&console.error("Expected %s props to match memoized props before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",ee(e)||"instance"),t.state!==e.memoizedState&&console.error("Expected %s state to match memoized state before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",ee(e)||"instance"));try{var o=Mi(e.type,n,e.elementType===e.type),l=W(e,RR,t,o,a);n=r1,l!==void 0||n.has(e.type)||(n.add(e.type),W(e,function(){console.error("%s.getSnapshotBeforeUpdate(): A snapshot value (or null) must be returned. You have returned undefined.",ee(e))})),t.__reactInternalSnapshotBeforeUpdate=l}catch(c){Be(e,e.return,c)}}function L0(e,t,n){n.props=Mi(e.type,e.memoizedProps),n.state=e.memoizedState,Ta(e)?(Sa(),W(e,UT,e,t,n),_a()):W(e,UT,e,t,n)}function DR(e){var t=e.ref;if(t!==null){switch(e.tag){case 26:case 27:case 5:var n=e.stateNode;break;case 30:n=e.stateNode;break;default:n=e.stateNode}if(typeof t=="function")if(Ta(e))try{Sa(),e.refCleanup=t(n)}finally{_a()}else e.refCleanup=t(n);else typeof t=="string"?console.error("String refs are no longer supported."):t.hasOwnProperty("current")||console.error("Unexpected ref object provided for %s. Use either a ref-setter function or React.createRef().",ee(e)),t.current=n}}function us(e,t){try{W(e,DR,e)}catch(n){Be(e,t,n)}}function Oa(e,t){var n=e.ref,a=e.refCleanup;if(n!==null)if(typeof a=="function")try{if(Ta(e))try{Sa(),W(e,a)}finally{_a(e)}else W(e,a)}catch(o){Be(e,t,o)}finally{e.refCleanup=null,e=e.alternate,e!=null&&(e.refCleanup=null)}else if(typeof n=="function")try{if(Ta(e))try{Sa(),W(e,n,null)}finally{_a(e)}else W(e,n,null)}catch(o){Be(e,t,o)}else n.current=null}function B0(e,t,n,a){var o=e.memoizedProps,l=o.id,c=o.onCommit;o=o.onRender,t=t===null?"mount":"update",Tf&&(t="nested-update"),typeof o=="function"&&o(l,t,e.actualDuration,e.treeBaseDuration,e.actualStartTime,n),typeof c=="function"&&c(e.memoizedProps.id,t,a,n)}function CR(e,t,n,a){var o=e.memoizedProps;e=o.id,o=o.onPostCommit,t=t===null?"mount":"update",Tf&&(t="nested-update"),typeof o=="function"&&o(e,t,a,n)}function V0(e){var t=e.type,n=e.memoizedProps,a=e.stateNode;try{W(e,FR,a,t,n,e)}catch(o){Be(e,e.return,o)}}function Ap(e,t,n){try{W(e,ez,e.stateNode,e.type,n,t,e)}catch(a){Be(e,e.return,a)}}function P0(e){return e.tag===5||e.tag===3||e.tag===26||e.tag===27&&Ho(e.type)||e.tag===4}function xp(e){e:for(;;){for(;e.sibling===null;){if(e.return===null||P0(e.return))return null;e=e.return}for(e.sibling.return=e.return,e=e.sibling;e.tag!==5&&e.tag!==6&&e.tag!==18;){if(e.tag===27&&Ho(e.type)||e.flags&2||e.child===null||e.tag===4)continue e;e.child.return=e,e=e.child}if(!(e.flags&2))return e.stateNode}}function Rp(e,t,n){var a=e.tag;if(a===5||a===6)e=e.stateNode,t?(n.nodeType===9?n.body:n.nodeName==="HTML"?n.ownerDocument.body:n).insertBefore(e,t):(t=n.nodeType===9?n.body:n.nodeName==="HTML"?n.ownerDocument.body:n,t.appendChild(e),n=n._reactRootContainer,n!=null||t.onclick!==null||(t.onclick=Zc));else if(a!==4&&(a===27&&Ho(e.type)&&(n=e.stateNode,t=null),e=e.child,e!==null))for(Rp(e,t,n),e=e.sibling;e!==null;)Rp(e,t,n),e=e.sibling}function Gc(e,t,n){var a=e.tag;if(a===5||a===6)e=e.stateNode,t?n.insertBefore(e,t):n.appendChild(e);else if(a!==4&&(a===27&&Ho(e.type)&&(n=e.stateNode),e=e.child,e!==null))for(Gc(e,t,n),e=e.sibling;e!==null;)Gc(e,t,n),e=e.sibling}function MR(e){for(var t,n=e.return;n!==null;){if(P0(n)){t=n;break}n=n.return}if(t==null)throw Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");switch(t.tag){case 27:t=t.stateNode,n=xp(e),Gc(e,n,t);break;case 5:n=t.stateNode,t.flags&32&&(q_(n),t.flags&=-33),t=xp(e),Gc(e,t,n);break;case 3:case 4:t=t.stateNode.containerInfo,n=xp(e),Rp(e,n,t);break;default:throw Error("Invalid host parent fiber. This error is likely caused by a bug in React. Please file an issue.")}}function $0(e){var t=e.stateNode,n=e.memoizedProps;try{W(e,hz,e.type,n,t,e)}catch(a){Be(e,e.return,a)}}function jR(e,t){if(e=e.containerInfo,Ov=Kf,e=yb(e),yh(e)){if("selectionStart"in e)var n={start:e.selectionStart,end:e.selectionEnd};else e:{n=(n=e.ownerDocument)&&n.defaultView||window;var a=n.getSelection&&n.getSelection();if(a&&a.rangeCount!==0){n=a.anchorNode;var o=a.anchorOffset,l=a.focusNode;a=a.focusOffset;try{n.nodeType,l.nodeType}catch{n=null;break e}var c=0,d=-1,v=-1,y=0,R=0,C=e,A=null;t:for(;;){for(var M;C!==n||o!==0&&C.nodeType!==3||(d=c+o),C!==l||a!==0&&C.nodeType!==3||(v=c+a),C.nodeType===3&&(c+=C.nodeValue.length),(M=C.firstChild)!==null;)A=C,C=M;for(;;){if(C===e)break t;if(A===n&&++y===o&&(d=c),A===l&&++R===a&&(v=c),(M=C.nextSibling)!==null)break;C=A,A=C.parentNode}C=M}n=d===-1||v===-1?null:{start:d,end:v}}else n=null}n=n||{start:0,end:0}}else n=null;for(Ev={focusedElem:e,selectionRange:n},Kf=!1,Ot=t;Ot!==null;)if(t=Ot,e=t.child,(t.subtreeFlags&1024)!==0&&e!==null)e.return=t,Ot=e;else for(;Ot!==null;){switch(e=t=Ot,n=e.alternate,o=e.flags,e.tag){case 0:break;case 11:case 15:break;case 1:(o&1024)!==0&&n!==null&&zR(e,n);break;case 3:if((o&1024)!==0){if(e=e.stateNode.containerInfo,n=e.nodeType,n===9)Kp(e);else if(n===1)switch(e.nodeName){case"HEAD":case"HTML":case"BODY":Kp(e);break;default:e.textContent=""}}break;case 5:case 26:case 27:case 6:case 4:case 17:break;default:if((o&1024)!==0)throw Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}if(e=t.sibling,e!==null){e.return=t.return,Ot=e;break}Ot=t.return}}function q0(e,t,n){var a=n.flags;switch(n.tag){case 0:case 11:case 15:Ja(e,n),a&4&&N0(n,jt|Qn);break;case 1:if(Ja(e,n),a&4)if(e=n.stateNode,t===null)n.type.defaultProps||"ref"in n.memoizedProps||or||(e.props!==n.memoizedProps&&console.error("Expected %s props to match memoized props before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",ee(n)||"instance"),e.state!==n.memoizedState&&console.error("Expected %s state to match memoized state before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",ee(n)||"instance")),Ta(n)?(Sa(),W(n,Jm,n,e),_a()):W(n,Jm,n,e);else{var o=Mi(n.type,t.memoizedProps);t=t.memoizedState,n.type.defaultProps||"ref"in n.memoizedProps||or||(e.props!==n.memoizedProps&&console.error("Expected %s props to match memoized props before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",ee(n)||"instance"),e.state!==n.memoizedState&&console.error("Expected %s state to match memoized state before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",ee(n)||"instance")),Ta(n)?(Sa(),W(n,CT,n,e,o,t,e.__reactInternalSnapshotBeforeUpdate),_a()):W(n,CT,n,e,o,t,e.__reactInternalSnapshotBeforeUpdate)}a&64&&H0(n),a&512&&us(n,n.return);break;case 3:if(t=Xa(),Ja(e,n),a&64&&(a=n.updateQueue,a!==null)){if(o=null,n.child!==null)switch(n.child.tag){case 27:case 5:o=n.child.stateNode;break;case 1:o=n.child.stateNode}try{W(n,Lb,a,o)}catch(c){Be(n,n.return,c)}}e.effectDuration+=bc(t);break;case 27:t===null&&a&4&&$0(n);case 26:case 5:Ja(e,n),t===null&&a&4&&V0(n),a&512&&us(n,n.return);break;case 12:if(a&4){a=Xa(),Ja(e,n),e=n.stateNode,e.effectDuration+=Fr(a);try{W(n,B0,n,t,Sf,e.effectDuration)}catch(c){Be(n,n.return,c)}}else Ja(e,n);break;case 13:Ja(e,n),a&4&&X0(e,n),a&64&&(e=n.memoizedState,e!==null&&(e=e.dehydrated,e!==null&&(n=$R.bind(null,n),cz(e,n))));break;case 22:if(a=n.memoizedState!==null||so,!a){t=t!==null&&t.memoizedState!==null||lt,o=so;var l=lt;so=a,(lt=t)&&!l?Wa(e,n,(n.subtreeFlags&8772)!==0):Ja(e,n),so=o,lt=l}break;case 30:break;default:Ja(e,n)}}function G0(e){var t=e.alternate;t!==null&&(e.alternate=null,G0(t)),e.child=null,e.deletions=null,e.sibling=null,e.tag===5&&(t=e.stateNode,t!==null&&xo(t)),e.stateNode=null,e._debugOwner=null,e.return=null,e.dependencies=null,e.memoizedProps=null,e.memoizedState=null,e.pendingProps=null,e.stateNode=null,e.updateQueue=null}function Ka(e,t,n){for(n=n.child;n!==null;)Y0(e,t,n),n=n.sibling}function Y0(e,t,n){if(Ht&&typeof Ht.onCommitFiberUnmount=="function")try{Ht.onCommitFiberUnmount(Ul,n)}catch(l){za||(za=!0,console.error("React instrumentation encountered an error: %s",l))}switch(n.tag){case 26:lt||Oa(n,t),Ka(e,t,n),n.memoizedState?n.memoizedState.count--:n.stateNode&&(n=n.stateNode,n.parentNode.removeChild(n));break;case 27:lt||Oa(n,t);var a=ht,o=nn;Ho(n.type)&&(ht=n.stateNode,nn=!1),Ka(e,t,n),W(n,vs,n.stateNode),ht=a,nn=o;break;case 5:lt||Oa(n,t);case 6:if(a=ht,o=nn,ht=null,Ka(e,t,n),ht=a,nn=o,ht!==null)if(nn)try{W(n,az,ht,n.stateNode)}catch(l){Be(n,t,l)}else try{W(n,nz,ht,n.stateNode)}catch(l){Be(n,t,l)}break;case 18:ht!==null&&(nn?(e=ht,G_(e.nodeType===9?e.body:e.nodeName==="HTML"?e.ownerDocument.body:e,n.stateNode),_s(e)):G_(ht,n.stateNode));break;case 4:a=ht,o=nn,ht=n.stateNode.containerInfo,nn=!0,Ka(e,t,n),ht=a,nn=o;break;case 0:case 11:case 14:case 15:lt||Tl(Yt,n,t),lt||Ep(n,t,jt),Ka(e,t,n);break;case 1:lt||(Oa(n,t),a=n.stateNode,typeof a.componentWillUnmount=="function"&&L0(n,t,a)),Ka(e,t,n);break;case 21:Ka(e,t,n);break;case 22:lt=(a=lt)||n.memoizedState!==null,Ka(e,t,n),lt=a;break;default:Ka(e,t,n)}}function X0(e,t){if(t.memoizedState===null&&(e=t.alternate,e!==null&&(e=e.memoizedState,e!==null&&(e=e.dehydrated,e!==null))))try{W(t,dz,e)}catch(n){Be(t,t.return,n)}}function UR(e){switch(e.tag){case 13:case 19:var t=e.stateNode;return t===null&&(t=e.stateNode=new s1),t;case 22:return e=e.stateNode,t=e._retryCache,t===null&&(t=e._retryCache=new s1),t;default:throw Error("Unexpected Suspense handler tag ("+e.tag+"). This is a bug in React.")}}function zp(e,t){var n=UR(e);t.forEach(function(a){var o=qR.bind(null,e,a);if(!n.has(a)){if(n.add(a),ua)if(ir!==null&&lr!==null)hs(lr,ir);else throw Error("Expected finished root and lanes to be set. This is a bug in React.");a.then(o,o)}})}function yn(e,t){var n=t.deletions;if(n!==null)for(var a=0;a<n.length;a++){var o=e,l=t,c=n[a],d=l;e:for(;d!==null;){switch(d.tag){case 27:if(Ho(d.type)){ht=d.stateNode,nn=!1;break e}break;case 5:ht=d.stateNode,nn=!1;break e;case 3:case 4:ht=d.stateNode.containerInfo,nn=!0;break e}d=d.return}if(ht===null)throw Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");Y0(o,l,c),ht=null,nn=!1,o=c,l=o.alternate,l!==null&&(l.return=null),o.return=null}if(t.subtreeFlags&13878)for(t=t.child;t!==null;)I0(t,e),t=t.sibling}function I0(e,t){var n=e.alternate,a=e.flags;switch(e.tag){case 0:case 11:case 14:case 15:yn(t,e),gn(e),a&4&&(Tl(Yt|Qn,e,e.return),ss(Yt|Qn,e),Ep(e,e.return,jt|Qn));break;case 1:yn(t,e),gn(e),a&512&&(lt||n===null||Oa(n,n.return)),a&64&&so&&(e=e.updateQueue,e!==null&&(a=e.callbacks,a!==null&&(n=e.shared.hiddenCallbacks,e.shared.hiddenCallbacks=n===null?a:n.concat(a))));break;case 26:var o=ha;if(yn(t,e),gn(e),a&512&&(lt||n===null||Oa(n,n.return)),a&4)if(t=n!==null?n.memoizedState:null,a=e.memoizedState,n===null)if(a===null)if(e.stateNode===null){e:{a=e.type,n=e.memoizedProps,t=o.ownerDocument||o;t:switch(a){case"title":o=t.getElementsByTagName("title")[0],(!o||o[Os]||o[Lt]||o.namespaceURI===kl||o.hasAttribute("itemprop"))&&(o=t.createElement(a),t.head.insertBefore(o,t.querySelector("head > title"))),Dt(o,a,n),o[Lt]=e,w(o),a=o;break e;case"link":var l=eS("link","href",t).get(a+(n.href||""));if(l){for(var c=0;c<l.length;c++)if(o=l[c],o.getAttribute("href")===(n.href==null||n.href===""?null:n.href)&&o.getAttribute("rel")===(n.rel==null?null:n.rel)&&o.getAttribute("title")===(n.title==null?null:n.title)&&o.getAttribute("crossorigin")===(n.crossOrigin==null?null:n.crossOrigin)){l.splice(c,1);break t}}o=t.createElement(a),Dt(o,a,n),t.head.appendChild(o);break;case"meta":if(l=eS("meta","content",t).get(a+(n.content||""))){for(c=0;c<l.length;c++)if(o=l[c],F(n.content,"content"),o.getAttribute("content")===(n.content==null?null:""+n.content)&&o.getAttribute("name")===(n.name==null?null:n.name)&&o.getAttribute("property")===(n.property==null?null:n.property)&&o.getAttribute("http-equiv")===(n.httpEquiv==null?null:n.httpEquiv)&&o.getAttribute("charset")===(n.charSet==null?null:n.charSet)){l.splice(c,1);break t}}o=t.createElement(a),Dt(o,a,n),t.head.appendChild(o);break;default:throw Error('getNodesForType encountered a type it did not expect: "'+a+'". This is a bug in React.')}o[Lt]=e,w(o),a=o}e.stateNode=a}else tS(o,e.type,e.stateNode);else e.stateNode=F_(o,a,e.memoizedProps);else t!==a?(t===null?n.stateNode!==null&&(n=n.stateNode,n.parentNode.removeChild(n)):t.count--,a===null?tS(o,e.type,e.stateNode):F_(o,a,e.memoizedProps)):a===null&&e.stateNode!==null&&Ap(e,e.memoizedProps,n.memoizedProps);break;case 27:yn(t,e),gn(e),a&512&&(lt||n===null||Oa(n,n.return)),n!==null&&a&4&&Ap(e,e.memoizedProps,n.memoizedProps);break;case 5:if(yn(t,e),gn(e),a&512&&(lt||n===null||Oa(n,n.return)),e.flags&32){t=e.stateNode;try{W(e,q_,t)}catch(R){Be(e,e.return,R)}}a&4&&e.stateNode!=null&&(t=e.memoizedProps,Ap(e,t,n!==null?n.memoizedProps:t)),a&1024&&(ov=!0,e.type!=="form"&&console.error("Unexpected host component type. Expected a form. This is a bug in React."));break;case 6:if(yn(t,e),gn(e),a&4){if(e.stateNode===null)throw Error("This should have a text node initialized. This error is likely caused by a bug in React. Please file an issue.");a=e.memoizedProps,n=n!==null?n.memoizedProps:a,t=e.stateNode;try{W(e,tz,t,n,a)}catch(R){Be(e,e.return,R)}}break;case 3:if(o=Xa(),If=null,l=ha,ha=Jc(t.containerInfo),yn(t,e),ha=l,gn(e),a&4&&n!==null&&n.memoizedState.isDehydrated)try{W(e,fz,t.containerInfo)}catch(R){Be(e,e.return,R)}ov&&(ov=!1,Q0(e)),t.effectDuration+=bc(o);break;case 4:a=ha,ha=Jc(e.stateNode.containerInfo),yn(t,e),gn(e),ha=a;break;case 12:a=Xa(),yn(t,e),gn(e),e.stateNode.effectDuration+=Fr(a);break;case 13:yn(t,e),gn(e),e.child.flags&8192&&e.memoizedState!==null!=(n!==null&&n.memoizedState!==null)&&(cv=Ra()),a&4&&(a=e.updateQueue,a!==null&&(e.updateQueue=null,zp(e,a)));break;case 22:o=e.memoizedState!==null;var d=n!==null&&n.memoizedState!==null,v=so,y=lt;if(so=v||o,lt=y||d,yn(t,e),lt=y,so=v,gn(e),a&8192)e:for(t=e.stateNode,t._visibility=o?t._visibility&~yf:t._visibility|yf,o&&(n===null||d||so||lt||ji(e)),n=null,t=e;;){if(t.tag===5||t.tag===26){if(n===null){d=n=t;try{l=d.stateNode,o?W(d,oz,l):W(d,lz,d.stateNode,d.memoizedProps)}catch(R){Be(d,d.return,R)}}}else if(t.tag===6){if(n===null){d=t;try{c=d.stateNode,o?W(d,iz,c):W(d,rz,c,d.memoizedProps)}catch(R){Be(d,d.return,R)}}}else if((t.tag!==22&&t.tag!==23||t.memoizedState===null||t===e)&&t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break e;for(;t.sibling===null;){if(t.return===null||t.return===e)break e;n===t&&(n=null),t=t.return}n===t&&(n=null),t.sibling.return=t.return,t=t.sibling}a&4&&(a=e.updateQueue,a!==null&&(n=a.retryQueue,n!==null&&(a.retryQueue=null,zp(e,n))));break;case 19:yn(t,e),gn(e),a&4&&(a=e.updateQueue,a!==null&&(e.updateQueue=null,zp(e,a)));break;case 30:break;case 21:break;default:yn(t,e),gn(e)}}function gn(e){var t=e.flags;if(t&2){try{W(e,MR,e)}catch(n){Be(e,e.return,n)}e.flags&=-3}t&4096&&(e.flags&=-4097)}function Q0(e){if(e.subtreeFlags&1024)for(e=e.child;e!==null;){var t=e;Q0(t),t.tag===5&&t.flags&1024&&t.stateNode.reset(),e=e.sibling}}function Ja(e,t){if(t.subtreeFlags&8772)for(t=t.child;t!==null;)q0(e,t.alternate,t),t=t.sibling}function Z0(e){switch(e.tag){case 0:case 11:case 14:case 15:Ep(e,e.return,jt),ji(e);break;case 1:Oa(e,e.return);var t=e.stateNode;typeof t.componentWillUnmount=="function"&&L0(e,e.return,t),ji(e);break;case 27:W(e,vs,e.stateNode);case 26:case 5:Oa(e,e.return),ji(e);break;case 22:e.memoizedState===null&&ji(e);break;case 30:ji(e);break;default:ji(e)}}function ji(e){for(e=e.child;e!==null;)Z0(e),e=e.sibling}function K0(e,t,n,a){var o=n.flags;switch(n.tag){case 0:case 11:case 15:Wa(e,n,a),N0(n,jt);break;case 1:if(Wa(e,n,a),t=n.stateNode,typeof t.componentDidMount=="function"&&W(n,Jm,n,t),t=n.updateQueue,t!==null){e=n.stateNode;try{W(n,gR,t,e)}catch(l){Be(n,n.return,l)}}a&&o&64&&H0(n),us(n,n.return);break;case 27:$0(n);case 26:case 5:Wa(e,n,a),a&&t===null&&o&4&&V0(n),us(n,n.return);break;case 12:if(a&&o&4){o=Xa(),Wa(e,n,a),a=n.stateNode,a.effectDuration+=Fr(o);try{W(n,B0,n,t,Sf,a.effectDuration)}catch(l){Be(n,n.return,l)}}else Wa(e,n,a);break;case 13:Wa(e,n,a),a&&o&4&&X0(e,n);break;case 22:n.memoizedState===null&&Wa(e,n,a),us(n,n.return);break;case 30:break;default:Wa(e,n,a)}}function Wa(e,t,n){for(n=n&&(t.subtreeFlags&8772)!==0,t=t.child;t!==null;)K0(e,t.alternate,t,n),t=t.sibling}function Dp(e,t){var n=null;e!==null&&e.memoizedState!==null&&e.memoizedState.cachePool!==null&&(n=e.memoizedState.cachePool.pool),e=null,t.memoizedState!==null&&t.memoizedState.cachePool!==null&&(e=t.memoizedState.cachePool.pool),e!==n&&(e!=null&&Ri(e),n!=null&&Wr(n))}function Cp(e,t){e=null,t.alternate!==null&&(e=t.alternate.memoizedState.cache),t=t.memoizedState.cache,t!==e&&(Ri(t),e!=null&&Wr(e))}function Ea(e,t,n,a){if(t.subtreeFlags&10256)for(t=t.child;t!==null;)J0(e,t,n,a),t=t.sibling}function J0(e,t,n,a){var o=t.flags;switch(t.tag){case 0:case 11:case 15:Ea(e,t,n,a),o&2048&&k0(t,yt|Qn);break;case 1:Ea(e,t,n,a);break;case 3:var l=Xa();Ea(e,t,n,a),o&2048&&(n=null,t.alternate!==null&&(n=t.alternate.memoizedState.cache),t=t.memoizedState.cache,t!==n&&(Ri(t),n!=null&&Wr(n))),e.passiveEffectDuration+=bc(l);break;case 12:if(o&2048){o=Xa(),Ea(e,t,n,a),e=t.stateNode,e.passiveEffectDuration+=Fr(o);try{W(t,CR,t,t.alternate,Sf,e.passiveEffectDuration)}catch(d){Be(t,t.return,d)}}else Ea(e,t,n,a);break;case 13:Ea(e,t,n,a);break;case 23:break;case 22:l=t.stateNode;var c=t.alternate;t.memoizedState!==null?l._visibility&no?Ea(e,t,n,a):cs(e,t):l._visibility&no?Ea(e,t,n,a):(l._visibility|=no,Ol(e,t,n,a,(t.subtreeFlags&10256)!==0)),o&2048&&Dp(c,t);break;case 24:Ea(e,t,n,a),o&2048&&Cp(t.alternate,t);break;default:Ea(e,t,n,a)}}function Ol(e,t,n,a,o){for(o=o&&(t.subtreeFlags&10256)!==0,t=t.child;t!==null;)W0(e,t,n,a,o),t=t.sibling}function W0(e,t,n,a,o){var l=t.flags;switch(t.tag){case 0:case 11:case 15:Ol(e,t,n,a,o),k0(t,yt);break;case 23:break;case 22:var c=t.stateNode;t.memoizedState!==null?c._visibility&no?Ol(e,t,n,a,o):cs(e,t):(c._visibility|=no,Ol(e,t,n,a,o)),o&&l&2048&&Dp(t.alternate,t);break;case 24:Ol(e,t,n,a,o),o&&l&2048&&Cp(t.alternate,t);break;default:Ol(e,t,n,a,o)}}function cs(e,t){if(t.subtreeFlags&10256)for(t=t.child;t!==null;){var n=e,a=t,o=a.flags;switch(a.tag){case 22:cs(n,a),o&2048&&Dp(a.alternate,a);break;case 24:cs(n,a),o&2048&&Cp(a.alternate,a);break;default:cs(n,a)}t=t.sibling}}function El(e){if(e.subtreeFlags&Xs)for(e=e.child;e!==null;)F0(e),e=e.sibling}function F0(e){switch(e.tag){case 26:El(e),e.flags&Xs&&e.memoizedState!==null&&yz(ha,e.memoizedState,e.memoizedProps);break;case 5:El(e);break;case 3:case 4:var t=ha;ha=Jc(e.stateNode.containerInfo),El(e),ha=t;break;case 22:e.memoizedState===null&&(t=e.alternate,t!==null&&t.memoizedState!==null?(t=Xs,Xs=16777216,El(e),Xs=t):El(e));break;default:El(e)}}function e_(e){var t=e.alternate;if(t!==null&&(e=t.child,e!==null)){t.child=null;do t=e.sibling,e.sibling=null,e=t;while(e!==null)}}function fs(e){var t=e.deletions;if((e.flags&16)!==0){if(t!==null)for(var n=0;n<t.length;n++){var a=t[n];Ot=a,a_(a,e)}e_(e)}if(e.subtreeFlags&10256)for(e=e.child;e!==null;)t_(e),e=e.sibling}function t_(e){switch(e.tag){case 0:case 11:case 15:fs(e),e.flags&2048&&wp(e,e.return,yt|Qn);break;case 3:var t=Xa();fs(e),e.stateNode.passiveEffectDuration+=bc(t);break;case 12:t=Xa(),fs(e),e.stateNode.passiveEffectDuration+=Fr(t);break;case 22:t=e.stateNode,e.memoizedState!==null&&t._visibility&no&&(e.return===null||e.return.tag!==13)?(t._visibility&=~no,Yc(e)):fs(e);break;default:fs(e)}}function Yc(e){var t=e.deletions;if((e.flags&16)!==0){if(t!==null)for(var n=0;n<t.length;n++){var a=t[n];Ot=a,a_(a,e)}e_(e)}for(e=e.child;e!==null;)n_(e),e=e.sibling}function n_(e){switch(e.tag){case 0:case 11:case 15:wp(e,e.return,yt),Yc(e);break;case 22:var t=e.stateNode;t._visibility&no&&(t._visibility&=~no,Yc(e));break;default:Yc(e)}}function a_(e,t){for(;Ot!==null;){var n=Ot,a=n;switch(a.tag){case 0:case 11:case 15:wp(a,t,yt);break;case 23:case 22:a.memoizedState!==null&&a.memoizedState.cachePool!==null&&(a=a.memoizedState.cachePool.pool,a!=null&&Ri(a));break;case 24:Wr(a.memoizedState.cache)}if(a=n.child,a!==null)a.return=n,Ot=a;else e:for(n=e;Ot!==null;){a=Ot;var o=a.sibling,l=a.return;if(G0(a),a===n){Ot=null;break e}if(o!==null){o.return=l,Ot=o;break e}Ot=l}}}function NR(){ZD.forEach(function(e){return e()})}function o_(){var e=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0;return e||D.actQueue===null||console.error("The current testing environment is not configured to support act(...)"),e}function bn(e){if((je&Xt)!==Tn&&Oe!==0)return Oe&-Oe;var t=D.T;return t!==null?(t._updatedFibers||(t._updatedFibers=new Set),t._updatedFibers.add(e),e=Bi,e!==0?e:Vp()):Vr()}function i_(){En===0&&(En=(Oe&536870912)===0||xe?_e():536870912);var e=Kn.current;return e!==null&&(e.flags|=32),En}function it(e,t,n){if(fr&&console.error("useInsertionEffect must not schedule updates."),mv&&(Nf=!0),(e===qe&&(Ue===Gi||Ue===Yi)||e.cancelPendingCommit!==null)&&(Al(e,0),No(e,Oe,En,!1)),Ao(e,n),(je&Xt)!==0&&e===qe){if(Ca)switch(t.tag){case 0:case 11:case 15:e=Se&&ee(Se)||"Unknown",g1.has(e)||(g1.add(e),t=ee(t)||"Unknown",console.error("Cannot update a component (`%s`) while rendering a different component (`%s`). To locate the bad setState() call inside `%s`, follow the stack trace as described in https://react.dev/link/setstate-in-render",t,e,e));break;case 1:y1||(console.error("Cannot update during an existing state transition (such as within `render`). Render methods should be a pure function of props and state."),y1=!0)}}else ua&&Lr(e,t,n),YR(t),e===qe&&((je&Xt)===Tn&&(Ko|=n),nt===qi&&No(e,Oe,En,!1)),wa(e)}function l_(e,t,n){if((je&(Xt|pa))!==Tn)throw Error("Should not already be working.");var a=!n&&(t&124)===0&&(t&e.expiredLanes)===0||wo(e,t),o=a?HR(e,t):Up(e,t,!0),l=a;do{if(o===uo){ur&&!a&&No(e,t,0,!1);break}else{if(n=e.current.alternate,l&&!kR(n)){o=Up(e,t,!1),l=!1;continue}if(o===rr){if(l=t,e.errorRecoveryDisabledLanes&l)var c=0;else c=e.pendingLanes&-536870913,c=c!==0?c:c&536870912?536870912:0;if(c!==0){t=c;e:{o=e;var d=c;c=Ws;var v=o.current.memoizedState.isDehydrated;if(v&&(Al(o,d).flags|=256),d=Up(o,d,!1),d!==rr){if(sv&&!v){o.errorRecoveryDisabledLanes|=l,Ko|=l,o=qi;break e}o=It,It=c,o!==null&&(It===null?It=o:It.push.apply(It,o))}o=d}if(l=!1,o!==rr)continue}}if(o===Qs){Al(e,0),No(e,t,0,!0);break}e:{switch(a=e,o){case uo:case Qs:throw Error("Root did not complete. This is a bug in React.");case qi:if((t&4194048)!==t)break;case Mf:No(a,t,En,!Qo);break e;case rr:It=null;break;case iv:case u1:break;default:throw Error("Unknown root exit status.")}if(D.actQueue!==null)Np(a,n,t,It,Fs,jf,En,Ko,Xi);else{if((t&62914560)===t&&(l=cv+f1-Ra(),10<l)){if(No(a,t,En,!Qo),$a(a,0,!0)!==0)break e;a.timeoutHandle=A1(r_.bind(null,a,n,It,Fs,jf,t,En,Ko,Xi,Qo,o,FD,hT,0),l);break e}r_(a,n,It,Fs,jf,t,En,Ko,Xi,Qo,o,JD,hT,0)}}}break}while(!0);wa(e)}function r_(e,t,n,a,o,l,c,d,v,y,R,C,A,M){if(e.timeoutHandle=Ji,C=t.subtreeFlags,(C&8192||(C&16785408)===16785408)&&(iu={stylesheets:null,count:0,unsuspend:vz},F0(t),C=gz(),C!==null)){e.cancelPendingCommit=C(Np.bind(null,e,t,l,n,a,o,c,d,v,R,WD,A,M)),No(e,l,c,!y);return}Np(e,t,l,n,a,o,c,d,v)}function kR(e){for(var t=e;;){var n=t.tag;if((n===0||n===11||n===15)&&t.flags&16384&&(n=t.updateQueue,n!==null&&(n=n.stores,n!==null)))for(var a=0;a<n.length;a++){var o=n[a],l=o.getSnapshot;o=o.value;try{if(!qt(l(),o))return!1}catch{return!1}}if(n=t.child,t.subtreeFlags&16384&&n!==null)n.return=t,t=n;else{if(t===e)break;for(;t.sibling===null;){if(t.return===null||t.return===e)return!0;t=t.return}t.sibling.return=t.return,t=t.sibling}}return!0}function No(e,t,n,a){t&=~uv,t&=~Ko,e.suspendedLanes|=t,e.pingedLanes&=~t,a&&(e.warmLanes|=t),a=e.expirationTimes;for(var o=t;0<o;){var l=31-Pt(o),c=1<<l;a[l]=-1,o&=~c}n!==0&&kr(e,n,t)}function wl(){return(je&(Xt|pa))===Tn?(ps(0),!1):!0}function Mp(){if(Se!==null){if(Ue===an)var e=Se.return;else e=Se,vc(),qh(e),er=null,Gs=0,e=Se;for(;e!==null;)U0(e.alternate,e),e=e.return;Se=null}}function Al(e,t){var n=e.timeoutHandle;n!==Ji&&(e.timeoutHandle=Ji,dC(n)),n=e.cancelPendingCommit,n!==null&&(e.cancelPendingCommit=null,n()),Mp(),qe=e,Se=n=Ga(e.current,null),Oe=t,Ue=an,On=null,Qo=!1,ur=wo(e,t),sv=!1,nt=uo,Xi=En=uv=Ko=Zo=0,It=Ws=null,jf=!1,(t&8)!==0&&(t|=t&32);var a=e.entangledLanes;if(a!==0)for(e=e.entanglements,a&=t;0<a;){var o=31-Pt(a),l=1<<o;t|=e[o],a&=~l}return ka=t,dc(),t=fT(),1e3<t-cT&&(D.recentlyCreatedOwnerStacks=0,cT=t),fa.discardPendingWarnings(),n}function s_(e,t){ce=null,D.H=Df,D.getCurrentStack=null,Ca=!1,Sn=null,t===Vs||t===wf?(t=Nb(),Ue=Ks):t===vT?(t=Nb(),Ue=c1):Ue=t===e1?rv:t!==null&&typeof t=="object"&&typeof t.then=="function"?sr:Zs,On=t;var n=Se;if(n===null)nt=Qs,Vc(e,vn(t,e.current));else switch(n.mode&Mt&&Mh(n),Nn(),Ue){case Zs:Y!==null&&typeof Y.markComponentErrored=="function"&&Y.markComponentErrored(n,t,Oe);break;case Gi:case Yi:case Ks:case sr:case Js:Y!==null&&typeof Y.markComponentSuspended=="function"&&Y.markComponentSuspended(n,t,Oe)}}function u_(){var e=D.H;return D.H=Df,e===null?Df:e}function c_(){var e=D.A;return D.A=QD,e}function jp(){nt=qi,Qo||(Oe&4194048)!==Oe&&Kn.current!==null||(ur=!0),(Zo&134217727)===0&&(Ko&134217727)===0||qe===null||No(qe,Oe,En,!1)}function Up(e,t,n){var a=je;je|=Xt;var o=u_(),l=c_();if(qe!==e||Oe!==t){if(ua){var c=e.memoizedUpdaters;0<c.size&&(hs(e,Oe),c.clear()),Br(e,t)}Fs=null,Al(e,t)}vi(t),t=!1,c=nt;e:do try{if(Ue!==an&&Se!==null){var d=Se,v=On;switch(Ue){case rv:Mp(),c=Mf;break e;case Ks:case Gi:case Yi:case sr:Kn.current===null&&(t=!0);var y=Ue;if(Ue=an,On=null,xl(e,d,v,y),n&&ur){c=uo;break e}break;default:y=Ue,Ue=an,On=null,xl(e,d,v,y)}}f_(),c=nt;break}catch(R){s_(e,R)}while(!0);return t&&e.shellSuspendCounter++,vc(),je=a,D.H=o,D.A=l,Ur(),Se===null&&(qe=null,Oe=0,dc()),c}function f_(){for(;Se!==null;)d_(Se)}function HR(e,t){var n=je;je|=Xt;var a=u_(),o=c_();if(qe!==e||Oe!==t){if(ua){var l=e.memoizedUpdaters;0<l.size&&(hs(e,Oe),l.clear()),Br(e,t)}Fs=null,Uf=Ra()+d1,Al(e,t)}else ur=wo(e,t);vi(t);e:do try{if(Ue!==an&&Se!==null)t:switch(t=Se,l=On,Ue){case Zs:Ue=an,On=null,xl(e,t,l,Zs);break;case Gi:case Yi:if(jb(l)){Ue=an,On=null,h_(t);break}t=function(){Ue!==Gi&&Ue!==Yi||qe!==e||(Ue=Js),wa(e)},l.then(t,t);break e;case Ks:Ue=Js;break e;case c1:Ue=lv;break e;case Js:jb(l)?(Ue=an,On=null,h_(t)):(Ue=an,On=null,xl(e,t,l,Js));break;case lv:var c=null;switch(Se.tag){case 26:c=Se.memoizedState;case 5:case 27:var d=Se;if(!c||nS(c)){Ue=an,On=null;var v=d.sibling;if(v!==null)Se=v;else{var y=d.return;y!==null?(Se=y,Xc(y)):Se=null}break t}break;default:console.error("Unexpected type of fiber triggered a suspensey commit. This is a bug in React.")}Ue=an,On=null,xl(e,t,l,lv);break;case sr:Ue=an,On=null,xl(e,t,l,sr);break;case rv:Mp(),nt=Mf;break e;default:throw Error("Unexpected SuspendedReason. This is a bug in React.")}D.actQueue!==null?f_():LR();break}catch(R){s_(e,R)}while(!0);return vc(),D.H=a,D.A=o,je=n,Se!==null?(Y!==null&&typeof Y.markRenderYielded=="function"&&Y.markRenderYielded(),uo):(Ur(),qe=null,Oe=0,dc(),nt)}function LR(){for(;Se!==null&&!Uz();)d_(Se)}function d_(e){var t=e.alternate;(e.mode&Mt)!==Xe?(Ch(e),t=W(e,Op,t,e,ka),Mh(e)):t=W(e,Op,t,e,ka),e.memoizedProps=e.pendingProps,t===null?Xc(e):Se=t}function h_(e){var t=W(e,BR,e);e.memoizedProps=e.pendingProps,t===null?Xc(e):Se=t}function BR(e){var t=e.alternate,n=(e.mode&Mt)!==Xe;switch(n&&Ch(e),e.tag){case 15:case 0:t=A0(t,e,e.pendingProps,e.type,void 0,Oe);break;case 11:t=A0(t,e,e.pendingProps,e.type.render,e.ref,Oe);break;case 5:qh(e);default:U0(t,e),e=Se=Tb(e,ka),t=Op(t,e,ka)}return n&&Mh(e),t}function xl(e,t,n,a){vc(),qh(t),er=null,Gs=0;var o=t.return;try{if(ER(e,o,t,n,Oe)){nt=Qs,Vc(e,vn(n,e.current)),Se=null;return}}catch(l){if(o!==null)throw Se=o,l;nt=Qs,Vc(e,vn(n,e.current)),Se=null;return}t.flags&32768?(xe||a===Zs?e=!0:ur||(Oe&536870912)!==0?e=!1:(Qo=e=!0,(a===Gi||a===Yi||a===Ks||a===sr)&&(a=Kn.current,a!==null&&a.tag===13&&(a.flags|=16384))),p_(t,e)):Xc(t)}function Xc(e){var t=e;do{if((t.flags&32768)!==0){p_(t,Qo);return}var n=t.alternate;if(e=t.return,Ch(t),n=W(t,AR,n,t,ka),(t.mode&Mt)!==Xe&&zb(t),n!==null){Se=n;return}if(t=t.sibling,t!==null){Se=t;return}Se=t=e}while(t!==null);nt===uo&&(nt=u1)}function p_(e,t){do{var n=xR(e.alternate,e);if(n!==null){n.flags&=32767,Se=n;return}if((e.mode&Mt)!==Xe){zb(e),n=e.actualDuration;for(var a=e.child;a!==null;)n+=a.actualDuration,a=a.sibling;e.actualDuration=n}if(n=e.return,n!==null&&(n.flags|=32768,n.subtreeFlags=0,n.deletions=null),!t&&(e=e.sibling,e!==null)){Se=e;return}Se=e=n}while(e!==null);nt=Mf,Se=null}function Np(e,t,n,a,o,l,c,d,v){e.cancelPendingCommit=null;do ds();while(Ut!==Ii);if(fa.flushLegacyContextWarning(),fa.flushPendingUnsafeLifecycleWarnings(),(je&(Xt|pa))!==Tn)throw Error("Should not already be working.");if(Y!==null&&typeof Y.markCommitStarted=="function"&&Y.markCommitStarted(n),t===null)et();else{if(n===0&&console.error("finishedLanes should not be empty during a commit. This is a bug in React."),t===e.current)throw Error("Cannot commit the same tree as before. This error is likely caused by a bug in React. Please file an issue.");if(l=t.lanes|t.childLanes,l|=Lm,nc(e,n,l,c,d,v),e===qe&&(Se=qe=null,Oe=0),cr=t,Wo=e,Fo=n,dv=l,hv=o,v1=a,(t.subtreeFlags&10256)!==0||(t.flags&10256)!==0?(e.callbackNode=null,e.callbackPriority=0,GR(jl,function(){return b_(),null})):(e.callbackNode=null,e.callbackPriority=0),Sf=Zl(),a=(t.flags&13878)!==0,(t.subtreeFlags&13878)!==0||a){a=D.T,D.T=null,o=Me.p,Me.p=Pn,c=je,je|=pa;try{jR(e,t,n)}finally{je=c,Me.p=o,D.T=a}}Ut=h1,m_(),v_(),y_()}}function m_(){if(Ut===h1){Ut=Ii;var e=Wo,t=cr,n=Fo,a=(t.flags&13878)!==0;if((t.subtreeFlags&13878)!==0||a){a=D.T,D.T=null;var o=Me.p;Me.p=Pn;var l=je;je|=pa;try{ir=n,lr=e,I0(t,e),lr=ir=null,n=Ev;var c=yb(e.containerInfo),d=n.focusedElem,v=n.selectionRange;if(c!==d&&d&&d.ownerDocument&&vb(d.ownerDocument.documentElement,d)){if(v!==null&&yh(d)){var y=v.start,R=v.end;if(R===void 0&&(R=y),"selectionStart"in d)d.selectionStart=y,d.selectionEnd=Math.min(R,d.value.length);else{var C=d.ownerDocument||document,A=C&&C.defaultView||window;if(A.getSelection){var M=A.getSelection(),Q=d.textContent.length,le=Math.min(v.start,Q),Ge=v.end===void 0?le:Math.min(v.end,Q);!M.extend&&le>Ge&&(c=Ge,Ge=le,le=c);var we=mb(d,le),S=mb(d,Ge);if(we&&S&&(M.rangeCount!==1||M.anchorNode!==we.node||M.anchorOffset!==we.offset||M.focusNode!==S.node||M.focusOffset!==S.offset)){var T=C.createRange();T.setStart(we.node,we.offset),M.removeAllRanges(),le>Ge?(M.addRange(T),M.extend(S.node,S.offset)):(T.setEnd(S.node,S.offset),M.addRange(T))}}}}for(C=[],M=d;M=M.parentNode;)M.nodeType===1&&C.push({element:M,left:M.scrollLeft,top:M.scrollTop});for(typeof d.focus=="function"&&d.focus(),d=0;d<C.length;d++){var E=C[d];E.element.scrollLeft=E.left,E.element.scrollTop=E.top}}Kf=!!Ov,Ev=Ov=null}finally{je=l,Me.p=o,D.T=a}}e.current=t,Ut=p1}}function v_(){if(Ut===p1){Ut=Ii;var e=Wo,t=cr,n=Fo,a=(t.flags&8772)!==0;if((t.subtreeFlags&8772)!==0||a){a=D.T,D.T=null;var o=Me.p;Me.p=Pn;var l=je;je|=pa;try{Y!==null&&typeof Y.markLayoutEffectsStarted=="function"&&Y.markLayoutEffectsStarted(n),ir=n,lr=e,q0(e,t.alternate,t),lr=ir=null,Y!==null&&typeof Y.markLayoutEffectsStopped=="function"&&Y.markLayoutEffectsStopped()}finally{je=l,Me.p=o,D.T=a}}Ut=m1}}function y_(){if(Ut===eC||Ut===m1){Ut=Ii,Nz();var e=Wo,t=cr,n=Fo,a=v1,o=(t.subtreeFlags&10256)!==0||(t.flags&10256)!==0;o?Ut=fv:(Ut=Ii,cr=Wo=null,g_(e,e.pendingLanes),Qi=0,tu=null);var l=e.pendingLanes;if(l===0&&(Jo=null),o||O_(e),o=hl(n),t=t.stateNode,Ht&&typeof Ht.onCommitFiberRoot=="function")try{var c=(t.current.flags&128)===128;switch(o){case Pn:var d=vm;break;case Da:d=ym;break;case to:d=jl;break;case cf:d=gm;break;default:d=jl}Ht.onCommitFiberRoot(Ul,t,d,c)}catch(C){za||(za=!0,console.error("React instrumentation encountered an error: %s",C))}if(ua&&e.memoizedUpdaters.clear(),NR(),a!==null){c=D.T,d=Me.p,Me.p=Pn,D.T=null;try{var v=e.onRecoverableError;for(t=0;t<a.length;t++){var y=a[t],R=VR(y.stack);W(y.source,v,y.value,R)}}finally{D.T=c,Me.p=d}}(Fo&3)!==0&&ds(),wa(e),l=e.pendingLanes,(n&4194090)!==0&&(l&42)!==0?(Of=!0,e===pv?eu++:(eu=0,pv=e)):eu=0,ps(0),et()}}function VR(e){return e={componentStack:e},Object.defineProperty(e,"digest",{get:function(){console.error('You are accessing "digest" from the errorInfo object passed to onRecoverableError. This property is no longer provided as part of errorInfo but can be accessed as a property of the Error instance itself.')}}),e}function g_(e,t){(e.pooledCacheLanes&=t)===0&&(t=e.pooledCache,t!=null&&(e.pooledCache=null,Wr(t)))}function ds(e){return m_(),v_(),y_(),b_()}function b_(){if(Ut!==fv)return!1;var e=Wo,t=dv;dv=0;var n=hl(Fo),a=to>n?to:n;n=D.T;var o=Me.p;try{Me.p=a,D.T=null,a=hv,hv=null;var l=Wo,c=Fo;if(Ut=Ii,cr=Wo=null,Fo=0,(je&(Xt|pa))!==Tn)throw Error("Cannot flush passive effects while already rendering.");mv=!0,Nf=!1,Y!==null&&typeof Y.markPassiveEffectsStarted=="function"&&Y.markPassiveEffectsStarted(c);var d=je;if(je|=pa,t_(l.current),J0(l,l.current,c,a),Y!==null&&typeof Y.markPassiveEffectsStopped=="function"&&Y.markPassiveEffectsStopped(),O_(l),je=d,ps(0,!1),Nf?l===tu?Qi++:(Qi=0,tu=l):Qi=0,Nf=mv=!1,Ht&&typeof Ht.onPostCommitFiberRoot=="function")try{Ht.onPostCommitFiberRoot(Ul,l)}catch(y){za||(za=!0,console.error("React instrumentation encountered an error: %s",y))}var v=l.current.stateNode;return v.effectDuration=0,v.passiveEffectDuration=0,!0}finally{Me.p=o,D.T=n,g_(e,t)}}function __(e,t,n){t=vn(n,t),t=dp(e.stateNode,t,2),e=Co(e,t,2),e!==null&&(Ao(e,2),wa(e))}function Be(e,t,n){if(fr=!1,e.tag===3)__(e,e,n);else{for(;t!==null;){if(t.tag===3){__(t,e,n);return}if(t.tag===1){var a=t.stateNode;if(typeof t.type.getDerivedStateFromError=="function"||typeof a.componentDidCatch=="function"&&(Jo===null||!Jo.has(a))){e=vn(n,e),n=hp(2),a=Co(t,n,2),a!==null&&(pp(n,a,t,e),Ao(a,2),wa(a));return}}t=t.return}console.error(`Internal React error: Attempted to capture a commit phase error inside a detached tree. This indicates a bug in React. Potential causes include deleting the same fiber more than once, committing an already-finished tree, or an inconsistent return pointer.

Error message:

%s`,n)}}function kp(e,t,n){var a=e.pingCache;if(a===null){a=e.pingCache=new KD;var o=new Set;a.set(t,o)}else o=a.get(t),o===void 0&&(o=new Set,a.set(t,o));o.has(n)||(sv=!0,o.add(n),a=PR.bind(null,e,t,n),ua&&hs(e,n),t.then(a,a))}function PR(e,t,n){var a=e.pingCache;a!==null&&a.delete(t),e.pingedLanes|=e.suspendedLanes&n,e.warmLanes&=~n,o_()&&D.actQueue===null&&console.error(`A suspended resource finished loading inside a test, but the event was not wrapped in act(...).

When testing, code that resolves suspended data should be wrapped into act(...):

act(() => {
  /* finish loading suspended data */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://react.dev/link/wrap-tests-with-act`),qe===e&&(Oe&n)===n&&(nt===qi||nt===iv&&(Oe&62914560)===Oe&&Ra()-cv<f1?(je&Xt)===Tn&&Al(e,0):uv|=n,Xi===Oe&&(Xi=0)),wa(e)}function S_(e,t){t===0&&(t=yi()),e=Jt(e,t),e!==null&&(Ao(e,t),wa(e))}function $R(e){var t=e.memoizedState,n=0;t!==null&&(n=t.retryLane),S_(e,n)}function qR(e,t){var n=0;switch(e.tag){case 13:var a=e.stateNode,o=e.memoizedState;o!==null&&(n=o.retryLane);break;case 19:a=e.stateNode;break;case 22:a=e.stateNode._retryCache;break;default:throw Error("Pinged unknown suspense boundary type. This is probably a bug in React.")}a!==null&&a.delete(t),S_(e,n)}function Hp(e,t,n){if((t.subtreeFlags&67117056)!==0)for(t=t.child;t!==null;){var a=e,o=t,l=o.type===of;l=n||l,o.tag!==22?o.flags&67108864?l&&W(o,T_,a,o,(o.mode&rT)===Xe):Hp(a,o,l):o.memoizedState===null&&(l&&o.flags&8192?W(o,T_,a,o):o.subtreeFlags&67108864&&W(o,Hp,a,o,l)),t=t.sibling}}function T_(e,t){var n=2<arguments.length&&arguments[2]!==void 0?arguments[2]:!0;Ce(!0);try{Z0(t),n&&n_(t),K0(e,t.alternate,t,!1),n&&W0(e,t,0,null,!1,0)}finally{Ce(!1)}}function O_(e){var t=!0;e.current.mode&(Bt|ca)||(t=!1),Hp(e,e.current,t)}function E_(e){if((je&Xt)===Tn){var t=e.tag;if(t===3||t===1||t===0||t===11||t===14||t===15){if(t=ee(e)||"ReactComponent",kf!==null){if(kf.has(t))return;kf.add(t)}else kf=new Set([t]);W(e,function(){console.error("Can't perform a React state update on a component that hasn't mounted yet. This indicates that you have a side-effect in your render function that asynchronously later calls tries to update the component. Move this work to useEffect instead.")})}}}function hs(e,t){ua&&e.memoizedUpdaters.forEach(function(n){Lr(e,n,t)})}function GR(e,t){var n=D.actQueue;return n!==null?(n.push(t),aC):mm(e,t)}function YR(e){o_()&&D.actQueue===null&&W(e,function(){console.error(`An update to %s inside a test was not wrapped in act(...).

When testing, code that causes React state updates should be wrapped into act(...):

act(() => {
  /* fire events that update state */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://react.dev/link/wrap-tests-with-act`,ee(e))})}function wa(e){e!==dr&&e.next===null&&(dr===null?Hf=dr=e:dr=dr.next=e),Lf=!0,D.actQueue!==null?yv||(yv=!0,R_()):vv||(vv=!0,R_())}function ps(e,t){if(!gv&&Lf){gv=!0;do for(var n=!1,a=Hf;a!==null;){if(e!==0){var o=a.pendingLanes;if(o===0)var l=0;else{var c=a.suspendedLanes,d=a.pingedLanes;l=(1<<31-Pt(42|e)+1)-1,l&=o&~(c&~d),l=l&201326741?l&201326741|1:l?l|2:0}l!==0&&(n=!0,x_(a,l))}else l=Oe,l=$a(a,a===qe?l:0,a.cancelPendingCommit!==null||a.timeoutHandle!==Ji),(l&3)===0||wo(a,l)||(n=!0,x_(a,l));a=a.next}while(n);gv=!1}}function XR(){Lp()}function Lp(){Lf=yv=vv=!1;var e=0;Zi!==0&&(JR()&&(e=Zi),Zi=0);for(var t=Ra(),n=null,a=Hf;a!==null;){var o=a.next,l=w_(a,t);l===0?(a.next=null,n===null?Hf=o:n.next=o,o===null&&(dr=n)):(n=a,(e!==0||(l&3)!==0)&&(Lf=!0)),a=o}ps(e)}function w_(e,t){for(var n=e.suspendedLanes,a=e.pingedLanes,o=e.expirationTimes,l=e.pendingLanes&-62914561;0<l;){var c=31-Pt(l),d=1<<c,v=o[c];v===-1?((d&n)===0||(d&a)!==0)&&(o[c]=ah(d,t)):v<=t&&(e.expiredLanes|=d),l&=~d}if(t=qe,n=Oe,n=$a(e,e===t?n:0,e.cancelPendingCommit!==null||e.timeoutHandle!==Ji),a=e.callbackNode,n===0||e===t&&(Ue===Gi||Ue===Yi)||e.cancelPendingCommit!==null)return a!==null&&Bp(a),e.callbackNode=null,e.callbackPriority=0;if((n&3)===0||wo(e,n)){if(t=n&-n,t!==e.callbackPriority||D.actQueue!==null&&a!==bv)Bp(a);else return t;switch(hl(n)){case Pn:case Da:n=ym;break;case to:n=jl;break;case cf:n=gm;break;default:n=jl}return a=A_.bind(null,e),D.actQueue!==null?(D.actQueue.push(a),n=bv):n=mm(n,a),e.callbackPriority=t,e.callbackNode=n,t}return a!==null&&Bp(a),e.callbackPriority=2,e.callbackNode=null,2}function A_(e,t){if(Of=Tf=!1,Ut!==Ii&&Ut!==fv)return e.callbackNode=null,e.callbackPriority=0,null;var n=e.callbackNode;if(ds()&&e.callbackNode!==n)return null;var a=Oe;return a=$a(e,e===qe?a:0,e.cancelPendingCommit!==null||e.timeoutHandle!==Ji),a===0?null:(l_(e,a,t),w_(e,Ra()),e.callbackNode!=null&&e.callbackNode===n?A_.bind(null,e):null)}function x_(e,t){if(ds())return null;Tf=Of,Of=!1,l_(e,t,!0)}function Bp(e){e!==bv&&e!==null&&jz(e)}function R_(){D.actQueue!==null&&D.actQueue.push(function(){return Lp(),null}),hC(function(){(je&(Xt|pa))!==Tn?mm(vm,XR):Lp()})}function Vp(){return Zi===0&&(Zi=_e()),Zi}function z_(e){return e==null||typeof e=="symbol"||typeof e=="boolean"?null:typeof e=="function"?e:(F(e,"action"),Yr(""+e))}function D_(e,t){var n=t.ownerDocument.createElement("input");return n.name=t.name,n.value=t.value,e.id&&n.setAttribute("form",e.id),t.parentNode.insertBefore(n,t),e=new FormData(e),n.parentNode.removeChild(n),e}function IR(e,t,n,a,o){if(t==="submit"&&n&&n.stateNode===o){var l=z_((o[en]||null).action),c=a.submitter;c&&(t=(t=c[en]||null)?z_(t.formAction):c.getAttribute("formAction"),t!==null&&(l=t,c=null));var d=new mf("action","action",null,a,o);e.push({event:d,listeners:[{instance:null,listener:function(){if(a.defaultPrevented){if(Zi!==0){var v=c?D_(o,c):new FormData(o),y={pending:!0,data:v,method:o.method,action:l};Object.freeze(y),ip(n,y,null,v)}}else typeof l=="function"&&(d.preventDefault(),v=c?D_(o,c):new FormData(o),y={pending:!0,data:v,method:o.method,action:l},Object.freeze(y),ip(n,y,l,v))},currentTarget:o}]})}}function Ic(e,t,n){e.currentTarget=n;try{t(e)}catch(a){ev(a)}e.currentTarget=null}function C_(e,t){t=(t&4)!==0;for(var n=0;n<e.length;n++){var a=e[n];e:{var o=void 0,l=a.event;if(a=a.listeners,t)for(var c=a.length-1;0<=c;c--){var d=a[c],v=d.instance,y=d.currentTarget;if(d=d.listener,v!==o&&l.isPropagationStopped())break e;v!==null?W(v,Ic,l,d,y):Ic(l,d,y),o=v}else for(c=0;c<a.length;c++){if(d=a[c],v=d.instance,y=d.currentTarget,d=d.listener,v!==o&&l.isPropagationStopped())break e;v!==null?W(v,Ic,l,d,y):Ic(l,d,y),o=v}}}}function Ee(e,t){_v.has(e)||console.error('Did not expect a listenToNonDelegatedEvent() call for "%s". This is a bug in React. Please file an issue.',e);var n=t[bm];n===void 0&&(n=t[bm]=new Set);var a=e+"__bubble";n.has(a)||(M_(t,e,2,!1),n.add(a))}function Pp(e,t,n){_v.has(e)&&!t&&console.error('Did not expect a listenToNativeEvent() call for "%s" in the bubble phase. This is a bug in React. Please file an issue.',e);var a=0;t&&(a|=4),M_(n,e,a,t)}function $p(e){if(!e[Bf]){e[Bf]=!0,mS.forEach(function(n){n!=="selectionchange"&&(_v.has(n)||Pp(n,!1,e),Pp(n,!0,e))});var t=e.nodeType===9?e:e.ownerDocument;t===null||t[Bf]||(t[Bf]=!0,Pp("selectionchange",!1,t))}}function M_(e,t,n,a){switch(rS(t)){case Pn:var o=Oz;break;case Da:o=Ez;break;default:o=am}n=o.bind(null,t,n,e),o=void 0,!xm||t!=="touchstart"&&t!=="touchmove"&&t!=="wheel"||(o=!0),a?o!==void 0?e.addEventListener(t,n,{capture:!0,passive:o}):e.addEventListener(t,n,!0):o!==void 0?e.addEventListener(t,n,{passive:o}):e.addEventListener(t,n,!1)}function qp(e,t,n,a,o){var l=a;if((t&1)===0&&(t&2)===0&&a!==null)e:for(;;){if(a===null)return;var c=a.tag;if(c===3||c===4){var d=a.stateNode.containerInfo;if(d===o)break;if(c===4)for(c=a.return;c!==null;){var v=c.tag;if((v===3||v===4)&&c.stateNode.containerInfo===o)return;c=c.return}for(;d!==null;){if(c=ia(d),c===null)return;if(v=c.tag,v===5||v===6||v===26||v===27){a=l=c;continue e}d=d.parentNode}}a=a.return}ob(function(){var y=l,R=mh(n),C=[];e:{var A=lT.get(e);if(A!==void 0){var M=mf,Q=e;switch(e){case"keypress":if(uc(n)===0)break e;case"keydown":case"keyup":M=_D;break;case"focusin":Q="focus",M=Cm;break;case"focusout":Q="blur",M=Cm;break;case"beforeblur":case"afterblur":M=Cm;break;case"click":if(n.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":M=XS;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":M=sD;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":M=OD;break;case nT:case aT:case oT:M=fD;break;case iT:M=wD;break;case"scroll":case"scrollend":M=lD;break;case"wheel":M=xD;break;case"copy":case"cut":case"paste":M=hD;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":M=QS;break;case"toggle":case"beforetoggle":M=zD}var le=(t&4)!==0,Ge=!le&&(e==="scroll"||e==="scrollend"),we=le?A!==null?A+"Capture":null:A;le=[];for(var S=y,T;S!==null;){var E=S;if(T=E.stateNode,E=E.tag,E!==5&&E!==26&&E!==27||T===null||we===null||(E=Xr(S,we),E!=null&&le.push(ms(S,E,T))),Ge)break;S=S.return}0<le.length&&(A=new M(A,Q,null,n,R),C.push({event:A,listeners:le}))}}if((t&7)===0){e:{if(A=e==="mouseover"||e==="pointerover",M=e==="mouseout"||e==="pointerout",A&&n!==ws&&(Q=n.relatedTarget||n.fromElement)&&(ia(Q)||Q[$o]))break e;if((M||A)&&(A=R.window===R?R:(A=R.ownerDocument)?A.defaultView||A.parentWindow:window,M?(Q=n.relatedTarget||n.toElement,M=y,Q=Q?ia(Q):null,Q!==null&&(Ge=ie(Q),le=Q.tag,Q!==Ge||le!==5&&le!==27&&le!==6)&&(Q=null)):(M=null,Q=y),M!==Q)){if(le=XS,E="onMouseLeave",we="onMouseEnter",S="mouse",(e==="pointerout"||e==="pointerover")&&(le=QS,E="onPointerLeave",we="onPointerEnter",S="pointer"),Ge=M==null?A:Ro(M),T=Q==null?A:Ro(Q),A=new le(E,S+"leave",M,n,R),A.target=Ge,A.relatedTarget=T,E=null,ia(R)===y&&(le=new le(we,S+"enter",Q,n,R),le.target=T,le.relatedTarget=Ge,E=le),Ge=E,M&&Q)t:{for(le=M,we=Q,S=0,T=le;T;T=Rl(T))S++;for(T=0,E=we;E;E=Rl(E))T++;for(;0<S-T;)le=Rl(le),S--;for(;0<T-S;)we=Rl(we),T--;for(;S--;){if(le===we||we!==null&&le===we.alternate)break t;le=Rl(le),we=Rl(we)}le=null}else le=null;M!==null&&j_(C,A,M,le,!1),Q!==null&&Ge!==null&&j_(C,Ge,Q,le,!0)}}e:{if(A=y?Ro(y):window,M=A.nodeName&&A.nodeName.toLowerCase(),M==="select"||M==="input"&&A.type==="file")var j=fb;else if(ub(A))if(eT)j=hR;else{j=fR;var $=cR}else M=A.nodeName,!M||M.toLowerCase()!=="input"||A.type!=="checkbox"&&A.type!=="radio"?y&&Gr(y.elementType)&&(j=fb):j=dR;if(j&&(j=j(e,y))){cb(C,j,n,R);break e}$&&$(e,A,y),e==="focusout"&&y&&A.type==="number"&&y.memoizedProps.value!=null&&uh(A,"number",A.value)}switch($=y?Ro(y):window,e){case"focusin":(ub($)||$.contentEditable==="true")&&(Pl=$,jm=y,Ms=null);break;case"focusout":Ms=jm=Pl=null;break;case"mousedown":Um=!0;break;case"contextmenu":case"mouseup":case"dragend":Um=!1,gb(C,n,R);break;case"selectionchange":if(jD)break;case"keydown":case"keyup":gb(C,n,R)}var fe;if(Mm)e:{switch(e){case"compositionstart":var Z="onCompositionStart";break e;case"compositionend":Z="onCompositionEnd";break e;case"compositionupdate":Z="onCompositionUpdate";break e}Z=void 0}else Vl?rb(e,n)&&(Z="onCompositionEnd"):e==="keydown"&&n.keyCode===ZS&&(Z="onCompositionStart");Z&&(KS&&n.locale!=="ko"&&(Vl||Z!=="onCompositionStart"?Z==="onCompositionEnd"&&Vl&&(fe=ib()):(qo=R,Rm="value"in qo?qo.value:qo.textContent,Vl=!0)),$=Qc(y,Z),0<$.length&&(Z=new IS(Z,e,null,n,R),C.push({event:Z,listeners:$}),fe?Z.data=fe:(fe=sb(n),fe!==null&&(Z.data=fe)))),(fe=CD?lR(e,n):rR(e,n))&&(Z=Qc(y,"onBeforeInput"),0<Z.length&&($=new mD("onBeforeInput","beforeinput",null,n,R),C.push({event:$,listeners:Z}),$.data=fe)),IR(C,e,y,n,R)}C_(C,t)})}function ms(e,t,n){return{instance:e,listener:t,currentTarget:n}}function Qc(e,t){for(var n=t+"Capture",a=[];e!==null;){var o=e,l=o.stateNode;if(o=o.tag,o!==5&&o!==26&&o!==27||l===null||(o=Xr(e,n),o!=null&&a.unshift(ms(e,o,l)),o=Xr(e,t),o!=null&&a.push(ms(e,o,l))),e.tag===3)return a;e=e.return}return[]}function Rl(e){if(e===null)return null;do e=e.return;while(e&&e.tag!==5&&e.tag!==27);return e||null}function j_(e,t,n,a,o){for(var l=t._reactName,c=[];n!==null&&n!==a;){var d=n,v=d.alternate,y=d.stateNode;if(d=d.tag,v!==null&&v===a)break;d!==5&&d!==26&&d!==27||y===null||(v=y,o?(y=Xr(n,l),y!=null&&c.unshift(ms(n,y,v))):o||(y=Xr(n,l),y!=null&&c.push(ms(n,y,v)))),n=n.return}c.length!==0&&e.push({event:t,listeners:c})}function Gp(e,t){nR(e,t),e!=="input"&&e!=="textarea"&&e!=="select"||t==null||t.value!==null||GS||(GS=!0,e==="select"&&t.multiple?console.error("`value` prop on `%s` should not be null. Consider using an empty array when `multiple` is set to `true` to clear the component or `undefined` for uncontrolled components.",e):console.error("`value` prop on `%s` should not be null. Consider using an empty string to clear the component or `undefined` for uncontrolled components.",e));var n={registrationNameDependencies:Ui,possibleRegistrationNames:_m};Gr(e)||typeof t.is=="string"||oR(e,t,n),t.contentEditable&&!t.suppressContentEditableWarning&&t.children!=null&&console.error("A component is `contentEditable` and contains `children` managed by React. It is now your responsibility to guarantee that none of those nodes are unexpectedly modified or duplicated. This is probably not intentional.")}function St(e,t,n,a){t!==n&&(n=ko(n),ko(t)!==n&&(a[e]=t))}function QR(e,t,n){t.forEach(function(a){n[k_(a)]=a==="style"?Xp(e):e.getAttribute(a)})}function Aa(e,t){t===!1?console.error("Expected `%s` listener to be a function, instead got `false`.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.",e,e,e):console.error("Expected `%s` listener to be a function, instead got a value of `%s` type.",e,typeof t)}function U_(e,t){return e=e.namespaceURI===df||e.namespaceURI===kl?e.ownerDocument.createElementNS(e.namespaceURI,e.tagName):e.ownerDocument.createElement(e.tagName),e.innerHTML=t,e.innerHTML}function ko(e){return H(e)&&(console.error("The provided HTML markup uses a value of unsupported type %s. This value must be coerced to a string before using it here.",De(e)),ae(e)),(typeof e=="string"?e:""+e).replace(oC,`
`).replace(iC,"")}function N_(e,t){return t=ko(t),ko(e)===t}function Zc(){}function Ve(e,t,n,a,o,l){switch(n){case"children":typeof a=="string"?(sc(a,t,!1),t==="body"||t==="textarea"&&a===""||qr(e,a)):(typeof a=="number"||typeof a=="bigint")&&(sc(""+a,t,!1),t!=="body"&&qr(e,""+a));break;case"className":me(e,"class",a);break;case"tabIndex":me(e,"tabindex",a);break;case"dir":case"role":case"viewBox":case"width":case"height":me(e,n,a);break;case"style":tb(e,a,l);break;case"data":if(t!=="object"){me(e,"data",a);break}case"src":case"href":if(a===""&&(t!=="a"||n!=="href")){console.error(n==="src"?'An empty string ("") was passed to the %s attribute. This may cause the browser to download the whole page again over the network. To fix this, either do not render the element at all or pass null to %s instead of an empty string.':'An empty string ("") was passed to the %s attribute. To fix this, either do not render the element at all or pass null to %s instead of an empty string.',n,n),e.removeAttribute(n);break}if(a==null||typeof a=="function"||typeof a=="symbol"||typeof a=="boolean"){e.removeAttribute(n);break}F(a,n),a=Yr(""+a),e.setAttribute(n,a);break;case"action":case"formAction":if(a!=null&&(t==="form"?n==="formAction"?console.error("You can only pass the formAction prop to <input> or <button>. Use the action prop on <form>."):typeof a=="function"&&(o.encType==null&&o.method==null||$f||($f=!0,console.error("Cannot specify a encType or method for a form that specifies a function as the action. React provides those automatically. They will get overridden.")),o.target==null||Pf||(Pf=!0,console.error("Cannot specify a target for a form that specifies a function as the action. The function will always be executed in the same window."))):t==="input"||t==="button"?n==="action"?console.error("You can only pass the action prop to <form>. Use the formAction prop on <input> or <button>."):t!=="input"||o.type==="submit"||o.type==="image"||Vf?t!=="button"||o.type==null||o.type==="submit"||Vf?typeof a=="function"&&(o.name==null||S1||(S1=!0,console.error('Cannot specify a "name" prop for a button that specifies a function as a formAction. React needs it to encode which action should be invoked. It will get overridden.')),o.formEncType==null&&o.formMethod==null||$f||($f=!0,console.error("Cannot specify a formEncType or formMethod for a button that specifies a function as a formAction. React provides those automatically. They will get overridden.")),o.formTarget==null||Pf||(Pf=!0,console.error("Cannot specify a formTarget for a button that specifies a function as a formAction. The function will always be executed in the same window."))):(Vf=!0,console.error('A button can only specify a formAction along with type="submit" or no type.')):(Vf=!0,console.error('An input can only specify a formAction along with type="submit" or type="image".')):console.error(n==="action"?"You can only pass the action prop to <form>.":"You can only pass the formAction prop to <input> or <button>.")),typeof a=="function"){e.setAttribute(n,"javascript:throw new Error('A React form was unexpectedly submitted. If you called form.submit() manually, consider using form.requestSubmit() instead. If you\\'re trying to use event.stopPropagation() in a submit event handler, consider also calling event.preventDefault().')");break}else typeof l=="function"&&(n==="formAction"?(t!=="input"&&Ve(e,t,"name",o.name,o,null),Ve(e,t,"formEncType",o.formEncType,o,null),Ve(e,t,"formMethod",o.formMethod,o,null),Ve(e,t,"formTarget",o.formTarget,o,null)):(Ve(e,t,"encType",o.encType,o,null),Ve(e,t,"method",o.method,o,null),Ve(e,t,"target",o.target,o,null)));if(a==null||typeof a=="symbol"||typeof a=="boolean"){e.removeAttribute(n);break}F(a,n),a=Yr(""+a),e.setAttribute(n,a);break;case"onClick":a!=null&&(typeof a!="function"&&Aa(n,a),e.onclick=Zc);break;case"onScroll":a!=null&&(typeof a!="function"&&Aa(n,a),Ee("scroll",e));break;case"onScrollEnd":a!=null&&(typeof a!="function"&&Aa(n,a),Ee("scrollend",e));break;case"dangerouslySetInnerHTML":if(a!=null){if(typeof a!="object"||!("__html"in a))throw Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://react.dev/link/dangerously-set-inner-html for more information.");if(n=a.__html,n!=null){if(o.children!=null)throw Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");e.innerHTML=n}}break;case"multiple":e.multiple=a&&typeof a!="function"&&typeof a!="symbol";break;case"muted":e.muted=a&&typeof a!="function"&&typeof a!="symbol";break;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":break;case"autoFocus":break;case"xlinkHref":if(a==null||typeof a=="function"||typeof a=="boolean"||typeof a=="symbol"){e.removeAttribute("xlink:href");break}F(a,n),n=Yr(""+a),e.setAttributeNS(Ki,"xlink:href",n);break;case"contentEditable":case"spellCheck":case"draggable":case"value":case"autoReverse":case"externalResourcesRequired":case"focusable":case"preserveAlpha":a!=null&&typeof a!="function"&&typeof a!="symbol"?(F(a,n),e.setAttribute(n,""+a)):e.removeAttribute(n);break;case"inert":a!==""||qf[n]||(qf[n]=!0,console.error("Received an empty string for a boolean attribute `%s`. This will treat the attribute as if it were false. Either pass `false` to silence this warning, or pass `true` if you used an empty string in earlier versions of React to indicate this attribute is true.",n));case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":a&&typeof a!="function"&&typeof a!="symbol"?e.setAttribute(n,""):e.removeAttribute(n);break;case"capture":case"download":a===!0?e.setAttribute(n,""):a!==!1&&a!=null&&typeof a!="function"&&typeof a!="symbol"?(F(a,n),e.setAttribute(n,a)):e.removeAttribute(n);break;case"cols":case"rows":case"size":case"span":a!=null&&typeof a!="function"&&typeof a!="symbol"&&!isNaN(a)&&1<=a?(F(a,n),e.setAttribute(n,a)):e.removeAttribute(n);break;case"rowSpan":case"start":a==null||typeof a=="function"||typeof a=="symbol"||isNaN(a)?e.removeAttribute(n):(F(a,n),e.setAttribute(n,a));break;case"popover":Ee("beforetoggle",e),Ee("toggle",e),Ae(e,"popover",a);break;case"xlinkActuate":_t(e,Ki,"xlink:actuate",a);break;case"xlinkArcrole":_t(e,Ki,"xlink:arcrole",a);break;case"xlinkRole":_t(e,Ki,"xlink:role",a);break;case"xlinkShow":_t(e,Ki,"xlink:show",a);break;case"xlinkTitle":_t(e,Ki,"xlink:title",a);break;case"xlinkType":_t(e,Ki,"xlink:type",a);break;case"xmlBase":_t(e,Sv,"xml:base",a);break;case"xmlLang":_t(e,Sv,"xml:lang",a);break;case"xmlSpace":_t(e,Sv,"xml:space",a);break;case"is":l!=null&&console.error('Cannot update the "is" prop after it has been initialized.'),Ae(e,"is",a);break;case"innerText":case"textContent":break;case"popoverTarget":T1||a==null||typeof a!="object"||(T1=!0,console.error("The `popoverTarget` prop expects the ID of an Element as a string. Received %s instead.",a));default:!(2<n.length)||n[0]!=="o"&&n[0]!=="O"||n[1]!=="n"&&n[1]!=="N"?(n=nb(n),Ae(e,n,a)):Ui.hasOwnProperty(n)&&a!=null&&typeof a!="function"&&Aa(n,a)}}function Yp(e,t,n,a,o,l){switch(n){case"style":tb(e,a,l);break;case"dangerouslySetInnerHTML":if(a!=null){if(typeof a!="object"||!("__html"in a))throw Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://react.dev/link/dangerously-set-inner-html for more information.");if(n=a.__html,n!=null){if(o.children!=null)throw Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");e.innerHTML=n}}break;case"children":typeof a=="string"?qr(e,a):(typeof a=="number"||typeof a=="bigint")&&qr(e,""+a);break;case"onScroll":a!=null&&(typeof a!="function"&&Aa(n,a),Ee("scroll",e));break;case"onScrollEnd":a!=null&&(typeof a!="function"&&Aa(n,a),Ee("scrollend",e));break;case"onClick":a!=null&&(typeof a!="function"&&Aa(n,a),e.onclick=Zc);break;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"innerHTML":case"ref":break;case"innerText":case"textContent":break;default:if(Ui.hasOwnProperty(n))a!=null&&typeof a!="function"&&Aa(n,a);else e:{if(n[0]==="o"&&n[1]==="n"&&(o=n.endsWith("Capture"),t=n.slice(2,o?n.length-7:void 0),l=e[en]||null,l=l!=null?l[n]:null,typeof l=="function"&&e.removeEventListener(t,l,o),typeof a=="function")){typeof l!="function"&&l!==null&&(n in e?e[n]=null:e.hasAttribute(n)&&e.removeAttribute(n)),e.addEventListener(t,a,o);break e}n in e?e[n]=a:a===!0?e.setAttribute(n,""):Ae(e,n,a)}}}function Dt(e,t,n){switch(Gp(t,n),t){case"div":case"span":case"svg":case"path":case"a":case"g":case"p":case"li":break;case"img":Ee("error",e),Ee("load",e);var a=!1,o=!1,l;for(l in n)if(n.hasOwnProperty(l)){var c=n[l];if(c!=null)switch(l){case"src":a=!0;break;case"srcSet":o=!0;break;case"children":case"dangerouslySetInnerHTML":throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");default:Ve(e,t,l,c,n,null)}}o&&Ve(e,t,"srcSet",n.srcSet,n,null),a&&Ve(e,t,"src",n.src,n,null);return;case"input":J("input",n),Ee("invalid",e);var d=l=c=o=null,v=null,y=null;for(a in n)if(n.hasOwnProperty(a)){var R=n[a];if(R!=null)switch(a){case"name":o=R;break;case"type":c=R;break;case"checked":v=R;break;case"defaultChecked":y=R;break;case"value":l=R;break;case"defaultValue":d=R;break;case"children":case"dangerouslySetInnerHTML":if(R!=null)throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");break;default:Ve(e,t,a,R,n,null)}}Bg(e,n),Vg(e,l,d,v,y,c,o,!1),ic(e);return;case"select":J("select",n),Ee("invalid",e),a=c=l=null;for(o in n)if(n.hasOwnProperty(o)&&(d=n[o],d!=null))switch(o){case"value":l=d;break;case"defaultValue":c=d;break;case"multiple":a=d;default:Ve(e,t,o,d,n,null)}qg(e,n),t=l,n=c,e.multiple=!!a,t!=null?ml(e,!!a,t,!1):n!=null&&ml(e,!!a,n,!0);return;case"textarea":J("textarea",n),Ee("invalid",e),l=o=a=null;for(c in n)if(n.hasOwnProperty(c)&&(d=n[c],d!=null))switch(c){case"value":a=d;break;case"defaultValue":o=d;break;case"children":l=d;break;case"dangerouslySetInnerHTML":if(d!=null)throw Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");break;default:Ve(e,t,c,d,n,null)}Gg(e,n),Xg(e,a,o,l),ic(e);return;case"option":Pg(e,n);for(v in n)if(n.hasOwnProperty(v)&&(a=n[v],a!=null))switch(v){case"selected":e.selected=a&&typeof a!="function"&&typeof a!="symbol";break;default:Ve(e,t,v,a,n,null)}return;case"dialog":Ee("beforetoggle",e),Ee("toggle",e),Ee("cancel",e),Ee("close",e);break;case"iframe":case"object":Ee("load",e);break;case"video":case"audio":for(a=0;a<nu.length;a++)Ee(nu[a],e);break;case"image":Ee("error",e),Ee("load",e);break;case"details":Ee("toggle",e);break;case"embed":case"source":case"link":Ee("error",e),Ee("load",e);case"area":case"base":case"br":case"col":case"hr":case"keygen":case"meta":case"param":case"track":case"wbr":case"menuitem":for(y in n)if(n.hasOwnProperty(y)&&(a=n[y],a!=null))switch(y){case"children":case"dangerouslySetInnerHTML":throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");default:Ve(e,t,y,a,n,null)}return;default:if(Gr(t)){for(R in n)n.hasOwnProperty(R)&&(a=n[R],a!==void 0&&Yp(e,t,R,a,n,void 0));return}}for(d in n)n.hasOwnProperty(d)&&(a=n[d],a!=null&&Ve(e,t,d,a,n,null))}function ZR(e,t,n,a){switch(Gp(t,a),t){case"div":case"span":case"svg":case"path":case"a":case"g":case"p":case"li":break;case"input":var o=null,l=null,c=null,d=null,v=null,y=null,R=null;for(M in n){var C=n[M];if(n.hasOwnProperty(M)&&C!=null)switch(M){case"checked":break;case"value":break;case"defaultValue":v=C;default:a.hasOwnProperty(M)||Ve(e,t,M,null,a,C)}}for(var A in a){var M=a[A];if(C=n[A],a.hasOwnProperty(A)&&(M!=null||C!=null))switch(A){case"type":l=M;break;case"name":o=M;break;case"checked":y=M;break;case"defaultChecked":R=M;break;case"value":c=M;break;case"defaultValue":d=M;break;case"children":case"dangerouslySetInnerHTML":if(M!=null)throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");break;default:M!==C&&Ve(e,t,A,M,a,C)}}t=n.type==="checkbox"||n.type==="radio"?n.checked!=null:n.value!=null,a=a.type==="checkbox"||a.type==="radio"?a.checked!=null:a.value!=null,t||!a||_1||(console.error("A component is changing an uncontrolled input to be controlled. This is likely caused by the value changing from undefined to a defined value, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://react.dev/link/controlled-components"),_1=!0),!t||a||b1||(console.error("A component is changing a controlled input to be uncontrolled. This is likely caused by the value changing from a defined to undefined, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://react.dev/link/controlled-components"),b1=!0),sh(e,c,d,v,y,R,l,o);return;case"select":M=c=d=A=null;for(l in n)if(v=n[l],n.hasOwnProperty(l)&&v!=null)switch(l){case"value":break;case"multiple":M=v;default:a.hasOwnProperty(l)||Ve(e,t,l,null,a,v)}for(o in a)if(l=a[o],v=n[o],a.hasOwnProperty(o)&&(l!=null||v!=null))switch(o){case"value":A=l;break;case"defaultValue":d=l;break;case"multiple":c=l;default:l!==v&&Ve(e,t,o,l,a,v)}a=d,t=c,n=M,A!=null?ml(e,!!t,A,!1):!!n!=!!t&&(a!=null?ml(e,!!t,a,!0):ml(e,!!t,t?[]:"",!1));return;case"textarea":M=A=null;for(d in n)if(o=n[d],n.hasOwnProperty(d)&&o!=null&&!a.hasOwnProperty(d))switch(d){case"value":break;case"children":break;default:Ve(e,t,d,null,a,o)}for(c in a)if(o=a[c],l=n[c],a.hasOwnProperty(c)&&(o!=null||l!=null))switch(c){case"value":A=o;break;case"defaultValue":M=o;break;case"children":break;case"dangerouslySetInnerHTML":if(o!=null)throw Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");break;default:o!==l&&Ve(e,t,c,o,a,l)}Yg(e,A,M);return;case"option":for(var Q in n)if(A=n[Q],n.hasOwnProperty(Q)&&A!=null&&!a.hasOwnProperty(Q))switch(Q){case"selected":e.selected=!1;break;default:Ve(e,t,Q,null,a,A)}for(v in a)if(A=a[v],M=n[v],a.hasOwnProperty(v)&&A!==M&&(A!=null||M!=null))switch(v){case"selected":e.selected=A&&typeof A!="function"&&typeof A!="symbol";break;default:Ve(e,t,v,A,a,M)}return;case"img":case"link":case"area":case"base":case"br":case"col":case"embed":case"hr":case"keygen":case"meta":case"param":case"source":case"track":case"wbr":case"menuitem":for(var le in n)A=n[le],n.hasOwnProperty(le)&&A!=null&&!a.hasOwnProperty(le)&&Ve(e,t,le,null,a,A);for(y in a)if(A=a[y],M=n[y],a.hasOwnProperty(y)&&A!==M&&(A!=null||M!=null))switch(y){case"children":case"dangerouslySetInnerHTML":if(A!=null)throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");break;default:Ve(e,t,y,A,a,M)}return;default:if(Gr(t)){for(var Ge in n)A=n[Ge],n.hasOwnProperty(Ge)&&A!==void 0&&!a.hasOwnProperty(Ge)&&Yp(e,t,Ge,void 0,a,A);for(R in a)A=a[R],M=n[R],!a.hasOwnProperty(R)||A===M||A===void 0&&M===void 0||Yp(e,t,R,A,a,M);return}}for(var we in n)A=n[we],n.hasOwnProperty(we)&&A!=null&&!a.hasOwnProperty(we)&&Ve(e,t,we,null,a,A);for(C in a)A=a[C],M=n[C],!a.hasOwnProperty(C)||A===M||A==null&&M==null||Ve(e,t,C,A,a,M)}function k_(e){switch(e){case"class":return"className";case"for":return"htmlFor";default:return e}}function Xp(e){var t={};e=e.style;for(var n=0;n<e.length;n++){var a=e[n];t[a]=e.getPropertyValue(a)}return t}function H_(e,t,n){if(t!=null&&typeof t!="object")console.error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.");else{var a,o=a="",l;for(l in t)if(t.hasOwnProperty(l)){var c=t[l];c!=null&&typeof c!="boolean"&&c!==""&&(l.indexOf("--")===0?(Le(c,l),a+=o+l+":"+(""+c).trim()):typeof c!="number"||c===0||$S.has(l)?(Le(c,l),a+=o+l.replace(HS,"-$1").toLowerCase().replace(LS,"-ms-")+":"+(""+c).trim()):a+=o+l.replace(HS,"-$1").toLowerCase().replace(LS,"-ms-")+":"+c+"px",o=";")}a=a||null,t=e.getAttribute("style"),t!==a&&(a=ko(a),ko(t)!==a&&(n.style=Xp(e)))}}function Bn(e,t,n,a,o,l){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":return}else if(a!=null)switch(typeof a){case"function":case"symbol":case"boolean":break;default:if(F(a,t),e===""+a)return}St(t,e,a,l)}function L_(e,t,n,a,o,l){if(o.delete(n),e=e.getAttribute(n),e===null){switch(typeof a){case"function":case"symbol":return}if(!a)return}else switch(typeof a){case"function":case"symbol":break;default:if(a)return}St(t,e,a,l)}function Ip(e,t,n,a,o,l){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":return}else if(a!=null)switch(typeof a){case"function":case"symbol":break;default:if(F(a,n),e===""+a)return}St(t,e,a,l)}function B_(e,t,n,a,o,l){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":return;default:if(isNaN(a))return}else if(a!=null)switch(typeof a){case"function":case"symbol":case"boolean":break;default:if(!isNaN(a)&&(F(a,t),e===""+a))return}St(t,e,a,l)}function Qp(e,t,n,a,o,l){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":return}else if(a!=null)switch(typeof a){case"function":case"symbol":case"boolean":break;default:if(F(a,t),n=Yr(""+a),e===n)return}St(t,e,a,l)}function V_(e,t,n,a){for(var o={},l=new Set,c=e.attributes,d=0;d<c.length;d++)switch(c[d].name.toLowerCase()){case"value":break;case"checked":break;case"selected":break;default:l.add(c[d].name)}if(Gr(t)){for(var v in n)if(n.hasOwnProperty(v)){var y=n[v];if(y!=null){if(Ui.hasOwnProperty(v))typeof y!="function"&&Aa(v,y);else if(n.suppressHydrationWarning!==!0)switch(v){case"children":typeof y!="string"&&typeof y!="number"||St("children",e.textContent,y,o);continue;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":continue;case"dangerouslySetInnerHTML":c=e.innerHTML,y=y?y.__html:void 0,y!=null&&(y=U_(e,y),St(v,c,y,o));continue;case"style":l.delete(v),H_(e,y,o);continue;case"offsetParent":case"offsetTop":case"offsetLeft":case"offsetWidth":case"offsetHeight":case"isContentEditable":case"outerText":case"outerHTML":l.delete(v.toLowerCase()),console.error("Assignment to read-only property will result in a no-op: `%s`",v);continue;case"className":l.delete("class"),c=pe(e,"class",y),St("className",c,y,o);continue;default:a.context===fo&&t!=="svg"&&t!=="math"?l.delete(v.toLowerCase()):l.delete(v),c=pe(e,v,y),St(v,c,y,o)}}}}else for(y in n)if(n.hasOwnProperty(y)&&(v=n[y],v!=null)){if(Ui.hasOwnProperty(y))typeof v!="function"&&Aa(y,v);else if(n.suppressHydrationWarning!==!0)switch(y){case"children":typeof v!="string"&&typeof v!="number"||St("children",e.textContent,v,o);continue;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"value":case"checked":case"selected":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":continue;case"dangerouslySetInnerHTML":c=e.innerHTML,v=v?v.__html:void 0,v!=null&&(v=U_(e,v),c!==v&&(o[y]={__html:c}));continue;case"className":Bn(e,y,"class",v,l,o);continue;case"tabIndex":Bn(e,y,"tabindex",v,l,o);continue;case"style":l.delete(y),H_(e,v,o);continue;case"multiple":l.delete(y),St(y,e.multiple,v,o);continue;case"muted":l.delete(y),St(y,e.muted,v,o);continue;case"autoFocus":l.delete("autofocus"),St(y,e.autofocus,v,o);continue;case"data":if(t!=="object"){l.delete(y),c=e.getAttribute("data"),St(y,c,v,o);continue}case"src":case"href":if(!(v!==""||t==="a"&&y==="href"||t==="object"&&y==="data")){console.error(y==="src"?'An empty string ("") was passed to the %s attribute. This may cause the browser to download the whole page again over the network. To fix this, either do not render the element at all or pass null to %s instead of an empty string.':'An empty string ("") was passed to the %s attribute. To fix this, either do not render the element at all or pass null to %s instead of an empty string.',y,y);continue}Qp(e,y,y,v,l,o);continue;case"action":case"formAction":if(c=e.getAttribute(y),typeof v=="function"){l.delete(y.toLowerCase()),y==="formAction"?(l.delete("name"),l.delete("formenctype"),l.delete("formmethod"),l.delete("formtarget")):(l.delete("enctype"),l.delete("method"),l.delete("target"));continue}else if(c===lC){l.delete(y.toLowerCase()),St(y,"function",v,o);continue}Qp(e,y,y.toLowerCase(),v,l,o);continue;case"xlinkHref":Qp(e,y,"xlink:href",v,l,o);continue;case"contentEditable":Ip(e,y,"contenteditable",v,l,o);continue;case"spellCheck":Ip(e,y,"spellcheck",v,l,o);continue;case"draggable":case"autoReverse":case"externalResourcesRequired":case"focusable":case"preserveAlpha":Ip(e,y,y,v,l,o);continue;case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":L_(e,y,y.toLowerCase(),v,l,o);continue;case"capture":case"download":e:{d=e;var R=c=y,C=o;if(l.delete(R),d=d.getAttribute(R),d===null)switch(typeof v){case"undefined":case"function":case"symbol":break e;default:if(v===!1)break e}else if(v!=null)switch(typeof v){case"function":case"symbol":break;case"boolean":if(v===!0&&d==="")break e;break;default:if(F(v,c),d===""+v)break e}St(c,d,v,C)}continue;case"cols":case"rows":case"size":case"span":e:{if(d=e,R=c=y,C=o,l.delete(R),d=d.getAttribute(R),d===null)switch(typeof v){case"undefined":case"function":case"symbol":case"boolean":break e;default:if(isNaN(v)||1>v)break e}else if(v!=null)switch(typeof v){case"function":case"symbol":case"boolean":break;default:if(!(isNaN(v)||1>v)&&(F(v,c),d===""+v))break e}St(c,d,v,C)}continue;case"rowSpan":B_(e,y,"rowspan",v,l,o);continue;case"start":B_(e,y,y,v,l,o);continue;case"xHeight":Bn(e,y,"x-height",v,l,o);continue;case"xlinkActuate":Bn(e,y,"xlink:actuate",v,l,o);continue;case"xlinkArcrole":Bn(e,y,"xlink:arcrole",v,l,o);continue;case"xlinkRole":Bn(e,y,"xlink:role",v,l,o);continue;case"xlinkShow":Bn(e,y,"xlink:show",v,l,o);continue;case"xlinkTitle":Bn(e,y,"xlink:title",v,l,o);continue;case"xlinkType":Bn(e,y,"xlink:type",v,l,o);continue;case"xmlBase":Bn(e,y,"xml:base",v,l,o);continue;case"xmlLang":Bn(e,y,"xml:lang",v,l,o);continue;case"xmlSpace":Bn(e,y,"xml:space",v,l,o);continue;case"inert":v!==""||qf[y]||(qf[y]=!0,console.error("Received an empty string for a boolean attribute `%s`. This will treat the attribute as if it were false. Either pass `false` to silence this warning, or pass `true` if you used an empty string in earlier versions of React to indicate this attribute is true.",y)),L_(e,y,y,v,l,o);continue;default:if(!(2<y.length)||y[0]!=="o"&&y[0]!=="O"||y[1]!=="n"&&y[1]!=="N"){d=nb(y),c=!1,a.context===fo&&t!=="svg"&&t!=="math"?l.delete(d.toLowerCase()):(R=y.toLowerCase(),R=hf.hasOwnProperty(R)&&hf[R]||null,R!==null&&R!==y&&(c=!0,l.delete(R)),l.delete(d));e:if(R=e,C=d,d=v,ye(C))if(R.hasAttribute(C))R=R.getAttribute(C),F(d,C),d=R===""+d?d:R;else{switch(typeof d){case"function":case"symbol":break e;case"boolean":if(R=C.toLowerCase().slice(0,5),R!=="data-"&&R!=="aria-")break e}d=d===void 0?void 0:null}else d=void 0;c||St(y,d,v,o)}}}return 0<l.size&&n.suppressHydrationWarning!==!0&&QR(e,l,o),Object.keys(o).length===0?null:o}function KR(e,t){switch(e.length){case 0:return"";case 1:return e[0];case 2:return e[0]+" "+t+" "+e[1];default:return e.slice(0,-1).join(", ")+", "+t+" "+e[e.length-1]}}function Kc(e){return e.nodeType===9?e:e.ownerDocument}function P_(e){switch(e){case kl:return hr;case df:return Xf;default:return fo}}function $_(e,t){if(e===fo)switch(t){case"svg":return hr;case"math":return Xf;default:return fo}return e===hr&&t==="foreignObject"?fo:e}function Zp(e,t){return e==="textarea"||e==="noscript"||typeof t.children=="string"||typeof t.children=="number"||typeof t.children=="bigint"||typeof t.dangerouslySetInnerHTML=="object"&&t.dangerouslySetInnerHTML!==null&&t.dangerouslySetInnerHTML.__html!=null}function JR(){var e=window.event;return e&&e.type==="popstate"?e===wv?!1:(wv=e,!0):(wv=null,!1)}function WR(e){setTimeout(function(){throw e})}function FR(e,t,n){switch(t){case"button":case"input":case"select":case"textarea":n.autoFocus&&e.focus();break;case"img":n.src?e.src=n.src:n.srcSet&&(e.srcset=n.srcSet)}}function ez(e,t,n,a){ZR(e,t,n,a),e[en]=a}function q_(e){qr(e,"")}function tz(e,t,n){e.nodeValue=n}function Ho(e){return e==="head"}function nz(e,t){e.removeChild(t)}function az(e,t){(e.nodeType===9?e.body:e.nodeName==="HTML"?e.ownerDocument.body:e).removeChild(t)}function G_(e,t){var n=t,a=0,o=0;do{var l=n.nextSibling;if(e.removeChild(n),l&&l.nodeType===8)if(n=l.data,n===Yf){if(0<a&&8>a){n=a;var c=e.ownerDocument;if(n&sC&&vs(c.documentElement),n&uC&&vs(c.body),n&cC)for(n=c.head,vs(n),c=n.firstChild;c;){var d=c.nextSibling,v=c.nodeName;c[Os]||v==="SCRIPT"||v==="STYLE"||v==="LINK"&&c.rel.toLowerCase()==="stylesheet"||n.removeChild(c),c=d}}if(o===0){e.removeChild(l),_s(t);return}o--}else n===Gf||n===co||n===au?o++:a=n.charCodeAt(0)-48;else a=0;n=l}while(n);_s(t)}function oz(e){e=e.style,typeof e.setProperty=="function"?e.setProperty("display","none","important"):e.display="none"}function iz(e){e.nodeValue=""}function lz(e,t){t=t[fC],t=t!=null&&t.hasOwnProperty("display")?t.display:null,e.style.display=t==null||typeof t=="boolean"?"":(""+t).trim()}function rz(e,t){e.nodeValue=t}function Kp(e){var t=e.firstChild;for(t&&t.nodeType===10&&(t=t.nextSibling);t;){var n=t;switch(t=t.nextSibling,n.nodeName){case"HTML":case"HEAD":case"BODY":Kp(n),xo(n);continue;case"SCRIPT":case"STYLE":continue;case"LINK":if(n.rel.toLowerCase()==="stylesheet")continue}e.removeChild(n)}}function sz(e,t,n,a){for(;e.nodeType===1;){var o=n;if(e.nodeName.toLowerCase()!==t.toLowerCase()){if(!a&&(e.nodeName!=="INPUT"||e.type!=="hidden"))break}else if(a){if(!e[Os])switch(t){case"meta":if(!e.hasAttribute("itemprop"))break;return e;case"link":if(l=e.getAttribute("rel"),l==="stylesheet"&&e.hasAttribute("data-precedence"))break;if(l!==o.rel||e.getAttribute("href")!==(o.href==null||o.href===""?null:o.href)||e.getAttribute("crossorigin")!==(o.crossOrigin==null?null:o.crossOrigin)||e.getAttribute("title")!==(o.title==null?null:o.title))break;return e;case"style":if(e.hasAttribute("data-precedence"))break;return e;case"script":if(l=e.getAttribute("src"),(l!==(o.src==null?null:o.src)||e.getAttribute("type")!==(o.type==null?null:o.type)||e.getAttribute("crossorigin")!==(o.crossOrigin==null?null:o.crossOrigin))&&l&&e.hasAttribute("async")&&!e.hasAttribute("itemprop"))break;return e;default:return e}}else if(t==="input"&&e.type==="hidden"){F(o.name,"name");var l=o.name==null?null:""+o.name;if(o.type==="hidden"&&e.getAttribute("name")===l)return e}else return e;if(e=Vn(e.nextSibling),e===null)break}return null}function uz(e,t,n){if(t==="")return null;for(;e.nodeType!==3;)if((e.nodeType!==1||e.nodeName!=="INPUT"||e.type!=="hidden")&&!n||(e=Vn(e.nextSibling),e===null))return null;return e}function Jp(e){return e.data===au||e.data===co&&e.ownerDocument.readyState===E1}function cz(e,t){var n=e.ownerDocument;if(e.data!==co||n.readyState===E1)t();else{var a=function(){t(),n.removeEventListener("DOMContentLoaded",a)};n.addEventListener("DOMContentLoaded",a),e._reactRetry=a}}function Vn(e){for(;e!=null;e=e.nextSibling){var t=e.nodeType;if(t===1||t===3)break;if(t===8){if(t=e.data,t===Gf||t===au||t===co||t===Tv||t===O1)break;if(t===Yf)return null}}return e}function Y_(e){if(e.nodeType===1){for(var t=e.nodeName.toLowerCase(),n={},a=e.attributes,o=0;o<a.length;o++){var l=a[o];n[k_(l.name)]=l.name.toLowerCase()==="style"?Xp(e):l.value}return{type:t,props:n}}return e.nodeType===8?{type:"Suspense",props:{}}:e.nodeValue}function X_(e,t,n){return n===null||n[rC]!==!0?(e.nodeValue===t?e=null:(t=ko(t),e=ko(e.nodeValue)===t?null:e.nodeValue),e):null}function I_(e){e=e.nextSibling;for(var t=0;e;){if(e.nodeType===8){var n=e.data;if(n===Yf){if(t===0)return Vn(e.nextSibling);t--}else n!==Gf&&n!==au&&n!==co||t++}e=e.nextSibling}return null}function Q_(e){e=e.previousSibling;for(var t=0;e;){if(e.nodeType===8){var n=e.data;if(n===Gf||n===au||n===co){if(t===0)return e;t--}else n===Yf&&t++}e=e.previousSibling}return null}function fz(e){_s(e)}function dz(e){_s(e)}function Z_(e,t,n,a,o){switch(o&&ph(e,a.ancestorInfo),t=Kc(n),e){case"html":if(e=t.documentElement,!e)throw Error("React expected an <html> element (document.documentElement) to exist in the Document but one was not found. React never removes the documentElement for any Document it renders into so the cause is likely in some other script running on this page.");return e;case"head":if(e=t.head,!e)throw Error("React expected a <head> element (document.head) to exist in the Document but one was not found. React never removes the head for any Document it renders into so the cause is likely in some other script running on this page.");return e;case"body":if(e=t.body,!e)throw Error("React expected a <body> element (document.body) to exist in the Document but one was not found. React never removes the body for any Document it renders into so the cause is likely in some other script running on this page.");return e;default:throw Error("resolveSingletonInstance was called with an element type that is not supported. This is a bug in React.")}}function hz(e,t,n,a){if(!n[$o]&&la(n)){var o=n.tagName.toLowerCase();console.error("You are mounting a new %s component when a previous one has not first unmounted. It is an error to render more than one %s component at a time and attributes and children of these components will likely fail in unpredictable ways. Please only render a single instance of <%s> and if you need to mount a new one, ensure any previous ones have unmounted first.",o,o,o)}switch(e){case"html":case"head":case"body":break;default:console.error("acquireSingletonInstance was called with an element type that is not supported. This is a bug in React.")}for(o=n.attributes;o.length;)n.removeAttributeNode(o[0]);Dt(n,e,t),n[Lt]=a,n[en]=t}function vs(e){for(var t=e.attributes;t.length;)e.removeAttributeNode(t[0]);xo(e)}function Jc(e){return typeof e.getRootNode=="function"?e.getRootNode():e.nodeType===9?e:e.ownerDocument}function K_(e,t,n){var a=pr;if(a&&typeof t=="string"&&t){var o=Ln(t);o='link[rel="'+e+'"][href="'+o+'"]',typeof n=="string"&&(o+='[crossorigin="'+n+'"]'),D1.has(o)||(D1.add(o),e={rel:e,crossOrigin:n,href:t},a.querySelector(o)===null&&(t=a.createElement("link"),Dt(t,"link",e),w(t),a.head.appendChild(t)))}}function J_(e,t,n,a){var o=(o=Vo.current)?Jc(o):null;if(!o)throw Error('"resourceRoot" was expected to exist. This is a bug in React.');switch(e){case"meta":case"title":return null;case"style":return typeof n.precedence=="string"&&typeof n.href=="string"?(n=zl(n.href),t=m(o).hoistableStyles,a=t.get(n),a||(a={type:"style",instance:null,count:0,state:null},t.set(n,a)),a):{type:"void",instance:null,count:0,state:null};case"link":if(n.rel==="stylesheet"&&typeof n.href=="string"&&typeof n.precedence=="string"){e=zl(n.href);var l=m(o).hoistableStyles,c=l.get(e);if(!c&&(o=o.ownerDocument||o,c={type:"stylesheet",instance:null,count:0,state:{loading:Wi,preload:null}},l.set(e,c),(l=o.querySelector(ys(e)))&&!l._p&&(c.instance=l,c.state.loading=ou|Jn),!Wn.has(e))){var d={rel:"preload",as:"style",href:n.href,crossOrigin:n.crossOrigin,integrity:n.integrity,media:n.media,hrefLang:n.hrefLang,referrerPolicy:n.referrerPolicy};Wn.set(e,d),l||pz(o,e,d,c.state)}if(t&&a===null)throw n=`

  - `+Wc(t)+`
  + `+Wc(n),Error("Expected <link> not to update to be updated to a stylesheet with precedence. Check the `rel`, `href`, and `precedence` props of this component. Alternatively, check whether two different <link> components render in the same slot or share the same key."+n);return c}if(t&&a!==null)throw n=`

  - `+Wc(t)+`
  + `+Wc(n),Error("Expected stylesheet with precedence to not be updated to a different kind of <link>. Check the `rel`, `href`, and `precedence` props of this component. Alternatively, check whether two different <link> components render in the same slot or share the same key."+n);return null;case"script":return t=n.async,n=n.src,typeof n=="string"&&t&&typeof t!="function"&&typeof t!="symbol"?(n=Dl(n),t=m(o).hoistableScripts,a=t.get(n),a||(a={type:"script",instance:null,count:0,state:null},t.set(n,a)),a):{type:"void",instance:null,count:0,state:null};default:throw Error('getResource encountered a type it did not expect: "'+e+'". this is a bug in React.')}}function Wc(e){var t=0,n="<link";return typeof e.rel=="string"?(t++,n+=' rel="'+e.rel+'"'):eo.call(e,"rel")&&(t++,n+=' rel="'+(e.rel===null?"null":"invalid type "+typeof e.rel)+'"'),typeof e.href=="string"?(t++,n+=' href="'+e.href+'"'):eo.call(e,"href")&&(t++,n+=' href="'+(e.href===null?"null":"invalid type "+typeof e.href)+'"'),typeof e.precedence=="string"?(t++,n+=' precedence="'+e.precedence+'"'):eo.call(e,"precedence")&&(t++,n+=" precedence={"+(e.precedence===null?"null":"invalid type "+typeof e.precedence)+"}"),Object.getOwnPropertyNames(e).length>t&&(n+=" ..."),n+" />"}function zl(e){return'href="'+Ln(e)+'"'}function ys(e){return'link[rel="stylesheet"]['+e+"]"}function W_(e){return ge({},e,{"data-precedence":e.precedence,precedence:null})}function pz(e,t,n,a){e.querySelector('link[rel="preload"][as="style"]['+t+"]")?a.loading=ou:(t=e.createElement("link"),a.preload=t,t.addEventListener("load",function(){return a.loading|=ou}),t.addEventListener("error",function(){return a.loading|=R1}),Dt(t,"link",n),w(t),e.head.appendChild(t))}function Dl(e){return'[src="'+Ln(e)+'"]'}function gs(e){return"script[async]"+e}function F_(e,t,n){if(t.count++,t.instance===null)switch(t.type){case"style":var a=e.querySelector('style[data-href~="'+Ln(n.href)+'"]');if(a)return t.instance=a,w(a),a;var o=ge({},n,{"data-href":n.href,"data-precedence":n.precedence,href:null,precedence:null});return a=(e.ownerDocument||e).createElement("style"),w(a),Dt(a,"style",o),Fc(a,n.precedence,e),t.instance=a;case"stylesheet":o=zl(n.href);var l=e.querySelector(ys(o));if(l)return t.state.loading|=Jn,t.instance=l,w(l),l;a=W_(n),(o=Wn.get(o))&&Wp(a,o),l=(e.ownerDocument||e).createElement("link"),w(l);var c=l;return c._p=new Promise(function(d,v){c.onload=d,c.onerror=v}),Dt(l,"link",a),t.state.loading|=Jn,Fc(l,n.precedence,e),t.instance=l;case"script":return l=Dl(n.src),(o=e.querySelector(gs(l)))?(t.instance=o,w(o),o):(a=n,(o=Wn.get(l))&&(a=ge({},n),Fp(a,o)),e=e.ownerDocument||e,o=e.createElement("script"),w(o),Dt(o,"link",a),e.head.appendChild(o),t.instance=o);case"void":return null;default:throw Error('acquireResource encountered a resource type it did not expect: "'+t.type+'". this is a bug in React.')}else t.type==="stylesheet"&&(t.state.loading&Jn)===Wi&&(a=t.instance,t.state.loading|=Jn,Fc(a,n.precedence,e));return t.instance}function Fc(e,t,n){for(var a=n.querySelectorAll('link[rel="stylesheet"][data-precedence],style[data-precedence]'),o=a.length?a[a.length-1]:null,l=o,c=0;c<a.length;c++){var d=a[c];if(d.dataset.precedence===t)l=d;else if(l!==o)break}l?l.parentNode.insertBefore(e,l.nextSibling):(t=n.nodeType===9?n.head:n,t.insertBefore(e,t.firstChild))}function Wp(e,t){e.crossOrigin==null&&(e.crossOrigin=t.crossOrigin),e.referrerPolicy==null&&(e.referrerPolicy=t.referrerPolicy),e.title==null&&(e.title=t.title)}function Fp(e,t){e.crossOrigin==null&&(e.crossOrigin=t.crossOrigin),e.referrerPolicy==null&&(e.referrerPolicy=t.referrerPolicy),e.integrity==null&&(e.integrity=t.integrity)}function eS(e,t,n){if(If===null){var a=new Map,o=If=new Map;o.set(n,a)}else o=If,a=o.get(n),a||(a=new Map,o.set(n,a));if(a.has(e))return a;for(a.set(e,null),n=n.getElementsByTagName(e),o=0;o<n.length;o++){var l=n[o];if(!(l[Os]||l[Lt]||e==="link"&&l.getAttribute("rel")==="stylesheet")&&l.namespaceURI!==kl){var c=l.getAttribute(t)||"";c=e+c;var d=a.get(c);d?d.push(l):a.set(c,[l])}}return a}function tS(e,t,n){e=e.ownerDocument||e,e.head.insertBefore(n,t==="title"?e.querySelector("head > title"):null)}function mz(e,t,n){var a=!n.ancestorInfo.containerTagInScope;if(n.context===hr||t.itemProp!=null)return!a||t.itemProp==null||e!=="meta"&&e!=="title"&&e!=="style"&&e!=="link"&&e!=="script"||console.error("Cannot render a <%s> outside the main document if it has an `itemProp` prop. `itemProp` suggests the tag belongs to an `itemScope` which can appear anywhere in the DOM. If you were intending for React to hoist this <%s> remove the `itemProp` prop. Otherwise, try moving this tag into the <head> or <body> of the Document.",e,e),!1;switch(e){case"meta":case"title":return!0;case"style":if(typeof t.precedence!="string"||typeof t.href!="string"||t.href===""){a&&console.error('Cannot render a <style> outside the main document without knowing its precedence and a unique href key. React can hoist and deduplicate <style> tags if you provide a `precedence` prop along with an `href` prop that does not conflict with the `href` values used in any other hoisted <style> or <link rel="stylesheet" ...> tags.  Note that hoisting <style> tags is considered an advanced feature that most will not use directly. Consider moving the <style> tag to the <head> or consider adding a `precedence="default"` and `href="some unique resource identifier"`.');break}return!0;case"link":if(typeof t.rel!="string"||typeof t.href!="string"||t.href===""||t.onLoad||t.onError){if(t.rel==="stylesheet"&&typeof t.precedence=="string"){e=t.href;var o=t.onError,l=t.disabled;n=[],t.onLoad&&n.push("`onLoad`"),o&&n.push("`onError`"),l!=null&&n.push("`disabled`"),o=KR(n,"and"),o+=n.length===1?" prop":" props",l=n.length===1?"an "+o:"the "+o,n.length&&console.error('React encountered a <link rel="stylesheet" href="%s" ... /> with a `precedence` prop that also included %s. The presence of loading and error handlers indicates an intent to manage the stylesheet loading state from your from your Component code and React will not hoist or deduplicate this stylesheet. If your intent was to have React hoist and deduplciate this stylesheet using the `precedence` prop remove the %s, otherwise remove the `precedence` prop.',e,l,o)}a&&(typeof t.rel!="string"||typeof t.href!="string"||t.href===""?console.error("Cannot render a <link> outside the main document without a `rel` and `href` prop. Try adding a `rel` and/or `href` prop to this <link> or moving the link into the <head> tag"):(t.onError||t.onLoad)&&console.error("Cannot render a <link> with onLoad or onError listeners outside the main document. Try removing onLoad={...} and onError={...} or moving it into the root <head> tag or somewhere in the <body>."));break}switch(t.rel){case"stylesheet":return e=t.precedence,t=t.disabled,typeof e!="string"&&a&&console.error('Cannot render a <link rel="stylesheet" /> outside the main document without knowing its precedence. Consider adding precedence="default" or moving it into the root <head> tag.'),typeof e=="string"&&t==null;default:return!0}case"script":if(e=t.async&&typeof t.async!="function"&&typeof t.async!="symbol",!e||t.onLoad||t.onError||!t.src||typeof t.src!="string"){a&&(e?t.onLoad||t.onError?console.error("Cannot render a <script> with onLoad or onError listeners outside the main document. Try removing onLoad={...} and onError={...} or moving it into the root <head> tag or somewhere in the <body>."):console.error("Cannot render a <script> outside the main document without `async={true}` and a non-empty `src` prop. Ensure there is a valid `src` and either make the script async or move it into the root <head> tag or somewhere in the <body>."):console.error('Cannot render a sync or defer <script> outside the main document without knowing its order. Try adding async="" or moving it into the root <head> tag.'));break}return!0;case"noscript":case"template":a&&console.error("Cannot render <%s> outside the main document. Try moving it into the root <head> tag.",e)}return!1}function nS(e){return!(e.type==="stylesheet"&&(e.state.loading&z1)===Wi)}function vz(){}function yz(e,t,n){if(iu===null)throw Error("Internal React Error: suspendedState null when it was expected to exists. Please report this as a React bug.");var a=iu;if(t.type==="stylesheet"&&(typeof n.media!="string"||matchMedia(n.media).matches!==!1)&&(t.state.loading&Jn)===Wi){if(t.instance===null){var o=zl(n.href),l=e.querySelector(ys(o));if(l){e=l._p,e!==null&&typeof e=="object"&&typeof e.then=="function"&&(a.count++,a=ef.bind(a),e.then(a,a)),t.state.loading|=Jn,t.instance=l,w(l);return}l=e.ownerDocument||e,n=W_(n),(o=Wn.get(o))&&Wp(n,o),l=l.createElement("link"),w(l);var c=l;c._p=new Promise(function(d,v){c.onload=d,c.onerror=v}),Dt(l,"link",n),t.instance=l}a.stylesheets===null&&(a.stylesheets=new Map),a.stylesheets.set(t,e),(e=t.state.preload)&&(t.state.loading&z1)===Wi&&(a.count++,t=ef.bind(a),e.addEventListener("load",t),e.addEventListener("error",t))}}function gz(){if(iu===null)throw Error("Internal React Error: suspendedState null when it was expected to exists. Please report this as a React bug.");var e=iu;return e.stylesheets&&e.count===0&&em(e,e.stylesheets),0<e.count?function(t){var n=setTimeout(function(){if(e.stylesheets&&em(e,e.stylesheets),e.unsuspend){var a=e.unsuspend;e.unsuspend=null,a()}},6e4);return e.unsuspend=t,function(){e.unsuspend=null,clearTimeout(n)}}:null}function ef(){if(this.count--,this.count===0){if(this.stylesheets)em(this,this.stylesheets);else if(this.unsuspend){var e=this.unsuspend;this.unsuspend=null,e()}}}function em(e,t){e.stylesheets=null,e.unsuspend!==null&&(e.count++,Qf=new Map,t.forEach(bz,e),Qf=null,ef.call(e))}function bz(e,t){if(!(t.state.loading&Jn)){var n=Qf.get(e);if(n)var a=n.get(xv);else{n=new Map,Qf.set(e,n);for(var o=e.querySelectorAll("link[data-precedence],style[data-precedence]"),l=0;l<o.length;l++){var c=o[l];(c.nodeName==="LINK"||c.getAttribute("media")!=="not all")&&(n.set(c.dataset.precedence,c),a=c)}a&&n.set(xv,a)}o=t.instance,c=o.getAttribute("data-precedence"),l=n.get(c)||a,l===a&&n.set(xv,o),n.set(c,o),this.count++,a=ef.bind(this),o.addEventListener("load",a),o.addEventListener("error",a),l?l.parentNode.insertBefore(o,l.nextSibling):(e=e.nodeType===9?e.head:e,e.insertBefore(o,e.firstChild)),t.state.loading|=Jn}}function _z(e,t,n,a,o,l,c,d){for(this.tag=1,this.containerInfo=e,this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=Ji,this.callbackNode=this.next=this.pendingContext=this.context=this.cancelPendingCommit=null,this.callbackPriority=0,this.expirationTimes=dl(-1),this.entangledLanes=this.shellSuspendCounter=this.errorRecoveryDisabledLanes=this.expiredLanes=this.warmLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=dl(0),this.hiddenUpdates=dl(null),this.identifierPrefix=a,this.onUncaughtError=o,this.onCaughtError=l,this.onRecoverableError=c,this.pooledCache=null,this.pooledCacheLanes=0,this.formState=d,this.incompleteTransitions=new Map,this.passiveEffectDuration=this.effectDuration=-0,this.memoizedUpdaters=new Set,e=this.pendingUpdatersLaneMap=[],t=0;31>t;t++)e.push(new Set);this._debugRootType=n?"hydrateRoot()":"createRoot()"}function aS(e,t,n,a,o,l,c,d,v,y,R,C){return e=new _z(e,t,n,c,d,v,y,C),t=HD,l===!0&&(t|=Bt|ca),ua&&(t|=Mt),l=O(3,null,null,t),e.current=l,l.stateNode=e,t=Dh(),Ri(t),e.pooledCache=t,Ri(t),l.memoizedState={element:a,isDehydrated:n,cache:t},Uh(l),e}function oS(e){return e?(e=Go,e):Go}function tm(e,t,n,a,o,l){if(Ht&&typeof Ht.onScheduleFiberRoot=="function")try{Ht.onScheduleFiberRoot(Ul,a,n)}catch(c){za||(za=!0,console.error("React instrumentation encountered an error: %s",c))}Y!==null&&typeof Y.markRenderScheduled=="function"&&Y.markRenderScheduled(t),o=oS(o),a.context===null?a.context=o:a.pendingContext=o,Ca&&Sn!==null&&!U1&&(U1=!0,console.error(`Render methods should be a pure function of props and state; triggering nested component updates from render is not allowed. If necessary, trigger nested updates in componentDidUpdate.

Check the render method of %s.`,ee(Sn)||"Unknown")),a=Do(t),a.payload={element:n},l=l===void 0?null:l,l!==null&&(typeof l!="function"&&console.error("Expected the last optional `callback` argument to be a function. Instead received: %s.",l),a.callback=l),n=Co(e,a,t),n!==null&&(it(n,e,t),es(n,e,t))}function iS(e,t){if(e=e.memoizedState,e!==null&&e.dehydrated!==null){var n=e.retryLane;e.retryLane=n!==0&&n<t?n:t}}function nm(e,t){iS(e,t),(e=e.alternate)&&iS(e,t)}function lS(e){if(e.tag===13){var t=Jt(e,67108864);t!==null&&it(t,e,67108864),nm(e,67108864)}}function Sz(){return Sn}function Tz(){for(var e=new Map,t=1,n=0;31>n;n++){var a=Nr(t);e.set(t,a),t*=2}return e}function Oz(e,t,n,a){var o=D.T;D.T=null;var l=Me.p;try{Me.p=Pn,am(e,t,n,a)}finally{Me.p=l,D.T=o}}function Ez(e,t,n,a){var o=D.T;D.T=null;var l=Me.p;try{Me.p=Da,am(e,t,n,a)}finally{Me.p=l,D.T=o}}function am(e,t,n,a){if(Kf){var o=om(a);if(o===null)qp(e,t,a,Jf,n),sS(e,a);else if(wz(o,e,t,n,a))a.stopPropagation();else if(sS(e,a),t&4&&-1<mC.indexOf(e)){for(;o!==null;){var l=la(o);if(l!==null)switch(l.tag){case 3:if(l=l.stateNode,l.current.memoizedState.isDehydrated){var c=ct(l.pendingLanes);if(c!==0){var d=l;for(d.pendingLanes|=2,d.entangledLanes|=2;c;){var v=1<<31-Pt(c);d.entanglements[1]|=v,c&=~v}wa(l),(je&(Xt|pa))===Tn&&(Uf=Ra()+d1,ps(0))}}break;case 13:d=Jt(l,2),d!==null&&it(d,l,2),wl(),nm(l,2)}if(l=om(a),l===null&&qp(e,t,a,Jf,n),l===o)break;o=l}o!==null&&a.stopPropagation()}else qp(e,t,a,null,n)}}function om(e){return e=mh(e),im(e)}function im(e){if(Jf=null,e=ia(e),e!==null){var t=ie(e);if(t===null)e=null;else{var n=t.tag;if(n===13){if(e=Qe(t),e!==null)return e;e=null}else if(n===3){if(t.stateNode.current.memoizedState.isDehydrated)return t.tag===3?t.stateNode.containerInfo:null;e=null}else t!==e&&(e=null)}}return Jf=e,null}function rS(e){switch(e){case"beforetoggle":case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"toggle":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return Pn;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return Da;case"message":switch(kz()){case vm:return Pn;case ym:return Da;case jl:case Hz:return to;case gm:return cf;default:return to}default:return to}}function sS(e,t){switch(e){case"focusin":case"focusout":ei=null;break;case"dragenter":case"dragleave":ti=null;break;case"mouseover":case"mouseout":ni=null;break;case"pointerover":case"pointerout":ru.delete(t.pointerId);break;case"gotpointercapture":case"lostpointercapture":su.delete(t.pointerId)}}function bs(e,t,n,a,o,l){return e===null||e.nativeEvent!==l?(e={blockedOn:t,domEventName:n,eventSystemFlags:a,nativeEvent:l,targetContainers:[o]},t!==null&&(t=la(t),t!==null&&lS(t)),e):(e.eventSystemFlags|=a,t=e.targetContainers,o!==null&&t.indexOf(o)===-1&&t.push(o),e)}function wz(e,t,n,a,o){switch(t){case"focusin":return ei=bs(ei,e,t,n,a,o),!0;case"dragenter":return ti=bs(ti,e,t,n,a,o),!0;case"mouseover":return ni=bs(ni,e,t,n,a,o),!0;case"pointerover":var l=o.pointerId;return ru.set(l,bs(ru.get(l)||null,e,t,n,a,o)),!0;case"gotpointercapture":return l=o.pointerId,su.set(l,bs(su.get(l)||null,e,t,n,a,o)),!0}return!1}function uS(e){var t=ia(e.target);if(t!==null){var n=ie(t);if(n!==null){if(t=n.tag,t===13){if(t=Qe(n),t!==null){e.blockedOn=t,pl(e.priority,function(){if(n.tag===13){var a=bn(n);a=Hr(a);var o=Jt(n,a);o!==null&&it(o,n,a),nm(n,a)}});return}}else if(t===3&&n.stateNode.current.memoizedState.isDehydrated){e.blockedOn=n.tag===3?n.stateNode.containerInfo:null;return}}}e.blockedOn=null}function tf(e){if(e.blockedOn!==null)return!1;for(var t=e.targetContainers;0<t.length;){var n=om(e.nativeEvent);if(n===null){n=e.nativeEvent;var a=new n.constructor(n.type,n),o=a;ws!==null&&console.error("Expected currently replaying event to be null. This error is likely caused by a bug in React. Please file an issue."),ws=o,n.target.dispatchEvent(a),ws===null&&console.error("Expected currently replaying event to not be null. This error is likely caused by a bug in React. Please file an issue."),ws=null}else return t=la(n),t!==null&&lS(t),e.blockedOn=n,!1;t.shift()}return!0}function cS(e,t,n){tf(e)&&n.delete(t)}function Az(){Rv=!1,ei!==null&&tf(ei)&&(ei=null),ti!==null&&tf(ti)&&(ti=null),ni!==null&&tf(ni)&&(ni=null),ru.forEach(cS),su.forEach(cS)}function nf(e,t){e.blockedOn===t&&(e.blockedOn=null,Rv||(Rv=!0,ft.unstable_scheduleCallback(ft.unstable_NormalPriority,Az)))}function fS(e){Wf!==e&&(Wf=e,ft.unstable_scheduleCallback(ft.unstable_NormalPriority,function(){Wf===e&&(Wf=null);for(var t=0;t<e.length;t+=3){var n=e[t],a=e[t+1],o=e[t+2];if(typeof a!="function"){if(im(a||n)===null)continue;break}var l=la(n);l!==null&&(e.splice(t,3),t-=3,n={pending:!0,data:o,method:n.method,action:a},Object.freeze(n),ip(l,n,a,o))}}))}function _s(e){function t(v){return nf(v,e)}ei!==null&&nf(ei,e),ti!==null&&nf(ti,e),ni!==null&&nf(ni,e),ru.forEach(t),su.forEach(t);for(var n=0;n<ai.length;n++){var a=ai[n];a.blockedOn===e&&(a.blockedOn=null)}for(;0<ai.length&&(n=ai[0],n.blockedOn===null);)uS(n),n.blockedOn===null&&ai.shift();if(n=(e.ownerDocument||e).$$reactFormReplay,n!=null)for(a=0;a<n.length;a+=3){var o=n[a],l=n[a+1],c=o[en]||null;if(typeof l=="function")c||fS(n);else if(c){var d=null;if(l&&l.hasAttribute("formAction")){if(o=l,c=l[en]||null)d=c.formAction;else if(im(o)!==null)continue}else d=c.action;typeof d=="function"?n[a+1]=d:(n.splice(a,3),a-=3),fS(n)}}}function lm(e){this._internalRoot=e}function af(e){this._internalRoot=e}function dS(e){e[$o]&&(e._reactRootContainer?console.error("You are calling ReactDOMClient.createRoot() on a container that was previously passed to ReactDOM.render(). This is not supported."):console.error("You are calling ReactDOMClient.createRoot() on a container that has already been passed to createRoot() before. Instead, call root.render() on the existing root instead if you want to update it."))}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var ft=Z1(),rm=gr(),xz=Hv(),ge=Object.assign,Rz=Symbol.for("react.element"),Lo=Symbol.for("react.transitional.element"),Cl=Symbol.for("react.portal"),Ml=Symbol.for("react.fragment"),of=Symbol.for("react.strict_mode"),sm=Symbol.for("react.profiler"),zz=Symbol.for("react.provider"),um=Symbol.for("react.consumer"),xa=Symbol.for("react.context"),Ss=Symbol.for("react.forward_ref"),cm=Symbol.for("react.suspense"),fm=Symbol.for("react.suspense_list"),lf=Symbol.for("react.memo"),_n=Symbol.for("react.lazy"),dm=Symbol.for("react.activity"),Dz=Symbol.for("react.memo_cache_sentinel"),hS=Symbol.iterator,Cz=Symbol.for("react.client.reference"),Ct=Array.isArray,D=rm.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,Me=xz.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,Mz=Object.freeze({pending:!1,data:null,method:null,action:null}),hm=[],pm=[],Fa=-1,Bo=at(null),Ts=at(null),Vo=at(null),rf=at(null),eo=Object.prototype.hasOwnProperty,mm=ft.unstable_scheduleCallback,jz=ft.unstable_cancelCallback,Uz=ft.unstable_shouldYield,Nz=ft.unstable_requestPaint,Ra=ft.unstable_now,kz=ft.unstable_getCurrentPriorityLevel,vm=ft.unstable_ImmediatePriority,ym=ft.unstable_UserBlockingPriority,jl=ft.unstable_NormalPriority,Hz=ft.unstable_LowPriority,gm=ft.unstable_IdlePriority,Lz=ft.log,Bz=ft.unstable_setDisableYieldValue,Ul=null,Ht=null,Y=null,za=!1,ua=typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u",Pt=Math.clz32?Math.clz32:tc,Vz=Math.log,Pz=Math.LN2,sf=256,uf=4194304,Pn=2,Da=8,to=32,cf=268435456,Po=Math.random().toString(36).slice(2),Lt="__reactFiber$"+Po,en="__reactProps$"+Po,$o="__reactContainer$"+Po,bm="__reactEvents$"+Po,$z="__reactListeners$"+Po,qz="__reactHandles$"+Po,pS="__reactResources$"+Po,Os="__reactMarker$"+Po,mS=new Set,Ui={},_m={},Gz={button:!0,checkbox:!0,image:!0,hidden:!0,radio:!0,reset:!0,submit:!0},Yz=RegExp("^[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),vS={},yS={},Es=0,gS,bS,_S,SS,TS,OS,ES;qa.__reactDisabledLog=!0;var Sm,wS,Tm=!1,Om=new(typeof WeakMap=="function"?WeakMap:Map),Sn=null,Ca=!1,Xz=/[\n"\\]/g,AS=!1,xS=!1,RS=!1,zS=!1,DS=!1,CS=!1,MS=["value","defaultValue"],jS=!1,US=/["'&<>\n\t]|^\s|\s$/,Iz="address applet area article aside base basefont bgsound blockquote body br button caption center col colgroup dd details dir div dl dt embed fieldset figcaption figure footer form frame frameset h1 h2 h3 h4 h5 h6 head header hgroup hr html iframe img input isindex li link listing main marquee menu menuitem meta nav noembed noframes noscript object ol p param plaintext pre script section select source style summary table tbody td template textarea tfoot th thead title tr track ul wbr xmp".split(" "),NS="applet caption html table td th marquee object template foreignObject desc title".split(" "),Qz=NS.concat(["button"]),Zz="dd dt li option optgroup p rp rt".split(" "),kS={current:null,formTag:null,aTagInScope:null,buttonTagInScope:null,nobrTagInScope:null,pTagInButtonScope:null,listItemTagAutoclosing:null,dlItemTagAutoclosing:null,containerTagInScope:null,implicitRootScope:!1},ff={},Em={animation:"animationDelay animationDirection animationDuration animationFillMode animationIterationCount animationName animationPlayState animationTimingFunction".split(" "),background:"backgroundAttachment backgroundClip backgroundColor backgroundImage backgroundOrigin backgroundPositionX backgroundPositionY backgroundRepeat backgroundSize".split(" "),backgroundPosition:["backgroundPositionX","backgroundPositionY"],border:"borderBottomColor borderBottomStyle borderBottomWidth borderImageOutset borderImageRepeat borderImageSlice borderImageSource borderImageWidth borderLeftColor borderLeftStyle borderLeftWidth borderRightColor borderRightStyle borderRightWidth borderTopColor borderTopStyle borderTopWidth".split(" "),borderBlockEnd:["borderBlockEndColor","borderBlockEndStyle","borderBlockEndWidth"],borderBlockStart:["borderBlockStartColor","borderBlockStartStyle","borderBlockStartWidth"],borderBottom:["borderBottomColor","borderBottomStyle","borderBottomWidth"],borderColor:["borderBottomColor","borderLeftColor","borderRightColor","borderTopColor"],borderImage:["borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth"],borderInlineEnd:["borderInlineEndColor","borderInlineEndStyle","borderInlineEndWidth"],borderInlineStart:["borderInlineStartColor","borderInlineStartStyle","borderInlineStartWidth"],borderLeft:["borderLeftColor","borderLeftStyle","borderLeftWidth"],borderRadius:["borderBottomLeftRadius","borderBottomRightRadius","borderTopLeftRadius","borderTopRightRadius"],borderRight:["borderRightColor","borderRightStyle","borderRightWidth"],borderStyle:["borderBottomStyle","borderLeftStyle","borderRightStyle","borderTopStyle"],borderTop:["borderTopColor","borderTopStyle","borderTopWidth"],borderWidth:["borderBottomWidth","borderLeftWidth","borderRightWidth","borderTopWidth"],columnRule:["columnRuleColor","columnRuleStyle","columnRuleWidth"],columns:["columnCount","columnWidth"],flex:["flexBasis","flexGrow","flexShrink"],flexFlow:["flexDirection","flexWrap"],font:"fontFamily fontFeatureSettings fontKerning fontLanguageOverride fontSize fontSizeAdjust fontStretch fontStyle fontVariant fontVariantAlternates fontVariantCaps fontVariantEastAsian fontVariantLigatures fontVariantNumeric fontVariantPosition fontWeight lineHeight".split(" "),fontVariant:"fontVariantAlternates fontVariantCaps fontVariantEastAsian fontVariantLigatures fontVariantNumeric fontVariantPosition".split(" "),gap:["columnGap","rowGap"],grid:"gridAutoColumns gridAutoFlow gridAutoRows gridTemplateAreas gridTemplateColumns gridTemplateRows".split(" "),gridArea:["gridColumnEnd","gridColumnStart","gridRowEnd","gridRowStart"],gridColumn:["gridColumnEnd","gridColumnStart"],gridColumnGap:["columnGap"],gridGap:["columnGap","rowGap"],gridRow:["gridRowEnd","gridRowStart"],gridRowGap:["rowGap"],gridTemplate:["gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],listStyle:["listStyleImage","listStylePosition","listStyleType"],margin:["marginBottom","marginLeft","marginRight","marginTop"],marker:["markerEnd","markerMid","markerStart"],mask:"maskClip maskComposite maskImage maskMode maskOrigin maskPositionX maskPositionY maskRepeat maskSize".split(" "),maskPosition:["maskPositionX","maskPositionY"],outline:["outlineColor","outlineStyle","outlineWidth"],overflow:["overflowX","overflowY"],padding:["paddingBottom","paddingLeft","paddingRight","paddingTop"],placeContent:["alignContent","justifyContent"],placeItems:["alignItems","justifyItems"],placeSelf:["alignSelf","justifySelf"],textDecoration:["textDecorationColor","textDecorationLine","textDecorationStyle"],textEmphasis:["textEmphasisColor","textEmphasisStyle"],transition:["transitionDelay","transitionDuration","transitionProperty","transitionTimingFunction"],wordWrap:["overflowWrap"]},HS=/([A-Z])/g,LS=/^ms-/,Kz=/^(?:webkit|moz|o)[A-Z]/,Jz=/^-ms-/,Wz=/-(.)/g,BS=/;\s*$/,Nl={},wm={},VS=!1,PS=!1,$S=new Set("animationIterationCount aspectRatio borderImageOutset borderImageSlice borderImageWidth boxFlex boxFlexGroup boxOrdinalGroup columnCount columns flex flexGrow flexPositive flexShrink flexNegative flexOrder gridArea gridRow gridRowEnd gridRowSpan gridRowStart gridColumn gridColumnEnd gridColumnSpan gridColumnStart fontWeight lineClamp lineHeight opacity order orphans scale tabSize widows zIndex zoom fillOpacity floodOpacity stopOpacity strokeDasharray strokeDashoffset strokeMiterlimit strokeOpacity strokeWidth MozAnimationIterationCount MozBoxFlex MozBoxFlexGroup MozLineClamp msAnimationIterationCount msFlex msZoom msFlexGrow msFlexNegative msFlexOrder msFlexPositive msFlexShrink msGridColumn msGridColumnSpan msGridRow msGridRowSpan WebkitAnimationIterationCount WebkitBoxFlex WebKitBoxFlexGroup WebkitBoxOrdinalGroup WebkitColumnCount WebkitColumns WebkitFlex WebkitFlexGrow WebkitFlexPositive WebkitFlexShrink WebkitLineClamp".split(" ")),df="http://www.w3.org/1998/Math/MathML",kl="http://www.w3.org/2000/svg",Fz=new Map([["acceptCharset","accept-charset"],["htmlFor","for"],["httpEquiv","http-equiv"],["crossOrigin","crossorigin"],["accentHeight","accent-height"],["alignmentBaseline","alignment-baseline"],["arabicForm","arabic-form"],["baselineShift","baseline-shift"],["capHeight","cap-height"],["clipPath","clip-path"],["clipRule","clip-rule"],["colorInterpolation","color-interpolation"],["colorInterpolationFilters","color-interpolation-filters"],["colorProfile","color-profile"],["colorRendering","color-rendering"],["dominantBaseline","dominant-baseline"],["enableBackground","enable-background"],["fillOpacity","fill-opacity"],["fillRule","fill-rule"],["floodColor","flood-color"],["floodOpacity","flood-opacity"],["fontFamily","font-family"],["fontSize","font-size"],["fontSizeAdjust","font-size-adjust"],["fontStretch","font-stretch"],["fontStyle","font-style"],["fontVariant","font-variant"],["fontWeight","font-weight"],["glyphName","glyph-name"],["glyphOrientationHorizontal","glyph-orientation-horizontal"],["glyphOrientationVertical","glyph-orientation-vertical"],["horizAdvX","horiz-adv-x"],["horizOriginX","horiz-origin-x"],["imageRendering","image-rendering"],["letterSpacing","letter-spacing"],["lightingColor","lighting-color"],["markerEnd","marker-end"],["markerMid","marker-mid"],["markerStart","marker-start"],["overlinePosition","overline-position"],["overlineThickness","overline-thickness"],["paintOrder","paint-order"],["panose-1","panose-1"],["pointerEvents","pointer-events"],["renderingIntent","rendering-intent"],["shapeRendering","shape-rendering"],["stopColor","stop-color"],["stopOpacity","stop-opacity"],["strikethroughPosition","strikethrough-position"],["strikethroughThickness","strikethrough-thickness"],["strokeDasharray","stroke-dasharray"],["strokeDashoffset","stroke-dashoffset"],["strokeLinecap","stroke-linecap"],["strokeLinejoin","stroke-linejoin"],["strokeMiterlimit","stroke-miterlimit"],["strokeOpacity","stroke-opacity"],["strokeWidth","stroke-width"],["textAnchor","text-anchor"],["textDecoration","text-decoration"],["textRendering","text-rendering"],["transformOrigin","transform-origin"],["underlinePosition","underline-position"],["underlineThickness","underline-thickness"],["unicodeBidi","unicode-bidi"],["unicodeRange","unicode-range"],["unitsPerEm","units-per-em"],["vAlphabetic","v-alphabetic"],["vHanging","v-hanging"],["vIdeographic","v-ideographic"],["vMathematical","v-mathematical"],["vectorEffect","vector-effect"],["vertAdvY","vert-adv-y"],["vertOriginX","vert-origin-x"],["vertOriginY","vert-origin-y"],["wordSpacing","word-spacing"],["writingMode","writing-mode"],["xmlnsXlink","xmlns:xlink"],["xHeight","x-height"]]),hf={accept:"accept",acceptcharset:"acceptCharset","accept-charset":"acceptCharset",accesskey:"accessKey",action:"action",allowfullscreen:"allowFullScreen",alt:"alt",as:"as",async:"async",autocapitalize:"autoCapitalize",autocomplete:"autoComplete",autocorrect:"autoCorrect",autofocus:"autoFocus",autoplay:"autoPlay",autosave:"autoSave",capture:"capture",cellpadding:"cellPadding",cellspacing:"cellSpacing",challenge:"challenge",charset:"charSet",checked:"checked",children:"children",cite:"cite",class:"className",classid:"classID",classname:"className",cols:"cols",colspan:"colSpan",content:"content",contenteditable:"contentEditable",contextmenu:"contextMenu",controls:"controls",controlslist:"controlsList",coords:"coords",crossorigin:"crossOrigin",dangerouslysetinnerhtml:"dangerouslySetInnerHTML",data:"data",datetime:"dateTime",default:"default",defaultchecked:"defaultChecked",defaultvalue:"defaultValue",defer:"defer",dir:"dir",disabled:"disabled",disablepictureinpicture:"disablePictureInPicture",disableremoteplayback:"disableRemotePlayback",download:"download",draggable:"draggable",enctype:"encType",enterkeyhint:"enterKeyHint",fetchpriority:"fetchPriority",for:"htmlFor",form:"form",formmethod:"formMethod",formaction:"formAction",formenctype:"formEncType",formnovalidate:"formNoValidate",formtarget:"formTarget",frameborder:"frameBorder",headers:"headers",height:"height",hidden:"hidden",high:"high",href:"href",hreflang:"hrefLang",htmlfor:"htmlFor",httpequiv:"httpEquiv","http-equiv":"httpEquiv",icon:"icon",id:"id",imagesizes:"imageSizes",imagesrcset:"imageSrcSet",inert:"inert",innerhtml:"innerHTML",inputmode:"inputMode",integrity:"integrity",is:"is",itemid:"itemID",itemprop:"itemProp",itemref:"itemRef",itemscope:"itemScope",itemtype:"itemType",keyparams:"keyParams",keytype:"keyType",kind:"kind",label:"label",lang:"lang",list:"list",loop:"loop",low:"low",manifest:"manifest",marginwidth:"marginWidth",marginheight:"marginHeight",max:"max",maxlength:"maxLength",media:"media",mediagroup:"mediaGroup",method:"method",min:"min",minlength:"minLength",multiple:"multiple",muted:"muted",name:"name",nomodule:"noModule",nonce:"nonce",novalidate:"noValidate",open:"open",optimum:"optimum",pattern:"pattern",placeholder:"placeholder",playsinline:"playsInline",poster:"poster",preload:"preload",profile:"profile",radiogroup:"radioGroup",readonly:"readOnly",referrerpolicy:"referrerPolicy",rel:"rel",required:"required",reversed:"reversed",role:"role",rows:"rows",rowspan:"rowSpan",sandbox:"sandbox",scope:"scope",scoped:"scoped",scrolling:"scrolling",seamless:"seamless",selected:"selected",shape:"shape",size:"size",sizes:"sizes",span:"span",spellcheck:"spellCheck",src:"src",srcdoc:"srcDoc",srclang:"srcLang",srcset:"srcSet",start:"start",step:"step",style:"style",summary:"summary",tabindex:"tabIndex",target:"target",title:"title",type:"type",usemap:"useMap",value:"value",width:"width",wmode:"wmode",wrap:"wrap",about:"about",accentheight:"accentHeight","accent-height":"accentHeight",accumulate:"accumulate",additive:"additive",alignmentbaseline:"alignmentBaseline","alignment-baseline":"alignmentBaseline",allowreorder:"allowReorder",alphabetic:"alphabetic",amplitude:"amplitude",arabicform:"arabicForm","arabic-form":"arabicForm",ascent:"ascent",attributename:"attributeName",attributetype:"attributeType",autoreverse:"autoReverse",azimuth:"azimuth",basefrequency:"baseFrequency",baselineshift:"baselineShift","baseline-shift":"baselineShift",baseprofile:"baseProfile",bbox:"bbox",begin:"begin",bias:"bias",by:"by",calcmode:"calcMode",capheight:"capHeight","cap-height":"capHeight",clip:"clip",clippath:"clipPath","clip-path":"clipPath",clippathunits:"clipPathUnits",cliprule:"clipRule","clip-rule":"clipRule",color:"color",colorinterpolation:"colorInterpolation","color-interpolation":"colorInterpolation",colorinterpolationfilters:"colorInterpolationFilters","color-interpolation-filters":"colorInterpolationFilters",colorprofile:"colorProfile","color-profile":"colorProfile",colorrendering:"colorRendering","color-rendering":"colorRendering",contentscripttype:"contentScriptType",contentstyletype:"contentStyleType",cursor:"cursor",cx:"cx",cy:"cy",d:"d",datatype:"datatype",decelerate:"decelerate",descent:"descent",diffuseconstant:"diffuseConstant",direction:"direction",display:"display",divisor:"divisor",dominantbaseline:"dominantBaseline","dominant-baseline":"dominantBaseline",dur:"dur",dx:"dx",dy:"dy",edgemode:"edgeMode",elevation:"elevation",enablebackground:"enableBackground","enable-background":"enableBackground",end:"end",exponent:"exponent",externalresourcesrequired:"externalResourcesRequired",fill:"fill",fillopacity:"fillOpacity","fill-opacity":"fillOpacity",fillrule:"fillRule","fill-rule":"fillRule",filter:"filter",filterres:"filterRes",filterunits:"filterUnits",floodopacity:"floodOpacity","flood-opacity":"floodOpacity",floodcolor:"floodColor","flood-color":"floodColor",focusable:"focusable",fontfamily:"fontFamily","font-family":"fontFamily",fontsize:"fontSize","font-size":"fontSize",fontsizeadjust:"fontSizeAdjust","font-size-adjust":"fontSizeAdjust",fontstretch:"fontStretch","font-stretch":"fontStretch",fontstyle:"fontStyle","font-style":"fontStyle",fontvariant:"fontVariant","font-variant":"fontVariant",fontweight:"fontWeight","font-weight":"fontWeight",format:"format",from:"from",fx:"fx",fy:"fy",g1:"g1",g2:"g2",glyphname:"glyphName","glyph-name":"glyphName",glyphorientationhorizontal:"glyphOrientationHorizontal","glyph-orientation-horizontal":"glyphOrientationHorizontal",glyphorientationvertical:"glyphOrientationVertical","glyph-orientation-vertical":"glyphOrientationVertical",glyphref:"glyphRef",gradienttransform:"gradientTransform",gradientunits:"gradientUnits",hanging:"hanging",horizadvx:"horizAdvX","horiz-adv-x":"horizAdvX",horizoriginx:"horizOriginX","horiz-origin-x":"horizOriginX",ideographic:"ideographic",imagerendering:"imageRendering","image-rendering":"imageRendering",in2:"in2",in:"in",inlist:"inlist",intercept:"intercept",k1:"k1",k2:"k2",k3:"k3",k4:"k4",k:"k",kernelmatrix:"kernelMatrix",kernelunitlength:"kernelUnitLength",kerning:"kerning",keypoints:"keyPoints",keysplines:"keySplines",keytimes:"keyTimes",lengthadjust:"lengthAdjust",letterspacing:"letterSpacing","letter-spacing":"letterSpacing",lightingcolor:"lightingColor","lighting-color":"lightingColor",limitingconeangle:"limitingConeAngle",local:"local",markerend:"markerEnd","marker-end":"markerEnd",markerheight:"markerHeight",markermid:"markerMid","marker-mid":"markerMid",markerstart:"markerStart","marker-start":"markerStart",markerunits:"markerUnits",markerwidth:"markerWidth",mask:"mask",maskcontentunits:"maskContentUnits",maskunits:"maskUnits",mathematical:"mathematical",mode:"mode",numoctaves:"numOctaves",offset:"offset",opacity:"opacity",operator:"operator",order:"order",orient:"orient",orientation:"orientation",origin:"origin",overflow:"overflow",overlineposition:"overlinePosition","overline-position":"overlinePosition",overlinethickness:"overlineThickness","overline-thickness":"overlineThickness",paintorder:"paintOrder","paint-order":"paintOrder",panose1:"panose1","panose-1":"panose1",pathlength:"pathLength",patterncontentunits:"patternContentUnits",patterntransform:"patternTransform",patternunits:"patternUnits",pointerevents:"pointerEvents","pointer-events":"pointerEvents",points:"points",pointsatx:"pointsAtX",pointsaty:"pointsAtY",pointsatz:"pointsAtZ",popover:"popover",popovertarget:"popoverTarget",popovertargetaction:"popoverTargetAction",prefix:"prefix",preservealpha:"preserveAlpha",preserveaspectratio:"preserveAspectRatio",primitiveunits:"primitiveUnits",property:"property",r:"r",radius:"radius",refx:"refX",refy:"refY",renderingintent:"renderingIntent","rendering-intent":"renderingIntent",repeatcount:"repeatCount",repeatdur:"repeatDur",requiredextensions:"requiredExtensions",requiredfeatures:"requiredFeatures",resource:"resource",restart:"restart",result:"result",results:"results",rotate:"rotate",rx:"rx",ry:"ry",scale:"scale",security:"security",seed:"seed",shaperendering:"shapeRendering","shape-rendering":"shapeRendering",slope:"slope",spacing:"spacing",specularconstant:"specularConstant",specularexponent:"specularExponent",speed:"speed",spreadmethod:"spreadMethod",startoffset:"startOffset",stddeviation:"stdDeviation",stemh:"stemh",stemv:"stemv",stitchtiles:"stitchTiles",stopcolor:"stopColor","stop-color":"stopColor",stopopacity:"stopOpacity","stop-opacity":"stopOpacity",strikethroughposition:"strikethroughPosition","strikethrough-position":"strikethroughPosition",strikethroughthickness:"strikethroughThickness","strikethrough-thickness":"strikethroughThickness",string:"string",stroke:"stroke",strokedasharray:"strokeDasharray","stroke-dasharray":"strokeDasharray",strokedashoffset:"strokeDashoffset","stroke-dashoffset":"strokeDashoffset",strokelinecap:"strokeLinecap","stroke-linecap":"strokeLinecap",strokelinejoin:"strokeLinejoin","stroke-linejoin":"strokeLinejoin",strokemiterlimit:"strokeMiterlimit","stroke-miterlimit":"strokeMiterlimit",strokewidth:"strokeWidth","stroke-width":"strokeWidth",strokeopacity:"strokeOpacity","stroke-opacity":"strokeOpacity",suppresscontenteditablewarning:"suppressContentEditableWarning",suppresshydrationwarning:"suppressHydrationWarning",surfacescale:"surfaceScale",systemlanguage:"systemLanguage",tablevalues:"tableValues",targetx:"targetX",targety:"targetY",textanchor:"textAnchor","text-anchor":"textAnchor",textdecoration:"textDecoration","text-decoration":"textDecoration",textlength:"textLength",textrendering:"textRendering","text-rendering":"textRendering",to:"to",transform:"transform",transformorigin:"transformOrigin","transform-origin":"transformOrigin",typeof:"typeof",u1:"u1",u2:"u2",underlineposition:"underlinePosition","underline-position":"underlinePosition",underlinethickness:"underlineThickness","underline-thickness":"underlineThickness",unicode:"unicode",unicodebidi:"unicodeBidi","unicode-bidi":"unicodeBidi",unicoderange:"unicodeRange","unicode-range":"unicodeRange",unitsperem:"unitsPerEm","units-per-em":"unitsPerEm",unselectable:"unselectable",valphabetic:"vAlphabetic","v-alphabetic":"vAlphabetic",values:"values",vectoreffect:"vectorEffect","vector-effect":"vectorEffect",version:"version",vertadvy:"vertAdvY","vert-adv-y":"vertAdvY",vertoriginx:"vertOriginX","vert-origin-x":"vertOriginX",vertoriginy:"vertOriginY","vert-origin-y":"vertOriginY",vhanging:"vHanging","v-hanging":"vHanging",videographic:"vIdeographic","v-ideographic":"vIdeographic",viewbox:"viewBox",viewtarget:"viewTarget",visibility:"visibility",vmathematical:"vMathematical","v-mathematical":"vMathematical",vocab:"vocab",widths:"widths",wordspacing:"wordSpacing","word-spacing":"wordSpacing",writingmode:"writingMode","writing-mode":"writingMode",x1:"x1",x2:"x2",x:"x",xchannelselector:"xChannelSelector",xheight:"xHeight","x-height":"xHeight",xlinkactuate:"xlinkActuate","xlink:actuate":"xlinkActuate",xlinkarcrole:"xlinkArcrole","xlink:arcrole":"xlinkArcrole",xlinkhref:"xlinkHref","xlink:href":"xlinkHref",xlinkrole:"xlinkRole","xlink:role":"xlinkRole",xlinkshow:"xlinkShow","xlink:show":"xlinkShow",xlinktitle:"xlinkTitle","xlink:title":"xlinkTitle",xlinktype:"xlinkType","xlink:type":"xlinkType",xmlbase:"xmlBase","xml:base":"xmlBase",xmllang:"xmlLang","xml:lang":"xmlLang",xmlns:"xmlns","xml:space":"xmlSpace",xmlnsxlink:"xmlnsXlink","xmlns:xlink":"xmlnsXlink",xmlspace:"xmlSpace",y1:"y1",y2:"y2",y:"y",ychannelselector:"yChannelSelector",z:"z",zoomandpan:"zoomAndPan"},qS={"aria-current":0,"aria-description":0,"aria-details":0,"aria-disabled":0,"aria-hidden":0,"aria-invalid":0,"aria-keyshortcuts":0,"aria-label":0,"aria-roledescription":0,"aria-autocomplete":0,"aria-checked":0,"aria-expanded":0,"aria-haspopup":0,"aria-level":0,"aria-modal":0,"aria-multiline":0,"aria-multiselectable":0,"aria-orientation":0,"aria-placeholder":0,"aria-pressed":0,"aria-readonly":0,"aria-required":0,"aria-selected":0,"aria-sort":0,"aria-valuemax":0,"aria-valuemin":0,"aria-valuenow":0,"aria-valuetext":0,"aria-atomic":0,"aria-busy":0,"aria-live":0,"aria-relevant":0,"aria-dropeffect":0,"aria-grabbed":0,"aria-activedescendant":0,"aria-colcount":0,"aria-colindex":0,"aria-colspan":0,"aria-controls":0,"aria-describedby":0,"aria-errormessage":0,"aria-flowto":0,"aria-labelledby":0,"aria-owns":0,"aria-posinset":0,"aria-rowcount":0,"aria-rowindex":0,"aria-rowspan":0,"aria-setsize":0},Hl={},eD=RegExp("^(aria)-[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),tD=RegExp("^(aria)[A-Z][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),GS=!1,$t={},YS=/^on./,nD=/^on[^A-Z]/,aD=RegExp("^(aria)-[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),oD=RegExp("^(aria)[A-Z][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),iD=/^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*:/i,ws=null,Ll=null,Bl=null,Am=!1,Ma=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),xm=!1;if(Ma)try{var As={};Object.defineProperty(As,"passive",{get:function(){xm=!0}}),window.addEventListener("test",As,As),window.removeEventListener("test",As,As)}catch{xm=!1}var qo=null,Rm=null,pf=null,Ni={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(e){return e.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},mf=Kt(Ni),xs=ge({},Ni,{view:0,detail:0}),lD=Kt(xs),zm,Dm,Rs,vf=ge({},xs,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:vh,button:0,buttons:0,relatedTarget:function(e){return e.relatedTarget===void 0?e.fromElement===e.srcElement?e.toElement:e.fromElement:e.relatedTarget},movementX:function(e){return"movementX"in e?e.movementX:(e!==Rs&&(Rs&&e.type==="mousemove"?(zm=e.screenX-Rs.screenX,Dm=e.screenY-Rs.screenY):Dm=zm=0,Rs=e),zm)},movementY:function(e){return"movementY"in e?e.movementY:Dm}}),XS=Kt(vf),rD=ge({},vf,{dataTransfer:0}),sD=Kt(rD),uD=ge({},xs,{relatedTarget:0}),Cm=Kt(uD),cD=ge({},Ni,{animationName:0,elapsedTime:0,pseudoElement:0}),fD=Kt(cD),dD=ge({},Ni,{clipboardData:function(e){return"clipboardData"in e?e.clipboardData:window.clipboardData}}),hD=Kt(dD),pD=ge({},Ni,{data:0}),IS=Kt(pD),mD=IS,vD={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},yD={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},gD={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"},bD=ge({},xs,{key:function(e){if(e.key){var t=vD[e.key]||e.key;if(t!=="Unidentified")return t}return e.type==="keypress"?(e=uc(e),e===13?"Enter":String.fromCharCode(e)):e.type==="keydown"||e.type==="keyup"?yD[e.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:vh,charCode:function(e){return e.type==="keypress"?uc(e):0},keyCode:function(e){return e.type==="keydown"||e.type==="keyup"?e.keyCode:0},which:function(e){return e.type==="keypress"?uc(e):e.type==="keydown"||e.type==="keyup"?e.keyCode:0}}),_D=Kt(bD),SD=ge({},vf,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),QS=Kt(SD),TD=ge({},xs,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:vh}),OD=Kt(TD),ED=ge({},Ni,{propertyName:0,elapsedTime:0,pseudoElement:0}),wD=Kt(ED),AD=ge({},vf,{deltaX:function(e){return"deltaX"in e?e.deltaX:"wheelDeltaX"in e?-e.wheelDeltaX:0},deltaY:function(e){return"deltaY"in e?e.deltaY:"wheelDeltaY"in e?-e.wheelDeltaY:"wheelDelta"in e?-e.wheelDelta:0},deltaZ:0,deltaMode:0}),xD=Kt(AD),RD=ge({},Ni,{newState:0,oldState:0}),zD=Kt(RD),DD=[9,13,27,32],ZS=229,Mm=Ma&&"CompositionEvent"in window,zs=null;Ma&&"documentMode"in document&&(zs=document.documentMode);var CD=Ma&&"TextEvent"in window&&!zs,KS=Ma&&(!Mm||zs&&8<zs&&11>=zs),JS=32,WS=String.fromCharCode(JS),FS=!1,Vl=!1,MD={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0},Ds=null,Cs=null,eT=!1;Ma&&(eT=sR("input")&&(!document.documentMode||9<document.documentMode));var qt=typeof Object.is=="function"?Object.is:pR,jD=Ma&&"documentMode"in document&&11>=document.documentMode,Pl=null,jm=null,Ms=null,Um=!1,$l={animationend:bi("Animation","AnimationEnd"),animationiteration:bi("Animation","AnimationIteration"),animationstart:bi("Animation","AnimationStart"),transitionrun:bi("Transition","TransitionRun"),transitionstart:bi("Transition","TransitionStart"),transitioncancel:bi("Transition","TransitionCancel"),transitionend:bi("Transition","TransitionEnd")},Nm={},tT={};Ma&&(tT=document.createElement("div").style,"AnimationEvent"in window||(delete $l.animationend.animation,delete $l.animationiteration.animation,delete $l.animationstart.animation),"TransitionEvent"in window||delete $l.transitionend.transition);var nT=_i("animationend"),aT=_i("animationiteration"),oT=_i("animationstart"),UD=_i("transitionrun"),ND=_i("transitionstart"),kD=_i("transitioncancel"),iT=_i("transitionend"),lT=new Map,km="abort auxClick beforeToggle cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");km.push("scrollEnd");var Hm=new WeakMap,yf=1,no=2,$n=[],ql=0,Lm=0,Go={};Object.freeze(Go);var qn=null,Gl=null,Xe=0,HD=1,Mt=2,Bt=8,ca=16,rT=64,sT=!1;try{var uT=Object.preventExtensions({})}catch{sT=!0}var Yl=[],Xl=0,gf=null,bf=0,Gn=[],Yn=0,ki=null,ao=1,oo="",Gt=null,tt=null,xe=!1,io=!1,Xn=null,Hi=null,ja=!1,Bm=Error("Hydration Mismatch Exception: This is not a real error, and should not leak into userspace. If you're seeing this, it's likely a bug in React."),cT=0;if(typeof performance=="object"&&typeof performance.now=="function")var LD=performance,fT=function(){return LD.now()};else{var BD=Date;fT=function(){return BD.now()}}var Vm=at(null),Pm=at(null),dT={},_f=null,Il=null,Ql=!1,VD=typeof AbortController<"u"?AbortController:function(){var e=[],t=this.signal={aborted:!1,addEventListener:function(n,a){e.push(a)}};this.abort=function(){t.aborted=!0,e.forEach(function(n){return n()})}},PD=ft.unstable_scheduleCallback,$D=ft.unstable_NormalPriority,vt={$$typeof:xa,Consumer:null,Provider:null,_currentValue:null,_currentValue2:null,_threadCount:0,_currentRenderer:null,_currentRenderer2:null},Zl=ft.unstable_now,hT=-0,Sf=-0,tn=-1.1,Li=-0,Tf=!1,Of=!1,js=null,$m=0,Bi=0,Kl=null,pT=D.S;D.S=function(e,t){typeof t=="object"&&t!==null&&typeof t.then=="function"&&vR(e,t),pT!==null&&pT(e,t)};var Vi=at(null),fa={recordUnsafeLifecycleWarnings:function(){},flushPendingUnsafeLifecycleWarnings:function(){},recordLegacyContextWarning:function(){},flushLegacyContextWarning:function(){},discardPendingWarnings:function(){}},Us=[],Ns=[],ks=[],Hs=[],Ls=[],Bs=[],Pi=new Set;fa.recordUnsafeLifecycleWarnings=function(e,t){Pi.has(e.type)||(typeof t.componentWillMount=="function"&&t.componentWillMount.__suppressDeprecationWarning!==!0&&Us.push(e),e.mode&Bt&&typeof t.UNSAFE_componentWillMount=="function"&&Ns.push(e),typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps.__suppressDeprecationWarning!==!0&&ks.push(e),e.mode&Bt&&typeof t.UNSAFE_componentWillReceiveProps=="function"&&Hs.push(e),typeof t.componentWillUpdate=="function"&&t.componentWillUpdate.__suppressDeprecationWarning!==!0&&Ls.push(e),e.mode&Bt&&typeof t.UNSAFE_componentWillUpdate=="function"&&Bs.push(e))},fa.flushPendingUnsafeLifecycleWarnings=function(){var e=new Set;0<Us.length&&(Us.forEach(function(d){e.add(ee(d)||"Component"),Pi.add(d.type)}),Us=[]);var t=new Set;0<Ns.length&&(Ns.forEach(function(d){t.add(ee(d)||"Component"),Pi.add(d.type)}),Ns=[]);var n=new Set;0<ks.length&&(ks.forEach(function(d){n.add(ee(d)||"Component"),Pi.add(d.type)}),ks=[]);var a=new Set;0<Hs.length&&(Hs.forEach(function(d){a.add(ee(d)||"Component"),Pi.add(d.type)}),Hs=[]);var o=new Set;0<Ls.length&&(Ls.forEach(function(d){o.add(ee(d)||"Component"),Pi.add(d.type)}),Ls=[]);var l=new Set;if(0<Bs.length&&(Bs.forEach(function(d){l.add(ee(d)||"Component"),Pi.add(d.type)}),Bs=[]),0<t.size){var c=x(t);console.error(`Using UNSAFE_componentWillMount in strict mode is not recommended and may indicate bugs in your code. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.

Please update the following components: %s`,c)}0<a.size&&(c=x(a),console.error(`Using UNSAFE_componentWillReceiveProps in strict mode is not recommended and may indicate bugs in your code. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://react.dev/link/derived-state

Please update the following components: %s`,c)),0<l.size&&(c=x(l),console.error(`Using UNSAFE_componentWillUpdate in strict mode is not recommended and may indicate bugs in your code. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.

Please update the following components: %s`,c)),0<e.size&&(c=x(e),console.warn(`componentWillMount has been renamed, and is not recommended for use. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.
* Rename componentWillMount to UNSAFE_componentWillMount to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,c)),0<n.size&&(c=x(n),console.warn(`componentWillReceiveProps has been renamed, and is not recommended for use. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://react.dev/link/derived-state
* Rename componentWillReceiveProps to UNSAFE_componentWillReceiveProps to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,c)),0<o.size&&(c=x(o),console.warn(`componentWillUpdate has been renamed, and is not recommended for use. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* Rename componentWillUpdate to UNSAFE_componentWillUpdate to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,c))};var Ef=new Map,mT=new Set;fa.recordLegacyContextWarning=function(e,t){for(var n=null,a=e;a!==null;)a.mode&Bt&&(n=a),a=a.return;n===null?console.error("Expected to find a StrictMode component in a strict mode tree. This error is likely caused by a bug in React. Please file an issue."):!mT.has(e.type)&&(a=Ef.get(n),e.type.contextTypes!=null||e.type.childContextTypes!=null||t!==null&&typeof t.getChildContext=="function")&&(a===void 0&&(a=[],Ef.set(n,a)),a.push(e))},fa.flushLegacyContextWarning=function(){Ef.forEach(function(e){if(e.length!==0){var t=e[0],n=new Set;e.forEach(function(o){n.add(ee(o)||"Component"),mT.add(o.type)});var a=x(n);W(t,function(){console.error(`Legacy context API has been detected within a strict-mode tree.

The old API will be supported in all 16.x releases, but applications using it should migrate to the new version.

Please update the following components: %s

Learn more about this warning here: https://react.dev/link/legacy-context`,a)})}})},fa.discardPendingWarnings=function(){Us=[],Ns=[],ks=[],Hs=[],Ls=[],Bs=[],Ef=new Map};var Vs=Error("Suspense Exception: This is not a real error! It's an implementation detail of `use` to interrupt the current render. You must either rethrow it immediately, or move the `use` call outside of the `try/catch` block. Capturing without rethrowing will lead to unexpected behavior.\n\nTo handle async errors, wrap your component in an error boundary, or call the promise's `.catch` method and pass the result to `use`."),vT=Error("Suspense Exception: This is not a real error, and should not leak into userspace. If you're seeing this, it's likely a bug in React."),wf=Error("Suspense Exception: This is not a real error! It's an implementation detail of `useActionState` to interrupt the current render. You must either rethrow it immediately, or move the `useActionState` call outside of the `try/catch` block. Capturing without rethrowing will lead to unexpected behavior.\n\nTo handle async errors, wrap your component in an error boundary."),qm={then:function(){console.error('Internal React error: A listener was unexpectedly attached to a "noop" thenable. This is a bug in React. Please file an issue.')}},Ps=null,Af=!1,In=0,Qn=1,Yt=2,jt=4,yt=8,yT=0,gT=1,bT=2,Gm=3,Yo=!1,_T=!1,Ym=null,Xm=!1,Jl=at(null),xf=at(0),Wl,ST=new Set,TT=new Set,Im=new Set,OT=new Set,Xo=0,ce=null,Pe=null,dt=null,Rf=!1,Fl=!1,$i=!1,zf=0,$s=0,lo=null,qD=0,GD=25,z=null,Zn=null,ro=-1,qs=!1,Df={readContext:Je,use:Mo,useCallback:ot,useContext:ot,useEffect:ot,useImperativeHandle:ot,useLayoutEffect:ot,useInsertionEffect:ot,useMemo:ot,useReducer:ot,useRef:ot,useState:ot,useDebugValue:ot,useDeferredValue:ot,useTransition:ot,useSyncExternalStore:ot,useId:ot,useHostTransitionStatus:ot,useFormState:ot,useActionState:ot,useOptimistic:ot,useMemoCache:ot,useCacheRefresh:ot},Qm=null,ET=null,Zm=null,wT=null,Ua=null,da=null,Cf=null;Qm={readContext:function(e){return Je(e)},use:Mo,useCallback:function(e,t){return z="useCallback",Te(),gl(t),tp(e,t)},useContext:function(e){return z="useContext",Te(),Je(e)},useEffect:function(e,t){return z="useEffect",Te(),gl(t),zc(e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",Te(),gl(n),ep(e,t,n)},useInsertionEffect:function(e,t){z="useInsertionEffect",Te(),gl(t),Di(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",Te(),gl(t),Fh(e,t)},useMemo:function(e,t){z="useMemo",Te(),gl(t);var n=D.H;D.H=Ua;try{return np(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",Te();var a=D.H;D.H=Ua;try{return Yh(e,t,n)}finally{D.H=a}},useRef:function(e){return z="useRef",Te(),Wh(e)},useState:function(e){z="useState",Te();var t=D.H;D.H=Ua;try{return Zh(e)}finally{D.H=t}},useDebugValue:function(){z="useDebugValue",Te()},useDeferredValue:function(e,t){return z="useDeferredValue",Te(),ap(e,t)},useTransition:function(){return z="useTransition",Te(),lp()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",Te(),Ih(e,t,n)},useId:function(){return z="useId",Te(),rp()},useFormState:function(e,t){return z="useFormState",Te(),Ec(),_l(e,t)},useActionState:function(e,t){return z="useActionState",Te(),_l(e,t)},useOptimistic:function(e){return z="useOptimistic",Te(),Kh(e)},useHostTransitionStatus:Ci,useMemoCache:zi,useCacheRefresh:function(){return z="useCacheRefresh",Te(),sp()}},ET={readContext:function(e){return Je(e)},use:Mo,useCallback:function(e,t){return z="useCallback",V(),tp(e,t)},useContext:function(e){return z="useContext",V(),Je(e)},useEffect:function(e,t){return z="useEffect",V(),zc(e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",V(),ep(e,t,n)},useInsertionEffect:function(e,t){z="useInsertionEffect",V(),Di(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",V(),Fh(e,t)},useMemo:function(e,t){z="useMemo",V();var n=D.H;D.H=Ua;try{return np(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",V();var a=D.H;D.H=Ua;try{return Yh(e,t,n)}finally{D.H=a}},useRef:function(e){return z="useRef",V(),Wh(e)},useState:function(e){z="useState",V();var t=D.H;D.H=Ua;try{return Zh(e)}finally{D.H=t}},useDebugValue:function(){z="useDebugValue",V()},useDeferredValue:function(e,t){return z="useDeferredValue",V(),ap(e,t)},useTransition:function(){return z="useTransition",V(),lp()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",V(),Ih(e,t,n)},useId:function(){return z="useId",V(),rp()},useActionState:function(e,t){return z="useActionState",V(),_l(e,t)},useFormState:function(e,t){return z="useFormState",V(),Ec(),_l(e,t)},useOptimistic:function(e){return z="useOptimistic",V(),Kh(e)},useHostTransitionStatus:Ci,useMemoCache:zi,useCacheRefresh:function(){return z="useCacheRefresh",V(),sp()}},Zm={readContext:function(e){return Je(e)},use:Mo,useCallback:function(e,t){return z="useCallback",V(),Cc(e,t)},useContext:function(e){return z="useContext",V(),Je(e)},useEffect:function(e,t){z="useEffect",V(),Ft(2048,yt,e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",V(),Dc(e,t,n)},useInsertionEffect:function(e,t){return z="useInsertionEffect",V(),Ft(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",V(),Ft(4,jt,e,t)},useMemo:function(e,t){z="useMemo",V();var n=D.H;D.H=da;try{return Mc(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",V();var a=D.H;D.H=da;try{return bl(e,t,n)}finally{D.H=a}},useRef:function(){return z="useRef",V(),He().memoizedState},useState:function(){z="useState",V();var e=D.H;D.H=da;try{return bl(sa)}finally{D.H=e}},useDebugValue:function(){z="useDebugValue",V()},useDeferredValue:function(e,t){return z="useDeferredValue",V(),n0(e,t)},useTransition:function(){return z="useTransition",V(),s0()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",V(),wc(e,t,n)},useId:function(){return z="useId",V(),He().memoizedState},useFormState:function(e){return z="useFormState",V(),Ec(),Ac(e)},useActionState:function(e){return z="useActionState",V(),Ac(e)},useOptimistic:function(e,t){return z="useOptimistic",V(),Xb(e,t)},useHostTransitionStatus:Ci,useMemoCache:zi,useCacheRefresh:function(){return z="useCacheRefresh",V(),He().memoizedState}},wT={readContext:function(e){return Je(e)},use:Mo,useCallback:function(e,t){return z="useCallback",V(),Cc(e,t)},useContext:function(e){return z="useContext",V(),Je(e)},useEffect:function(e,t){z="useEffect",V(),Ft(2048,yt,e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",V(),Dc(e,t,n)},useInsertionEffect:function(e,t){return z="useInsertionEffect",V(),Ft(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",V(),Ft(4,jt,e,t)},useMemo:function(e,t){z="useMemo",V();var n=D.H;D.H=Cf;try{return Mc(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",V();var a=D.H;D.H=Cf;try{return os(e,t,n)}finally{D.H=a}},useRef:function(){return z="useRef",V(),He().memoizedState},useState:function(){z="useState",V();var e=D.H;D.H=Cf;try{return os(sa)}finally{D.H=e}},useDebugValue:function(){z="useDebugValue",V()},useDeferredValue:function(e,t){return z="useDeferredValue",V(),a0(e,t)},useTransition:function(){return z="useTransition",V(),u0()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",V(),wc(e,t,n)},useId:function(){return z="useId",V(),He().memoizedState},useFormState:function(e){return z="useFormState",V(),Ec(),xc(e)},useActionState:function(e){return z="useActionState",V(),xc(e)},useOptimistic:function(e,t){return z="useOptimistic",V(),Qb(e,t)},useHostTransitionStatus:Ci,useMemoCache:zi,useCacheRefresh:function(){return z="useCacheRefresh",V(),He().memoizedState}},Ua={readContext:function(e){return N(),Je(e)},use:function(e){return g(),Mo(e)},useCallback:function(e,t){return z="useCallback",g(),Te(),tp(e,t)},useContext:function(e){return z="useContext",g(),Te(),Je(e)},useEffect:function(e,t){return z="useEffect",g(),Te(),zc(e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",g(),Te(),ep(e,t,n)},useInsertionEffect:function(e,t){z="useInsertionEffect",g(),Te(),Di(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",g(),Te(),Fh(e,t)},useMemo:function(e,t){z="useMemo",g(),Te();var n=D.H;D.H=Ua;try{return np(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",g(),Te();var a=D.H;D.H=Ua;try{return Yh(e,t,n)}finally{D.H=a}},useRef:function(e){return z="useRef",g(),Te(),Wh(e)},useState:function(e){z="useState",g(),Te();var t=D.H;D.H=Ua;try{return Zh(e)}finally{D.H=t}},useDebugValue:function(){z="useDebugValue",g(),Te()},useDeferredValue:function(e,t){return z="useDeferredValue",g(),Te(),ap(e,t)},useTransition:function(){return z="useTransition",g(),Te(),lp()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",g(),Te(),Ih(e,t,n)},useId:function(){return z="useId",g(),Te(),rp()},useFormState:function(e,t){return z="useFormState",g(),Te(),_l(e,t)},useActionState:function(e,t){return z="useActionState",g(),Te(),_l(e,t)},useOptimistic:function(e){return z="useOptimistic",g(),Te(),Kh(e)},useMemoCache:function(e){return g(),zi(e)},useHostTransitionStatus:Ci,useCacheRefresh:function(){return z="useCacheRefresh",Te(),sp()}},da={readContext:function(e){return N(),Je(e)},use:function(e){return g(),Mo(e)},useCallback:function(e,t){return z="useCallback",g(),V(),Cc(e,t)},useContext:function(e){return z="useContext",g(),V(),Je(e)},useEffect:function(e,t){z="useEffect",g(),V(),Ft(2048,yt,e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",g(),V(),Dc(e,t,n)},useInsertionEffect:function(e,t){return z="useInsertionEffect",g(),V(),Ft(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",g(),V(),Ft(4,jt,e,t)},useMemo:function(e,t){z="useMemo",g(),V();var n=D.H;D.H=da;try{return Mc(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",g(),V();var a=D.H;D.H=da;try{return bl(e,t,n)}finally{D.H=a}},useRef:function(){return z="useRef",g(),V(),He().memoizedState},useState:function(){z="useState",g(),V();var e=D.H;D.H=da;try{return bl(sa)}finally{D.H=e}},useDebugValue:function(){z="useDebugValue",g(),V()},useDeferredValue:function(e,t){return z="useDeferredValue",g(),V(),n0(e,t)},useTransition:function(){return z="useTransition",g(),V(),s0()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",g(),V(),wc(e,t,n)},useId:function(){return z="useId",g(),V(),He().memoizedState},useFormState:function(e){return z="useFormState",g(),V(),Ac(e)},useActionState:function(e){return z="useActionState",g(),V(),Ac(e)},useOptimistic:function(e,t){return z="useOptimistic",g(),V(),Xb(e,t)},useMemoCache:function(e){return g(),zi(e)},useHostTransitionStatus:Ci,useCacheRefresh:function(){return z="useCacheRefresh",V(),He().memoizedState}},Cf={readContext:function(e){return N(),Je(e)},use:function(e){return g(),Mo(e)},useCallback:function(e,t){return z="useCallback",g(),V(),Cc(e,t)},useContext:function(e){return z="useContext",g(),V(),Je(e)},useEffect:function(e,t){z="useEffect",g(),V(),Ft(2048,yt,e,t)},useImperativeHandle:function(e,t,n){return z="useImperativeHandle",g(),V(),Dc(e,t,n)},useInsertionEffect:function(e,t){return z="useInsertionEffect",g(),V(),Ft(4,Yt,e,t)},useLayoutEffect:function(e,t){return z="useLayoutEffect",g(),V(),Ft(4,jt,e,t)},useMemo:function(e,t){z="useMemo",g(),V();var n=D.H;D.H=da;try{return Mc(e,t)}finally{D.H=n}},useReducer:function(e,t,n){z="useReducer",g(),V();var a=D.H;D.H=da;try{return os(e,t,n)}finally{D.H=a}},useRef:function(){return z="useRef",g(),V(),He().memoizedState},useState:function(){z="useState",g(),V();var e=D.H;D.H=da;try{return os(sa)}finally{D.H=e}},useDebugValue:function(){z="useDebugValue",g(),V()},useDeferredValue:function(e,t){return z="useDeferredValue",g(),V(),a0(e,t)},useTransition:function(){return z="useTransition",g(),V(),u0()},useSyncExternalStore:function(e,t,n){return z="useSyncExternalStore",g(),V(),wc(e,t,n)},useId:function(){return z="useId",g(),V(),He().memoizedState},useFormState:function(e){return z="useFormState",g(),V(),xc(e)},useActionState:function(e){return z="useActionState",g(),V(),xc(e)},useOptimistic:function(e,t){return z="useOptimistic",g(),V(),Qb(e,t)},useMemoCache:function(e){return g(),zi(e)},useHostTransitionStatus:Ci,useCacheRefresh:function(){return z="useCacheRefresh",V(),He().memoizedState}};var AT={react_stack_bottom_frame:function(e,t,n){var a=Ca;Ca=!0;try{return e(t,n)}finally{Ca=a}}},Km=AT.react_stack_bottom_frame.bind(AT),xT={react_stack_bottom_frame:function(e){var t=Ca;Ca=!0;try{return e.render()}finally{Ca=t}}},RT=xT.react_stack_bottom_frame.bind(xT),zT={react_stack_bottom_frame:function(e,t){try{t.componentDidMount()}catch(n){Be(e,e.return,n)}}},Jm=zT.react_stack_bottom_frame.bind(zT),DT={react_stack_bottom_frame:function(e,t,n,a,o){try{t.componentDidUpdate(n,a,o)}catch(l){Be(e,e.return,l)}}},CT=DT.react_stack_bottom_frame.bind(DT),MT={react_stack_bottom_frame:function(e,t){var n=t.stack;e.componentDidCatch(t.value,{componentStack:n!==null?n:""})}},YD=MT.react_stack_bottom_frame.bind(MT),jT={react_stack_bottom_frame:function(e,t,n){try{n.componentWillUnmount()}catch(a){Be(e,t,a)}}},UT=jT.react_stack_bottom_frame.bind(jT),NT={react_stack_bottom_frame:function(e){e.resourceKind!=null&&console.error("Expected only SimpleEffects when enableUseEffectCRUDOverload is disabled, got %s",e.resourceKind);var t=e.create;return e=e.inst,t=t(),e.destroy=t}},XD=NT.react_stack_bottom_frame.bind(NT),kT={react_stack_bottom_frame:function(e,t,n){try{n()}catch(a){Be(e,t,a)}}},ID=kT.react_stack_bottom_frame.bind(kT),HT={react_stack_bottom_frame:function(e){var t=e._init;return t(e._payload)}},Io=HT.react_stack_bottom_frame.bind(HT),er=null,Gs=0,ve=null,Wm,LT=Wm=!1,BT={},VT={},PT={};b=function(e,t,n){if(n!==null&&typeof n=="object"&&n._store&&(!n._store.validated&&n.key==null||n._store.validated===2)){if(typeof n._store!="object")throw Error("React Component in warnForMissingKey should have a _store. This error is likely caused by a bug in React. Please file an issue.");n._store.validated=1;var a=ee(e),o=a||"null";if(!BT[o]){BT[o]=!0,n=n._owner,e=e._debugOwner;var l="";e&&typeof e.tag=="number"&&(o=ee(e))&&(l=`

Check the render method of \``+o+"`."),l||a&&(l=`

Check the top-level render call using <`+a+">.");var c="";n!=null&&e!==n&&(a=null,typeof n.tag=="number"?a=ee(n):typeof n.name=="string"&&(a=n.name),a&&(c=" It was passed a child from "+a+".")),W(t,function(){console.error('Each child in a list should have a unique "key" prop.%s%s See https://react.dev/link/warning-keys for more information.',l,c)})}}};var tr=h0(!0),$T=h0(!1),Kn=at(null),Na=null,nr=1,Ys=2,gt=at(0),qT={},GT=new Set,YT=new Set,XT=new Set,IT=new Set,QT=new Set,ZT=new Set,KT=new Set,JT=new Set,WT=new Set,FT=new Set;Object.freeze(qT);var Fm={enqueueSetState:function(e,t,n){e=e._reactInternals;var a=bn(e),o=Do(a);o.payload=t,n!=null&&(cp(n),o.callback=n),t=Co(e,o,a),t!==null&&(it(t,e,a),es(t,e,a)),Eo(e,a)},enqueueReplaceState:function(e,t,n){e=e._reactInternals;var a=bn(e),o=Do(a);o.tag=gT,o.payload=t,n!=null&&(cp(n),o.callback=n),t=Co(e,o,a),t!==null&&(it(t,e,a),es(t,e,a)),Eo(e,a)},enqueueForceUpdate:function(e,t){e=e._reactInternals;var n=bn(e),a=Do(n);a.tag=bT,t!=null&&(cp(t),a.callback=t),t=Co(e,a,n),t!==null&&(it(t,e,n),es(t,e,n)),Y!==null&&typeof Y.markForceUpdateScheduled=="function"&&Y.markForceUpdateScheduled(e,n)}},ev=typeof reportError=="function"?reportError:function(e){if(typeof window=="object"&&typeof window.ErrorEvent=="function"){var t=new window.ErrorEvent("error",{bubbles:!0,cancelable:!0,message:typeof e=="object"&&e!==null&&typeof e.message=="string"?String(e.message):String(e),error:e});if(!window.dispatchEvent(t))return}else if(typeof process=="object"&&typeof process.emit=="function"){process.emit("uncaughtException",e);return}console.error(e)},ar=null,tv=null,e1=Error("This is not a real error. It's an implementation detail of React's selective hydration feature. If this leaks into userspace, it's a bug in React. Please file an issue."),Tt=!1,t1={},n1={},a1={},o1={},or=!1,i1={},nv={},av={dehydrated:null,treeContext:null,retryLane:0,hydrationErrors:null},l1=!1,r1=null;r1=new Set;var so=!1,lt=!1,ov=!1,s1=typeof WeakSet=="function"?WeakSet:Set,Ot=null,ir=null,lr=null,ht=null,nn=!1,ha=null,Xs=8192,QD={getCacheForType:function(e){var t=Je(vt),n=t.data.get(e);return n===void 0&&(n=e(),t.data.set(e,n)),n},getOwner:function(){return Sn}};if(typeof Symbol=="function"&&Symbol.for){var Is=Symbol.for;Is("selector.component"),Is("selector.has_pseudo_class"),Is("selector.role"),Is("selector.test_id"),Is("selector.text")}var ZD=[],KD=typeof WeakMap=="function"?WeakMap:Map,Tn=0,Xt=2,pa=4,uo=0,Qs=1,rr=2,iv=3,qi=4,Mf=6,u1=5,je=Tn,qe=null,Se=null,Oe=0,an=0,Zs=1,Gi=2,Ks=3,c1=4,lv=5,sr=6,Js=7,rv=8,Yi=9,Ue=an,On=null,Qo=!1,ur=!1,sv=!1,ka=0,nt=uo,Zo=0,Ko=0,uv=0,En=0,Xi=0,Ws=null,It=null,jf=!1,cv=0,f1=300,Uf=1/0,d1=500,Fs=null,Jo=null,JD=0,WD=1,FD=2,Ii=0,h1=1,p1=2,m1=3,eC=4,fv=5,Ut=0,Wo=null,cr=null,Fo=0,dv=0,hv=null,v1=null,tC=50,eu=0,pv=null,mv=!1,Nf=!1,nC=50,Qi=0,tu=null,fr=!1,kf=null,y1=!1,g1=new Set,aC={},Hf=null,dr=null,vv=!1,yv=!1,Lf=!1,gv=!1,Zi=0,bv={};(function(){for(var e=0;e<km.length;e++){var t=km[e],n=t.toLowerCase();t=t[0].toUpperCase()+t.slice(1),ra(n,"on"+t)}ra(nT,"onAnimationEnd"),ra(aT,"onAnimationIteration"),ra(oT,"onAnimationStart"),ra("dblclick","onDoubleClick"),ra("focusin","onFocus"),ra("focusout","onBlur"),ra(UD,"onTransitionRun"),ra(ND,"onTransitionStart"),ra(kD,"onTransitionCancel"),ra(iT,"onTransitionEnd")})(),G("onMouseEnter",["mouseout","mouseover"]),G("onMouseLeave",["mouseout","mouseover"]),G("onPointerEnter",["pointerout","pointerover"]),G("onPointerLeave",["pointerout","pointerover"]),P("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),P("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),P("onBeforeInput",["compositionend","keypress","textInput","paste"]),P("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),P("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),P("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var nu="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),_v=new Set("beforetoggle cancel close invalid load scroll scrollend toggle".split(" ").concat(nu)),Bf="_reactListening"+Math.random().toString(36).slice(2),b1=!1,_1=!1,Vf=!1,S1=!1,Pf=!1,$f=!1,T1=!1,qf={},oC=/\r\n?/g,iC=/\u0000|\uFFFD/g,Ki="http://www.w3.org/1999/xlink",Sv="http://www.w3.org/XML/1998/namespace",lC="javascript:throw new Error('React form unexpectedly submitted.')",rC="suppressHydrationWarning",Gf="$",Yf="/$",co="$?",au="$!",sC=1,uC=2,cC=4,Tv="F!",O1="F",E1="complete",fC="style",fo=0,hr=1,Xf=2,Ov=null,Ev=null,w1={dialog:!0,webview:!0},wv=null,A1=typeof setTimeout=="function"?setTimeout:void 0,dC=typeof clearTimeout=="function"?clearTimeout:void 0,Ji=-1,x1=typeof Promise=="function"?Promise:void 0,hC=typeof queueMicrotask=="function"?queueMicrotask:typeof x1<"u"?function(e){return x1.resolve(null).then(e).catch(WR)}:A1,Av=null,Wi=0,ou=1,R1=2,z1=3,Jn=4,Wn=new Map,D1=new Set,ho=Me.d;Me.d={f:function(){var e=ho.f(),t=wl();return e||t},r:function(e){var t=la(e);t!==null&&t.tag===5&&t.type==="form"?r0(t):ho.r(e)},D:function(e){ho.D(e),K_("dns-prefetch",e,null)},C:function(e,t){ho.C(e,t),K_("preconnect",e,t)},L:function(e,t,n){ho.L(e,t,n);var a=pr;if(a&&e&&t){var o='link[rel="preload"][as="'+Ln(t)+'"]';t==="image"&&n&&n.imageSrcSet?(o+='[imagesrcset="'+Ln(n.imageSrcSet)+'"]',typeof n.imageSizes=="string"&&(o+='[imagesizes="'+Ln(n.imageSizes)+'"]')):o+='[href="'+Ln(e)+'"]';var l=o;switch(t){case"style":l=zl(e);break;case"script":l=Dl(e)}Wn.has(l)||(e=ge({rel:"preload",href:t==="image"&&n&&n.imageSrcSet?void 0:e,as:t},n),Wn.set(l,e),a.querySelector(o)!==null||t==="style"&&a.querySelector(ys(l))||t==="script"&&a.querySelector(gs(l))||(t=a.createElement("link"),Dt(t,"link",e),w(t),a.head.appendChild(t)))}},m:function(e,t){ho.m(e,t);var n=pr;if(n&&e){var a=t&&typeof t.as=="string"?t.as:"script",o='link[rel="modulepreload"][as="'+Ln(a)+'"][href="'+Ln(e)+'"]',l=o;switch(a){case"audioworklet":case"paintworklet":case"serviceworker":case"sharedworker":case"worker":case"script":l=Dl(e)}if(!Wn.has(l)&&(e=ge({rel:"modulepreload",href:e},t),Wn.set(l,e),n.querySelector(o)===null)){switch(a){case"audioworklet":case"paintworklet":case"serviceworker":case"sharedworker":case"worker":case"script":if(n.querySelector(gs(l)))return}a=n.createElement("link"),Dt(a,"link",e),w(a),n.head.appendChild(a)}}},X:function(e,t){ho.X(e,t);var n=pr;if(n&&e){var a=m(n).hoistableScripts,o=Dl(e),l=a.get(o);l||(l=n.querySelector(gs(o)),l||(e=ge({src:e,async:!0},t),(t=Wn.get(o))&&Fp(e,t),l=n.createElement("script"),w(l),Dt(l,"link",e),n.head.appendChild(l)),l={type:"script",instance:l,count:1,state:null},a.set(o,l))}},S:function(e,t,n){ho.S(e,t,n);var a=pr;if(a&&e){var o=m(a).hoistableStyles,l=zl(e);t=t||"default";var c=o.get(l);if(!c){var d={loading:Wi,preload:null};if(c=a.querySelector(ys(l)))d.loading=ou|Jn;else{e=ge({rel:"stylesheet",href:e,"data-precedence":t},n),(n=Wn.get(l))&&Wp(e,n);var v=c=a.createElement("link");w(v),Dt(v,"link",e),v._p=new Promise(function(y,R){v.onload=y,v.onerror=R}),v.addEventListener("load",function(){d.loading|=ou}),v.addEventListener("error",function(){d.loading|=R1}),d.loading|=Jn,Fc(c,t,a)}c={type:"stylesheet",instance:c,count:1,state:d},o.set(l,c)}}},M:function(e,t){ho.M(e,t);var n=pr;if(n&&e){var a=m(n).hoistableScripts,o=Dl(e),l=a.get(o);l||(l=n.querySelector(gs(o)),l||(e=ge({src:e,async:!0,type:"module"},t),(t=Wn.get(o))&&Fp(e,t),l=n.createElement("script"),w(l),Dt(l,"link",e),n.head.appendChild(l)),l={type:"script",instance:l,count:1,state:null},a.set(o,l))}}};var pr=typeof document>"u"?null:document,If=null,iu=null,xv=null,Qf=null,Fi=Mz,lu={$$typeof:xa,Provider:null,Consumer:null,_currentValue:Fi,_currentValue2:Fi,_threadCount:0},C1="%c%s%c ",M1="background: #e6e6e6;background: light-dark(rgba(0,0,0,0.1), rgba(255,255,255,0.25));color: #000000;color: light-dark(#000000, #ffffff);border-radius: 2px",j1="",Zf=" ",pC=Function.prototype.bind,U1=!1,N1=null,k1=null,H1=null,L1=null,B1=null,V1=null,P1=null,$1=null,q1=null;N1=function(e,t,n,a){t=i(e,t),t!==null&&(n=r(t.memoizedState,n,0,a),t.memoizedState=n,t.baseState=n,e.memoizedProps=ge({},e.memoizedProps),n=Jt(e,2),n!==null&&it(n,e,2))},k1=function(e,t,n){t=i(e,t),t!==null&&(n=f(t.memoizedState,n,0),t.memoizedState=n,t.baseState=n,e.memoizedProps=ge({},e.memoizedProps),n=Jt(e,2),n!==null&&it(n,e,2))},H1=function(e,t,n,a){t=i(e,t),t!==null&&(n=u(t.memoizedState,n,a),t.memoizedState=n,t.baseState=n,e.memoizedProps=ge({},e.memoizedProps),n=Jt(e,2),n!==null&&it(n,e,2))},L1=function(e,t,n){e.pendingProps=r(e.memoizedProps,t,0,n),e.alternate&&(e.alternate.pendingProps=e.pendingProps),t=Jt(e,2),t!==null&&it(t,e,2)},B1=function(e,t){e.pendingProps=f(e.memoizedProps,t,0),e.alternate&&(e.alternate.pendingProps=e.pendingProps),t=Jt(e,2),t!==null&&it(t,e,2)},V1=function(e,t,n){e.pendingProps=u(e.memoizedProps,t,n),e.alternate&&(e.alternate.pendingProps=e.pendingProps),t=Jt(e,2),t!==null&&it(t,e,2)},P1=function(e){var t=Jt(e,2);t!==null&&it(t,e,2)},$1=function(e){p=e},q1=function(e){h=e};var Kf=!0,Jf=null,Rv=!1,ei=null,ti=null,ni=null,ru=new Map,su=new Map,ai=[],mC="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset".split(" "),Wf=null;if(af.prototype.render=lm.prototype.render=function(e){var t=this._internalRoot;if(t===null)throw Error("Cannot update an unmounted root.");var n=arguments;typeof n[1]=="function"?console.error("does not support the second callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."):ne(n[1])?console.error("You passed a container to the second argument of root.render(...). You don't need to pass it again since you already passed it to create the root."):typeof n[1]<"u"&&console.error("You passed a second argument to root.render(...) but it only accepts one argument."),n=e;var a=t.current,o=bn(a);tm(a,o,n,t,null,null)},af.prototype.unmount=lm.prototype.unmount=function(){var e=arguments;if(typeof e[0]=="function"&&console.error("does not support a callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."),e=this._internalRoot,e!==null){this._internalRoot=null;var t=e.containerInfo;(je&(Xt|pa))!==Tn&&console.error("Attempted to synchronously unmount a root while React was already rendering. React cannot finish unmounting the root until the current render has completed, which may lead to a race condition."),tm(e.current,2,null,e,null,null),wl(),t[$o]=null}},af.prototype.unstable_scheduleHydration=function(e){if(e){var t=Vr();e={blockedOn:null,target:e,priority:t};for(var n=0;n<ai.length&&t!==0&&t<ai[n].priority;n++);ai.splice(n,0,e),n===0&&uS(e)}},function(){var e=rm.version;if(e!=="19.1.1")throw Error(`Incompatible React versions: The "react" and "react-dom" packages must have the exact same version. Instead got:
  - react:      `+(e+`
  - react-dom:  19.1.1
Learn more: https://react.dev/warnings/version-mismatch`))}(),typeof Map=="function"&&Map.prototype!=null&&typeof Map.prototype.forEach=="function"&&typeof Set=="function"&&Set.prototype!=null&&typeof Set.prototype.clear=="function"&&typeof Set.prototype.forEach=="function"||console.error("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://react.dev/link/react-polyfills"),Me.findDOMNode=function(e){var t=e._reactInternals;if(t===void 0)throw typeof e.render=="function"?Error("Unable to find node on an unmounted component."):(e=Object.keys(e).join(","),Error("Argument appears to not be a ReactComponent. Keys: "+e));return e=Ne(t),e=e!==null?Ze(e):null,e=e===null?null:e.stateNode,e},!function(){var e={bundleType:1,version:"19.1.1",rendererPackageName:"react-dom",currentDispatcherRef:D,reconcilerVersion:"19.1.1"};return e.overrideHookState=N1,e.overrideHookStateDeletePath=k1,e.overrideHookStateRenamePath=H1,e.overrideProps=L1,e.overridePropsDeletePath=B1,e.overridePropsRenamePath=V1,e.scheduleUpdate=P1,e.setErrorHandler=$1,e.setSuspenseHandler=q1,e.scheduleRefresh=I,e.scheduleRoot=L,e.setRefreshHandler=te,e.getCurrentFiber=Sz,e.getLaneLabelMap=Tz,e.injectProfilingHooks=mi,ut(e)}()&&Ma&&window.top===window.self&&(-1<navigator.userAgent.indexOf("Chrome")&&navigator.userAgent.indexOf("Edge")===-1||-1<navigator.userAgent.indexOf("Firefox"))){var G1=window.location.protocol;/^(https?|file):$/.test(G1)&&console.info("%cDownload the React DevTools for a better development experience: https://react.dev/link/react-devtools"+(G1==="file:"?`
You might need to use a local HTTP server (instead of file://): https://react.dev/link/react-devtools-faq`:""),"font-weight:bold")}br.createRoot=function(e,t){if(!ne(e))throw Error("Target container is not a DOM element.");dS(e);var n=!1,a="",o=y0,l=g0,c=b0,d=null;return t!=null&&(t.hydrate?console.warn("hydrate through createRoot is deprecated. Use ReactDOMClient.hydrateRoot(container, <App />) instead."):typeof t=="object"&&t!==null&&t.$$typeof===Lo&&console.error(`You passed a JSX element to createRoot. You probably meant to call root.render instead. Example usage:

  let root = createRoot(domContainer);
  root.render(<App />);`),t.unstable_strictMode===!0&&(n=!0),t.identifierPrefix!==void 0&&(a=t.identifierPrefix),t.onUncaughtError!==void 0&&(o=t.onUncaughtError),t.onCaughtError!==void 0&&(l=t.onCaughtError),t.onRecoverableError!==void 0&&(c=t.onRecoverableError),t.unstable_transitionCallbacks!==void 0&&(d=t.unstable_transitionCallbacks)),t=aS(e,1,!1,null,null,n,a,o,l,c,d,null),e[$o]=t.current,$p(e),new lm(t)},br.hydrateRoot=function(e,t,n){if(!ne(e))throw Error("Target container is not a DOM element.");dS(e),t===void 0&&console.error("Must provide initial children as second argument to hydrateRoot. Example usage: hydrateRoot(domContainer, <App />)");var a=!1,o="",l=y0,c=g0,d=b0,v=null,y=null;return n!=null&&(n.unstable_strictMode===!0&&(a=!0),n.identifierPrefix!==void 0&&(o=n.identifierPrefix),n.onUncaughtError!==void 0&&(l=n.onUncaughtError),n.onCaughtError!==void 0&&(c=n.onCaughtError),n.onRecoverableError!==void 0&&(d=n.onRecoverableError),n.unstable_transitionCallbacks!==void 0&&(v=n.unstable_transitionCallbacks),n.formState!==void 0&&(y=n.formState)),t=aS(e,1,!0,t,n??null,a,o,l,c,d,v,y),t.context=oS(null),n=t.current,a=bn(n),a=Hr(a),o=Do(a),o.callback=null,Co(n,o,a),n=a,t.current.lanes=n,Ao(t,n),wa(t),e[$o]=t.current,$p(e),new af(t)},br.version="19.1.1",typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())}(),br}var Bv;function W1(){return Bv||(Bv=1,td.exports=J1()),td.exports}var F1=W1(),K=gr();const Vv=mr(K);var Qt={NODE_ENV:'"production"',WEBPACK_ENV:'"production"'},eO={0:"Invalid value for configuration 'enforceActions', expected 'never', 'always' or 'observed'",1:function(r,u){return"Cannot apply '"+r+"' to '"+u.toString()+"': Field not found."},5:"'keys()' can only be used on observable objects, arrays, sets and maps",6:"'values()' can only be used on observable objects, arrays, sets and maps",7:"'entries()' can only be used on observable objects, arrays and maps",8:"'set()' can only be used on observable objects, arrays and maps",9:"'remove()' can only be used on observable objects, arrays and maps",10:"'has()' can only be used on observable objects, arrays and maps",11:"'get()' can only be used on observable objects, arrays and maps",12:"Invalid annotation",13:"Dynamic observable objects cannot be frozen. If you're passing observables to 3rd party component/function that calls Object.freeze, pass copy instead: toJS(observable)",14:"Intercept handlers should return nothing or a change object",15:"Observable arrays cannot be frozen. If you're passing observables to 3rd party component/function that calls Object.freeze, pass copy instead: toJS(observable)",16:"Modification exception: the internal structure of an observable array was changed.",17:function(r,u){return"[mobx.array] Index out of bounds, "+r+" is larger than "+u},18:"mobx.map requires Map polyfill for the current browser. Check babel-polyfill or core-js/es6/map.js",19:function(r){return"Cannot initialize from classes that inherit from Map: "+r.constructor.name},20:function(r){return"Cannot initialize map from "+r},21:function(r){return"Cannot convert to map from '"+r+"'"},22:"mobx.set requires Set polyfill for the current browser. Check babel-polyfill or core-js/es6/set.js",23:"It is not possible to get index atoms from arrays",24:function(r){return"Cannot obtain administration from "+r},25:function(r,u){return"the entry '"+r+"' does not exist in the observable map '"+u+"'"},26:"please specify a property",27:function(r,u){return"no observable property '"+r.toString()+"' found on the observable object '"+u+"'"},28:function(r){return"Cannot obtain atom from "+r},29:"Expecting some object",30:"invalid action stack. did you forget to finish an action?",31:"missing option for computed: get",32:function(r,u){return"Cycle detected in computation "+r+": "+u},33:function(r){return"The setter of computed value '"+r+"' is trying to update itself. Did you intend to update an _observable_ value, instead of the computed property?"},34:function(r){return"[ComputedValue '"+r+"'] It is not possible to assign a new value to a computed value."},35:"There are multiple, different versions of MobX active. Make sure MobX is loaded only once or use `configure({ isolateGlobalState: true })`",36:"isolateGlobalState should be called before MobX is running any reactions",37:function(r){return"[mobx] `observableArray."+r+"()` mutates the array in-place, which is not allowed inside a derivation. Use `array.slice()."+r+"()` instead"},38:"'ownKeys()' can only be used on observable objects",39:"'defineProperty()' can only be used on observable objects"},tO=eO;function X(i){for(var r=arguments.length,u=new Array(r>1?r-1:0),s=1;s<r;s++)u[s-1]=arguments[s];{var f=typeof i=="string"?i:tO[i];throw typeof f=="function"&&(f=f.apply(null,u)),new Error("[MobX] "+f)}}var nO={};function uu(){return typeof globalThis<"u"?globalThis:typeof window<"u"?window:typeof global<"u"?global:typeof self<"u"?self:nO}var Pv=Object.assign,cu=Object.getOwnPropertyDescriptor,ma=Object.defineProperty,fu=Object.prototype,du=[];Object.freeze(du);var $v={};Object.freeze($v);var aO=typeof Proxy<"u",oO=Object.toString();function qv(){aO||X("`Proxy` objects are not available in the current environment. Please configure MobX to enable a fallback implementation.`")}function _r(i){B.verifyProxies&&X("MobX is currently configured to be able to run in ES5 mode, but in ES5 MobX won't be able to "+i)}function Fn(){return++B.mobxGuid}function id(i){var r=!1;return function(){if(!r)return r=!0,i.apply(this,arguments)}}var el=function(){};function pt(i){return typeof i=="function"}function oi(i){var r=typeof i;switch(r){case"string":case"symbol":case"number":return!0}return!1}function hu(i){return i!==null&&typeof i=="object"}function on(i){if(!hu(i))return!1;var r=Object.getPrototypeOf(i);if(r==null)return!0;var u=Object.hasOwnProperty.call(r,"constructor")&&r.constructor;return typeof u=="function"&&u.toString()===oO}function Gv(i){var r=i?.constructor;return r?r.name==="GeneratorFunction"||r.displayName==="GeneratorFunction":!1}function Sr(i,r,u){ma(i,r,{enumerable:!1,writable:!0,configurable:!0,value:u})}function Yv(i,r,u){ma(i,r,{enumerable:!1,writable:!1,configurable:!0,value:u})}function ii(i,r){var u="isMobX"+i;return r.prototype[u]=!0,function(s){return hu(s)&&s[u]===!0}}function tl(i){return i!=null&&Object.prototype.toString.call(i)==="[object Map]"}function iO(i){var r=Object.getPrototypeOf(i),u=Object.getPrototypeOf(r),s=Object.getPrototypeOf(u);return s===null}function Ha(i){return i!=null&&Object.prototype.toString.call(i)==="[object Set]"}var Xv=typeof Object.getOwnPropertySymbols<"u";function lO(i){var r=Object.keys(i);if(!Xv)return r;var u=Object.getOwnPropertySymbols(i);return u.length?[].concat(r,u.filter(function(s){return fu.propertyIsEnumerable.call(i,s)})):r}var nl=typeof Reflect<"u"&&Reflect.ownKeys?Reflect.ownKeys:Xv?function(i){return Object.getOwnPropertyNames(i).concat(Object.getOwnPropertySymbols(i))}:Object.getOwnPropertyNames;function ld(i){return typeof i=="string"?i:typeof i=="symbol"?i.toString():new String(i).toString()}function Iv(i){return i===null?null:typeof i=="object"?""+i:i}function wn(i,r){return fu.hasOwnProperty.call(i,r)}var rO=Object.getOwnPropertyDescriptors||function(r){var u={};return nl(r).forEach(function(s){u[s]=cu(r,s)}),u};function ln(i,r){return!!(i&r)}function rn(i,r,u){return u?i|=r:i&=~r,i}function Qv(i,r){(r==null||r>i.length)&&(r=i.length);for(var u=0,s=Array(r);u<r;u++)s[u]=i[u];return s}function sO(i,r){for(var u=0;u<r.length;u++){var s=r[u];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(i,cO(s.key),s)}}function al(i,r,u){return r&&sO(i.prototype,r),Object.defineProperty(i,"prototype",{writable:!1}),i}function ol(i,r){var u=typeof Symbol<"u"&&i[Symbol.iterator]||i["@@iterator"];if(u)return(u=u.call(i)).next.bind(u);if(Array.isArray(i)||(u=fO(i))||r){u&&(i=u);var s=0;return function(){return s>=i.length?{done:!0}:{done:!1,value:i[s++]}}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function La(){return La=Object.assign?Object.assign.bind():function(i){for(var r=1;r<arguments.length;r++){var u=arguments[r];for(var s in u)({}).hasOwnProperty.call(u,s)&&(i[s]=u[s])}return i},La.apply(null,arguments)}function Zv(i,r){i.prototype=Object.create(r.prototype),i.prototype.constructor=i,rd(i,r)}function rd(i,r){return rd=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(u,s){return u.__proto__=s,u},rd(i,r)}function uO(i,r){if(typeof i!="object"||!i)return i;var u=i[Symbol.toPrimitive];if(u!==void 0){var s=u.call(i,r);if(typeof s!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(i)}function cO(i){var r=uO(i,"string");return typeof r=="symbol"?r:r+""}function fO(i,r){if(i){if(typeof i=="string")return Qv(i,r);var u={}.toString.call(i).slice(8,-1);return u==="Object"&&i.constructor&&(u=i.constructor.name),u==="Map"||u==="Set"?Array.from(i):u==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(u)?Qv(i,r):void 0}}var Zt=Symbol("mobx-stored-annotations");function va(i){function r(u,s){if(Or(s))return i.decorate_20223_(u,s);Tr(u,s,i)}return Object.assign(r,i)}function Tr(i,r,u){if(wn(i,Zt)||Sr(i,Zt,La({},i[Zt])),yu(u)&&!wn(i[Zt],r)){var s=i.constructor.name+".prototype."+r.toString();X("'"+s+"' is decorated with 'override', but no such decorated member was found on prototype.")}dO(i,u,r),yu(u)||(i[Zt][r]=u)}function dO(i,r,u){if(!yu(r)&&wn(i[Zt],u)){var s=i.constructor.name+".prototype."+u.toString(),f=i[Zt][u].annotationType_,h=r.annotationType_;X("Cannot apply '@"+h+"' to '"+s+"':"+(`
The field is already decorated with '@`+f+"'.")+`
Re-decorating fields is not allowed.
Use '@override' decorator for methods overridden by subclass.`)}}function hO(i){return wn(i,Zt)||Sr(i,Zt,La({},i[Zt])),i[Zt]}function Or(i){return typeof i=="object"&&typeof i.kind=="string"}function pu(i,r){r.includes(i.kind)||X("The decorator applied to '"+String(i.name)+"' cannot be used on a "+i.kind+" element")}var re=Symbol("mobx administration"),mo=function(){function i(u){u===void 0&&(u="Atom@"+Fn()),this.name_=void 0,this.flags_=0,this.observers_=new Set,this.lastAccessedBy_=0,this.lowestObserverState_=ze.NOT_TRACKING_,this.onBOL=void 0,this.onBUOL=void 0,this.name_=u}var r=i.prototype;return r.onBO=function(){this.onBOL&&this.onBOL.forEach(function(s){return s()})},r.onBUO=function(){this.onBUOL&&this.onBUOL.forEach(function(s){return s()})},r.reportObserved=function(){return my(this)},r.reportChanged=function(){Rn(),vy(this),zn()},r.toString=function(){return this.name_},al(i,[{key:"isBeingObserved",get:function(){return ln(this.flags_,i.isBeingObservedMask_)},set:function(s){this.flags_=rn(this.flags_,i.isBeingObservedMask_,s)}},{key:"isPendingUnobservation",get:function(){return ln(this.flags_,i.isPendingUnobservationMask_)},set:function(s){this.flags_=rn(this.flags_,i.isPendingUnobservationMask_,s)}},{key:"diffValue",get:function(){return ln(this.flags_,i.diffValueMask_)?1:0},set:function(s){this.flags_=rn(this.flags_,i.diffValueMask_,s===1)}}])}();mo.isBeingObservedMask_=1,mo.isPendingUnobservationMask_=2,mo.diffValueMask_=4;var sd=ii("Atom",mo);function Kv(i,r,u){r===void 0&&(r=el),u===void 0&&(u=el);var s=new mo(i);return r!==el&&EE(s,r),u!==el&&Ey(s,u),s}function pO(i,r){return Iy(i,r)}function mO(i,r){return Object.is?Object.is(i,r):i===r?i!==0||1/i===1/r:i!==i&&r!==r}var mu={structural:pO,default:mO};function li(i,r,u){return Rr(i)?i:Array.isArray(i)?Et.array(i,{name:u}):on(i)?Et.object(i,void 0,{name:u}):tl(i)?Et.map(i,{name:u}):Ha(i)?Et.set(i,{name:u}):typeof i=="function"&&!ll(i)&&!xr(i)?Gv(i)?rl(i):Ar(u,i):i}function vO(i,r,u){if(i==null||sl(i)||Cu(i)||yo(i)||ga(i))return i;if(Array.isArray(i))return Et.array(i,{name:u,deep:!1});if(on(i))return Et.object(i,void 0,{name:u,deep:!1});if(tl(i))return Et.map(i,{name:u,deep:!1});if(Ha(i))return Et.set(i,{name:u,deep:!1});X("The shallow modifier / decorator can only used in combination with arrays, objects, maps and sets")}function vu(i){return i}function yO(i,r){return Rr(i)&&X("observable.struct should not be used with observable values"),Iy(i,r)?r:i}var gO="override";function yu(i){return i.annotationType_===gO}function Er(i,r){return{annotationType_:i,options_:r,make_:bO,extend_:_O,decorate_20223_:SO}}function bO(i,r,u,s){var f;if((f=this.options_)!=null&&f.bound)return this.extend_(i,r,u,!1)===null?0:1;if(s===i.target_)return this.extend_(i,r,u,!1)===null?0:2;if(ll(u.value))return 1;var h=Jv(i,this,r,u,!1);return ma(s,r,h),2}function _O(i,r,u,s){var f=Jv(i,this,r,u);return i.defineProperty_(r,f,s)}function SO(i,r){pu(r,["method","field"]);var u=r.kind,s=r.name,f=r.addInitializer,h=this,p=function(N){var U,x,O,L;return ri((U=(x=h.options_)==null?void 0:x.name)!=null?U:s.toString(),N,(O=(L=h.options_)==null?void 0:L.autoAction)!=null?O:!1)};if(u=="field")return function(g){var N,U=g;return ll(U)||(U=p(U)),(N=h.options_)!=null&&N.bound&&(U=U.bind(this),U.isMobxAction=!0),U};if(u=="method"){var b;return ll(i)||(i=p(i)),(b=this.options_)!=null&&b.bound&&f(function(){var g=this,N=g[s].bind(g);N.isMobxAction=!0,g[s]=N}),i}X("Cannot apply '"+h.annotationType_+"' to '"+String(s)+"' (kind: "+u+"):"+(`
'`+h.annotationType_+"' can only be used on properties with a function value."))}function TO(i,r,u,s){var f=r.annotationType_,h=s.value;pt(h)||X("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' can only be used on properties with a function value."))}function Jv(i,r,u,s,f){var h,p,b,g,N,U,x;f===void 0&&(f=B.safeDescriptors),TO(i,r,u,s);var O=s.value;if((h=r.options_)!=null&&h.bound){var L;O=O.bind((L=i.proxy_)!=null?L:i.target_)}return{value:ri((p=(b=r.options_)==null?void 0:b.name)!=null?p:u.toString(),O,(g=(N=r.options_)==null?void 0:N.autoAction)!=null?g:!1,(U=r.options_)!=null&&U.bound?(x=i.proxy_)!=null?x:i.target_:void 0),configurable:f?i.isPlainObject_:!0,enumerable:!1,writable:!f}}function Wv(i,r){return{annotationType_:i,options_:r,make_:OO,extend_:EO,decorate_20223_:wO}}function OO(i,r,u,s){var f;if(s===i.target_)return this.extend_(i,r,u,!1)===null?0:2;if((f=this.options_)!=null&&f.bound&&(!wn(i.target_,r)||!xr(i.target_[r]))&&this.extend_(i,r,u,!1)===null)return 0;if(xr(u.value))return 1;var h=Fv(i,this,r,u,!1,!1);return ma(s,r,h),2}function EO(i,r,u,s){var f,h=Fv(i,this,r,u,(f=this.options_)==null?void 0:f.bound);return i.defineProperty_(r,h,s)}function wO(i,r){var u;pu(r,["method"]);var s=r.name,f=r.addInitializer;return xr(i)||(i=rl(i)),(u=this.options_)!=null&&u.bound&&f(function(){var h=this,p=h[s].bind(h);p.isMobXFlow=!0,h[s]=p}),i}function AO(i,r,u,s){var f=r.annotationType_,h=s.value;pt(h)||X("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' can only be used on properties with a generator function value."))}function Fv(i,r,u,s,f,h){h===void 0&&(h=B.safeDescriptors),AO(i,r,u,s);var p=s.value;if(xr(p)||(p=rl(p)),f){var b;p=p.bind((b=i.proxy_)!=null?b:i.target_),p.isMobXFlow=!0}return{value:p,configurable:h?i.isPlainObject_:!0,enumerable:!1,writable:!h}}function ud(i,r){return{annotationType_:i,options_:r,make_:xO,extend_:RO,decorate_20223_:zO}}function xO(i,r,u){return this.extend_(i,r,u,!1)===null?0:1}function RO(i,r,u,s){return DO(i,this,r,u),i.defineComputedProperty_(r,La({},this.options_,{get:u.get,set:u.set}),s)}function zO(i,r){pu(r,["getter"]);var u=this,s=r.name,f=r.addInitializer;return f(function(){var h=di(this)[re],p=La({},u.options_,{get:i,context:this});p.name||(p.name=h.name_+"."+s.toString()),h.values_.set(s,new An(p))}),function(){return this[re].getObservablePropValue_(s)}}function DO(i,r,u,s){var f=r.annotationType_,h=s.get;h||X("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' can only be used on getter(+setter) properties."))}function gu(i,r){return{annotationType_:i,options_:r,make_:CO,extend_:MO,decorate_20223_:jO}}function CO(i,r,u){return this.extend_(i,r,u,!1)===null?0:1}function MO(i,r,u,s){var f,h;return UO(i,this,r,u),i.defineObservableProperty_(r,u.value,(f=(h=this.options_)==null?void 0:h.enhancer)!=null?f:li,s)}function jO(i,r){{if(r.kind==="field")throw X("Please use `@observable accessor "+String(r.name)+"` instead of `@observable "+String(r.name)+"`");pu(r,["accessor"])}var u=this,s=r.kind,f=r.name,h=new WeakSet;function p(b,g){var N,U,x=di(b)[re],O=new si(g,(N=(U=u.options_)==null?void 0:U.enhancer)!=null?N:li,x.name_+"."+f.toString(),!1);x.values_.set(f,O),h.add(b)}if(s=="accessor")return{get:function(){return h.has(this)||p(this,i.get.call(this)),this[re].getObservablePropValue_(f)},set:function(g){return h.has(this)||p(this,g),this[re].setObservablePropValue_(f,g)},init:function(g){return h.has(this)||p(this,g),g}}}function UO(i,r,u,s){var f=r.annotationType_;"value"in s||X("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' cannot be used on getter/setter properties"))}var NO="true",kO=ey();function ey(i){return{annotationType_:NO,options_:i,make_:HO,extend_:LO,decorate_20223_:BO}}function HO(i,r,u,s){var f,h;if(u.get)return Su.make_(i,r,u,s);if(u.set){var p=ri(r.toString(),u.set);return s===i.target_?i.defineProperty_(r,{configurable:B.safeDescriptors?i.isPlainObject_:!0,set:p})===null?0:2:(ma(s,r,{configurable:!0,set:p}),2)}if(s!==i.target_&&typeof u.value=="function"){var b;if(Gv(u.value)){var g,N=(g=this.options_)!=null&&g.autoBind?rl.bound:rl;return N.make_(i,r,u,s)}var U=(b=this.options_)!=null&&b.autoBind?Ar.bound:Ar;return U.make_(i,r,u,s)}var x=((f=this.options_)==null?void 0:f.deep)===!1?Et.ref:Et;if(typeof u.value=="function"&&(h=this.options_)!=null&&h.autoBind){var O;u.value=u.value.bind((O=i.proxy_)!=null?O:i.target_)}return x.make_(i,r,u,s)}function LO(i,r,u,s){var f,h;if(u.get)return Su.extend_(i,r,u,s);if(u.set)return i.defineProperty_(r,{configurable:B.safeDescriptors?i.isPlainObject_:!0,set:ri(r.toString(),u.set)},s);if(typeof u.value=="function"&&(f=this.options_)!=null&&f.autoBind){var p;u.value=u.value.bind((p=i.proxy_)!=null?p:i.target_)}var b=((h=this.options_)==null?void 0:h.deep)===!1?Et.ref:Et;return b.extend_(i,r,u,s)}function BO(i,r){X("'"+this.annotationType_+"' cannot be used as a decorator")}var VO="observable",PO="observable.ref",$O="observable.shallow",qO="observable.struct",ty={deep:!0,name:void 0,defaultDecorator:void 0,proxy:!0};Object.freeze(ty);function bu(i){return i||ty}var cd=gu(VO),GO=gu(PO,{enhancer:vu}),YO=gu($O,{enhancer:vO}),XO=gu(qO,{enhancer:yO}),ny=va(cd);function _u(i){return i.deep===!0?li:i.deep===!1?vu:QO(i.defaultDecorator)}function IO(i){var r;return i?(r=i.defaultDecorator)!=null?r:ey(i):void 0}function QO(i){var r,u;return i&&(r=(u=i.options_)==null?void 0:u.enhancer)!=null?r:li}function ay(i,r,u){if(Or(r))return cd.decorate_20223_(i,r);if(oi(r)){Tr(i,r,cd);return}return Rr(i)?i:on(i)?Et.object(i,r,u):Array.isArray(i)?Et.array(i,r):tl(i)?Et.map(i,r):Ha(i)?Et.set(i,r):typeof i=="object"&&i!==null?i:Et.box(i,r)}Pv(ay,ny);var ZO={box:function(r,u){var s=bu(u);return new si(r,_u(s),s.name,!0,s.equals)},array:function(r,u){var s=bu(u);return(B.useProxies===!1||s.proxy===!1?QE:LE)(r,_u(s),s.name)},map:function(r,u){var s=bu(u);return new jy(r,_u(s),s.name)},set:function(r,u){var s=bu(u);return new Ny(r,_u(s),s.name)},object:function(r,u,s){return go(function(){return Ay(B.useProxies===!1||s?.proxy===!1?di({},s):UE({},s),r,u)})},ref:va(GO),shallow:va(YO),deep:ny,struct:va(XO)},Et=Pv(ay,ZO),oy="computed",KO="computed.struct",fd=ud(oy),JO=ud(KO,{equals:mu.structural}),Su=function(r,u){if(Or(u))return fd.decorate_20223_(r,u);if(oi(u))return Tr(r,u,fd);if(on(r))return va(ud(oy,r));pt(r)||X("First argument to `computed` should be an expression."),pt(u)&&X("A setter as second argument is no longer supported, use `{ set: fn }` option instead");var s=on(u)?u:{};return s.get=r,s.name||(s.name=r.name||""),new An(s)};Object.assign(Su,fd),Su.struct=va(JO);var iy,ly,Tu=0,WO=1,FO=(iy=(ly=cu(function(){},"name"))==null?void 0:ly.configurable)!=null?iy:!1,ry={value:"action",configurable:!0,writable:!1,enumerable:!1};function ri(i,r,u,s){u===void 0&&(u=!1),pt(r)||X("`action` can only be invoked on functions"),(typeof i!="string"||!i)&&X("actions should have valid names, got: '"+i+"'");function f(){return sy(i,u,r,s||this,arguments)}return f.isMobxAction=!0,f.toString=function(){return r.toString()},FO&&(ry.value=i,ma(f,"name",ry)),f}function sy(i,r,u,s,f){var h=eE(i,r,s,f);try{return u.apply(s,f)}catch(p){throw h.error_=p,p}finally{tE(h)}}function eE(i,r,u,s){var f=wt()&&!!i,h=0;if(f){h=Date.now();var p=s?Array.from(s):du;sn({type:gd,name:i,object:u,arguments:p})}var b=B.trackingDerivation,g=!r||!b;Rn();var N=B.allowStateChanges;g&&(ui(),N=dd(!0));var U=vd(!0),x={runAsAction_:g,prevDerivation_:b,prevAllowStateChanges_:N,prevAllowStateReads_:U,notifySpy_:f,startTime_:h,actionId_:WO++,parentActionId_:Tu};return Tu=x.actionId_,x}function tE(i){Tu!==i.actionId_&&X(30),Tu=i.parentActionId_,i.error_!==void 0&&(B.suppressReactionErrors=!0),hd(i.prevAllowStateChanges_),wr(i.prevAllowStateReads_),zn(),i.runAsAction_&&Ba(i.prevDerivation_),i.notifySpy_&&un({time:Date.now()-i.startTime_}),B.suppressReactionErrors=!1}function dd(i){var r=B.allowStateChanges;return B.allowStateChanges=i,r}function hd(i){B.allowStateChanges=i}var nE="create",si=function(i){function r(s,f,h,p,b){var g;return h===void 0&&(h="ObservableValue@"+Fn()),p===void 0&&(p=!0),b===void 0&&(b=mu.default),g=i.call(this,h)||this,g.enhancer=void 0,g.name_=void 0,g.equals=void 0,g.hasUnreportedChange_=!1,g.interceptors_=void 0,g.changeListeners_=void 0,g.value_=void 0,g.dehancer=void 0,g.enhancer=f,g.name_=h,g.equals=b,g.value_=f(s,void 0,h),p&&wt()&&ci({type:nE,object:g,observableKind:"value",debugObjectName:g.name_,newValue:""+g.value_}),g}Zv(r,i);var u=r.prototype;return u.dehanceValue=function(f){return this.dehancer!==void 0?this.dehancer(f):f},u.set=function(f){var h=this.value_;if(f=this.prepareNewValue_(f),f!==B.UNCHANGED){var p=wt();p&&sn({type:na,object:this,observableKind:"value",debugObjectName:this.name_,newValue:f,oldValue:h}),this.setNewValue_(f),p&&un()}},u.prepareNewValue_=function(f){if(ya(this),Dn(this)){var h=Cn(this,{object:this,type:na,newValue:f});if(!h)return B.UNCHANGED;f=h.newValue}return f=this.enhancer(f,this.value_,this.name_),this.equals(this.value_,f)?B.UNCHANGED:f},u.setNewValue_=function(f){var h=this.value_;this.value_=f,this.reportChanged(),ea(this)&&ta(this,{type:na,object:this,newValue:f,oldValue:h})},u.get=function(){return this.reportObserved(),this.dehanceValue(this.value_)},u.intercept_=function(f){return zr(this,f)},u.observe_=function(f,h){return h&&f({observableKind:"value",debugObjectName:this.name_,object:this,type:na,newValue:this.value_,oldValue:void 0}),Dr(this,f)},u.raw=function(){return this.value_},u.toJSON=function(){return this.get()},u.toString=function(){return this.name_+"["+this.value_+"]"},u.valueOf=function(){return Iv(this.get())},u[Symbol.toPrimitive]=function(){return this.valueOf()},r}(mo),An=function(){function i(u){this.dependenciesState_=ze.NOT_TRACKING_,this.observing_=[],this.newObserving_=null,this.observers_=new Set,this.runId_=0,this.lastAccessedBy_=0,this.lowestObserverState_=ze.UP_TO_DATE_,this.unboundDepsCount_=0,this.value_=new Eu(null),this.name_=void 0,this.triggeredBy_=void 0,this.flags_=0,this.derivation=void 0,this.setter_=void 0,this.isTracing_=xn.NONE,this.scope_=void 0,this.equals_=void 0,this.requiresReaction_=void 0,this.keepAlive_=void 0,this.onBOL=void 0,this.onBUOL=void 0,u.get||X(31),this.derivation=u.get,this.name_=u.name||"ComputedValue@"+Fn(),u.set&&(this.setter_=ri(this.name_+"-setter",u.set)),this.equals_=u.equals||(u.compareStructural||u.struct?mu.structural:mu.default),this.scope_=u.context,this.requiresReaction_=u.requiresReaction,this.keepAlive_=!!u.keepAlive}var r=i.prototype;return r.onBecomeStale_=function(){uE(this)},r.onBO=function(){this.onBOL&&this.onBOL.forEach(function(s){return s()})},r.onBUO=function(){this.onBUOL&&this.onBUOL.forEach(function(s){return s()})},r.get=function(){if(this.isComputing&&X(32,this.name_,this.derivation),B.inBatch===0&&this.observers_.size===0&&!this.keepAlive_)pd(this)&&(this.warnAboutUntrackedRead_(),Rn(),this.value_=this.computeValue_(!1),zn());else if(my(this),pd(this)){var s=B.trackingContext;this.keepAlive_&&!s&&(B.trackingContext=this),this.trackAndCompute()&&sE(this),B.trackingContext=s}var f=this.value_;if(wu(f))throw f.cause;return f},r.set=function(s){if(this.setter_){this.isRunningSetter&&X(33,this.name_),this.isRunningSetter=!0;try{this.setter_.call(this.scope_,s)}finally{this.isRunningSetter=!1}}else X(34,this.name_)},r.trackAndCompute=function(){var s=this.value_,f=this.dependenciesState_===ze.NOT_TRACKING_,h=this.computeValue_(!0),p=f||wu(s)||wu(h)||!this.equals_(s,h);return p&&(this.value_=h,wt()&&ci({observableKind:"computed",debugObjectName:this.name_,object:this.scope_,type:"update",oldValue:s,newValue:h})),p},r.computeValue_=function(s){this.isComputing=!0;var f=dd(!1),h;if(s)h=uy(this,this.derivation,this.scope_);else if(B.disableErrorBoundaries===!0)h=this.derivation.call(this.scope_);else try{h=this.derivation.call(this.scope_)}catch(p){h=new Eu(p)}return hd(f),this.isComputing=!1,h},r.suspend_=function(){this.keepAlive_||(md(this),this.value_=void 0,this.isTracing_!==xn.NONE&&console.log("[mobx.trace] Computed value '"+this.name_+"' was suspended and it will recompute on the next access."))},r.observe_=function(s,f){var h=this,p=!0,b=void 0;return bE(function(){var g=h.get();if(!p||f){var N=ui();s({observableKind:"computed",debugObjectName:h.name_,type:na,object:h,newValue:g,oldValue:b}),Ba(N)}p=!1,b=g})},r.warnAboutUntrackedRead_=function(){this.isTracing_!==xn.NONE&&console.log("[mobx.trace] Computed value '"+this.name_+"' is being read outside a reactive context. Doing a full recompute."),(typeof this.requiresReaction_=="boolean"?this.requiresReaction_:B.computedRequiresReaction)&&console.warn("[mobx] Computed value '"+this.name_+"' is being read outside a reactive context. Doing a full recompute.")},r.toString=function(){return this.name_+"["+this.derivation.toString()+"]"},r.valueOf=function(){return Iv(this.get())},r[Symbol.toPrimitive]=function(){return this.valueOf()},al(i,[{key:"isComputing",get:function(){return ln(this.flags_,i.isComputingMask_)},set:function(s){this.flags_=rn(this.flags_,i.isComputingMask_,s)}},{key:"isRunningSetter",get:function(){return ln(this.flags_,i.isRunningSetterMask_)},set:function(s){this.flags_=rn(this.flags_,i.isRunningSetterMask_,s)}},{key:"isBeingObserved",get:function(){return ln(this.flags_,i.isBeingObservedMask_)},set:function(s){this.flags_=rn(this.flags_,i.isBeingObservedMask_,s)}},{key:"isPendingUnobservation",get:function(){return ln(this.flags_,i.isPendingUnobservationMask_)},set:function(s){this.flags_=rn(this.flags_,i.isPendingUnobservationMask_,s)}},{key:"diffValue",get:function(){return ln(this.flags_,i.diffValueMask_)?1:0},set:function(s){this.flags_=rn(this.flags_,i.diffValueMask_,s===1)}}])}();An.isComputingMask_=1,An.isRunningSetterMask_=2,An.isBeingObservedMask_=4,An.isPendingUnobservationMask_=8,An.diffValueMask_=16;var Ou=ii("ComputedValue",An),ze;(function(i){i[i.NOT_TRACKING_=-1]="NOT_TRACKING_",i[i.UP_TO_DATE_=0]="UP_TO_DATE_",i[i.POSSIBLY_STALE_=1]="POSSIBLY_STALE_",i[i.STALE_=2]="STALE_"})(ze||(ze={}));var xn;(function(i){i[i.NONE=0]="NONE",i[i.LOG=1]="LOG",i[i.BREAK=2]="BREAK"})(xn||(xn={}));var Eu=function(r){this.cause=void 0,this.cause=r};function wu(i){return i instanceof Eu}function pd(i){switch(i.dependenciesState_){case ze.UP_TO_DATE_:return!1;case ze.NOT_TRACKING_:case ze.STALE_:return!0;case ze.POSSIBLY_STALE_:{for(var r=vd(!0),u=ui(),s=i.observing_,f=s.length,h=0;h<f;h++){var p=s[h];if(Ou(p)){if(B.disableErrorBoundaries)p.get();else try{p.get()}catch{return Ba(u),wr(r),!0}if(i.dependenciesState_===ze.STALE_)return Ba(u),wr(r),!0}}return fy(i),Ba(u),wr(r),!1}}}function ya(i){var r=i.observers_.size>0;!B.allowStateChanges&&(r||B.enforceActions==="always")&&console.warn("[MobX] "+(B.enforceActions?"Since strict-mode is enabled, changing (observed) observable values without using an action is not allowed. Tried to modify: ":"Side effects like changing state are not allowed at this point. Are you trying to modify state from, for example, a computed value or the render function of a React component? You can wrap side effects in 'runInAction' (or decorate functions with 'action') if needed. Tried to modify: ")+i.name_)}function aE(i){!B.allowStateReads&&B.observableRequiresReaction&&console.warn("[mobx] Observable '"+i.name_+"' being read outside a reactive context.")}function uy(i,r,u){var s=vd(!0);fy(i),i.newObserving_=new Array(i.runId_===0?100:i.observing_.length),i.unboundDepsCount_=0,i.runId_=++B.runId;var f=B.trackingDerivation;B.trackingDerivation=i,B.inBatch++;var h;if(B.disableErrorBoundaries===!0)h=r.call(u);else try{h=r.call(u)}catch(p){h=new Eu(p)}return B.inBatch--,B.trackingDerivation=f,iE(i),oE(i),wr(s),h}function oE(i){i.observing_.length===0&&(typeof i.requiresObservable_=="boolean"?i.requiresObservable_:B.reactionRequiresObservable)&&console.warn("[mobx] Derivation '"+i.name_+"' is created/updated without reading any observable value.")}function iE(i){for(var r=i.observing_,u=i.observing_=i.newObserving_,s=ze.UP_TO_DATE_,f=0,h=i.unboundDepsCount_,p=0;p<h;p++){var b=u[p];b.diffValue===0&&(b.diffValue=1,f!==p&&(u[f]=b),f++),b.dependenciesState_>s&&(s=b.dependenciesState_)}for(u.length=f,i.newObserving_=null,h=r.length;h--;){var g=r[h];g.diffValue===0&&hy(g,i),g.diffValue=0}for(;f--;){var N=u[f];N.diffValue===1&&(N.diffValue=0,rE(N,i))}s!==ze.UP_TO_DATE_&&(i.dependenciesState_=s,i.onBecomeStale_())}function md(i){var r=i.observing_;i.observing_=[];for(var u=r.length;u--;)hy(r[u],i);i.dependenciesState_=ze.NOT_TRACKING_}function cy(i){var r=ui();try{return i()}finally{Ba(r)}}function ui(){var i=B.trackingDerivation;return B.trackingDerivation=null,i}function Ba(i){B.trackingDerivation=i}function vd(i){var r=B.allowStateReads;return B.allowStateReads=i,r}function wr(i){B.allowStateReads=i}function fy(i){if(i.dependenciesState_!==ze.UP_TO_DATE_){i.dependenciesState_=ze.UP_TO_DATE_;for(var r=i.observing_,u=r.length;u--;)r[u].lowestObserverState_=ze.UP_TO_DATE_}}var Au=function(){this.version=6,this.UNCHANGED={},this.trackingDerivation=null,this.trackingContext=null,this.runId=0,this.mobxGuid=0,this.inBatch=0,this.pendingUnobservations=[],this.pendingReactions=[],this.isRunningReactions=!1,this.allowStateChanges=!1,this.allowStateReads=!0,this.enforceActions=!0,this.spyListeners=[],this.globalReactionErrorHandlers=[],this.computedRequiresReaction=!1,this.reactionRequiresObservable=!1,this.observableRequiresReaction=!1,this.disableErrorBoundaries=!1,this.suppressReactionErrors=!1,this.useProxies=!0,this.verifyProxies=!1,this.safeDescriptors=!0},xu=!0,dy=!1,B=function(){var i=uu();return i.__mobxInstanceCount>0&&!i.__mobxGlobals&&(xu=!1),i.__mobxGlobals&&i.__mobxGlobals.version!==new Au().version&&(xu=!1),xu?i.__mobxGlobals?(i.__mobxInstanceCount+=1,i.__mobxGlobals.UNCHANGED||(i.__mobxGlobals.UNCHANGED={}),i.__mobxGlobals):(i.__mobxInstanceCount=1,i.__mobxGlobals=new Au):(setTimeout(function(){dy||X(35)},1),new Au)}();function lE(){if((B.pendingReactions.length||B.inBatch||B.isRunningReactions)&&X(36),dy=!0,xu){var i=uu();--i.__mobxInstanceCount===0&&(i.__mobxGlobals=void 0),B=new Au}}function rE(i,r){i.observers_.add(r),i.lowestObserverState_>r.dependenciesState_&&(i.lowestObserverState_=r.dependenciesState_)}function hy(i,r){i.observers_.delete(r),i.observers_.size===0&&py(i)}function py(i){i.isPendingUnobservation===!1&&(i.isPendingUnobservation=!0,B.pendingUnobservations.push(i))}function Rn(){B.inBatch++}function zn(){if(--B.inBatch===0){_y();for(var i=B.pendingUnobservations,r=0;r<i.length;r++){var u=i[r];u.isPendingUnobservation=!1,u.observers_.size===0&&(u.isBeingObserved&&(u.isBeingObserved=!1,u.onBUO()),u instanceof An&&u.suspend_())}B.pendingUnobservations=[]}}function my(i){aE(i);var r=B.trackingDerivation;return r!==null?(r.runId_!==i.lastAccessedBy_&&(i.lastAccessedBy_=r.runId_,r.newObserving_[r.unboundDepsCount_++]=i,!i.isBeingObserved&&B.trackingContext&&(i.isBeingObserved=!0,i.onBO())),i.isBeingObserved):(i.observers_.size===0&&B.inBatch>0&&py(i),!1)}function vy(i){i.lowestObserverState_!==ze.STALE_&&(i.lowestObserverState_=ze.STALE_,i.observers_.forEach(function(r){r.dependenciesState_===ze.UP_TO_DATE_&&(r.isTracing_!==xn.NONE&&yy(r,i),r.onBecomeStale_()),r.dependenciesState_=ze.STALE_}))}function sE(i){i.lowestObserverState_!==ze.STALE_&&(i.lowestObserverState_=ze.STALE_,i.observers_.forEach(function(r){r.dependenciesState_===ze.POSSIBLY_STALE_?(r.dependenciesState_=ze.STALE_,r.isTracing_!==xn.NONE&&yy(r,i)):r.dependenciesState_===ze.UP_TO_DATE_&&(i.lowestObserverState_=ze.UP_TO_DATE_)}))}function uE(i){i.lowestObserverState_===ze.UP_TO_DATE_&&(i.lowestObserverState_=ze.POSSIBLY_STALE_,i.observers_.forEach(function(r){r.dependenciesState_===ze.UP_TO_DATE_&&(r.dependenciesState_=ze.POSSIBLY_STALE_,r.onBecomeStale_())}))}function yy(i,r){if(console.log("[mobx.trace] '"+i.name_+"' is invalidated due to a change in: '"+r.name_+"'"),i.isTracing_===xn.BREAK){var u=[];gy(xy(i),u,1),new Function(`debugger;
/*
Tracing '`+i.name_+`'

You are entering this break point because derivation '`+i.name_+"' is being traced and '"+r.name_+`' is now forcing it to update.
Just follow the stacktrace you should now see in the devtools to see precisely what piece of your code is causing this update
The stackframe you are looking for is at least ~6-8 stack-frames up.

`+(i instanceof An?i.derivation.toString().replace(/[*]\//g,"/"):"")+`

The dependencies for this derivation are:

`+u.join(`
`)+`
*/
    `)()}}function gy(i,r,u){if(r.length>=1e3){r.push("(and many more)");return}r.push(""+"	".repeat(u-1)+i.name),i.dependencies&&i.dependencies.forEach(function(s){return gy(s,r,u+1)})}var Va=function(){function i(u,s,f,h){u===void 0&&(u="Reaction@"+Fn()),this.name_=void 0,this.onInvalidate_=void 0,this.errorHandler_=void 0,this.requiresObservable_=void 0,this.observing_=[],this.newObserving_=[],this.dependenciesState_=ze.NOT_TRACKING_,this.runId_=0,this.unboundDepsCount_=0,this.flags_=0,this.isTracing_=xn.NONE,this.name_=u,this.onInvalidate_=s,this.errorHandler_=f,this.requiresObservable_=h}var r=i.prototype;return r.onBecomeStale_=function(){this.schedule_()},r.schedule_=function(){this.isScheduled||(this.isScheduled=!0,B.pendingReactions.push(this),_y())},r.runReaction_=function(){if(!this.isDisposed){Rn(),this.isScheduled=!1;var s=B.trackingContext;if(B.trackingContext=this,pd(this)){this.isTrackPending=!0;try{this.onInvalidate_(),Qt.NODE_ENV!=="production"&&this.isTrackPending&&wt()&&ci({name:this.name_,type:"scheduled-reaction"})}catch(f){this.reportExceptionInDerivation_(f)}}B.trackingContext=s,zn()}},r.track=function(s){if(!this.isDisposed){Rn();var f=wt(),h;f&&(h=Date.now(),sn({name:this.name_,type:"reaction"})),this.isRunning=!0;var p=B.trackingContext;B.trackingContext=this;var b=uy(this,s,void 0);B.trackingContext=p,this.isRunning=!1,this.isTrackPending=!1,this.isDisposed&&md(this),wu(b)&&this.reportExceptionInDerivation_(b.cause),f&&un({time:Date.now()-h}),zn()}},r.reportExceptionInDerivation_=function(s){var f=this;if(this.errorHandler_){this.errorHandler_(s,this);return}if(B.disableErrorBoundaries)throw s;var h="[mobx] Encountered an uncaught exception that was thrown by a reaction or observer component, in: '"+this+"'";B.suppressReactionErrors?console.warn("[mobx] (error in reaction '"+this.name_+"' suppressed, fix error of causing action below)"):console.error(h,s),wt()&&ci({type:"error",name:this.name_,message:h,error:""+s}),B.globalReactionErrorHandlers.forEach(function(p){return p(s,f)})},r.dispose=function(){this.isDisposed||(this.isDisposed=!0,this.isRunning||(Rn(),md(this),zn()))},r.getDisposer_=function(s){var f=this,h=function p(){f.dispose(),s==null||s.removeEventListener==null||s.removeEventListener("abort",p)};return s==null||s.addEventListener==null||s.addEventListener("abort",h),h[re]=this,h},r.toString=function(){return"Reaction["+this.name_+"]"},r.trace=function(s){s===void 0&&(s=!1),CE(this,s)},al(i,[{key:"isDisposed",get:function(){return ln(this.flags_,i.isDisposedMask_)},set:function(s){this.flags_=rn(this.flags_,i.isDisposedMask_,s)}},{key:"isScheduled",get:function(){return ln(this.flags_,i.isScheduledMask_)},set:function(s){this.flags_=rn(this.flags_,i.isScheduledMask_,s)}},{key:"isTrackPending",get:function(){return ln(this.flags_,i.isTrackPendingMask_)},set:function(s){this.flags_=rn(this.flags_,i.isTrackPendingMask_,s)}},{key:"isRunning",get:function(){return ln(this.flags_,i.isRunningMask_)},set:function(s){this.flags_=rn(this.flags_,i.isRunningMask_,s)}},{key:"diffValue",get:function(){return ln(this.flags_,i.diffValueMask_)?1:0},set:function(s){this.flags_=rn(this.flags_,i.diffValueMask_,s===1)}}])}();Va.isDisposedMask_=1,Va.isScheduledMask_=2,Va.isTrackPendingMask_=4,Va.isRunningMask_=8,Va.diffValueMask_=16;var by=100,yd=function(r){return r()};function _y(){B.inBatch>0||B.isRunningReactions||yd(cE)}function cE(){B.isRunningReactions=!0;for(var i=B.pendingReactions,r=0;i.length>0;){++r===by&&(console.error("Reaction doesn't converge to a stable state after "+by+" iterations."+(" Probably there is a cycle in the reactive function: "+i[0])),i.splice(0));for(var u=i.splice(0),s=0,f=u.length;s<f;s++)u[s].runReaction_()}B.isRunningReactions=!1}var Ru=ii("Reaction",Va);function fE(i){var r=yd;yd=function(s){return i(function(){return r(s)})}}function wt(){return!!B.spyListeners.length}function ci(i){if(B.spyListeners.length)for(var r=B.spyListeners,u=0,s=r.length;u<s;u++)r[u](i)}function sn(i){var r=La({},i,{spyReportStart:!0});ci(r)}var dE={type:"report-end",spyReportEnd:!0};function un(i){ci(i?La({},i,{type:"report-end",spyReportEnd:!0}):dE)}function hE(i){return B.spyListeners.push(i),id(function(){B.spyListeners=B.spyListeners.filter(function(r){return r!==i})})}var gd="action",pE="action.bound",Sy="autoAction",mE="autoAction.bound",Ty="<unnamed action>",bd=Er(gd),vE=Er(pE,{bound:!0}),_d=Er(Sy,{autoAction:!0}),yE=Er(mE,{autoAction:!0,bound:!0});function Oy(i){var r=function(s,f){if(pt(s))return ri(s.name||Ty,s,i);if(pt(f))return ri(s,f,i);if(Or(f))return(i?_d:bd).decorate_20223_(s,f);if(oi(f))return Tr(s,f,i?_d:bd);if(oi(s))return va(Er(i?Sy:gd,{name:s,autoAction:i}));X("Invalid arguments for `action`")};return r}var il=Oy(!1);Object.assign(il,bd);var Ar=Oy(!0);Object.assign(Ar,_d),il.bound=va(vE),Ar.bound=va(yE);function gE(i){return sy(i.name||Ty,!1,i,this,void 0)}function ll(i){return pt(i)&&i.isMobxAction===!0}function bE(i,r){var u,s,f,h;r===void 0&&(r=$v),pt(i)||X("Autorun expects a function as first argument"),ll(i)&&X("Autorun does not accept actions since actions are untrackable");var p=(u=(s=r)==null?void 0:s.name)!=null?u:i.name||"Autorun@"+Fn(),b=!r.scheduler&&!r.delay,g;if(b)g=new Va(p,function(){this.track(x)},r.onError,r.requiresObservable);else{var N=SE(r),U=!1;g=new Va(p,function(){U||(U=!0,N(function(){U=!1,g.isDisposed||g.track(x)}))},r.onError,r.requiresObservable)}function x(){i(g)}return(f=r)!=null&&(f=f.signal)!=null&&f.aborted||g.schedule_(),g.getDisposer_((h=r)==null?void 0:h.signal)}var _E=function(r){return r()};function SE(i){return i.scheduler?i.scheduler:i.delay?function(r){return setTimeout(r,i.delay)}:_E}var TE="onBO",OE="onBUO";function EE(i,r,u){return wy(TE,i,r,u)}function Ey(i,r,u){return wy(OE,i,r,u)}function wy(i,r,u,s){var f=ul(r),h=pt(s)?s:u,p=i+"L";return f[p]?f[p].add(h):f[p]=new Set([h]),function(){var b=f[p];b&&(b.delete(h),b.size===0&&delete f[p])}}var wE="never",zu="always",AE="observed";function mt(i){i.isolateGlobalState===!0&&lE();var r=i.useProxies,u=i.enforceActions;if(r!==void 0&&(B.useProxies=r===zu?!0:r===wE?!1:typeof Proxy<"u"),r==="ifavailable"&&(B.verifyProxies=!0),u!==void 0){var s=u===zu?zu:u===AE;B.enforceActions=s,B.allowStateChanges=!(s===!0||s===zu)}["computedRequiresReaction","reactionRequiresObservable","observableRequiresReaction","disableErrorBoundaries","safeDescriptors"].forEach(function(f){f in i&&(B[f]=!!i[f])}),B.allowStateReads=!B.observableRequiresReaction,B.disableErrorBoundaries===!0&&console.warn("WARNING: Debug feature only. MobX will NOT recover from errors when `disableErrorBoundaries` is enabled."),i.reactionScheduler&&fE(i.reactionScheduler)}function Ay(i,r,u,s){arguments.length>4&&X("'extendObservable' expected 2-4 arguments"),typeof i!="object"&&X("'extendObservable' expects an object as first argument"),yo(i)&&X("'extendObservable' should not be used on maps, use map.merge instead"),on(r)||X("'extendObservable' only accepts plain objects as second argument"),(Rr(r)||Rr(u))&&X("Extending an object with another observable (object) is not supported");var f=rO(r);return go(function(){var h=di(i,s)[re];nl(f).forEach(function(p){h.extend_(p,f[p],u&&p in u?u[p]:!0)})}),i}function xy(i,r){return Ry(ul(i,r))}function Ry(i){var r={name:i.name_};return i.observing_&&i.observing_.length>0&&(r.dependencies=xE(i.observing_).map(Ry)),r}function xE(i){return Array.from(new Set(i))}var RE=0;function zy(){this.message="FLOW_CANCELLED"}zy.prototype=Object.create(Error.prototype);var Sd=Wv("flow"),zE=Wv("flow.bound",{bound:!0}),rl=Object.assign(function(r,u){if(Or(u))return Sd.decorate_20223_(r,u);if(oi(u))return Tr(r,u,Sd);arguments.length!==1&&X("Flow expects single argument with generator function");var s=r,f=s.name||"<unnamed flow>",h=function(){var b=this,g=arguments,N=++RE,U=il(f+" - runid: "+N+" - init",s).apply(b,g),x,O=void 0,L=new Promise(function(I,te){var ne=0;x=te;function ie(Ne){O=void 0;var Ze;try{Ze=il(f+" - runid: "+N+" - yield "+ne++,U.next).call(U,Ne)}catch(Fe){return te(Fe)}$e(Ze)}function Qe(Ne){O=void 0;var Ze;try{Ze=il(f+" - runid: "+N+" - yield "+ne++,U.throw).call(U,Ne)}catch(Fe){return te(Fe)}$e(Ze)}function $e(Ne){if(pt(Ne?.then)){Ne.then($e,te);return}return Ne.done?I(Ne.value):(O=Promise.resolve(Ne.value),O.then(ie,Qe))}ie(void 0)});return L.cancel=il(f+" - runid: "+N+" - cancel",function(){try{O&&Dy(O);var I=U.return(void 0),te=Promise.resolve(I.value);te.then(el,el),Dy(te),x(new zy)}catch(ne){x(ne)}}),L};return h.isMobXFlow=!0,h},Sd);rl.bound=va(zE);function Dy(i){pt(i.cancel)&&i.cancel()}function xr(i){return i?.isMobXFlow===!0}function DE(i,r){return i?sl(i)||!!i[re]||sd(i)||Ru(i)||Ou(i):!1}function Rr(i){return arguments.length!==1&&X("isObservable expects only 1 argument. Use isObservableProp to inspect the observability of a property"),DE(i)}function CE(){for(var i=!1,r=arguments.length,u=new Array(r),s=0;s<r;s++)u[s]=arguments[s];typeof u[u.length-1]=="boolean"&&(i=u.pop());var f=ME(u);if(!f)return X("'trace(break?)' can only be used inside a tracked computed value or a Reaction. Consider passing in the computed value or reaction explicitly");f.isTracing_===xn.NONE&&console.log("[mobx.trace] '"+f.name_+"' tracing enabled"),f.isTracing_=i?xn.BREAK:xn.LOG}function ME(i){switch(i.length){case 0:return B.trackingDerivation;case 1:return ul(i[0]);case 2:return ul(i[0],i[1])}}function Pa(i,r){r===void 0&&(r=void 0),Rn();try{return i.apply(r)}finally{zn()}}function fi(i){return i[re]}var jE={has:function(r,u){return B.trackingDerivation&&_r("detect new properties using the 'in' operator. Use 'has' from 'mobx' instead."),fi(r).has_(u)},get:function(r,u){return fi(r).get_(u)},set:function(r,u,s){var f;return oi(u)?(fi(r).values_.has(u)||_r("add a new observable property through direct assignment. Use 'set' from 'mobx' instead."),(f=fi(r).set_(u,s,!0))!=null?f:!0):!1},deleteProperty:function(r,u){var s;return _r("delete properties from an observable object. Use 'remove' from 'mobx' instead."),oi(u)?(s=fi(r).delete_(u,!0))!=null?s:!0:!1},defineProperty:function(r,u,s){var f;return _r("define property on an observable object. Use 'defineProperty' from 'mobx' instead."),(f=fi(r).defineProperty_(u,s))!=null?f:!0},ownKeys:function(r){return B.trackingDerivation&&_r("iterate keys to detect added / removed properties. Use 'keys' from 'mobx' instead."),fi(r).ownKeys_()},preventExtensions:function(r){X(13)}};function UE(i,r){var u,s;return qv(),i=di(i,r),(s=(u=i[re]).proxy_)!=null?s:u.proxy_=new Proxy(i,jE)}function Dn(i){return i.interceptors_!==void 0&&i.interceptors_.length>0}function zr(i,r){var u=i.interceptors_||(i.interceptors_=[]);return u.push(r),id(function(){var s=u.indexOf(r);s!==-1&&u.splice(s,1)})}function Cn(i,r){var u=ui();try{for(var s=[].concat(i.interceptors_||[]),f=0,h=s.length;f<h&&(r=s[f](r),r&&!r.type&&X(14),!!r);f++);return r}finally{Ba(u)}}function ea(i){return i.changeListeners_!==void 0&&i.changeListeners_.length>0}function Dr(i,r){var u=i.changeListeners_||(i.changeListeners_=[]);return u.push(r),id(function(){var s=u.indexOf(r);s!==-1&&u.splice(s,1)})}function ta(i,r){var u=ui(),s=i.changeListeners_;if(s){s=s.slice();for(var f=0,h=s.length;f<h;f++)s[f](r);Ba(u)}}function NE(i,r,u){return go(function(){var s,f=di(i,u)[re];Qt.NODE_ENV!=="production"&&r&&i[Zt]&&X("makeObservable second arg must be nullish when using decorators. Mixing @decorator syntax with annotations is not supported."),(s=r)!=null||(r=hO(i)),nl(r).forEach(function(h){return f.make_(h,r[h])})}),i}var Td=Symbol("mobx-keys");function bt(i,r,u){return!on(i)&&!on(Object.getPrototypeOf(i))&&X("'makeAutoObservable' can only be used for classes that don't have a superclass"),sl(i)&&X("makeAutoObservable can only be used on objects not already made observable"),on(i)?Ay(i,i,r,u):(go(function(){var s=di(i,u)[re];if(!i[Td]){var f=Object.getPrototypeOf(i),h=new Set([].concat(nl(i),nl(f)));h.delete("constructor"),h.delete(re),Sr(f,Td,h)}i[Td].forEach(function(p){return s.make_(p,r&&p in r?r[p]:!0)})}),i)}var Cy="splice",na="update",kE=1e4,HE={get:function(r,u){var s=r[re];return u===re?s:u==="length"?s.getArrayLength_():typeof u=="string"&&!isNaN(u)?s.get_(parseInt(u)):wn(Du,u)?Du[u]:r[u]},set:function(r,u,s){var f=r[re];return u==="length"&&f.setArrayLength_(s),typeof u=="symbol"||isNaN(u)?r[u]=s:f.set_(parseInt(u),s),!0},preventExtensions:function(){X(15)}},Od=function(){function i(u,s,f,h){u===void 0&&(u="ObservableArray@"+Fn()),this.owned_=void 0,this.legacyMode_=void 0,this.atom_=void 0,this.values_=[],this.interceptors_=void 0,this.changeListeners_=void 0,this.enhancer_=void 0,this.dehancer=void 0,this.proxy_=void 0,this.lastKnownLength_=0,this.owned_=f,this.legacyMode_=h,this.atom_=new mo(u),this.enhancer_=function(p,b){return s(p,b,u+"[..]")}}var r=i.prototype;return r.dehanceValue_=function(s){return this.dehancer!==void 0?this.dehancer(s):s},r.dehanceValues_=function(s){return this.dehancer!==void 0&&s.length>0?s.map(this.dehancer):s},r.intercept_=function(s){return zr(this,s)},r.observe_=function(s,f){return f===void 0&&(f=!1),f&&s({observableKind:"array",object:this.proxy_,debugObjectName:this.atom_.name_,type:"splice",index:0,added:this.values_.slice(),addedCount:this.values_.length,removed:[],removedCount:0}),Dr(this,s)},r.getArrayLength_=function(){return this.atom_.reportObserved(),this.values_.length},r.setArrayLength_=function(s){(typeof s!="number"||isNaN(s)||s<0)&&X("Out of range: "+s);var f=this.values_.length;if(s!==f)if(s>f){for(var h=new Array(s-f),p=0;p<s-f;p++)h[p]=void 0;this.spliceWithArray_(f,0,h)}else this.spliceWithArray_(s,f-s)},r.updateArrayLength_=function(s,f){s!==this.lastKnownLength_&&X(16),this.lastKnownLength_+=f,this.legacyMode_&&f>0&&Gy(s+f+1)},r.spliceWithArray_=function(s,f,h){var p=this;ya(this.atom_);var b=this.values_.length;if(s===void 0?s=0:s>b?s=b:s<0&&(s=Math.max(0,b+s)),arguments.length===1?f=b-s:f==null?f=0:f=Math.max(0,Math.min(f,b-s)),h===void 0&&(h=du),Dn(this)){var g=Cn(this,{object:this.proxy_,type:Cy,index:s,removedCount:f,added:h});if(!g)return du;f=g.removedCount,h=g.added}if(h=h.length===0?h:h.map(function(x){return p.enhancer_(x,void 0)}),this.legacyMode_||Qt.NODE_ENV!=="production"){var N=h.length-f;this.updateArrayLength_(b,N)}var U=this.spliceItemsIntoValues_(s,f,h);return(f!==0||h.length!==0)&&this.notifyArraySplice_(s,h,U),this.dehanceValues_(U)},r.spliceItemsIntoValues_=function(s,f,h){if(h.length<kE){var p;return(p=this.values_).splice.apply(p,[s,f].concat(h))}else{var b=this.values_.slice(s,s+f),g=this.values_.slice(s+f);this.values_.length+=h.length-f;for(var N=0;N<h.length;N++)this.values_[s+N]=h[N];for(var U=0;U<g.length;U++)this.values_[s+h.length+U]=g[U];return b}},r.notifyArrayChildUpdate_=function(s,f,h){var p=!this.owned_&&wt(),b=ea(this),g=b||p?{observableKind:"array",object:this.proxy_,type:na,debugObjectName:this.atom_.name_,index:s,newValue:f,oldValue:h}:null;p&&sn(g),this.atom_.reportChanged(),b&&ta(this,g),p&&un()},r.notifyArraySplice_=function(s,f,h){var p=!this.owned_&&wt(),b=ea(this),g=b||p?{observableKind:"array",object:this.proxy_,debugObjectName:this.atom_.name_,type:Cy,index:s,removed:h,added:f,removedCount:h.length,addedCount:f.length}:null;p&&sn(g),this.atom_.reportChanged(),b&&ta(this,g),p&&un()},r.get_=function(s){if(this.legacyMode_&&s>=this.values_.length){console.warn("[mobx.array] Attempt to read an array index ("+s+") that is out of bounds ("+this.values_.length+"). Please check length first. Out of bound indices will not be tracked by MobX");return}return this.atom_.reportObserved(),this.dehanceValue_(this.values_[s])},r.set_=function(s,f){var h=this.values_;if(this.legacyMode_&&s>h.length&&X(17,s,h.length),s<h.length){ya(this.atom_);var p=h[s];if(Dn(this)){var b=Cn(this,{type:na,object:this.proxy_,index:s,newValue:f});if(!b)return;f=b.newValue}f=this.enhancer_(f,p);var g=f!==p;g&&(h[s]=f,this.notifyArrayChildUpdate_(s,f,p))}else{for(var N=new Array(s+1-h.length),U=0;U<N.length-1;U++)N[U]=void 0;N[N.length-1]=f,this.spliceWithArray_(h.length,0,N)}},i}();function LE(i,r,u,s){return u===void 0&&(u="ObservableArray@"+Fn()),s===void 0&&(s=!1),qv(),go(function(){var f=new Od(u,r,s,!1);Yv(f.values_,re,f);var h=new Proxy(f.values_,HE);return f.proxy_=h,i&&i.length&&f.spliceWithArray_(0,0,i),h})}var Du={clear:function(){return this.splice(0)},replace:function(r){var u=this[re];return u.spliceWithArray_(0,u.values_.length,r)},toJSON:function(){return this.slice()},splice:function(r,u){for(var s=arguments.length,f=new Array(s>2?s-2:0),h=2;h<s;h++)f[h-2]=arguments[h];var p=this[re];switch(arguments.length){case 0:return[];case 1:return p.spliceWithArray_(r);case 2:return p.spliceWithArray_(r,u)}return p.spliceWithArray_(r,u,f)},spliceWithArray:function(r,u,s){return this[re].spliceWithArray_(r,u,s)},push:function(){for(var r=this[re],u=arguments.length,s=new Array(u),f=0;f<u;f++)s[f]=arguments[f];return r.spliceWithArray_(r.values_.length,0,s),r.values_.length},pop:function(){return this.splice(Math.max(this[re].values_.length-1,0),1)[0]},shift:function(){return this.splice(0,1)[0]},unshift:function(){for(var r=this[re],u=arguments.length,s=new Array(u),f=0;f<u;f++)s[f]=arguments[f];return r.spliceWithArray_(0,0,s),r.values_.length},reverse:function(){return B.trackingDerivation&&X(37,"reverse"),this.replace(this.slice().reverse()),this},sort:function(){B.trackingDerivation&&X(37,"sort");var r=this.slice();return r.sort.apply(r,arguments),this.replace(r),this},remove:function(r){var u=this[re],s=u.dehanceValues_(u.values_).indexOf(r);return s>-1?(this.splice(s,1),!0):!1}};Ye("at",cn),Ye("concat",cn),Ye("flat",cn),Ye("includes",cn),Ye("indexOf",cn),Ye("join",cn),Ye("lastIndexOf",cn),Ye("slice",cn),Ye("toString",cn),Ye("toLocaleString",cn),Ye("toSorted",cn),Ye("toSpliced",cn),Ye("with",cn),Ye("every",aa),Ye("filter",aa),Ye("find",aa),Ye("findIndex",aa),Ye("findLast",aa),Ye("findLastIndex",aa),Ye("flatMap",aa),Ye("forEach",aa),Ye("map",aa),Ye("some",aa),Ye("toReversed",aa),Ye("reduce",My),Ye("reduceRight",My);function Ye(i,r){typeof Array.prototype[i]=="function"&&(Du[i]=r(i))}function cn(i){return function(){var r=this[re];r.atom_.reportObserved();var u=r.dehanceValues_(r.values_);return u[i].apply(u,arguments)}}function aa(i){return function(r,u){var s=this,f=this[re];f.atom_.reportObserved();var h=f.dehanceValues_(f.values_);return h[i](function(p,b){return r.call(u,p,b,s)})}}function My(i){return function(){var r=this,u=this[re];u.atom_.reportObserved();var s=u.dehanceValues_(u.values_),f=arguments[0];return arguments[0]=function(h,p,b){return f(h,p,b,r)},s[i].apply(s,arguments)}}var BE=ii("ObservableArrayAdministration",Od);function Cu(i){return hu(i)&&BE(i[re])}var VE={},vo="add",Mu="delete",jy=function(){function i(u,s,f){var h=this;s===void 0&&(s=li),f===void 0&&(f="ObservableMap@"+Fn()),this.enhancer_=void 0,this.name_=void 0,this[re]=VE,this.data_=void 0,this.hasMap_=void 0,this.keysAtom_=void 0,this.interceptors_=void 0,this.changeListeners_=void 0,this.dehancer=void 0,this.enhancer_=s,this.name_=f,pt(Map)||X(18),go(function(){h.keysAtom_=Kv(Qt.NODE_ENV!=="production"?h.name_+".keys()":"ObservableMap.keys()"),h.data_=new Map,h.hasMap_=new Map,u&&h.merge(u)})}var r=i.prototype;return r.has_=function(s){return this.data_.has(s)},r.has=function(s){var f=this;if(!B.trackingDerivation)return this.has_(s);var h=this.hasMap_.get(s);if(!h){var p=h=new si(this.has_(s),vu,this.name_+"."+ld(s)+"?",!1);this.hasMap_.set(s,p),Ey(p,function(){return f.hasMap_.delete(s)})}return h.get()},r.set=function(s,f){var h=this.has_(s);if(Dn(this)){var p=Cn(this,{type:h?na:vo,object:this,newValue:f,name:s});if(!p)return this;f=p.newValue}return h?this.updateValue_(s,f):this.addValue_(s,f),this},r.delete=function(s){var f=this;if(ya(this.keysAtom_),Dn(this)){var h=Cn(this,{type:Mu,object:this,name:s});if(!h)return!1}if(this.has_(s)){var p=wt(),b=ea(this),g=b||p?{observableKind:"map",debugObjectName:this.name_,type:Mu,object:this,oldValue:this.data_.get(s).value_,name:s}:null;return p&&sn(g),Pa(function(){var N;f.keysAtom_.reportChanged(),(N=f.hasMap_.get(s))==null||N.setNewValue_(!1);var U=f.data_.get(s);U.setNewValue_(void 0),f.data_.delete(s)}),b&&ta(this,g),p&&un(),!0}return!1},r.updateValue_=function(s,f){var h=this.data_.get(s);if(f=h.prepareNewValue_(f),f!==B.UNCHANGED){var p=wt(),b=ea(this),g=b||p?{observableKind:"map",debugObjectName:this.name_,type:na,object:this,oldValue:h.value_,name:s,newValue:f}:null;p&&sn(g),h.setNewValue_(f),b&&ta(this,g),p&&un()}},r.addValue_=function(s,f){var h=this;ya(this.keysAtom_),Pa(function(){var N,U=new si(f,h.enhancer_,h.name_+"."+ld(s),!1);h.data_.set(s,U),f=U.value_,(N=h.hasMap_.get(s))==null||N.setNewValue_(!0),h.keysAtom_.reportChanged()});var p=wt(),b=ea(this),g=b||p?{observableKind:"map",debugObjectName:this.name_,type:vo,object:this,name:s,newValue:f}:null;p&&sn(g),b&&ta(this,g),p&&un()},r.get=function(s){return this.has(s)?this.dehanceValue_(this.data_.get(s).get()):this.dehanceValue_(void 0)},r.dehanceValue_=function(s){return this.dehancer!==void 0?this.dehancer(s):s},r.keys=function(){return this.keysAtom_.reportObserved(),this.data_.keys()},r.values=function(){var s=this,f=this.keys();return Uy({next:function(){var p=f.next(),b=p.done,g=p.value;return{done:b,value:b?void 0:s.get(g)}}})},r.entries=function(){var s=this,f=this.keys();return Uy({next:function(){var p=f.next(),b=p.done,g=p.value;return{done:b,value:b?void 0:[g,s.get(g)]}}})},r[Symbol.iterator]=function(){return this.entries()},r.forEach=function(s,f){for(var h=ol(this),p;!(p=h()).done;){var b=p.value,g=b[0],N=b[1];s.call(f,N,g,this)}},r.merge=function(s){var f=this;return yo(s)&&(s=new Map(s)),Pa(function(){on(s)?lO(s).forEach(function(h){return f.set(h,s[h])}):Array.isArray(s)?s.forEach(function(h){var p=h[0],b=h[1];return f.set(p,b)}):tl(s)?(iO(s)||X(19,s),s.forEach(function(h,p){return f.set(p,h)})):s!=null&&X(20,s)}),this},r.clear=function(){var s=this;Pa(function(){cy(function(){for(var f=ol(s.keys()),h;!(h=f()).done;){var p=h.value;s.delete(p)}})})},r.replace=function(s){var f=this;return Pa(function(){for(var h=PE(s),p=new Map,b=!1,g=ol(f.data_.keys()),N;!(N=g()).done;){var U=N.value;if(!h.has(U)){var x=f.delete(U);if(x)b=!0;else{var O=f.data_.get(U);p.set(U,O)}}}for(var L=ol(h.entries()),I;!(I=L()).done;){var te=I.value,ne=te[0],ie=te[1],Qe=f.data_.has(ne);if(f.set(ne,ie),f.data_.has(ne)){var $e=f.data_.get(ne);p.set(ne,$e),Qe||(b=!0)}}if(!b)if(f.data_.size!==p.size)f.keysAtom_.reportChanged();else for(var Ne=f.data_.keys(),Ze=p.keys(),Fe=Ne.next(),ke=Ze.next();!Fe.done;){if(Fe.value!==ke.value){f.keysAtom_.reportChanged();break}Fe=Ne.next(),ke=Ze.next()}f.data_=p}),this},r.toString=function(){return"[object ObservableMap]"},r.toJSON=function(){return Array.from(this)},r.observe_=function(s,f){return f===!0&&X("`observe` doesn't support fireImmediately=true in combination with maps."),Dr(this,s)},r.intercept_=function(s){return zr(this,s)},al(i,[{key:"size",get:function(){return this.keysAtom_.reportObserved(),this.data_.size}},{key:Symbol.toStringTag,get:function(){return"Map"}}])}(),yo=ii("ObservableMap",jy);function Uy(i){return i[Symbol.toStringTag]="MapIterator",Rd(i)}function PE(i){if(tl(i)||yo(i))return i;if(Array.isArray(i))return new Map(i);if(on(i)){var r=new Map;for(var u in i)r.set(u,i[u]);return r}else return X(21,i)}var $E={},Ny=function(){function i(u,s,f){var h=this;s===void 0&&(s=li),f===void 0&&(f="ObservableSet@"+Fn()),this.name_=void 0,this[re]=$E,this.data_=new Set,this.atom_=void 0,this.changeListeners_=void 0,this.interceptors_=void 0,this.dehancer=void 0,this.enhancer_=void 0,this.name_=f,pt(Set)||X(22),this.enhancer_=function(p,b){return s(p,b,f)},go(function(){h.atom_=Kv(h.name_),u&&h.replace(u)})}var r=i.prototype;return r.dehanceValue_=function(s){return this.dehancer!==void 0?this.dehancer(s):s},r.clear=function(){var s=this;Pa(function(){cy(function(){for(var f=ol(s.data_.values()),h;!(h=f()).done;){var p=h.value;s.delete(p)}})})},r.forEach=function(s,f){for(var h=ol(this),p;!(p=h()).done;){var b=p.value;s.call(f,b,b,this)}},r.add=function(s){var f=this;if(ya(this.atom_),Dn(this)){var h=Cn(this,{type:vo,object:this,newValue:s});if(!h)return this;s=h.newValue}if(!this.has(s)){Pa(function(){f.data_.add(f.enhancer_(s,void 0)),f.atom_.reportChanged()});var p=wt(),b=ea(this),g=b||p?{observableKind:"set",debugObjectName:this.name_,type:vo,object:this,newValue:s}:null;p&&Qt.NODE_ENV!=="production"&&sn(g),b&&ta(this,g),p&&Qt.NODE_ENV!=="production"&&un()}return this},r.delete=function(s){var f=this;if(Dn(this)){var h=Cn(this,{type:Mu,object:this,oldValue:s});if(!h)return!1}if(this.has(s)){var p=wt(),b=ea(this),g=b||p?{observableKind:"set",debugObjectName:this.name_,type:Mu,object:this,oldValue:s}:null;return p&&Qt.NODE_ENV!=="production"&&sn(g),Pa(function(){f.atom_.reportChanged(),f.data_.delete(s)}),b&&ta(this,g),p&&Qt.NODE_ENV!=="production"&&un(),!0}return!1},r.has=function(s){return this.atom_.reportObserved(),this.data_.has(this.dehanceValue_(s))},r.entries=function(){var s=this.values();return ky({next:function(){var h=s.next(),p=h.value,b=h.done;return b?{value:void 0,done:b}:{value:[p,p],done:b}}})},r.keys=function(){return this.values()},r.values=function(){this.atom_.reportObserved();var s=this,f=this.data_.values();return ky({next:function(){var p=f.next(),b=p.value,g=p.done;return g?{value:void 0,done:g}:{value:s.dehanceValue_(b),done:g}}})},r.intersection=function(s){if(Ha(s)&&!ga(s))return s.intersection(this);var f=new Set(this);return f.intersection(s)},r.union=function(s){if(Ha(s)&&!ga(s))return s.union(this);var f=new Set(this);return f.union(s)},r.difference=function(s){return new Set(this).difference(s)},r.symmetricDifference=function(s){if(Ha(s)&&!ga(s))return s.symmetricDifference(this);var f=new Set(this);return f.symmetricDifference(s)},r.isSubsetOf=function(s){return new Set(this).isSubsetOf(s)},r.isSupersetOf=function(s){return new Set(this).isSupersetOf(s)},r.isDisjointFrom=function(s){if(Ha(s)&&!ga(s))return s.isDisjointFrom(this);var f=new Set(this);return f.isDisjointFrom(s)},r.replace=function(s){var f=this;return ga(s)&&(s=new Set(s)),Pa(function(){Array.isArray(s)?(f.clear(),s.forEach(function(h){return f.add(h)})):Ha(s)?(f.clear(),s.forEach(function(h){return f.add(h)})):s!=null&&X("Cannot initialize set from "+s)}),this},r.observe_=function(s,f){return f===!0&&X("`observe` doesn't support fireImmediately=true in combination with sets."),Dr(this,s)},r.intercept_=function(s){return zr(this,s)},r.toJSON=function(){return Array.from(this)},r.toString=function(){return"[object ObservableSet]"},r[Symbol.iterator]=function(){return this.values()},al(i,[{key:"size",get:function(){return this.atom_.reportObserved(),this.data_.size}},{key:Symbol.toStringTag,get:function(){return"Set"}}])}(),ga=ii("ObservableSet",Ny);function ky(i){return i[Symbol.toStringTag]="SetIterator",Rd(i)}var Hy=Object.create(null),Ly="remove",Ed=function(){function i(u,s,f,h){s===void 0&&(s=new Map),h===void 0&&(h=kO),this.target_=void 0,this.values_=void 0,this.name_=void 0,this.defaultAnnotation_=void 0,this.keysAtom_=void 0,this.changeListeners_=void 0,this.interceptors_=void 0,this.proxy_=void 0,this.isPlainObject_=void 0,this.appliedAnnotations_=void 0,this.pendingKeys_=void 0,this.target_=u,this.values_=s,this.name_=f,this.defaultAnnotation_=h,this.keysAtom_=new mo(this.name_+".keys"),this.isPlainObject_=on(this.target_),Ky(this.defaultAnnotation_)||X("defaultAnnotation must be valid annotation"),this.appliedAnnotations_={}}var r=i.prototype;return r.getObservablePropValue_=function(s){return this.values_.get(s).get()},r.setObservablePropValue_=function(s,f){var h=this.values_.get(s);if(h instanceof An)return h.set(f),!0;if(Dn(this)){var p=Cn(this,{type:na,object:this.proxy_||this.target_,name:s,newValue:f});if(!p)return null;f=p.newValue}if(f=h.prepareNewValue_(f),f!==B.UNCHANGED){var b=ea(this),g=wt(),N=b||g?{type:na,observableKind:"object",debugObjectName:this.name_,object:this.proxy_||this.target_,oldValue:h.value_,name:s,newValue:f}:null;g&&sn(N),h.setNewValue_(f),b&&ta(this,N),g&&un()}return!0},r.get_=function(s){return B.trackingDerivation&&!wn(this.target_,s)&&this.has_(s),this.target_[s]},r.set_=function(s,f,h){return h===void 0&&(h=!1),wn(this.target_,s)?this.values_.has(s)?this.setObservablePropValue_(s,f):h?Reflect.set(this.target_,s,f):(this.target_[s]=f,!0):this.extend_(s,{value:f,enumerable:!0,writable:!0,configurable:!0},this.defaultAnnotation_,h)},r.has_=function(s){if(!B.trackingDerivation)return s in this.target_;this.pendingKeys_||(this.pendingKeys_=new Map);var f=this.pendingKeys_.get(s);return f||(f=new si(s in this.target_,vu,this.name_+"."+ld(s)+"?",!1),this.pendingKeys_.set(s,f)),f.get()},r.make_=function(s,f){if(f===!0&&(f=this.defaultAnnotation_),f!==!1){if(Py(this,f,s),!(s in this.target_)){var h;if((h=this.target_[Zt])!=null&&h[s])return;X(1,f.annotationType_,this.name_+"."+s.toString())}for(var p=this.target_;p&&p!==fu;){var b=cu(p,s);if(b){var g=f.make_(this,s,b,p);if(g===0)return;if(g===1)break}p=Object.getPrototypeOf(p)}Vy(this,f,s)}},r.extend_=function(s,f,h,p){if(p===void 0&&(p=!1),h===!0&&(h=this.defaultAnnotation_),h===!1)return this.defineProperty_(s,f,p);Py(this,h,s);var b=h.extend_(this,s,f,p);return b&&Vy(this,h,s),b},r.defineProperty_=function(s,f,h){h===void 0&&(h=!1),ya(this.keysAtom_);try{Rn();var p=this.delete_(s);if(!p)return p;if(Dn(this)){var b=Cn(this,{object:this.proxy_||this.target_,name:s,type:vo,newValue:f.value});if(!b)return null;var g=b.newValue;f.value!==g&&(f=La({},f,{value:g}))}if(h){if(!Reflect.defineProperty(this.target_,s,f))return!1}else ma(this.target_,s,f);this.notifyPropertyAddition_(s,f.value)}finally{zn()}return!0},r.defineObservableProperty_=function(s,f,h,p){p===void 0&&(p=!1),ya(this.keysAtom_);try{Rn();var b=this.delete_(s);if(!b)return b;if(Dn(this)){var g=Cn(this,{object:this.proxy_||this.target_,name:s,type:vo,newValue:f});if(!g)return null;f=g.newValue}var N=By(s),U={configurable:B.safeDescriptors?this.isPlainObject_:!0,enumerable:!0,get:N.get,set:N.set};if(p){if(!Reflect.defineProperty(this.target_,s,U))return!1}else ma(this.target_,s,U);var x=new si(f,h,Qt.NODE_ENV!=="production"?this.name_+"."+s.toString():"ObservableObject.key",!1);this.values_.set(s,x),this.notifyPropertyAddition_(s,x.value_)}finally{zn()}return!0},r.defineComputedProperty_=function(s,f,h){h===void 0&&(h=!1),ya(this.keysAtom_);try{Rn();var p=this.delete_(s);if(!p)return p;if(Dn(this)){var b=Cn(this,{object:this.proxy_||this.target_,name:s,type:vo,newValue:void 0});if(!b)return null}f.name||(f.name=Qt.NODE_ENV!=="production"?this.name_+"."+s.toString():"ObservableObject.key"),f.context=this.proxy_||this.target_;var g=By(s),N={configurable:B.safeDescriptors?this.isPlainObject_:!0,enumerable:!1,get:g.get,set:g.set};if(h){if(!Reflect.defineProperty(this.target_,s,N))return!1}else ma(this.target_,s,N);this.values_.set(s,new An(f)),this.notifyPropertyAddition_(s,void 0)}finally{zn()}return!0},r.delete_=function(s,f){if(f===void 0&&(f=!1),ya(this.keysAtom_),!wn(this.target_,s))return!0;if(Dn(this)){var h=Cn(this,{object:this.proxy_||this.target_,name:s,type:Ly});if(!h)return null}try{var p;Rn();var b=ea(this),g=Qt.NODE_ENV!=="production"&&wt(),N=this.values_.get(s),U=void 0;if(!N&&(b||g)){var x;U=(x=cu(this.target_,s))==null?void 0:x.value}if(f){if(!Reflect.deleteProperty(this.target_,s))return!1}else delete this.target_[s];if(Qt.NODE_ENV!=="production"&&delete this.appliedAnnotations_[s],N&&(this.values_.delete(s),N instanceof si&&(U=N.value_),vy(N)),this.keysAtom_.reportChanged(),(p=this.pendingKeys_)==null||(p=p.get(s))==null||p.set(s in this.target_),b||g){var O={type:Ly,observableKind:"object",object:this.proxy_||this.target_,debugObjectName:this.name_,oldValue:U,name:s};Qt.NODE_ENV!=="production"&&g&&sn(O),b&&ta(this,O),Qt.NODE_ENV!=="production"&&g&&un()}}finally{zn()}return!0},r.observe_=function(s,f){return f===!0&&X("`observe` doesn't support the fire immediately property for observable objects."),Dr(this,s)},r.intercept_=function(s){return zr(this,s)},r.notifyPropertyAddition_=function(s,f){var h,p=ea(this),b=wt();if(p||b){var g=p||b?{type:vo,observableKind:"object",debugObjectName:this.name_,object:this.proxy_||this.target_,name:s,newValue:f}:null;b&&sn(g),p&&ta(this,g),b&&un()}(h=this.pendingKeys_)==null||(h=h.get(s))==null||h.set(!0),this.keysAtom_.reportChanged()},r.ownKeys_=function(){return this.keysAtom_.reportObserved(),nl(this.target_)},r.keys_=function(){return this.keysAtom_.reportObserved(),Object.keys(this.target_)},i}();function di(i,r){var u;if(r&&sl(i)&&X("Options can't be provided for already observable objects."),wn(i,re))return Yy(i)instanceof Ed||X("Cannot convert '"+ju(i)+`' into observable object:
The target is already observable of different type.
Extending builtins is not supported.`),i;Object.isExtensible(i)||X("Cannot make the designated object observable; it is not extensible");var s=(u=r?.name)!=null?u:(on(i)?"ObservableObject":i.constructor.name)+"@"+Fn(),f=new Ed(i,new Map,String(s),IO(r));return Sr(i,re,f),i}var qE=ii("ObservableObjectAdministration",Ed);function By(i){return Hy[i]||(Hy[i]={get:function(){return this[re].getObservablePropValue_(i)},set:function(u){return this[re].setObservablePropValue_(i,u)}})}function sl(i){return hu(i)?qE(i[re]):!1}function Vy(i,r,u){var s;i.appliedAnnotations_[u]=r,(s=i.target_[Zt])==null||delete s[u]}function Py(i,r,u){if(Ky(r)||X("Cannot annotate '"+i.name_+"."+u.toString()+"': Invalid annotation."),!yu(r)&&wn(i.appliedAnnotations_,u)){var s=i.name_+"."+u.toString(),f=i.appliedAnnotations_[u].annotationType_,h=r.annotationType_;X("Cannot apply '"+h+"' to '"+s+"':"+(`
The field is already annotated with '`+f+"'.")+`
Re-annotating fields is not allowed.
Use 'override' annotation for methods overridden by subclass.`)}}var GE=qy(0),YE=function(){var i=!1,r={};return Object.defineProperty(r,"0",{set:function(){i=!0}}),Object.create(r)[0]=1,i===!1}(),wd=0,$y=function(){};function XE(i,r){Object.setPrototypeOf?Object.setPrototypeOf(i.prototype,r):i.prototype.__proto__!==void 0?i.prototype.__proto__=r:i.prototype=r}XE($y,Array.prototype);var Ad=function(i){function r(s,f,h,p){var b;return h===void 0&&(h="ObservableArray@"+Fn()),p===void 0&&(p=!1),b=i.call(this)||this,go(function(){var g=new Od(h,f,p,!0);g.proxy_=b,Yv(b,re,g),s&&s.length&&b.spliceWithArray(0,0,s),YE&&Object.defineProperty(b,"0",GE)}),b}Zv(r,i);var u=r.prototype;return u.concat=function(){this[re].atom_.reportObserved();for(var f=arguments.length,h=new Array(f),p=0;p<f;p++)h[p]=arguments[p];return Array.prototype.concat.apply(this.slice(),h.map(function(b){return Cu(b)?b.slice():b}))},u[Symbol.iterator]=function(){var s=this,f=0;return Rd({next:function(){return f<s.length?{value:s[f++],done:!1}:{done:!0,value:void 0}}})},al(r,[{key:"length",get:function(){return this[re].getArrayLength_()},set:function(f){this[re].setArrayLength_(f)}},{key:Symbol.toStringTag,get:function(){return"Array"}}])}($y);Object.entries(Du).forEach(function(i){var r=i[0],u=i[1];r!=="concat"&&Sr(Ad.prototype,r,u)});function qy(i){return{enumerable:!1,configurable:!0,get:function(){return this[re].get_(i)},set:function(u){this[re].set_(i,u)}}}function IE(i){ma(Ad.prototype,""+i,qy(i))}function Gy(i){if(i>wd){for(var r=wd;r<i+100;r++)IE(r);wd=i}}Gy(1e3);function QE(i,r,u){return new Ad(i,r,u)}function ul(i,r){if(typeof i=="object"&&i!==null){if(Cu(i))return r!==void 0&&X(23),i[re].atom_;if(ga(i))return i.atom_;if(yo(i)){if(r===void 0)return i.keysAtom_;var u=i.data_.get(r)||i.hasMap_.get(r);return u||X(25,r,ju(i)),u}if(sl(i)){if(!r)return X(26);var s=i[re].values_.get(r);return s||X(27,r,ju(i)),s}if(sd(i)||Ou(i)||Ru(i))return i}else if(pt(i)&&Ru(i[re]))return i[re];X(28)}function Yy(i,r){if(i||X(29),sd(i)||Ou(i)||Ru(i)||yo(i)||ga(i))return i;if(i[re])return i[re];X(24,i)}function ju(i,r){var u;if(r!==void 0)u=ul(i,r);else{if(ll(i))return i.name;sl(i)||yo(i)||ga(i)?u=Yy(i):u=ul(i)}return u.name_}function go(i){var r=ui(),u=dd(!0);Rn();try{return i()}finally{zn(),hd(u),Ba(r)}}var Xy=fu.toString;function Iy(i,r,u){return u===void 0&&(u=-1),xd(i,r,u)}function xd(i,r,u,s,f){if(i===r)return i!==0||1/i===1/r;if(i==null||r==null)return!1;if(i!==i)return r!==r;var h=typeof i;if(h!=="function"&&h!=="object"&&typeof r!="object")return!1;var p=Xy.call(i);if(p!==Xy.call(r))return!1;switch(p){case"[object RegExp]":case"[object String]":return""+i==""+r;case"[object Number]":return+i!=+i?+r!=+r:+i==0?1/+i===1/r:+i==+r;case"[object Date]":case"[object Boolean]":return+i==+r;case"[object Symbol]":return typeof Symbol<"u"&&Symbol.valueOf.call(i)===Symbol.valueOf.call(r);case"[object Map]":case"[object Set]":u>=0&&u++;break}i=Qy(i),r=Qy(r);var b=p==="[object Array]";if(!b){if(typeof i!="object"||typeof r!="object")return!1;var g=i.constructor,N=r.constructor;if(g!==N&&!(pt(g)&&g instanceof g&&pt(N)&&N instanceof N)&&"constructor"in i&&"constructor"in r)return!1}if(u===0)return!1;u<0&&(u=-1),s=s||[],f=f||[];for(var U=s.length;U--;)if(s[U]===i)return f[U]===r;if(s.push(i),f.push(r),b){if(U=i.length,U!==r.length)return!1;for(;U--;)if(!xd(i[U],r[U],u-1,s,f))return!1}else{var x=Object.keys(i),O=x.length;if(Object.keys(r).length!==O)return!1;for(var L=0;L<O;L++){var I=x[L];if(!(wn(r,I)&&xd(i[I],r[I],u-1,s,f)))return!1}}return s.pop(),f.pop(),!0}function Qy(i){return Cu(i)?i.slice():tl(i)||yo(i)||Ha(i)||ga(i)?Array.from(i.entries()):i}var Zy,ZE=((Zy=uu().Iterator)==null?void 0:Zy.prototype)||{};function Rd(i){return i[Symbol.iterator]=KE,Object.assign(Object.create(ZE),i)}function KE(){return this}function Ky(i){return i instanceof Object&&typeof i.annotationType_=="string"&&pt(i.make_)&&pt(i.extend_)}["Symbol","Map","Set"].forEach(function(i){var r=uu();typeof r[i]>"u"&&X("MobX requires global '"+i+"' to be available or polyfilled")}),typeof __MOBX_DEVTOOLS_GLOBAL_HOOK__=="object"&&__MOBX_DEVTOOLS_GLOBAL_HOOK__.injectMobx({spy:hE,extras:{getDebugName:ju},$mobx:re});function Mn(i,r){return Cr(i,r,new Map)}function Cr(i,r,u){if(i===r||Number.isNaN(i)&&Number.isNaN(r))return!0;const s=typeof i;if(s!==typeof r)return!1;if(i===null||r===null||s!=="object")return i===r;if(u.has(i)&&u.get(i)===r)return!0;if(u.set(i,r),i instanceof Date&&r instanceof Date)return i.getTime()===r.getTime();if(i instanceof RegExp&&r instanceof RegExp)return i.toString()===r.toString();if(i instanceof Map&&r instanceof Map){if(i.size!==r.size)return!1;for(const[g,N]of i){if(!r.has(g))return!1;const U=r.get(g);if(!Cr(N,U,u))return!1}return!0}if(i instanceof Set&&r instanceof Set){if(i.size!==r.size)return!1;for(const g of i){let N=!1;for(const U of r)if(Cr(g,U,u)){N=!0;break}if(!N)return!1}return!0}if(Array.isArray(i)&&Array.isArray(r)){if(i.length!==r.length)return!1;for(let g=0;g<i.length;g++)if(!Cr(i[g],r[g],u))return!1;return!0}if(typeof i!="object"||i===null||typeof r!="object"||r===null)return!1;const h=Object.keys(i),p=Object.keys(r);if(h.length!==p.length)return!1;const b=new Set(p);for(const g of h){if(!b.has(g))return!1;const N=i[g],U=r[g];if(!Cr(N,U,u))return!1}return!0}mt({enforceActions:"observed"});let JE=class{pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)}};const zd=new JE;mt({enforceActions:"observed"});let WE=class{pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)}};const Jy=new WE;mt({enforceActions:"observed"});let FE=class{pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)}};const Dd=new FE,Cd={IS_DEV:!!window.GLOBAL_CONFIG.IS_DEV,AUTHORIZATION:String(window.GLOBAL_CONFIG.AUTHORIZATION)},Wy=i=>`${window.location.pathname}?action=${i}`,oa=async(i,r={})=>{console.log(Cd);const u={method:"GET",headers:{"Content-Type":"application/json",...Cd.AUTHORIZATION?{Authorization:Cd.AUTHORIZATION||""}:{}},cache:"no-cache",credentials:"omit",...r},s=await fetch(Wy(i),u);return{status:s.status,data:s.ok?await s.json().catch(()=>null):null}};if(!K.useState)throw new Error("mobx-react-lite requires React with Hooks support");if(!NE)throw new Error("mobx-react-lite@3 requires mobx at least version 6 to be available");var Fy=Hv();function ew(i){i()}function tw(i){i||(i=ew,console.warn("[MobX] Failed to get unstable_batched updates from react-dom / react-native")),mt({reactionScheduler:i})}function nw(i){return xy(i)}var aw=1e4,ow=1e4,iw=function(){function i(r){var u=this;Object.defineProperty(this,"finalize",{enumerable:!0,configurable:!0,writable:!0,value:r}),Object.defineProperty(this,"registrations",{enumerable:!0,configurable:!0,writable:!0,value:new Map}),Object.defineProperty(this,"sweepTimeout",{enumerable:!0,configurable:!0,writable:!0,value:void 0}),Object.defineProperty(this,"sweep",{enumerable:!0,configurable:!0,writable:!0,value:function(s){s===void 0&&(s=aw),clearTimeout(u.sweepTimeout),u.sweepTimeout=void 0;var f=Date.now();u.registrations.forEach(function(h,p){f-h.registeredAt>=s&&(u.finalize(h.value),u.registrations.delete(p))}),u.registrations.size>0&&u.scheduleSweep()}}),Object.defineProperty(this,"finalizeAllImmediately",{enumerable:!0,configurable:!0,writable:!0,value:function(){u.sweep(0)}})}return Object.defineProperty(i.prototype,"register",{enumerable:!1,configurable:!0,writable:!0,value:function(r,u,s){this.registrations.set(s,{value:u,registeredAt:Date.now()}),this.scheduleSweep()}}),Object.defineProperty(i.prototype,"unregister",{enumerable:!1,configurable:!0,writable:!0,value:function(r){this.registrations.delete(r)}}),Object.defineProperty(i.prototype,"scheduleSweep",{enumerable:!1,configurable:!0,writable:!0,value:function(){this.sweepTimeout===void 0&&(this.sweepTimeout=setTimeout(this.sweep,ow))}}),i}(),lw=typeof FinalizationRegistry<"u"?FinalizationRegistry:iw,Md=new lw(function(i){var r;(r=i.reaction)===null||r===void 0||r.dispose(),i.reaction=null}),jd={exports:{}},Ud={},eg;function rw(){if(eg)return Ud;eg=1;return function(){function i(L,I){return L===I&&(L!==0||1/L===1/I)||L!==L&&I!==I}function r(L,I){U||f.startTransition===void 0||(U=!0,console.error("You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."));var te=I();if(!x){var ne=I();h(te,ne)||(console.error("The result of getSnapshot should be cached to avoid an infinite loop"),x=!0)}ne=p({inst:{value:te,getSnapshot:I}});var ie=ne[0].inst,Qe=ne[1];return g(function(){ie.value=te,ie.getSnapshot=I,u(ie)&&Qe({inst:ie})},[L,te,I]),b(function(){return u(ie)&&Qe({inst:ie}),L(function(){u(ie)&&Qe({inst:ie})})},[L]),N(te),te}function u(L){var I=L.getSnapshot;L=L.value;try{var te=I();return!h(L,te)}catch{return!0}}function s(L,I){return I()}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var f=gr(),h=typeof Object.is=="function"?Object.is:i,p=f.useState,b=f.useEffect,g=f.useLayoutEffect,N=f.useDebugValue,U=!1,x=!1,O=typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"?s:r;Ud.useSyncExternalStore=f.useSyncExternalStore!==void 0?f.useSyncExternalStore:O,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())}(),Ud}var tg;function sw(){return tg||(tg=1,jd.exports=rw()),jd.exports}var uw=sw();function ng(i){i.reaction=new Va("observer".concat(i.name),function(){var r;i.stateVersion=Symbol(),(r=i.onStoreChange)===null||r===void 0||r.call(i)})}function cw(i,r){r===void 0&&(r="observed");var u=Vv.useRef(null);if(!u.current){var s={reaction:null,onStoreChange:null,stateVersion:Symbol(),name:r,subscribe:function(b){return Md.unregister(s),s.onStoreChange=b,s.reaction||(ng(s),s.stateVersion=Symbol()),function(){var g;s.onStoreChange=null,(g=s.reaction)===null||g===void 0||g.dispose(),s.reaction=null}},getSnapshot:function(){return s.stateVersion}};u.current=s}var f=u.current;f.reaction||(ng(f),Md.register(u,f,f)),Vv.useDebugValue(f.reaction,nw),uw.useSyncExternalStore(f.subscribe,f.getSnapshot,f.getSnapshot);var h,p;if(f.reaction.track(function(){try{h=i()}catch(b){p=b}}),p)throw p;return h}var Nd,kd,ag=!0,og=typeof Symbol=="function"&&Symbol.for,fw=(kd=(Nd=Object.getOwnPropertyDescriptor(function(){},"name"))===null||Nd===void 0?void 0:Nd.configurable)!==null&&kd!==void 0?kd:!1,ig=og?Symbol.for("react.forward_ref"):typeof K.forwardRef=="function"&&K.forwardRef(function(i){return null}).$$typeof,lg=og?Symbol.for("react.memo"):typeof K.memo=="function"&&K.memo(function(i){return null}).$$typeof;function be(i,r){var u;if(lg&&i.$$typeof===lg)throw new Error("[mobx-react-lite] You are trying to use `observer` on a function component wrapped in either another `observer` or `React.memo`. The observer already applies 'React.memo' for you.");var s=(u=void 0)!==null&&u!==void 0?u:!1,f=i,h=i.displayName||i.name;if(ig&&i.$$typeof===ig&&(s=!0,f=i.render,typeof f!="function"))throw new Error("[mobx-react-lite] `render` property of ForwardRef was not a function");var p=function(b,g){return cw(function(){return f(b,g)},h)};return p.displayName=i.displayName,fw&&Object.defineProperty(p,"name",{value:i.name,writable:!0,configurable:!0}),i.contextTypes&&(p.contextTypes=i.contextTypes,ag&&(ag=!1,console.warn("[mobx-react-lite] Support for Legacy Context in function components will be removed in the next major release."))),s&&(p=K.forwardRef(p)),p=K.memo(p),hw(i,p),Object.defineProperty(p,"contextTypes",{set:function(){var b,g;throw new Error("[mobx-react-lite] `".concat(this.displayName||((b=this.type)===null||b===void 0?void 0:b.displayName)||((g=this.type)===null||g===void 0?void 0:g.name)||"Component",".contextTypes` must be set before applying `observer`."))}}),p}var dw={$$typeof:!0,render:!0,compare:!0,type:!0,displayName:!0};function hw(i,r){Object.keys(i).forEach(function(u){dw[u]||Object.defineProperty(r,u,Object.getOwnPropertyDescriptor(i,u))})}var Hd;tw(Fy.unstable_batchedUpdates),Hd=Md.finalizeAllImmediately;const pw={"":{ja:`Project-Id-Version: X-Prober
POT-Creation-Date: 
PO-Revision-Date: 2025-08-16 22:48+0800
Last-Translator: Km.Van <kmvan.com@gmail.com>
Language-Team: Km.Van <inn-studio.com>
Language: ja
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=1; plural=0;
X-Generator: Poedit 3.4.2
X-Poedit-Basepath: .
X-Poedit-SourceCharset: UTF-8
X-Poedit-KeywordsList: gettext
`,zh:`Project-Id-Version: X-Prober
Report-Msgid-Bugs-To: 
POT-Creation-Date: 
PO-Revision-Date: 2025-08-16 22:48+0800
Last-Translator: Km.Van <kmvan.com@gmail.com>
Language-Team: kmvan <kmvan.com@gmail.com>
Language: zh_CN
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Poedit-KeywordsList: gettext
X-Poedit-Basepath: .
X-Poedit-SourceCharset: UTF-8
X-Generator: Poedit 3.4.2
X-Poedit-Flags-xgettext: --add-comments
`,zhcn:`Project-Id-Version: X-Prober
Report-Msgid-Bugs-To: 
POT-Creation-Date: 
PO-Revision-Date: 2025-08-16 22:48+0800
Last-Translator: Km.Van <kmvan.com@gmail.com>
Language-Team: kmvan <kmvan.com@gmail.com>
Language: zh_CN
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Poedit-KeywordsList: gettext
X-Poedit-Basepath: .
X-Poedit-SourceCharset: UTF-8
X-Generator: Poedit 3.4.2
X-Poedit-Flags-xgettext: --add-comments
`,zhhk:`Project-Id-Version: X-Prober
Report-Msgid-Bugs-To: 
POT-Creation-Date: 
PO-Revision-Date: 2025-08-16 22:48+0800
Last-Translator: Km.Van <kmvan.com@gmail.com>
Language-Team: Km.Van <inn-studio.com>
Language: zh_HK
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Poedit-KeywordsList: gettext
X-Poedit-Basepath: .
X-Poedit-SourceCharset: UTF-8
X-Generator: Poedit 3.4.2
X-Poedit-Flags-xgettext: --add-comments
`,zhtw:`Project-Id-Version: X-Prober
Report-Msgid-Bugs-To: 
POT-Creation-Date: 
PO-Revision-Date: 2025-08-16 22:48+0800
Last-Translator: Km.Van <kmvan.com@gmail.com>
Language-Team: Km.Van <inn-studio.com>
Language: zh_TW
MIME-Version: 1.0
Content-Type: text/plain; charset=UTF-8
Content-Transfer-Encoding: 8bit
Plural-Forms: nplurals=2; plural=n != 1;
X-Poedit-KeywordsList: gettext
X-Poedit-Basepath: .
X-Poedit-SourceCharset: UTF-8
X-Generator: Poedit 3.4.2
X-ZhConverter: 繁化姬 dict-b76338ce-r665 @ 2019/11/20 14:24:08 | https://zhconvert.org
X-Poedit-Flags-xgettext: --add-comments
`},"{{days}}d {{hours}}h {{mins}}min {{secs}}s":{ja:"{{days}}日 {{hours}}時間 {{mins}}分 {{secs}}秒",zh:"{{days}} 天 {{hours}} 时 {{mins}} 分 {{secs}} 秒",zhcn:"{{days}} 天 {{hours}} 时 {{mins}} 分 {{secs}} 秒",zhhk:"{{days}}天 {{hours}}小時 {{mins}}分鐘 {{secs}}秒",zhtw:"{{days}}天 {{hours}}小時 {{mins}}分鐘 {{secs}}秒"},"{{minute}} minute average":{ja:"{{minute}}分平均負荷",zh:"{{minute}} 分钟平均负载",zhcn:"{{minute}} 分钟平均负载",zhhk:"{{minute}} 分鐘平均",zhtw:"{{minute}} 分鐘平均"},"{{oldVersion}} (Latest: {{latestPhpVersion}})":{ja:"{{oldVersion}} (最新: {{latestPhpVersion}})",zh:"{{oldVersion}}（最新版：{{latestPhpVersion}}）",zhcn:"{{oldVersion}}（最新版：{{latestPhpVersion}}）",zhhk:"{{oldVersion}}（最新版：{{latestPhpVersion}}）",zhtw:"{{oldVersion}}（最新版：{{latestPhpVersion}}）"},"{{sensor}} temperature":{ja:"{{sensor}} 温度",zh:"{{sensor}} 温度",zhcn:"{{sensor}} 温度",zhhk:"{{sensor}} 溫度",zhtw:"{{sensor}} 溫度"},"{{times}} times, min/avg/max/mdev = {{min}}/{{avg}}/{{max}}/{{mdev}} ms":{ja:"{{times}}回実行: 最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} ms",zh:"{{times}}次, 最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒",zhcn:"{{times}}次, 最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒",zhhk:"{{times}} 次，最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒",zhtw:"{{times}} 次，最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒"},"{{usage}}% CPU usage":{ja:"CPU使用率: {{usage}}%",zh:"{{usage}}% CPU 使用率",zhcn:"{{usage}}% CPU 使用率",zhhk:"CPU 使用率 {{usage}}%",zhtw:"CPU 使用率 {{usage}}%"},"⏳ Updating, please wait a second...":{ja:"⏳ 更新中...",zh:"⏳ 更新中...",zhcn:"⏳ 更新中...",zhhk:"⏳ 更新中，請稍候...",zhtw:"⏳ 更新中，請稍候..."},"✨ Found new version: {{oldVersion}} ⇢ {{newVersion}}":{ja:"✨ 新版検出: {{oldVersion}} → {{newVersion}}",zh:"✨ 发现新版本: {{oldVersion}} → {{newVersion}}",zhcn:"✨ 发现新版本: {{oldVersion}} → {{newVersion}}",zhhk:"✨ 發現新版本：{{oldVersion}} → {{newVersion}}",zhtw:"✨ 發現新版本：{{oldVersion}} → {{newVersion}}"},"❌ Update error, click here to try again?":{ja:"❌ 更新エラー [再試行]",zh:"❌ 更新错误 [点击重试]",zhcn:"❌ 更新错误 [点击重试]",zhhk:"❌ 更新錯誤，點此重試？",zhtw:"❌ 更新錯誤，點此重試？"},Benchmark:{ja:"性能測定",zh:"性能测试",zhcn:"性能测试",zhhk:"跑分測試",zhtw:"跑分測試"},"Benchmark my server":{ja:"ベンチマーク実行",zh:"测试服务器性能",zhcn:"测试服务器性能",zhhk:"測試我的伺服器",zhtw:"測試我的伺服器"},"Browser UA":{ja:"ブラウザユーザーエージェント",zh:"浏览器 UA",zhcn:"浏览器 UA",zhhk:"瀏覽器 UA",zhtw:"瀏覽器 UA"},"Buffers are in-memory block I/O buffers. They are relatively short-lived. Prior to Linux kernel version 2.4, Linux had separate page and buffer caches. Since 2.4, the page and buffer cache are unified and Buffers is raw disk blocks not represented in the page cache—i.e., not file data.":{ja:"バッファはメモリ内のブロックI/O用一時領域です。Linuxカーネル2.4以前ではページキャッシュとバッファキャッシュが分離されていましたが、2.4以降は統合され、バッファはページキャッシュに含まれない生ディスクブロック（非ファイルデータ）を指します。",zh:"缓冲区是内存中的块 I/O 缓冲区，生命周期较短。Linux 内核 2.4 版本前，页面缓存和缓冲区缓存是分离的。2.4 版本后两者统一，缓冲区指不在页面缓存中的原始磁盘块（即非文件数据）。",zhcn:"缓冲区是内存中的块 I/O 缓冲区，生命周期较短。Linux 内核 2.4 版本前，页面缓存和缓冲区缓存是分离的。2.4 版本后两者统一，缓冲区指不在页面缓存中的原始磁盘块（即非文件数据）。",zhhk:"緩衝區是記憶體中的區塊 I/O 緩衝，生命週期較短。在 Linux 核心 2.4 版之前，頁面快取和緩衝區快取是分開的。自 2.4 版起，兩者已統一，緩衝區代表未存入頁面快取的原始磁碟區塊（即非檔案資料）。",zhtw:"緩衝區是記憶體中的區塊 I/O 緩衝，生命週期較短。在 Linux 核心 2.4 版之前，頁面快取和緩衝區快取是分開的。自 2.4 版起，兩者已統一，緩衝區代表未存入頁面快取的原始磁碟區塊（即非檔案資料）。"},'Cached memory is memory that Linux uses for disk caching. However, this does not count as "used" memory, since it will be freed when applications require it. Hence you do not have to worry if a large amount is being used.':{ja:"キャッシュメモリはディスクキャッシュ用に確保された領域です。アプリケーションが必要時に解放されるため「使用中」メモリにはカウントされず、使用量が多くても問題ありません。",zh:"缓存内存是 Linux 用于磁盘缓存的内存空间，不计入“已用”内存，因为应用程序需要时会自动释放。因此即使使用量较大也无需担心。",zhcn:"缓存内存是 Linux 用于磁盘缓存的内存空间，不计入“已用”内存，因为应用程序需要时会自动释放。因此即使使用量较大也无需担心。",zhhk:"快取記憶體是 Linux 用於磁碟快取的空間，不計入「已用」記憶體，因應用程式需要時會自動釋放。故即使使用量較大亦無需擔心。",zhtw:"快取記憶體是 Linux 用於磁碟快取的空間，不計入「已用」記憶體，因應用程式需要時會自動釋放。故即使使用量較大亦無需擔心。"},"Can not fetch IP":{ja:"IP取得失敗",zh:"无法获取 IP",zhcn:"无法获取 IP",zhhk:"無法取得 IP",zhtw:"無法取得 IP"},"Can not fetch location.":{ja:"位置情報を取得できません。",zh:"无法获取位置信息。",zhcn:"无法获取位置信息。",zhhk:"無法取得地理位置。",zhtw:"無法取得地理位置。"},"Can not fetch marks data from GitHub.":{ja:"GitHubからスコアデータを取得できません。",zh:"无法从GitHub获取测试数据。",zhcn:"无法从GitHub获取测试数据。",zhhk:"無法從 GitHub 取得跑分數據。",zhtw:"無法從 GitHub 取得跑分資料。"},"Can not update file, please check the server permissions and space.":{ja:"ファイル更新失敗 サーバーの権限/空き容量を確認してください。",zh:"无法更新文件，请检查服务器权限和空间。",zhcn:"无法更新文件，请检查服务器权限和空间。",zhhk:"無法更新檔案，請檢查伺服器權限及空間。",zhtw:"無法更新檔案，請檢查伺服器權限及空間。"},"Click to close":{ja:"クリックで閉じる",zh:"点击关闭",zhcn:"点击关闭",zhhk:"點擊關閉",zhtw:"點擊關閉"},"Click to fetch":{ja:"クリックして取得",zh:"点击获取",zhcn:"点击获取",zhhk:"點擊獲取",zhtw:"點擊獲取"},"Click to update":{ja:"クリックで更新",zh:"点击更新",zhcn:"点击更新",zhhk:"點擊更新",zhtw:"點擊更新"},CPU:{ja:"CPU",zh:"处理器",zhcn:"处理器",zhhk:"中央處理器",zhtw:"中央處理器"},"CPU model":{ja:"CPUモデル",zh:"CPU 型号",zhcn:"CPU 型号",zhhk:"CPU 型號",zhtw:"CPU 型號"},"CPU usage":{ja:"CPU使用率",zh:"CPU 使用率",zhcn:"CPU 使用率",zhhk:"CPU 使用率",zhtw:"CPU 使用率"},Dark:{ja:"ダークモード",zh:"深色模式",zhcn:"深色模式",zhhk:"暗黑模式",zhtw:"深色模式"},Database:{ja:"データベース",zh:"数据库",zhcn:"数据库",zhhk:"資料庫",zhtw:"資料庫"},DB:{ja:"データベース",zh:"数据库",zhcn:"数据库",zhhk:"資料庫",zhtw:"資料庫"},Default:{ja:"デフォルト",zh:"默认",zhcn:"默认",zhhk:"預設",zhtw:"預設"},Detail:{ja:"詳細",zh:"详情",zhcn:"详情",zhhk:"詳細資料",zhtw:"詳細資料"},"Different versions cannot be compared, and different time servers have different loads, just for reference.":{ja:"異なるバージョン間の比較は不可。タイムサーバーの負荷状態により結果が変動します（参考値）",zh:"不同版本无法直接比较，不同时间服务器负载各异，结果仅供参考。",zhcn:"不同版本无法直接比较，不同时间服务器负载各异，结果仅供参考。",zhhk:"不同版本無法直接比較，且不同時間伺服器負載各異，結果僅供參考。",zhtw:"不同版本無法直接比較，且不同時間伺服器負載各異，結果僅供參考。"},"Disabled classes":{ja:"無効化クラス",zh:"禁用类",zhcn:"禁用类",zhhk:"已停用類別",zhtw:"已停用類別"},"Disabled functions":{ja:"無効化関数",zh:"禁用函数",zhcn:"禁用函数",zhhk:"已停用函式",zhtw:"已停用函式"},Disk:{ja:"ディスク",zh:"磁盘",zhcn:"磁盘",zhhk:"磁碟",zhtw:"磁碟"},"Disk Usage":{ja:"ディスク使用量",zh:"磁盘使用量",zhcn:"磁盘使用量",zhhk:"磁碟用量",zhtw:"磁碟使用量"},"Display errors":{ja:"エラー表示設定",zh:"显示错误",zhcn:"显示错误",zhhk:"顯示錯誤",zhtw:"顯示錯誤"},"Download speed test":{ja:"ダウンロード速度テスト",zh:"下载速度测试",zhcn:"下载速度测试",zhhk:"下載速度測試",zhtw:"下載速度測試"},"Error reporting":{ja:"エラーレポート設定",zh:"错误报告",zhcn:"错误报告",zhhk:"錯誤報告",zhtw:"錯誤報告"},"Error: {{error}}":{ja:"エラー: {{error}}",zh:"错误: {{error}}",zhcn:"错误: {{error}}",zhhk:"錯誤: {{error}}",zhtw:"錯誤: {{error}}"},"Failed to fetch data. Please try again later.":{ja:"データの取得に失敗しました。しばらくしてからもう一度お試しください。",zh:"无法获取数据，请稍后重试。",zhcn:"无法获取数据，请稍后重试。",zhhk:"無法獲取數據，請稍後重試。",zhtw:"無法獲取資料，請稍後重試。"},"Generate by {{appName}} and developed by {{authorName}}":{ja:"{{appName}} によって生成され、{{authorName}} によって開発されました",zh:"由 {{appName}} 生成并由 {{authorName}} 开发",zhcn:"由 {{appName}} 生成并由 {{authorName}} 开发",zhhk:"由{{appName}}生成並由{{authorName}}開發",zhtw:"由{{appName}}生成並由{{authorName}}開發"},"idle: {{idle}} \\nnice: {{nice}} \\nsys: {{sys}} \\nuser: {{user}}":{ja:"アイドル: {{idle}} \\n低優先: {{nice}} \\nシステム: {{sys}} \\nユーザー: {{user}}",zh:"空闲: {{idle}} \\n低优先级: {{nice}} \\n系统: {{sys}} \\n用户: {{user}}",zhcn:"空闲: {{idle}} \\n低优先级: {{nice}} \\n系统: {{sys}} \\n用户: {{user}}",zhhk:"閒置: {{idle}} \\n優先: {{nice}} \\n系統: {{sys}} \\n用戶: {{user}}",zhtw:"閒置: {{idle}} \\n優先: {{nice}} \\n系統: {{sys}} \\n使用者: {{user}}"},Info:{ja:"情報",zh:"信息",zhcn:"信息",zhhk:"資訊",zhtw:"資訊"},IPv4:{ja:"IPv4",zh:"IPv4",zhcn:"IPv4",zhhk:"IPv4",zhtw:"IPv4"},IPv6:{ja:"IPv6",zh:"IPv6",zhcn:"IPv6",zhhk:"IPv6",zhtw:"IPv6"},"JS Browser languages":{ja:"JS ブラウザ言語",zh:"JS 浏览器语言",zhcn:"JS 浏览器语言",zhhk:"JS 瀏覽器語言",zhtw:"JS瀏覽器語言"},'Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.':{ja:"Linuxにはメモリ使用量確認コマンドが複数存在します。「free」コマンドは物理メモリ/スワップの使用状況とカーネルバッファを表示し、「top」コマンドはシステムのリアルタイム状態を動的に表示します。",zh:"Linux 提供多种内存检测命令：“free”命令显示系统物理内存和交换空间的总用量及内核缓冲区；“top”命令提供运行中系统的实时动态视图。",zhcn:"Linux 提供多种内存检测命令：“free”命令显示系统物理内存和交换空间的总用量及内核缓冲区；“top”命令提供运行中系统的实时动态视图。",zhhk:"Linux 提供多種記憶體檢測指令：「free」指令顯示系統實體記憶體及交換區的總用量與緩衝區使用情況；「top」指令則提供運行中系統的即時動態檢視。",zhtw:"Linux 提供多種記憶體檢測指令：「free」指令顯示系統實體記憶體及交換區的總用量與緩衝區使用情況；「top」指令則提供運行中系統的即時動態檢視。"},"Loaded extensions":{ja:"ロード済み拡張機能",zh:"已加载扩展",zhcn:"已加载扩展",zhhk:"已載入擴充功能",zhtw:"已載入擴充功能"},"Loading...":{ja:"読込中...",zh:"加载中...",zhcn:"加载中...",zhhk:"載入中...",zhtw:"載入中..."},"Local IPv4":{ja:"ネイティブ IPv4",zh:"本地 IPv4",zhcn:"本地 IPv4",zhhk:"本地 IPv4",zhtw:"本地 IPv4"},"Local IPv6":{ja:"ネイティブ IPv6",zh:"本地 IPv6",zhcn:"本地 IPv6",zhhk:"本地 IPv6",zhtw:"本地 IPv6"},"Location (IPv4)":{ja:"位置情報 (IPv4)",zh:"位置 (IPv4)",zhcn:"位置 (IPv4)",zhhk:"位置 (IPv4)",zhtw:"位置 (IPv4)"},"Max execution time":{ja:"最大実行時間",zh:"最长执行时间",zhcn:"最长执行时间",zhhk:"最長執行時間",zhtw:"最長執行時間"},"Max input variables":{ja:"最大入力変数",zh:"最大输入变量数",zhcn:"最大输入变量数",zhhk:"最大輸入變數",zhtw:"最大輸入變數"},"Max memory limit":{ja:"最大メモリ制限",zh:"最大内存限制",zhcn:"最大内存限制",zhhk:"最大記憶體限制",zhtw:"最大記憶體限制"},"Max POST size":{ja:"最大POSTサイズ",zh:"最大 POST 大小",zhcn:"最大 POST 大小",zhhk:"最大 POST 容量",zhtw:"最大 POST 容量"},"Max upload size":{ja:"最大アップロードサイズ",zh:"最大上传大小",zhcn:"最大上传大小",zhhk:"最大上載容量",zhtw:"最大上傳容量"},"Memory buffers":{ja:"メモリバッファ",zh:"内存缓冲区",zhcn:"内存缓冲区",zhhk:"記憶體緩衝區",zhtw:"記憶體緩衝區"},"Memory cached":{ja:"キャッシュメモリ",zh:"缓存内存",zhcn:"缓存内存",zhhk:"記憶體快取",zhtw:"記憶體快取"},"Memory real usage":{ja:"実メモリ使用量",zh:"实际内存使用",zhcn:"实际内存使用",zhhk:"實際記憶體用量",zhtw:"實際記憶體用量"},Mine:{ja:"マイデータ",zh:"我的",zhcn:"我的",zhhk:"我的",zhtw:"我的"},"Move down":{ja:"下へ移動",zh:"下移",zhcn:"下移",zhhk:"下移",zhtw:"下移"},"Move up":{ja:"上へ移動",zh:"上移",zhcn:"上移",zhhk:"上移",zhtw:"上移"},"My Info":{ja:"マイ情報",zh:"我的信息",zhcn:"我的信息",zhhk:"我的資訊",zhtw:"我的資訊"},Name:{ja:"名称",zh:"名称",zhcn:"名称",zhhk:"名稱",zhtw:"名稱"},Network:{ja:"ネットワーク",zh:"网络",zhcn:"网络",zhhk:"網絡",zhtw:"網路"},"Network error, please try again later.":{ja:"ネットワークエラー。後ほど再試行してください。",zh:"网络错误，请稍后重试。",zhcn:"网络错误，请稍后重试。",zhhk:"網絡錯誤，請稍後重試。",zhtw:"網路錯誤，請稍後重試。"},"Network Stats":{ja:"ネットワーク統計",zh:"网络统计",zhcn:"网络统计",zhhk:"網絡統計",zhtw:"網路統計"},Nodes:{ja:"ノード",zh:"节点",zhcn:"节点",zhhk:"節點",zhtw:"節點"},"Not support":{ja:"非対応",zh:"不支持",zhcn:"不支持",zhhk:"不支援",zhtw:"不支援"},"Opcache enabled":{ja:"Opcache 有効",zh:"Opcache 已启用",zhcn:"Opcache 已启用",zhhk:"Opcache 已啟用",zhtw:"Opcache 已啟用"},"Opcache JIT enabled":{ja:"Opcache JIT 有効",zh:"Opcache JIT 已启用",zhcn:"Opcache JIT 已启用",zhhk:"Opcache JIT 已啟用",zhtw:"Opcache JIT 已啟用"},OS:{ja:"OS",zh:"操作系统",zhcn:"操作系统",zhhk:"作業系統",zhtw:"作業系統"},"PHP Browser languages":{ja:"PHP ブラウザ言語",zh:"PHP 浏览器语言",zhcn:"PHP 浏览器语言",zhhk:"PHP 瀏覽器語言",zhtw:"PHP瀏覽器語言"},"PHP Ext":{ja:"PHP拡張",zh:"PHP扩展",zhcn:"PHP扩展",zhhk:"PHP擴充",zhtw:"PHP擴充"},"PHP Extensions":{ja:"PHP拡張機能",zh:"PHP 扩展",zhcn:"PHP 扩展",zhhk:"PHP 擴充功能",zhtw:"PHP 擴充功能"},"PHP Info":{ja:"PHP情報",zh:"PHP信息",zhcn:"PHP信息",zhhk:"PHP資訊",zhtw:"PHP資訊"},"PHP Information":{ja:"PHP情報",zh:"PHP 信息",zhcn:"PHP 信息",zhhk:"PHP 資訊",zhtw:"PHP 資訊"},Ping:{ja:"ネットワーク診断",zh:"网络检测",zhcn:"网络检测",zhhk:"網絡檢測",zhtw:"網路檢測"},"Please wait {{seconds}}s":{ja:"{{seconds}}秒お待ちください",zh:"请等待 {{seconds}} 秒",zhcn:"请等待 {{seconds}} 秒",zhhk:"請等候 {{seconds}} 秒",zhtw:"請等候 {{seconds}} 秒"},"Public IPv4":{ja:"パブリック IPv4",zh:"公网 IPv4",zhcn:"公网 IPv4",zhhk:"公眾 IPv4",zhtw:"公開 IPv4"},"Public IPv6":{ja:"パブリック IPv6",zh:"公网 IPv6",zhcn:"公网 IPv6",zhhk:"公眾 IPv6",zhtw:"公開 IPv6"},Ram:{ja:"RAM",zh:"内存",zhcn:"内存",zhhk:"記憶體",zhtw:"記憶體"},Read:{ja:"読取",zh:"读取",zhcn:"读取",zhhk:"讀取",zhtw:"讀取"},"Recived: {{total}}":{ja:"受信: {{total}}",zh:"接收: {{total}}",zhcn:"接收: {{total}}",zhhk:"接收: {{total}}",zhtw:"接收: {{total}}"},Results:{ja:"診断結果",zh:"结果",zhcn:"结果",zhhk:"結果",zhtw:"結果"},"SAPI interface":{ja:"SAPIインターフェース",zh:"SAPI 接口",zhcn:"SAPI 接口",zhhk:"SAPI 介面",zhtw:"SAPI 介面"},"Script path":{ja:"スクリプトパス",zh:"脚本路径",zhcn:"脚本路径",zhhk:"腳本路徑",zhtw:"腳本路徑"},"Sent: {{total}}":{ja:"送信: {{total}}",zh:"发送: {{total}}",zhcn:"发送: {{total}}",zhhk:"傳送: {{total}}",zhtw:"傳送: {{total}}"},"Server ⇄ Browser":{ja:"サーバー ⇄ ブラウザー",zh:"服务器 ⇄ 浏览器",zhcn:"服务器 ⇄ 浏览器",zhhk:"伺服器 ⇄ 瀏覽器",zhtw:"伺服器 ⇄ 瀏覽器"},"Server Benchmark":{ja:"サーバーベンチマーク",zh:"服务器性能测试",zhcn:"服务器性能测试",zhhk:"伺服器跑分測試",zhtw:"伺服器跑分測試"},"Server Info":{ja:"サーバー情報",zh:"服务器信息",zhcn:"服务器信息",zhhk:"伺服器資訊",zhtw:"伺服器資訊"},"Server Status":{ja:"サーバー状態",zh:"服务器状态",zhcn:"服务器状态",zhhk:"伺服器狀態",zhtw:"伺服器狀態"},"SMTP support":{ja:"SMTPサポート",zh:"SMTP 支持",zhcn:"SMTP 支持",zhhk:"SMTP 支援",zhtw:"SMTP 支援"},"Start ping":{ja:"Ping 開始",zh:"开始 Ping",zhcn:"开始 Ping",zhhk:"開始 Ping",zhtw:"開始 Ping"},"Stop ping":{ja:"Ping 停止",zh:"停止 Ping",zhcn:"停止 Ping",zhhk:"停止 Ping",zhtw:"停止 Ping"},Swap:{ja:"スワップ",zh:"交换空间",zhcn:"交换空间",zhhk:"交換區",zhtw:"交換區"},"Swap cached":{ja:"スワップキャッシュ",zh:"交换区缓存",zhcn:"交换区缓存",zhhk:"交換區快取",zhtw:"交換區快取"},"Swap usage":{ja:"スワップ使用量",zh:"交换区使用",zhcn:"交换区使用",zhhk:"交換區用量",zhtw:"交換區用量"},"System load":{ja:"システム負荷",zh:"系统负载",zhcn:"系统负载",zhhk:"系統負載",zhtw:"系統負載"},Temperature:{ja:"温度",zh:"温度",zhcn:"温度",zhhk:"溫度",zhtw:"溫度"},"Temperature sensor":{ja:"温度センサー",zh:"温度传感器",zhcn:"温度传感器",zhhk:"溫度感測器",zhtw:"溫度感測器"},"Testing, please wait...":{ja:"テスト実行中...",zh:"测试中，请稍候...",zhcn:"测试中，请稍候...",zhhk:"測試中，請稍候...",zhtw:"測試中，請稍候..."},Time:{ja:"時間",zh:"时间",zhcn:"时间",zhhk:"時間",zhtw:"時間"},"Timeout for socket":{ja:"ソケットタイムアウト",zh:"Socket 超时时间",zhcn:"Socket 超时时间",zhhk:"Socket 逾時時間",zhtw:"Socket 逾時時間"},"Touch to copy marks":{ja:"タップでスコアをコピー",zh:"点击复制分数",zhcn:"点击复制分数",zhhk:"點擊複製跑分數據",zhtw:"點擊複製跑分數據"},"Treatment URLs file":{ja:"リモートファイル処理",zh:"远程文件处理",zhcn:"远程文件处理",zhhk:"檔案遠端開啟功能",zhtw:"檔案遠端開啟功能"},Unavailable:{ja:"取得不可",zh:"不可用",zhcn:"不可用",zhhk:"無法取得",zhtw:"無法取得"},"Update is disabled in dev mode.":{ja:"開発モードでは更新不可。",zh:"开发模式下禁用更新。",zhcn:"开发模式下禁用更新。",zhhk:"開發模式下停用更新功能。",zhtw:"開發模式下停用更新功能。"},"Update success, refreshing...":{ja:"更新成功 再読込中...",zh:"更新成功，刷新中...",zhcn:"更新成功，刷新中...",zhhk:"更新成功，重新整理中...",zhtw:"更新成功，重新整理中..."},Uptime:{ja:"稼働時間",zh:"运行时间",zhcn:"运行时间",zhhk:"運行時間",zhtw:"運行時間"},Version:{ja:"バージョン",zh:"版本",zhcn:"版本",zhhk:"版本",zhtw:"版本"},"Visit PHP.net Official website":{ja:"PHP.net公式サイトへ",zh:"访问 PHP.net 官网",zhcn:"访问 PHP.net 官网",zhhk:"瀏覽 PHP.net 官方網站",zhtw:"瀏覽 PHP.net 官方網站"},"Visit probe page":{ja:"プローブページへ",zh:"访问检测页面",zhcn:"访问检测页面",zhhk:"瀏覽檢測頁面",zhtw:"瀏覽檢測頁面"},"Visit the official website":{ja:"公式サイトへ",zh:"访问官网",zhcn:"访问官网",zhhk:"瀏覽官方網站",zhtw:"瀏覽官方網站"},"Web server":{ja:"ウェブサーバー",zh:"Web 服务器",zhcn:"Web 服务器",zhhk:"Web伺服器",zhtw:"Web伺服器"},Write:{ja:"書込",zh:"写入",zhcn:"写入",zhhk:"寫入",zhtw:"寫入"}},mw=navigator.language.replace("-","").replace("_","").toLowerCase(),k=(i,r="")=>{const u=`${r?`${r}|`:""}${i}`;return pw?.[u]?.[mw]??i};mt({enforceActions:"observed"});let vw=class{pollData=null;constructor(){bt(this)}setPollData=r=>{this.pollData=r}};const Ld=new vw;function fn(i,r){let u=i;for(const[s,f]of Object.entries(r)){const h=new RegExp(`\\{\\{${s}\\}\\}`,"g");u=u.replace(h,String(f))}return u}const yw={main:"_main_17cch_12"},gw=be(()=>{const{pollData:i}=Ld;if(!i?.config)return null;const{APP_NAME:r,APP_URL:u,AUTHOR_NAME:s,AUTHOR_URL:f}=i.config;return _.jsx("div",{className:yw.main,dangerouslySetInnerHTML:{__html:fn(k("Generate by {{appName}} and developed by {{authorName}}"),{appName:`<a href="${u}" target="_blank">${r}</a>`,authorName:`<a href="${f}" target="_blank">${s}</a>`})}})}),bw={main:"_main_1jpdc_16"},ba=200,_w=201,Sw=403,Tw=429,Ow=500,Ew=507;mt({enforceActions:"observed"});let ww=class{isUpdating=!1;isUpdateError=!1;targetVersion="";constructor(){bt(this)}setTargetVersion=r=>{this.targetVersion=r};setIsUpdating=r=>{this.isUpdating=r};setIsUpdateError=r=>{this.isUpdateError=r};get notiText(){return this.isUpdating?k("⏳ Updating, please wait a second..."):this.isUpdateError?k("❌ Update error, click here to try again?"):this.targetVersion?fn(k("✨ Found new version: {{oldVersion}} ⇢ {{newVersion}}"),{oldVersion:zd.pollData?.APP_VERSION??"-",newVersion:this.targetVersion}):""}};const Bd=new ww,rg={main:"_main_p5526_16"},Aw=i=>_.jsx("a",{className:rg.main,...i}),xw=i=>_.jsx("button",{className:rg.main,...i});mt({enforceActions:"observed"});let Rw=class{isOpen=!1;msg="";constructor(){bt(this)}setMsg=r=>{this.msg=r};close=(r=0)=>{setTimeout(()=>{gE(()=>{this.isOpen=!1})},r*1e3)};open=r=>{this.msg=r,this.isOpen=!0}};const bo=new Rw,zw=be(()=>{const{isUpdating:i,setIsUpdating:r,setIsUpdateError:u,notiText:s}=Bd,{open:f}=bo,h=K.useCallback(async p=>{if(p.preventDefault(),p.stopPropagation(),i)return;r(!0);const{status:b}=await oa("update");switch(b){case _w:f(k("Update success, refreshing...")),window.location.reload();return;case Sw:f(k("Update is disabled in dev mode.")),r(!1),u(!0);return;case Ew:case Ow:f(k("Can not update file, please check the server permissions and space.")),r(!1),u(!0);return}f(k("Network error, please try again later.")),r(!1),u(!0)},[i,r,u,f]);return _.jsx(xw,{onClick:h,title:k("Click to update"),children:s})}),sg=(i,r)=>{if(typeof i+typeof r!="stringstring")return 0;const u=i.split("."),s=r.split("."),f=Math.max(u.length,s.length);for(let h=0;h<f;h+=1){if(u[h]&&!s[h]&&Number(u[h])>0||Number(u[h])>Number(s[h]))return 1;if(s[h]&&!u[h]&&Number(s[h])>0||Number(u[h])<Number(s[h]))return-1}return 0},Vd={main:"_main_1k8xz_1",name:"_name_1k8xz_6",version:"_version_1k8xz_10"},Dw=be(()=>{const{pollData:i}=zd,{setTargetVersion:r,targetVersion:u}=Bd;if(K.useEffect(()=>{if(!i)return;(async()=>{const{data:b,status:g}=await oa("latestVersion");!b?.version||g!==ba||r(b.version)})()},[i,r]),!i)return null;const{APP_NAME:s,APP_URL:f,APP_VERSION:h}=i;return _.jsx("h1",{className:Vd.main,children:u&&sg(h,u)<0?_.jsx(zw,{}):_.jsxs(Aw,{href:f,rel:"noreferrer",target:"_blank",children:[_.jsx("span",{className:Vd.name,children:s}),_.jsx("span",{className:Vd.version,children:h})]})})}),Cw=()=>_.jsx("div",{className:bw.main,children:_.jsx(Dw,{})});mt({enforceActions:"observed"});let Mw=class{pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)}};const Pd=new Mw;mt({enforceActions:"observed"});let jw=class{pollData=null;constructor(){bt(this)}setPollData(r){Mn(r,this.pollData)||(this.pollData=r)}get networks(){return this.pollData?.networks??[]}get timestamp(){return this.pollData?.timestamp??0}get sortNetworks(){return this.networks.filter(({tx:r})=>!!r).toSorted((r,u)=>r.tx-u.tx)}get networksCount(){return this.sortNetworks.length}};const $d=new jw;mt({enforceActions:"observed"});let Uw=class{DEFAULT_ITEM={id:"",url:"",fetchUrl:"",loading:!0,status:204,data:null};items=[];pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)};setItems=r=>{this.items=r};setItem=({id:r,...u})=>{const s=this.items.findIndex(f=>f.id===r);s!==-1&&(this.items[s]={...this.items[s],...u})}};const qd=new Uw;mt({enforceActions:"observed"});let Nw=class{pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)}};const Gd=new Nw;mt({enforceActions:"observed"});let kw=class{pollData=null;latestPhpVersion="";constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)};setLatestPhpVersion=r=>{this.latestPhpVersion=r}};const Uu=new kw;mt({enforceActions:"observed"});let Hw=class{pollData=null;publicIpv4="";publicIpv6="";constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)};setPublicIpv4=r=>{this.publicIpv4=r};setPublicIpv6=r=>{this.publicIpv6=r}};const Yd=new Hw;mt({enforceActions:"observed"});let Lw=class{pollData=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)};get sysLoad(){return this.pollData?.sysLoad||[0,0,0]}get cpuUsage(){return this.pollData?.cpuUsage??{usage:0,idle:100,sys:0,user:0}}get memRealUsage(){return this.pollData?.memRealUsage??{max:0,value:0}}get memCached(){return this.pollData?.memCached??{max:0,value:0}}get memBuffers(){return this.pollData?.memBuffers??{max:0,value:0}}get swapUsage(){return this.pollData?.swapUsage??{max:0,value:0}}get swapCached(){return this.pollData?.swapCached??{max:0,value:0}}};const _o=new Lw,Bw=i=>{const r=K.useRef(document.createElement("div"));return K.useEffect(()=>(document.body.appendChild(r.current),()=>{r.current.remove()}),[i]),r.current},Vw=({children:i})=>{const r=Bw();return Fy.createPortal(i,r)},Pw={main:"_main_17sik_12"},$w=be(()=>{const{isOpen:i,msg:r,close:u}=bo,s=f=>{f.preventDefault(),f.stopPropagation(),u()};return i?_.jsx(Vw,{children:_.jsx("button",{className:Pw.main,onClick:s,title:k("Click to close"),type:"button",children:r})}):null});mt({enforceActions:"observed"});let qw=class{data=null;constructor(){bt(this)}setPollData=r=>{Mn(r,this.data)||(this.data=r)}};const Gw=new qw,Nu={id:"browserBenchmark"},ku={id:"ping"},Hu={id:"database"},Lu={id:"diskUsage"},Bu={id:"myInfo"},Vu={id:"networkStats"},Pu={id:"nodes"},$u={id:"phpExtensions"},qu={id:"phpInfo"},Gu={id:"serverBenchmark"},Yu={id:"serverInfo"},Xu={id:"serverStatus"},Iu={id:"temperatureSensor"},Xd=[Pu.id,Iu.id,Xu.id,Vu.id,Lu.id,Yu.id,ku.id,qu.id,$u.id,Hu.id,Gu.id,Nu.id,Bu.id],Yw={container:"_container_30sck_1"};const Xw=i=>i.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase(),Iw=i=>i.replace(/^([A-Z])|[\s-_]+(\w)/g,(r,u,s)=>s?s.toUpperCase():u.toLowerCase()),ug=i=>{const r=Iw(i);return r.charAt(0).toUpperCase()+r.slice(1)},cg=(...i)=>i.filter((r,u,s)=>!!r&&r.trim()!==""&&s.indexOf(r)===u).join(" ").trim(),Qw=i=>{for(const r in i)if(r.startsWith("aria-")||r==="role"||r==="title")return!0};var Zw={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:2,strokeLinecap:"round",strokeLinejoin:"round"};const Kw=K.forwardRef(({color:i="currentColor",size:r=24,strokeWidth:u=2,absoluteStrokeWidth:s,className:f="",children:h,iconNode:p,...b},g)=>K.createElement("svg",{ref:g,...Zw,width:r,height:r,stroke:i,strokeWidth:s?Number(u)*24/Number(r):u,className:cg("lucide",f),...!h&&!Qw(b)&&{"aria-hidden":"true"},...b},[...p.map(([N,U])=>K.createElement(N,U)),...Array.isArray(h)?h:[h]]));const So=(i,r)=>{const u=K.forwardRef(({className:s,...f},h)=>K.createElement(Kw,{ref:h,iconNode:r,className:cg(`lucide-${Xw(ug(i))}`,`lucide-${i}`,s),...f}));return u.displayName=ug(i),u};const Jw=So("chevron-down",[["path",{d:"m6 9 6 6 6-6",key:"qrunsl"}]]);const Ww=So("chevron-up",[["path",{d:"m18 15-6-6-6 6",key:"153udz"}]]);const Fw=So("cloud-download",[["path",{d:"M12 13v8l-4-4",key:"1f5nwf"}],["path",{d:"m12 21 4-4",key:"1lfcce"}],["path",{d:"M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284",key:"ui1hmy"}]]);const eA=So("link",[["path",{d:"M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71",key:"1cjeqo"}],["path",{d:"M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71",key:"19qd67"}]]);const tA=So("loader-pinwheel",[["path",{d:"M22 12a1 1 0 0 1-10 0 1 1 0 0 0-10 0",key:"1lzz15"}],["path",{d:"M7 20.7a1 1 0 1 1 5-8.7 1 1 0 1 0 5-8.6",key:"1gnrpi"}],["path",{d:"M7 3.3a1 1 0 1 1 5 8.6 1 1 0 1 0 5 8.6",key:"u9yy5q"}],["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}]]);const nA=So("pointer",[["path",{d:"M22 14a8 8 0 0 1-8 8",key:"56vcr3"}],["path",{d:"M18 11v-1a2 2 0 0 0-2-2a2 2 0 0 0-2 2",key:"1agjmk"}],["path",{d:"M14 10V9a2 2 0 0 0-2-2a2 2 0 0 0-2 2v1",key:"wdbh2u"}],["path",{d:"M10 9.5V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v10",key:"1ibuk9"}],["path",{d:"M18 11a2 2 0 1 1 4 0v3a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15",key:"g6ys72"}]]);const aA=So("triangle-alert",[["path",{d:"m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3",key:"wmoenq"}],["path",{d:"M12 9v4",key:"juzpu7"}],["path",{d:"M12 17h.01",key:"p32p05"}]]);const oA=So("x",[["path",{d:"M18 6 6 18",key:"1bl5f8"}],["path",{d:"m6 6 12 12",key:"d8bk6v"}]]),iA={arrow:"_arrow_1qtu9_14"},fg="module-priority",dg={getItems(){const i=localStorage.getItem(fg);if(!i)return{};try{return JSON.parse(i)}catch{return{}}},setItems(i){localStorage.setItem(fg,JSON.stringify(i))},getPriority(i){return this.getItems()[i]||0},setPriority({id:i,priority:r}){const u=this.getItems();u[i]=r,this.setItems(u)}};mt({enforceActions:"observed"});const hg=i=>{const r={};for(const u of i)r[u.id]=u.priority;dg.setItems(r)};let lA=class{sortedModules=[];constructor(){bt(this)}setSortedModules=r=>{this.sortedModules=r.toSorted((u,s)=>u.priority-s.priority)};get availableModules(){const{pollData:r}=Ld;return jg.items.filter(({id:s})=>!!r?.[s]).toSorted((s,f)=>{const h=this.sortedModules.find(b=>b.id===s.id),p=this.sortedModules.find(b=>b.id===f.id);return Number(h?.priority??Xd.indexOf(s.id))-Number(p?.priority??Xd.indexOf(f.id))})}moveUp=r=>{const u=this.sortedModules.findIndex(f=>f.id===r);if(u===0)return;const s=this.sortedModules[u].priority;this.sortedModules[u].priority=this.sortedModules[u-1].priority,this.sortedModules[u-1].priority=s,this.sortedModules.sort((f,h)=>f.priority-h.priority),hg(this.sortedModules)};moveDown=r=>{const u=this.sortedModules.findIndex(f=>f.id===r);if(u===this.sortedModules.length-1)return;const s=this.sortedModules[u].priority;this.sortedModules[u].priority=this.sortedModules[u+1].priority,this.sortedModules[u+1].priority=s,this.sortedModules.sort((f,h)=>f.priority-h.priority),hg(this.sortedModules)};get disabledMoveUpId(){const r=this.availableModules;return r.length<=1?"":r[0].id}get disabledMoveDownId(){const r=this.availableModules;return r.length<=1?"":r.at(-1)?.id??""}};const Id=new lA,pg=be(({isDown:i,id:r})=>{const{disabledMoveUpId:u,disabledMoveDownId:s,moveUp:f,moveDown:h}=Id,p=i?s===r:u===r,b=K.useCallback(g=>{if(g.preventDefault(),g.stopPropagation(),i){h(r);return}f(r)},[i,h,f,r]);return _.jsx("button",{className:iA.arrow,"data-disabled":p||void 0,disabled:p,onClick:b,title:k(i?"Move down":"Move up"),type:"button",children:i?_.jsx(Jw,{}):_.jsx(Ww,{})})}),Qu={main:"_main_60fl9_23",header:"_header_60fl9_29",title:"_title_60fl9_41",body:"_body_60fl9_45"},rA=({id:i,title:r})=>_.jsxs("h2",{className:Qu.header,children:[_.jsx(pg,{id:i,isDown:!1}),_.jsx("span",{className:Qu.title,children:r}),_.jsx(pg,{id:i,isDown:!0})]}),dn=({id:i,title:r,children:u,...s})=>_.jsxs("div",{className:Qu.main,id:i,...s,children:[_.jsx(rA,{id:i,title:r}),_.jsx("div",{className:Qu.body,children:u})]}),mg={main:"_main_1hf64_14",item:"_item_1hf64_22"},vg=({items:i})=>_.jsx("ul",{className:mg.main,children:i.map(({id:r,text:u})=>_.jsx("li",{className:mg.item,children:u},r))}),sA=K.memo(()=>_.jsx(dn,{id:Nu.id,title:k("Browser Benchmark"),children:_.jsx(vg,{items:[{id:"dev",text:"(Under development)"}]})})),yg={main:"_main_1ygx7_18",link:"_link_1ygx7_35"},hn=({id:i,title:r})=>_.jsx("a",{className:yg.link,href:`#${i}`,children:r},i),uA=()=>_.jsx(hn,{id:Nu.id,title:k("Browser bench")}),cA={id:Nu.id,content:sA,nav:uA},Qd={main:"_main_11zmy_14",label:"_label_11zmy_24",content:"_content_11zmy_34"},pn=({label:i="",title:r="",minWidth:u=4,maxWidth:s=8,children:f})=>{const h={"--min-width":`${u}rem`,"--max-width":`${s}rem`};return _.jsxs("div",{className:Qd.main,style:h,children:[!!i&&_.jsx("div",{className:Qd.label,title:r,children:i}),_.jsx("div",{className:Qd.content,children:f})]})},fA={main:"_main_z8p86_1"},Zu=({minWidth:i=16,...r})=>{const u={"--min-width":`${i}rem`};return _.jsx("div",{className:fA.main,style:u,...r})},dA={main:"_main_xo4z4_2"},Mr=({isEnable:i,text:r=""})=>_.jsx("div",{className:dA.main,"data-error":!i||void 0,"data-icon":!r||void 0,"data-ok":i||void 0,children:r}),hA=K.memo(be(()=>{const{pollData:i}=Jy,r=[["SQLite3",i?.sqlite3??!1],["MySQLi client",i?.mysqliClientVersion??!1],["Mongo",i?.mongo??!1],["MongoDB",i?.mongoDb??!1],["PostgreSQL",i?.postgreSql??!1],["Paradox",i?.paradox??!1],["MS SQL",i?.msSql??!1],["PDO",i?.pdo??!1]];return _.jsx(dn,{id:Hu.id,title:k("Database"),children:_.jsx(Zu,{minWidth:14,children:r.map(([u,s])=>_.jsx(pn,{label:u,maxWidth:7,minWidth:4,children:_.jsx(Mr,{isEnable:!!s,text:s})},u))})})})),pA=()=>_.jsx(hn,{id:Hu.id,title:k("DB")}),mA={id:Hu.id,content:hA,nav:pA},jn=(i,r=2)=>{if(i===0)return"0";const u=1024,s=["B","K","M","G","T","P","E","Z","Y"];let f=Math.floor(Math.log(i)/Math.log(u));f=f<0?0:f;const h=Number.parseFloat((i/u**f).toFixed(r));return h?`${h.toFixed(2)} ${s[f]}`:"0"},cl={main:"_main_1isor_18",percent:"_percent_1isor_25",name:"_name_1isor_30",nameText:"_nameText_1isor_41",overview:"_overview_1isor_49",core:"_core_1isor_53"},Ku=K.memo(({value:i,max:r=100,low:u=60,optimum:s,high:f=80})=>_.jsx("meter",{className:cl.core,high:f,low:u,max:r,optimum:s,value:i})),vA=({title:i,name:r="",value:u,max:s,isCapacity:f,percentTag:h="%",percent:p,percentRender:b,progressPercent:g})=>{const N=K.useCallback(O=>{O.preventDefault(),O.stopPropagation();const L=i||r;bo.open(L),!(i?.length??!1)&&navigator.clipboard.writeText(r)},[r,i]),U=s===0||u===0?0:u/s*100,x=f?`${jn(u)} / ${jn(s)}`:`${u.toFixed(1)}${h} / ${s}${h}`;return _.jsxs("div",{className:cl.main,title:i,children:[_.jsx("div",{className:cl.percent,children:b??`${(p??U).toFixed(1)}%`}),_.jsx("button",{className:cl.name,onClick:N,title:r,type:"button",children:_.jsx("div",{className:cl.nameText,children:r})}),_.jsx("div",{className:cl.overview,children:x}),_.jsx(Ku,{value:g??U})]})},hi=K.memo(vA),yA={main:"_main_b4lx8_1"},gA=be(()=>{const{pollData:i}=Dd,r=i?.items??[];return r.length?_.jsx(dn,{id:Lu.id,title:k("Disk Usage"),children:_.jsx("div",{className:yA.main,children:r.map(({id:u,free:s,total:f})=>_.jsx(hi,{isCapacity:!0,max:f,name:u,value:f-s},u))})}):null}),bA=be(()=>{const{pollData:i}=Dd;return(i?.items??[]).length?_.jsx(hn,{id:Lu.id,title:k("Disk")}):null}),_A={id:Lu.id,content:gA,nav:bA},Zd={button:"_button_1shxn_25",icon:"_icon_1shxn_49"},Un={Error:"error",Loading:"loading",Warning:"warning",Pointer:"pointer"},gg=({status:i})=>_.jsx("span",{className:Zd.icon,"data-status":i,children:{[Un.Error]:_.jsx(oA,{}),[Un.Loading]:_.jsx(tA,{}),[Un.Warning]:_.jsx(aA,{}),[Un.Pointer]:_.jsx(nA,{})}[i]??null}),Kd=({status:i=Un.Pointer,children:r,...u})=>_.jsxs("button",{className:Zd.button,type:"button",...u,children:[_.jsx(gg,{status:i}),r]}),bg=({status:i=Un.Pointer,children:r,...u})=>_.jsxs("a",{className:Zd.button,"data-link":!0,...u,children:[_.jsx(gg,{status:i}),r]}),_g=be(({ip:i})=>{const[r,u]=K.useState(!1),[s,f]=K.useState(null),h=K.useCallback(async p=>{if(p.preventDefault(),p.stopPropagation(),r)return;u(!0);const{data:b,status:g}=await oa(`locationIpv4&ip=${i}`);if(u(!1),b&&g===ba){f(b);return}bo.open(k("Can not fetch location."))},[i,r]);return _.jsx(Kd,{onClick:h,status:r?Un.Loading:Un.Pointer,children:s?Object.values(s).filter(Boolean).join(", "):k("Click to fetch")})}),Sg=i=>{const[r,u]=K.useState({ip:"",msg:k("Loading..."),isLoading:!0});return K.useEffect(()=>{(async()=>{const f=await fetch(`https://ipv${i}.inn-studio.com/ip/?json`);await f.json().catch(()=>{u({ip:"",msg:k("Not support"),isLoading:!1})}).then(h=>{if(h?.ip&&f.status===ba){u({ip:h.ip,msg:"",isLoading:!1});return}u({ip:"",msg:k("Can not fetch IP"),isLoading:!1})})})()},[i]),r},SA={main:"_main_mc2kq_1"},fl=i=>_.jsx("div",{className:SA.main,...i}),TA=be(()=>{const{pollData:i}=Pd,{ip:r,msg:u,isLoading:s}=Sg(4),{ip:f,msg:h,isLoading:p}=Sg(6);let b="",g="";s?b=u:r?b=r:i?.ipv4?b=i.ipv4:b=u,p?g=h:f?g=f:i?.ipv6?g=i.ipv6:g=h;const N=[[k("IPv4"),b],[k("IPv6"),g],[k("Location (IPv4)"),_.jsx(_g,{ip:b},"myLocalIpv4")],[k("Browser UA"),navigator.userAgent],[k("JS Browser languages"),navigator.languages.join(",")],[k("PHP Browser languages"),i?.phpLanguage]];return i?_.jsx(dn,{id:Bu.id,title:k("My Info"),children:_.jsx(fl,{children:N.map(([U,x])=>_.jsx(pn,{label:U,children:x},U))})}):null}),OA=be(()=>{const{pollData:i}=Pd;return i?_.jsx(hn,{id:Bu.id,title:k("Mine")}):null}),EA={id:Bu.id,content:TA,nav:OA};function Tg(i){const r=K.useRef(null);return K.useEffect(()=>{r.current=i},[i]),r.current}const wA={container:"_container_1i47d_2"},To={main:"_main_1cyw0_17",id:"_id_1cyw0_24",type:"_type_1cyw0_29",rx:"_rx_1cyw0_35",tx:"_tx_1cyw0_36",rateRx:"_rateRx_1cyw0_57",rateTx:"_rateTx_1cyw0_58"},Og=({id:i,totalRx:r=0,rateRx:u=0,totalTx:s=0,rateTx:f=0})=>i?_.jsxs("div",{className:To.main,children:[_.jsx("div",{className:To.id,children:i}),_.jsxs("div",{className:To.rx,children:[_.jsx("div",{className:To.type,children:fn(k("Recived: {{total}}"),{total:jn(r)})}),_.jsxs("div",{className:To.rateRx,children:[jn(u),"/s"]})]}),_.jsxs("div",{className:To.tx,children:[_.jsx("div",{className:To.type,children:fn(k("Sent: {{total}}"),{total:jn(s)})}),_.jsxs("div",{className:To.rateTx,children:[jn(f),"/s"]})]})]}):null,AA=be(()=>{const{sortNetworks:i,networksCount:r,timestamp:u}=$d,s=Tg({items:i,timestamp:u});if(!r)return null;const f=u-(s?.timestamp||u);return _.jsx(dn,{id:Vu.id,title:k("Network Stats"),children:_.jsx("div",{className:wA.container,children:i.map(({id:h,rx:p,tx:b})=>{if(!(p||b))return null;const g=(s?.items||i).find(x=>x.id===h),N=g?.rx||0,U=g?.tx||0;return _.jsx(Og,{id:h,rateRx:(p-N)/f,rateTx:(b-U)/f,totalRx:p,totalTx:b},h)})})})}),xA=be(()=>{const{networksCount:i}=$d;return i?_.jsx(hn,{id:Vu.id,title:k("Network")}):null}),RA={id:Vu.id,content:AA,nav:xA},zA={main:"_main_zmhfm_1"},DA={main:"_main_vvbro_25"},Eg=({height:i=5})=>_.jsx("div",{className:DA.main,style:{height:`${i}rem`}}),CA={main:"_main_1ogv8_16"},wg=({children:i})=>_.jsx("div",{className:CA.main,role:"alert",children:i}),pi={main:"_main_1xqpo_13",label:"_label_1xqpo_20",meter:"_meter_1xqpo_27",usage:"_usage_1xqpo_33",group:"_group_1xqpo_38",groupItem:"_groupItem_1xqpo_45"},MA=({load:i,title:r})=>_.jsx("div",{className:pi.groupItem,title:r,children:i.toFixed(2)}),jA=({sysLoad:i})=>{const r=[1,5,15],u=i.map((s,f)=>({id:`${r[f]}minAvg`,load:s,text:fn(k("{{minute}} minute average"),{minute:r[f]})}));return _.jsx("div",{className:pi.group,children:u.map(({id:s,load:f,text:h})=>_.jsx("div",{className:pi.groupItem,title:h,children:f.toFixed(2)},s))})},UA=be(()=>{const{sysLoad:i,cpuUsage:r}=_o,u=r.user+r.idle+r.sys,s=`
user: ${(r.user/u*100).toFixed(2)}%
idle: ${(r.idle/u*100).toFixed(2)}%
sys: ${(r.sys/u*100).toFixed(2)}%
`;return _.jsxs("div",{className:pi.main,children:[_.jsx("div",{className:pi.label,children:k("System load")}),_.jsx(jA,{sysLoad:i}),_.jsx("div",{className:pi.usage,title:s,children:fn(k("{{usage}}% CPU usage"),{usage:r.usage})}),_.jsx("div",{className:pi.meter,children:_.jsx(Ku,{value:r.usage>100?100:r.usage})})]})}),NA={sysLoad:"_sysLoad_mqy5s_1"},jr={main:"_main_66xvd_1",meter:"_meter_66xvd_10",label:"_label_66xvd_16",overview:"_overview_66xvd_20",percent:"_percent_66xvd_31"},Ju=({children:i,percent:r})=>_.jsxs("div",{className:jr.main,children:[i,_.jsxs("div",{className:jr.percent,children:[r,"%"]}),_.jsx("div",{className:jr.meter,children:_.jsx(Ku,{max:100,value:r})})]}),Wu=i=>_.jsx("div",{className:jr.label,...i}),Fu=i=>_.jsx("div",{className:jr.overview,...i}),kA=({items:i})=>_.jsx("div",{className:NA.sysLoad,children:i.map(r=>_.jsx(MA,{load:r},Math.random()))}),HA=K.memo(({sysLoad:i,cpuUsage:r})=>{const{user:u,idle:s,sys:f,usage:h}=r,p=u+s+f,b=`
user: ${(u/p*100).toFixed(2)}%
idle: ${(s/p*100).toFixed(2)}%
sys: ${(f/p*100).toFixed(2)}%
`;return _.jsxs(Ju,{percent:h,children:[_.jsx(Wu,{children:k("CPU")}),_.jsx(Fu,{title:b,children:_.jsx(kA,{items:i})})]})}),Ag={main:"_main_1gdd5_1",item:"_item_1gdd5_12"},LA=K.memo(({id:i,free:r,total:u})=>_.jsx("div",{className:Ag.item,children:_.jsxs(Ju,{percent:u?Math.round(r/u*100):0,children:[_.jsx(Wu,{children:`🖴 ${i}`}),_.jsx(Fu,{children:`${jn(r)} / ${jn(u)}`})]})},i)),BA=({data:i})=>{const r=i?.items??[];return _.jsx("div",{className:Ag.main,children:r.map(({id:u,free:s,total:f})=>_.jsx(LA,{free:s,id:u,total:f},u))})},VA={main:"_main_mc2kq_1"},PA=({data:i})=>{const{networks:r,timestamp:u}=i,s=Tg({items:r,timestamp:u}),f=u-(s?.timestamp||u);return _.jsx("div",{className:VA.main,children:r.map(({id:h,rx:p,tx:b})=>{if(!(p||b))return null;const g=(s?.items||r).find(x=>x.id===h),N=g?.rx||0,U=g?.tx||0;return _.jsx(Og,{id:h,rateRx:(p-N)/f,rateTx:(b-U)/f,totalRx:p,totalTx:b},h)})})},xg={main:"_main_18siw_1",name:"_name_18siw_6"},$A=K.memo(({data:i})=>{const{value:r,max:u}=i,s=u?Math.round(r/u*100):0;return _.jsxs(Ju,{percent:s,children:[_.jsx(Wu,{children:`🐏 ${k("Ram")}`}),_.jsx(Fu,{children:`${jn(r)} / ${jn(u)}`})]})}),qA=K.memo(({data:i})=>{const{value:r,max:u}=i,s=u?Math.round(r/u*100):0;return _.jsxs(Ju,{percent:s,children:[_.jsx(Wu,{children:`🐏 ${k("Swap")}`}),_.jsx(Fu,{children:`${jn(r)} / ${jn(u)}`})]})}),GA=K.memo(({id:i})=>{const[r,u]=K.useState(!0),[s,f]=K.useState(0),[h,p]=K.useState(null);K.useEffect(()=>{let I,te=!0;const ne=async()=>{try{const{data:ie,status:Qe}=await oa(`nodes&nodeId=${i}`);if(r&&u(!1),!ie||Qe!==ba){f(Qe);return}p(ie)}finally{te&&(I=setTimeout(ne,2e3))}};return ne(),()=>{te=!1,clearTimeout(I)}},[i,r]);const b=h?.serverStatus??null,g=h?.diskUsage??null,N=h?.networkStats??null,U=h?.serverStatus?.memRealUsage??null,x=h?.serverStatus?.swapUsage??null,O=b?.sysLoad??[],L=b?.cpuUsage??null;return _.jsxs("div",{className:xg.main,children:[_.jsx("header",{className:xg.name,children:i}),s!==0&&_.jsx(wg,{children:fn(k("Error: {{error}}"),{error:s})}),r&&_.jsx(Eg,{height:10}),!r&&b&&_.jsxs(_.Fragment,{children:[L?_.jsx(HA,{cpuUsage:L,sysLoad:O}):null,U?_.jsx($A,{data:U}):null,x?_.jsx(qA,{data:x}):null,g?_.jsx(BA,{data:g}):null,N?_.jsx(PA,{data:N}):null]})]})}),YA=be(()=>{const{pollData:i}=qd,r=i?.nodesIds??[];return r.length?_.jsx(dn,{id:Pu.id,title:k("Nodes"),children:_.jsx("div",{className:zA.main,children:r.map(u=>_.jsx(GA,{id:u},u))})}):null}),XA=be(()=>{const{pollData:i}=qd;return(i?.nodesIds??[]).length?_.jsx(hn,{id:Pu.id,title:k("Nodes")}):null}),IA={id:Pu.id,content:YA,nav:XA},QA={main:"_main_uj7jp_16"},Jd=({keyword:i})=>_.jsx("a",{className:QA.main,href:`https://www.google.com/search?q=php+${encodeURIComponent(i)}`,rel:"nofollow noreferrer",target:"_blank",children:i}),ZA=K.memo(be(()=>{const{pollData:i}=Gd;if(!i)return null;const r=[["Redis",!!i.redis],["SQLite3",!!i.sqlite3],["Memcache",!!i.memcache],["Memcached",!!i.memcached],["Opcache",!!i.opcache],[k("Opcache enabled"),!!i.opcacheEnabled],[k("Opcache JIT enabled"),!!i.opcacheJitEnabled],["Swoole",!!i.swoole],["Image Magick",!!i.imagick],["Graphics Magick",!!i.gmagick],["Exif",!!i.exif],["Fileinfo",!!i.fileinfo],["SimpleXML",!!i.simplexml],["Sockets",!!i.sockets],["MySQLi",!!i.mysqli],["Zip",!!i.zip],["Multibyte String",!!i.mbstring],["Phalcon",!!i.phalcon],["Xdebug",!!i.xdebug],["Zend Optimizer",!!i.zendOptimizer],["ionCube",!!i.ionCube],["Source Guardian",!!i.sourceGuardian],["LDAP",!!i.ldap],["cURL",!!i.curl]];r.slice().sort((s,f)=>{const h=s[0].toLowerCase(),p=f[0].toLowerCase();return h<p?-1:h>p?1:0});const u=i.loadedExtensions||[];return u.slice().sort((s,f)=>{const h=s.toLowerCase(),p=f.toLowerCase();return h<p?-1:h>p?1:0}),_.jsxs(dn,{id:$u.id,title:k("PHP Extensions"),children:[_.jsx(Zu,{minWidth:14,children:r.map(([s,f])=>_.jsx(pn,{label:s,maxWidth:10,minWidth:4,children:_.jsx(Mr,{isEnable:f})},s))}),_.jsx(fl,{children:!!u.length&&_.jsx(pn,{label:k("Loaded extensions"),maxWidth:6,minWidth:4,children:u.map(s=>_.jsx(Jd,{keyword:s},s))})})]})})),KA=be(()=>{const{pollData:i}=Gd;return i?_.jsx(hn,{id:$u.id,title:k("PHP Ext")}):null}),JA={id:$u.id,content:ZA,nav:KA},WA=be(()=>{const{pollData:i,latestPhpVersion:r,setLatestPhpVersion:u}=Uu;K.useEffect(()=>{(async()=>{const{data:p,status:b}=await oa("latestPhpVersion");p?.version&&b===ba&&u(p.version)})()},[u]);const s=i?.phpVersion??"",f=sg(s,r);return _.jsx(bg,{href:"https://www.php.net/",title:k("Visit PHP.net Official website"),children:f===-1?` ${fn(k("{{oldVersion}} (Latest: {{latestPhpVersion}})"),{oldVersion:s,latestPhpVersion:r})}`:s})}),FA=K.memo(be(()=>{const{pollData:i}=Uu;if(!i)return null;const r=[["PHP info",_.jsx(bg,{href:Wy("phpInfoDetail"),target:"_blank",children:k("Detail")},"phpInfoDetail")],[k("Version"),_.jsx(WA,{},"phpVersion")]],u=[[k("SAPI interface"),i?.sapi],[k("Display errors"),_.jsx(Mr,{isEnable:i?.displayErrors},"displayErrors")],[k("Error reporting"),i.errorReporting],[k("Max memory limit"),i.memoryLimit],[k("Max POST size"),i.postMaxSize],[k("Max upload size"),i.uploadMaxFilesize],[k("Max input variables"),i.maxInputVars],[k("Max execution time"),i.maxExecutionTime],[k("Timeout for socket"),i.defaultSocketTimeout],[k("Treatment URLs file"),_.jsx(Mr,{isEnable:i.allowUrlFopen},"allowUrlFopen")],[k("SMTP support"),_.jsx(Mr,{isEnable:i.smtp},"smtp")]],{disableFunctions:s,disableClasses:f}=i;s.slice().sort(),f.slice().sort();const h=[[k("Disabled functions"),s.length?s.map(p=>_.jsx(Jd,{keyword:p},p)):"-"],[k("Disabled classes"),f.length?f.map(p=>_.jsx(Jd,{keyword:p},p)):"-"]];return _.jsxs(dn,{id:qu.id,title:k("PHP Information"),children:[_.jsxs(Zu,{children:[r.map(([p,b])=>_.jsx(pn,{label:p,children:b},p)),u.map(([p,b])=>_.jsx(pn,{label:p,children:b},p))]}),_.jsx(fl,{children:h.map(([p,b])=>_.jsx(pn,{label:p,maxWidth:7,minWidth:4,children:b},p))})]})})),ex=be(()=>{const{pollData:i}=Uu;return i?_.jsx(hn,{id:qu.id,title:k("PHP Info")}):null}),tx={id:qu.id,content:FA,nav:ex},nx=i=>{const u=i.reduce((h,p)=>h+p,0)/i.length,f=i.map(h=>{const p=h-u;return p*p}).reduce((h,p)=>h+p,0)/i.length;return Math.sqrt(f)};mt({enforceActions:"observed"});let ax=class{isPing=!1;isPingServerToBrowser=!1;isPingServerToServer=!1;serverToBrowserPingItems=[];serverToServerPingItems=[];constructor(){bt(this)}setIsPing=r=>{this.isPing=r};setIsPingServerToBrowser=r=>{this.isPingServerToBrowser=r};setIsPingServerToServer=r=>{this.isPingServerToServer=r};setServerToBrowserPingItems=r=>{this.serverToBrowserPingItems=r};setServerToServerPingItems=r=>{this.serverToServerPingItems=r};addServerToBrowserPingItem=r=>{this.serverToBrowserPingItems.push(r)};addServerToServerPingItem=r=>{this.serverToServerPingItems.push(r)}};const Wd=new ax,Fd={itemContainer:"_itemContainer_y6c35_12",resultContainer:"_resultContainer_y6c35_27",result:"_result_y6c35_27"},ox=be(()=>{const{serverToBrowserPingItems:i}=Wd,r=i.length,u=i.map(({time:b})=>b),s=r?(u.reduce((b,g)=>b+g,0)/r).toFixed(2):0,f=r?Math.max(...u):0,h=r?Math.min(...u):0,p=nx(u).toFixed(2);return _.jsx("div",{className:Fd.result,children:fn(k("{{times}} times, min/avg/max/mdev = {{min}}/{{avg}}/{{max}}/{{mdev}} ms"),{times:r,min:h,max:f,avg:s,mdev:p})})}),ix=be(({refContainer:i})=>{const{serverToBrowserPingItems:r}=Wd,u=r.length;return _.jsx(pn,{label:k("Results"),children:_.jsxs("div",{className:Fd.resultContainer,children:[!u&&"-",!!u&&_.jsx("ul",{className:Fd.itemContainer,ref:i,children:r.map(({id:s,time:f})=>_.jsx("li",{children:`${f} ms`},s))}),!!u&&_.jsx(ox,{})]})})}),lx=be(()=>{const{setIsPing:i,setIsPingServerToBrowser:r,addServerToBrowserPingItem:u,isPing:s,isPingServerToBrowser:f}=Wd,h=K.useRef(null),p=K.useRef(0),b=1e3,g=1e3,N=100,U=K.useCallback(async()=>{const L=Date.now(),{data:I,status:te}=await oa("ping");if(I?.time&&te===ba){const{id:ne,time:ie}=I,Qe=Date.now(),$e=ie*b;u({id:ne,time:Math.floor(Qe-L-$e)}),setTimeout(()=>{if(!h.current)return;const Ne=h.current.scrollTop,Ze=h.current.scrollHeight;Ne<Ze&&(h.current.scrollTop=Ze)},N)}},[u]),x=K.useCallback(async()=>{await U(),p.current=window.setTimeout(async()=>{await x()},g)},[U]),O=K.useCallback(async()=>{if(s||f){i(!1),r(!1),clearTimeout(p.current);return}i(!0),r(!0),await x()},[s,f,x,i,r]);return _.jsxs(fl,{children:[_.jsx(pn,{label:k("Server ⇄ Browser"),children:_.jsx(Kd,{onClick:O,status:s?Un.Loading:Un.Pointer,children:k(s?"Stop ping":"Start ping")})}),_.jsx(ix,{refContainer:h})]})}),rx=K.memo(()=>_.jsx(dn,{id:ku.id,title:k("Ping"),children:_.jsx(lx,{})})),sx=()=>_.jsx(hn,{id:ku.id,title:k("Ping")}),ux={id:ku.id,content:rx,nav:sx},cx={servers:"_servers_1dtle_5"};var eh,Rg;function fx(){return Rg||(Rg=1,eh=function(){var i=document.getSelection();if(!i.rangeCount)return function(){};for(var r=document.activeElement,u=[],s=0;s<i.rangeCount;s++)u.push(i.getRangeAt(s));switch(r.tagName.toUpperCase()){case"INPUT":case"TEXTAREA":r.blur();break;default:r=null;break}return i.removeAllRanges(),function(){i.type==="Caret"&&i.removeAllRanges(),i.rangeCount||u.forEach(function(f){i.addRange(f)}),r&&r.focus()}}),eh}var th,zg;function dx(){if(zg)return th;zg=1;var i=fx(),r={"text/plain":"Text","text/html":"Url",default:"Text"},u="Copy to clipboard: #{key}, Enter";function s(h){var p=(/mac os x/i.test(navigator.userAgent)?"⌘":"Ctrl")+"+C";return h.replace(/#{\s*key\s*}/g,p)}function f(h,p){var b,g,N,U,x,O,L=!1;p||(p={}),b=p.debug||!1;try{N=i(),U=document.createRange(),x=document.getSelection(),O=document.createElement("span"),O.textContent=h,O.ariaHidden="true",O.style.all="unset",O.style.position="fixed",O.style.top=0,O.style.clip="rect(0, 0, 0, 0)",O.style.whiteSpace="pre",O.style.webkitUserSelect="text",O.style.MozUserSelect="text",O.style.msUserSelect="text",O.style.userSelect="text",O.addEventListener("copy",function(te){if(te.stopPropagation(),p.format)if(te.preventDefault(),typeof te.clipboardData>"u"){b&&console.warn("unable to use e.clipboardData"),b&&console.warn("trying IE specific stuff"),window.clipboardData.clearData();var ne=r[p.format]||r.default;window.clipboardData.setData(ne,h)}else te.clipboardData.clearData(),te.clipboardData.setData(p.format,h);p.onCopy&&(te.preventDefault(),p.onCopy(te.clipboardData))}),document.body.appendChild(O),U.selectNodeContents(O),x.addRange(U);var I=document.execCommand("copy");if(!I)throw new Error("copy command was unsuccessful");L=!0}catch(te){b&&console.error("unable to copy using execCommand: ",te),b&&console.warn("trying IE specific stuff");try{window.clipboardData.setData(p.format||"text",h),p.onCopy&&p.onCopy(window.clipboardData),L=!0}catch(ne){b&&console.error("unable to copy using clipboardData: ",ne),b&&console.error("falling back to prompt"),g=s("message"in p?p.message:u),window.prompt(g,h)}}finally{x&&(typeof x.removeRange=="function"?x.removeRange(U):x.removeAllRanges()),O&&document.body.removeChild(O),N()}return L}return th=f,th}var hx=dx();const px=mr(hx),mx={main:"_main_1j8ow_12"},ec=({ruby:i,rt:r,isResult:u=!1,...s})=>_.jsxs("ruby",{className:mx.main,"data-is-result":u||void 0,...s,children:[i,_.jsx("rp",{children:"("}),_.jsx("rt",{children:r}),_.jsx("rp",{children:")"})]}),vx={main:"_main_fajqi_1"},yx=({totalMarks:i,total:r})=>_.jsx("div",{className:vx.main,children:_.jsx(Ku,{high:i*.7,low:i*.5,max:i,optimum:i,value:r})}),Oo={main:"_main_1e6oe_13",header:"_header_1e6oe_22",link:"_link_1e6oe_28",marks:"_marks_1e6oe_46",sign:"_sign_1e6oe_62"},gx=({cpu:i,read:r,write:u,date:s})=>{const f=i+r+u,h=i.toLocaleString(),p=r.toLocaleString(),b=u.toLocaleString(),g=f.toLocaleString(),N=fn("{{cpu}} (CPU) + {{read}} (Read) + {{write}} (Write) = {{total}}",{cpu:h,read:p,write:b,total:g}),U=_.jsx("span",{className:Oo.sign,children:"+"}),x=O=>{O.preventDefault(),O.stopPropagation(),px(N)};return _.jsxs("button",{className:Oo.marks,onClick:x,title:k("Touch to copy marks"),type:"button",children:[_.jsx(ec,{rt:"CPU",ruby:h}),U,_.jsx(ec,{rt:k("Read"),ruby:p}),U,_.jsx(ec,{rt:k("Write"),ruby:b}),_.jsx("span",{className:Oo.sign,children:"="}),_.jsx(ec,{isResult:!0,rt:s||"",ruby:g})]})},Dg=({header:i,marks:r,maxMarks:u,date:s})=>{const{cpu:f,read:h,write:p}=r;return _.jsxs("div",{className:Oo.main,children:[_.jsx("div",{className:Oo.header,children:i}),_.jsx(gx,{cpu:f,date:s,read:h,write:p}),_.jsx(yx,{total:f+h+p,totalMarks:u})]})};mt({enforceActions:"observed"});let bx=class{benchmarking=!1;maxMarks=0;servers=[];constructor(){bt(this)}setMaxMarks=r=>{this.maxMarks=r};setServers=r=>{this.servers=r};setServer=(r,u)=>{const s=this.servers.findIndex(f=>f.id===r);s!==-1&&(this.servers[s]=u)};setBenchmarking=r=>{this.benchmarking=r}};const Cg=new bx,_x=be(()=>{const[i,r]=K.useState(!1),{setMaxMarks:u,maxMarks:s}=Cg,[f,h]=K.useState({cpu:0,read:0,write:0}),p=K.useCallback(async N=>{if(N.preventDefault(),N.stopPropagation(),i)return;r(!0);const{data:U,status:x}=await oa("benchmarkPerformance");if(r(!1),x===ba){if(U?.marks){h(U.marks);const O=Object.values(U.marks).reduce((L,I)=>L+I,0);O>s&&u(O);return}bo.open(k("Network error, please try again later."));return}if(U?.seconds&&x===Tw){bo.open(fn(k("Please wait {{seconds}}s"),{seconds:U.seconds}));return}bo.open(k("Network error, please try again later."))},[i,s,u]),b=new Date,g=_.jsx(Kd,{onClick:p,status:i?Un.Loading:Un.Pointer,children:k("Benchmark my server")});return _.jsx(Dg,{date:`${b.getFullYear()}-${b.getMonth()+1}-${b.getDate()}`,header:g,marks:f,maxMarks:s})}),Sx=be(()=>{const[i,r]=K.useState(!0),[u,s]=K.useState(!1),{servers:f,setServers:h,setMaxMarks:p,maxMarks:b}=Cg;K.useEffect(()=>{(async()=>{r(!0);const{data:U,status:x}=await oa("benchmarkServers");if(r(!1),!U?.length||x!==ba){s(!0);return}s(!1);let O=0;h(U.map(L=>(L.total=L.detail?Object.values(L.detail).reduce((I,te)=>I+te,0):0,L.total>O&&(O=L.total),L)).toSorted((L,I)=>(I?.total??0)-(L?.total??0))),p(O)})()},[h,p]);const g=f.map(({name:N,url:U,date:x,probeUrl:O,binUrl:L,detail:I})=>{if(!I)return null;const{cpu:te=0,read:ne=0,write:ie=0}=I,Qe=O?_.jsx("a",{className:Oo.link,href:O,rel:"noreferrer",target:"_blank",title:k("Visit probe page"),children:_.jsx(eA,{})}):"",$e=L?_.jsx("a",{className:Oo.link,href:L,rel:"noreferrer",target:"_blank",title:k("Download speed test"),children:_.jsx(Fw,{})}):"",Ne=_.jsx("a",{className:Oo.link,href:U,rel:"noreferrer",target:"_blank",title:k("Visit the official website"),children:N});return _.jsx(Dg,{date:x,header:_.jsxs(_.Fragment,{children:[Ne,Qe,$e]}),marks:{cpu:te,read:ne,write:ie},maxMarks:b},N)});return _.jsxs("div",{className:cx.servers,children:[_.jsx(_x,{}),i?[...new Array(5)].map(()=>_.jsx(Eg,{},Math.random())):g,u&&_.jsx(wg,{children:k("Can not fetch marks data from GitHub.")})]})}),Tx=K.memo(()=>_.jsxs(dn,{id:Gu.id,title:k("Server Benchmark"),children:[_.jsx(vg,{items:[{id:"serverBenchmarkTos",text:k("Different versions cannot be compared, and different time servers have different loads, just for reference.")}]}),_.jsx(Sx,{})]})),Ox=()=>_.jsx(hn,{id:Gu.id,title:k("Server bench")}),Ex={id:Gu.id,content:Tx,nav:Ox},wx=be(({serverUptime:i,serverTime:r})=>{const{days:u,hours:s,mins:f,secs:h}=i,p=fn(k("{{days}}d {{hours}}h {{mins}}min {{secs}}s"),{days:u,hours:s,mins:f,secs:h}),b=[[k("Time"),r],[k("Uptime"),p]];return _.jsx(_.Fragment,{children:b.map(([g,N])=>_.jsx(pn,{label:g,maxWidth:6,children:N},g))})}),Ax=K.memo(({cpuModel:i,serverOs:r,scriptPath:u,publicIpv4:s})=>{const f=[[k("Location (IPv4)"),_.jsx(_g,{ip:s},"serverLocalIpv4")],[k("CPU model"),i??k("Unavailable")],[k("OS"),r??k("Unavailable")],[k("Script path"),u??k("Unavailable")]];return _.jsx(fl,{children:f.map(([h,p])=>_.jsx(pn,{label:h,maxWidth:6,children:p},h))})}),xx=K.memo(({serverName:i,serverSoftware:r,publicIpv4:u,publicIpv6:s,localIpv4:f,localIpv6:h})=>{const p=[[k("Name"),i??k("Unavailable")],[k("Web server"),r??k("Unavailable")],[k("Public IPv4"),u||"-"],[k("Public IPv6"),s||"-"],[k("Local IPv4"),f||"-"],[k("Local IPv6"),h||"-"]];return _.jsx(_.Fragment,{children:p.map(([b,g])=>_.jsx(pn,{label:b,maxWidth:6,children:g},b))})}),Rx=be(()=>{const{pollData:i,publicIpv4:r,publicIpv6:u,setPublicIpv4:s,setPublicIpv6:f}=Yd;return K.useEffect(()=>{(async()=>{const{data:p,status:b}=await oa("serverPublicIpv4");p?.ip&&b===ba&&s(p.ip)})()},[s]),K.useEffect(()=>{(async()=>{const{data:p,status:b}=await oa("serverPublicIpv6");p?.ip&&b===ba&&f(p.ip)})()},[f]),i?_.jsxs(dn,{id:Yu.id,title:k("Server Info"),children:[_.jsxs(Zu,{minWidth:20,children:[_.jsx(wx,{serverTime:i.serverTime,serverUptime:i.serverUptime}),_.jsx(xx,{localIpv4:i.localIpv4,localIpv6:i.localIpv6,publicIpv4:r,publicIpv6:u,serverName:i.serverName,serverSoftware:i.serverSoftware})]}),_.jsx(Ax,{cpuModel:i.cpuModel,publicIpv4:r,scriptPath:i.scriptPath,serverOs:i.serverOs})]}):null}),zx=be(()=>{const{pollData:i}=Yd;return i?_.jsx(hn,{id:Yu.id,title:k("Info")}):null}),Dx={id:Yu.id,content:Rx,nav:zx},Mg={main:"_main_raw5t_1",modules:"_modules_raw5t_6"},Cx=be(()=>{const{max:i,value:r}=_o.memBuffers;return _.jsx(hi,{isCapacity:!0,max:i,name:k("Memory buffers"),title:k("Buffers are in-memory block I/O buffers. They are relatively short-lived. Prior to Linux kernel version 2.4, Linux had separate page and buffer caches. Since 2.4, the page and buffer cache are unified and Buffers is raw disk blocks not represented in the page cache—i.e., not file data."),value:r})}),Mx=be(()=>{const{max:i,value:r}=_o.memCached;return _.jsx(hi,{isCapacity:!0,max:i,name:k("Memory cached"),title:k('Cached memory is memory that Linux uses for disk caching. However, this does not count as "used" memory, since it will be freed when applications require it. Hence you do not have to worry if a large amount is being used.'),value:r})}),jx=be(()=>{const{max:i,value:r}=_o.memRealUsage;return _.jsx(hi,{isCapacity:!0,max:i,name:k("Memory real usage"),title:k('Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.'),value:r})}),Ux=be(()=>{const{max:i,value:r}=_o.swapCached;return i?_.jsx(hi,{isCapacity:!0,max:i,name:k("Swap cached"),value:r}):null}),Nx=be(()=>{const{max:i,value:r}=_o.swapUsage;return i?_.jsx(hi,{isCapacity:!0,max:i,name:k("Swap usage"),value:r}):null}),kx=()=>_.jsx(dn,{id:Xu.id,title:k("Server Status"),children:_.jsx("div",{className:Mg.main,children:_.jsxs("div",{className:Mg.modules,children:[_.jsx(UA,{}),_.jsx(jx,{}),_.jsx(Mx,{}),_.jsx(Cx,{}),_.jsx(Nx,{}),_.jsx(Ux,{})]})})}),Hx=be(()=>{const{pollData:i}=_o;return i?_.jsx(hn,{id:Xu.id,title:k("Info")}):null}),Lx={id:Xu.id,content:kx,nav:Hx};mt({enforceActions:"observed"});class Bx{pollData=null;latestPhpVersion="";constructor(){bt(this)}setPollData=r=>{Mn(r,this.pollData)||(this.pollData=r)};setLatestPhpVersion=r=>{this.latestPhpVersion=r}}const nh=new Bx,Vx=be(()=>{const{pollData:i}=nh;if(!i?.items?.length)return null;const{items:r}=i;return _.jsx(dn,{id:Iu.id,title:k("Temperature sensor"),children:_.jsx(fl,{children:r.map(({id:u,name:s,celsius:f})=>_.jsx(pn,{title:fn(k("{{sensor}} temperature"),{sensor:s}),children:_.jsx(hi,{isCapacity:!1,max:150,percentTag:"℃",value:f})},u))})})}),Px=be(()=>{const{pollData:i}=nh;if(!i?.items?.length)return null;const{items:r}=i;return r.length?_.jsx(hn,{id:Iu.id,title:k("Temperature")}):null}),$x={id:Iu.id,content:Vx,nav:Px},jg={items:[IA,$x,Lx,RA,_A,ux,Dx,tx,JA,mA,Ex,cA,EA]},qx=be(()=>{const{setSortedModules:i,availableModules:r}=Id;return K.useEffect(()=>{const u=dg.getItems(),s=[];for(const f of jg.items)s.push({id:f.id,priority:Number(u?.[f.id])||Xd.indexOf(f.id)});i(s)},[i]),r.length?_.jsx("div",{className:Yw.container,children:r.map(({id:u,content:s})=>_.jsx(s,{},u))}):null}),Gx=be(()=>{const{availableModules:i}=Id,r=i.map(({id:u,nav:s})=>_.jsx(s,{},u));return _.jsx("div",{className:yg.main,children:r})}),Yx={main:"_main_nuyl9_6"},Xx=()=>_.jsx("div",{className:Yx.main,children:"Loading..."}),Ix=()=>{const[i,r]=K.useState(!0),{isUpdating:u}=Bd;return K.useEffect(()=>{let s,f=!0;const h=async()=>{try{if(u)return;const{data:p,status:b}=await oa("poll");p&&b===200?(Ld.setPollData(p),zd.setPollData(p?.config),Gw.setPollData(p?.userConfig),Jy.setPollData(p?.database),Pd.setPollData(p?.myInfo),Uu.setPollData(p?.phpInfo),Dd.setPollData(p?.diskUsage),Gd.setPollData(p?.phpExtensions),$d.setPollData(p?.networkStats),_o.setPollData(p?.serverStatus),Yd.setPollData(p?.serverInfo),qd.setPollData(p?.nodes),nh.setPollData(p?.temperatureSensor)):bo.open(k("Failed to fetch data. Please try again later.")),i&&r(!1)}finally{f&&(s=setTimeout(h,2e3))}};return h(),()=>{f=!1,clearTimeout(s)}},[i,u]),i?_.jsx(Xx,{}):_.jsxs(_.Fragment,{children:[_.jsx(Cw,{}),_.jsx(qx,{}),_.jsx(gw,{}),_.jsx(Gx,{}),_.jsx($w,{})]})};document.addEventListener("DOMContentLoaded",()=>{document.body.innerHTML="",F1.createRoot(document.body).render(_.jsx(Ix,{}))})});

CODE;
        exit;
    }
}
namespace InnStudio\Prober\Components\Nodes;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
class NodesApi
{
    public static function getUserConfigNodes()
    {
        $items = UserConfigApi::get(NodesConstants::ID);
        if ( ! $items || ! \is_array($items)) {
            return [];
        }
        return array_values(
            array_filter(
                array_map(function ($item) {
                    if (2 !== \count($item)) {
                        return;
                    }
                    return [
                        'id' => $item[0],
                        'url' => $item[1],
                    ];
                }, $items)
            )
        );
    }
}
namespace InnStudio\Prober\Components\Nodes;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\Utils\UtilsApi;
final class NodesAction
{
    public function render($action)
    {
        if (NodesConstants::ID !== $action) {
            return;
        }
        $nodeId = filter_input(\INPUT_GET, 'nodeId', \FILTER_DEFAULT);
        $response = new RestResponse();
        if ( ! $nodeId) {
            $response
                ->setStatus(StatusCode::BAD_REQUEST)
                ->end();
        }
        $data = $this->getNodeData($nodeId);
        if ( ! $data) {
            $response
                ->setStatus(StatusCode::NO_CONTENT)
                ->end();
        }
        $response
            ->setData($data)
            ->end();
    }
    private function getNodeData($nodeId)
    {
        $node = UtilsApi::arrayFind(NodesApi::getUserConfigNodes(), function ($item) use ($nodeId) {
            return isset($item['url']) && isset($item['id']) && $item['id'] === $nodeId;
        });
        if ( ! $node) {
            return;
        }
        $isDev = \defined('XPROBER_IS_DEV') && XPROBER_IS_DEV;
        $url = $node['url'];
        $isRemote = ( ! str_contains($url, 'localhost') || ! str_contains($url, '127.0.0.1'));
        $params = 'action=poll';
        $url = ($isDev && ! $isRemote) ? "{$url}/api?{$params}" : "{$url}?{$params}";
        return $this->getRemoteData($url);
    }
    private function getRemoteData($url)
    {
        if (\function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                \CURLOPT_RETURNTRANSFER => true,
            ]);
            $content = curl_exec($ch);
            curl_close($ch);
            return json_decode($content) ?: null;
        }
        return json_decode(file_get_contents($url)) ?: null;
    }
}
namespace InnStudio\Prober\Components\Nodes;
class NodesConstants
{
    const ID = 'nodes';
}
namespace InnStudio\Prober\Components\Nodes;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class NodesPoll
{
    public function render()
    {
        $id = NodesConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        $items = array_map(function ($item) {
            return $item['id'];
        }, NodesApi::getUserConfigNodes());
        if ( ! $items) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'nodesIds' => $items,
            ],
        ];
    }
}
/**
 * The file is automatically generated.
 */
namespace InnStudio\Prober\Components\Config;
class ConfigApi
{
    public static $config = array (
  'APP_VERSION' => '9.0.8',
  'APP_NAME' => 'X Prober',
  'APP_URL' => 'https://github.com/kmvan/x-prober',
  'AUTHOR_URL' => 'https://inn-studio.com/prober',
  'UPDATE_PHP_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/dist/prober.php',
    1 => 'https://api.inn-studio.com/download/?id=xprober',
  ),
  'APP_CONFIG_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/AppConfig.json',
    1 => 'https://api.inn-studio.com/download/?id=xprober-config',
  ),
  'BENCHMARKS_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/benchmarks.json',
    1 => 'https://api.inn-studio.com/download/?id=xprober-benchmarks',
  ),
  'APP_CONFIG_URL_DEV' => 'http://localhost:8000/AppConfig.json',
  'APP_TEMPERATURE_SENSOR_URL' => 'http://127.0.0.1',
  'APP_TEMPERATURE_SENSOR_PORTS' => 
  array (
    0 => 2048,
    1 => 4096,
  ),
  'AUTHOR_NAME' => 'INN STUDIO',
  'LATEST_PHP_STABLE_VERSION' => '8',
  'LATEST_NGINX_STABLE_VERSION' => '1.22.0',
  'BENCHMARKS' => 
  array (
    0 => 
    array (
      'name' => 'Olink / E5-2680 v4 / PHP83 JIT',
      'url' => 'https://www.olink.cloud/clients/aff.php?aff=120',
      'date' => '2024-05-29',
      'proberUrl' => 'https://x-prober-server-benchmark-olink-sj.inn-studio.com',
      'binUrl' => '',
      'detail' => 
      array (
        'cpu' => 268212,
        'read' => 18495,
        'write' => 6164,
      ),
    ),
    1 => 
    array (
      'name' => 'RamNode / PHP82 JIT',
      'url' => 'https://clientarea.ramnode.com/aff.php?aff=4143',
      'date' => '2023-05-02',
      'detail' => 
      array (
        'cpu' => 203245,
        'read' => 68706,
        'write' => 11452,
      ),
    ),
    2 => 
    array (
      'name' => 'SpartanHost / HDD / PHP80 JIT',
      'url' => 'https://billing.spartanhost.net/aff.php?aff=801',
      'date' => '2021-07-17',
      'proberUrl' => 'https://x-prober-server-benchmark-spartanhost-dalls.inn-studio.com',
      'binUrl' => 'https://lg.dal.spartanhost.net/100MB.test',
      'detail' => 
      array (
        'cpu' => 280903,
        'read' => 65551,
        'write' => 16238,
      ),
    ),
    3 => 
    array (
      'name' => 'Vultr / Tokyo / PHP82 JIT',
      'url' => 'https://www.vultr.com/?ref=7826363-4F',
      'date' => '2023-05-02',
      'proberUrl' => 'https://x-prober-server-benchmark-vultr-tokyo.inn-studio.com/',
      'binUrl' => 'https://hnd-jp-ping.vultr.com/vultr.com.100MB.bin',
      'detail' => 
      array (
        'cpu' => 243748,
        'read' => 46066,
        'write' => 13824,
      ),
    ),
    4 => 
    array (
      'name' => 'BandwagonHOST / KVM / PHP80 JIT',
      'url' => 'https://bandwagonhost.com/aff.php?aff=34116',
      'proberUrl' => 'https://x-prober-server-benchmark-bwh-los-angeles.inn-studio.com/',
      'binUrl' => 'https://x-prober-server-benchmark-bwh-los-angeles.inn-studio.com/512m.bin',
      'date' => '2021-07-17',
      'detail' => 
      array (
        'cpu' => 185491,
        'read' => 13616,
        'write' => 4529,
      ),
    ),
  ),
);
}
namespace InnStudio\Prober\Components\Config;
final class ConfigPoll
{
    public function render()
    {
        $config = ConfigApi::$config;
        return [
            ConfigConstants::ID => [
                'APP_VERSION' => $config['APP_VERSION'],
                'APP_NAME' => $config['APP_NAME'],
                'APP_URL' => $config['APP_URL'],
                'AUTHOR_URL' => $config['AUTHOR_URL'],
                'UPDATE_PHP_URLS' => $config['UPDATE_PHP_URLS'],
                'APP_CONFIG_URLS' => $config['APP_CONFIG_URLS'],
                'APP_CONFIG_URL_DEV' => $config['APP_CONFIG_URL_DEV'],
                'APP_TEMPERATURE_SENSOR_URL' => $config['APP_TEMPERATURE_SENSOR_URL'],
                'APP_TEMPERATURE_SENSOR_PORTS' => $config['APP_TEMPERATURE_SENSOR_PORTS'],
                'AUTHOR_NAME' => $config['AUTHOR_NAME'],
                'LATEST_PHP_STABLE_VERSION' => $config['LATEST_PHP_STABLE_VERSION'],
                'LATEST_NGINX_STABLE_VERSION' => $config['LATEST_NGINX_STABLE_VERSION'],
            ],
        ];
    }
}
namespace InnStudio\Prober\Components\Config;
class ConfigConstants
{
    const ID = 'config';
}
namespace InnStudio\Prober\Components\Database;
class DatabaseConstants
{
    const ID = 'database';
}
namespace InnStudio\Prober\Components\Database;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use PDO;
use SQLite3;
final class DatabasePoll
{
    public function render()
    {
        $id = DatabaseConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        $sqlite3Version = class_exists('SQLite3') ? SQLite3::version() : false;
        return [
            $id => [
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
namespace InnStudio\Prober\Components\Updater;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
final class UpdaterActionUpdate
{
    public function render($action)
    {
        if ('update' !== $action) {
            return $action;
        }
        $response = new RestResponse();
        // prevent update file on dev mode
        if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            $response
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        // check file writable
        if ( ! is_writable(__FILE__)) {
            $response
                ->setStatus(StatusCode::INSUFFICIENT_STORAGE)
                ->end();
        }
        $code = '';
        foreach (ConfigApi::$config['UPDATE_PHP_URLS'] as $url) {
            $curl = curl_init($url);
            curl_setopt($curl, \CURLOPT_RETURNTRANSFER, true);
            $status = (int) curl_getinfo($curl, \CURLINFO_HTTP_CODE);
            $code = (string) curl_exec($curl);
            curl_close($curl);
            if (StatusCode::OK !== $status || '' !== trim($code)) {
                break;
            }
        }
        if ( ! $code) {
            $response
                ->setStatus(StatusCode::NOT_FOUND)
                ->end();
        }
        if ((bool) file_put_contents(__FILE__, $code)) {
            if (\function_exists('opcache_invalidate')) {
                opcache_invalidate(__FILE__, true) || opcache_reset();
            }
            $response
                ->setStatus(StatusCode::CREATED)
                ->end();
        }
        $response
            ->setStatus(StatusCode::INTERNAL_SERVER_ERROR)
            ->end();
    }
}
namespace InnStudio\Prober\Components\Updater;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
final class UpdaterActionVersion
{
    public function render($action)
    {
        if ('latestVersion' !== $action) {
            return;
        }
        $response = new RestResponse();
        foreach (ConfigApi::$config['APP_CONFIG_URLS'] as $url) {
            $curl = curl_init();
            curl_setopt($curl, \CURLOPT_URL, $url);
            curl_setopt($curl, \CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, \CURLOPT_TIMEOUT, 3);
            $data = curl_exec($curl);
            curl_close($curl);
            if ( ! $data) {
                continue;
            }
            $data = json_decode($data, true);
            if ( ! $data) {
                continue;
            }
            $response
                ->setData([
                    'version' => $data['APP_VERSION'],
                ])
                ->end();
        }
        $response
            ->setStatus(StatusCode::NO_CONTENT)
            ->end();
    }
}
namespace InnStudio\Prober\Components\Updater;
class UpdaterConstants
{
    const ID = 'updater';
}
namespace InnStudio\Prober\Components\UserConfig;
use InnStudio\Prober\Components\Utils\UtilsApi;
final class UserConfigApi
{
    private static $conf;
    private static $filename = 'xconfig.json';
    public static function isDisabled($id)
    {
        return \in_array($id, self::get('disabled') ?: [], true);
    }
    public static function get($id = null)
    {
        self::setConf();
        if ($id) {
            return isset(self::$conf[$id]) ? self::$conf[$id] : null;
        }
        return self::$conf;
    }
    private static function getFilePath()
    {
        if ( ! \defined('XPROBER_DIR')) {
            return '';
        }
        if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            return \dirname(XPROBER_DIR) . '/' . self::$filename;
        }
        return XPROBER_DIR . '/' . self::$filename;
    }
    private static function setConf()
    {
        if (null !== self::$conf) {
            return;
        }
        if ( ! is_readable(self::getFilePath())) {
            self::$conf = null;
            return;
        }
        $conf = UtilsApi::jsonDecode(file_get_contents(self::getFilePath()));
        if ( ! $conf) {
            self::$conf = null;
            return;
        }
        self::$conf = $conf;
    }
}
namespace InnStudio\Prober\Components\UserConfig;
class UserConfigConstants
{
    const ID = 'userConfig';
}
namespace InnStudio\Prober\Components\UserConfig;
final class UserConfigPoll
{
    public function render()
    {
        return [
            UserConfigConstants::ID => UserConfigApi::get(),
        ];
    }
}
namespace InnStudio\Prober\Components\ServerInfo;
class ServerInfoConstants
{
    const ID = 'serverInfo';
    const FEATURE_SERVER_IP = 'serverIp';
}
namespace InnStudio\Prober\Components\ServerInfo;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsLocation;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
final class ServerInfoLocationIpv4Action
{
    public function render($action)
    {
        if ('serverLocationIpv4' !== $action) {
            return;
        }
        if (UserConfigApi::isDisabled(ServerInfoConstants::ID) || UserConfigApi::isDisabled(ServerInfoConstants::FEATURE_SERVER_IP)) {
            (new RestResponse())
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        $response = new RestResponse();
        $ip = UtilsServerIp::getPublicIpV4();
        if ( ! $ip) {
            $response
                ->setStatus(StatusCode::INTERNAL_SERVER_ERROR)
                ->end();
        }
        $response
            ->setData(UtilsLocation::getLocation($ip))
            ->end();
    }
}
namespace InnStudio\Prober\Components\ServerInfo;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsCpu;
use InnStudio\Prober\Components\Utils\UtilsDisk;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
use InnStudio\Prober\Components\Utils\UtilsTime;
final class ServerInfoPoll
{
    public function render()
    {
        $id = ServerInfoConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => [
                'serverName' => $this->getServerInfo('SERVER_NAME'),
                'serverUtcTime' => UtilsTime::getUtcTime(),
                'localIpv4' => UtilsServerIp::getLocalIpv4(),
                'localIpv6' => UtilsServerIp::getLocalIpv6(),
                'serverTime' => UtilsTime::getTime(),
                'serverUptime' => UtilsTime::getUptime(),
                'serverSoftware' => $this->getServerInfo('SERVER_SOFTWARE'),
                'phpVersion' => \PHP_VERSION,
                'cpuModel' => UtilsCpu::getModel(),
                'serverOs' => php_uname(),
                'scriptPath' => __FILE__,
                'diskUsage' => UtilsDisk::getItems(),
            ],
        ];
    }
    private function getServerInfo($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}
namespace InnStudio\Prober\Components\ServerInfo;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
final class ServerInfoPublicIpv4Action
{
    public function render($action)
    {
        if ('serverPublicIpv4' !== $action) {
            return;
        }
        if (UserConfigApi::isDisabled(ServerInfoConstants::ID) || UserConfigApi::isDisabled(ServerInfoConstants::FEATURE_SERVER_IP)) {
            (new RestResponse())
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        (new RestResponse())
            ->setData([
                'ip' => UtilsServerIp::getPublicIpV4(),
            ])
            ->end();
    }
}
namespace InnStudio\Prober\Components\ServerInfo;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
use InnStudio\Prober\Components\Utils\UtilsServerIp;
final class ServerInfoPublicIpv6Action
{
    public function render($action)
    {
        if ('serverPublicIpv6' !== $action) {
            return;
        }
        if (UserConfigApi::isDisabled(ServerInfoConstants::ID) || UserConfigApi::isDisabled(ServerInfoConstants::FEATURE_SERVER_IP)) {
            (new RestResponse())
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        (new RestResponse())
            ->setData([
                'ip' => UtilsServerIp::getPublicIpV6(),
            ])
            ->end();
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsDisk
{
    public static function getItems()
    {
        switch (\PHP_OS) {
            case 'Linux':
                return self::getLinuxItems();
            default:
                return;
        }
    }
    private static function getLinuxItems()
    {
        if ( ! \function_exists('shell_exec')) {
            return [
                [
                    'id' => __DIR__,
                    'free' => disk_free_space(__DIR__),
                    'total' => disk_total_space(__DIR__),
                ],
            ];
        }
        $items = [];
        $dfLines = explode("\n", shell_exec('df -k'));
        if (\count($dfLines) <= 1) {
            return $items;
        }
        $dfLines = \array_slice($dfLines, 1);
        $fsExclude = ['tmpfs', 'run', 'dev'];
        foreach ($dfLines as $dfLine) {
            $dfObj = explode(' ', preg_replace('/\\s+/', ' ', $dfLine));
            if (\count($dfObj) < 6) {
                continue;
            }
            $dfFs = $dfObj[0];
            $dfTotal = (int) $dfObj[1];
            $dfAvailable = (int) $dfObj[3];
            $dfMountedOn = $dfObj[5];
            if (\in_array($dfFs, $fsExclude, true)) {
                continue;
            }
            $free = $dfAvailable * 1024;
            $total = $dfTotal * 1024;
            $items[] = [
                'id' => "{$dfFs}:{$dfMountedOn}",
                'free' => $free,
                'total' => $total,
            ];
        }
        if ( ! $items) {
            return [];
        }
        // sort by total desc
        usort($items, function ($a, $b) {
            return $b['total'] - $a['total'];
        });
        return $items;
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsClientIp
{
    public static function getV4()
    {
        $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if ( ! isset($_SERVER[$key])) {
                continue;
            }
            $ip = array_filter(explode(',', $_SERVER[$key]));
            $ip = filter_var(end($ip), \FILTER_VALIDATE_IP, [
                'flags' => \FILTER_FLAG_IPV4,
            ]);
            if ($ip) {
                return $ip;
            }
        }
        return '';
    }
    public static function getV6()
    {
        $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if ( ! isset($_SERVER[$key])) {
                continue;
            }
            $ip = array_filter(explode(',', $_SERVER[$key]));
            $ip = filter_var(end($ip), \FILTER_VALIDATE_IP, [
                'flags' => \FILTER_FLAG_IPV6,
            ]);
            if ($ip) {
                return $ip;
            }
        }
        return '';
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsMemory
{
    public static function getMemoryUsage($key)
    {
        $key = ucfirst($key);
        if (UtilsApi::isWin()) {
            return 0;
        }
        static $memInfo = null;
        if (null === $memInfo) {
            $memInfoFile = '/proc/meminfo';
            error_reporting(0);
            if ( ! is_readable($memInfoFile)) {
                error_reporting(\E_ALL);
                $memInfo = 0;
                return 0;
            }
            $memInfo = file_get_contents($memInfoFile);
            $memInfo = str_replace([
                ' kB',
                '  ',
            ], '', $memInfo);
            $lines = [];
            foreach (explode("\n", $memInfo) as $line) {
                if ( ! $line) {
                    continue;
                }
                $line = explode(':', $line);
                $lines[$line[0]] = (float) $line[1] * 1024;
            }
            $memInfo = $lines;
        }
        if ( ! isset($memInfo['MemTotal'])) {
            return 0;
        }
        switch ($key) {
            case 'MemRealUsage':
                if (isset($memInfo['MemAvailable'])) {
                    return $memInfo['MemTotal'] - $memInfo['MemAvailable'];
                }
                if (isset($memInfo['MemFree'])) {
                    if (isset($memInfo['Buffers'], $memInfo['Cached'])) {
                        return $memInfo['MemTotal'] - $memInfo['MemFree'] - $memInfo['Buffers'] - $memInfo['Cached'];
                    }
                    return $memInfo['MemTotal'] - $memInfo['Buffers'];
                }
                return 0;
            case 'MemUsage':
                return isset($memInfo['MemFree']) ? $memInfo['MemTotal'] - $memInfo['MemFree'] : 0;
            case 'SwapUsage':
                if ( ! isset($memInfo['SwapTotal']) || ! isset($memInfo['SwapFree'])) {
                    return 0;
                }
                return $memInfo['SwapTotal'] - $memInfo['SwapFree'];
        }
        return isset($memInfo[$key]) ? $memInfo[$key] : 0;
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsLocation
{
    /**
     * Get IP location.
     *
     * @param [string] $ip
     *
     * @return array|null $args
     *                    $args['country'] string Country, e.g, China
     *                    $args['region'] string Region, e.g, Heilongjiang
     *                    $args['city'] string City, e.g, Mohe
     *                    $args['flag'] string Emoji string, e,g, 🇨🇳
     */
    public static function getLocation($ip)
    {
        $url = "https://api.inn-studio.com/ip-location/?ip={$ip}";
        $content = '';
        if (\function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                \CURLOPT_URL => $url,
                \CURLOPT_RETURNTRANSFER => true,
            ]);
            $content = (string) curl_exec($ch);
            curl_close($ch);
        } else {
            $content = file_get_contents($url);
        }
        $item = json_decode($content, true) ?: null;
        if ( ! $item) {
            return;
        }
        // get langcode from en-US,en;q=0.9,zh-CN;q=0.8,zh-TW;q=0.7,zh;q=0.6
        $langcode = '';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            if (str_contains($_SERVER['HTTP_ACCEPT_LANGUAGE'], ',')) {
                $langcode = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], ','));
            }
        }
        switch ($langcode) {
            case 'en-US':
                $langcode = 'en';
                break;
            case 'zh-TW':
            case 'zh-HK':
            case 'zh':
                $langcode = 'zh-CN';
                break;
        }
        if ( ! \in_array($langcode, ['en', 'de', 'es', 'ru', 'pt-BR', 'fr', 'zh-CN'], true)) {
            $langcode = 'en';
        }
        return [
            'continent' => isset($item['continent']['names'][$langcode]) ? $item['continent']['names'][$langcode] : '',
            'country' => isset($item['country']['names'][$langcode]) ? $item['country']['names'][$langcode] : '',
            'city' => isset($item['city']['names'][$langcode]) ? $item['city']['names'][$langcode] : '',
        ];
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsTime
{
    public static function getTime()
    {
        return date('Y-m-d H:i:s');
    }
    public static function getUtcTime()
    {
        return gmdate('Y/m/d H:i:s');
    }
    public static function getUptime()
    {
        $filePath = '/proc/uptime';
        error_reporting(0);
        if ( ! is_file($filePath)) {
            error_reporting(\E_ALL);
            return [
                'days' => 0,
                'hours' => 0,
                'mins' => 0,
                'secs' => 0,
            ];
        }
        $str = file_get_contents($filePath);
        $num = (float) $str;
        $secs = (int) fmod($num, 60);
        $num = (int) ($num / 60);
        $mins = (int) $num % 60;
        $num = (int) ($num / 60);
        $hours = (int) $num % 24;
        $num = (int) ($num / 24);
        $days = (int) $num;
        return [
            'days' => $days,
            'hours' => $hours,
            'mins' => $mins,
            'secs' => $secs,
        ];
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsApi
{
    public static function arrayFind(array $array, $callback)
    {
        foreach ($array as $value) {
            if (\call_user_func($callback, $value)) {
                return $value;
            }
        }
    }
    public static function jsonDecode($json, $depth = 512, $options = 0)
    {
        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\\*([^*]|[\r\n]|(\\*+([^*/]|[\r\n])))*\\*+/)|([\\s\t]//.*)|(^//.*)#", '', $json);
        if (\PHP_VERSION_ID >= 50400) {
            return json_decode($json, true, $depth, $options);
        }
        if (\PHP_VERSION_ID >= 50300) {
            return json_decode($json, true, $depth);
        }
        return json_decode($json, true);
    }
    public static function setFileCacheHeader()
    {
        // 1 year expired
        $seconds = 3600 * 24 * 30 * 12;
        $ts = gmdate('D, d M Y H:i:s', (int) $_SERVER['REQUEST_TIME'] + $seconds) . ' GMT';
        header("Expires: {$ts}");
        header('Pragma: cache');
        header("Cache-Control: public, max-age={$seconds}");
    }
    public static function getErrNameByCode($code)
    {
        if (0 === (int) $code) {
            return '';
        }
        $levels = [
            \E_ALL => 'E_ALL',
            \E_USER_DEPRECATED => 'E_USER_DEPRECATED',
            \E_DEPRECATED => 'E_DEPRECATED',
            \E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            \E_USER_NOTICE => 'E_USER_NOTICE',
            \E_USER_WARNING => 'E_USER_WARNING',
            \E_USER_ERROR => 'E_USER_ERROR',
            \E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            \E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            \E_CORE_WARNING => 'E_CORE_WARNING',
            \E_CORE_ERROR => 'E_CORE_ERROR',
            \E_NOTICE => 'E_NOTICE',
            \E_PARSE => 'E_PARSE',
            \E_WARNING => 'E_WARNING',
            \E_ERROR => 'E_ERROR',
        ];
        $result = '';
        foreach ($levels as $number => $name) {
            if (($code & $number) === $number) {
                $result .= ('' !== $result ? ', ' : '') . $name;
            }
        }
        return $result;
    }
    public static function isWin()
    {
        return \PHP_OS === 'WINNT';
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsNetwork
{
    public static function getAuthorization()
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
    }
    public static function getStats()
    {
        $filePath = '/proc/net/dev';
        error_reporting(0);
        if ( ! is_readable($filePath)) {
            error_reporting(\E_ALL);
            return;
        }
        static $eths = null;
        if (null !== $eths) {
            return $eths;
        }
        $lines = file($filePath);
        unset($lines[0], $lines[1]);
        $eths = [];
        foreach ($lines as $line) {
            $line = preg_replace('/\\s+/', ' ', trim($line));
            $lineArr = explode(':', $line);
            $numberArr = explode(' ', trim($lineArr[1]));
            $rx = (float) $numberArr[0];
            $tx = (float) $numberArr[8];
            if ( ! $rx && ! $tx) {
                continue;
            }
            $eths[] = [
                'id' => $lineArr[0],
                'rx' => $rx,
                'tx' => $tx,
            ];
        }
        return $eths;
    }
}
namespace InnStudio\Prober\Components\Utils;
final class UtilsServerIp
{
    public static function getPublicIpV4()
    {
        return self::getV4ViaInnStudioCom() ?: self::getV4ViaIpv6TestCom() ?: '';
    }
    public static function getPublicIpV6()
    {
        return self::getV6ViaInnStudioCom() ?: self::getV6ViaIpv6TestCom() ?: '';
    }
    public static function getLocalIpV4()
    {
        $content = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        return filter_var($content, \FILTER_VALIDATE_IP, [
            'flags' => \FILTER_FLAG_IPV4,
        ]) ?: '';
    }
    public static function getLocalIpV6()
    {
        $content = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        return filter_var($content, \FILTER_VALIDATE_IP, [
            'flags' => \FILTER_FLAG_IPV6,
        ]) ?: '';
    }
    private static function getV4ViaInnStudioCom()
    {
        return self::getContent('https://ipv4.inn-studio.com/ip/', 4);
    }
    private static function getV6ViaInnStudioCom()
    {
        return self::getContent('https://ipv6.inn-studio.com/ip/', 6);
    }
    private static function getV4ViaIpv6TestCom()
    {
        return self::getContent('https://v4.ipv6-test.com/api/myip.php', 4);
    }
    private static function getV6ViaIpv6TestCom()
    {
        return self::getContent('https://v6.ipv6-test.com/api/myip.php', 6);
    }
    private static function getContent($url, $type)
    {
        $content = '';
        if (\function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                \CURLOPT_URL => $url,
                \CURLOPT_RETURNTRANSFER => true,
            ]);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            $content = file_get_contents($url);
        }
        return (string) filter_var($content, \FILTER_VALIDATE_IP, [
            'flags' => 6 === $type ? \FILTER_FLAG_IPV6 : \FILTER_FLAG_IPV4,
        ]) ?: '';
    }
}
namespace InnStudio\Prober\Components\Utils;
use COM;
final class UtilsCpu
{
    private static $HW_IMPLEMENTER = [
        '0x41' => [[
            '0x810' => 'ARM810',
            '0x920' => 'ARM920',
            '0x922' => 'ARM922',
            '0x926' => 'ARM926',
            '0x940' => 'ARM940',
            '0x946' => 'ARM946',
            '0x966' => 'ARM966',
            '0xa20' => 'ARM1020',
            '0xa22' => 'ARM1022',
            '0xa26' => 'ARM1026',
            '0xb02' => 'ARM11 MPCore',
            '0xb36' => 'ARM1136',
            '0xb56' => 'ARM1156',
            '0xb76' => 'ARM1176',
            '0xc05' => 'Cortex-A5',
            '0xc07' => 'Cortex-A7',
            '0xc08' => 'Cortex-A8',
            '0xc09' => 'Cortex-A9',
            '0xc0d' => 'Cortex-A17/A12',
            '0xc0f' => 'Cortex-A15',
            '0xc0e' => 'Cortex-A17',
            '0xc14' => 'Cortex-R4',
            '0xc15' => 'Cortex-R5',
            '0xc17' => 'Cortex-R7',
            '0xc18' => 'Cortex-R8',
            '0xc20' => 'Cortex-M0',
            '0xc21' => 'Cortex-M1',
            '0xc23' => 'Cortex-M3',
            '0xc24' => 'Cortex-M4',
            '0xc27' => 'Cortex-M7',
            '0xc60' => 'Cortex-M0+',
            '0xd01' => 'Cortex-A32',
            '0xd02' => 'Cortex-A34',
            '0xd03' => 'Cortex-A53',
            '0xd04' => 'Cortex-A35',
            '0xd05' => 'Cortex-A55',
            '0xd06' => 'Cortex-A65',
            '0xd07' => 'Cortex-A57',
            '0xd08' => 'Cortex-A72',
            '0xd09' => 'Cortex-A73',
            '0xd0a' => 'Cortex-A75',
            '0xd0b' => 'Cortex-A76',
            '0xd0c' => 'Neoverse-N1',
            '0xd0d' => 'Cortex-A77',
            '0xd0e' => 'Cortex-A76AE',
            '0xd13' => 'Cortex-R52',
            '0xd15' => 'Cortex-R82',
            '0xd16' => 'Cortex-R52+',
            '0xd20' => 'Cortex-M23',
            '0xd21' => 'Cortex-M33',
            '0xd22' => 'Cortex-M55',
            '0xd23' => 'Cortex-M85',
            '0xd40' => 'Neoverse-V1',
            '0xd41' => 'Cortex-A78',
            '0xd42' => 'Cortex-A78AE',
            '0xd43' => 'Cortex-A65AE',
            '0xd44' => 'Cortex-X1',
            '0xd46' => 'Cortex-A510',
            '0xd47' => 'Cortex-A710',
            '0xd48' => 'Cortex-X2',
            '0xd49' => 'Neoverse-N2',
            '0xd4a' => 'Neoverse-E1',
            '0xd4b' => 'Cortex-A78C',
            '0xd4c' => 'Cortex-X1C',
            '0xd4d' => 'Cortex-A715',
            '0xd4e' => 'Cortex-X3',
            '0xd4f' => 'Neoverse-V2',
            '0xd80' => 'Cortex-A520',
            '0xd81' => 'Cortex-A720',
            '0xd82' => 'Cortex-X4',
        ], 'ARM'],
        '0x42' => [[
            '0x0f' => 'Brahma-B15',
            '0x100' => 'Brahma-B53',
            '0x516' => 'ThunderX2',
        ], 'Broadcom'],
        '0x43' => [[
            '0x0a0' => 'ThunderX',
            '0x0a1' => 'ThunderX-88XX',
            '0x0a2' => 'ThunderX-81XX',
            '0x0a3' => 'ThunderX-83XX',
            '0x0af' => 'ThunderX2-99xx',
            '0x0b0' => 'OcteonTX2',
            '0x0b1' => 'OcteonTX2-98XX',
            '0x0b2' => 'OcteonTX2-96XX',
            '0x0b3' => 'OcteonTX2-95XX',
            '0x0b4' => 'OcteonTX2-95XXN',
            '0x0b5' => 'OcteonTX2-95XXMM',
            '0x0b6' => 'OcteonTX2-95XXO',
            '0x0b8' => 'ThunderX3-T110',
        ], 'Cavium'],
        '0x44' => [[
            '0xa10' => 'SA110',
            '0xa11' => 'SA1100',
        ], 'DEC'],
        '0x46' => [[
            '0x001' => 'A64FX',
        ], 'FUJITSU'],
        '0x48' => [[
            '0xd01' => 'TaiShan-v110', // used in Kunpeng-920 SoC
            '0xd02' => 'TaiShan-v120', // used in Kirin 990A and 9000S SoCs
            '0xd40' => 'Cortex-A76', // HiSilicon uses this ID though advertises A76
            '0xd41' => 'Cortex-A77', // HiSilicon uses this ID though advertises A77
        ], 'HiSilicon'],
        '0x49' => [null, 'Infineon'],
        '0x4d' => [null, 'Motorola/Freescale'],
        '0x4e' => [[
            '0x000' => 'Denver',
            '0x003' => 'Denver 2',
            '0x004' => 'Carmel',
        ], 'NVIDIA'],
        '0x50' => [[
            '0x000' => 'X-Gene',
        ], 'APM'],
        '0x51' => [[
            '0x00f' => 'Scorpion',
            '0x02d' => 'Scorpion',
            '0x04d' => 'Krait',
            '0x06f' => 'Krait',
            '0x201' => 'Kryo',
            '0x205' => 'Kryo',
            '0x211' => 'Kryo',
            '0x800' => 'Falkor-V1/Kryo',
            '0x801' => 'Kryo-V2',
            '0x802' => 'Kryo-3XX-Gold',
            '0x803' => 'Kryo-3XX-Silver',
            '0x804' => 'Kryo-4XX-Gold',
            '0x805' => 'Kryo-4XX-Silver',
            '0xc00' => 'Falkor',
            '0xc01' => 'Saphira',
        ], 'Qualcomm'],
        '0x53' => [[
            '0x001' => 'exynos-m1',
            '0x002' => 'exynos-m3',
            '0x003' => 'exynos-m4',
            '0x004' => 'exynos-m5',
        ], 'Samsung'],
        '0x56' => [[
            '0x131' => 'Feroceon-88FR131',
            '0x581' => 'PJ4/PJ4b',
            '0x584' => 'PJ4B-MP',
        ], 'Marvell'],
        '0x61' => [[
            '0x000' => 'Swift',
            '0x001' => 'Cyclone',
            '0x002' => 'Typhoon',
            '0x003' => 'Typhoon/Capri',
            '0x004' => 'Twister',
            '0x005' => 'Twister/Elba/Malta',
            '0x006' => 'Hurricane',
            '0x007' => 'Hurricane/Myst',
            '0x008' => 'Monsoon',
            '0x009' => 'Mistral',
            '0x00b' => 'Vortex',
            '0x00c' => 'Tempest',
            '0x00f' => 'Tempest-M9',
            '0x010' => 'Vortex/Aruba',
            '0x011' => 'Tempest/Aruba',
            '0x012' => 'Lightning',
            '0x013' => 'Thunder',
            '0x020' => 'Icestorm-A14',
            '0x021' => 'Firestorm-A14',
            '0x022' => 'Icestorm-M1',
            '0x023' => 'Firestorm-M1',
            '0x024' => 'Icestorm-M1-Pro',
            '0x025' => 'Firestorm-M1-Pro',
            '0x026' => 'Thunder-M10',
            '0x028' => 'Icestorm-M1-Max',
            '0x029' => 'Firestorm-M1-Max',
            '0x030' => 'Blizzard-A15',
            '0x031' => 'Avalanche-A15',
            '0x032' => 'Blizzard-M2',
            '0x033' => 'Avalanche-M2',
            '0x034' => 'Blizzard-M2-Pro',
            '0x035' => 'Avalanche-M2-Pro',
            '0x036' => 'Sawtooth-A16',
            '0x037' => 'Everest-A16',
            '0x038' => 'Blizzard-M2-Max',
            '0x039' => 'Avalanche-M2-Max',
        ], 'Apple'],
        '0x66' => [[
            '0x526' => 'FA526',
            '0x626' => 'FA626',
        ], 'Faraday'],
        '0x69' => [[
            '0x200' => 'i80200',
            '0x210' => 'PXA250A',
            '0x212' => 'PXA210A',
            '0x242' => 'i80321-400',
            '0x243' => 'i80321-600',
            '0x290' => 'PXA250B/PXA26x',
            '0x292' => 'PXA210B',
            '0x2c2' => 'i80321-400-B0',
            '0x2c3' => 'i80321-600-B0',
            '0x2d0' => 'PXA250C/PXA255/PXA26x',
            '0x2d2' => 'PXA210C',
            '0x411' => 'PXA27x',
            '0x41c' => 'IPX425-533',
            '0x41d' => 'IPX425-400',
            '0x41f' => 'IPX425-266',
            '0x682' => 'PXA32x',
            '0x683' => 'PXA930/PXA935',
            '0x688' => 'PXA30x',
            '0x689' => 'PXA31x',
            '0xb11' => 'SA1110',
            '0xc12' => 'IPX1200',
        ], 'Intel'],
        '0x6d' => [[
            '0xd49' => 'Azure-Cobalt-100',
        ], 'Microsoft'],
        '0x70' => [[
            '0x303' => 'FTC310',
            '0x660' => 'FTC660',
            '0x661' => 'FTC661',
            '0x662' => 'FTC662',
            '0x663' => 'FTC663',
            '0x664' => 'FTC664',
            '0x862' => 'FTC862',
        ], 'Phytium'],
        '0xc0' => [[
            '0xac3' => 'Ampere-1',
            '0xac4' => 'Ampere-1a',
        ], 'Ampere'],
    ];
    public static function getLoadAvg()
    {
        if (UtilsApi::isWin()) {
            return [0, 0, 0];
        }
        return array_map(function ($load) {
            return (float) \sprintf('%.2f', $load);
        }, sys_getloadavg());
    }
    public static function isArm($content)
    {
        return false !== stripos($content, 'CPU architecture');
    }
    public static function match($content, $search)
    {
        preg_match_all("/{$search}\\s*:\\s*(.+)/i", $content, $matches);
        return 2 === \count($matches) ? $matches[1] : [];
    }
    public static function getArmCpu($content)
    {
        $searchImplementer = self::match($content, 'CPU implementer');
        $implementer = \count($searchImplementer) ? $searchImplementer[0] : '';
        $implementer = isset(self::$HW_IMPLEMENTER[$implementer]) ? self::$HW_IMPLEMENTER[$implementer] : '';
        if ( ! $implementer) {
            return [];
        }
        $searchPart = self::match($content, 'CPU part');
        $part = \count($searchPart) ? $searchPart[0] : '';
        if ( ! $part) {
            return [$implementer];
        }
        $parts = $implementer[0];
        $partName = isset($parts[$part]) ? " {$parts[$part]}" : '';
        // features
        $searchFeatures = self::match($content, 'Features');
        $features = \count($searchFeatures) ? " ({$searchFeatures[0]})" : '';
        return ["{$implementer[1]}{$partName}{$features}"];
    }
    public static function getCores()
    {
        $filePath = '/proc/cpuinfo';
        error_reporting(0);
        if ( ! is_readable($filePath)) {
            error_reporting(\E_ALL);
            return 0;
        }
        $content = file_get_contents($filePath);
        if ( ! $content) {
            return 0;
        }
        if (self::isArm($content)) {
            $cores = substr_count($content, 'processor');
            if ( ! $cores) {
                return 0;
            }
        }
        return \count(self::match($content, 'cpu cores')) ?: substr_count($content, 'vendor_id');
    }
    public static function getModel()
    {
        $filePath = '/proc/cpuinfo';
        error_reporting(0);
        if ( ! is_readable($filePath)) {
            error_reporting(\E_ALL);
            return '';
        }
        $content = file_get_contents($filePath);
        if ( ! $content) {
            return '';
        }
        if (self::isArm($content)) {
            $cores = substr_count($content, 'processor');
            if ( ! $cores) {
                return '';
            }
            return "{$cores} x " . implode(' / ', array_filter(self::getArmCpu($content)));
        }
        // cpu cores
        $cores = self::getCores();
        if ( ! $cores) {
            return '';
        }
        // cpu model name
        $searchModelName = self::match($content, 'model name');
        // cpu MHz
        $searchMHz = self::match($content, 'cpu MHz');
        // cache size
        $searchCache = self::match($content, 'cache size');
        return implode(' / ', array_filter([
            \count($searchModelName) ? $searchModelName[0] : '',
            \count($searchMHz) ? "{$searchMHz[0]}MHz" : '',
            \count($searchCache) ? "{$searchCache[0]} cache" : '',
            "{$cores} core(s)",
        ]));
    }
    public static function getWinUsage()
    {
        static $prev = null;
        $usage = [];
        // com
        if (class_exists('COM')) {
            // need help
            $wmi = new COM('Winmgmts://');
            $server = $wmi->execquery('SELECT LoadPercentage FROM Win32_Processor');
            $total = 0;
            foreach ($server as $cpu) {
                $total += (int) $cpu->loadpercentage;
            }
            $total = (float) $total / \count($server);
            $usage['idle'] = 100 - $total;
            $usage['user'] = $total;
        // exec
        } else {
            if ( ! \function_exists('exec')) {
                return;
            }
            $p = [];
            exec('wmic cpu get LoadPercentage', $p);
            if (isset($p[1])) {
                $percent = (int) $p[1];
                $usage['idle'] = 100 - $percent;
                $usage['user'] = $percent;
            }
        }
        return $usage;
    }
    public static function getLinuxUsage()
    {
        static $prev = null;
        $statFile = '/proc/stat';
        error_reporting(0);
        if ( ! is_readable($statFile)) {
            error_reporting(\E_ALL);
            return [
                'usage' => 0,
                'user' => 0,
                'sys' => 0,
                'idle' => 100,
            ];
        }
        $stats1 = self::parseLinuxCpuStats($statFile);
        if (null === $stats1) {
            return [
                'usage' => 0,
                'user' => 0,
                'sys' => 0,
                'idle' => 100,
            ];
        }
        if (null === $prev) {
            $prev = $stats1;
            sleep(1);
            $stats1 = self::parseLinuxCpuStats($statFile);
        }
        $stats2 = self::parseLinuxCpuStats($statFile);
        $idle = $stats2['idle'] - $prev['idle'];
        $sys = $stats2['sys'] - $prev['sys'];
        $user = $stats2['user'] - $prev['user'];
        return [
            'usage' => round(100 * ($user + $sys) / ($user + $sys + $idle)),
            'idle' => $idle,
            'user' => $user,
            'sys' => $sys,
        ];
    }
    public static function getUsage()
    {
        return self::getLinuxUsage();
        // static $cpu = null;
        // if (null !== $cpu) {
        //     return $cpu;
        // }
        // if (UtilsApi::isWin()) {
        //     $cpu = self::getWinUsage();
        //     return $cpu;
        // }
    }
    private static function parseLinuxCpuStats($statFile)
    {
        $lines = file($statFile, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);
        if ( ! $lines) {
            return;
        }
        $line = $lines[0];
        if (0 !== mb_strpos($line, 'cpu ')) {
            return;
        }
        $items = explode(' ', preg_replace('/\\s+/', ' ', $line));
        array_shift($items);
        $items = array_map('intval', $items);
        $total = array_sum($items);
        return [
            'usage' => round(100 * ($total - $items[3]) / $total),
            'user' => (int) $items[0],
            'sys' => (int) $items[2],
            'idle' => (int) $items[3],
        ];
    }
}
namespace InnStudio\Prober\Components\ServerBenchmark;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
class ServerBenchmarkApi
{
    public static function getTmpRecorderPath()
    {
        return sys_get_temp_dir() . \DIRECTORY_SEPARATOR . 'xproberBenchmarkCool';
    }
    public static function setRecorder(array $data)
    {
        return (bool) file_put_contents(self::getTmpRecorderPath(), json_encode(array_merge(self::getRecorder(), $data)));
    }
    public static function setExpired()
    {
        return (bool) self::setRecorder([
            'expired' => (int) $_SERVER['REQUEST_TIME'] + self::cooldown(),
        ]);
    }
    public static function setIsRunning($isRunning)
    {
        return (bool) self::setRecorder([
            'isRunning' => true === (bool) $isRunning ? 1 : 0,
        ]);
    }
    public static function isRunning()
    {
        $recorder = self::getRecorder();
        return isset($recorder['isRunning']) ? 1 === (int) $recorder['isRunning'] : false;
    }
    public static function getRemainingSeconds()
    {
        $recorder = self::getRecorder();
        $expired = isset($recorder['expired']) ? (int) $recorder['expired'] : 0;
        if ( ! $expired) {
            return 0;
        }
        return $expired > (int) $_SERVER['REQUEST_TIME'] ? $expired - (int) $_SERVER['REQUEST_TIME'] : 0;
    }
    public static function getPointsByTime($time)
    {
        return pow(10, 3) - (int) ($time * pow(10, 3));
    }
    public static function getCpuPoints()
    {
        $data = 'inn-studio.com';
        $hash = ['md5', 'sha512', 'sha256', 'crc32'];
        $start = microtime(true);
        $i = 0;
        while (microtime(true) - $start < .5) {
            foreach ($hash as $v) {
                hash($v, $data);
            }
            ++$i;
        }
        return $i;
    }
    public static function getWritePoints()
    {
        $tmpDir = sys_get_temp_dir();
        if ( ! is_writable($tmpDir)) {
            return 0;
        }
        $i = 0;
        $start = microtime(true);
        while (microtime(true) - $start < .5) {
            $filePath = "{$tmpDir}/innStudioWriteBenchmark:{$i}";
            clearstatcache(true, $filePath);
            file_put_contents($filePath, $filePath);
            unlink($filePath);
            ++$i;
        }
        return $i;
    }
    public static function getReadPoints()
    {
        $tmpDir = sys_get_temp_dir();
        error_reporting(0);
        if ( ! is_readable($tmpDir)) {
            error_reporting(\E_ALL);
            return 0;
        }
        $i = 0;
        $start = microtime(true);
        $filePath = "{$tmpDir}/innStudioIoBenchmark";
        if ( ! file_exists($filePath)) {
            file_put_contents($filePath, 'innStudioReadBenchmark');
        }
        while (microtime(true) - $start < .5) {
            clearstatcache(true, $filePath);
            file_get_contents($filePath);
            ++$i;
        }
        return $i;
    }
    public static function getPoints()
    {
        return [
            'cpu' => self::getMedian([
                self::getCpuPoints(),
                self::getCpuPoints(),
                self::getCpuPoints(),
            ]),
            'write' => self::getMedian([
                self::getWritePoints(),
                self::getWritePoints(),
                self::getWritePoints(),
            ]),
            'read' => self::getMedian([
                self::getReadPoints(),
                self::getReadPoints(),
                self::getReadPoints(),
            ]),
        ];
    }
    private static function cooldown()
    {
        return (int) UserConfigApi::get('serverBenchmarkCd') ?: 60;
    }
    private static function getRecorder()
    {
        $path = self::getTmpRecorderPath();
        $defaults = [
            'expired' => 0,
            'running' => 0,
        ];
        error_reporting(0);
        if ( ! is_readable($path)) {
            error_reporting(\E_ALL);
            return $defaults;
        }
        $data = (string) file_get_contents($path);
        if ( ! $data) {
            return $defaults;
        }
        $data = json_decode($data, true);
        if ( ! $data) {
            return $defaults;
        }
        return array_merge($defaults, $data);
    }
    private static function getMedian(array $arr)
    {
        $count = \count($arr);
        sort($arr);
        $mid = floor(($count - 1) / 2);
        return ($arr[$mid] + $arr[$mid + 1 - $count % 2]) / 2;
    }
}
namespace InnStudio\Prober\Components\ServerBenchmark;
class ServerBenchmarkConstants
{
    const ID = 'serverBenchmark';
}
namespace InnStudio\Prober\Components\ServerBenchmark;
final class ServerBenchmarkDelay
{
    public function delay()
    {
        while (ServerBenchmarkApi::isRunning()) {
            sleep(2);
        }
    }
}
namespace InnStudio\Prober\Components\ServerBenchmark;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class ServerBenchmarkPerformanceAction
{
    public function render($action)
    {
        if ('benchmarkPerformance' !== $action) {
            return;
        }
        if (UserConfigApi::isDisabled('myServerBenchmark')) {
            (new RestResponse())
                ->setStatus(StatusCode::FORBIDDEN)
                ->end();
        }
        $this->renderMarks();
    }
    private function renderMarks()
    {
        set_time_limit(0);
        $remainingSeconds = ServerBenchmarkApi::getRemainingSeconds();
        $response = new RestResponse();
        if ($remainingSeconds) {
            $response
                ->setStatus(StatusCode::TOO_MANY_REQUESTS)
                ->setData([
                    'seconds' => $remainingSeconds,
                ])
                ->end();
        }
        ServerBenchmarkApi::setExpired();
        ServerBenchmarkApi::setIsRunning(true);
        // start benchmark
        $marks = ServerBenchmarkApi::getPoints();
        // end benchmark
        ServerBenchmarkApi::setIsRunning(false);
        $response
            ->setData([
                'marks' => $marks,
            ])
            ->end();
    }
}
namespace InnStudio\Prober\Components\ServerBenchmark;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class ServerBenchmarkPoll
{
    public function render()
    {
        $id = ServerBenchmarkConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => true,
        ];
    }
}
namespace InnStudio\Prober\Components\ServerBenchmark;
use InnStudio\Prober\Components\Bootstrap\Bootstrap;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
final class ServerBenchmarkServersAction
{
    public function render($action)
    {
        if ('benchmarkServers' !== $action) {
            return;
        }
        $reponse = new RestResponse();
        if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            $reponse
                ->setData($this->getDevItems())
                ->end();
        }
        foreach (ConfigApi::$config['BENCHMARKS_URLS'] as $url) {
            $curl = curl_init($url);
            curl_setopt($curl, \CURLOPT_RETURNTRANSFER, true);
            $data = (string) curl_exec($curl);
            curl_close($curl);
            if ( ! $data) {
                continue;
            }
            $json = json_decode($data, true);
            if ( ! $json) {
                continue;
            }
            $reponse
                ->setData($json)
                ->end();
        }
        $reponse
            ->setStatus(StatusCode::NO_CONTENT)
            ->end();
    }
    private function getDevItems()
    {
        $path = Bootstrap::$dir . '/benchmarks.json';
        if ( ! file_exists($path)) {
            return [];
        }
        $data = file_get_contents($path);
        if ( ! $data) {
            return [];
        }
        $items = json_decode($data, true);
        if ( ! $items) {
            return [];
        }
        return $items;
    }
}
namespace InnStudio\Prober\Components\WindowConfig;
use InnStudio\Prober\Components\Utils\UtilsNetwork;
class WindowConfigApi
{
    public static function getConfig()
    {
        return [
            'IS_DEV' => false,
            'AUTHORIZATION' => UtilsNetwork::getAuthorization(),
        ];
    }
    public static function getGlobalConfig()
    {
        $config = json_encode(self::getConfig(), \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
        return <<<HTML
<script>
window['GLOBAL_CONFIG'] = {$config};
</script>
HTML;
    }
}
namespace InnStudio\Prober\Components\BrowserBenchmark;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;
final class BrowserBenchmarkPoll
{
    public function render()
    {
        $id = BrowserBenchmarkConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        return [
            $id => true,
        ];
    }
}
namespace InnStudio\Prober\Components\BrowserBenchmark;
class BrowserBenchmarkConstants
{
    const ID = 'browserBenchmark';
}new \InnStudio\Prober\Components\Bootstrap\Bootstrap(__DIR__);