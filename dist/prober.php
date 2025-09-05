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
            'BrowserBenchmark\\BrowserBenchmarkBrowsersAction',
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
@charset "UTF-8";:root{--x-max-width: 1680px;--x-radius: .5rem;--x-fg: hsl(0, 0%, 20%);--x-bg: hsl(0, 0%, 97%);--x-text-font-family: Verdana, Geneva, Tahoma, sans-serif;--x-code-font-family: monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New";--x-app-border-color: var(--x-fg);--x-app-bg: var(--x-bg);--x-star-me-fg: var(--x-bg);--x-star-me-bg: var(--x-fg);--x-star-me-hover-fg: hsl(0, 0%, 100%);--x-star-me-hover-bg: var(--x-fg);--x-star-me-border-color: linear-gradient(90deg, transparent, hsl(0, 0%, 100%), transparent);--x-status-ok-fg: hsl(0, 0%, 100%);--x-status-ok-bg: linear-gradient(hsl(120, 100%, 30%), hsl(120, 100%, 45%));--x-status-error-fg: hsl(0, 0%, 100%);--x-status-error-bg: linear-gradient(hsl(0, 0%, 50%), hsl(0, 0%, 73%));--x-network-node-fg: var(--x-fg);--x-network-node-bg: hsla(132, 4%, 23%, .1);--x-network-node-border-color: var(--x-card-split-color);--x-network-node-row-bg: linear-gradient(to right, transparent, hsla(0, 0%, 100%, .5), transparent)}:root{--x-gutter: 1rem;--x-gutter-sm: .5rem}@media (prefers-color-scheme: dark){:root{--x-fg: hsl(0, 0%, 80%);--x-bg: hsl(0, 0%, 0%);--x-app-border-color: var(--x-bg);--x-app-bg: hsl(0, 0%, 13%);--x-star-me-fg: var(--x-fg);--x-star-me-bg: var(--x-bg);--x-star-me-hover-fg: hsl(0, 0%, 100%);--x-star-me-hover-bg: var(--x-bg);--x-star-me-border-color: linear-gradient(90deg, transparent, hsl(0, 0%, 100%), transparent);--x-status-ok-fg: hsl(0, 0%, 100%);--x-status-ok-bg: linear-gradient(hsl(120, 100%, 20%), hsl(120, 100%, 25%));--x-status-error-fg: hsl(0, 0%, 100%);--x-status-error-bg: linear-gradient(hsl(0, 0%, 27%), hsl(0, 0%, 33%));--x-network-node-fg: var(--x-fg);--x-network-node-bg: hsla(0, 0%, 100%, .05);--x-network-node-border-color: var(--x-card-split-color);--x-network-node-row-bg: var(--x-card-bg-hover)}}:root{--x-footer-bg: hsl(0 0% 0% / .05);--x-footer-fg: hsl(0 0% 0% / .5)}@media (prefers-color-scheme: dark){:root{--x-footer-bg: hsl(0 0% 100% / .1);--x-footer-fg: hsl(0 0% 100% / .5)}}._main_17cch_12{width:100%;color:var(--x-footer-fg);text-align:center;word-break:normal}._main_17cch_12 a,._main_17cch_12 a:hover{color:var(--x-footer-fg)}:root{--x-header-fg: hsl(0 0% 0% / .9);--x-header-bg: transparent;--x-header-link-bg: hsl(0 0% 0% / .1);--x-header-link-bg-hover: hsl(0 0% 0% / .15)}@media (prefers-color-scheme: dark){:root{--x-header-fg: hsl(0 0% 100% / .9);--x-header-bg: hsl(0 0% 100% / .1);--x-header-link-bg: hsl(0 0% 100% / .1);--x-header-link-bg-hover: hsl(0 0% 100% / .15)}}._main_1jpdc_16{display:flex;justify-content:center;padding-top:var(--x-gutter)}:root{--x-link-fg: hsl(0 0% 95% / .95);--x-link-bg: hsl(0 0% 15% / .95);--x-link-bg-hover: hsl(0 0% 20% / .95);--x-link-bg-active: hsl(0 0% 25% / .95)}@media (prefers-color-scheme: dark){:root{--x-link-fg: hsl(0 0% 100% / .95);--x-link-bg: hsl(0 0% 10% / .95);--x-link-bg-hover: hsl(0 0% 15% / .95);--x-link-bg-active: hsl(0 0% 20% / .95)}}._main_p5526_16{display:flex;align-items:center;gap:var(--x-gutter-sm);cursor:pointer;border:none;border-radius:10rem;background:var(--x-link-bg);padding:var(--x-gutter-sm) var(--x-gutter);color:var(--x-link-fg);text-decoration:none}._main_p5526_16:hover{background:var(--x-link-bg-hover);color:var(--x-link-fg);text-decoration:none}._main_p5526_16:active{background:var(--x-link-bg-active);color:var(--x-link-fg);text-decoration:none}._main_1k8xz_1{font-weight:400;font-size:1rem}._name_1k8xz_6{font-weight:700}._version_1k8xz_10{opacity:.75;font-weight:400;font-size:.8em}:root{--x-toast-fg: hsl(0 0% 100% / .95);--x-toast-bg: hsl(0 0% 0% / .75)}@media (prefers-color-scheme: dark){:root{--x-toast-fg: hsl(0 0% 100% / .95);--x-toast-bg: hsl(0 0% 100% / .15)}}._main_17sik_12{position:fixed;bottom:4rem;left:50%;transform:translate(-50%);z-index:20;backdrop-filter:blur(5px);cursor:pointer;border:none;border-radius:var(--x-gutter);background:var(--x-toast-bg);padding:var(--x-gutter);max-width:80vw;color:var(--x-toast-fg);text-align:justify}:root{--x-fg: hsl(0 0% 10%);--x-body-fg: hsl(0 0% 10%);--x-body-bg: hsl(0 0% 90%)}@media (prefers-color-scheme: dark){:root{--x-fg: hsl(0 0% 90%);--x-body-fg: hsl(0 0% 90%);--x-body-bg: hsl(0 0% 0%)}}*{box-sizing:border-box;margin:0;padding:0;word-break:break-word}html{scroll-behavior:smooth;font-size:85%}body{display:grid;place-content:safe center;vertical-align:middle;gap:var(--x-gutter);margin:0;background:var(--x-body-bg);padding:0;color:var(--x-body-fg);line-height:1.5;font-family:var(--x-code-font-family)}a{cursor:pointer;color:var(--x-fg);text-decoration:none}a:hover,a:active{color:var(--x-fg);text-decoration:underline}._container_30sck_1{display:grid;gap:var(--x-gutter);max-width:1200px}:root{--x-card-legend-arrow-fg: var(--x-card-legend-fg);--x-card-legend-arrow-bg-hover: hsl(0 0% 0% / .05);--x-card-legend-arrow-bg-active: hsl(0 0% 0% / .1)}@media (prefers-color-scheme: dark){:root{--x-card-legend-arrow-fg: var(--x-card-legend-fg);--x-card-legend-arrow-bg-hover: hsl(0 0% 100% / .05);--x-card-legend-arrow-bg-active: hsl(0 0% 100% / .1)}}._arrow_1qtu9_14{display:flex;align-items:center;cursor:pointer;border:none;border-radius:var(--x-radius);background:transparent;padding:var(--x-gutter-sm);color:var(--x-card-legend-arrow-fg)}._arrow_1qtu9_14:hover{background:var(--x-card-legend-arrow-bg-hover);color:var(--x-card-legend-arrow-fg)}._arrow_1qtu9_14:active{background:var(--x-card-legend-arrow-bg-active);color:var(--x-card-legend-arrow-fg)}._arrow_1qtu9_14[data-disabled],._arrow_1qtu9_14[data-disabled]:hover{opacity:.5;cursor:not-allowed}._arrow_1qtu9_14 svg{width:1rem;height:1rem}:root{--x-module-bg: hsl(0 0% 0% / .95);--x-module-header-bg: hsl(0 0% 100% / .75);--x-module-header-fg: hsl(0 0% 0%);--x-module-header-title-fg: hsl(0 0% 0% / .7);--x-module-header-title-bg: hsl(0 0% 0% / .1);--x-module-body-bg: var(--x-module-header-bg);--x-module-box-shadow: hsla(0 0% 20% .3) 0px -1px 0px hsl(0 0% 100%) 0px 1px 0px inset, hsla(0 0% 20% .3) 0px -1px 0px inset hsl(0 0% 100%) 0px 1px 0px}@media (prefers-color-scheme: dark){:root{--x-module-bg: hsl(0 0% 15% / .95);--x-module-header-bg: hsl(0 0% 100% / .1);--x-module-header-fg: hsl(0 0% 100% / .7);--x-module-header-title-fg: hsl(0 0% 100% / .7);--x-module-header-title-bg: hsl(0 0% 100% / .1);--x-module-body-bg: var(--x-module-header-bg);--x-module-box-shadow: 0px 0px 0px 1px hsl(0 0% 0%) inset}}._main_60fl9_23{position:relative;flex-grow:1;scroll-margin-top:0}._header_60fl9_29{display:flex;align-items:center;border-radius:var(--x-radius) var(--x-radius) 0 0;background:var(--x-module-header-bg);padding:1px;width:fit-content;color:var(--x-module-header-fg);font-size:1rem;white-space:nowrap}._title_60fl9_41{font-weight:400}._body_60fl9_45{display:grid;gap:var(--x-gutter-sm);border-radius:0 var(--x-radius) var(--x-radius) var(--x-radius);background:var(--x-module-body-bg);padding:var(--x-gutter)}:root{--x-card-des-fg: var(--x-fg);--x-card-des-bg: hsl(0 0% 100% / .1);--x-card-des-accent: hsl(0 0% 0% / .5)}@media (prefers-color-scheme: dark){:root{--x-card-des-fg: var(--x-fg);--x-card-des-bg: hsl(0 0% 100% / .1);--x-card-des-accent: hsl(209, 100%, 63%)}}._main_1hf64_14{display:grid;border-radius:var(--x-radius);color:var(--x-card-des-fg);font-family:var(--x-text-font-family);list-style-type:none}._item_1hf64_22{display:flex;align-items:center;gap:var(--x-gutter-sm)}._item_1hf64_22:before{border-radius:var(--x-radius);background:var(--x-card-des-accent);width:2px;height:50%;content:""}:root{--x-placeholder-bg: linear-gradient(to right, hsl(0 0% 0% / .1) 46%, hsl(0 0% 0% / .15) 50%, hsl(0 0% 0% / .1) 54%) 50% 50%}@media (prefers-color-scheme: dark){:root{--x-placeholder-bg: linear-gradient( to right, hsl(0 0% 100% / .1) 46%, hsl(0 0% 100% / .15) 50%, hsl(0 0% 100% / .1) 54% ) 50% 50%}}@keyframes _animation_vvbro_1{0%{transform:translate3d(-30%,0,0)}to{transform:translate3d(30%,0,0)}}._main_vvbro_25{position:relative;border-radius:var(--x-radius);overflow:hidden}._main_vvbro_25:before{position:absolute;inset:0 0 0 50%;z-index:1;animation:_animation_vvbro_1 1s linear infinite;margin-left:-250%;background:var(--x-placeholder-bg);width:500%;pointer-events:none;content:" "}:root{--x-error-fg: hsl(0 100% 50%);--x-error-bg: hsl(0 100% 30%);--x-error-icon-fg: hsl(0 100% 50%);--x-error-icon-bg: hsl(0 100% 97%)}@media (prefers-color-scheme: dark){:root{--x-error-fg: hsl(0 0% 100% / .9);--x-error-bg: hsl(0, 100%, 50%);--x-error-icon-fg: var(--x-error-bg);--x-error-icon-bg: hsl(0 0% 100% / .5)}}._main_1ogv8_16{display:flex;position:relative;align-items:center;gap:var(--x-gutter-sm);border-radius:var(--x-radius);color:var(--x-error-fg);font-family:var(--x-text-font-family)}._main_1ogv8_16:before{border-radius:var(--x-radius);background:var(--x-error-bg);width:2px;height:50%;content:""}:root{--x-benchmark-ruby-bg: hsl(0 0% 0% / .05);--x-benchmark-ruby-bg-hover: hsl(0 0% 0% / .05)}@media (prefers-color-scheme: dark){:root{--x-benchmark-ruby-bg: hsl(0 0% 100% / .05);--x-benchmark-ruby-bg-hover: hsl(0 0% 100% / .1)}}._main_1j8ow_12 rt{opacity:.5;font-weight:400;font-size:1rem}._main_1j8ow_12[data-is-result]{font-weight:700}._main_1p71d_13{display:grid;gap:var(--x-gutter-sm);border-radius:var(--x-radius);background:var(--x-server-benchmark-bg);padding:var(--x-gutter-sm);text-align:center}._header_1p71d_22{display:flex;justify-content:center;align-items:center}._link_1p71d_28{opacity:.75;cursor:pointer;border:none;border-radius:var(--x-radius);background:none;padding:0 var(--x-gutter-sm)}._link_1p71d_28:hover,._link_1p71d_28:active{opacity:1;background:var(--x-server-benchmark-link-bg);text-decoration:none}._link_1p71d_28 svg{width:1rem;height:1rem}._marks_1p71d_46{display:flex;justify-content:center;align-items:center;gap:var(--x-gutter-sm);cursor:pointer;border:none;border-radius:var(--x-radius);background:transparent;color:var(--x-server-benchmark-link-fg);font-size:1.25rem}._marks_1p71d_46:hover{background:var(--x-server-benchmark-link-bg)}._sign_1p71d_62{opacity:.5}:root{--x-meter-height: 2px;--x-meter-bar-bg: hsl(0 0% 0% / .1);--x-meter-value-bg: hsl(120 100% 40%);--x-meter-value-optimum-bg: hsl(120 100% 30%);--x-meter-value-suboptimum-bg: hsl(36 77% 64%);--x-meter-value-even-less-good-bg: hsl(12 100% 39%)}@media (prefers-color-scheme: dark){:root{--x-meter-bar-bg: hsl(0 0% 100% / .1);--x-meter-value-optimum-bg: hsl(120 100% 30%);--x-meter-value-suboptimum-bg: hsl(36 77% 54%);--x-meter-value-even-less-good-bg: hsl(12 100% 39%)}}._main_1isor_18{display:grid;grid-template-columns:1fr auto;grid-template-areas:"x-meter-name x-meter-percent" "x-meter-name x-meter-overview" "x-meter-core x-meter-core ";gap:var(--x-gutter-sm)}._percent_1isor_25{grid-area:x-meter-percent;text-align:right}._name_1isor_30{display:flex;grid-area:x-meter-name;align-items:center;border:none;background:none;color:var(--x-bg-fg);font-weight:700;text-align:center}._nameText_1isor_41{display:-webkit-box;-webkit-box-orient:vertical;max-width:15rem;-webkit-line-clamp:2;overflow:hidden}._overview_1isor_49{grid-area:x-meter-overview}._core_1isor_53{grid-area:x-meter-core;background:none;width:100%;height:var(--x-meter-height)}._core_1isor_53::-webkit-meter-bar{border-radius:10rem;background:var(--x-meter-bar-bg);height:var(--x-meter-height)}._core_1isor_53::-webkit-meter-optimum-value{border-radius:10rem;background:var(--x-meter-value-optimum-bg)}._core_1isor_53::-webkit-meter-suboptimum-value{border-radius:10rem;background:var(--x-meter-value-suboptimum-bg)}._core_1isor_53::-webkit-meter-even-less-good-value{border-radius:10rem;background:var(--x-meter-value-even-less-good-bg)}._btn_9r2wb_1{display:block}._browsersLoading_9r2wb_5{display:grid;justify-content:center;align-items:center;height:5rem}._browsers_9r2wb_5{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter-sm)}:root{--x-button-fg: var(--x-fg);--x-button-bg: hsl(0 0% 0% / .1);--x-button-fg-hover: var(--x-fg);--x-button-bg-hover: hsl(0 0% 0% / .15);--x-button-fg-active: var(--x-fg);--x-button-bg-active: hsl(0 0% 0% / .2)}@media (prefers-color-scheme: dark){:root{--x-button-fg: var(--x-fg);--x-button-bg: hsl(0 0% 100% / .1);--x-button-fg-hover: var(--x-fg);--x-button-bg-hover: hsl(0 0% 100% / .15);--x-button-fg-active: var(--x-fg);--x-button-bg-active: hsl(0 0% 100% / .2)}}@keyframes _spin_1shxn_1{to{transform:rotate(360deg)}}._button_1shxn_25{display:flex;align-items:center;gap:.25em;cursor:pointer;border:none;border-radius:var(--x-radius);background:var(--x-button-bg);padding:0 var(--x-gutter-sm);color:var(--x-button-fg);font-family:var(--x-text-font-family);text-decoration:none}._button_1shxn_25:hover{background:var(--x-button-bg-hover);color:var(--x-button-fg-hover);text-decoration:none}._button_1shxn_25:active{background:var(--x-button-bg-active);color:var(--x-button-fg-active);text-decoration:none}._icon_1shxn_49{display:grid;place-content:center;aspect-ratio:1/1;width:1rem}._icon_1shxn_49 svg{width:1rem;height:1rem}._icon_1shxn_49[data-status=loading]{animation:_spin_1shxn_1 1s linear infinite}:root{--x-nav-fg: hsl(0 0% 100% / .9);--x-nav-bg: hsl(0 0% 15% / .95);--x-nav-bg-hover: hsl(0 0% 100% / .05);--x-nav-bg-active: hsl(0 0% 100% / .1);--x-nav-border-color: hsl(0 0% 100% / .05)}@media (prefers-color-scheme: dark){:root{--x-nav-fg: hsl(0 0% 95% / .95);--x-nav-bg: hsl(0 0% 20% / .95);--x-nav-bg-hover: hsl(0 0% 25% / .95);--x-nav-bg-active: hsl(0 0% 30% / .95);--x-nav-border-color: hsl(0 0% 100% / .05)}}._main_1ygx7_18{display:flex;position:sticky;bottom:0;justify-content:flex-start;align-items:center;z-index:10;background:var(--x-nav-bg);overflow-x:auto}@media (min-width: 768px){._main_1ygx7_18{justify-content:center;border-radius:var(--x-radius) var(--x-radius) 0 0}}._link_1ygx7_35{position:relative;border-right:1px solid var(--x-nav-border-color);padding:var(--x-gutter);color:var(--x-nav-fg);white-space:nowrap}._link_1ygx7_35:hover{background:var(--x-nav-bg-hover);color:var(--x-nav-fg);text-decoration:none}._link_1ygx7_35:focus,._link_1ygx7_35:active,._link_1ygx7_35[data-active]{background:var(--x-nav-bg-active);color:var(--x-nav-fg);text-decoration:none}._link_1ygx7_35:last-child{border-right:0}:root{--x-card-group-label-fg: var(--x-fg);--x-card-group-split-color: hsl(0 0% 0% / .1);--x-card-group-bg-hover: hsl(0 0% 0% / .05)}@media (prefers-color-scheme: dark){:root{--x-card-group-label-fg: var(--x-fg);--x-card-group-split-color: hsl(0 0% 100% / .1);--x-card-group-bg-hover: hsl(0 0% 100% / .05)}}._main_11zmy_14{display:grid;grid-template-columns:minmax(var(--min-width),var(--max-width)) 1fr;gap:var(--x-gutter-sm);border-radius:var(--x-radius)}._main_11zmy_14:hover{background:var(--x-card-group-bg-hover)}._label_11zmy_24{color:var(--x-card-group-label-fg);font-family:var(--x-text-font-family);text-align:right;word-break:normal}._label_11zmy_24:after{content:":"}._content_11zmy_34{display:flex;flex-wrap:wrap;align-items:flex-start;gap:var(--x-gutter-sm)}._main_z8p86_1{display:grid;grid-template-columns:repeat(auto-fill,minmax(var(--min-width),1fr));gap:var(--x-gutter-sm)}._main_xo4z4_2{display:inline-flex;border-radius:var(--x-radius);align-items:center;justify-content:center;font-family:Arial Black,sans-serif;font-weight:bolder;min-width:2em;padding:0 .5rem;white-space:nowrap;cursor:pointer;text-shadow:0 1px 1px #000}._main_xo4z4_2:active{transform:scale3d(.95,.95,1)}._main_xo4z4_2[data-ok]{background:var(--x-status-ok-bg);color:var(--x-status-ok-fg)}._main_xo4z4_2[data-error]{background:var(--x-status-error-bg);color:var(--x-status-error-fg)}._main_xo4z4_2[data-ok][data-icon]:before{content:"✓"}._main_xo4z4_2[data-error][data-icon]:before{content:"×"}._main_b4lx8_1{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter)}._main_mc2kq_1{display:grid;gap:var(--x-gutter-sm)}._container_1i47d_2{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter)}._item_1i47d_8{display:grid}._id_1i47d_12{text-align:center;text-decoration:underline}._idRow_1i47d_17{display:grid;align-items:center}._dataContainer_1i47d_22{display:flex;justify-content:center;align-items:center;text-align:center}._data_1i47d_22{flex:0 0 50%}._data_1i47d_22[data-rx]{color:var(--x-network-stats-rx-fg)}._data_1i47d_22[data-tx]{color:var(--x-network-stats-tx-fg)}._rate_1i47d_39{font-family:Arial Black,sans-serif}._rate_1i47d_39:before{margin-right:.5rem}._rateRx_1i47d_46:before{content:"▼"}._rateTx_1i47d_50:before{content:"▲"}:root{--x-network-stats-tx-fg: hsl(23 100% 38%);--x-network-stats-tx-bg: hsl(23 100% 38% / .1);--x-network-stats-rx-fg: hsl(120 100% 23%);--x-network-stats-rx-bg: hsl(120 100% 23% / .1)}@media (prefers-color-scheme: dark){:root{--x-network-stats-tx-fg: hsl(23 100% 58%);--x-network-stats-tx-bg: hsl(23 100% 58% /.15);--x-network-stats-rx-fg: hsl(120 100% 43%);--x-network-stats-rx-bg: hsl(120 100% 43% / .15)}}._main_1cyw0_17{display:grid;grid-template-areas:"network-stats-item-id network-stats-item-id" "network-stats-item-rx network-stats-item-tx";gap:1px;font-family:Arial Black,sans-serif}._id_1cyw0_24{grid-area:network-stats-item-id;text-align:center}._type_1cyw0_29:before{opacity:.5;content:"▼";font-size:1rem}._rx_1cyw0_35,._tx_1cyw0_36{display:grid;position:relative;grid-area:network-stats-item-rx;border-radius:var(--x-radius) 0 0 var(--x-radius);background:var(--x-network-stats-rx-bg);padding:var(--x-gutter-sm);color:var(--x-network-stats-rx-fg);text-align:center}._tx_1cyw0_36{grid-area:network-stats-item-tx;border-radius:0 var(--x-radius) var(--x-radius) 0;background:var(--x-network-stats-tx-bg);color:var(--x-network-stats-tx-fg)}._tx_1cyw0_36 ._type_1cyw0_29:before{content:"▲"}._rateRx_1cyw0_57,._rateTx_1cyw0_58{font-weight:700;font-size:1.5rem}._main_zmhfm_1{display:grid;grid-template-columns:repeat(auto-fill,minmax(20rem,1fr));gap:var(--x-gutter)}._groupId_zmhfm_7{display:block;margin-bottom:calc(var(--x-gutter) * .5);text-align:center;text-decoration:underline}._groupId_zmhfm_7:hover{text-decoration:none}._group_zmhfm_7{margin-bottom:calc(var(--x-gutter) * .5)}._groupMsg_zmhfm_21{display:flex;justify-content:center}._groupNetworks_zmhfm_26{margin-bottom:var(--x-gutter);border-radius:var(--x-radius);background:var(--x-network-node-bg);padding:var(--x-gutter);color:var(--x-network-node-fg)}._groupNetwork_zmhfm_26{margin-bottom:calc(var(--x-gutter) * .5);border-bottom:1px dashed var(--x-network-node-border-color);padding-bottom:calc(var(--x-gutter) * .5)}._groupNetwork_zmhfm_26:last-child{margin-bottom:0;border-bottom:0;padding-bottom:0}._groupNetwork_zmhfm_26:hover{background:var(--x-network-node-row-bg)}:root{--x-sys-load-fg: var(--x-fg);--x-sys-load-bg: transparent;--x-sys-load-interval-bg: hsl(0 0% 0% / .1)}@media (prefers-color-scheme: dark){:root{--x-sys-load-fg: var(--x-fg);--x-sys-load-interval-bg: hsl(0 0% 100% / .1)}}._main_1xqpo_13{display:grid;grid-template-columns:1fr auto;grid-template-areas:"x-server-stats-system-load-label x-server-stats-system-load-usage" "x-server-stats-system-load-label x-server-stats-system-load-group" "x-server-stats-system-load-meter x-server-stats-system-load-meter";gap:var(--x-gutter-sm)}._label_1xqpo_20{display:grid;grid-area:x-server-stats-system-load-label;align-items:center;font-weight:700}._meter_1xqpo_27{display:grid;grid-template-areas:"x-meter-core";grid-area:x-server-stats-system-load-meter}._usage_1xqpo_33{grid-area:x-server-stats-system-load-usage;text-align:right}._group_1xqpo_38{display:flex;grid-area:x-server-stats-system-load-group;align-items:center;gap:var(--x-gutter-sm)}._groupItem_1xqpo_45{border-radius:var(--x-radius);background:var(--x-sys-load-interval-bg);padding:0 var(--x-gutter);color:var(--x-sys-load-fg);font-weight:700;font-family:Arial Black,sans-serif,monospace}._sysLoad_mqy5s_1{display:flex;gap:var(--x-gutter-sm)}._main_66xvd_1{display:grid;grid-template-columns:1fr auto;grid-template-areas:"x-nodes-usage-label x-nodes-usage-label" "x-nodes-usage-overview x-nodes-usage-percent" "x-nodes-usage-meter x-nodes-usage-meter";column-gap:var(--x-gutter-sm);row-gap:0;gap:var(--x-gutter-sm)}._meter_66xvd_10{display:flex;grid-area:x-nodes-usage-meter;height:var(--x-meter-height)}._label_66xvd_16{grid-area:x-nodes-usage-label}._overview_66xvd_20{display:flex;grid-area:x-nodes-usage-overview;gap:var(--x-gutter-sm)}._chart_66xvd_26{display:none;grid-area:x-nodes-usage-chart}._percent_66xvd_31{grid-area:x-nodes-usage-percent}._main_1gdd5_1{display:grid;gap:var(--x-gutter-sm);container-type:inline-size;max-height:calc(100px + var(--x-gutter-sm));overflow-y:auto;overscroll-behavior:contain;scroll-snap-type:y mandatory;scrollbar-color:hsla(0,0%,50%,.5) transparent}._item_1gdd5_12{scroll-snap-align:start}._main_mc2kq_1,._main_18siw_1{display:grid;gap:var(--x-gutter-sm)}._name_18siw_6{text-align:center}._loading_18siw_10{display:grid;place-content:center center;height:10rem}:root{--x-search-fg: var(--x-fg);--x-search-bg: hsl(0 0% 0% / .1);--x-search-bg-hover: hsl(0 0% 0% / .15);--x-search-bg-active: hsl(0 0% 0% / .2)}@media (prefers-color-scheme: dark){:root{--x-search-fg: var(--x-fg);--x-search-bg: hsl(0 0% 100% / .1);--x-search-bg-hover: hsl(0 0% 100% / .15);--x-search-bg-active: hsl(0 0% 100% / .2)}}._main_uj7jp_16{border-radius:var(--x-radius);background:var(--x-search-bg);padding:calc(var(--x-gutter-sm) * .5) var(--x-gutter-sm);color:var(--x-search-fg);font-family:monospace}._main_uj7jp_16:hover{background:var(--x-search-bg-hover);text-decoration:none}._main_uj7jp_16:active{background:var(--x-search-bg-active)}:root{--x-ping-result-scrollbar-bg: hsl(0 0% 0% / .5);--x-ping-item-bg: hsl(0 0% 0% / .1)}@media (prefers-color-scheme: dark){:root{--x-ping-result-scrollbar-bg: hsl(0 0% 100% / .5);--x-ping-item-bg: hsl(0 0% 100% / .1)}}._itemContainer_y6c35_12{display:grid;grid-template-columns:repeat(auto-fill,minmax(5rem,1fr));grid-auto-flow:row;flex-grow:1;gap:.15em;border-radius:var(--x-radius);background:var(--x-ping-item-bg);padding:var(--x-gutter-sm) var(--x-gutter);height:7rem;overflow-y:auto;scrollbar-color:var(--x-ping-result-scrollbar-bg) transparent;list-style-type:none}._resultContainer_y6c35_27{display:grid;flex-grow:1;gap:var(--x-gutter-sm)}._result_y6c35_27{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center}._btn_1dtle_1{display:block}._serversLoading_1dtle_5{display:grid;justify-content:center;align-items:center;height:5rem}._servers_1dtle_5{display:grid;grid-template-columns:repeat(auto-fill,minmax(25rem,1fr));gap:var(--x-gutter-sm)}._main_fajqi_1{display:flex}:root{--x-server-benchmark-bg: transparent;--x-server-benchmark-link-bg: hsl(0 0% 0% / .05);--x-server-benchmark-link-fg: hsl(0 0% 0% / .95)}@media (prefers-color-scheme: dark){:root{--x-server-benchmark-link-fg: hsl(0 0% 100% / .95);--x-server-benchmark-link-bg: hsl(0 0% 100% / .05)}}._main_1e6oe_13{display:grid;gap:var(--x-gutter-sm);border-radius:var(--x-radius);background:var(--x-server-benchmark-bg);padding:var(--x-gutter-sm);text-align:center}._header_1e6oe_22{display:flex;justify-content:center;align-items:center}._link_1e6oe_28{opacity:.75;cursor:pointer;border:none;border-radius:var(--x-radius);background:none;padding:0 var(--x-gutter-sm)}._link_1e6oe_28:hover,._link_1e6oe_28:active{opacity:1;background:var(--x-server-benchmark-link-bg);text-decoration:none}._link_1e6oe_28 svg{width:1rem;height:1rem}._marks_1e6oe_46{display:flex;justify-content:center;align-items:center;gap:var(--x-gutter-sm);cursor:pointer;border:none;border-radius:var(--x-radius);background-color:transparent;color:var(--x-server-benchmark-link-fg);font-size:1.25rem}._marks_1e6oe_46:hover{background:var(--x-server-benchmark-link-bg)}._sign_1e6oe_62{opacity:.5}._main_raw5t_1{display:grid;gap:var(--x-gutter-sm)}._modules_raw5t_6{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:var(--x-gutter)}@keyframes _spin_nuyl9_1{to{transform:rotate(360deg)}}._main_nuyl9_6{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:.5em;height:100svh}._main_nuyl9_6:before{animation:_spin_nuyl9_1 1s linear infinite;box-sizing:border-box;border:1px solid var(--x-button-bg);border-top-color:var(--x-button-fg);border-radius:50%;width:16px;height:16px;content:""}

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
(function(vl){typeof define=="function"&&define.amd?define(vl):vl()})(function(){"use strict";function vl(i){return i&&i.__esModule&&Object.prototype.hasOwnProperty.call(i,"default")?i.default:i}var td={exports:{}},yl={},nd={exports:{}},gl={exports:{}};gl.exports;var Mv;function K1(){return Mv||(Mv=1,function(i,l){(function(){function u(m,w){Object.defineProperty(h.prototype,m,{get:function(){console.warn("%s(...) is deprecated in plain JavaScript React classes. %s",w[0],w[1])}})}function s(m){return m===null||typeof m!="object"?null:(m=yi&&m[yi]||m["@@iterator"],typeof m=="function"?m:null)}function f(m,w){m=(m=m.constructor)&&(m.displayName||m.name)||"ReactClass";var $=m+"."+w;Hl[$]||(console.error("Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",w,m),Hl[$]=!0)}function h(m,w,$){this.props=m,this.context=w,this.refs=Ll,this.updater=$||wo}function p(){}function b(m,w,$){this.props=m,this.context=w,this.refs=Ll,this.updater=$||wo}function y(m){return""+m}function z(m){try{y(m);var w=!1}catch{w=!0}if(w){w=console;var $=w.error,G=typeof Symbol=="function"&&Symbol.toStringTag&&m[Symbol.toStringTag]||m.constructor.name||"Object";return $.call(w,"The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",G),y(m)}}function D(m){if(m==null)return null;if(typeof m=="function")return m.$$typeof===rh?null:m.displayName||m.name||null;if(typeof m=="string")return m;switch(m){case L:return"Fragment";case ee:return"Profiler";case ae:return"StrictMode";case Ce:return"Suspense";case vi:return"SuspenseList";case Nn:return"Activity"}if(typeof m=="object")switch(typeof m.tag=="number"&&console.error("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),m.$$typeof){case De:return"Portal";case Ke:return(m.displayName||"Context")+".Provider";case Le:return(m._context.displayName||"Context")+".Consumer";case ct:var w=m.render;return m=m.displayName,m||(m=w.displayName||w.name||"",m=m!==""?"ForwardRef("+m+")":"ForwardRef"),m;case et:return w=m.displayName||null,w!==null?w:D(m.type)||"Memo";case At:w=m._payload,m=m._init;try{return D(m(w))}catch{}}return null}function x(m){if(m===L)return"<>";if(typeof m=="object"&&m!==null&&m.$$typeof===At)return"<...>";try{var w=D(m);return w?"<"+w+">":"<...>"}catch{return"<...>"}}function O(){var m=_e.A;return m===null?null:m.getOwner()}function H(){return Error("react-stack-top-frame")}function Y(m){if(gi.call(m,"key")){var w=Object.getOwnPropertyDescriptor(m,"key").get;if(w&&w.isReactWarning)return!1}return m.key!==void 0}function F(m,w){function $(){Ao||(Ao=!0,console.error("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",w))}$.isReactWarning=!0,Object.defineProperty(m,"key",{get:$,configurable:!0})}function ne(){var m=D(this.type);return Bl[m]||(Bl[m]=!0,console.error("Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release.")),m=this.props.ref,m!==void 0?m:null}function ie(m,w,$,G,J,ge,pe,xe){return $=ge.ref,m={$$typeof:oe,type:m,key:w,props:ge,_owner:J},($!==void 0?$:null)!==null?Object.defineProperty(m,"ref",{enumerable:!1,get:ne}):Object.defineProperty(m,"ref",{enumerable:!1,value:null}),m._store={},Object.defineProperty(m._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:0}),Object.defineProperty(m,"_debugInfo",{configurable:!1,enumerable:!1,writable:!0,value:null}),Object.defineProperty(m,"_debugStack",{configurable:!1,enumerable:!1,writable:!0,value:pe}),Object.defineProperty(m,"_debugTask",{configurable:!1,enumerable:!1,writable:!0,value:xe}),Object.freeze&&(Object.freeze(m.props),Object.freeze(m)),m}function Qe(m,w){return w=ie(m.type,w,void 0,void 0,m._owner,m.props,m._debugStack,m._debugTask),m._store&&(w._store.validated=m._store.validated),w}function Pe(m){return typeof m=="object"&&m!==null&&m.$$typeof===oe}function ke(m){var w={"=":"=0",":":"=2"};return"$"+m.replace(/[=:]/g,function($){return w[$]})}function Ze(m,w){return typeof m=="object"&&m!==null&&m.key!=null?(z(m.key),ke(""+m.key)):w.toString(36)}function Fe(){}function Ne(m){switch(m.status){case"fulfilled":return m.value;case"rejected":throw m.reason;default:switch(typeof m.status=="string"?m.then(Fe,Fe):(m.status="pending",m.then(function(w){m.status==="pending"&&(m.status="fulfilled",m.value=w)},function(w){m.status==="pending"&&(m.status="rejected",m.reason=w)})),m.status){case"fulfilled":return m.value;case"rejected":throw m.reason}}throw m}function Vt(m,w,$,G,J){var ge=typeof m;(ge==="undefined"||ge==="boolean")&&(m=null);var pe=!1;if(m===null)pe=!0;else switch(ge){case"bigint":case"string":case"number":pe=!0;break;case"object":switch(m.$$typeof){case oe:case De:pe=!0;break;case At:return pe=m._init,Vt(pe(m._payload),w,$,G,J)}}if(pe){pe=m,J=J(pe);var xe=G===""?"."+Ze(pe,0):G;return xo(J)?($="",xe!=null&&($=xe.replace(Pl,"$&/")+"/"),Vt(J,w,$,"",function(_t){return _t})):J!=null&&(Pe(J)&&(J.key!=null&&(pe&&pe.key===J.key||z(J.key)),$=Qe(J,$+(J.key==null||pe&&pe.key===J.key?"":(""+J.key).replace(Pl,"$&/")+"/")+xe),G!==""&&pe!=null&&Pe(pe)&&pe.key==null&&pe._store&&!pe._store.validated&&($._store.validated=2),J=$),w.push(J)),1}if(pe=0,xe=G===""?".":G+":",xo(m))for(var me=0;me<m.length;me++)G=m[me],ge=xe+Ze(G,me),pe+=Vt(G,w,$,ge,J);else if(me=s(m),typeof me=="function")for(me===m.entries&&($l||console.warn("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),$l=!0),m=me.call(m),me=0;!(G=m.next()).done;)G=G.value,ge=xe+Ze(G,me++),pe+=Vt(G,w,$,ge,J);else if(ge==="object"){if(typeof m.then=="function")return Vt(Ne(m),w,$,G,J);throw w=String(m),Error("Objects are not valid as a React child (found: "+(w==="[object Object]"?"object with keys {"+Object.keys(m).join(", ")+"}":w)+"). If you meant to render a collection of children, use an array instead.")}return pe}function te(m,w,$){if(m==null)return m;var G=[],J=0;return Vt(m,G,"","",function(ge){return w.call($,ge,J++)}),G}function at(m){if(m._status===-1){var w=m._result;w=w(),w.then(function($){(m._status===0||m._status===-1)&&(m._status=1,m._result=$)},function($){(m._status===0||m._status===-1)&&(m._status=2,m._result=$)}),m._status===-1&&(m._status=0,m._result=w)}if(m._status===1)return w=m._result,w===void 0&&console.error(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))

Did you accidentally put curly braces around the import?`,w),"default"in w||console.error(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))`,w),w.default;throw m._result}function ue(){var m=_e.H;return m===null&&console.error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`),m}function Re(){}function ut(m){if(mr===null)try{var w=("require"+Math.random()).slice(0,7);mr=(i&&i[w]).call(i,"timers").setImmediate}catch{mr=function(G){ql===!1&&(ql=!0,typeof MessageChannel>"u"&&console.error("This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."));var J=new MessageChannel;J.port1.onmessage=G,J.port2.postMessage(void 0)}}return mr(m)}function xt(m){return 1<m.length&&typeof AggregateError=="function"?new AggregateError(m):m[0]}function Nt(m,w){w!==Ro-1&&console.error("You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "),Ro=w}function q(m,w,$){var G=_e.actQueue;if(G!==null)if(G.length!==0)try{se(G),ut(function(){return q(m,w,$)});return}catch(J){_e.thrownErrors.push(J)}else _e.actQueue=null;0<_e.thrownErrors.length?(G=xt(_e.thrownErrors),_e.thrownErrors.length=0,$(G)):w(m)}function se(m){if(!la){la=!0;var w=0;try{for(;w<m.length;w++){var $=m[w];do{_e.didUsePromise=!1;var G=$(!1);if(G!==null){if(_e.didUsePromise){m[w]=$,m.splice(0,w);return}$=G}else break}while(!0)}m.length=0}catch(J){m.splice(0,w+1),_e.thrownErrors.push(J)}finally{la=!1}}}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var oe=Symbol.for("react.transitional.element"),De=Symbol.for("react.portal"),L=Symbol.for("react.fragment"),ae=Symbol.for("react.strict_mode"),ee=Symbol.for("react.profiler"),Le=Symbol.for("react.consumer"),Ke=Symbol.for("react.context"),ct=Symbol.for("react.forward_ref"),Ce=Symbol.for("react.suspense"),vi=Symbol.for("react.suspense_list"),et=Symbol.for("react.memo"),At=Symbol.for("react.lazy"),Nn=Symbol.for("react.activity"),yi=Symbol.iterator,Hl={},wo={isMounted:function(){return!1},enqueueForceUpdate:function(m){f(m,"forceUpdate")},enqueueReplaceState:function(m){f(m,"replaceState")},enqueueSetState:function(m){f(m,"setState")}},ac=Object.assign,Ll={};Object.freeze(Ll),h.prototype.isReactComponent={},h.prototype.setState=function(m,w){if(typeof m!="object"&&typeof m!="function"&&m!=null)throw Error("takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,m,w,"setState")},h.prototype.forceUpdate=function(m){this.updater.enqueueForceUpdate(this,m,"forceUpdate")};var ft={isMounted:["isMounted","Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."],replaceState:["replaceState","Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."]},Pa;for(Pa in ft)ft.hasOwnProperty(Pa)&&u(Pa,ft[Pa]);p.prototype=h.prototype,ft=b.prototype=new p,ft.constructor=b,ac(ft,h.prototype),ft.isPureReactComponent=!0;var xo=Array.isArray,rh=Symbol.for("react.client.reference"),_e={H:null,A:null,T:null,S:null,V:null,actQueue:null,isBatchingLegacy:!1,didScheduleLegacyUpdate:!1,didUsePromise:!1,thrownErrors:[],getCurrentStack:null,recentlyCreatedOwnerStacks:0},gi=Object.prototype.hasOwnProperty,hr=console.createTask?console.createTask:function(){return null};ft={react_stack_bottom_frame:function(m){return m()}};var Ao,oc,Bl={},ic=ft.react_stack_bottom_frame.bind(ft,H)(),Vl=hr(x(H)),$l=!1,Pl=/\/+/g,pr=typeof reportError=="function"?reportError:function(m){if(typeof window=="object"&&typeof window.ErrorEvent=="function"){var w=new window.ErrorEvent("error",{bubbles:!0,cancelable:!0,message:typeof m=="object"&&m!==null&&typeof m.message=="string"?String(m.message):String(m),error:m});if(!window.dispatchEvent(w))return}else if(typeof process=="object"&&typeof process.emit=="function"){process.emit("uncaughtException",m);return}console.error(m)},ql=!1,mr=null,Ro=0,ra=!1,la=!1,zo=typeof queueMicrotask=="function"?function(m){queueMicrotask(function(){return queueMicrotask(m)})}:ut;ft=Object.freeze({__proto__:null,c:function(m){return ue().useMemoCache(m)}}),l.Children={map:te,forEach:function(m,w,$){te(m,function(){w.apply(this,arguments)},$)},count:function(m){var w=0;return te(m,function(){w++}),w},toArray:function(m){return te(m,function(w){return w})||[]},only:function(m){if(!Pe(m))throw Error("React.Children.only expected to receive a single React element child.");return m}},l.Component=h,l.Fragment=L,l.Profiler=ee,l.PureComponent=b,l.StrictMode=ae,l.Suspense=Ce,l.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE=_e,l.__COMPILER_RUNTIME=ft,l.act=function(m){var w=_e.actQueue,$=Ro;Ro++;var G=_e.actQueue=w!==null?w:[],J=!1;try{var ge=m()}catch(me){_e.thrownErrors.push(me)}if(0<_e.thrownErrors.length)throw Nt(w,$),m=xt(_e.thrownErrors),_e.thrownErrors.length=0,m;if(ge!==null&&typeof ge=="object"&&typeof ge.then=="function"){var pe=ge;return zo(function(){J||ra||(ra=!0,console.error("You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"))}),{then:function(me,_t){J=!0,pe.then(function(qa){if(Nt(w,$),$===0){try{se(G),ut(function(){return q(qa,me,_t)})}catch(sh){_e.thrownErrors.push(sh)}if(0<_e.thrownErrors.length){var lh=xt(_e.thrownErrors);_e.thrownErrors.length=0,_t(lh)}}else me(qa)},function(qa){Nt(w,$),0<_e.thrownErrors.length&&(qa=xt(_e.thrownErrors),_e.thrownErrors.length=0),_t(qa)})}}}var xe=ge;if(Nt(w,$),$===0&&(se(G),G.length!==0&&zo(function(){J||ra||(ra=!0,console.error("A component suspended inside an `act` scope, but the `act` call was not awaited. When testing React components that depend on asynchronous data, you must await the result:\n\nawait act(() => ...)"))}),_e.actQueue=null),0<_e.thrownErrors.length)throw m=xt(_e.thrownErrors),_e.thrownErrors.length=0,m;return{then:function(me,_t){J=!0,$===0?(_e.actQueue=G,ut(function(){return q(xe,me,_t)})):me(xe)}}},l.cache=function(m){return function(){return m.apply(null,arguments)}},l.captureOwnerStack=function(){var m=_e.getCurrentStack;return m===null?null:m()},l.cloneElement=function(m,w,$){if(m==null)throw Error("The argument must be a React element, but you passed "+m+".");var G=ac({},m.props),J=m.key,ge=m._owner;if(w!=null){var pe;e:{if(gi.call(w,"ref")&&(pe=Object.getOwnPropertyDescriptor(w,"ref").get)&&pe.isReactWarning){pe=!1;break e}pe=w.ref!==void 0}pe&&(ge=O()),Y(w)&&(z(w.key),J=""+w.key);for(xe in w)!gi.call(w,xe)||xe==="key"||xe==="__self"||xe==="__source"||xe==="ref"&&w.ref===void 0||(G[xe]=w[xe])}var xe=arguments.length-2;if(xe===1)G.children=$;else if(1<xe){pe=Array(xe);for(var me=0;me<xe;me++)pe[me]=arguments[me+2];G.children=pe}for(G=ie(m.type,J,void 0,void 0,ge,G,m._debugStack,m._debugTask),J=2;J<arguments.length;J++)ge=arguments[J],Pe(ge)&&ge._store&&(ge._store.validated=1);return G},l.createContext=function(m){return m={$$typeof:Ke,_currentValue:m,_currentValue2:m,_threadCount:0,Provider:null,Consumer:null},m.Provider=m,m.Consumer={$$typeof:Le,_context:m},m._currentRenderer=null,m._currentRenderer2=null,m},l.createElement=function(m,w,$){for(var G=2;G<arguments.length;G++){var J=arguments[G];Pe(J)&&J._store&&(J._store.validated=1)}if(G={},J=null,w!=null)for(me in oc||!("__self"in w)||"key"in w||(oc=!0,console.warn("Your app (or one of its dependencies) is using an outdated JSX transform. Update to the modern JSX transform for faster performance: https://react.dev/link/new-jsx-transform")),Y(w)&&(z(w.key),J=""+w.key),w)gi.call(w,me)&&me!=="key"&&me!=="__self"&&me!=="__source"&&(G[me]=w[me]);var ge=arguments.length-2;if(ge===1)G.children=$;else if(1<ge){for(var pe=Array(ge),xe=0;xe<ge;xe++)pe[xe]=arguments[xe+2];Object.freeze&&Object.freeze(pe),G.children=pe}if(m&&m.defaultProps)for(me in ge=m.defaultProps,ge)G[me]===void 0&&(G[me]=ge[me]);J&&F(G,typeof m=="function"?m.displayName||m.name||"Unknown":m);var me=1e4>_e.recentlyCreatedOwnerStacks++;return ie(m,J,void 0,void 0,O(),G,me?Error("react-stack-top-frame"):ic,me?hr(x(m)):Vl)},l.createRef=function(){var m={current:null};return Object.seal(m),m},l.forwardRef=function(m){m!=null&&m.$$typeof===et?console.error("forwardRef requires a render function but received a `memo` component. Instead of forwardRef(memo(...)), use memo(forwardRef(...))."):typeof m!="function"?console.error("forwardRef requires a render function but was given %s.",m===null?"null":typeof m):m.length!==0&&m.length!==2&&console.error("forwardRef render functions accept exactly two parameters: props and ref. %s",m.length===1?"Did you forget to use the ref parameter?":"Any additional parameter will be undefined."),m!=null&&m.defaultProps!=null&&console.error("forwardRef render functions do not support defaultProps. Did you accidentally pass a React component?");var w={$$typeof:ct,render:m},$;return Object.defineProperty(w,"displayName",{enumerable:!1,configurable:!0,get:function(){return $},set:function(G){$=G,m.name||m.displayName||(Object.defineProperty(m,"name",{value:G}),m.displayName=G)}}),w},l.isValidElement=Pe,l.lazy=function(m){return{$$typeof:At,_payload:{_status:-1,_result:m},_init:at}},l.memo=function(m,w){m==null&&console.error("memo: The first argument must be a component. Instead received: %s",m===null?"null":typeof m),w={$$typeof:et,type:m,compare:w===void 0?null:w};var $;return Object.defineProperty(w,"displayName",{enumerable:!1,configurable:!0,get:function(){return $},set:function(G){$=G,m.name||m.displayName||(Object.defineProperty(m,"name",{value:G}),m.displayName=G)}}),w},l.startTransition=function(m){var w=_e.T,$={};_e.T=$,$._updatedFibers=new Set;try{var G=m(),J=_e.S;J!==null&&J($,G),typeof G=="object"&&G!==null&&typeof G.then=="function"&&G.then(Re,pr)}catch(ge){pr(ge)}finally{w===null&&$._updatedFibers&&(m=$._updatedFibers.size,$._updatedFibers.clear(),10<m&&console.warn("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table.")),_e.T=w}},l.unstable_useCacheRefresh=function(){return ue().useCacheRefresh()},l.use=function(m){return ue().use(m)},l.useActionState=function(m,w,$){return ue().useActionState(m,w,$)},l.useCallback=function(m,w){return ue().useCallback(m,w)},l.useContext=function(m){var w=ue();return m.$$typeof===Le&&console.error("Calling useContext(Context.Consumer) is not supported and will cause bugs. Did you mean to call useContext(Context) instead?"),w.useContext(m)},l.useDebugValue=function(m,w){return ue().useDebugValue(m,w)},l.useDeferredValue=function(m,w){return ue().useDeferredValue(m,w)},l.useEffect=function(m,w,$){m==null&&console.warn("React Hook useEffect requires an effect callback. Did you forget to pass a callback to the hook?");var G=ue();if(typeof $=="function")throw Error("useEffect CRUD overload is not enabled in this build of React.");return G.useEffect(m,w)},l.useId=function(){return ue().useId()},l.useImperativeHandle=function(m,w,$){return ue().useImperativeHandle(m,w,$)},l.useInsertionEffect=function(m,w){return m==null&&console.warn("React Hook useInsertionEffect requires an effect callback. Did you forget to pass a callback to the hook?"),ue().useInsertionEffect(m,w)},l.useLayoutEffect=function(m,w){return m==null&&console.warn("React Hook useLayoutEffect requires an effect callback. Did you forget to pass a callback to the hook?"),ue().useLayoutEffect(m,w)},l.useMemo=function(m,w){return ue().useMemo(m,w)},l.useOptimistic=function(m,w){return ue().useOptimistic(m,w)},l.useReducer=function(m,w,$){return ue().useReducer(m,w,$)},l.useRef=function(m){return ue().useRef(m)},l.useState=function(m){return ue().useState(m)},l.useSyncExternalStore=function(m,w,$){return ue().useSyncExternalStore(m,w,$)},l.useTransition=function(){return ue().useTransition()},l.version="19.1.1",typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())})()}(gl,gl.exports)),gl.exports}var jv;function bl(){return jv||(jv=1,nd.exports=K1()),nd.exports}var Uv;function J1(){if(Uv)return yl;Uv=1;return function(){function i(L){if(L==null)return null;if(typeof L=="function")return L.$$typeof===at?null:L.displayName||L.name||null;if(typeof L=="string")return L;switch(L){case F:return"Fragment";case ie:return"Profiler";case ne:return"StrictMode";case Ze:return"Suspense";case Fe:return"SuspenseList";case te:return"Activity"}if(typeof L=="object")switch(typeof L.tag=="number"&&console.error("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),L.$$typeof){case Y:return"Portal";case Pe:return(L.displayName||"Context")+".Provider";case Qe:return(L._context.displayName||"Context")+".Consumer";case ke:var ae=L.render;return L=L.displayName,L||(L=ae.displayName||ae.name||"",L=L!==""?"ForwardRef("+L+")":"ForwardRef"),L;case Ne:return ae=L.displayName||null,ae!==null?ae:i(L.type)||"Memo";case Vt:ae=L._payload,L=L._init;try{return i(L(ae))}catch{}}return null}function l(L){return""+L}function u(L){try{l(L);var ae=!1}catch{ae=!0}if(ae){ae=console;var ee=ae.error,Le=typeof Symbol=="function"&&Symbol.toStringTag&&L[Symbol.toStringTag]||L.constructor.name||"Object";return ee.call(ae,"The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",Le),l(L)}}function s(L){if(L===F)return"<>";if(typeof L=="object"&&L!==null&&L.$$typeof===Vt)return"<...>";try{var ae=i(L);return ae?"<"+ae+">":"<...>"}catch{return"<...>"}}function f(){var L=ue.A;return L===null?null:L.getOwner()}function h(){return Error("react-stack-top-frame")}function p(L){if(Re.call(L,"key")){var ae=Object.getOwnPropertyDescriptor(L,"key").get;if(ae&&ae.isReactWarning)return!1}return L.key!==void 0}function b(L,ae){function ee(){Nt||(Nt=!0,console.error("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://react.dev/link/special-props)",ae))}ee.isReactWarning=!0,Object.defineProperty(L,"key",{get:ee,configurable:!0})}function y(){var L=i(this.type);return q[L]||(q[L]=!0,console.error("Accessing element.ref was removed in React 19. ref is now a regular prop. It will be removed from the JSX Element type in a future release.")),L=this.props.ref,L!==void 0?L:null}function z(L,ae,ee,Le,Ke,ct,Ce,vi){return ee=ct.ref,L={$$typeof:H,type:L,key:ae,props:ct,_owner:Ke},(ee!==void 0?ee:null)!==null?Object.defineProperty(L,"ref",{enumerable:!1,get:y}):Object.defineProperty(L,"ref",{enumerable:!1,value:null}),L._store={},Object.defineProperty(L._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:0}),Object.defineProperty(L,"_debugInfo",{configurable:!1,enumerable:!1,writable:!0,value:null}),Object.defineProperty(L,"_debugStack",{configurable:!1,enumerable:!1,writable:!0,value:Ce}),Object.defineProperty(L,"_debugTask",{configurable:!1,enumerable:!1,writable:!0,value:vi}),Object.freeze&&(Object.freeze(L.props),Object.freeze(L)),L}function D(L,ae,ee,Le,Ke,ct,Ce,vi){var et=ae.children;if(et!==void 0)if(Le)if(ut(et)){for(Le=0;Le<et.length;Le++)x(et[Le]);Object.freeze&&Object.freeze(et)}else console.error("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");else x(et);if(Re.call(ae,"key")){et=i(L);var At=Object.keys(ae).filter(function(yi){return yi!=="key"});Le=0<At.length?"{key: someKey, "+At.join(": ..., ")+": ...}":"{key: someKey}",De[et+Le]||(At=0<At.length?"{"+At.join(": ..., ")+": ...}":"{}",console.error(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,Le,et,At,et),De[et+Le]=!0)}if(et=null,ee!==void 0&&(u(ee),et=""+ee),p(ae)&&(u(ae.key),et=""+ae.key),"key"in ae){ee={};for(var Nn in ae)Nn!=="key"&&(ee[Nn]=ae[Nn])}else ee=ae;return et&&b(ee,typeof L=="function"?L.displayName||L.name||"Unknown":L),z(L,et,ct,Ke,f(),ee,Ce,vi)}function x(L){typeof L=="object"&&L!==null&&L.$$typeof===H&&L._store&&(L._store.validated=1)}var O=bl(),H=Symbol.for("react.transitional.element"),Y=Symbol.for("react.portal"),F=Symbol.for("react.fragment"),ne=Symbol.for("react.strict_mode"),ie=Symbol.for("react.profiler"),Qe=Symbol.for("react.consumer"),Pe=Symbol.for("react.context"),ke=Symbol.for("react.forward_ref"),Ze=Symbol.for("react.suspense"),Fe=Symbol.for("react.suspense_list"),Ne=Symbol.for("react.memo"),Vt=Symbol.for("react.lazy"),te=Symbol.for("react.activity"),at=Symbol.for("react.client.reference"),ue=O.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,Re=Object.prototype.hasOwnProperty,ut=Array.isArray,xt=console.createTask?console.createTask:function(){return null};O={react_stack_bottom_frame:function(L){return L()}};var Nt,q={},se=O.react_stack_bottom_frame.bind(O,h)(),oe=xt(s(h)),De={};yl.Fragment=F,yl.jsx=function(L,ae,ee,Le,Ke){var ct=1e4>ue.recentlyCreatedOwnerStacks++;return D(L,ae,ee,!1,Le,Ke,ct?Error("react-stack-top-frame"):se,ct?xt(s(L)):oe)},yl.jsxs=function(L,ae,ee,Le,Ke){var ct=1e4>ue.recentlyCreatedOwnerStacks++;return D(L,ae,ee,!0,Le,Ke,ct?Error("react-stack-top-frame"):se,ct?xt(s(L)):oe)}}(),yl}var kv;function W1(){return kv||(kv=1,td.exports=J1()),td.exports}var _=W1(),ad={exports:{}},od={exports:{}},id={},Nv;function F1(){return Nv||(Nv=1,function(i){(function(){function l(){if(Ze=!1,te){var q=i.unstable_now();Re=q;var se=!0;try{e:{Pe=!1,ke&&(ke=!1,Ne(at),at=-1),Qe=!0;var oe=ie;try{t:{for(p(q),ne=s(H);ne!==null&&!(ne.expirationTime>q&&y());){var De=ne.callback;if(typeof De=="function"){ne.callback=null,ie=ne.priorityLevel;var L=De(ne.expirationTime<=q);if(q=i.unstable_now(),typeof L=="function"){ne.callback=L,p(q),se=!0;break t}ne===s(H)&&f(H),p(q)}else f(H);ne=s(H)}if(ne!==null)se=!0;else{var ae=s(Y);ae!==null&&z(b,ae.startTime-q),se=!1}}break e}finally{ne=null,ie=oe,Qe=!1}se=void 0}}finally{se?ut():te=!1}}}function u(q,se){var oe=q.length;q.push(se);e:for(;0<oe;){var De=oe-1>>>1,L=q[De];if(0<h(L,se))q[De]=se,q[oe]=L,oe=De;else break e}}function s(q){return q.length===0?null:q[0]}function f(q){if(q.length===0)return null;var se=q[0],oe=q.pop();if(oe!==se){q[0]=oe;e:for(var De=0,L=q.length,ae=L>>>1;De<ae;){var ee=2*(De+1)-1,Le=q[ee],Ke=ee+1,ct=q[Ke];if(0>h(Le,oe))Ke<L&&0>h(ct,Le)?(q[De]=ct,q[Ke]=oe,De=Ke):(q[De]=Le,q[ee]=oe,De=ee);else if(Ke<L&&0>h(ct,oe))q[De]=ct,q[Ke]=oe,De=Ke;else break e}}return se}function h(q,se){var oe=q.sortIndex-se.sortIndex;return oe!==0?oe:q.id-se.id}function p(q){for(var se=s(Y);se!==null;){if(se.callback===null)f(Y);else if(se.startTime<=q)f(Y),se.sortIndex=se.expirationTime,u(H,se);else break;se=s(Y)}}function b(q){if(ke=!1,p(q),!Pe)if(s(H)!==null)Pe=!0,te||(te=!0,ut());else{var se=s(Y);se!==null&&z(b,se.startTime-q)}}function y(){return Ze?!0:!(i.unstable_now()-Re<ue)}function z(q,se){at=Fe(function(){q(i.unstable_now())},se)}if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error()),i.unstable_now=void 0,typeof performance=="object"&&typeof performance.now=="function"){var D=performance;i.unstable_now=function(){return D.now()}}else{var x=Date,O=x.now();i.unstable_now=function(){return x.now()-O}}var H=[],Y=[],F=1,ne=null,ie=3,Qe=!1,Pe=!1,ke=!1,Ze=!1,Fe=typeof setTimeout=="function"?setTimeout:null,Ne=typeof clearTimeout=="function"?clearTimeout:null,Vt=typeof setImmediate<"u"?setImmediate:null,te=!1,at=-1,ue=5,Re=-1;if(typeof Vt=="function")var ut=function(){Vt(l)};else if(typeof MessageChannel<"u"){var xt=new MessageChannel,Nt=xt.port2;xt.port1.onmessage=l,ut=function(){Nt.postMessage(null)}}else ut=function(){Fe(l,0)};i.unstable_IdlePriority=5,i.unstable_ImmediatePriority=1,i.unstable_LowPriority=4,i.unstable_NormalPriority=3,i.unstable_Profiling=null,i.unstable_UserBlockingPriority=2,i.unstable_cancelCallback=function(q){q.callback=null},i.unstable_forceFrameRate=function(q){0>q||125<q?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):ue=0<q?Math.floor(1e3/q):5},i.unstable_getCurrentPriorityLevel=function(){return ie},i.unstable_next=function(q){switch(ie){case 1:case 2:case 3:var se=3;break;default:se=ie}var oe=ie;ie=se;try{return q()}finally{ie=oe}},i.unstable_requestPaint=function(){Ze=!0},i.unstable_runWithPriority=function(q,se){switch(q){case 1:case 2:case 3:case 4:case 5:break;default:q=3}var oe=ie;ie=q;try{return se()}finally{ie=oe}},i.unstable_scheduleCallback=function(q,se,oe){var De=i.unstable_now();switch(typeof oe=="object"&&oe!==null?(oe=oe.delay,oe=typeof oe=="number"&&0<oe?De+oe:De):oe=De,q){case 1:var L=-1;break;case 2:L=250;break;case 5:L=1073741823;break;case 4:L=1e4;break;default:L=5e3}return L=oe+L,q={id:F++,callback:se,priorityLevel:q,startTime:oe,expirationTime:L,sortIndex:-1},oe>De?(q.sortIndex=oe,u(Y,q),s(H)===null&&q===s(Y)&&(ke?(Ne(at),at=-1):ke=!0,z(b,oe-De))):(q.sortIndex=L,u(H,q),Pe||Qe||(Pe=!0,te||(te=!0,ut()))),q},i.unstable_shouldYield=y,i.unstable_wrapCallback=function(q){var se=ie;return function(){var oe=ie;ie=se;try{return q.apply(this,arguments)}finally{ie=oe}}},typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())})()}(id)),id}var Hv;function eO(){return Hv||(Hv=1,od.exports=F1()),od.exports}var rd={exports:{}},kt={},Lv;function tO(){if(Lv)return kt;Lv=1;return function(){function i(){}function l(x){return""+x}function u(x,O,H){var Y=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;try{l(Y);var F=!1}catch{F=!0}return F&&(console.error("The provided key is an unsupported type %s. This value must be coerced to a string before using it here.",typeof Symbol=="function"&&Symbol.toStringTag&&Y[Symbol.toStringTag]||Y.constructor.name||"Object"),l(Y)),{$$typeof:z,key:Y==null?null:""+Y,children:x,containerInfo:O,implementation:H}}function s(x,O){if(x==="font")return"";if(typeof O=="string")return O==="use-credentials"?O:""}function f(x){return x===null?"`null`":x===void 0?"`undefined`":x===""?"an empty string":'something with type "'+typeof x+'"'}function h(x){return x===null?"`null`":x===void 0?"`undefined`":x===""?"an empty string":typeof x=="string"?JSON.stringify(x):typeof x=="number"?"`"+x+"`":'something with type "'+typeof x+'"'}function p(){var x=D.H;return x===null&&console.error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`),x}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var b=bl(),y={d:{f:i,r:function(){throw Error("Invalid form element. requestFormReset must be passed a form that was rendered by React.")},D:i,C:i,L:i,m:i,X:i,S:i,M:i},p:0,findDOMNode:null},z=Symbol.for("react.portal"),D=b.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;typeof Map=="function"&&Map.prototype!=null&&typeof Map.prototype.forEach=="function"&&typeof Set=="function"&&Set.prototype!=null&&typeof Set.prototype.clear=="function"&&typeof Set.prototype.forEach=="function"||console.error("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"),kt.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE=y,kt.createPortal=function(x,O){var H=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!O||O.nodeType!==1&&O.nodeType!==9&&O.nodeType!==11)throw Error("Target container is not a DOM element.");return u(x,O,null,H)},kt.flushSync=function(x){var O=D.T,H=y.p;try{if(D.T=null,y.p=2,x)return x()}finally{D.T=O,y.p=H,y.d.f()&&console.error("flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task.")}},kt.preconnect=function(x,O){typeof x=="string"&&x?O!=null&&typeof O!="object"?console.error("ReactDOM.preconnect(): Expected the `options` argument (second) to be an object but encountered %s instead. The only supported option at this time is `crossOrigin` which accepts a string.",h(O)):O!=null&&typeof O.crossOrigin!="string"&&console.error("ReactDOM.preconnect(): Expected the `crossOrigin` option (second argument) to be a string but encountered %s instead. Try removing this option or passing a string value instead.",f(O.crossOrigin)):console.error("ReactDOM.preconnect(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",f(x)),typeof x=="string"&&(O?(O=O.crossOrigin,O=typeof O=="string"?O==="use-credentials"?O:"":void 0):O=null,y.d.C(x,O))},kt.prefetchDNS=function(x){if(typeof x!="string"||!x)console.error("ReactDOM.prefetchDNS(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",f(x));else if(1<arguments.length){var O=arguments[1];typeof O=="object"&&O.hasOwnProperty("crossOrigin")?console.error("ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. It looks like the you are attempting to set a crossOrigin property for this DNS lookup hint. Browsers do not perform DNS queries using CORS and setting this attribute on the resource hint has no effect. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",h(O)):console.error("ReactDOM.prefetchDNS(): Expected only one argument, `href`, but encountered %s as a second argument instead. This argument is reserved for future options and is currently disallowed. Try calling ReactDOM.prefetchDNS() with just a single string argument, `href`.",h(O))}typeof x=="string"&&y.d.D(x)},kt.preinit=function(x,O){if(typeof x=="string"&&x?O==null||typeof O!="object"?console.error("ReactDOM.preinit(): Expected the `options` argument (second) to be an object with an `as` property describing the type of resource to be preinitialized but encountered %s instead.",h(O)):O.as!=="style"&&O.as!=="script"&&console.error('ReactDOM.preinit(): Expected the `as` property in the `options` argument (second) to contain a valid value describing the type of resource to be preinitialized but encountered %s instead. Valid values for `as` are "style" and "script".',h(O.as)):console.error("ReactDOM.preinit(): Expected the `href` argument (first) to be a non-empty string but encountered %s instead.",f(x)),typeof x=="string"&&O&&typeof O.as=="string"){var H=O.as,Y=s(H,O.crossOrigin),F=typeof O.integrity=="string"?O.integrity:void 0,ne=typeof O.fetchPriority=="string"?O.fetchPriority:void 0;H==="style"?y.d.S(x,typeof O.precedence=="string"?O.precedence:void 0,{crossOrigin:Y,integrity:F,fetchPriority:ne}):H==="script"&&y.d.X(x,{crossOrigin:Y,integrity:F,fetchPriority:ne,nonce:typeof O.nonce=="string"?O.nonce:void 0})}},kt.preinitModule=function(x,O){var H="";if(typeof x=="string"&&x||(H+=" The `href` argument encountered was "+f(x)+"."),O!==void 0&&typeof O!="object"?H+=" The `options` argument encountered was "+f(O)+".":O&&"as"in O&&O.as!=="script"&&(H+=" The `as` option encountered was "+h(O.as)+"."),H)console.error("ReactDOM.preinitModule(): Expected up to two arguments, a non-empty `href` string and, optionally, an `options` object with a valid `as` property.%s",H);else switch(H=O&&typeof O.as=="string"?O.as:"script",H){case"script":break;default:H=h(H),console.error('ReactDOM.preinitModule(): Currently the only supported "as" type for this function is "script" but received "%s" instead. This warning was generated for `href` "%s". In the future other module types will be supported, aligning with the import-attributes proposal. Learn more here: (https://github.com/tc39/proposal-import-attributes)',H,x)}typeof x=="string"&&(typeof O=="object"&&O!==null?(O.as==null||O.as==="script")&&(H=s(O.as,O.crossOrigin),y.d.M(x,{crossOrigin:H,integrity:typeof O.integrity=="string"?O.integrity:void 0,nonce:typeof O.nonce=="string"?O.nonce:void 0})):O==null&&y.d.M(x))},kt.preload=function(x,O){var H="";if(typeof x=="string"&&x||(H+=" The `href` argument encountered was "+f(x)+"."),O==null||typeof O!="object"?H+=" The `options` argument encountered was "+f(O)+".":typeof O.as=="string"&&O.as||(H+=" The `as` option encountered was "+f(O.as)+"."),H&&console.error('ReactDOM.preload(): Expected two arguments, a non-empty `href` string and an `options` object with an `as` property valid for a `<link rel="preload" as="..." />` tag.%s',H),typeof x=="string"&&typeof O=="object"&&O!==null&&typeof O.as=="string"){H=O.as;var Y=s(H,O.crossOrigin);y.d.L(x,H,{crossOrigin:Y,integrity:typeof O.integrity=="string"?O.integrity:void 0,nonce:typeof O.nonce=="string"?O.nonce:void 0,type:typeof O.type=="string"?O.type:void 0,fetchPriority:typeof O.fetchPriority=="string"?O.fetchPriority:void 0,referrerPolicy:typeof O.referrerPolicy=="string"?O.referrerPolicy:void 0,imageSrcSet:typeof O.imageSrcSet=="string"?O.imageSrcSet:void 0,imageSizes:typeof O.imageSizes=="string"?O.imageSizes:void 0,media:typeof O.media=="string"?O.media:void 0})}},kt.preloadModule=function(x,O){var H="";typeof x=="string"&&x||(H+=" The `href` argument encountered was "+f(x)+"."),O!==void 0&&typeof O!="object"?H+=" The `options` argument encountered was "+f(O)+".":O&&"as"in O&&typeof O.as!="string"&&(H+=" The `as` option encountered was "+f(O.as)+"."),H&&console.error('ReactDOM.preloadModule(): Expected two arguments, a non-empty `href` string and, optionally, an `options` object with an `as` property valid for a `<link rel="modulepreload" as="..." />` tag.%s',H),typeof x=="string"&&(O?(H=s(O.as,O.crossOrigin),y.d.m(x,{as:typeof O.as=="string"&&O.as!=="script"?O.as:void 0,crossOrigin:H,integrity:typeof O.integrity=="string"?O.integrity:void 0})):y.d.m(x))},kt.requestFormReset=function(x){y.d.r(x)},kt.unstable_batchedUpdates=function(x,O){return x(O)},kt.useFormState=function(x,O,H){return p().useFormState(x,O,H)},kt.useFormStatus=function(){return p().useHostTransitionStatus()},kt.version="19.1.1",typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())}(),kt}var Bv;function Vv(){return Bv||(Bv=1,rd.exports=tO()),rd.exports}var _l={},$v;function nO(){if($v)return _l;$v=1;return function(){function i(e,t){for(e=e.memoizedState;e!==null&&0<t;)e=e.next,t--;return e}function l(e,t,n,a){if(n>=t.length)return a;var o=t[n],r=Ct(e)?e.slice():be({},e);return r[o]=l(e[o],t,n+1,a),r}function u(e,t,n){if(t.length!==n.length)console.warn("copyWithRename() expects paths of the same length");else{for(var a=0;a<n.length-1;a++)if(t[a]!==n[a]){console.warn("copyWithRename() expects paths to be the same except for the deepest key");return}return s(e,t,n,0)}}function s(e,t,n,a){var o=t[a],r=Ct(e)?e.slice():be({},e);return a+1===t.length?(r[n[a]]=r[o],Ct(r)?r.splice(o,1):delete r[o]):r[o]=s(e[o],t,n,a+1),r}function f(e,t,n){var a=t[n],o=Ct(e)?e.slice():be({},e);return n+1===t.length?(Ct(o)?o.splice(a,1):delete o[a],o):(o[a]=f(e[a],t,n+1),o)}function h(){return!1}function p(){return null}function b(){}function y(){console.error("Do not call Hooks inside useEffect(...), useMemo(...), or other built-in Hooks. You can only call Hooks at the top level of your React function. For more information, see https://react.dev/link/rules-of-hooks")}function z(){console.error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().")}function D(){}function x(e){var t=[];return e.forEach(function(n){t.push(n)}),t.sort().join(", ")}function O(e,t,n,a){return new AR(e,t,n,a)}function H(e,t){e.context===Yo&&(om(e.current,2,t,e,null,null),xr())}function Y(e,t){if(Gn!==null){var n=t.staleFamilies;t=t.updatedFamilies,ms(),Oh(e.current,t,n),xr()}}function F(e){Gn=e}function ne(e){return!(!e||e.nodeType!==1&&e.nodeType!==9&&e.nodeType!==11)}function ie(e){var t=e,n=e;if(e.alternate)for(;t.return;)t=t.return;else{e=t;do t=e,(t.flags&4098)!==0&&(n=t.return),e=t.return;while(e)}return t.tag===3?n:null}function Qe(e){if(e.tag===13){var t=e.memoizedState;if(t===null&&(e=e.alternate,e!==null&&(t=e.memoizedState)),t!==null)return t.dehydrated}return null}function Pe(e){if(ie(e)!==e)throw Error("Unable to find node on an unmounted component.")}function ke(e){var t=e.alternate;if(!t){if(t=ie(e),t===null)throw Error("Unable to find node on an unmounted component.");return t!==e?null:e}for(var n=e,a=t;;){var o=n.return;if(o===null)break;var r=o.alternate;if(r===null){if(a=o.return,a!==null){n=a;continue}break}if(o.child===r.child){for(r=o.child;r;){if(r===n)return Pe(o),e;if(r===a)return Pe(o),t;r=r.sibling}throw Error("Unable to find node on an unmounted component.")}if(n.return!==a.return)n=o,a=r;else{for(var c=!1,d=o.child;d;){if(d===n){c=!0,n=o,a=r;break}if(d===a){c=!0,a=o,n=r;break}d=d.sibling}if(!c){for(d=r.child;d;){if(d===n){c=!0,n=r,a=o;break}if(d===a){c=!0,a=r,n=o;break}d=d.sibling}if(!c)throw Error("Child was not found in either parent set. This indicates a bug in React related to the return pointer. Please file an issue.")}}if(n.alternate!==a)throw Error("Return fibers should always be each others' alternates. This error is likely caused by a bug in React. Please file an issue.")}if(n.tag!==3)throw Error("Unable to find node on an unmounted component.");return n.stateNode.current===n?e:t}function Ze(e){var t=e.tag;if(t===5||t===26||t===27||t===6)return e;for(e=e.child;e!==null;){if(t=Ze(e),t!==null)return t;e=e.sibling}return null}function Fe(e){return e===null||typeof e!="object"?null:(e=gS&&e[gS]||e["@@iterator"],typeof e=="function"?e:null)}function Ne(e){if(e==null)return null;if(typeof e=="function")return e.$$typeof===qz?null:e.displayName||e.name||null;if(typeof e=="string")return e;switch(e){case jr:return"Fragment";case fm:return"Profiler";case lf:return"StrictMode";case hm:return"Suspense";case pm:return"SuspenseList";case mm:return"Activity"}if(typeof e=="object")switch(typeof e.tag=="number"&&console.error("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),e.$$typeof){case Mr:return"Portal";case Aa:return(e.displayName||"Context")+".Provider";case dm:return(e._context.displayName||"Context")+".Consumer";case Es:var t=e.render;return e=e.displayName,e||(e=t.displayName||t.name||"",e=e!==""?"ForwardRef("+e+")":"ForwardRef"),e;case sf:return t=e.displayName||null,t!==null?t:Ne(e.type)||"Memo";case Sn:t=e._payload,e=e._init;try{return Ne(e(t))}catch{}}return null}function Vt(e){return typeof e.tag=="number"?te(e):typeof e.name=="string"?e.name:null}function te(e){var t=e.type;switch(e.tag){case 31:return"Activity";case 24:return"Cache";case 9:return(t._context.displayName||"Context")+".Consumer";case 10:return(t.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return e=t.render,e=e.displayName||e.name||"",t.displayName||(e!==""?"ForwardRef("+e+")":"ForwardRef");case 7:return"Fragment";case 26:case 27:case 5:return t;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return Ne(t);case 8:return t===lf?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 14:case 15:if(typeof t=="function")return t.displayName||t.name||null;if(typeof t=="string")return t;break;case 29:if(t=e._debugInfo,t!=null){for(var n=t.length-1;0<=n;n--)if(typeof t[n].name=="string")return t[n].name}if(e.return!==null)return te(e.return)}return null}function at(e){return{current:e}}function ue(e,t){0>Fa?console.error("Unexpected pop."):(t!==ym[Fa]&&console.error("Unexpected Fiber popped."),e.current=vm[Fa],vm[Fa]=null,ym[Fa]=null,Fa--)}function Re(e,t,n){Fa++,vm[Fa]=e.current,ym[Fa]=n,e.current=t}function ut(e){return e===null&&console.error("Expected host context to exist. This error is likely caused by a bug in React. Please file an issue."),e}function xt(e,t){Re($o,t,e),Re(ws,e,e),Re(Vo,null,e);var n=t.nodeType;switch(n){case 9:case 11:n=n===9?"#document":"#fragment",t=(t=t.documentElement)&&(t=t.namespaceURI)?X_(t):fo;break;default:if(n=t.tagName,t=t.namespaceURI)t=X_(t),t=I_(t,n);else switch(n){case"svg":t=pl;break;case"math":t=Qf;break;default:t=fo}}n=n.toLowerCase(),n=nb(null,n),n={context:t,ancestorInfo:n},ue(Vo,e),Re(Vo,n,e)}function Nt(e){ue(Vo,e),ue(ws,e),ue($o,e)}function q(){return ut(Vo.current)}function se(e){e.memoizedState!==null&&Re(uf,e,e);var t=ut(Vo.current),n=e.type,a=I_(t.context,n);n=nb(t.ancestorInfo,n),a={context:a,ancestorInfo:n},t!==a&&(Re(ws,e,e),Re(Vo,a,e))}function oe(e){ws.current===e&&(ue(Vo,e),ue(ws,e)),uf.current===e&&(ue(uf,e),uu._currentValue=er)}function De(e){return typeof Symbol=="function"&&Symbol.toStringTag&&e[Symbol.toStringTag]||e.constructor.name||"Object"}function L(e){try{return ae(e),!1}catch{return!0}}function ae(e){return""+e}function ee(e,t){if(L(e))return console.error("The provided `%s` attribute is an unsupported type %s. This value must be coerced to a string before using it here.",t,De(e)),ae(e)}function Le(e,t){if(L(e))return console.error("The provided `%s` CSS property is an unsupported type %s. This value must be coerced to a string before using it here.",t,De(e)),ae(e)}function Ke(e){if(L(e))return console.error("Form field values (value, checked, defaultValue, or defaultChecked props) must be strings, not %s. This value must be coerced to a string before using it here.",De(e)),ae(e)}function ct(e){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u")return!1;var t=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(t.isDisabled)return!0;if(!t.supportsFiber)return console.error("The installed version of React DevTools is too old and will not work with the current version of React. Please update React DevTools. https://react.dev/link/react-devtools"),!0;try{kr=t.inject(e),Ht=t}catch(n){console.error("React instrumentation encountered an error: %s.",n)}return!!t.checkDCE}function Ce(e){if(typeof Kz=="function"&&Jz(e),Ht&&typeof Ht.setStrictMode=="function")try{Ht.setStrictMode(kr,e)}catch(t){za||(za=!0,console.error("React instrumentation encountered an error: %s",t))}}function vi(e){X=e}function et(){X!==null&&typeof X.markCommitStopped=="function"&&X.markCommitStopped()}function At(e){X!==null&&typeof X.markComponentRenderStarted=="function"&&X.markComponentRenderStarted(e)}function Nn(){X!==null&&typeof X.markComponentRenderStopped=="function"&&X.markComponentRenderStopped()}function yi(e){X!==null&&typeof X.markRenderStarted=="function"&&X.markRenderStarted(e)}function Hl(){X!==null&&typeof X.markRenderStopped=="function"&&X.markRenderStopped()}function wo(e,t){X!==null&&typeof X.markStateUpdateScheduled=="function"&&X.markStateUpdateScheduled(e,t)}function ac(e){return e>>>=0,e===0?32:31-(Wz(e)/Fz|0)|0}function Ll(e){if(e&1)return"SyncHydrationLane";if(e&2)return"Sync";if(e&4)return"InputContinuousHydration";if(e&8)return"InputContinuous";if(e&16)return"DefaultHydration";if(e&32)return"Default";if(e&128)return"TransitionHydration";if(e&4194048)return"Transition";if(e&62914560)return"Retry";if(e&67108864)return"SelectiveHydration";if(e&134217728)return"IdleHydration";if(e&268435456)return"Idle";if(e&536870912)return"Offscreen";if(e&1073741824)return"Deferred"}function ft(e){var t=e&42;if(t!==0)return t;switch(e&-e){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:return 64;case 128:return 128;case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return e&4194048;case 4194304:case 8388608:case 16777216:case 33554432:return e&62914560;case 67108864:return 67108864;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 0;default:return console.error("Should have found matching lanes. This is a bug in React."),e}}function Pa(e,t,n){var a=e.pendingLanes;if(a===0)return 0;var o=0,r=e.suspendedLanes,c=e.pingedLanes;e=e.warmLanes;var d=a&134217727;return d!==0?(a=d&~r,a!==0?o=ft(a):(c&=d,c!==0?o=ft(c):n||(n=d&~e,n!==0&&(o=ft(n))))):(d=a&~r,d!==0?o=ft(d):c!==0?o=ft(c):n||(n=a&~e,n!==0&&(o=ft(n)))),o===0?0:t!==0&&t!==o&&(t&r)===0&&(r=o&-o,n=t&-t,r>=n||r===32&&(n&4194048)!==0)?t:o}function xo(e,t){return(e.pendingLanes&~(e.suspendedLanes&~e.pingedLanes)&t)===0}function rh(e,t){switch(e){case 1:case 2:case 4:case 8:case 64:return t+250;case 16:case 32:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return t+5e3;case 4194304:case 8388608:case 16777216:case 33554432:return-1;case 67108864:case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return console.error("Should have found matching lanes. This is a bug in React."),-1}}function _e(){var e=cf;return cf<<=1,(cf&4194048)===0&&(cf=256),e}function gi(){var e=ff;return ff<<=1,(ff&62914560)===0&&(ff=4194304),e}function hr(e){for(var t=[],n=0;31>n;n++)t.push(e);return t}function Ao(e,t){e.pendingLanes|=t,t!==268435456&&(e.suspendedLanes=0,e.pingedLanes=0,e.warmLanes=0)}function oc(e,t,n,a,o,r){var c=e.pendingLanes;e.pendingLanes=n,e.suspendedLanes=0,e.pingedLanes=0,e.warmLanes=0,e.expiredLanes&=n,e.entangledLanes&=n,e.errorRecoveryDisabledLanes&=n,e.shellSuspendCounter=0;var d=e.entanglements,v=e.expirationTimes,g=e.hiddenUpdates;for(n=c&~n;0<n;){var R=31-$t(n),j=1<<R;d[R]=0,v[R]=-1;var A=g[R];if(A!==null)for(g[R]=null,R=0;R<A.length;R++){var U=A[R];U!==null&&(U.lane&=-536870913)}n&=~j}a!==0&&Bl(e,a,0),r!==0&&o===0&&e.tag!==0&&(e.suspendedLanes|=r&~(c&~t))}function Bl(e,t,n){e.pendingLanes|=t,e.suspendedLanes&=~t;var a=31-$t(t);e.entangledLanes|=t,e.entanglements[a]=e.entanglements[a]|1073741824|n&4194090}function ic(e,t){var n=e.entangledLanes|=t;for(e=e.entanglements;n;){var a=31-$t(n),o=1<<a;o&t|e[a]&t&&(e[a]|=t),n&=~o}}function Vl(e){switch(e){case 2:e=1;break;case 8:e=4;break;case 32:e=16;break;case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:e=128;break;case 268435456:e=134217728;break;default:e=0}return e}function $l(e,t,n){if(ca)for(e=e.pendingUpdatersLaneMap;0<n;){var a=31-$t(n),o=1<<a;e[a].add(t),n&=~o}}function Pl(e,t){if(ca)for(var n=e.pendingUpdatersLaneMap,a=e.memoizedUpdaters;0<t;){var o=31-$t(t);e=1<<o,o=n[o],0<o.size&&(o.forEach(function(r){var c=r.alternate;c!==null&&a.has(c)||a.add(r)}),o.clear()),t&=~e}}function pr(e){return e&=-e,Pn<e?Da<e?(e&134217727)!==0?to:df:Da:Pn}function ql(){var e=Me.p;return e!==0?e:(e=window.event,e===void 0?to:dS(e.type))}function mr(e,t){var n=Me.p;try{return Me.p=e,t()}finally{Me.p=n}}function Ro(e){delete e[Lt],delete e[nn],delete e[Tm],delete e[eD],delete e[tD]}function ra(e){var t=e[Lt];if(t)return t;for(var n=e.parentNode;n;){if(t=n[qo]||n[Lt]){if(n=t.alternate,t.child!==null||n!==null&&n.child!==null)for(e=F_(e);e!==null;){if(n=e[Lt])return n;e=F_(e)}return t}e=n,n=e.parentNode}return null}function la(e){if(e=e[Lt]||e[qo]){var t=e.tag;if(t===5||t===6||t===13||t===26||t===27||t===3)return e}return null}function zo(e){var t=e.tag;if(t===5||t===26||t===27||t===6)return e.stateNode;throw Error("getNodeFromInstance: Invalid argument.")}function m(e){var t=e[bS];return t||(t=e[bS]={hoistableStyles:new Map,hoistableScripts:new Map}),t}function w(e){e[xs]=!0}function $(e,t){G(e,t),G(e+"Capture",t)}function G(e,t){ki[e]&&console.error("EventRegistry: More than one plugin attempted to publish the same registration name, `%s`.",e),ki[e]=t;var n=e.toLowerCase();for(Om[n]=e,e==="onDoubleClick"&&(Om.ondblclick=e),e=0;e<t.length;e++)_S.add(t[e])}function J(e,t){nD[t.type]||t.onChange||t.onInput||t.readOnly||t.disabled||t.value==null||console.error(e==="select"?"You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set `onChange`.":"You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set either `onChange` or `readOnly`."),t.onChange||t.readOnly||t.disabled||t.checked==null||console.error("You provided a `checked` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultChecked`. Otherwise, set either `onChange` or `readOnly`.")}function ge(e){return eo.call(TS,e)?!0:eo.call(SS,e)?!1:aD.test(e)?TS[e]=!0:(SS[e]=!0,console.error("Invalid attribute name: `%s`",e),!1)}function pe(e,t,n){if(ge(t)){if(!e.hasAttribute(t)){switch(typeof n){case"symbol":case"object":return n;case"function":return n;case"boolean":if(n===!1)return n}return n===void 0?void 0:null}return e=e.getAttribute(t),e===""&&n===!0?!0:(ee(n,t),e===""+n?n:e)}}function xe(e,t,n){if(ge(t))if(n===null)e.removeAttribute(t);else{switch(typeof n){case"undefined":case"function":case"symbol":e.removeAttribute(t);return;case"boolean":var a=t.toLowerCase().slice(0,5);if(a!=="data-"&&a!=="aria-"){e.removeAttribute(t);return}}ee(n,t),e.setAttribute(t,""+n)}}function me(e,t,n){if(n===null)e.removeAttribute(t);else{switch(typeof n){case"undefined":case"function":case"symbol":case"boolean":e.removeAttribute(t);return}ee(n,t),e.setAttribute(t,""+n)}}function _t(e,t,n,a){if(a===null)e.removeAttribute(n);else{switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":e.removeAttribute(n);return}ee(a,n),e.setAttributeNS(t,n,""+a)}}function qa(){}function lh(){if(As===0){OS=console.log,ES=console.info,wS=console.warn,xS=console.error,AS=console.group,RS=console.groupCollapsed,zS=console.groupEnd;var e={configurable:!0,enumerable:!0,value:qa,writable:!0};Object.defineProperties(console,{info:e,log:e,warn:e,error:e,group:e,groupCollapsed:e,groupEnd:e})}As++}function sh(){if(As--,As===0){var e={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:be({},e,{value:OS}),info:be({},e,{value:ES}),warn:be({},e,{value:wS}),error:be({},e,{value:xS}),group:be({},e,{value:AS}),groupCollapsed:be({},e,{value:RS}),groupEnd:be({},e,{value:zS})})}0>As&&console.error("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}function Hn(e){if(Em===void 0)try{throw Error()}catch(n){var t=n.stack.trim().match(/\n( *(at )?)/);Em=t&&t[1]||"",DS=-1<n.stack.indexOf(`
    at`)?" (<anonymous>)":-1<n.stack.indexOf("@")?"@unknown:0:0":""}return`
`+Em+e+DS}function uh(e,t){if(!e||wm)return"";var n=xm.get(e);if(n!==void 0)return n;wm=!0,n=Error.prepareStackTrace,Error.prepareStackTrace=void 0;var a=null;a=M.H,M.H=null,lh();try{var o={DetermineComponentFrameRoot:function(){try{if(t){var A=function(){throw Error()};if(Object.defineProperty(A.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(A,[])}catch(Z){var U=Z}Reflect.construct(e,[],A)}else{try{A.call()}catch(Z){U=Z}e.call(A.prototype)}}else{try{throw Error()}catch(Z){U=Z}(A=e())&&typeof A.catch=="function"&&A.catch(function(){})}}catch(Z){if(Z&&U&&typeof Z.stack=="string")return[Z.stack,U.stack]}return[null,null]}};o.DetermineComponentFrameRoot.displayName="DetermineComponentFrameRoot";var r=Object.getOwnPropertyDescriptor(o.DetermineComponentFrameRoot,"name");r&&r.configurable&&Object.defineProperty(o.DetermineComponentFrameRoot,"name",{value:"DetermineComponentFrameRoot"});var c=o.DetermineComponentFrameRoot(),d=c[0],v=c[1];if(d&&v){var g=d.split(`
`),R=v.split(`
`);for(c=r=0;r<g.length&&!g[r].includes("DetermineComponentFrameRoot");)r++;for(;c<R.length&&!R[c].includes("DetermineComponentFrameRoot");)c++;if(r===g.length||c===R.length)for(r=g.length-1,c=R.length-1;1<=r&&0<=c&&g[r]!==R[c];)c--;for(;1<=r&&0<=c;r--,c--)if(g[r]!==R[c]){if(r!==1||c!==1)do if(r--,c--,0>c||g[r]!==R[c]){var j=`
`+g[r].replace(" at new "," at ");return e.displayName&&j.includes("<anonymous>")&&(j=j.replace("<anonymous>",e.displayName)),typeof e=="function"&&xm.set(e,j),j}while(1<=r&&0<=c);break}}}finally{wm=!1,M.H=a,sh(),Error.prepareStackTrace=n}return g=(g=e?e.displayName||e.name:"")?Hn(g):"",typeof e=="function"&&xm.set(e,g),g}function Bg(e){var t=Error.prepareStackTrace;if(Error.prepareStackTrace=void 0,e=e.stack,Error.prepareStackTrace=t,e.startsWith(`Error: react-stack-top-frame
`)&&(e=e.slice(29)),t=e.indexOf(`
`),t!==-1&&(e=e.slice(t+1)),t=e.indexOf("react_stack_bottom_frame"),t!==-1&&(t=e.lastIndexOf(`
`,t)),t!==-1)e=e.slice(0,t);else return"";return e}function rR(e){switch(e.tag){case 26:case 27:case 5:return Hn(e.type);case 16:return Hn("Lazy");case 13:return Hn("Suspense");case 19:return Hn("SuspenseList");case 0:case 15:return uh(e.type,!1);case 11:return uh(e.type.render,!1);case 1:return uh(e.type,!0);case 31:return Hn("Activity");default:return""}}function Vg(e){try{var t="";do{t+=rR(e);var n=e._debugInfo;if(n)for(var a=n.length-1;0<=a;a--){var o=n[a];if(typeof o.name=="string"){var r=t,c=o.env,d=Hn(o.name+(c?" ["+c+"]":""));t=r+d}}e=e.return}while(e);return t}catch(v){return`
Error generating stack: `+v.message+`
`+v.stack}}function $g(e){return(e=e?e.displayName||e.name:"")?Hn(e):""}function rc(){if(Tn===null)return null;var e=Tn._debugOwner;return e!=null?Vt(e):null}function lR(){if(Tn===null)return"";var e=Tn;try{var t="";switch(e.tag===6&&(e=e.return),e.tag){case 26:case 27:case 5:t+=Hn(e.type);break;case 13:t+=Hn("Suspense");break;case 19:t+=Hn("SuspenseList");break;case 31:t+=Hn("Activity");break;case 30:case 0:case 15:case 1:e._debugOwner||t!==""||(t+=$g(e.type));break;case 11:e._debugOwner||t!==""||(t+=$g(e.type.render))}for(;e;)if(typeof e.tag=="number"){var n=e;e=n._debugOwner;var a=n._debugStack;e&&a&&(typeof a!="string"&&(n._debugStack=a=Bg(a)),a!==""&&(t+=`
`+a))}else if(e.debugStack!=null){var o=e.debugStack;(e=e.owner)&&o&&(t+=`
`+Bg(o))}else break;var r=t}catch(c){r=`
Error generating stack: `+c.message+`
`+c.stack}return r}function W(e,t,n,a,o,r,c){var d=Tn;ch(e);try{return e!==null&&e._debugTask?e._debugTask.run(t.bind(null,n,a,o,r,c)):t(n,a,o,r,c)}finally{ch(d)}throw Error("runWithFiberInDEV should never be called in production. This is a bug in React.")}function ch(e){M.getCurrentStack=e===null?null:lR,Ca=!1,Tn=e}function Ln(e){switch(typeof e){case"bigint":case"boolean":case"number":case"string":case"undefined":return e;case"object":return Ke(e),e;default:return""}}function Pg(e){var t=e.type;return(e=e.nodeName)&&e.toLowerCase()==="input"&&(t==="checkbox"||t==="radio")}function sR(e){var t=Pg(e)?"checked":"value",n=Object.getOwnPropertyDescriptor(e.constructor.prototype,t);Ke(e[t]);var a=""+e[t];if(!e.hasOwnProperty(t)&&typeof n<"u"&&typeof n.get=="function"&&typeof n.set=="function"){var o=n.get,r=n.set;return Object.defineProperty(e,t,{configurable:!0,get:function(){return o.call(this)},set:function(c){Ke(c),a=""+c,r.call(this,c)}}),Object.defineProperty(e,t,{enumerable:n.enumerable}),{getValue:function(){return a},setValue:function(c){Ke(c),a=""+c},stopTracking:function(){e._valueTracker=null,delete e[t]}}}}function lc(e){e._valueTracker||(e._valueTracker=sR(e))}function qg(e){if(!e)return!1;var t=e._valueTracker;if(!t)return!0;var n=t.getValue(),a="";return e&&(a=Pg(e)?e.checked?"true":"false":e.value),e=a,e!==n?(t.setValue(e),!0):!1}function sc(e){if(e=e||(typeof document<"u"?document:void 0),typeof e>"u")return null;try{return e.activeElement||e.body}catch{return e.body}}function Bn(e){return e.replace(oD,function(t){return"\\"+t.charCodeAt(0).toString(16)+" "})}function Gg(e,t){t.checked===void 0||t.defaultChecked===void 0||MS||(console.error("%s contains an input of type %s with both checked and defaultChecked props. Input elements must be either controlled or uncontrolled (specify either the checked prop, or the defaultChecked prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://react.dev/link/controlled-components",rc()||"A component",t.type),MS=!0),t.value===void 0||t.defaultValue===void 0||CS||(console.error("%s contains an input of type %s with both value and defaultValue props. Input elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://react.dev/link/controlled-components",rc()||"A component",t.type),CS=!0)}function fh(e,t,n,a,o,r,c,d){e.name="",c!=null&&typeof c!="function"&&typeof c!="symbol"&&typeof c!="boolean"?(ee(c,"type"),e.type=c):e.removeAttribute("type"),t!=null?c==="number"?(t===0&&e.value===""||e.value!=t)&&(e.value=""+Ln(t)):e.value!==""+Ln(t)&&(e.value=""+Ln(t)):c!=="submit"&&c!=="reset"||e.removeAttribute("value"),t!=null?dh(e,c,Ln(t)):n!=null?dh(e,c,Ln(n)):a!=null&&e.removeAttribute("value"),o==null&&r!=null&&(e.defaultChecked=!!r),o!=null&&(e.checked=o&&typeof o!="function"&&typeof o!="symbol"),d!=null&&typeof d!="function"&&typeof d!="symbol"&&typeof d!="boolean"?(ee(d,"name"),e.name=""+Ln(d)):e.removeAttribute("name")}function Yg(e,t,n,a,o,r,c,d){if(r!=null&&typeof r!="function"&&typeof r!="symbol"&&typeof r!="boolean"&&(ee(r,"type"),e.type=r),t!=null||n!=null){if(!(r!=="submit"&&r!=="reset"||t!=null))return;n=n!=null?""+Ln(n):"",t=t!=null?""+Ln(t):n,d||t===e.value||(e.value=t),e.defaultValue=t}a=a??o,a=typeof a!="function"&&typeof a!="symbol"&&!!a,e.checked=d?e.checked:!!a,e.defaultChecked=!!a,c!=null&&typeof c!="function"&&typeof c!="symbol"&&typeof c!="boolean"&&(ee(c,"name"),e.name=c)}function dh(e,t,n){t==="number"&&sc(e.ownerDocument)===e||e.defaultValue===""+n||(e.defaultValue=""+n)}function Xg(e,t){t.value==null&&(typeof t.children=="object"&&t.children!==null?cm.Children.forEach(t.children,function(n){n==null||typeof n=="string"||typeof n=="number"||typeof n=="bigint"||US||(US=!0,console.error("Cannot infer the option value of complex children. Pass a `value` prop or use a plain string as children to <option>."))}):t.dangerouslySetInnerHTML==null||kS||(kS=!0,console.error("Pass a `value` prop if you set dangerouslyInnerHTML so React knows which value should be selected."))),t.selected==null||jS||(console.error("Use the `defaultValue` or `value` props on <select> instead of setting `selected` on <option>."),jS=!0)}function Ig(){var e=rc();return e?`

Check the render method of \``+e+"`.":""}function vr(e,t,n,a){if(e=e.options,t){t={};for(var o=0;o<n.length;o++)t["$"+n[o]]=!0;for(n=0;n<e.length;n++)o=t.hasOwnProperty("$"+e[n].value),e[n].selected!==o&&(e[n].selected=o),o&&a&&(e[n].defaultSelected=!0)}else{for(n=""+Ln(n),t=null,o=0;o<e.length;o++){if(e[o].value===n){e[o].selected=!0,a&&(e[o].defaultSelected=!0);return}t!==null||e[o].disabled||(t=e[o])}t!==null&&(t.selected=!0)}}function Qg(e,t){for(e=0;e<HS.length;e++){var n=HS[e];if(t[n]!=null){var a=Ct(t[n]);t.multiple&&!a?console.error("The `%s` prop supplied to <select> must be an array if `multiple` is true.%s",n,Ig()):!t.multiple&&a&&console.error("The `%s` prop supplied to <select> must be a scalar value if `multiple` is false.%s",n,Ig())}}t.value===void 0||t.defaultValue===void 0||NS||(console.error("Select elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled select element and remove one of these props. More info: https://react.dev/link/controlled-components"),NS=!0)}function Zg(e,t){t.value===void 0||t.defaultValue===void 0||LS||(console.error("%s contains a textarea with both value and defaultValue props. Textarea elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled textarea and remove one of these props. More info: https://react.dev/link/controlled-components",rc()||"A component"),LS=!0),t.children!=null&&t.value==null&&console.error("Use the `defaultValue` or `value` props instead of setting children on <textarea>.")}function Kg(e,t,n){if(t!=null&&(t=""+Ln(t),t!==e.value&&(e.value=t),n==null)){e.defaultValue!==t&&(e.defaultValue=t);return}e.defaultValue=n!=null?""+Ln(n):""}function Jg(e,t,n,a){if(t==null){if(a!=null){if(n!=null)throw Error("If you supply `defaultValue` on a <textarea>, do not pass children.");if(Ct(a)){if(1<a.length)throw Error("<textarea> can only have at most one child.");a=a[0]}n=a}n==null&&(n=""),t=n}n=Ln(t),e.defaultValue=n,a=e.textContent,a===n&&a!==""&&a!==null&&(e.value=a)}function Wg(e,t){return e.serverProps===void 0&&e.serverTail.length===0&&e.children.length===1&&3<e.distanceFromLeaf&&e.distanceFromLeaf>15-t?Wg(e.children[0],t):e}function vn(e){return"  "+"  ".repeat(e)}function yr(e){return"+ "+"  ".repeat(e)}function bi(e){return"- "+"  ".repeat(e)}function Fg(e){switch(e.tag){case 26:case 27:case 5:return e.type;case 16:return"Lazy";case 13:return"Suspense";case 19:return"SuspenseList";case 0:case 15:return e=e.type,e.displayName||e.name||null;case 11:return e=e.type.render,e.displayName||e.name||null;case 1:return e=e.type,e.displayName||e.name||null;default:return null}}function Gl(e,t){return BS.test(e)?(e=JSON.stringify(e),e.length>t-2?8>t?'{"..."}':"{"+e.slice(0,t-7)+'..."}':"{"+e+"}"):e.length>t?5>t?'{"..."}':e.slice(0,t-3)+"...":e}function uc(e,t,n){var a=120-2*n;if(t===null)return yr(n)+Gl(e,a)+`
`;if(typeof t=="string"){for(var o=0;o<t.length&&o<e.length&&t.charCodeAt(o)===e.charCodeAt(o);o++);return o>a-8&&10<o&&(e="..."+e.slice(o-8),t="..."+t.slice(o-8)),yr(n)+Gl(e,a)+`
`+bi(n)+Gl(t,a)+`
`}return vn(n)+Gl(e,a)+`
`}function hh(e){return Object.prototype.toString.call(e).replace(/^\[object (.*)\]$/,function(t,n){return n})}function Yl(e,t){switch(typeof e){case"string":return e=JSON.stringify(e),e.length>t?5>t?'"..."':e.slice(0,t-4)+'..."':e;case"object":if(e===null)return"null";if(Ct(e))return"[...]";if(e.$$typeof===Bo)return(t=Ne(e.type))?"<"+t+">":"<...>";var n=hh(e);if(n==="Object"){n="",t-=2;for(var a in e)if(e.hasOwnProperty(a)){var o=JSON.stringify(a);if(o!=='"'+a+'"'&&(a=o),t-=a.length-2,o=Yl(e[a],15>t?t:15),t-=o.length,0>t){n+=n===""?"...":", ...";break}n+=(n===""?"":",")+a+":"+o}return"{"+n+"}"}return n;case"function":return(t=e.displayName||e.name)?"function "+t:"function";default:return String(e)}}function gr(e,t){return typeof e!="string"||BS.test(e)?"{"+Yl(e,t-2)+"}":e.length>t-2?5>t?'"..."':'"'+e.slice(0,t-5)+'..."':'"'+e+'"'}function ph(e,t,n){var a=120-n.length-e.length,o=[],r;for(r in t)if(t.hasOwnProperty(r)&&r!=="children"){var c=gr(t[r],120-n.length-r.length-1);a-=r.length+c.length+2,o.push(r+"="+c)}return o.length===0?n+"<"+e+`>
`:0<a?n+"<"+e+" "+o.join(" ")+`>
`:n+"<"+e+`
`+n+"  "+o.join(`
`+n+"  ")+`
`+n+`>
`}function uR(e,t,n){var a="",o=be({},t),r;for(r in e)if(e.hasOwnProperty(r)){delete o[r];var c=120-2*n-r.length-2,d=Yl(e[r],c);t.hasOwnProperty(r)?(c=Yl(t[r],c),a+=yr(n)+r+": "+d+`
`,a+=bi(n)+r+": "+c+`
`):a+=yr(n)+r+": "+d+`
`}for(var v in o)o.hasOwnProperty(v)&&(e=Yl(o[v],120-2*n-v.length-2),a+=bi(n)+v+": "+e+`
`);return a}function cR(e,t,n,a){var o="",r=new Map;for(g in n)n.hasOwnProperty(g)&&r.set(g.toLowerCase(),g);if(r.size===1&&r.has("children"))o+=ph(e,t,vn(a));else{for(var c in t)if(t.hasOwnProperty(c)&&c!=="children"){var d=120-2*(a+1)-c.length-1,v=r.get(c.toLowerCase());if(v!==void 0){r.delete(c.toLowerCase());var g=t[c];v=n[v];var R=gr(g,d);d=gr(v,d),typeof g=="object"&&g!==null&&typeof v=="object"&&v!==null&&hh(g)==="Object"&&hh(v)==="Object"&&(2<Object.keys(g).length||2<Object.keys(v).length||-1<R.indexOf("...")||-1<d.indexOf("..."))?o+=vn(a+1)+c+`={{
`+uR(g,v,a+2)+vn(a+1)+`}}
`:(o+=yr(a+1)+c+"="+R+`
`,o+=bi(a+1)+c+"="+d+`
`)}else o+=vn(a+1)+c+"="+gr(t[c],d)+`
`}r.forEach(function(j){if(j!=="children"){var A=120-2*(a+1)-j.length-1;o+=bi(a+1)+j+"="+gr(n[j],A)+`
`}}),o=o===""?vn(a)+"<"+e+`>
`:vn(a)+"<"+e+`
`+o+vn(a)+`>
`}return e=n.children,t=t.children,typeof e=="string"||typeof e=="number"||typeof e=="bigint"?(r="",(typeof t=="string"||typeof t=="number"||typeof t=="bigint")&&(r=""+t),o+=uc(r,""+e,a+1)):(typeof t=="string"||typeof t=="number"||typeof t=="bigint")&&(o=e==null?o+uc(""+t,null,a+1):o+uc(""+t,void 0,a+1)),o}function eb(e,t){var n=Fg(e);if(n===null){for(n="",e=e.child;e;)n+=eb(e,t),e=e.sibling;return n}return vn(t)+"<"+n+`>
`}function mh(e,t){var n=Wg(e,t);if(n!==e&&(e.children.length!==1||e.children[0]!==n))return vn(t)+`...
`+mh(n,t+1);n="";var a=e.fiber._debugInfo;if(a)for(var o=0;o<a.length;o++){var r=a[o].name;typeof r=="string"&&(n+=vn(t)+"<"+r+`>
`,t++)}if(a="",o=e.fiber.pendingProps,e.fiber.tag===6)a=uc(o,e.serverProps,t),t++;else if(r=Fg(e.fiber),r!==null)if(e.serverProps===void 0){a=t;var c=120-2*a-r.length-2,d="";for(g in o)if(o.hasOwnProperty(g)&&g!=="children"){var v=gr(o[g],15);if(c-=g.length+v.length+2,0>c){d+=" ...";break}d+=" "+g+"="+v}a=vn(a)+"<"+r+d+`>
`,t++}else e.serverProps===null?(a=ph(r,o,yr(t)),t++):typeof e.serverProps=="string"?console.error("Should not have matched a non HostText fiber to a Text node. This is a bug in React."):(a=cR(r,o,e.serverProps,t),t++);var g="";for(o=e.fiber.child,r=0;o&&r<e.children.length;)c=e.children[r],c.fiber===o?(g+=mh(c,t),r++):g+=eb(o,t),o=o.sibling;for(o&&0<e.children.length&&(g+=vn(t)+`...
`),o=e.serverTail,e.serverProps===null&&t--,e=0;e<o.length;e++)r=o[e],g=typeof r=="string"?g+(bi(t)+Gl(r,120-2*t)+`
`):g+ph(r.type,r.props,bi(t));return n+a+g}function vh(e){try{return`

`+mh(e,0)}catch{return""}}function tb(e,t,n){for(var a=t,o=null,r=0;a;)a===e&&(r=0),o={fiber:a,children:o!==null?[o]:[],serverProps:a===t?n:a===e?null:void 0,serverTail:[],distanceFromLeaf:r},r++,a=a.return;return o!==null?vh(o).replaceAll(/^[+-]/gm,">"):""}function nb(e,t){var n=be({},e||$S),a={tag:t};return VS.indexOf(t)!==-1&&(n.aTagInScope=null,n.buttonTagInScope=null,n.nobrTagInScope=null),rD.indexOf(t)!==-1&&(n.pTagInButtonScope=null),iD.indexOf(t)!==-1&&t!=="address"&&t!=="div"&&t!=="p"&&(n.listItemTagAutoclosing=null,n.dlItemTagAutoclosing=null),n.current=a,t==="form"&&(n.formTag=a),t==="a"&&(n.aTagInScope=a),t==="button"&&(n.buttonTagInScope=a),t==="nobr"&&(n.nobrTagInScope=a),t==="p"&&(n.pTagInButtonScope=a),t==="li"&&(n.listItemTagAutoclosing=a),(t==="dd"||t==="dt")&&(n.dlItemTagAutoclosing=a),t==="#document"||t==="html"?n.containerTagInScope=null:n.containerTagInScope||(n.containerTagInScope=a),e!==null||t!=="#document"&&t!=="html"&&t!=="body"?n.implicitRootScope===!0&&(n.implicitRootScope=!1):n.implicitRootScope=!0,n}function ab(e,t,n){switch(t){case"select":return e==="hr"||e==="option"||e==="optgroup"||e==="script"||e==="template"||e==="#text";case"optgroup":return e==="option"||e==="#text";case"option":return e==="#text";case"tr":return e==="th"||e==="td"||e==="style"||e==="script"||e==="template";case"tbody":case"thead":case"tfoot":return e==="tr"||e==="style"||e==="script"||e==="template";case"colgroup":return e==="col"||e==="template";case"table":return e==="caption"||e==="colgroup"||e==="tbody"||e==="tfoot"||e==="thead"||e==="style"||e==="script"||e==="template";case"head":return e==="base"||e==="basefont"||e==="bgsound"||e==="link"||e==="meta"||e==="title"||e==="noscript"||e==="noframes"||e==="style"||e==="script"||e==="template";case"html":if(n)break;return e==="head"||e==="body"||e==="frameset";case"frameset":return e==="frame";case"#document":if(!n)return e==="html"}switch(e){case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t!=="h1"&&t!=="h2"&&t!=="h3"&&t!=="h4"&&t!=="h5"&&t!=="h6";case"rp":case"rt":return lD.indexOf(t)===-1;case"caption":case"col":case"colgroup":case"frameset":case"frame":case"tbody":case"td":case"tfoot":case"th":case"thead":case"tr":return t==null;case"head":return n||t===null;case"html":return n&&t==="#document"||t===null;case"body":return n&&(t==="#document"||t==="html")||t===null}return!0}function fR(e,t){switch(e){case"address":case"article":case"aside":case"blockquote":case"center":case"details":case"dialog":case"dir":case"div":case"dl":case"fieldset":case"figcaption":case"figure":case"footer":case"header":case"hgroup":case"main":case"menu":case"nav":case"ol":case"p":case"section":case"summary":case"ul":case"pre":case"listing":case"table":case"hr":case"xmp":case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t.pTagInButtonScope;case"form":return t.formTag||t.pTagInButtonScope;case"li":return t.listItemTagAutoclosing;case"dd":case"dt":return t.dlItemTagAutoclosing;case"button":return t.buttonTagInScope;case"a":return t.aTagInScope;case"nobr":return t.nobrTagInScope}return null}function ob(e,t){for(;e;){switch(e.tag){case 5:case 26:case 27:if(e.type===t)return e}e=e.return}return null}function yh(e,t){t=t||$S;var n=t.current;if(t=(n=ab(e,n&&n.tag,t.implicitRootScope)?null:n)?null:fR(e,t),t=n||t,!t)return!0;var a=t.tag;if(t=String(!!n)+"|"+e+"|"+a,hf[t])return!1;hf[t]=!0;var o=(t=Tn)?ob(t.return,a):null,r=t!==null&&o!==null?tb(o,t,null):"",c="<"+e+">";return n?(n="",a==="table"&&e==="tr"&&(n+=" Add a <tbody>, <thead> or <tfoot> to your code to match the DOM tree generated by the browser."),console.error(`In HTML, %s cannot be a child of <%s>.%s
This will cause a hydration error.%s`,c,a,n,r)):console.error(`In HTML, %s cannot be a descendant of <%s>.
This will cause a hydration error.%s`,c,a,r),t&&(e=t.return,o===null||e===null||o===e&&e._debugOwner===t._debugOwner||W(o,function(){console.error(`<%s> cannot contain a nested %s.
See this log for the ancestor stack trace.`,a,c)})),!1}function cc(e,t,n){if(n||ab("#text",t,!1))return!0;if(n="#text|"+t,hf[n])return!1;hf[n]=!0;var a=(n=Tn)?ob(n,t):null;return n=n!==null&&a!==null?tb(a,n,n.tag!==6?{children:null}:null):"",/\S/.test(e)?console.error(`In HTML, text nodes cannot be a child of <%s>.
This will cause a hydration error.%s`,t,n):console.error(`In HTML, whitespace text nodes cannot be a child of <%s>. Make sure you don't have any extra whitespace between tags on each line of your source code.
This will cause a hydration error.%s`,t,n),!1}function Xl(e,t){if(t){var n=e.firstChild;if(n&&n===e.lastChild&&n.nodeType===3){n.nodeValue=t;return}}e.textContent=t}function dR(e){return e.replace(cD,function(t,n){return n.toUpperCase()})}function ib(e,t,n){var a=t.indexOf("--")===0;a||(-1<t.indexOf("-")?Nr.hasOwnProperty(t)&&Nr[t]||(Nr[t]=!0,console.error("Unsupported style property %s. Did you mean %s?",t,dR(t.replace(uD,"ms-")))):sD.test(t)?Nr.hasOwnProperty(t)&&Nr[t]||(Nr[t]=!0,console.error("Unsupported vendor-prefixed style property %s. Did you mean %s?",t,t.charAt(0).toUpperCase()+t.slice(1))):!GS.test(n)||Rm.hasOwnProperty(n)&&Rm[n]||(Rm[n]=!0,console.error(`Style property values shouldn't contain a semicolon. Try "%s: %s" instead.`,t,n.replace(GS,""))),typeof n=="number"&&(isNaN(n)?YS||(YS=!0,console.error("`NaN` is an invalid value for the `%s` css style property.",t)):isFinite(n)||XS||(XS=!0,console.error("`Infinity` is an invalid value for the `%s` css style property.",t)))),n==null||typeof n=="boolean"||n===""?a?e.setProperty(t,""):t==="float"?e.cssFloat="":e[t]="":a?e.setProperty(t,n):typeof n!="number"||n===0||IS.has(t)?t==="float"?e.cssFloat=n:(Le(n,t),e[t]=(""+n).trim()):e[t]=n+"px"}function rb(e,t,n){if(t!=null&&typeof t!="object")throw Error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.");if(t&&Object.freeze(t),e=e.style,n!=null){if(t){var a={};if(n){for(var o in n)if(n.hasOwnProperty(o)&&!t.hasOwnProperty(o))for(var r=Am[o]||[o],c=0;c<r.length;c++)a[r[c]]=o}for(var d in t)if(t.hasOwnProperty(d)&&(!n||n[d]!==t[d]))for(o=Am[d]||[d],r=0;r<o.length;r++)a[o[r]]=d;d={};for(var v in t)for(o=Am[v]||[v],r=0;r<o.length;r++)d[o[r]]=v;v={};for(var g in a)if(o=a[g],(r=d[g])&&o!==r&&(c=o+","+r,!v[c])){v[c]=!0,c=console;var R=t[o];c.error.call(c,"%s a style property during rerender (%s) when a conflicting property is set (%s) can lead to styling bugs. To avoid this, don't mix shorthand and non-shorthand properties for the same value; instead, replace the shorthand with separate values.",R==null||typeof R=="boolean"||R===""?"Removing":"Updating",o,r)}}for(var j in n)!n.hasOwnProperty(j)||t!=null&&t.hasOwnProperty(j)||(j.indexOf("--")===0?e.setProperty(j,""):j==="float"?e.cssFloat="":e[j]="");for(var A in t)g=t[A],t.hasOwnProperty(A)&&n[A]!==g&&ib(e,A,g)}else for(a in t)t.hasOwnProperty(a)&&ib(e,a,t[a])}function Il(e){if(e.indexOf("-")===-1)return!1;switch(e){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}function lb(e){return fD.get(e)||e}function hR(e,t){if(eo.call(Lr,t)&&Lr[t])return!0;if(hD.test(t)){if(e="aria-"+t.slice(4).toLowerCase(),e=QS.hasOwnProperty(e)?e:null,e==null)return console.error("Invalid ARIA attribute `%s`. ARIA attributes follow the pattern aria-* and must be lowercase.",t),Lr[t]=!0;if(t!==e)return console.error("Invalid ARIA attribute `%s`. Did you mean `%s`?",t,e),Lr[t]=!0}if(dD.test(t)){if(e=t.toLowerCase(),e=QS.hasOwnProperty(e)?e:null,e==null)return Lr[t]=!0,!1;t!==e&&(console.error("Unknown ARIA attribute `%s`. Did you mean `%s`?",t,e),Lr[t]=!0)}return!0}function pR(e,t){var n=[],a;for(a in t)hR(e,a)||n.push(a);t=n.map(function(o){return"`"+o+"`"}).join(", "),n.length===1?console.error("Invalid aria prop %s on <%s> tag. For details, see https://react.dev/link/invalid-aria-props",t,e):1<n.length&&console.error("Invalid aria props %s on <%s> tag. For details, see https://react.dev/link/invalid-aria-props",t,e)}function mR(e,t,n,a){if(eo.call(Pt,t)&&Pt[t])return!0;var o=t.toLowerCase();if(o==="onfocusin"||o==="onfocusout")return console.error("React uses onFocus and onBlur instead of onFocusIn and onFocusOut. All React events are normalized to bubble, so onFocusIn and onFocusOut are not needed/supported by React."),Pt[t]=!0;if(typeof n=="function"&&(e==="form"&&t==="action"||e==="input"&&t==="formAction"||e==="button"&&t==="formAction"))return!0;if(a!=null){if(e=a.possibleRegistrationNames,a.registrationNameDependencies.hasOwnProperty(t))return!0;if(a=e.hasOwnProperty(o)?e[o]:null,a!=null)return console.error("Invalid event handler property `%s`. Did you mean `%s`?",t,a),Pt[t]=!0;if(KS.test(t))return console.error("Unknown event handler property `%s`. It will be ignored.",t),Pt[t]=!0}else if(KS.test(t))return pD.test(t)&&console.error("Invalid event handler property `%s`. React events use the camelCase naming convention, for example `onClick`.",t),Pt[t]=!0;if(mD.test(t)||vD.test(t))return!0;if(o==="innerhtml")return console.error("Directly setting property `innerHTML` is not permitted. For more information, lookup documentation on `dangerouslySetInnerHTML`."),Pt[t]=!0;if(o==="aria")return console.error("The `aria` attribute is reserved for future use in React. Pass individual `aria-` attributes instead."),Pt[t]=!0;if(o==="is"&&n!==null&&n!==void 0&&typeof n!="string")return console.error("Received a `%s` for a string attribute `is`. If this is expected, cast the value to a string.",typeof n),Pt[t]=!0;if(typeof n=="number"&&isNaN(n))return console.error("Received NaN for the `%s` attribute. If this is expected, cast the value to a string.",t),Pt[t]=!0;if(mf.hasOwnProperty(o)){if(o=mf[o],o!==t)return console.error("Invalid DOM property `%s`. Did you mean `%s`?",t,o),Pt[t]=!0}else if(t!==o)return console.error("React does not recognize the `%s` prop on a DOM element. If you intentionally want it to appear in the DOM as a custom attribute, spell it as lowercase `%s` instead. If you accidentally passed it from a parent component, remove it from the DOM element.",t,o),Pt[t]=!0;switch(t){case"dangerouslySetInnerHTML":case"children":case"style":case"suppressContentEditableWarning":case"suppressHydrationWarning":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":return!0;case"innerText":case"textContent":return!0}switch(typeof n){case"boolean":switch(t){case"autoFocus":case"checked":case"multiple":case"muted":case"selected":case"contentEditable":case"spellCheck":case"draggable":case"value":case"autoReverse":case"externalResourcesRequired":case"focusable":case"preserveAlpha":case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":case"capture":case"download":case"inert":return!0;default:return o=t.toLowerCase().slice(0,5),o==="data-"||o==="aria-"?!0:(n?console.error('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.',n,t,t,n,t):console.error('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.',n,t,t,n,t,t,t),Pt[t]=!0)}case"function":case"symbol":return Pt[t]=!0,!1;case"string":if(n==="false"||n==="true"){switch(t){case"checked":case"selected":case"multiple":case"muted":case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":case"inert":break;default:return!0}console.error("Received the string `%s` for the boolean attribute `%s`. %s Did you mean %s={%s}?",n,t,n==="false"?"The browser will interpret it as a truthy value.":'Although this works, it will not work as expected if you pass the string "false".',t,n),Pt[t]=!0}}return!0}function vR(e,t,n){var a=[],o;for(o in t)mR(e,o,t[o],n)||a.push(o);t=a.map(function(r){return"`"+r+"`"}).join(", "),a.length===1?console.error("Invalid value for prop %s on <%s> tag. Either remove it from the element, or pass a string or number value to keep it in the DOM. For details, see https://react.dev/link/attribute-behavior ",t,e):1<a.length&&console.error("Invalid values for props %s on <%s> tag. Either remove them from the element, or pass a string or number value to keep them in the DOM. For details, see https://react.dev/link/attribute-behavior ",t,e)}function Ql(e){return yD.test(""+e)?"javascript:throw new Error('React has blocked a javascript: URL as a security precaution.')":e}function gh(e){return e=e.target||e.srcElement||window,e.correspondingUseElement&&(e=e.correspondingUseElement),e.nodeType===3?e.parentNode:e}function sb(e){var t=la(e);if(t&&(e=t.stateNode)){var n=e[nn]||null;e:switch(e=t.stateNode,t.type){case"input":if(fh(e,n.value,n.defaultValue,n.defaultValue,n.checked,n.defaultChecked,n.type,n.name),t=n.name,n.type==="radio"&&t!=null){for(n=e;n.parentNode;)n=n.parentNode;for(ee(t,"name"),n=n.querySelectorAll('input[name="'+Bn(""+t)+'"][type="radio"]'),t=0;t<n.length;t++){var a=n[t];if(a!==e&&a.form===e.form){var o=a[nn]||null;if(!o)throw Error("ReactDOMInput: Mixing React and non-React radio inputs with the same `name` is not supported.");fh(a,o.value,o.defaultValue,o.defaultValue,o.checked,o.defaultChecked,o.type,o.name)}}for(t=0;t<n.length;t++)a=n[t],a.form===e.form&&qg(a)}break e;case"textarea":Kg(e,n.value,n.defaultValue);break e;case"select":t=n.value,t!=null&&vr(e,!!n.multiple,t,!1)}}}function ub(e,t,n){if(zm)return e(t,n);zm=!0;try{var a=e(t);return a}finally{if(zm=!1,(Br!==null||Vr!==null)&&(xr(),Br&&(t=Br,e=Vr,Vr=Br=null,sb(t),e)))for(t=0;t<e.length;t++)sb(e[t])}}function Zl(e,t){var n=e.stateNode;if(n===null)return null;var a=n[nn]||null;if(a===null)return null;n=a[t];e:switch(t){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(a=!a.disabled)||(e=e.type,a=!(e==="button"||e==="input"||e==="select"||e==="textarea")),e=!a;break e;default:e=!1}if(e)return null;if(n&&typeof n!="function")throw Error("Expected `"+t+"` listener to be a function, instead got a value of `"+typeof n+"` type.");return n}function cb(){if(vf)return vf;var e,t=Cm,n=t.length,a,o="value"in Go?Go.value:Go.textContent,r=o.length;for(e=0;e<n&&t[e]===o[e];e++);var c=n-e;for(a=1;a<=c&&t[n-a]===o[r-a];a++);return vf=o.slice(e,1<a?1-a:void 0)}function fc(e){var t=e.keyCode;return"charCode"in e?(e=e.charCode,e===0&&t===13&&(e=13)):e=t,e===10&&(e=13),32<=e||e===13?e:0}function dc(){return!0}function fb(){return!1}function Wt(e){function t(n,a,o,r,c){this._reactName=n,this._targetInst=o,this.type=a,this.nativeEvent=r,this.target=c,this.currentTarget=null;for(var d in e)e.hasOwnProperty(d)&&(n=e[d],this[d]=n?n(r):r[d]);return this.isDefaultPrevented=(r.defaultPrevented!=null?r.defaultPrevented:r.returnValue===!1)?dc:fb,this.isPropagationStopped=fb,this}return be(t.prototype,{preventDefault:function(){this.defaultPrevented=!0;var n=this.nativeEvent;n&&(n.preventDefault?n.preventDefault():typeof n.returnValue!="unknown"&&(n.returnValue=!1),this.isDefaultPrevented=dc)},stopPropagation:function(){var n=this.nativeEvent;n&&(n.stopPropagation?n.stopPropagation():typeof n.cancelBubble!="unknown"&&(n.cancelBubble=!0),this.isPropagationStopped=dc)},persist:function(){},isPersistent:dc}),t}function yR(e){var t=this.nativeEvent;return t.getModifierState?t.getModifierState(e):(e=DD[e])?!!t[e]:!1}function bh(){return yR}function db(e,t){switch(e){case"keyup":return PD.indexOf(t.keyCode)!==-1;case"keydown":return t.keyCode!==eT;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function hb(e){return e=e.detail,typeof e=="object"&&"data"in e?e.data:null}function gR(e,t){switch(e){case"compositionend":return hb(t);case"keypress":return t.which!==nT?null:(oT=!0,aT);case"textInput":return e=t.data,e===aT&&oT?null:e;default:return null}}function bR(e,t){if($r)return e==="compositionend"||!km&&db(e,t)?(e=cb(),vf=Cm=Go=null,$r=!1,e):null;switch(e){case"paste":return null;case"keypress":if(!(t.ctrlKey||t.altKey||t.metaKey)||t.ctrlKey&&t.altKey){if(t.char&&1<t.char.length)return t.char;if(t.which)return String.fromCharCode(t.which)}return null;case"compositionend":return tT&&t.locale!=="ko"?null:t.data;default:return null}}function pb(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t==="input"?!!GD[e.type]:t==="textarea"}function _R(e){if(!Ma)return!1;e="on"+e;var t=e in document;return t||(t=document.createElement("div"),t.setAttribute(e,"return;"),t=typeof t[e]=="function"),t}function mb(e,t,n,a){Br?Vr?Vr.push(a):Vr=[a]:Br=a,t=Kc(t,"onChange"),0<t.length&&(n=new yf("onChange","change",null,n,a),e.push({event:n,listeners:t}))}function SR(e){N_(e,0)}function hc(e){var t=zo(e);if(qg(t))return e}function vb(e,t){if(e==="change")return t}function yb(){js&&(js.detachEvent("onpropertychange",gb),Us=js=null)}function gb(e){if(e.propertyName==="value"&&hc(Us)){var t=[];mb(t,Us,e,gh(e)),ub(SR,t)}}function TR(e,t,n){e==="focusin"?(yb(),js=t,Us=n,js.attachEvent("onpropertychange",gb)):e==="focusout"&&yb()}function OR(e){if(e==="selectionchange"||e==="keyup"||e==="keydown")return hc(Us)}function ER(e,t){if(e==="click")return hc(t)}function wR(e,t){if(e==="input"||e==="change")return hc(t)}function xR(e,t){return e===t&&(e!==0||1/e===1/t)||e!==e&&t!==t}function Kl(e,t){if(qt(e,t))return!0;if(typeof e!="object"||e===null||typeof t!="object"||t===null)return!1;var n=Object.keys(e),a=Object.keys(t);if(n.length!==a.length)return!1;for(a=0;a<n.length;a++){var o=n[a];if(!eo.call(t,o)||!qt(e[o],t[o]))return!1}return!0}function bb(e){for(;e&&e.firstChild;)e=e.firstChild;return e}function _b(e,t){var n=bb(e);e=0;for(var a;n;){if(n.nodeType===3){if(a=e+n.textContent.length,e<=t&&a>=t)return{node:n,offset:t-e};e=a}e:{for(;n;){if(n.nextSibling){n=n.nextSibling;break e}n=n.parentNode}n=void 0}n=bb(n)}}function Sb(e,t){return e&&t?e===t?!0:e&&e.nodeType===3?!1:t&&t.nodeType===3?Sb(e,t.parentNode):"contains"in e?e.contains(t):e.compareDocumentPosition?!!(e.compareDocumentPosition(t)&16):!1:!1}function Tb(e){e=e!=null&&e.ownerDocument!=null&&e.ownerDocument.defaultView!=null?e.ownerDocument.defaultView:window;for(var t=sc(e.document);t instanceof e.HTMLIFrameElement;){try{var n=typeof t.contentWindow.location.href=="string"}catch{n=!1}if(n)e=t.contentWindow;else break;t=sc(e.document)}return t}function _h(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t&&(t==="input"&&(e.type==="text"||e.type==="search"||e.type==="tel"||e.type==="url"||e.type==="password")||t==="textarea"||e.contentEditable==="true")}function Ob(e,t,n){var a=n.window===n?n.document:n.nodeType===9?n:n.ownerDocument;Hm||Pr==null||Pr!==sc(a)||(a=Pr,"selectionStart"in a&&_h(a)?a={start:a.selectionStart,end:a.selectionEnd}:(a=(a.ownerDocument&&a.ownerDocument.defaultView||window).getSelection(),a={anchorNode:a.anchorNode,anchorOffset:a.anchorOffset,focusNode:a.focusNode,focusOffset:a.focusOffset}),ks&&Kl(ks,a)||(ks=a,a=Kc(Nm,"onSelect"),0<a.length&&(t=new yf("onSelect","select",null,t,n),e.push({event:t,listeners:a}),t.target=Pr)))}function _i(e,t){var n={};return n[e.toLowerCase()]=t.toLowerCase(),n["Webkit"+e]="webkit"+t,n["Moz"+e]="moz"+t,n}function Si(e){if(Lm[e])return Lm[e];if(!qr[e])return e;var t=qr[e],n;for(n in t)if(t.hasOwnProperty(n)&&n in rT)return Lm[e]=t[n];return e}function sa(e,t){fT.set(e,t),$(t,[e])}function yn(e,t){if(typeof e=="object"&&e!==null){var n=Vm.get(e);return n!==void 0?n:(t={value:e,source:t,stack:Vg(t)},Vm.set(e,t),t)}return{value:e,source:t,stack:Vg(t)}}function pc(){for(var e=Gr,t=$m=Gr=0;t<e;){var n=qn[t];qn[t++]=null;var a=qn[t];qn[t++]=null;var o=qn[t];qn[t++]=null;var r=qn[t];if(qn[t++]=null,a!==null&&o!==null){var c=a.pending;c===null?o.next=o:(o.next=c.next,c.next=o),a.pending=o}r!==0&&Eb(n,o,r)}}function mc(e,t,n,a){qn[Gr++]=e,qn[Gr++]=t,qn[Gr++]=n,qn[Gr++]=a,$m|=a,e.lanes|=a,e=e.alternate,e!==null&&(e.lanes|=a)}function Sh(e,t,n,a){return mc(e,t,n,a),vc(e)}function Ft(e,t){return mc(e,null,null,t),vc(e)}function Eb(e,t,n){e.lanes|=n;var a=e.alternate;a!==null&&(a.lanes|=n);for(var o=!1,r=e.return;r!==null;)r.childLanes|=n,a=r.alternate,a!==null&&(a.childLanes|=n),r.tag===22&&(e=r.stateNode,e===null||e._visibility&bf||(o=!0)),e=r,r=r.return;return e.tag===3?(r=e.stateNode,o&&t!==null&&(o=31-$t(n),e=r.hiddenUpdates,a=e[o],a===null?e[o]=[t]:a.push(t),t.lane=n|536870912),r):null}function vc(e){if(au>hC)throw Zi=au=0,ou=yv=null,Error("Maximum update depth exceeded. This can happen when a component repeatedly calls setState inside componentWillUpdate or componentDidUpdate. React limits the number of nested updates to prevent infinite loops.");Zi>pC&&(Zi=0,ou=null,console.error("Maximum update depth exceeded. This can happen when a component calls setState inside useEffect, but useEffect either doesn't have a dependency array, or one of the dependencies changes on every render.")),e.alternate===null&&(e.flags&4098)!==0&&z_(e);for(var t=e,n=t.return;n!==null;)t.alternate===null&&(t.flags&4098)!==0&&z_(e),t=n,n=t.return;return t.tag===3?t.stateNode:null}function Ti(e){if(Gn===null)return e;var t=Gn(e);return t===void 0?e:t.current}function Th(e){if(Gn===null)return e;var t=Gn(e);return t===void 0?e!=null&&typeof e.render=="function"&&(t=Ti(e.render),e.render!==t)?(t={$$typeof:Es,render:t},e.displayName!==void 0&&(t.displayName=e.displayName),t):e:t.current}function wb(e,t){if(Gn===null)return!1;var n=e.elementType;t=t.type;var a=!1,o=typeof t=="object"&&t!==null?t.$$typeof:null;switch(e.tag){case 1:typeof t=="function"&&(a=!0);break;case 0:(typeof t=="function"||o===Sn)&&(a=!0);break;case 11:(o===Es||o===Sn)&&(a=!0);break;case 14:case 15:(o===sf||o===Sn)&&(a=!0);break;default:return!1}return!!(a&&(e=Gn(n),e!==void 0&&e===Gn(t)))}function xb(e){Gn!==null&&typeof WeakSet=="function"&&(Yr===null&&(Yr=new WeakSet),Yr.add(e))}function Oh(e,t,n){var a=e.alternate,o=e.child,r=e.sibling,c=e.tag,d=e.type,v=null;switch(c){case 0:case 15:case 1:v=d;break;case 11:v=d.render}if(Gn===null)throw Error("Expected resolveFamily to be set during hot reload.");var g=!1;d=!1,v!==null&&(v=Gn(v),v!==void 0&&(n.has(v)?d=!0:t.has(v)&&(c===1?d=!0:g=!0))),Yr!==null&&(Yr.has(e)||a!==null&&Yr.has(a))&&(d=!0),d&&(e._debugNeedsRemount=!0),(d||g)&&(a=Ft(e,2),a!==null&&it(a,e,2)),o===null||d||Oh(o,t,n),r!==null&&Oh(r,t,n)}function AR(e,t,n,a){this.tag=e,this.key=n,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.refCleanup=this.ref=null,this.pendingProps=t,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=a,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null,this.actualDuration=-0,this.actualStartTime=-1.1,this.treeBaseDuration=this.selfBaseDuration=-0,this._debugTask=this._debugStack=this._debugOwner=this._debugInfo=null,this._debugNeedsRemount=!1,this._debugHookTypes=null,hT||typeof Object.preventExtensions!="function"||Object.preventExtensions(this)}function Eh(e){return e=e.prototype,!(!e||!e.isReactComponent)}function Ga(e,t){var n=e.alternate;switch(n===null?(n=O(e.tag,t,e.key,e.mode),n.elementType=e.elementType,n.type=e.type,n.stateNode=e.stateNode,n._debugOwner=e._debugOwner,n._debugStack=e._debugStack,n._debugTask=e._debugTask,n._debugHookTypes=e._debugHookTypes,n.alternate=e,e.alternate=n):(n.pendingProps=t,n.type=e.type,n.flags=0,n.subtreeFlags=0,n.deletions=null,n.actualDuration=-0,n.actualStartTime=-1.1),n.flags=e.flags&65011712,n.childLanes=e.childLanes,n.lanes=e.lanes,n.child=e.child,n.memoizedProps=e.memoizedProps,n.memoizedState=e.memoizedState,n.updateQueue=e.updateQueue,t=e.dependencies,n.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext,_debugThenableState:t._debugThenableState},n.sibling=e.sibling,n.index=e.index,n.ref=e.ref,n.refCleanup=e.refCleanup,n.selfBaseDuration=e.selfBaseDuration,n.treeBaseDuration=e.treeBaseDuration,n._debugInfo=e._debugInfo,n._debugNeedsRemount=e._debugNeedsRemount,n.tag){case 0:case 15:n.type=Ti(e.type);break;case 1:n.type=Ti(e.type);break;case 11:n.type=Th(e.type)}return n}function Ab(e,t){e.flags&=65011714;var n=e.alternate;return n===null?(e.childLanes=0,e.lanes=t,e.child=null,e.subtreeFlags=0,e.memoizedProps=null,e.memoizedState=null,e.updateQueue=null,e.dependencies=null,e.stateNode=null,e.selfBaseDuration=0,e.treeBaseDuration=0):(e.childLanes=n.childLanes,e.lanes=n.lanes,e.child=n.child,e.subtreeFlags=0,e.deletions=null,e.memoizedProps=n.memoizedProps,e.memoizedState=n.memoizedState,e.updateQueue=n.updateQueue,e.type=n.type,t=n.dependencies,e.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext,_debugThenableState:t._debugThenableState},e.selfBaseDuration=n.selfBaseDuration,e.treeBaseDuration=n.treeBaseDuration),e}function wh(e,t,n,a,o,r){var c=0,d=e;if(typeof e=="function")Eh(e)&&(c=1),d=Ti(d);else if(typeof e=="string")c=q(),c=Az(e,n,c)?26:e==="html"||e==="head"||e==="body"?27:5;else e:switch(e){case mm:return t=O(31,n,t,o),t.elementType=mm,t.lanes=r,t;case jr:return Oi(n.children,o,r,t);case lf:c=8,o|=Bt,o|=fa;break;case fm:return e=n,a=o,typeof e.id!="string"&&console.error('Profiler must specify an "id" of type `string` as a prop. Received the type `%s` instead.',typeof e.id),t=O(12,e,t,a|Mt),t.elementType=fm,t.lanes=r,t.stateNode={effectDuration:0,passiveEffectDuration:0},t;case hm:return t=O(13,n,t,o),t.elementType=hm,t.lanes=r,t;case pm:return t=O(19,n,t,o),t.elementType=pm,t.lanes=r,t;default:if(typeof e=="object"&&e!==null)switch(e.$$typeof){case $z:case Aa:c=10;break e;case dm:c=9;break e;case Es:c=11,d=Th(d);break e;case sf:c=14;break e;case Sn:c=16,d=null;break e}d="",(e===void 0||typeof e=="object"&&e!==null&&Object.keys(e).length===0)&&(d+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports."),e===null?n="null":Ct(e)?n="array":e!==void 0&&e.$$typeof===Bo?(n="<"+(Ne(e.type)||"Unknown")+" />",d=" Did you accidentally export a JSX literal instead of a component?"):n=typeof e,(c=a?Vt(a):null)&&(d+=`

Check the render method of \``+c+"`."),c=29,n=Error("Element type is invalid: expected a string (for built-in components) or a class/function (for composite components) but got: "+(n+"."+d)),d=null}return t=O(c,n,t,o),t.elementType=e,t.type=d,t.lanes=r,t._debugOwner=a,t}function yc(e,t,n){return t=wh(e.type,e.key,e.props,e._owner,t,n),t._debugOwner=e._owner,t._debugStack=e._debugStack,t._debugTask=e._debugTask,t}function Oi(e,t,n,a){return e=O(7,e,a,t),e.lanes=n,e}function xh(e,t,n){return e=O(6,e,null,t),e.lanes=n,e}function Ah(e,t,n){return t=O(4,e.children!==null?e.children:[],e.key,t),t.lanes=n,t.stateNode={containerInfo:e.containerInfo,pendingChildren:null,implementation:e.implementation},t}function Ei(e,t){wi(),Xr[Ir++]=Sf,Xr[Ir++]=_f,_f=e,Sf=t}function Rb(e,t,n){wi(),Yn[Xn++]=ao,Yn[Xn++]=oo,Yn[Xn++]=Hi,Hi=e;var a=ao;e=oo;var o=32-$t(a)-1;a&=~(1<<o),n+=1;var r=32-$t(t)+o;if(30<r){var c=o-o%5;r=(a&(1<<c)-1).toString(32),a>>=c,o-=c,ao=1<<32-$t(t)+o|n<<o|a,oo=r+e}else ao=1<<r|n<<o|a,oo=e}function Rh(e){wi(),e.return!==null&&(Ei(e,1),Rb(e,1,0))}function zh(e){for(;e===_f;)_f=Xr[--Ir],Xr[Ir]=null,Sf=Xr[--Ir],Xr[Ir]=null;for(;e===Hi;)Hi=Yn[--Xn],Yn[Xn]=null,oo=Yn[--Xn],Yn[Xn]=null,ao=Yn[--Xn],Yn[Xn]=null}function wi(){Ae||console.error("Expected to be hydrating. This is a bug in React. Please file an issue.")}function xi(e,t){if(e.return===null){if(In===null)In={fiber:e,children:[],serverProps:void 0,serverTail:[],distanceFromLeaf:t};else{if(In.fiber!==e)throw Error("Saw multiple hydration diff roots in a pass. This is a bug in React.");In.distanceFromLeaf>t&&(In.distanceFromLeaf=t)}return In}var n=xi(e.return,t+1).children;return 0<n.length&&n[n.length-1].fiber===e?(n=n[n.length-1],n.distanceFromLeaf>t&&(n.distanceFromLeaf=t),n):(t={fiber:e,children:[],serverProps:void 0,serverTail:[],distanceFromLeaf:t},n.push(t),t)}function Dh(e,t){io||(e=xi(e,0),e.serverProps=null,t!==null&&(t=K_(t),e.serverTail.push(t)))}function Ai(e){var t="",n=In;throw n!==null&&(In=null,t=vh(n)),Fl(yn(Error(`Hydration failed because the server rendered HTML didn't match the client. As a result this tree will be regenerated on the client. This can happen if a SSR-ed Client Component used:

- A server/client branch \`if (typeof window !== 'undefined')\`.
- Variable input such as \`Date.now()\` or \`Math.random()\` which changes each time it's called.
- Date formatting in a user's locale which doesn't match the server.
- External changing data without sending a snapshot of it along with the HTML.
- Invalid HTML tag nesting.

It can also happen if the client has a browser extension installed which messes with the HTML before React loaded.

https://react.dev/link/hydration-mismatch`+t),e)),Pm}function zb(e){var t=e.stateNode,n=e.type,a=e.memoizedProps;switch(t[Lt]=e,t[nn]=a,Ip(n,a),n){case"dialog":Ee("cancel",t),Ee("close",t);break;case"iframe":case"object":case"embed":Ee("load",t);break;case"video":case"audio":for(n=0;n<iu.length;n++)Ee(iu[n],t);break;case"source":Ee("error",t);break;case"img":case"image":case"link":Ee("error",t),Ee("load",t);break;case"details":Ee("toggle",t);break;case"input":J("input",a),Ee("invalid",t),Gg(t,a),Yg(t,a.value,a.defaultValue,a.checked,a.defaultChecked,a.type,a.name,!0),lc(t);break;case"option":Xg(t,a);break;case"select":J("select",a),Ee("invalid",t),Qg(t,a);break;case"textarea":J("textarea",a),Ee("invalid",t),Zg(t,a),Jg(t,a.value,a.defaultValue,a.children),lc(t)}n=a.children,typeof n!="string"&&typeof n!="number"&&typeof n!="bigint"||t.textContent===""+n||a.suppressHydrationWarning===!0||V_(t.textContent,n)?(a.popover!=null&&(Ee("beforetoggle",t),Ee("toggle",t)),a.onScroll!=null&&Ee("scroll",t),a.onScrollEnd!=null&&Ee("scrollend",t),a.onClick!=null&&(t.onclick=Jc),t=!0):t=!1,t||Ai(e)}function Db(e){for(Gt=e.return;Gt;)switch(Gt.tag){case 5:case 13:ja=!1;return;case 27:case 3:ja=!0;return;default:Gt=Gt.return}}function Jl(e){if(e!==Gt)return!1;if(!Ae)return Db(e),Ae=!0,!1;var t=e.tag,n;if((n=t!==3&&t!==27)&&((n=t===5)&&(n=e.type,n=!(n!=="form"&&n!=="button")||Wp(e.type,e.memoizedProps)),n=!n),n&&tt){for(n=tt;n;){var a=xi(e,0),o=K_(n);a.serverTail.push(o),n=o.type==="Suspense"?W_(n):$n(n.nextSibling)}Ai(e)}if(Db(e),t===13){if(e=e.memoizedState,e=e!==null?e.dehydrated:null,!e)throw Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");tt=W_(e)}else t===27?(t=tt,Lo(e.type)?(e=zv,zv=null,tt=e):tt=t):tt=Gt?$n(e.stateNode.nextSibling):null;return!0}function Wl(){tt=Gt=null,io=Ae=!1}function Cb(){var e=Li;return e!==null&&(It===null?It=e:It.push.apply(It,e),Li=null),e}function Fl(e){Li===null?Li=[e]:Li.push(e)}function Mb(){var e=In;if(e!==null){In=null;for(var t=vh(e);0<e.children.length;)e=e.children[0];W(e.fiber,function(){console.error(`A tree hydrated but some attributes of the server rendered HTML didn't match the client properties. This won't be patched up. This can happen if a SSR-ed Client Component used:

- A server/client branch \`if (typeof window !== 'undefined')\`.
- Variable input such as \`Date.now()\` or \`Math.random()\` which changes each time it's called.
- Date formatting in a user's locale which doesn't match the server.
- External changing data without sending a snapshot of it along with the HTML.
- Invalid HTML tag nesting.

It can also happen if the client has a browser extension installed which messes with the HTML before React loaded.

%s%s`,"https://react.dev/link/hydration-mismatch",t)})}}function gc(){Qr=Tf=null,Zr=!1}function Do(e,t,n){Re(qm,t._currentValue,e),t._currentValue=n,Re(Gm,t._currentRenderer,e),t._currentRenderer!==void 0&&t._currentRenderer!==null&&t._currentRenderer!==yT&&console.error("Detected multiple renderers concurrently rendering the same context provider. This is currently unsupported."),t._currentRenderer=yT}function Ya(e,t){e._currentValue=qm.current;var n=Gm.current;ue(Gm,t),e._currentRenderer=n,ue(qm,t)}function Ch(e,t,n){for(;e!==null;){var a=e.alternate;if((e.childLanes&t)!==t?(e.childLanes|=t,a!==null&&(a.childLanes|=t)):a!==null&&(a.childLanes&t)!==t&&(a.childLanes|=t),e===n)break;e=e.return}e!==n&&console.error("Expected to find the propagation root when scheduling context work. This error is likely caused by a bug in React. Please file an issue.")}function Mh(e,t,n,a){var o=e.child;for(o!==null&&(o.return=e);o!==null;){var r=o.dependencies;if(r!==null){var c=o.child;r=r.firstContext;e:for(;r!==null;){var d=r;r=o;for(var v=0;v<t.length;v++)if(d.context===t[v]){r.lanes|=n,d=r.alternate,d!==null&&(d.lanes|=n),Ch(r.return,n,e),a||(c=null);break e}r=d.next}}else if(o.tag===18){if(c=o.return,c===null)throw Error("We just came from a parent so we must have had a parent. This is a bug in React.");c.lanes|=n,r=c.alternate,r!==null&&(r.lanes|=n),Ch(c,n,e),c=null}else c=o.child;if(c!==null)c.return=o;else for(c=o;c!==null;){if(c===e){c=null;break}if(o=c.sibling,o!==null){o.return=c.return,c=o;break}c=c.return}o=c}}function es(e,t,n,a){e=null;for(var o=t,r=!1;o!==null;){if(!r){if((o.flags&524288)!==0)r=!0;else if((o.flags&262144)!==0)break}if(o.tag===10){var c=o.alternate;if(c===null)throw Error("Should have a current fiber. This is a bug in React.");if(c=c.memoizedProps,c!==null){var d=o.type;qt(o.pendingProps.value,c.value)||(e!==null?e.push(d):e=[d])}}else if(o===uf.current){if(c=o.alternate,c===null)throw Error("Should have a current fiber. This is a bug in React.");c.memoizedState.memoizedState!==o.memoizedState.memoizedState&&(e!==null?e.push(uu):e=[uu])}o=o.return}e!==null&&Mh(t,e,n,a),t.flags|=262144}function bc(e){for(e=e.firstContext;e!==null;){if(!qt(e.context._currentValue,e.memoizedValue))return!0;e=e.next}return!1}function Ri(e){Tf=e,Qr=null,e=e.dependencies,e!==null&&(e.firstContext=null)}function Je(e){return Zr&&console.error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo()."),jb(Tf,e)}function _c(e,t){return Tf===null&&Ri(e),jb(e,t)}function jb(e,t){var n=t._currentValue;if(t={context:t,memoizedValue:n,next:null},Qr===null){if(e===null)throw Error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");Qr=t,e.dependencies={lanes:0,firstContext:t,_debugThenableState:null},e.flags|=524288}else Qr=Qr.next=t;return n}function jh(){return{controller:new WD,data:new Map,refCount:0}}function zi(e){e.controller.signal.aborted&&console.warn("A cache instance was retained after it was already freed. This likely indicates a bug in React."),e.refCount++}function ts(e){e.refCount--,0>e.refCount&&console.warn("A cache instance was released after it was already freed. This likely indicates a bug in React."),e.refCount===0&&FD(eC,function(){e.controller.abort()})}function Xa(){var e=Bi;return Bi=0,e}function Sc(e){var t=Bi;return Bi=e,t}function ns(e){var t=Bi;return Bi+=e,t}function Uh(e){an=Kr(),0>e.actualStartTime&&(e.actualStartTime=an)}function kh(e){if(0<=an){var t=Kr()-an;e.actualDuration+=t,e.selfBaseDuration=t,an=-1}}function Ub(e){if(0<=an){var t=Kr()-an;e.actualDuration+=t,an=-1}}function _a(){if(0<=an){var e=Kr()-an;an=-1,Bi+=e}}function Sa(){an=Kr()}function Tc(e){for(var t=e.child;t;)e.actualDuration+=t.actualDuration,t=t.sibling}function RR(e,t){if(Ns===null){var n=Ns=[];Ym=0,Vi=qp(),Jr={status:"pending",value:void 0,then:function(a){n.push(a)}}}return Ym++,t.then(kb,kb),t}function kb(){if(--Ym===0&&Ns!==null){Jr!==null&&(Jr.status="fulfilled");var e=Ns;Ns=null,Vi=0,Jr=null;for(var t=0;t<e.length;t++)(0,e[t])()}}function zR(e,t){var n=[],a={status:"pending",value:null,reason:null,then:function(o){n.push(o)}};return e.then(function(){a.status="fulfilled",a.value=t;for(var o=0;o<n.length;o++)(0,n[o])(t)},function(o){for(a.status="rejected",a.reason=o,o=0;o<n.length;o++)(0,n[o])(void 0)}),a}function Nh(){var e=$i.current;return e!==null?e:qe.pooledCache}function Oc(e,t){t===null?Re($i,$i.current,e):Re($i,t.pool,e)}function Nb(){var e=Nh();return e===null?null:{parent:yt._currentValue,pool:e}}function Hb(){return{didWarnAboutUncachedPromise:!1,thenables:[]}}function Lb(e){return e=e.status,e==="fulfilled"||e==="rejected"}function Ec(){}function Bb(e,t,n){M.actQueue!==null&&(M.didUsePromise=!0);var a=e.thenables;switch(n=a[n],n===void 0?a.push(t):n!==t&&(e.didWarnAboutUncachedPromise||(e.didWarnAboutUncachedPromise=!0,console.error("A component was suspended by an uncached promise. Creating promises inside a Client Component or hook is not yet supported, except via a Suspense-compatible library or framework.")),t.then(Ec,Ec),t=n),t.status){case"fulfilled":return t.value;case"rejected":throw e=t.reason,$b(e),e;default:if(typeof t.status=="string")t.then(Ec,Ec);else{if(e=qe,e!==null&&100<e.shellSuspendCounter)throw Error("An unknown Component is an async Client Component. Only Server Components can be async at the moment. This error is often caused by accidentally adding `'use client'` to a module that was originally written for the server.");e=t,e.status="pending",e.then(function(o){if(t.status==="pending"){var r=t;r.status="fulfilled",r.value=o}},function(o){if(t.status==="pending"){var r=t;r.status="rejected",r.reason=o}})}switch(t.status){case"fulfilled":return t.value;case"rejected":throw e=t.reason,$b(e),e}throw Gs=t,Rf=!0,qs}}function Vb(){if(Gs===null)throw Error("Expected a suspended thenable. This is a bug in React. Please file an issue.");var e=Gs;return Gs=null,Rf=!1,e}function $b(e){if(e===qs||e===Af)throw Error("Hooks are not supported inside an async component. This error is often caused by accidentally adding `'use client'` to a module that was originally written for the server.")}function Hh(e){e.updateQueue={baseState:e.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,lanes:0,hiddenCallbacks:null},callbacks:null}}function Lh(e,t){e=e.updateQueue,t.updateQueue===e&&(t.updateQueue={baseState:e.baseState,firstBaseUpdate:e.firstBaseUpdate,lastBaseUpdate:e.lastBaseUpdate,shared:e.shared,callbacks:null})}function Co(e){return{lane:e,tag:TT,payload:null,callback:null,next:null}}function Mo(e,t,n){var a=e.updateQueue;if(a===null)return null;if(a=a.shared,Qm===a&&!wT){var o=te(e);console.error(`An update (setState, replaceState, or forceUpdate) was scheduled from inside an update function. Update functions should be pure, with zero side-effects. Consider using componentDidUpdate or a callback.

Please update the following component: %s`,o),wT=!0}return(je&Xt)!==On?(o=a.pending,o===null?t.next=t:(t.next=o.next,o.next=t),a.pending=t,t=vc(e),Eb(e,null,n),t):(mc(e,a,t,n),vc(e))}function as(e,t,n){if(t=t.updateQueue,t!==null&&(t=t.shared,(n&4194048)!==0)){var a=t.lanes;a&=e.pendingLanes,n|=a,t.lanes=n,ic(e,n)}}function wc(e,t){var n=e.updateQueue,a=e.alternate;if(a!==null&&(a=a.updateQueue,n===a)){var o=null,r=null;if(n=n.firstBaseUpdate,n!==null){do{var c={lane:n.lane,tag:n.tag,payload:n.payload,callback:null,next:null};r===null?o=r=c:r=r.next=c,n=n.next}while(n!==null);r===null?o=r=t:r=r.next=t}else o=r=t;n={baseState:a.baseState,firstBaseUpdate:o,lastBaseUpdate:r,shared:a.shared,callbacks:a.callbacks},e.updateQueue=n;return}e=n.lastBaseUpdate,e===null?n.firstBaseUpdate=t:e.next=t,n.lastBaseUpdate=t}function os(){if(Zm){var e=Jr;if(e!==null)throw e}}function is(e,t,n,a){Zm=!1;var o=e.updateQueue;Xo=!1,Qm=o.shared;var r=o.firstBaseUpdate,c=o.lastBaseUpdate,d=o.shared.pending;if(d!==null){o.shared.pending=null;var v=d,g=v.next;v.next=null,c===null?r=g:c.next=g,c=v;var R=e.alternate;R!==null&&(R=R.updateQueue,d=R.lastBaseUpdate,d!==c&&(d===null?R.firstBaseUpdate=g:d.next=g,R.lastBaseUpdate=v))}if(r!==null){var j=o.baseState;c=0,R=g=v=null,d=r;do{var A=d.lane&-536870913,U=A!==d.lane;if(U?(Oe&A)===A:(a&A)===A){A!==0&&A===Vi&&(Zm=!0),R!==null&&(R=R.next={lane:0,tag:d.tag,payload:d.payload,callback:null,next:null});e:{A=e;var Z=d,re=t,Ge=n;switch(Z.tag){case OT:if(Z=Z.payload,typeof Z=="function"){Zr=!0;var we=Z.call(Ge,j,re);if(A.mode&Bt){Ce(!0);try{Z.call(Ge,j,re)}finally{Ce(!1)}}Zr=!1,j=we;break e}j=Z;break e;case Im:A.flags=A.flags&-65537|128;case TT:if(we=Z.payload,typeof we=="function"){if(Zr=!0,Z=we.call(Ge,j,re),A.mode&Bt){Ce(!0);try{we.call(Ge,j,re)}finally{Ce(!1)}}Zr=!1}else Z=we;if(Z==null)break e;j=be({},j,Z);break e;case ET:Xo=!0}}A=d.callback,A!==null&&(e.flags|=64,U&&(e.flags|=8192),U=o.callbacks,U===null?o.callbacks=[A]:U.push(A))}else U={lane:A,tag:d.tag,payload:d.payload,callback:d.callback,next:null},R===null?(g=R=U,v=j):R=R.next=U,c|=A;if(d=d.next,d===null){if(d=o.shared.pending,d===null)break;U=d,d=U.next,U.next=null,o.lastBaseUpdate=U,o.shared.pending=null}}while(!0);R===null&&(v=j),o.baseState=v,o.firstBaseUpdate=g,o.lastBaseUpdate=R,r===null&&(o.shared.lanes=0),Ko|=c,e.lanes=c,e.memoizedState=j}Qm=null}function Pb(e,t){if(typeof e!="function")throw Error("Invalid argument passed as callback. Expected a function. Instead received: "+e);e.call(t)}function DR(e,t){var n=e.shared.hiddenCallbacks;if(n!==null)for(e.shared.hiddenCallbacks=null,e=0;e<n.length;e++)Pb(n[e],t)}function qb(e,t){var n=e.callbacks;if(n!==null)for(e.callbacks=null,e=0;e<n.length;e++)Pb(n[e],t)}function Gb(e,t){var n=Na;Re(zf,n,e),Re(Wr,t,e),Na=n|t.baseLanes}function Bh(e){Re(zf,Na,e),Re(Wr,Wr.current,e)}function Vh(e){Na=zf.current,ue(Wr,e),ue(zf,e)}function Te(){var e=C;Kn===null?Kn=[e]:Kn.push(e)}function V(){var e=C;if(Kn!==null&&(lo++,Kn[lo]!==e)){var t=te(ce);if(!xT.has(t)&&(xT.add(t),Kn!==null)){for(var n="",a=0;a<=lo;a++){var o=Kn[a],r=a===lo?e:o;for(o=a+1+". "+o;30>o.length;)o+=" ";o+=r+`
`,n+=o}console.error(`React has detected a change in the order of Hooks called by %s. This will lead to bugs and errors if not fixed. For more information, read the Rules of Hooks: https://react.dev/link/rules-of-hooks

   Previous render            Next render
   ------------------------------------------------------
%s   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
`,t,n)}}}function br(e){e==null||Ct(e)||console.error("%s received a final argument that is not an array (instead, received `%s`). When specified, the final argument must be an array.",C,typeof e)}function xc(){var e=te(ce);RT.has(e)||(RT.add(e),console.error("ReactDOM.useFormState has been renamed to React.useActionState. Please update %s to use React.useActionState.",e))}function ot(){throw Error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://react.dev/link/invalid-hook-call for tips about how to debug and fix this problem.`)}function $h(e,t){if(Xs)return!1;if(t===null)return console.error("%s received a final argument during this render, but not during the previous render. Even though the final argument is optional, its type cannot change between renders.",C),!1;e.length!==t.length&&console.error(`The final argument passed to %s changed size between renders. The order and size of this array must remain constant.

Previous: %s
Incoming: %s`,C,"["+t.join(", ")+"]","["+e.join(", ")+"]");for(var n=0;n<t.length&&n<e.length;n++)if(!qt(e[n],t[n]))return!1;return!0}function Ph(e,t,n,a,o,r){Io=r,ce=t,Kn=e!==null?e._debugHookTypes:null,lo=-1,Xs=e!==null&&e.type!==t.type,(Object.prototype.toString.call(n)==="[object AsyncFunction]"||Object.prototype.toString.call(n)==="[object AsyncGeneratorFunction]")&&(r=te(ce),Km.has(r)||(Km.add(r),console.error("%s is an async Client Component. Only Server Components can be async at the moment. This error is often caused by accidentally adding `'use client'` to a module that was originally written for the server.",r===null?"An unknown Component":"<"+r+">"))),t.memoizedState=null,t.updateQueue=null,t.lanes=0,M.H=e!==null&&e.memoizedState!==null?Wm:Kn!==null?zT:Jm,qi=r=(t.mode&Bt)!==Xe;var c=Fm(n,a,o);if(qi=!1,el&&(c=qh(t,n,a,o)),r){Ce(!0);try{c=qh(t,n,a,o)}finally{Ce(!1)}}return Yb(e,t),c}function Yb(e,t){t._debugHookTypes=Kn,t.dependencies===null?ro!==null&&(t.dependencies={lanes:0,firstContext:null,_debugThenableState:ro}):t.dependencies._debugThenableState=ro,M.H=Mf;var n=$e!==null&&$e.next!==null;if(Io=0,Kn=C=ht=$e=ce=null,lo=-1,e!==null&&(e.flags&65011712)!==(t.flags&65011712)&&console.error("Internal React error: Expected static flag was missing. Please notify the React team."),Df=!1,Ys=0,ro=null,n)throw Error("Rendered fewer hooks than expected. This may be caused by an accidental early return statement.");e===null||Tt||(e=e.dependencies,e!==null&&bc(e)&&(Tt=!0)),Rf?(Rf=!1,e=!0):e=!1,e&&(t=te(t)||"Unknown",AT.has(t)||Km.has(t)||(AT.add(t),console.error("`use` was called from inside a try/catch block. This is not allowed and can lead to unexpected behavior. To handle errors triggered by `use`, wrap your component in a error boundary.")))}function qh(e,t,n,a){ce=e;var o=0;do{if(el&&(ro=null),Ys=0,el=!1,o>=nC)throw Error("Too many re-renders. React limits the number of renders to prevent an infinite loop.");if(o+=1,Xs=!1,ht=$e=null,e.updateQueue!=null){var r=e.updateQueue;r.lastEffect=null,r.events=null,r.stores=null,r.memoCache!=null&&(r.memoCache.index=0)}lo=-1,M.H=DT,r=Fm(t,n,a)}while(el);return r}function CR(){var e=M.H,t=e.useState()[0];return t=typeof t.then=="function"?rs(t):t,e=e.useState()[0],($e!==null?$e.memoizedState:null)!==e&&(ce.flags|=1024),t}function Gh(){var e=Cf!==0;return Cf=0,e}function Yh(e,t,n){t.updateQueue=e.updateQueue,t.flags=(t.mode&fa)!==Xe?t.flags&-402655237:t.flags&-2053,e.lanes&=~n}function Xh(e){if(Df){for(e=e.memoizedState;e!==null;){var t=e.queue;t!==null&&(t.pending=null),e=e.next}Df=!1}Io=0,Kn=ht=$e=ce=null,lo=-1,C=null,el=!1,Ys=Cf=0,ro=null}function en(){var e={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return ht===null?ce.memoizedState=ht=e:ht=ht.next=e,ht}function He(){if($e===null){var e=ce.alternate;e=e!==null?e.memoizedState:null}else e=$e.next;var t=ht===null?ce.memoizedState:ht.next;if(t!==null)ht=t,$e=e;else{if(e===null)throw ce.alternate===null?Error("Update hook called on initial render. This is likely a bug in React. Please file an issue."):Error("Rendered more hooks than during the previous render.");$e=e,e={memoizedState:$e.memoizedState,baseState:$e.baseState,baseQueue:$e.baseQueue,queue:$e.queue,next:null},ht===null?ce.memoizedState=ht=e:ht=ht.next=e}return ht}function Ih(){return{lastEffect:null,events:null,stores:null,memoCache:null}}function rs(e){var t=Ys;return Ys+=1,ro===null&&(ro=Hb()),e=Bb(ro,e,t),t=ce,(ht===null?t.memoizedState:ht.next)===null&&(t=t.alternate,M.H=t!==null&&t.memoizedState!==null?Wm:Jm),e}function jo(e){if(e!==null&&typeof e=="object"){if(typeof e.then=="function")return rs(e);if(e.$$typeof===Aa)return Je(e)}throw Error("An unsupported type was passed to use(): "+String(e))}function Di(e){var t=null,n=ce.updateQueue;if(n!==null&&(t=n.memoCache),t==null){var a=ce.alternate;a!==null&&(a=a.updateQueue,a!==null&&(a=a.memoCache,a!=null&&(t={data:a.data.map(function(o){return o.slice()}),index:0})))}if(t==null&&(t={data:[],index:0}),n===null&&(n=Ih(),ce.updateQueue=n),n.memoCache=t,n=t.data[t.index],n===void 0||Xs)for(n=t.data[t.index]=Array(e),a=0;a<e;a++)n[a]=Pz;else n.length!==e&&console.error("Expected a constant size argument for each invocation of useMemoCache. The previous cache was allocated with size %s but size %s was requested.",n.length,e);return t.index++,n}function ua(e,t){return typeof t=="function"?t(e):t}function Qh(e,t,n){var a=en();if(n!==void 0){var o=n(t);if(qi){Ce(!0);try{n(t)}finally{Ce(!1)}}}else o=t;return a.memoizedState=a.baseState=o,e={pending:null,lanes:0,dispatch:null,lastRenderedReducer:e,lastRenderedState:o},a.queue=e,e=e.dispatch=kR.bind(null,ce,e),[a.memoizedState,e]}function _r(e){var t=He();return Zh(t,$e,e)}function Zh(e,t,n){var a=e.queue;if(a===null)throw Error("Should have a queue. You are likely calling Hooks conditionally, which is not allowed. (https://react.dev/link/invalid-hook-call)");a.lastRenderedReducer=n;var o=e.baseQueue,r=a.pending;if(r!==null){if(o!==null){var c=o.next;o.next=r.next,r.next=c}t.baseQueue!==o&&console.error("Internal error: Expected work-in-progress queue to be a clone. This is a bug in React."),t.baseQueue=o=r,a.pending=null}if(r=e.baseState,o===null)e.memoizedState=r;else{t=o.next;var d=c=null,v=null,g=t,R=!1;do{var j=g.lane&-536870913;if(j!==g.lane?(Oe&j)===j:(Io&j)===j){var A=g.revertLane;if(A===0)v!==null&&(v=v.next={lane:0,revertLane:0,action:g.action,hasEagerState:g.hasEagerState,eagerState:g.eagerState,next:null}),j===Vi&&(R=!0);else if((Io&A)===A){g=g.next,A===Vi&&(R=!0);continue}else j={lane:0,revertLane:g.revertLane,action:g.action,hasEagerState:g.hasEagerState,eagerState:g.eagerState,next:null},v===null?(d=v=j,c=r):v=v.next=j,ce.lanes|=A,Ko|=A;j=g.action,qi&&n(r,j),r=g.hasEagerState?g.eagerState:n(r,j)}else A={lane:j,revertLane:g.revertLane,action:g.action,hasEagerState:g.hasEagerState,eagerState:g.eagerState,next:null},v===null?(d=v=A,c=r):v=v.next=A,ce.lanes|=j,Ko|=j;g=g.next}while(g!==null&&g!==t);if(v===null?c=r:v.next=d,!qt(r,e.memoizedState)&&(Tt=!0,R&&(n=Jr,n!==null)))throw n;e.memoizedState=r,e.baseState=c,e.baseQueue=v,a.lastRenderedState=r}return o===null&&(a.lanes=0),[e.memoizedState,a.dispatch]}function ls(e){var t=He(),n=t.queue;if(n===null)throw Error("Should have a queue. You are likely calling Hooks conditionally, which is not allowed. (https://react.dev/link/invalid-hook-call)");n.lastRenderedReducer=e;var a=n.dispatch,o=n.pending,r=t.memoizedState;if(o!==null){n.pending=null;var c=o=o.next;do r=e(r,c.action),c=c.next;while(c!==o);qt(r,t.memoizedState)||(Tt=!0),t.memoizedState=r,t.baseQueue===null&&(t.baseState=r),n.lastRenderedState=r}return[r,a]}function Kh(e,t,n){var a=ce,o=en();if(Ae){if(n===void 0)throw Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");var r=n();Fr||r===n()||(console.error("The result of getServerSnapshot should be cached to avoid an infinite loop"),Fr=!0)}else{if(r=t(),Fr||(n=t(),qt(r,n)||(console.error("The result of getSnapshot should be cached to avoid an infinite loop"),Fr=!0)),qe===null)throw Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");(Oe&124)!==0||Xb(a,t,r)}return o.memoizedState=r,n={value:r,getSnapshot:t},o.queue=n,Cc(Qb.bind(null,a,n,e),[e]),a.flags|=2048,Tr(Zn|gt,Dc(),Ib.bind(null,a,n,r,t),null),r}function Ac(e,t,n){var a=ce,o=He(),r=Ae;if(r){if(n===void 0)throw Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");n=n()}else if(n=t(),!Fr){var c=t();qt(n,c)||(console.error("The result of getSnapshot should be cached to avoid an infinite loop"),Fr=!0)}(c=!qt(($e||o).memoizedState,n))&&(o.memoizedState=n,Tt=!0),o=o.queue;var d=Qb.bind(null,a,o,e);if(tn(2048,gt,d,[e]),o.getSnapshot!==t||c||ht!==null&&ht.memoizedState.tag&Zn){if(a.flags|=2048,Tr(Zn|gt,Dc(),Ib.bind(null,a,o,n,t),null),qe===null)throw Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");r||(Io&124)!==0||Xb(a,t,n)}return n}function Xb(e,t,n){e.flags|=16384,e={getSnapshot:t,value:n},t=ce.updateQueue,t===null?(t=Ih(),ce.updateQueue=t,t.stores=[e]):(n=t.stores,n===null?t.stores=[e]:n.push(e))}function Ib(e,t,n,a){t.value=n,t.getSnapshot=a,Zb(t)&&Kb(e)}function Qb(e,t,n){return n(function(){Zb(t)&&Kb(e)})}function Zb(e){var t=e.getSnapshot;e=e.value;try{var n=t();return!qt(e,n)}catch{return!0}}function Kb(e){var t=Ft(e,2);t!==null&&it(t,e,2)}function Jh(e){var t=en();if(typeof e=="function"){var n=e;if(e=n(),qi){Ce(!0);try{n()}finally{Ce(!1)}}}return t.memoizedState=t.baseState=e,t.queue={pending:null,lanes:0,dispatch:null,lastRenderedReducer:ua,lastRenderedState:e},t}function Wh(e){e=Jh(e);var t=e.queue,n=m0.bind(null,ce,t);return t.dispatch=n,[e.memoizedState,n]}function Fh(e){var t=en();t.memoizedState=t.baseState=e;var n={pending:null,lanes:0,dispatch:null,lastRenderedReducer:null,lastRenderedState:null};return t.queue=n,t=dp.bind(null,ce,!0,n),n.dispatch=t,[e,t]}function Jb(e,t){var n=He();return Wb(n,$e,e,t)}function Wb(e,t,n,a){return e.baseState=n,Zh(e,$e,typeof a=="function"?a:ua)}function Fb(e,t){var n=He();return $e!==null?Wb(n,$e,e,t):(n.baseState=e,[e,n.queue.dispatch])}function MR(e,t,n,a,o){if(kc(e))throw Error("Cannot update form state while rendering.");if(e=t.action,e!==null){var r={payload:o,action:e,next:null,isTransition:!0,status:"pending",value:null,reason:null,listeners:[],then:function(c){r.listeners.push(c)}};M.T!==null?n(!0):r.isTransition=!1,a(r),n=t.pending,n===null?(r.next=t.pending=r,e0(t,r)):(r.next=n.next,t.pending=n.next=r)}}function e0(e,t){var n=t.action,a=t.payload,o=e.state;if(t.isTransition){var r=M.T,c={};M.T=c,M.T._updatedFibers=new Set;try{var d=n(o,a),v=M.S;v!==null&&v(c,d),t0(e,t,d)}catch(g){ep(e,t,g)}finally{M.T=r,r===null&&c._updatedFibers&&(e=c._updatedFibers.size,c._updatedFibers.clear(),10<e&&console.warn("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."))}}else try{c=n(o,a),t0(e,t,c)}catch(g){ep(e,t,g)}}function t0(e,t,n){n!==null&&typeof n=="object"&&typeof n.then=="function"?(n.then(function(a){n0(e,t,a)},function(a){return ep(e,t,a)}),t.isTransition||console.error("An async function with useActionState was called outside of a transition. This is likely not what you intended (for example, isPending will not update correctly). Either call the returned function inside startTransition, or pass it to an `action` or `formAction` prop.")):n0(e,t,n)}function n0(e,t,n){t.status="fulfilled",t.value=n,a0(t),e.state=n,t=e.pending,t!==null&&(n=t.next,n===t?e.pending=null:(n=n.next,t.next=n,e0(e,n)))}function ep(e,t,n){var a=e.pending;if(e.pending=null,a!==null){a=a.next;do t.status="rejected",t.reason=n,a0(t),t=t.next;while(t!==a)}e.action=null}function a0(e){e=e.listeners;for(var t=0;t<e.length;t++)(0,e[t])()}function o0(e,t){return t}function Sr(e,t){if(Ae){var n=qe.formState;if(n!==null){e:{var a=ce;if(Ae){if(tt){t:{for(var o=tt,r=ja;o.nodeType!==8;){if(!r){o=null;break t}if(o=$n(o.nextSibling),o===null){o=null;break t}}r=o.data,o=r===wv||r===R1?o:null}if(o){tt=$n(o.nextSibling),a=o.data===wv;break e}}Ai(a)}a=!1}a&&(t=n[0])}}return n=en(),n.memoizedState=n.baseState=t,a={pending:null,lanes:0,dispatch:null,lastRenderedReducer:o0,lastRenderedState:t},n.queue=a,n=m0.bind(null,ce,a),a.dispatch=n,a=Jh(!1),r=dp.bind(null,ce,!1,a.queue),a=en(),o={state:t,dispatch:null,action:e,pending:null},a.queue=o,n=MR.bind(null,ce,o,r,n),o.dispatch=n,a.memoizedState=e,[t,n,!1]}function Rc(e){var t=He();return i0(t,$e,e)}function i0(e,t,n){if(t=Zh(e,t,o0)[0],e=_r(ua)[0],typeof t=="object"&&t!==null&&typeof t.then=="function")try{var a=rs(t)}catch(c){throw c===qs?Af:c}else a=t;t=He();var o=t.queue,r=o.dispatch;return n!==t.memoizedState&&(ce.flags|=2048,Tr(Zn|gt,Dc(),jR.bind(null,o,n),null)),[a,r,e]}function jR(e,t){e.action=t}function zc(e){var t=He(),n=$e;if(n!==null)return i0(t,n,e);He(),t=t.memoizedState,n=He();var a=n.queue.dispatch;return n.memoizedState=e,[t,a,!1]}function Tr(e,t,n,a){return e={tag:e,create:n,deps:a,inst:t,next:null},t=ce.updateQueue,t===null&&(t=Ih(),ce.updateQueue=t),n=t.lastEffect,n===null?t.lastEffect=e.next=e:(a=n.next,n.next=e,e.next=a,t.lastEffect=e),e}function Dc(){return{destroy:void 0,resource:void 0}}function tp(e){var t=en();return e={current:e},t.memoizedState=e}function Ci(e,t,n,a){var o=en();a=a===void 0?null:a,ce.flags|=e,o.memoizedState=Tr(Zn|t,Dc(),n,a)}function tn(e,t,n,a){var o=He();a=a===void 0?null:a;var r=o.memoizedState.inst;$e!==null&&a!==null&&$h(a,$e.memoizedState.deps)?o.memoizedState=Tr(t,r,n,a):(ce.flags|=e,o.memoizedState=Tr(Zn|t,r,n,a))}function Cc(e,t){(ce.mode&fa)!==Xe&&(ce.mode&dT)===Xe?Ci(276826112,gt,e,t):Ci(8390656,gt,e,t)}function np(e,t){var n=4194308;return(ce.mode&fa)!==Xe&&(n|=134217728),Ci(n,jt,e,t)}function r0(e,t){if(typeof t=="function"){e=e();var n=t(e);return function(){typeof n=="function"?n():t(null)}}if(t!=null)return t.hasOwnProperty("current")||console.error("Expected useImperativeHandle() first argument to either be a ref callback or React.createRef() object. Instead received: %s.","an object with keys {"+Object.keys(t).join(", ")+"}"),e=e(),t.current=e,function(){t.current=null}}function ap(e,t,n){typeof t!="function"&&console.error("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null"),n=n!=null?n.concat([e]):null;var a=4194308;(ce.mode&fa)!==Xe&&(a|=134217728),Ci(a,jt,r0.bind(null,t,e),n)}function Mc(e,t,n){typeof t!="function"&&console.error("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null"),n=n!=null?n.concat([e]):null,tn(4,jt,r0.bind(null,t,e),n)}function op(e,t){return en().memoizedState=[e,t===void 0?null:t],e}function jc(e,t){var n=He();t=t===void 0?null:t;var a=n.memoizedState;return t!==null&&$h(t,a[1])?a[0]:(n.memoizedState=[e,t],e)}function ip(e,t){var n=en();t=t===void 0?null:t;var a=e();if(qi){Ce(!0);try{e()}finally{Ce(!1)}}return n.memoizedState=[a,t],a}function Uc(e,t){var n=He();t=t===void 0?null:t;var a=n.memoizedState;if(t!==null&&$h(t,a[1]))return a[0];if(a=e(),qi){Ce(!0);try{e()}finally{Ce(!1)}}return n.memoizedState=[a,t],a}function rp(e,t){var n=en();return lp(n,e,t)}function l0(e,t){var n=He();return u0(n,$e.memoizedState,e,t)}function s0(e,t){var n=He();return $e===null?lp(n,e,t):u0(n,$e.memoizedState,e,t)}function lp(e,t,n){return n===void 0||(Io&1073741824)!==0?e.memoizedState=t:(e.memoizedState=n,e=c_(),ce.lanes|=e,Ko|=e,n)}function u0(e,t,n,a){return qt(n,t)?n:Wr.current!==null?(e=lp(e,n,a),qt(e,t)||(Tt=!0),e):(Io&42)===0?(Tt=!0,e.memoizedState=n):(e=c_(),ce.lanes|=e,Ko|=e,t)}function c0(e,t,n,a,o){var r=Me.p;Me.p=r!==0&&r<Da?r:Da;var c=M.T,d={};M.T=d,dp(e,!1,t,n),d._updatedFibers=new Set;try{var v=o(),g=M.S;if(g!==null&&g(d,v),v!==null&&typeof v=="object"&&typeof v.then=="function"){var R=zR(v,a);ss(e,t,R,_n(e))}else ss(e,t,a,_n(e))}catch(j){ss(e,t,{then:function(){},status:"rejected",reason:j},_n(e))}finally{Me.p=r,M.T=c,c===null&&d._updatedFibers&&(e=d._updatedFibers.size,d._updatedFibers.clear(),10<e&&console.warn("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."))}}function sp(e,t,n,a){if(e.tag!==5)throw Error("Expected the form instance to be a HostComponent. This is a bug in React.");var o=f0(e).queue;c0(e,o,t,er,n===null?D:function(){return d0(e),n(a)})}function f0(e){var t=e.memoizedState;if(t!==null)return t;t={memoizedState:er,baseState:er,baseQueue:null,queue:{pending:null,lanes:0,dispatch:null,lastRenderedReducer:ua,lastRenderedState:er},next:null};var n={};return t.next={memoizedState:n,baseState:n,baseQueue:null,queue:{pending:null,lanes:0,dispatch:null,lastRenderedReducer:ua,lastRenderedState:n},next:null},e.memoizedState=t,e=e.alternate,e!==null&&(e.memoizedState=t),t}function d0(e){M.T===null&&console.error("requestFormReset was called outside a transition or action. To fix, move to an action, or wrap with startTransition.");var t=f0(e).next.queue;ss(e,t,{},_n(e))}function up(){var e=Jh(!1);return e=c0.bind(null,ce,e.queue,!0,!1),en().memoizedState=e,[!1,e]}function h0(){var e=_r(ua)[0],t=He().memoizedState;return[typeof e=="boolean"?e:rs(e),t]}function p0(){var e=ls(ua)[0],t=He().memoizedState;return[typeof e=="boolean"?e:rs(e),t]}function Mi(){return Je(uu)}function cp(){var e=en(),t=qe.identifierPrefix;if(Ae){var n=oo,a=ao;n=(a&~(1<<32-$t(a)-1)).toString(32)+n,t="«"+t+"R"+n,n=Cf++,0<n&&(t+="H"+n.toString(32)),t+="»"}else n=tC++,t="«"+t+"r"+n.toString(32)+"»";return e.memoizedState=t}function fp(){return en().memoizedState=UR.bind(null,ce)}function UR(e,t){for(var n=e.return;n!==null;){switch(n.tag){case 24:case 3:var a=_n(n);e=Co(a);var o=Mo(n,e,a);o!==null&&(it(o,n,a),as(o,n,a)),n=jh(),t!=null&&o!==null&&console.error("The seed argument is not enabled outside experimental channels."),e.payload={cache:n};return}n=n.return}}function kR(e,t,n){var a=arguments;typeof a[3]=="function"&&console.error("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect()."),a=_n(e);var o={lane:a,revertLane:0,action:n,hasEagerState:!1,eagerState:null,next:null};kc(e)?v0(t,o):(o=Sh(e,t,o,a),o!==null&&(it(o,e,a),y0(o,t,a))),wo(e,a)}function m0(e,t,n){var a=arguments;typeof a[3]=="function"&&console.error("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect()."),a=_n(e),ss(e,t,n,a),wo(e,a)}function ss(e,t,n,a){var o={lane:a,revertLane:0,action:n,hasEagerState:!1,eagerState:null,next:null};if(kc(e))v0(t,o);else{var r=e.alternate;if(e.lanes===0&&(r===null||r.lanes===0)&&(r=t.lastRenderedReducer,r!==null)){var c=M.H;M.H=ha;try{var d=t.lastRenderedState,v=r(d,n);if(o.hasEagerState=!0,o.eagerState=v,qt(v,d))return mc(e,t,o,0),qe===null&&pc(),!1}catch{}finally{M.H=c}}if(n=Sh(e,t,o,a),n!==null)return it(n,e,a),y0(n,t,a),!0}return!1}function dp(e,t,n,a){if(M.T===null&&Vi===0&&console.error("An optimistic state update occurred outside a transition or action. To fix, move the update to an action, or wrap with startTransition."),a={lane:2,revertLane:qp(),action:a,hasEagerState:!1,eagerState:null,next:null},kc(e)){if(t)throw Error("Cannot update optimistic state while rendering.");console.error("Cannot call startTransition while rendering.")}else t=Sh(e,n,a,2),t!==null&&it(t,e,2);wo(e,2)}function kc(e){var t=e.alternate;return e===ce||t!==null&&t===ce}function v0(e,t){el=Df=!0;var n=e.pending;n===null?t.next=t:(t.next=n.next,n.next=t),e.pending=t}function y0(e,t,n){if((n&4194048)!==0){var a=t.lanes;a&=e.pendingLanes,n|=a,t.lanes=n,ic(e,n)}}function Rt(e){var t=ye;return e!=null&&(ye=t===null?e:t.concat(e)),t}function Nc(e,t,n){for(var a=Object.keys(e.props),o=0;o<a.length;o++){var r=a[o];if(r!=="children"&&r!=="key"){t===null&&(t=yc(e,n.mode,0),t._debugInfo=ye,t.return=n),W(t,function(c){console.error("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",c)},r);break}}}function Hc(e){var t=Is;return Is+=1,tl===null&&(tl=Hb()),Bb(tl,e,t)}function us(e,t){t=t.props.ref,e.ref=t!==void 0?t:null}function Lc(e,t){throw t.$$typeof===Vz?Error(`A React Element from an older version of React was rendered. This is not supported. It can happen if:
- Multiple copies of the "react" package is used.
- A library pre-bundled an old copy of "react" or "react/jsx-runtime".
- A compiler tries to "inline" JSX instead of using the runtime.`):(e=Object.prototype.toString.call(t),Error("Objects are not valid as a React child (found: "+(e==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":e)+"). If you meant to render a collection of children, use an array instead."))}function Bc(e,t){var n=te(e)||"Component";YT[n]||(YT[n]=!0,t=t.displayName||t.name||"Component",e.tag===3?console.error(`Functions are not valid as a React child. This may happen if you return %s instead of <%s /> from render. Or maybe you meant to call this function rather than return it.
  root.render(%s)`,t,t,t):console.error(`Functions are not valid as a React child. This may happen if you return %s instead of <%s /> from render. Or maybe you meant to call this function rather than return it.
  <%s>{%s}</%s>`,t,t,n,t,n))}function Vc(e,t){var n=te(e)||"Component";XT[n]||(XT[n]=!0,t=String(t),e.tag===3?console.error(`Symbols are not valid as a React child.
  root.render(%s)`,t):console.error(`Symbols are not valid as a React child.
  <%s>%s</%s>`,n,t,n))}function g0(e){function t(S,T){if(e){var E=S.deletions;E===null?(S.deletions=[T],S.flags|=16):E.push(T)}}function n(S,T){if(!e)return null;for(;T!==null;)t(S,T),T=T.sibling;return null}function a(S){for(var T=new Map;S!==null;)S.key!==null?T.set(S.key,S):T.set(S.index,S),S=S.sibling;return T}function o(S,T){return S=Ga(S,T),S.index=0,S.sibling=null,S}function r(S,T,E){return S.index=E,e?(E=S.alternate,E!==null?(E=E.index,E<T?(S.flags|=67108866,T):E):(S.flags|=67108866,T)):(S.flags|=1048576,T)}function c(S){return e&&S.alternate===null&&(S.flags|=67108866),S}function d(S,T,E,k){return T===null||T.tag!==6?(T=xh(E,S.mode,k),T.return=S,T._debugOwner=S,T._debugTask=S._debugTask,T._debugInfo=ye,T):(T=o(T,E),T.return=S,T._debugInfo=ye,T)}function v(S,T,E,k){var P=E.type;return P===jr?(T=R(S,T,E.props.children,k,E.key),Nc(E,T,S),T):T!==null&&(T.elementType===P||wb(T,E)||typeof P=="object"&&P!==null&&P.$$typeof===Sn&&Qo(P)===T.type)?(T=o(T,E.props),us(T,E),T.return=S,T._debugOwner=E._owner,T._debugInfo=ye,T):(T=yc(E,S.mode,k),us(T,E),T.return=S,T._debugInfo=ye,T)}function g(S,T,E,k){return T===null||T.tag!==4||T.stateNode.containerInfo!==E.containerInfo||T.stateNode.implementation!==E.implementation?(T=Ah(E,S.mode,k),T.return=S,T._debugInfo=ye,T):(T=o(T,E.children||[]),T.return=S,T._debugInfo=ye,T)}function R(S,T,E,k,P){return T===null||T.tag!==7?(T=Oi(E,S.mode,k,P),T.return=S,T._debugOwner=S,T._debugTask=S._debugTask,T._debugInfo=ye,T):(T=o(T,E),T.return=S,T._debugInfo=ye,T)}function j(S,T,E){if(typeof T=="string"&&T!==""||typeof T=="number"||typeof T=="bigint")return T=xh(""+T,S.mode,E),T.return=S,T._debugOwner=S,T._debugTask=S._debugTask,T._debugInfo=ye,T;if(typeof T=="object"&&T!==null){switch(T.$$typeof){case Bo:return E=yc(T,S.mode,E),us(E,T),E.return=S,S=Rt(T._debugInfo),E._debugInfo=ye,ye=S,E;case Mr:return T=Ah(T,S.mode,E),T.return=S,T._debugInfo=ye,T;case Sn:var k=Rt(T._debugInfo);return T=Qo(T),S=j(S,T,E),ye=k,S}if(Ct(T)||Fe(T))return E=Oi(T,S.mode,E,null),E.return=S,E._debugOwner=S,E._debugTask=S._debugTask,S=Rt(T._debugInfo),E._debugInfo=ye,ye=S,E;if(typeof T.then=="function")return k=Rt(T._debugInfo),S=j(S,Hc(T),E),ye=k,S;if(T.$$typeof===Aa)return j(S,_c(S,T),E);Lc(S,T)}return typeof T=="function"&&Bc(S,T),typeof T=="symbol"&&Vc(S,T),null}function A(S,T,E,k){var P=T!==null?T.key:null;if(typeof E=="string"&&E!==""||typeof E=="number"||typeof E=="bigint")return P!==null?null:d(S,T,""+E,k);if(typeof E=="object"&&E!==null){switch(E.$$typeof){case Bo:return E.key===P?(P=Rt(E._debugInfo),S=v(S,T,E,k),ye=P,S):null;case Mr:return E.key===P?g(S,T,E,k):null;case Sn:return P=Rt(E._debugInfo),E=Qo(E),S=A(S,T,E,k),ye=P,S}if(Ct(E)||Fe(E))return P!==null?null:(P=Rt(E._debugInfo),S=R(S,T,E,k,null),ye=P,S);if(typeof E.then=="function")return P=Rt(E._debugInfo),S=A(S,T,Hc(E),k),ye=P,S;if(E.$$typeof===Aa)return A(S,T,_c(S,E),k);Lc(S,E)}return typeof E=="function"&&Bc(S,E),typeof E=="symbol"&&Vc(S,E),null}function U(S,T,E,k,P){if(typeof k=="string"&&k!==""||typeof k=="number"||typeof k=="bigint")return S=S.get(E)||null,d(T,S,""+k,P);if(typeof k=="object"&&k!==null){switch(k.$$typeof){case Bo:return E=S.get(k.key===null?E:k.key)||null,S=Rt(k._debugInfo),T=v(T,E,k,P),ye=S,T;case Mr:return S=S.get(k.key===null?E:k.key)||null,g(T,S,k,P);case Sn:var fe=Rt(k._debugInfo);return k=Qo(k),T=U(S,T,E,k,P),ye=fe,T}if(Ct(k)||Fe(k))return E=S.get(E)||null,S=Rt(k._debugInfo),T=R(T,E,k,P,null),ye=S,T;if(typeof k.then=="function")return fe=Rt(k._debugInfo),T=U(S,T,E,Hc(k),P),ye=fe,T;if(k.$$typeof===Aa)return U(S,T,E,_c(T,k),P);Lc(T,k)}return typeof k=="function"&&Bc(T,k),typeof k=="symbol"&&Vc(T,k),null}function Z(S,T,E,k){if(typeof E!="object"||E===null)return k;switch(E.$$typeof){case Bo:case Mr:b(S,T,E);var P=E.key;if(typeof P!="string")break;if(k===null){k=new Set,k.add(P);break}if(!k.has(P)){k.add(P);break}W(T,function(){console.error("Encountered two children with the same key, `%s`. Keys should be unique so that components maintain their identity across updates. Non-unique keys may cause children to be duplicated and/or omitted — the behavior is unsupported and could change in a future version.",P)});break;case Sn:E=Qo(E),Z(S,T,E,k)}return k}function re(S,T,E,k){for(var P=null,fe=null,K=null,de=T,he=T=0,Ie=null;de!==null&&he<E.length;he++){de.index>he?(Ie=de,de=null):Ie=de.sibling;var lt=A(S,de,E[he],k);if(lt===null){de===null&&(de=Ie);break}P=Z(S,lt,E[he],P),e&&de&&lt.alternate===null&&t(S,de),T=r(lt,T,he),K===null?fe=lt:K.sibling=lt,K=lt,de=Ie}if(he===E.length)return n(S,de),Ae&&Ei(S,he),fe;if(de===null){for(;he<E.length;he++)de=j(S,E[he],k),de!==null&&(P=Z(S,de,E[he],P),T=r(de,T,he),K===null?fe=de:K.sibling=de,K=de);return Ae&&Ei(S,he),fe}for(de=a(de);he<E.length;he++)Ie=U(de,S,he,E[he],k),Ie!==null&&(P=Z(S,Ie,E[he],P),e&&Ie.alternate!==null&&de.delete(Ie.key===null?he:Ie.key),T=r(Ie,T,he),K===null?fe=Ie:K.sibling=Ie,K=Ie);return e&&de.forEach(function(po){return t(S,po)}),Ae&&Ei(S,he),fe}function Ge(S,T,E,k){if(E==null)throw Error("An iterable object provided no iterator.");for(var P=null,fe=null,K=T,de=T=0,he=null,Ie=null,lt=E.next();K!==null&&!lt.done;de++,lt=E.next()){K.index>de?(he=K,K=null):he=K.sibling;var po=A(S,K,lt.value,k);if(po===null){K===null&&(K=he);break}Ie=Z(S,po,lt.value,Ie),e&&K&&po.alternate===null&&t(S,K),T=r(po,T,de),fe===null?P=po:fe.sibling=po,fe=po,K=he}if(lt.done)return n(S,K),Ae&&Ei(S,de),P;if(K===null){for(;!lt.done;de++,lt=E.next())K=j(S,lt.value,k),K!==null&&(Ie=Z(S,K,lt.value,Ie),T=r(K,T,de),fe===null?P=K:fe.sibling=K,fe=K);return Ae&&Ei(S,de),P}for(K=a(K);!lt.done;de++,lt=E.next())he=U(K,S,de,lt.value,k),he!==null&&(Ie=Z(S,he,lt.value,Ie),e&&he.alternate!==null&&K.delete(he.key===null?de:he.key),T=r(he,T,de),fe===null?P=he:fe.sibling=he,fe=he);return e&&K.forEach(function(RC){return t(S,RC)}),Ae&&Ei(S,de),P}function we(S,T,E,k){if(typeof E=="object"&&E!==null&&E.type===jr&&E.key===null&&(Nc(E,null,S),E=E.props.children),typeof E=="object"&&E!==null){switch(E.$$typeof){case Bo:var P=Rt(E._debugInfo);e:{for(var fe=E.key;T!==null;){if(T.key===fe){if(fe=E.type,fe===jr){if(T.tag===7){n(S,T.sibling),k=o(T,E.props.children),k.return=S,k._debugOwner=E._owner,k._debugInfo=ye,Nc(E,k,S),S=k;break e}}else if(T.elementType===fe||wb(T,E)||typeof fe=="object"&&fe!==null&&fe.$$typeof===Sn&&Qo(fe)===T.type){n(S,T.sibling),k=o(T,E.props),us(k,E),k.return=S,k._debugOwner=E._owner,k._debugInfo=ye,S=k;break e}n(S,T);break}else t(S,T);T=T.sibling}E.type===jr?(k=Oi(E.props.children,S.mode,k,E.key),k.return=S,k._debugOwner=S,k._debugTask=S._debugTask,k._debugInfo=ye,Nc(E,k,S),S=k):(k=yc(E,S.mode,k),us(k,E),k.return=S,k._debugInfo=ye,S=k)}return S=c(S),ye=P,S;case Mr:e:{for(P=E,E=P.key;T!==null;){if(T.key===E)if(T.tag===4&&T.stateNode.containerInfo===P.containerInfo&&T.stateNode.implementation===P.implementation){n(S,T.sibling),k=o(T,P.children||[]),k.return=S,S=k;break e}else{n(S,T);break}else t(S,T);T=T.sibling}k=Ah(P,S.mode,k),k.return=S,S=k}return c(S);case Sn:return P=Rt(E._debugInfo),E=Qo(E),S=we(S,T,E,k),ye=P,S}if(Ct(E))return P=Rt(E._debugInfo),S=re(S,T,E,k),ye=P,S;if(Fe(E)){if(P=Rt(E._debugInfo),fe=Fe(E),typeof fe!="function")throw Error("An object is not an iterable. This error is likely caused by a bug in React. Please file an issue.");var K=fe.call(E);return K===E?(S.tag!==0||Object.prototype.toString.call(S.type)!=="[object GeneratorFunction]"||Object.prototype.toString.call(K)!=="[object Generator]")&&(qT||console.error("Using Iterators as children is unsupported and will likely yield unexpected results because enumerating a generator mutates it. You may convert it to an array with `Array.from()` or the `[...spread]` operator before rendering. You can also use an Iterable that can iterate multiple times over the same items."),qT=!0):E.entries!==fe||tv||(console.error("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),tv=!0),S=Ge(S,T,K,k),ye=P,S}if(typeof E.then=="function")return P=Rt(E._debugInfo),S=we(S,T,Hc(E),k),ye=P,S;if(E.$$typeof===Aa)return we(S,T,_c(S,E),k);Lc(S,E)}return typeof E=="string"&&E!==""||typeof E=="number"||typeof E=="bigint"?(P=""+E,T!==null&&T.tag===6?(n(S,T.sibling),k=o(T,P),k.return=S,S=k):(n(S,T),k=xh(P,S.mode,k),k.return=S,k._debugOwner=S,k._debugTask=S._debugTask,k._debugInfo=ye,S=k),c(S)):(typeof E=="function"&&Bc(S,E),typeof E=="symbol"&&Vc(S,E),n(S,T))}return function(S,T,E,k){var P=ye;ye=null;try{Is=0;var fe=we(S,T,E,k);return tl=null,fe}catch(Ie){if(Ie===qs||Ie===Af)throw Ie;var K=O(29,Ie,null,S.mode);K.lanes=k,K.return=S;var de=K._debugInfo=ye;if(K._debugOwner=S._debugOwner,K._debugTask=S._debugTask,de!=null){for(var he=de.length-1;0<=he;he--)if(typeof de[he].stack=="string"){K._debugOwner=de[he],K._debugTask=de[he].debugTask;break}}return K}finally{ye=P}}}function Uo(e){var t=e.alternate;Re(bt,bt.current&al,e),Re(Jn,e,e),ka===null&&(t===null||Wr.current!==null||t.memoizedState!==null)&&(ka=e)}function b0(e){if(e.tag===22){if(Re(bt,bt.current,e),Re(Jn,e,e),ka===null){var t=e.alternate;t!==null&&t.memoizedState!==null&&(ka=e)}}else ko(e)}function ko(e){Re(bt,bt.current,e),Re(Jn,Jn.current,e)}function Ia(e){ue(Jn,e),ka===e&&(ka=null),ue(bt,e)}function $c(e){for(var t=e;t!==null;){if(t.tag===13){var n=t.memoizedState;if(n!==null&&(n=n.dehydrated,n===null||n.data===co||em(n)))return t}else if(t.tag===19&&t.memoizedProps.revealOrder!==void 0){if((t.flags&128)!==0)return t}else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break;for(;t.sibling===null;){if(t.return===null||t.return===e)return null;t=t.return}t.sibling.return=t.return,t=t.sibling}return null}function hp(e){if(e!==null&&typeof e!="function"){var t=String(e);o1.has(t)||(o1.add(t),console.error("Expected the last optional `callback` argument to be a function. Instead received: %s.",e))}}function pp(e,t,n,a){var o=e.memoizedState,r=n(a,o);if(e.mode&Bt){Ce(!0);try{r=n(a,o)}finally{Ce(!1)}}r===void 0&&(t=Ne(t)||"Component",e1.has(t)||(e1.add(t),console.error("%s.getDerivedStateFromProps(): A valid state object (or null) must be returned. You have returned undefined.",t))),o=r==null?o:be({},o,r),e.memoizedState=o,e.lanes===0&&(e.updateQueue.baseState=o)}function _0(e,t,n,a,o,r,c){var d=e.stateNode;if(typeof d.shouldComponentUpdate=="function"){if(n=d.shouldComponentUpdate(a,r,c),e.mode&Bt){Ce(!0);try{n=d.shouldComponentUpdate(a,r,c)}finally{Ce(!1)}}return n===void 0&&console.error("%s.shouldComponentUpdate(): Returned undefined instead of a boolean value. Make sure to return true or false.",Ne(t)||"Component"),n}return t.prototype&&t.prototype.isPureReactComponent?!Kl(n,a)||!Kl(o,r):!0}function S0(e,t,n,a){var o=t.state;typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps(n,a),typeof t.UNSAFE_componentWillReceiveProps=="function"&&t.UNSAFE_componentWillReceiveProps(n,a),t.state!==o&&(e=te(e)||"Component",ZT.has(e)||(ZT.add(e),console.error("%s.componentWillReceiveProps(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",e)),nv.enqueueReplaceState(t,t.state,null))}function ji(e,t){var n=t;if("ref"in t){n={};for(var a in t)a!=="ref"&&(n[a]=t[a])}if(e=e.defaultProps){n===t&&(n=be({},n));for(var o in e)n[o]===void 0&&(n[o]=e[o])}return n}function T0(e){av(e),console.warn(`%s

%s
`,ol?"An error occurred in the <"+ol+"> component.":"An error occurred in one of your React components.",`Consider adding an error boundary to your tree to customize error handling behavior.
Visit https://react.dev/link/error-boundaries to learn more about error boundaries.`)}function O0(e){var t=ol?"The above error occurred in the <"+ol+"> component.":"The above error occurred in one of your React components.",n="React will try to recreate this component tree from scratch using the error boundary you provided, "+((ov||"Anonymous")+".");if(typeof e=="object"&&e!==null&&typeof e.environmentName=="string"){var a=e.environmentName;e=[`%o

%s

%s
`,e,t,n].slice(0),typeof e[0]=="string"?e.splice(0,1,N1+e[0],H1,Jf+a+Jf,L1):e.splice(0,0,N1,H1,Jf+a+Jf,L1),e.unshift(console),a=xC.apply(console.error,e),a()}else console.error(`%o

%s

%s
`,e,t,n)}function E0(e){av(e)}function Pc(e,t){try{ol=t.source?te(t.source):null,ov=null;var n=t.value;if(M.actQueue!==null)M.thrownErrors.push(n);else{var a=e.onUncaughtError;a(n,{componentStack:t.stack})}}catch(o){setTimeout(function(){throw o})}}function w0(e,t,n){try{ol=n.source?te(n.source):null,ov=te(t);var a=e.onCaughtError;a(n.value,{componentStack:n.stack,errorBoundary:t.tag===1?t.stateNode:null})}catch(o){setTimeout(function(){throw o})}}function mp(e,t,n){return n=Co(n),n.tag=Im,n.payload={element:null},n.callback=function(){W(t.source,Pc,e,t)},n}function vp(e){return e=Co(e),e.tag=Im,e}function yp(e,t,n,a){var o=n.type.getDerivedStateFromError;if(typeof o=="function"){var r=a.value;e.payload=function(){return o(r)},e.callback=function(){xb(n),W(a.source,w0,t,n,a)}}var c=n.stateNode;c!==null&&typeof c.componentDidCatch=="function"&&(e.callback=function(){xb(n),W(a.source,w0,t,n,a),typeof o!="function"&&(Wo===null?Wo=new Set([this]):Wo.add(this)),aC(this,a),typeof o=="function"||(n.lanes&2)===0&&console.error("%s: Error boundaries should implement getDerivedStateFromError(). In that method, return a state update to display an error message or fallback UI.",te(n)||"Unknown")})}function NR(e,t,n,a,o){if(n.flags|=32768,ca&&vs(e,o),a!==null&&typeof a=="object"&&typeof a.then=="function"){if(t=n.alternate,t!==null&&es(t,n,o,!0),Ae&&(io=!0),n=Jn.current,n!==null){switch(n.tag){case 13:return ka===null?Np():n.alternate===null&&nt===uo&&(nt=sv),n.flags&=-257,n.flags|=65536,n.lanes=o,a===Xm?n.flags|=16384:(t=n.updateQueue,t===null?n.updateQueue=new Set([a]):t.add(a),Bp(e,a,o)),!1;case 22:return n.flags|=65536,a===Xm?n.flags|=16384:(t=n.updateQueue,t===null?(t={transitions:null,markerInstances:null,retryQueue:new Set([a])},n.updateQueue=t):(n=t.retryQueue,n===null?t.retryQueue=new Set([a]):n.add(a)),Bp(e,a,o)),!1}throw Error("Unexpected Suspense handler tag ("+n.tag+"). This is a bug in React.")}return Bp(e,a,o),Np(),!1}if(Ae)return io=!0,t=Jn.current,t!==null?((t.flags&65536)===0&&(t.flags|=256),t.flags|=65536,t.lanes=o,a!==Pm&&Fl(yn(Error("There was an error while hydrating but React was able to recover by instead client rendering from the nearest Suspense boundary.",{cause:a}),n))):(a!==Pm&&Fl(yn(Error("There was an error while hydrating but React was able to recover by instead client rendering the entire root.",{cause:a}),n)),e=e.current.alternate,e.flags|=65536,o&=-o,e.lanes|=o,a=yn(a,n),o=mp(e.stateNode,a,o),wc(e,o),nt!==Gi&&(nt=sl)),!1;var r=yn(Error("There was an error during concurrent rendering but React was able to recover by instead synchronously rendering the entire root.",{cause:a}),n);if(tu===null?tu=[r]:tu.push(r),nt!==Gi&&(nt=sl),t===null)return!0;a=yn(a,n),n=t;do{switch(n.tag){case 3:return n.flags|=65536,e=o&-o,n.lanes|=e,e=mp(n.stateNode,a,e),wc(n,e),!1;case 1:if(t=n.type,r=n.stateNode,(n.flags&128)===0&&(typeof t.getDerivedStateFromError=="function"||r!==null&&typeof r.componentDidCatch=="function"&&(Wo===null||!Wo.has(r))))return n.flags|=65536,o&=-o,n.lanes|=o,o=vp(o),yp(o,e,n,a),wc(n,o),!1}n=n.return}while(n!==null);return!1}function zt(e,t,n,a){t.child=e===null?IT(t,null,n,a):nl(t,e.child,n,a)}function x0(e,t,n,a,o){n=n.render;var r=t.ref;if("ref"in a){var c={};for(var d in a)d!=="ref"&&(c[d]=a[d])}else c=a;return Ri(t),At(t),a=Ph(e,t,n,c,r,o),d=Gh(),Nn(),e!==null&&!Tt?(Yh(e,t,o),Qa(e,t,o)):(Ae&&d&&Rh(t),t.flags|=1,zt(e,t,a,o),t.child)}function A0(e,t,n,a,o){if(e===null){var r=n.type;return typeof r=="function"&&!Eh(r)&&r.defaultProps===void 0&&n.compare===null?(n=Ti(r),t.tag=15,t.type=n,bp(t,r),R0(e,t,n,a,o)):(e=wh(n.type,null,a,t,t.mode,o),e.ref=t.ref,e.return=t,t.child=e)}if(r=e.child,!wp(e,o)){var c=r.memoizedProps;if(n=n.compare,n=n!==null?n:Kl,n(c,a)&&e.ref===t.ref)return Qa(e,t,o)}return t.flags|=1,e=Ga(r,a),e.ref=t.ref,e.return=t,t.child=e}function R0(e,t,n,a,o){if(e!==null){var r=e.memoizedProps;if(Kl(r,a)&&e.ref===t.ref&&t.type===e.type)if(Tt=!1,t.pendingProps=a=r,wp(e,o))(e.flags&131072)!==0&&(Tt=!0);else return t.lanes=e.lanes,Qa(e,t,o)}return gp(e,t,n,a,o)}function z0(e,t,n){var a=t.pendingProps,o=a.children,r=e!==null?e.memoizedState:null;if(a.mode==="hidden"){if((t.flags&128)!==0){if(a=r!==null?r.baseLanes|n:n,e!==null){for(o=t.child=e.child,r=0;o!==null;)r=r|o.lanes|o.childLanes,o=o.sibling;t.childLanes=r&~a}else t.childLanes=0,t.child=null;return D0(e,t,a,n)}if((n&536870912)!==0)t.memoizedState={baseLanes:0,cachePool:null},e!==null&&Oc(t,r!==null?r.cachePool:null),r!==null?Gb(t,r):Bh(t),b0(t);else return t.lanes=t.childLanes=536870912,D0(e,t,r!==null?r.baseLanes|n:n,n)}else r!==null?(Oc(t,r.cachePool),Gb(t,r),ko(t),t.memoizedState=null):(e!==null&&Oc(t,null),Bh(t),ko(t));return zt(e,t,o,n),t.child}function D0(e,t,n,a){var o=Nh();return o=o===null?null:{parent:yt._currentValue,pool:o},t.memoizedState={baseLanes:n,cachePool:o},e!==null&&Oc(t,null),Bh(t),b0(t),e!==null&&es(e,t,a,!0),null}function qc(e,t){var n=t.ref;if(n===null)e!==null&&e.ref!==null&&(t.flags|=4194816);else{if(typeof n!="function"&&typeof n!="object")throw Error("Expected ref to be a function, an object returned by React.createRef(), or undefined/null.");(e===null||e.ref!==n)&&(t.flags|=4194816)}}function gp(e,t,n,a,o){if(n.prototype&&typeof n.prototype.render=="function"){var r=Ne(n)||"Unknown";r1[r]||(console.error("The <%s /> component appears to have a render method, but doesn't extend React.Component. This is likely to cause errors. Change %s to extend React.Component instead.",r,r),r1[r]=!0)}return t.mode&Bt&&da.recordLegacyContextWarning(t,null),e===null&&(bp(t,t.type),n.contextTypes&&(r=Ne(n)||"Unknown",s1[r]||(s1[r]=!0,console.error("%s uses the legacy contextTypes API which was removed in React 19. Use React.createContext() with React.useContext() instead. (https://react.dev/link/legacy-context)",r)))),Ri(t),At(t),n=Ph(e,t,n,a,void 0,o),a=Gh(),Nn(),e!==null&&!Tt?(Yh(e,t,o),Qa(e,t,o)):(Ae&&a&&Rh(t),t.flags|=1,zt(e,t,n,o),t.child)}function C0(e,t,n,a,o,r){return Ri(t),At(t),lo=-1,Xs=e!==null&&e.type!==t.type,t.updateQueue=null,n=qh(t,a,n,o),Yb(e,t),a=Gh(),Nn(),e!==null&&!Tt?(Yh(e,t,r),Qa(e,t,r)):(Ae&&a&&Rh(t),t.flags|=1,zt(e,t,n,r),t.child)}function M0(e,t,n,a,o){switch(p(t)){case!1:var r=t.stateNode,c=new t.type(t.memoizedProps,r.context).state;r.updater.enqueueSetState(r,c,null);break;case!0:t.flags|=128,t.flags|=65536,r=Error("Simulated error coming from DevTools");var d=o&-o;if(t.lanes|=d,c=qe,c===null)throw Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");d=vp(d),yp(d,c,t,yn(r,t)),wc(t,d)}if(Ri(t),t.stateNode===null){if(c=Yo,r=n.contextType,"contextType"in n&&r!==null&&(r===void 0||r.$$typeof!==Aa)&&!a1.has(n)&&(a1.add(n),d=r===void 0?" However, it is set to undefined. This can be caused by a typo or by mixing up named and default imports. This can also happen due to a circular dependency, so try moving the createContext() call to a separate file.":typeof r!="object"?" However, it is set to a "+typeof r+".":r.$$typeof===dm?" Did you accidentally pass the Context.Consumer instead?":" However, it is set to an object with keys {"+Object.keys(r).join(", ")+"}.",console.error("%s defines an invalid contextType. contextType should point to the Context object returned by React.createContext().%s",Ne(n)||"Component",d)),typeof r=="object"&&r!==null&&(c=Je(r)),r=new n(a,c),t.mode&Bt){Ce(!0);try{r=new n(a,c)}finally{Ce(!1)}}if(c=t.memoizedState=r.state!==null&&r.state!==void 0?r.state:null,r.updater=nv,t.stateNode=r,r._reactInternals=t,r._reactInternalInstance=QT,typeof n.getDerivedStateFromProps=="function"&&c===null&&(c=Ne(n)||"Component",KT.has(c)||(KT.add(c),console.error("`%s` uses `getDerivedStateFromProps` but its initial state is %s. This is not recommended. Instead, define the initial state by assigning an object to `this.state` in the constructor of `%s`. This ensures that `getDerivedStateFromProps` arguments have a consistent shape.",c,r.state===null?"null":"undefined",c))),typeof n.getDerivedStateFromProps=="function"||typeof r.getSnapshotBeforeUpdate=="function"){var v=d=c=null;if(typeof r.componentWillMount=="function"&&r.componentWillMount.__suppressDeprecationWarning!==!0?c="componentWillMount":typeof r.UNSAFE_componentWillMount=="function"&&(c="UNSAFE_componentWillMount"),typeof r.componentWillReceiveProps=="function"&&r.componentWillReceiveProps.__suppressDeprecationWarning!==!0?d="componentWillReceiveProps":typeof r.UNSAFE_componentWillReceiveProps=="function"&&(d="UNSAFE_componentWillReceiveProps"),typeof r.componentWillUpdate=="function"&&r.componentWillUpdate.__suppressDeprecationWarning!==!0?v="componentWillUpdate":typeof r.UNSAFE_componentWillUpdate=="function"&&(v="UNSAFE_componentWillUpdate"),c!==null||d!==null||v!==null){r=Ne(n)||"Component";var g=typeof n.getDerivedStateFromProps=="function"?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";WT.has(r)||(WT.add(r),console.error(`Unsafe legacy lifecycles will not be called for components using new component APIs.

%s uses %s but also contains the following legacy lifecycles:%s%s%s

The above lifecycles should be removed. Learn more about this warning here:
https://react.dev/link/unsafe-component-lifecycles`,r,g,c!==null?`
  `+c:"",d!==null?`
  `+d:"",v!==null?`
  `+v:""))}}r=t.stateNode,c=Ne(n)||"Component",r.render||(n.prototype&&typeof n.prototype.render=="function"?console.error("No `render` method found on the %s instance: did you accidentally return an object from the constructor?",c):console.error("No `render` method found on the %s instance: you may have forgotten to define `render`.",c)),!r.getInitialState||r.getInitialState.isReactClassApproved||r.state||console.error("getInitialState was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Did you mean to define a state property instead?",c),r.getDefaultProps&&!r.getDefaultProps.isReactClassApproved&&console.error("getDefaultProps was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Use a static property to define defaultProps instead.",c),r.contextType&&console.error("contextType was defined as an instance property on %s. Use a static property to define contextType instead.",c),n.childContextTypes&&!n1.has(n)&&(n1.add(n),console.error("%s uses the legacy childContextTypes API which was removed in React 19. Use React.createContext() instead. (https://react.dev/link/legacy-context)",c)),n.contextTypes&&!t1.has(n)&&(t1.add(n),console.error("%s uses the legacy contextTypes API which was removed in React 19. Use React.createContext() with static contextType instead. (https://react.dev/link/legacy-context)",c)),typeof r.componentShouldUpdate=="function"&&console.error("%s has a method called componentShouldUpdate(). Did you mean shouldComponentUpdate()? The name is phrased as a question because the function is expected to return a value.",c),n.prototype&&n.prototype.isPureReactComponent&&typeof r.shouldComponentUpdate<"u"&&console.error("%s has a method called shouldComponentUpdate(). shouldComponentUpdate should not be used when extending React.PureComponent. Please extend React.Component if shouldComponentUpdate is used.",Ne(n)||"A pure component"),typeof r.componentDidUnmount=="function"&&console.error("%s has a method called componentDidUnmount(). But there is no such lifecycle method. Did you mean componentWillUnmount()?",c),typeof r.componentDidReceiveProps=="function"&&console.error("%s has a method called componentDidReceiveProps(). But there is no such lifecycle method. If you meant to update the state in response to changing props, use componentWillReceiveProps(). If you meant to fetch data or run side-effects or mutations after React has updated the UI, use componentDidUpdate().",c),typeof r.componentWillRecieveProps=="function"&&console.error("%s has a method called componentWillRecieveProps(). Did you mean componentWillReceiveProps()?",c),typeof r.UNSAFE_componentWillRecieveProps=="function"&&console.error("%s has a method called UNSAFE_componentWillRecieveProps(). Did you mean UNSAFE_componentWillReceiveProps()?",c),d=r.props!==a,r.props!==void 0&&d&&console.error("When calling super() in `%s`, make sure to pass up the same props that your component's constructor was passed.",c),r.defaultProps&&console.error("Setting defaultProps as an instance property on %s is not supported and will be ignored. Instead, define defaultProps as a static property on %s.",c,c),typeof r.getSnapshotBeforeUpdate!="function"||typeof r.componentDidUpdate=="function"||JT.has(n)||(JT.add(n),console.error("%s: getSnapshotBeforeUpdate() should be used with componentDidUpdate(). This component defines getSnapshotBeforeUpdate() only.",Ne(n))),typeof r.getDerivedStateFromProps=="function"&&console.error("%s: getDerivedStateFromProps() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof r.getDerivedStateFromError=="function"&&console.error("%s: getDerivedStateFromError() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof n.getSnapshotBeforeUpdate=="function"&&console.error("%s: getSnapshotBeforeUpdate() is defined as a static method and will be ignored. Instead, declare it as an instance method.",c),(d=r.state)&&(typeof d!="object"||Ct(d))&&console.error("%s.state: must be set to an object or null",c),typeof r.getChildContext=="function"&&typeof n.childContextTypes!="object"&&console.error("%s.getChildContext(): childContextTypes must be defined in order to use getChildContext().",c),r=t.stateNode,r.props=a,r.state=t.memoizedState,r.refs={},Hh(t),c=n.contextType,r.context=typeof c=="object"&&c!==null?Je(c):Yo,r.state===a&&(c=Ne(n)||"Component",FT.has(c)||(FT.add(c),console.error("%s: It is not recommended to assign props directly to state because updates to props won't be reflected in state. In most cases, it is better to use props directly.",c))),t.mode&Bt&&da.recordLegacyContextWarning(t,r),da.recordUnsafeLifecycleWarnings(t,r),r.state=t.memoizedState,c=n.getDerivedStateFromProps,typeof c=="function"&&(pp(t,n,c,a),r.state=t.memoizedState),typeof n.getDerivedStateFromProps=="function"||typeof r.getSnapshotBeforeUpdate=="function"||typeof r.UNSAFE_componentWillMount!="function"&&typeof r.componentWillMount!="function"||(c=r.state,typeof r.componentWillMount=="function"&&r.componentWillMount(),typeof r.UNSAFE_componentWillMount=="function"&&r.UNSAFE_componentWillMount(),c!==r.state&&(console.error("%s.componentWillMount(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",te(t)||"Component"),nv.enqueueReplaceState(r,r.state,null)),is(t,a,r,o),os(),r.state=t.memoizedState),typeof r.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&fa)!==Xe&&(t.flags|=134217728),r=!0}else if(e===null){r=t.stateNode;var R=t.memoizedProps;d=ji(n,R),r.props=d;var j=r.context;v=n.contextType,c=Yo,typeof v=="object"&&v!==null&&(c=Je(v)),g=n.getDerivedStateFromProps,v=typeof g=="function"||typeof r.getSnapshotBeforeUpdate=="function",R=t.pendingProps!==R,v||typeof r.UNSAFE_componentWillReceiveProps!="function"&&typeof r.componentWillReceiveProps!="function"||(R||j!==c)&&S0(t,r,a,c),Xo=!1;var A=t.memoizedState;r.state=A,is(t,a,r,o),os(),j=t.memoizedState,R||A!==j||Xo?(typeof g=="function"&&(pp(t,n,g,a),j=t.memoizedState),(d=Xo||_0(t,n,d,a,A,j,c))?(v||typeof r.UNSAFE_componentWillMount!="function"&&typeof r.componentWillMount!="function"||(typeof r.componentWillMount=="function"&&r.componentWillMount(),typeof r.UNSAFE_componentWillMount=="function"&&r.UNSAFE_componentWillMount()),typeof r.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&fa)!==Xe&&(t.flags|=134217728)):(typeof r.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&fa)!==Xe&&(t.flags|=134217728),t.memoizedProps=a,t.memoizedState=j),r.props=a,r.state=j,r.context=c,r=d):(typeof r.componentDidMount=="function"&&(t.flags|=4194308),(t.mode&fa)!==Xe&&(t.flags|=134217728),r=!1)}else{r=t.stateNode,Lh(e,t),c=t.memoizedProps,v=ji(n,c),r.props=v,g=t.pendingProps,A=r.context,j=n.contextType,d=Yo,typeof j=="object"&&j!==null&&(d=Je(j)),R=n.getDerivedStateFromProps,(j=typeof R=="function"||typeof r.getSnapshotBeforeUpdate=="function")||typeof r.UNSAFE_componentWillReceiveProps!="function"&&typeof r.componentWillReceiveProps!="function"||(c!==g||A!==d)&&S0(t,r,a,d),Xo=!1,A=t.memoizedState,r.state=A,is(t,a,r,o),os();var U=t.memoizedState;c!==g||A!==U||Xo||e!==null&&e.dependencies!==null&&bc(e.dependencies)?(typeof R=="function"&&(pp(t,n,R,a),U=t.memoizedState),(v=Xo||_0(t,n,v,a,A,U,d)||e!==null&&e.dependencies!==null&&bc(e.dependencies))?(j||typeof r.UNSAFE_componentWillUpdate!="function"&&typeof r.componentWillUpdate!="function"||(typeof r.componentWillUpdate=="function"&&r.componentWillUpdate(a,U,d),typeof r.UNSAFE_componentWillUpdate=="function"&&r.UNSAFE_componentWillUpdate(a,U,d)),typeof r.componentDidUpdate=="function"&&(t.flags|=4),typeof r.getSnapshotBeforeUpdate=="function"&&(t.flags|=1024)):(typeof r.componentDidUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=4),typeof r.getSnapshotBeforeUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=1024),t.memoizedProps=a,t.memoizedState=U),r.props=a,r.state=U,r.context=d,r=v):(typeof r.componentDidUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=4),typeof r.getSnapshotBeforeUpdate!="function"||c===e.memoizedProps&&A===e.memoizedState||(t.flags|=1024),r=!1)}if(d=r,qc(e,t),c=(t.flags&128)!==0,d||c){if(d=t.stateNode,ch(t),c&&typeof n.getDerivedStateFromError!="function")n=null,an=-1;else{if(At(t),n=jT(d),t.mode&Bt){Ce(!0);try{jT(d)}finally{Ce(!1)}}Nn()}t.flags|=1,e!==null&&c?(t.child=nl(t,e.child,null,o),t.child=nl(t,null,n,o)):zt(e,t,n,o),t.memoizedState=d.state,e=t.child}else e=Qa(e,t,o);return o=t.stateNode,r&&o.props!==a&&(il||console.error("It looks like %s is reassigning its own `this.props` while rendering. This is not supported and can lead to confusing bugs.",te(t)||"a component"),il=!0),e}function j0(e,t,n,a){return Wl(),t.flags|=256,zt(e,t,n,a),t.child}function bp(e,t){t&&t.childContextTypes&&console.error(`childContextTypes cannot be defined on a function component.
  %s.childContextTypes = ...`,t.displayName||t.name||"Component"),typeof t.getDerivedStateFromProps=="function"&&(e=Ne(t)||"Unknown",u1[e]||(console.error("%s: Function components do not support getDerivedStateFromProps.",e),u1[e]=!0)),typeof t.contextType=="object"&&t.contextType!==null&&(t=Ne(t)||"Unknown",l1[t]||(console.error("%s: Function components do not support contextType.",t),l1[t]=!0))}function _p(e){return{baseLanes:e,cachePool:Nb()}}function Sp(e,t,n){return e=e!==null?e.childLanes&~n:0,t&&(e|=wn),e}function U0(e,t,n){var a,o=t.pendingProps;h(t)&&(t.flags|=128);var r=!1,c=(t.flags&128)!==0;if((a=c)||(a=e!==null&&e.memoizedState===null?!1:(bt.current&Qs)!==0),a&&(r=!0,t.flags&=-129),a=(t.flags&32)!==0,t.flags&=-33,e===null){if(Ae){if(r?Uo(t):ko(t),Ae){var d=tt,v;if(!(v=!d)){e:{var g=d;for(v=ja;g.nodeType!==8;){if(!v){v=null;break e}if(g=$n(g.nextSibling),g===null){v=null;break e}}v=g}v!==null?(wi(),t.memoizedState={dehydrated:v,treeContext:Hi!==null?{id:ao,overflow:oo}:null,retryLane:536870912,hydrationErrors:null},g=O(18,null,null,Xe),g.stateNode=v,g.return=t,t.child=g,Gt=t,tt=null,v=!0):v=!1,v=!v}v&&(Dh(t,d),Ai(t))}if(d=t.memoizedState,d!==null&&(d=d.dehydrated,d!==null))return em(d)?t.lanes=32:t.lanes=536870912,null;Ia(t)}return d=o.children,o=o.fallback,r?(ko(t),r=t.mode,d=Gc({mode:"hidden",children:d},r),o=Oi(o,r,n,null),d.return=t,o.return=t,d.sibling=o,t.child=d,r=t.child,r.memoizedState=_p(n),r.childLanes=Sp(e,a,n),t.memoizedState=rv,o):(Uo(t),Tp(t,d))}var R=e.memoizedState;if(R!==null&&(d=R.dehydrated,d!==null)){if(c)t.flags&256?(Uo(t),t.flags&=-257,t=Op(e,t,n)):t.memoizedState!==null?(ko(t),t.child=e.child,t.flags|=128,t=null):(ko(t),r=o.fallback,d=t.mode,o=Gc({mode:"visible",children:o.children},d),r=Oi(r,d,n,null),r.flags|=2,o.return=t,r.return=t,o.sibling=r,t.child=o,nl(t,e.child,null,n),o=t.child,o.memoizedState=_p(n),o.childLanes=Sp(e,a,n),t.memoizedState=rv,t=r);else if(Uo(t),Ae&&console.error("We should not be hydrating here. This is a bug in React. Please file a bug."),em(d)){if(a=d.nextSibling&&d.nextSibling.dataset,a){v=a.dgst;var j=a.msg;g=a.stck;var A=a.cstck}d=j,a=v,o=g,v=r=A,r=Error(d||"The server could not finish this Suspense boundary, likely due to an error during server rendering. Switched to client rendering."),r.stack=o||"",r.digest=a,a=v===void 0?null:v,o={value:r,source:null,stack:a},typeof a=="string"&&Vm.set(r,o),Fl(o),t=Op(e,t,n)}else if(Tt||es(e,t,n,!1),a=(n&e.childLanes)!==0,Tt||a){if(a=qe,a!==null&&(o=n&-n,o=(o&42)!==0?1:Vl(o),o=(o&(a.suspendedLanes|n))!==0?0:o,o!==0&&o!==R.retryLane))throw R.retryLane=o,Ft(e,o),it(a,e,o),i1;d.data===co||Np(),t=Op(e,t,n)}else d.data===co?(t.flags|=192,t.child=e.child,t=null):(e=R.treeContext,tt=$n(d.nextSibling),Gt=t,Ae=!0,Li=null,io=!1,In=null,ja=!1,e!==null&&(wi(),Yn[Xn++]=ao,Yn[Xn++]=oo,Yn[Xn++]=Hi,ao=e.id,oo=e.overflow,Hi=t),t=Tp(t,o.children),t.flags|=4096);return t}return r?(ko(t),r=o.fallback,d=t.mode,v=e.child,g=v.sibling,o=Ga(v,{mode:"hidden",children:o.children}),o.subtreeFlags=v.subtreeFlags&65011712,g!==null?r=Ga(g,r):(r=Oi(r,d,n,null),r.flags|=2),r.return=t,o.return=t,o.sibling=r,t.child=o,o=r,r=t.child,d=e.child.memoizedState,d===null?d=_p(n):(v=d.cachePool,v!==null?(g=yt._currentValue,v=v.parent!==g?{parent:g,pool:g}:v):v=Nb(),d={baseLanes:d.baseLanes|n,cachePool:v}),r.memoizedState=d,r.childLanes=Sp(e,a,n),t.memoizedState=rv,o):(Uo(t),n=e.child,e=n.sibling,n=Ga(n,{mode:"visible",children:o.children}),n.return=t,n.sibling=null,e!==null&&(a=t.deletions,a===null?(t.deletions=[e],t.flags|=16):a.push(e)),t.child=n,t.memoizedState=null,n)}function Tp(e,t){return t=Gc({mode:"visible",children:t},e.mode),t.return=e,e.child=t}function Gc(e,t){return e=O(22,e,null,t),e.lanes=0,e.stateNode={_visibility:bf,_pendingMarkers:null,_retryCache:null,_transitions:null},e}function Op(e,t,n){return nl(t,e.child,null,n),e=Tp(t,t.pendingProps.children),e.flags|=2,t.memoizedState=null,e}function k0(e,t,n){e.lanes|=t;var a=e.alternate;a!==null&&(a.lanes|=t),Ch(e.return,t,n)}function N0(e,t){var n=Ct(e);return e=!n&&typeof Fe(e)=="function",n||e?(n=n?"array":"iterable",console.error("A nested %s was passed to row #%s in <SuspenseList />. Wrap it in an additional SuspenseList to configure its revealOrder: <SuspenseList revealOrder=...> ... <SuspenseList revealOrder=...>{%s}</SuspenseList> ... </SuspenseList>",n,t,n),!1):!0}function Ep(e,t,n,a,o){var r=e.memoizedState;r===null?e.memoizedState={isBackwards:t,rendering:null,renderingStartTime:0,last:a,tail:n,tailMode:o}:(r.isBackwards=t,r.rendering=null,r.renderingStartTime=0,r.last=a,r.tail=n,r.tailMode=o)}function H0(e,t,n){var a=t.pendingProps,o=a.revealOrder,r=a.tail;if(a=a.children,o!==void 0&&o!=="forwards"&&o!=="backwards"&&o!=="together"&&!c1[o])if(c1[o]=!0,typeof o=="string")switch(o.toLowerCase()){case"together":case"forwards":case"backwards":console.error('"%s" is not a valid value for revealOrder on <SuspenseList />. Use lowercase "%s" instead.',o,o.toLowerCase());break;case"forward":case"backward":console.error('"%s" is not a valid value for revealOrder on <SuspenseList />. React uses the -s suffix in the spelling. Use "%ss" instead.',o,o.toLowerCase());break;default:console.error('"%s" is not a supported revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',o)}else console.error('%s is not a supported value for revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',o);r===void 0||iv[r]||(r!=="collapsed"&&r!=="hidden"?(iv[r]=!0,console.error('"%s" is not a supported value for tail on <SuspenseList />. Did you mean "collapsed" or "hidden"?',r)):o!=="forwards"&&o!=="backwards"&&(iv[r]=!0,console.error('<SuspenseList tail="%s" /> is only valid if revealOrder is "forwards" or "backwards". Did you mean to specify revealOrder="forwards"?',r)));e:if((o==="forwards"||o==="backwards")&&a!==void 0&&a!==null&&a!==!1)if(Ct(a)){for(var c=0;c<a.length;c++)if(!N0(a[c],c))break e}else if(c=Fe(a),typeof c=="function"){if(c=c.call(a))for(var d=c.next(),v=0;!d.done;d=c.next()){if(!N0(d.value,v))break e;v++}}else console.error('A single row was passed to a <SuspenseList revealOrder="%s" />. This is not useful since it needs multiple rows. Did you mean to pass multiple children or an array?',o);if(zt(e,t,a,n),a=bt.current,(a&Qs)!==0)a=a&al|Qs,t.flags|=128;else{if(e!==null&&(e.flags&128)!==0)e:for(e=t.child;e!==null;){if(e.tag===13)e.memoizedState!==null&&k0(e,n,t);else if(e.tag===19)k0(e,n,t);else if(e.child!==null){e.child.return=e,e=e.child;continue}if(e===t)break e;for(;e.sibling===null;){if(e.return===null||e.return===t)break e;e=e.return}e.sibling.return=e.return,e=e.sibling}a&=al}switch(Re(bt,a,t),o){case"forwards":for(n=t.child,o=null;n!==null;)e=n.alternate,e!==null&&$c(e)===null&&(o=n),n=n.sibling;n=o,n===null?(o=t.child,t.child=null):(o=n.sibling,n.sibling=null),Ep(t,!1,o,n,r);break;case"backwards":for(n=null,o=t.child,t.child=null;o!==null;){if(e=o.alternate,e!==null&&$c(e)===null){t.child=o;break}e=o.sibling,o.sibling=n,n=o,o=e}Ep(t,!0,n,null,r);break;case"together":Ep(t,!1,null,null,void 0);break;default:t.memoizedState=null}return t.child}function Qa(e,t,n){if(e!==null&&(t.dependencies=e.dependencies),an=-1,Ko|=t.lanes,(n&t.childLanes)===0)if(e!==null){if(es(e,t,n,!1),(n&t.childLanes)===0)return null}else return null;if(e!==null&&t.child!==e.child)throw Error("Resuming work not yet implemented.");if(t.child!==null){for(e=t.child,n=Ga(e,e.pendingProps),t.child=n,n.return=t;e.sibling!==null;)e=e.sibling,n=n.sibling=Ga(e,e.pendingProps),n.return=t;n.sibling=null}return t.child}function wp(e,t){return(e.lanes&t)!==0?!0:(e=e.dependencies,!!(e!==null&&bc(e)))}function HR(e,t,n){switch(t.tag){case 3:xt(t,t.stateNode.containerInfo),Do(t,yt,e.memoizedState.cache),Wl();break;case 27:case 5:se(t);break;case 4:xt(t,t.stateNode.containerInfo);break;case 10:Do(t,t.type,t.memoizedProps.value);break;case 12:(n&t.childLanes)!==0&&(t.flags|=4),t.flags|=2048;var a=t.stateNode;a.effectDuration=-0,a.passiveEffectDuration=-0;break;case 13:if(a=t.memoizedState,a!==null)return a.dehydrated!==null?(Uo(t),t.flags|=128,null):(n&t.child.childLanes)!==0?U0(e,t,n):(Uo(t),e=Qa(e,t,n),e!==null?e.sibling:null);Uo(t);break;case 19:var o=(e.flags&128)!==0;if(a=(n&t.childLanes)!==0,a||(es(e,t,n,!1),a=(n&t.childLanes)!==0),o){if(a)return H0(e,t,n);t.flags|=128}if(o=t.memoizedState,o!==null&&(o.rendering=null,o.tail=null,o.lastEffect=null),Re(bt,bt.current,t),a)break;return null;case 22:case 23:return t.lanes=0,z0(e,t,n);case 24:Do(t,yt,e.memoizedState.cache)}return Qa(e,t,n)}function xp(e,t,n){if(t._debugNeedsRemount&&e!==null){n=wh(t.type,t.key,t.pendingProps,t._debugOwner||null,t.mode,t.lanes),n._debugStack=t._debugStack,n._debugTask=t._debugTask;var a=t.return;if(a===null)throw Error("Cannot swap the root fiber.");if(e.alternate=null,t.alternate=null,n.index=t.index,n.sibling=t.sibling,n.return=t.return,n.ref=t.ref,n._debugInfo=t._debugInfo,t===a.child)a.child=n;else{var o=a.child;if(o===null)throw Error("Expected parent to have a child.");for(;o.sibling!==t;)if(o=o.sibling,o===null)throw Error("Expected to find the previous sibling.");o.sibling=n}return t=a.deletions,t===null?(a.deletions=[e],a.flags|=16):t.push(e),n.flags|=2,n}if(e!==null)if(e.memoizedProps!==t.pendingProps||t.type!==e.type)Tt=!0;else{if(!wp(e,n)&&(t.flags&128)===0)return Tt=!1,HR(e,t,n);Tt=(e.flags&131072)!==0}else Tt=!1,(a=Ae)&&(wi(),a=(t.flags&1048576)!==0),a&&(a=t.index,wi(),Rb(t,Sf,a));switch(t.lanes=0,t.tag){case 16:e:if(a=t.pendingProps,e=Qo(t.elementType),t.type=e,typeof e=="function")Eh(e)?(a=ji(e,a),t.tag=1,t.type=e=Ti(e),t=M0(null,t,e,a,n)):(t.tag=0,bp(t,e),t.type=e=Ti(e),t=gp(null,t,e,a,n));else{if(e!=null){if(o=e.$$typeof,o===Es){t.tag=11,t.type=e=Th(e),t=x0(null,t,e,a,n);break e}else if(o===sf){t.tag=14,t=A0(null,t,e,a,n);break e}}throw t="",e!==null&&typeof e=="object"&&e.$$typeof===Sn&&(t=" Did you wrap a component in React.lazy() more than once?"),e=Ne(e)||e,Error("Element type is invalid. Received a promise that resolves to: "+e+". Lazy element type must resolve to a class or function."+t)}return t;case 0:return gp(e,t,t.type,t.pendingProps,n);case 1:return a=t.type,o=ji(a,t.pendingProps),M0(e,t,a,o,n);case 3:e:{if(xt(t,t.stateNode.containerInfo),e===null)throw Error("Should have a current fiber. This is a bug in React.");a=t.pendingProps;var r=t.memoizedState;o=r.element,Lh(e,t),is(t,a,null,n);var c=t.memoizedState;if(a=c.cache,Do(t,yt,a),a!==r.cache&&Mh(t,[yt],n,!0),os(),a=c.element,r.isDehydrated)if(r={element:a,isDehydrated:!1,cache:c.cache},t.updateQueue.baseState=r,t.memoizedState=r,t.flags&256){t=j0(e,t,a,n);break e}else if(a!==o){o=yn(Error("This root received an early update, before anything was able hydrate. Switched the entire root to client rendering."),t),Fl(o),t=j0(e,t,a,n);break e}else{switch(e=t.stateNode.containerInfo,e.nodeType){case 9:e=e.body;break;default:e=e.nodeName==="HTML"?e.ownerDocument.body:e}for(tt=$n(e.firstChild),Gt=t,Ae=!0,Li=null,io=!1,In=null,ja=!0,e=IT(t,null,a,n),t.child=e;e;)e.flags=e.flags&-3|4096,e=e.sibling}else{if(Wl(),a===o){t=Qa(e,t,n);break e}zt(e,t,a,n)}t=t.child}return t;case 26:return qc(e,t),e===null?(e=nS(t.type,null,t.pendingProps,null))?t.memoizedState=e:Ae||(e=t.type,n=t.pendingProps,a=ut($o.current),a=Wc(a).createElement(e),a[Lt]=t,a[nn]=n,Dt(a,e,n),w(a),t.stateNode=a):t.memoizedState=nS(t.type,e.memoizedProps,t.pendingProps,e.memoizedState),null;case 27:return se(t),e===null&&Ae&&(a=ut($o.current),o=q(),a=t.stateNode=eS(t.type,t.pendingProps,a,o,!1),io||(o=Y_(a,t.type,t.pendingProps,o),o!==null&&(xi(t,0).serverProps=o)),Gt=t,ja=!0,o=tt,Lo(t.type)?(zv=o,tt=$n(a.firstChild)):tt=o),zt(e,t,t.pendingProps.children,n),qc(e,t),e===null&&(t.flags|=4194304),t.child;case 5:return e===null&&Ae&&(r=q(),a=yh(t.type,r.ancestorInfo),o=tt,(c=!o)||(c=_z(o,t.type,t.pendingProps,ja),c!==null?(t.stateNode=c,io||(r=Y_(c,t.type,t.pendingProps,r),r!==null&&(xi(t,0).serverProps=r)),Gt=t,tt=$n(c.firstChild),ja=!1,r=!0):r=!1,c=!r),c&&(a&&Dh(t,o),Ai(t))),se(t),o=t.type,r=t.pendingProps,c=e!==null?e.memoizedProps:null,a=r.children,Wp(o,r)?a=null:c!==null&&Wp(o,c)&&(t.flags|=32),t.memoizedState!==null&&(o=Ph(e,t,CR,null,null,n),uu._currentValue=o),qc(e,t),zt(e,t,a,n),t.child;case 6:return e===null&&Ae&&(e=t.pendingProps,n=q(),a=n.ancestorInfo.current,e=a!=null?cc(e,a.tag,n.ancestorInfo.implicitRootScope):!0,n=tt,(a=!n)||(a=Sz(n,t.pendingProps,ja),a!==null?(t.stateNode=a,Gt=t,tt=null,a=!0):a=!1,a=!a),a&&(e&&Dh(t,n),Ai(t))),null;case 13:return U0(e,t,n);case 4:return xt(t,t.stateNode.containerInfo),a=t.pendingProps,e===null?t.child=nl(t,null,a,n):zt(e,t,a,n),t.child;case 11:return x0(e,t,t.type,t.pendingProps,n);case 7:return zt(e,t,t.pendingProps,n),t.child;case 8:return zt(e,t,t.pendingProps.children,n),t.child;case 12:return t.flags|=4,t.flags|=2048,a=t.stateNode,a.effectDuration=-0,a.passiveEffectDuration=-0,zt(e,t,t.pendingProps.children,n),t.child;case 10:return a=t.type,o=t.pendingProps,r=o.value,"value"in o||f1||(f1=!0,console.error("The `value` prop is required for the `<Context.Provider>`. Did you misspell it or forget to pass it?")),Do(t,a,r),zt(e,t,o.children,n),t.child;case 9:return o=t.type._context,a=t.pendingProps.children,typeof a!="function"&&console.error("A context consumer was rendered with multiple children, or a child that isn't a function. A context consumer expects a single child that is a function. If you did pass a function, make sure there is no trailing or leading whitespace around it."),Ri(t),o=Je(o),At(t),a=Fm(a,o,void 0),Nn(),t.flags|=1,zt(e,t,a,n),t.child;case 14:return A0(e,t,t.type,t.pendingProps,n);case 15:return R0(e,t,t.type,t.pendingProps,n);case 19:return H0(e,t,n);case 31:return a=t.pendingProps,n=t.mode,a={mode:a.mode,children:a.children},e===null?(e=Gc(a,n),e.ref=t.ref,t.child=e,e.return=t,t=e):(e=Ga(e.child,a),e.ref=t.ref,t.child=e,e.return=t,t=e),t;case 22:return z0(e,t,n);case 24:return Ri(t),a=Je(yt),e===null?(o=Nh(),o===null&&(o=qe,r=jh(),o.pooledCache=r,zi(r),r!==null&&(o.pooledCacheLanes|=n),o=r),t.memoizedState={parent:a,cache:o},Hh(t),Do(t,yt,o)):((e.lanes&n)!==0&&(Lh(e,t),is(t,null,null,n),os()),o=e.memoizedState,r=t.memoizedState,o.parent!==a?(o={parent:a,cache:a},t.memoizedState=o,t.lanes===0&&(t.memoizedState=t.updateQueue.baseState=o),Do(t,yt,a)):(a=r.cache,Do(t,yt,a),a!==o.cache&&Mh(t,[yt],n,!0))),zt(e,t,t.pendingProps.children,n),t.child;case 29:throw t.pendingProps}throw Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function Za(e){e.flags|=4}function L0(e,t){if(t.type!=="stylesheet"||(t.state.loading&Wn)!==Fi)e.flags&=-16777217;else if(e.flags|=16777216,!lS(t)){if(t=Jn.current,t!==null&&((Oe&4194048)===Oe?ka!==null:(Oe&62914560)!==Oe&&(Oe&536870912)===0||t!==ka))throw Gs=Xm,ST;e.flags|=8192}}function Yc(e,t){t!==null&&(e.flags|=4),e.flags&16384&&(t=e.tag!==22?gi():536870912,e.lanes|=t,Ii|=t)}function cs(e,t){if(!Ae)switch(e.tailMode){case"hidden":t=e.tail;for(var n=null;t!==null;)t.alternate!==null&&(n=t),t=t.sibling;n===null?e.tail=null:n.sibling=null;break;case"collapsed":n=e.tail;for(var a=null;n!==null;)n.alternate!==null&&(a=n),n=n.sibling;a===null?t||e.tail===null?e.tail=null:e.tail.sibling=null:a.sibling=null}}function We(e){var t=e.alternate!==null&&e.alternate.child===e.child,n=0,a=0;if(t)if((e.mode&Mt)!==Xe){for(var o=e.selfBaseDuration,r=e.child;r!==null;)n|=r.lanes|r.childLanes,a|=r.subtreeFlags&65011712,a|=r.flags&65011712,o+=r.treeBaseDuration,r=r.sibling;e.treeBaseDuration=o}else for(o=e.child;o!==null;)n|=o.lanes|o.childLanes,a|=o.subtreeFlags&65011712,a|=o.flags&65011712,o.return=e,o=o.sibling;else if((e.mode&Mt)!==Xe){o=e.actualDuration,r=e.selfBaseDuration;for(var c=e.child;c!==null;)n|=c.lanes|c.childLanes,a|=c.subtreeFlags,a|=c.flags,o+=c.actualDuration,r+=c.treeBaseDuration,c=c.sibling;e.actualDuration=o,e.treeBaseDuration=r}else for(o=e.child;o!==null;)n|=o.lanes|o.childLanes,a|=o.subtreeFlags,a|=o.flags,o.return=e,o=o.sibling;return e.subtreeFlags|=a,e.childLanes=n,t}function LR(e,t,n){var a=t.pendingProps;switch(zh(t),t.tag){case 31:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return We(t),null;case 1:return We(t),null;case 3:return n=t.stateNode,a=null,e!==null&&(a=e.memoizedState.cache),t.memoizedState.cache!==a&&(t.flags|=2048),Ya(yt,t),Nt(t),n.pendingContext&&(n.context=n.pendingContext,n.pendingContext=null),(e===null||e.child===null)&&(Jl(t)?(Mb(),Za(t)):e===null||e.memoizedState.isDehydrated&&(t.flags&256)===0||(t.flags|=1024,Cb())),We(t),null;case 26:return n=t.memoizedState,e===null?(Za(t),n!==null?(We(t),L0(t,n)):(We(t),t.flags&=-16777217)):n?n!==e.memoizedState?(Za(t),We(t),L0(t,n)):(We(t),t.flags&=-16777217):(e.memoizedProps!==a&&Za(t),We(t),t.flags&=-16777217),null;case 27:oe(t),n=ut($o.current);var o=t.type;if(e!==null&&t.stateNode!=null)e.memoizedProps!==a&&Za(t);else{if(!a){if(t.stateNode===null)throw Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return We(t),null}e=q(),Jl(t)?zb(t):(e=eS(o,a,n,e,!0),t.stateNode=e,Za(t))}return We(t),null;case 5:if(oe(t),n=t.type,e!==null&&t.stateNode!=null)e.memoizedProps!==a&&Za(t);else{if(!a){if(t.stateNode===null)throw Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return We(t),null}if(o=q(),Jl(t))zb(t);else{switch(e=ut($o.current),yh(n,o.ancestorInfo),o=o.context,e=Wc(e),o){case pl:e=e.createElementNS(Hr,n);break;case Qf:e=e.createElementNS(pf,n);break;default:switch(n){case"svg":e=e.createElementNS(Hr,n);break;case"math":e=e.createElementNS(pf,n);break;case"script":e=e.createElement("div"),e.innerHTML="<script><\/script>",e=e.removeChild(e.firstChild);break;case"select":e=typeof a.is=="string"?e.createElement("select",{is:a.is}):e.createElement("select"),a.multiple?e.multiple=!0:a.size&&(e.size=a.size);break;default:e=typeof a.is=="string"?e.createElement(n,{is:a.is}):e.createElement(n),n.indexOf("-")===-1&&(n!==n.toLowerCase()&&console.error("<%s /> is using incorrect casing. Use PascalCase for React components, or lowercase for HTML elements.",n),Object.prototype.toString.call(e)!=="[object HTMLUnknownElement]"||eo.call(D1,n)||(D1[n]=!0,console.error("The tag <%s> is unrecognized in this browser. If you meant to render a React component, start its name with an uppercase letter.",n)))}}e[Lt]=t,e[nn]=a;e:for(o=t.child;o!==null;){if(o.tag===5||o.tag===6)e.appendChild(o.stateNode);else if(o.tag!==4&&o.tag!==27&&o.child!==null){o.child.return=o,o=o.child;continue}if(o===t)break e;for(;o.sibling===null;){if(o.return===null||o.return===t)break e;o=o.return}o.sibling.return=o.return,o=o.sibling}t.stateNode=e;e:switch(Dt(e,n,a),n){case"button":case"input":case"select":case"textarea":e=!!a.autoFocus;break e;case"img":e=!0;break e;default:e=!1}e&&Za(t)}}return We(t),t.flags&=-16777217,null;case 6:if(e&&t.stateNode!=null)e.memoizedProps!==a&&Za(t);else{if(typeof a!="string"&&t.stateNode===null)throw Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");if(e=ut($o.current),n=q(),Jl(t)){e=t.stateNode,n=t.memoizedProps,o=!io,a=null;var r=Gt;if(r!==null)switch(r.tag){case 3:o&&(o=J_(e,n,a),o!==null&&(xi(t,0).serverProps=o));break;case 27:case 5:a=r.memoizedProps,o&&(o=J_(e,n,a),o!==null&&(xi(t,0).serverProps=o))}e[Lt]=t,e=!!(e.nodeValue===n||a!==null&&a.suppressHydrationWarning===!0||V_(e.nodeValue,n)),e||Ai(t)}else o=n.ancestorInfo.current,o!=null&&cc(a,o.tag,n.ancestorInfo.implicitRootScope),e=Wc(e).createTextNode(a),e[Lt]=t,t.stateNode=e}return We(t),null;case 13:if(a=t.memoizedState,e===null||e.memoizedState!==null&&e.memoizedState.dehydrated!==null){if(o=Jl(t),a!==null&&a.dehydrated!==null){if(e===null){if(!o)throw Error("A dehydrated suspense component was completed without a hydrated node. This is probably a bug in React.");if(o=t.memoizedState,o=o!==null?o.dehydrated:null,!o)throw Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");o[Lt]=t,We(t),(t.mode&Mt)!==Xe&&a!==null&&(o=t.child,o!==null&&(t.treeBaseDuration-=o.treeBaseDuration))}else Mb(),Wl(),(t.flags&128)===0&&(t.memoizedState=null),t.flags|=4,We(t),(t.mode&Mt)!==Xe&&a!==null&&(o=t.child,o!==null&&(t.treeBaseDuration-=o.treeBaseDuration));o=!1}else o=Cb(),e!==null&&e.memoizedState!==null&&(e.memoizedState.hydrationErrors=o),o=!0;if(!o)return t.flags&256?(Ia(t),t):(Ia(t),null)}return Ia(t),(t.flags&128)!==0?(t.lanes=n,(t.mode&Mt)!==Xe&&Tc(t),t):(n=a!==null,e=e!==null&&e.memoizedState!==null,n&&(a=t.child,o=null,a.alternate!==null&&a.alternate.memoizedState!==null&&a.alternate.memoizedState.cachePool!==null&&(o=a.alternate.memoizedState.cachePool.pool),r=null,a.memoizedState!==null&&a.memoizedState.cachePool!==null&&(r=a.memoizedState.cachePool.pool),r!==o&&(a.flags|=2048)),n!==e&&n&&(t.child.flags|=8192),Yc(t,t.updateQueue),We(t),(t.mode&Mt)!==Xe&&n&&(e=t.child,e!==null&&(t.treeBaseDuration-=e.treeBaseDuration)),null);case 4:return Nt(t),e===null&&Yp(t.stateNode.containerInfo),We(t),null;case 10:return Ya(t.type,t),We(t),null;case 19:if(ue(bt,t),o=t.memoizedState,o===null)return We(t),null;if(a=(t.flags&128)!==0,r=o.rendering,r===null)if(a)cs(o,!1);else{if(nt!==uo||e!==null&&(e.flags&128)!==0)for(e=t.child;e!==null;){if(r=$c(e),r!==null){for(t.flags|=128,cs(o,!1),e=r.updateQueue,t.updateQueue=e,Yc(t,e),t.subtreeFlags=0,e=n,n=t.child;n!==null;)Ab(n,e),n=n.sibling;return Re(bt,bt.current&al|Qs,t),t.child}e=e.sibling}o.tail!==null&&Ra()>Nf&&(t.flags|=128,a=!0,cs(o,!1),t.lanes=4194304)}else{if(!a)if(e=$c(r),e!==null){if(t.flags|=128,a=!0,e=e.updateQueue,t.updateQueue=e,Yc(t,e),cs(o,!0),o.tail===null&&o.tailMode==="hidden"&&!r.alternate&&!Ae)return We(t),null}else 2*Ra()-o.renderingStartTime>Nf&&n!==536870912&&(t.flags|=128,a=!0,cs(o,!1),t.lanes=4194304);o.isBackwards?(r.sibling=t.child,t.child=r):(e=o.last,e!==null?e.sibling=r:t.child=r,o.last=r)}return o.tail!==null?(e=o.tail,o.rendering=e,o.tail=e.sibling,o.renderingStartTime=Ra(),e.sibling=null,n=bt.current,n=a?n&al|Qs:n&al,Re(bt,n,t),e):(We(t),null);case 22:case 23:return Ia(t),Vh(t),a=t.memoizedState!==null,e!==null?e.memoizedState!==null!==a&&(t.flags|=8192):a&&(t.flags|=8192),a?(n&536870912)!==0&&(t.flags&128)===0&&(We(t),t.subtreeFlags&6&&(t.flags|=8192)):We(t),n=t.updateQueue,n!==null&&Yc(t,n.retryQueue),n=null,e!==null&&e.memoizedState!==null&&e.memoizedState.cachePool!==null&&(n=e.memoizedState.cachePool.pool),a=null,t.memoizedState!==null&&t.memoizedState.cachePool!==null&&(a=t.memoizedState.cachePool.pool),a!==n&&(t.flags|=2048),e!==null&&ue($i,t),null;case 24:return n=null,e!==null&&(n=e.memoizedState.cache),t.memoizedState.cache!==n&&(t.flags|=2048),Ya(yt,t),We(t),null;case 25:return null;case 30:return null}throw Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function BR(e,t){switch(zh(t),t.tag){case 1:return e=t.flags,e&65536?(t.flags=e&-65537|128,(t.mode&Mt)!==Xe&&Tc(t),t):null;case 3:return Ya(yt,t),Nt(t),e=t.flags,(e&65536)!==0&&(e&128)===0?(t.flags=e&-65537|128,t):null;case 26:case 27:case 5:return oe(t),null;case 13:if(Ia(t),e=t.memoizedState,e!==null&&e.dehydrated!==null){if(t.alternate===null)throw Error("Threw in newly mounted dehydrated component. This is likely a bug in React. Please file an issue.");Wl()}return e=t.flags,e&65536?(t.flags=e&-65537|128,(t.mode&Mt)!==Xe&&Tc(t),t):null;case 19:return ue(bt,t),null;case 4:return Nt(t),null;case 10:return Ya(t.type,t),null;case 22:case 23:return Ia(t),Vh(t),e!==null&&ue($i,t),e=t.flags,e&65536?(t.flags=e&-65537|128,(t.mode&Mt)!==Xe&&Tc(t),t):null;case 24:return Ya(yt,t),null;case 25:return null;default:return null}}function B0(e,t){switch(zh(t),t.tag){case 3:Ya(yt,t),Nt(t);break;case 26:case 27:case 5:oe(t);break;case 4:Nt(t);break;case 13:Ia(t);break;case 19:ue(bt,t);break;case 10:Ya(t.type,t);break;case 22:case 23:Ia(t),Vh(t),e!==null&&ue($i,t);break;case 24:Ya(yt,t)}}function Ta(e){return(e.mode&Mt)!==Xe}function V0(e,t){Ta(e)?(Sa(),fs(t,e),_a()):fs(t,e)}function Ap(e,t,n){Ta(e)?(Sa(),Or(n,e,t),_a()):Or(n,e,t)}function fs(e,t){try{var n=t.updateQueue,a=n!==null?n.lastEffect:null;if(a!==null){var o=a.next;n=o;do{if((n.tag&e)===e&&((e&gt)!==Qn?X!==null&&typeof X.markComponentPassiveEffectMountStarted=="function"&&X.markComponentPassiveEffectMountStarted(t):(e&jt)!==Qn&&X!==null&&typeof X.markComponentLayoutEffectMountStarted=="function"&&X.markComponentLayoutEffectMountStarted(t),a=void 0,(e&Yt)!==Qn&&(dl=!0),a=W(t,oC,n),(e&Yt)!==Qn&&(dl=!1),(e&gt)!==Qn?X!==null&&typeof X.markComponentPassiveEffectMountStopped=="function"&&X.markComponentPassiveEffectMountStopped():(e&jt)!==Qn&&X!==null&&typeof X.markComponentLayoutEffectMountStopped=="function"&&X.markComponentLayoutEffectMountStopped(),a!==void 0&&typeof a!="function")){var r=void 0;r=(n.tag&jt)!==0?"useLayoutEffect":(n.tag&Yt)!==0?"useInsertionEffect":"useEffect";var c=void 0;c=a===null?" You returned null. If your effect does not require clean up, return undefined (or nothing).":typeof a.then=="function"?`

It looks like you wrote `+r+`(async () => ...) or returned a Promise. Instead, write the async function inside your effect and call it immediately:

`+r+`(() => {
  async function fetchData() {
    // You can await here
    const response = await MyAPI.getData(someId);
    // ...
  }
  fetchData();
}, [someId]); // Or [] if effect doesn't need props or state

Learn more about data fetching with Hooks: https://react.dev/link/hooks-data-fetching`:" You returned: "+a,W(t,function(d,v){console.error("%s must not return anything besides a function, which is used for clean-up.%s",d,v)},r,c)}n=n.next}while(n!==o)}}catch(d){Be(t,t.return,d)}}function Or(e,t,n){try{var a=t.updateQueue,o=a!==null?a.lastEffect:null;if(o!==null){var r=o.next;a=r;do{if((a.tag&e)===e){var c=a.inst,d=c.destroy;d!==void 0&&(c.destroy=void 0,(e&gt)!==Qn?X!==null&&typeof X.markComponentPassiveEffectUnmountStarted=="function"&&X.markComponentPassiveEffectUnmountStarted(t):(e&jt)!==Qn&&X!==null&&typeof X.markComponentLayoutEffectUnmountStarted=="function"&&X.markComponentLayoutEffectUnmountStarted(t),(e&Yt)!==Qn&&(dl=!0),o=t,W(o,iC,o,n,d),(e&Yt)!==Qn&&(dl=!1),(e&gt)!==Qn?X!==null&&typeof X.markComponentPassiveEffectUnmountStopped=="function"&&X.markComponentPassiveEffectUnmountStopped():(e&jt)!==Qn&&X!==null&&typeof X.markComponentLayoutEffectUnmountStopped=="function"&&X.markComponentLayoutEffectUnmountStopped())}a=a.next}while(a!==r)}}catch(v){Be(t,t.return,v)}}function $0(e,t){Ta(e)?(Sa(),fs(t,e),_a()):fs(t,e)}function Rp(e,t,n){Ta(e)?(Sa(),Or(n,e,t),_a()):Or(n,e,t)}function P0(e){var t=e.updateQueue;if(t!==null){var n=e.stateNode;e.type.defaultProps||"ref"in e.memoizedProps||il||(n.props!==e.memoizedProps&&console.error("Expected %s props to match memoized props before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",te(e)||"instance"),n.state!==e.memoizedState&&console.error("Expected %s state to match memoized state before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",te(e)||"instance"));try{W(e,qb,t,n)}catch(a){Be(e,e.return,a)}}}function VR(e,t,n){return e.getSnapshotBeforeUpdate(t,n)}function $R(e,t){var n=t.memoizedProps,a=t.memoizedState;t=e.stateNode,e.type.defaultProps||"ref"in e.memoizedProps||il||(t.props!==e.memoizedProps&&console.error("Expected %s props to match memoized props before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",te(e)||"instance"),t.state!==e.memoizedState&&console.error("Expected %s state to match memoized state before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",te(e)||"instance"));try{var o=ji(e.type,n,e.elementType===e.type),r=W(e,VR,t,o,a);n=d1,r!==void 0||n.has(e.type)||(n.add(e.type),W(e,function(){console.error("%s.getSnapshotBeforeUpdate(): A snapshot value (or null) must be returned. You have returned undefined.",te(e))})),t.__reactInternalSnapshotBeforeUpdate=r}catch(c){Be(e,e.return,c)}}function q0(e,t,n){n.props=ji(e.type,e.memoizedProps),n.state=e.memoizedState,Ta(e)?(Sa(),W(e,BT,e,t,n),_a()):W(e,BT,e,t,n)}function PR(e){var t=e.ref;if(t!==null){switch(e.tag){case 26:case 27:case 5:var n=e.stateNode;break;case 30:n=e.stateNode;break;default:n=e.stateNode}if(typeof t=="function")if(Ta(e))try{Sa(),e.refCleanup=t(n)}finally{_a()}else e.refCleanup=t(n);else typeof t=="string"?console.error("String refs are no longer supported."):t.hasOwnProperty("current")||console.error("Unexpected ref object provided for %s. Use either a ref-setter function or React.createRef().",te(e)),t.current=n}}function ds(e,t){try{W(e,PR,e)}catch(n){Be(e,t,n)}}function Oa(e,t){var n=e.ref,a=e.refCleanup;if(n!==null)if(typeof a=="function")try{if(Ta(e))try{Sa(),W(e,a)}finally{_a(e)}else W(e,a)}catch(o){Be(e,t,o)}finally{e.refCleanup=null,e=e.alternate,e!=null&&(e.refCleanup=null)}else if(typeof n=="function")try{if(Ta(e))try{Sa(),W(e,n,null)}finally{_a(e)}else W(e,n,null)}catch(o){Be(e,t,o)}else n.current=null}function G0(e,t,n,a){var o=e.memoizedProps,r=o.id,c=o.onCommit;o=o.onRender,t=t===null?"mount":"update",Ef&&(t="nested-update"),typeof o=="function"&&o(r,t,e.actualDuration,e.treeBaseDuration,e.actualStartTime,n),typeof c=="function"&&c(e.memoizedProps.id,t,a,n)}function qR(e,t,n,a){var o=e.memoizedProps;e=o.id,o=o.onPostCommit,t=t===null?"mount":"update",Ef&&(t="nested-update"),typeof o=="function"&&o(e,t,a,n)}function Y0(e){var t=e.type,n=e.memoizedProps,a=e.stateNode;try{W(e,fz,a,t,n,e)}catch(o){Be(e,e.return,o)}}function zp(e,t,n){try{W(e,dz,e.stateNode,e.type,n,t,e)}catch(a){Be(e,e.return,a)}}function X0(e){return e.tag===5||e.tag===3||e.tag===26||e.tag===27&&Lo(e.type)||e.tag===4}function Dp(e){e:for(;;){for(;e.sibling===null;){if(e.return===null||X0(e.return))return null;e=e.return}for(e.sibling.return=e.return,e=e.sibling;e.tag!==5&&e.tag!==6&&e.tag!==18;){if(e.tag===27&&Lo(e.type)||e.flags&2||e.child===null||e.tag===4)continue e;e.child.return=e,e=e.child}if(!(e.flags&2))return e.stateNode}}function Cp(e,t,n){var a=e.tag;if(a===5||a===6)e=e.stateNode,t?(n.nodeType===9?n.body:n.nodeName==="HTML"?n.ownerDocument.body:n).insertBefore(e,t):(t=n.nodeType===9?n.body:n.nodeName==="HTML"?n.ownerDocument.body:n,t.appendChild(e),n=n._reactRootContainer,n!=null||t.onclick!==null||(t.onclick=Jc));else if(a!==4&&(a===27&&Lo(e.type)&&(n=e.stateNode,t=null),e=e.child,e!==null))for(Cp(e,t,n),e=e.sibling;e!==null;)Cp(e,t,n),e=e.sibling}function Xc(e,t,n){var a=e.tag;if(a===5||a===6)e=e.stateNode,t?n.insertBefore(e,t):n.appendChild(e);else if(a!==4&&(a===27&&Lo(e.type)&&(n=e.stateNode),e=e.child,e!==null))for(Xc(e,t,n),e=e.sibling;e!==null;)Xc(e,t,n),e=e.sibling}function GR(e){for(var t,n=e.return;n!==null;){if(X0(n)){t=n;break}n=n.return}if(t==null)throw Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");switch(t.tag){case 27:t=t.stateNode,n=Dp(e),Xc(e,n,t);break;case 5:n=t.stateNode,t.flags&32&&(Q_(n),t.flags&=-33),t=Dp(e),Xc(e,t,n);break;case 3:case 4:t=t.stateNode.containerInfo,n=Dp(e),Cp(e,n,t);break;default:throw Error("Invalid host parent fiber. This error is likely caused by a bug in React. Please file an issue.")}}function I0(e){var t=e.stateNode,n=e.memoizedProps;try{W(e,wz,e.type,n,t,e)}catch(a){Be(e,e.return,a)}}function YR(e,t){if(e=e.containerInfo,xv=Wf,e=Tb(e),_h(e)){if("selectionStart"in e)var n={start:e.selectionStart,end:e.selectionEnd};else e:{n=(n=e.ownerDocument)&&n.defaultView||window;var a=n.getSelection&&n.getSelection();if(a&&a.rangeCount!==0){n=a.anchorNode;var o=a.anchorOffset,r=a.focusNode;a=a.focusOffset;try{n.nodeType,r.nodeType}catch{n=null;break e}var c=0,d=-1,v=-1,g=0,R=0,j=e,A=null;t:for(;;){for(var U;j!==n||o!==0&&j.nodeType!==3||(d=c+o),j!==r||a!==0&&j.nodeType!==3||(v=c+a),j.nodeType===3&&(c+=j.nodeValue.length),(U=j.firstChild)!==null;)A=j,j=U;for(;;){if(j===e)break t;if(A===n&&++g===o&&(d=c),A===r&&++R===a&&(v=c),(U=j.nextSibling)!==null)break;j=A,A=j.parentNode}j=U}n=d===-1||v===-1?null:{start:d,end:v}}else n=null}n=n||{start:0,end:0}}else n=null;for(Av={focusedElem:e,selectionRange:n},Wf=!1,Ot=t;Ot!==null;)if(t=Ot,e=t.child,(t.subtreeFlags&1024)!==0&&e!==null)e.return=t,Ot=e;else for(;Ot!==null;){switch(e=t=Ot,n=e.alternate,o=e.flags,e.tag){case 0:break;case 11:case 15:break;case 1:(o&1024)!==0&&n!==null&&$R(e,n);break;case 3:if((o&1024)!==0){if(e=e.stateNode.containerInfo,n=e.nodeType,n===9)Fp(e);else if(n===1)switch(e.nodeName){case"HEAD":case"HTML":case"BODY":Fp(e);break;default:e.textContent=""}}break;case 5:case 26:case 27:case 6:case 4:case 17:break;default:if((o&1024)!==0)throw Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}if(e=t.sibling,e!==null){e.return=t.return,Ot=e;break}Ot=t.return}}function Q0(e,t,n){var a=n.flags;switch(n.tag){case 0:case 11:case 15:Ja(e,n),a&4&&V0(n,jt|Zn);break;case 1:if(Ja(e,n),a&4)if(e=n.stateNode,t===null)n.type.defaultProps||"ref"in n.memoizedProps||il||(e.props!==n.memoizedProps&&console.error("Expected %s props to match memoized props before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",te(n)||"instance"),e.state!==n.memoizedState&&console.error("Expected %s state to match memoized state before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",te(n)||"instance")),Ta(n)?(Sa(),W(n,ev,n,e),_a()):W(n,ev,n,e);else{var o=ji(n.type,t.memoizedProps);t=t.memoizedState,n.type.defaultProps||"ref"in n.memoizedProps||il||(e.props!==n.memoizedProps&&console.error("Expected %s props to match memoized props before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",te(n)||"instance"),e.state!==n.memoizedState&&console.error("Expected %s state to match memoized state before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",te(n)||"instance")),Ta(n)?(Sa(),W(n,NT,n,e,o,t,e.__reactInternalSnapshotBeforeUpdate),_a()):W(n,NT,n,e,o,t,e.__reactInternalSnapshotBeforeUpdate)}a&64&&P0(n),a&512&&ds(n,n.return);break;case 3:if(t=Xa(),Ja(e,n),a&64&&(a=n.updateQueue,a!==null)){if(o=null,n.child!==null)switch(n.child.tag){case 27:case 5:o=n.child.stateNode;break;case 1:o=n.child.stateNode}try{W(n,qb,a,o)}catch(c){Be(n,n.return,c)}}e.effectDuration+=Sc(t);break;case 27:t===null&&a&4&&I0(n);case 26:case 5:Ja(e,n),t===null&&a&4&&Y0(n),a&512&&ds(n,n.return);break;case 12:if(a&4){a=Xa(),Ja(e,n),e=n.stateNode,e.effectDuration+=ns(a);try{W(n,G0,n,t,Of,e.effectDuration)}catch(c){Be(n,n.return,c)}}else Ja(e,n);break;case 13:Ja(e,n),a&4&&J0(e,n),a&64&&(e=n.memoizedState,e!==null&&(e=e.dehydrated,e!==null&&(n=ez.bind(null,n),Tz(e,n))));break;case 22:if(a=n.memoizedState!==null||so,!a){t=t!==null&&t.memoizedState!==null||rt,o=so;var r=rt;so=a,(rt=t)&&!r?Wa(e,n,(n.subtreeFlags&8772)!==0):Ja(e,n),so=o,rt=r}break;case 30:break;default:Ja(e,n)}}function Z0(e){var t=e.alternate;t!==null&&(e.alternate=null,Z0(t)),e.child=null,e.deletions=null,e.sibling=null,e.tag===5&&(t=e.stateNode,t!==null&&Ro(t)),e.stateNode=null,e._debugOwner=null,e.return=null,e.dependencies=null,e.memoizedProps=null,e.memoizedState=null,e.pendingProps=null,e.stateNode=null,e.updateQueue=null}function Ka(e,t,n){for(n=n.child;n!==null;)K0(e,t,n),n=n.sibling}function K0(e,t,n){if(Ht&&typeof Ht.onCommitFiberUnmount=="function")try{Ht.onCommitFiberUnmount(kr,n)}catch(r){za||(za=!0,console.error("React instrumentation encountered an error: %s",r))}switch(n.tag){case 26:rt||Oa(n,t),Ka(e,t,n),n.memoizedState?n.memoizedState.count--:n.stateNode&&(n=n.stateNode,n.parentNode.removeChild(n));break;case 27:rt||Oa(n,t);var a=pt,o=on;Lo(n.type)&&(pt=n.stateNode,on=!1),Ka(e,t,n),W(n,bs,n.stateNode),pt=a,on=o;break;case 5:rt||Oa(n,t);case 6:if(a=pt,o=on,pt=null,Ka(e,t,n),pt=a,on=o,pt!==null)if(on)try{W(n,mz,pt,n.stateNode)}catch(r){Be(n,t,r)}else try{W(n,pz,pt,n.stateNode)}catch(r){Be(n,t,r)}break;case 18:pt!==null&&(on?(e=pt,Z_(e.nodeType===9?e.body:e.nodeName==="HTML"?e.ownerDocument.body:e,n.stateNode),Os(e)):Z_(pt,n.stateNode));break;case 4:a=pt,o=on,pt=n.stateNode.containerInfo,on=!0,Ka(e,t,n),pt=a,on=o;break;case 0:case 11:case 14:case 15:rt||Or(Yt,n,t),rt||Ap(n,t,jt),Ka(e,t,n);break;case 1:rt||(Oa(n,t),a=n.stateNode,typeof a.componentWillUnmount=="function"&&q0(n,t,a)),Ka(e,t,n);break;case 21:Ka(e,t,n);break;case 22:rt=(a=rt)||n.memoizedState!==null,Ka(e,t,n),rt=a;break;default:Ka(e,t,n)}}function J0(e,t){if(t.memoizedState===null&&(e=t.alternate,e!==null&&(e=e.memoizedState,e!==null&&(e=e.dehydrated,e!==null))))try{W(t,Ez,e)}catch(n){Be(t,t.return,n)}}function XR(e){switch(e.tag){case 13:case 19:var t=e.stateNode;return t===null&&(t=e.stateNode=new h1),t;case 22:return e=e.stateNode,t=e._retryCache,t===null&&(t=e._retryCache=new h1),t;default:throw Error("Unexpected Suspense handler tag ("+e.tag+"). This is a bug in React.")}}function Mp(e,t){var n=XR(e);t.forEach(function(a){var o=tz.bind(null,e,a);if(!n.has(a)){if(n.add(a),ca)if(rl!==null&&ll!==null)vs(ll,rl);else throw Error("Expected finished root and lanes to be set. This is a bug in React.");a.then(o,o)}})}function gn(e,t){var n=t.deletions;if(n!==null)for(var a=0;a<n.length;a++){var o=e,r=t,c=n[a],d=r;e:for(;d!==null;){switch(d.tag){case 27:if(Lo(d.type)){pt=d.stateNode,on=!1;break e}break;case 5:pt=d.stateNode,on=!1;break e;case 3:case 4:pt=d.stateNode.containerInfo,on=!0;break e}d=d.return}if(pt===null)throw Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");K0(o,r,c),pt=null,on=!1,o=c,r=o.alternate,r!==null&&(r.return=null),o.return=null}if(t.subtreeFlags&13878)for(t=t.child;t!==null;)W0(t,e),t=t.sibling}function W0(e,t){var n=e.alternate,a=e.flags;switch(e.tag){case 0:case 11:case 14:case 15:gn(t,e),bn(e),a&4&&(Or(Yt|Zn,e,e.return),fs(Yt|Zn,e),Ap(e,e.return,jt|Zn));break;case 1:gn(t,e),bn(e),a&512&&(rt||n===null||Oa(n,n.return)),a&64&&so&&(e=e.updateQueue,e!==null&&(a=e.callbacks,a!==null&&(n=e.shared.hiddenCallbacks,e.shared.hiddenCallbacks=n===null?a:n.concat(a))));break;case 26:var o=pa;if(gn(t,e),bn(e),a&512&&(rt||n===null||Oa(n,n.return)),a&4)if(t=n!==null?n.memoizedState:null,a=e.memoizedState,n===null)if(a===null)if(e.stateNode===null){e:{a=e.type,n=e.memoizedProps,t=o.ownerDocument||o;t:switch(a){case"title":o=t.getElementsByTagName("title")[0],(!o||o[xs]||o[Lt]||o.namespaceURI===Hr||o.hasAttribute("itemprop"))&&(o=t.createElement(a),t.head.insertBefore(o,t.querySelector("head > title"))),Dt(o,a,n),o[Lt]=e,w(o),a=o;break e;case"link":var r=iS("link","href",t).get(a+(n.href||""));if(r){for(var c=0;c<r.length;c++)if(o=r[c],o.getAttribute("href")===(n.href==null||n.href===""?null:n.href)&&o.getAttribute("rel")===(n.rel==null?null:n.rel)&&o.getAttribute("title")===(n.title==null?null:n.title)&&o.getAttribute("crossorigin")===(n.crossOrigin==null?null:n.crossOrigin)){r.splice(c,1);break t}}o=t.createElement(a),Dt(o,a,n),t.head.appendChild(o);break;case"meta":if(r=iS("meta","content",t).get(a+(n.content||""))){for(c=0;c<r.length;c++)if(o=r[c],ee(n.content,"content"),o.getAttribute("content")===(n.content==null?null:""+n.content)&&o.getAttribute("name")===(n.name==null?null:n.name)&&o.getAttribute("property")===(n.property==null?null:n.property)&&o.getAttribute("http-equiv")===(n.httpEquiv==null?null:n.httpEquiv)&&o.getAttribute("charset")===(n.charSet==null?null:n.charSet)){r.splice(c,1);break t}}o=t.createElement(a),Dt(o,a,n),t.head.appendChild(o);break;default:throw Error('getNodesForType encountered a type it did not expect: "'+a+'". This is a bug in React.')}o[Lt]=e,w(o),a=o}e.stateNode=a}else rS(o,e.type,e.stateNode);else e.stateNode=oS(o,a,e.memoizedProps);else t!==a?(t===null?n.stateNode!==null&&(n=n.stateNode,n.parentNode.removeChild(n)):t.count--,a===null?rS(o,e.type,e.stateNode):oS(o,a,e.memoizedProps)):a===null&&e.stateNode!==null&&zp(e,e.memoizedProps,n.memoizedProps);break;case 27:gn(t,e),bn(e),a&512&&(rt||n===null||Oa(n,n.return)),n!==null&&a&4&&zp(e,e.memoizedProps,n.memoizedProps);break;case 5:if(gn(t,e),bn(e),a&512&&(rt||n===null||Oa(n,n.return)),e.flags&32){t=e.stateNode;try{W(e,Q_,t)}catch(R){Be(e,e.return,R)}}a&4&&e.stateNode!=null&&(t=e.memoizedProps,zp(e,t,n!==null?n.memoizedProps:t)),a&1024&&(lv=!0,e.type!=="form"&&console.error("Unexpected host component type. Expected a form. This is a bug in React."));break;case 6:if(gn(t,e),bn(e),a&4){if(e.stateNode===null)throw Error("This should have a text node initialized. This error is likely caused by a bug in React. Please file an issue.");a=e.memoizedProps,n=n!==null?n.memoizedProps:a,t=e.stateNode;try{W(e,hz,t,n,a)}catch(R){Be(e,e.return,R)}}break;case 3:if(o=Xa(),Zf=null,r=pa,pa=Fc(t.containerInfo),gn(t,e),pa=r,bn(e),a&4&&n!==null&&n.memoizedState.isDehydrated)try{W(e,Oz,t.containerInfo)}catch(R){Be(e,e.return,R)}lv&&(lv=!1,F0(e)),t.effectDuration+=Sc(o);break;case 4:a=pa,pa=Fc(e.stateNode.containerInfo),gn(t,e),bn(e),pa=a;break;case 12:a=Xa(),gn(t,e),bn(e),e.stateNode.effectDuration+=ns(a);break;case 13:gn(t,e),bn(e),e.child.flags&8192&&e.memoizedState!==null!=(n!==null&&n.memoizedState!==null)&&(hv=Ra()),a&4&&(a=e.updateQueue,a!==null&&(e.updateQueue=null,Mp(e,a)));break;case 22:o=e.memoizedState!==null;var d=n!==null&&n.memoizedState!==null,v=so,g=rt;if(so=v||o,rt=g||d,gn(t,e),rt=g,so=v,bn(e),a&8192)e:for(t=e.stateNode,t._visibility=o?t._visibility&~bf:t._visibility|bf,o&&(n===null||d||so||rt||Ui(e)),n=null,t=e;;){if(t.tag===5||t.tag===26){if(n===null){d=n=t;try{r=d.stateNode,o?W(d,vz,r):W(d,gz,d.stateNode,d.memoizedProps)}catch(R){Be(d,d.return,R)}}}else if(t.tag===6){if(n===null){d=t;try{c=d.stateNode,o?W(d,yz,c):W(d,bz,c,d.memoizedProps)}catch(R){Be(d,d.return,R)}}}else if((t.tag!==22&&t.tag!==23||t.memoizedState===null||t===e)&&t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break e;for(;t.sibling===null;){if(t.return===null||t.return===e)break e;n===t&&(n=null),t=t.return}n===t&&(n=null),t.sibling.return=t.return,t=t.sibling}a&4&&(a=e.updateQueue,a!==null&&(n=a.retryQueue,n!==null&&(a.retryQueue=null,Mp(e,n))));break;case 19:gn(t,e),bn(e),a&4&&(a=e.updateQueue,a!==null&&(e.updateQueue=null,Mp(e,a)));break;case 30:break;case 21:break;default:gn(t,e),bn(e)}}function bn(e){var t=e.flags;if(t&2){try{W(e,GR,e)}catch(n){Be(e,e.return,n)}e.flags&=-3}t&4096&&(e.flags&=-4097)}function F0(e){if(e.subtreeFlags&1024)for(e=e.child;e!==null;){var t=e;F0(t),t.tag===5&&t.flags&1024&&t.stateNode.reset(),e=e.sibling}}function Ja(e,t){if(t.subtreeFlags&8772)for(t=t.child;t!==null;)Q0(e,t.alternate,t),t=t.sibling}function e_(e){switch(e.tag){case 0:case 11:case 14:case 15:Ap(e,e.return,jt),Ui(e);break;case 1:Oa(e,e.return);var t=e.stateNode;typeof t.componentWillUnmount=="function"&&q0(e,e.return,t),Ui(e);break;case 27:W(e,bs,e.stateNode);case 26:case 5:Oa(e,e.return),Ui(e);break;case 22:e.memoizedState===null&&Ui(e);break;case 30:Ui(e);break;default:Ui(e)}}function Ui(e){for(e=e.child;e!==null;)e_(e),e=e.sibling}function t_(e,t,n,a){var o=n.flags;switch(n.tag){case 0:case 11:case 15:Wa(e,n,a),V0(n,jt);break;case 1:if(Wa(e,n,a),t=n.stateNode,typeof t.componentDidMount=="function"&&W(n,ev,n,t),t=n.updateQueue,t!==null){e=n.stateNode;try{W(n,DR,t,e)}catch(r){Be(n,n.return,r)}}a&&o&64&&P0(n),ds(n,n.return);break;case 27:I0(n);case 26:case 5:Wa(e,n,a),a&&t===null&&o&4&&Y0(n),ds(n,n.return);break;case 12:if(a&&o&4){o=Xa(),Wa(e,n,a),a=n.stateNode,a.effectDuration+=ns(o);try{W(n,G0,n,t,Of,a.effectDuration)}catch(r){Be(n,n.return,r)}}else Wa(e,n,a);break;case 13:Wa(e,n,a),a&&o&4&&J0(e,n);break;case 22:n.memoizedState===null&&Wa(e,n,a),ds(n,n.return);break;case 30:break;default:Wa(e,n,a)}}function Wa(e,t,n){for(n=n&&(t.subtreeFlags&8772)!==0,t=t.child;t!==null;)t_(e,t.alternate,t,n),t=t.sibling}function jp(e,t){var n=null;e!==null&&e.memoizedState!==null&&e.memoizedState.cachePool!==null&&(n=e.memoizedState.cachePool.pool),e=null,t.memoizedState!==null&&t.memoizedState.cachePool!==null&&(e=t.memoizedState.cachePool.pool),e!==n&&(e!=null&&zi(e),n!=null&&ts(n))}function Up(e,t){e=null,t.alternate!==null&&(e=t.alternate.memoizedState.cache),t=t.memoizedState.cache,t!==e&&(zi(t),e!=null&&ts(e))}function Ea(e,t,n,a){if(t.subtreeFlags&10256)for(t=t.child;t!==null;)n_(e,t,n,a),t=t.sibling}function n_(e,t,n,a){var o=t.flags;switch(t.tag){case 0:case 11:case 15:Ea(e,t,n,a),o&2048&&$0(t,gt|Zn);break;case 1:Ea(e,t,n,a);break;case 3:var r=Xa();Ea(e,t,n,a),o&2048&&(n=null,t.alternate!==null&&(n=t.alternate.memoizedState.cache),t=t.memoizedState.cache,t!==n&&(zi(t),n!=null&&ts(n))),e.passiveEffectDuration+=Sc(r);break;case 12:if(o&2048){o=Xa(),Ea(e,t,n,a),e=t.stateNode,e.passiveEffectDuration+=ns(o);try{W(t,qR,t,t.alternate,Of,e.passiveEffectDuration)}catch(d){Be(t,t.return,d)}}else Ea(e,t,n,a);break;case 13:Ea(e,t,n,a);break;case 23:break;case 22:r=t.stateNode;var c=t.alternate;t.memoizedState!==null?r._visibility&no?Ea(e,t,n,a):hs(e,t):r._visibility&no?Ea(e,t,n,a):(r._visibility|=no,Er(e,t,n,a,(t.subtreeFlags&10256)!==0)),o&2048&&jp(c,t);break;case 24:Ea(e,t,n,a),o&2048&&Up(t.alternate,t);break;default:Ea(e,t,n,a)}}function Er(e,t,n,a,o){for(o=o&&(t.subtreeFlags&10256)!==0,t=t.child;t!==null;)a_(e,t,n,a,o),t=t.sibling}function a_(e,t,n,a,o){var r=t.flags;switch(t.tag){case 0:case 11:case 15:Er(e,t,n,a,o),$0(t,gt);break;case 23:break;case 22:var c=t.stateNode;t.memoizedState!==null?c._visibility&no?Er(e,t,n,a,o):hs(e,t):(c._visibility|=no,Er(e,t,n,a,o)),o&&r&2048&&jp(t.alternate,t);break;case 24:Er(e,t,n,a,o),o&&r&2048&&Up(t.alternate,t);break;default:Er(e,t,n,a,o)}}function hs(e,t){if(t.subtreeFlags&10256)for(t=t.child;t!==null;){var n=e,a=t,o=a.flags;switch(a.tag){case 22:hs(n,a),o&2048&&jp(a.alternate,a);break;case 24:hs(n,a),o&2048&&Up(a.alternate,a);break;default:hs(n,a)}t=t.sibling}}function wr(e){if(e.subtreeFlags&Zs)for(e=e.child;e!==null;)o_(e),e=e.sibling}function o_(e){switch(e.tag){case 26:wr(e),e.flags&Zs&&e.memoizedState!==null&&zz(pa,e.memoizedState,e.memoizedProps);break;case 5:wr(e);break;case 3:case 4:var t=pa;pa=Fc(e.stateNode.containerInfo),wr(e),pa=t;break;case 22:e.memoizedState===null&&(t=e.alternate,t!==null&&t.memoizedState!==null?(t=Zs,Zs=16777216,wr(e),Zs=t):wr(e));break;default:wr(e)}}function i_(e){var t=e.alternate;if(t!==null&&(e=t.child,e!==null)){t.child=null;do t=e.sibling,e.sibling=null,e=t;while(e!==null)}}function ps(e){var t=e.deletions;if((e.flags&16)!==0){if(t!==null)for(var n=0;n<t.length;n++){var a=t[n];Ot=a,s_(a,e)}i_(e)}if(e.subtreeFlags&10256)for(e=e.child;e!==null;)r_(e),e=e.sibling}function r_(e){switch(e.tag){case 0:case 11:case 15:ps(e),e.flags&2048&&Rp(e,e.return,gt|Zn);break;case 3:var t=Xa();ps(e),e.stateNode.passiveEffectDuration+=Sc(t);break;case 12:t=Xa(),ps(e),e.stateNode.passiveEffectDuration+=ns(t);break;case 22:t=e.stateNode,e.memoizedState!==null&&t._visibility&no&&(e.return===null||e.return.tag!==13)?(t._visibility&=~no,Ic(e)):ps(e);break;default:ps(e)}}function Ic(e){var t=e.deletions;if((e.flags&16)!==0){if(t!==null)for(var n=0;n<t.length;n++){var a=t[n];Ot=a,s_(a,e)}i_(e)}for(e=e.child;e!==null;)l_(e),e=e.sibling}function l_(e){switch(e.tag){case 0:case 11:case 15:Rp(e,e.return,gt),Ic(e);break;case 22:var t=e.stateNode;t._visibility&no&&(t._visibility&=~no,Ic(e));break;default:Ic(e)}}function s_(e,t){for(;Ot!==null;){var n=Ot,a=n;switch(a.tag){case 0:case 11:case 15:Rp(a,t,gt);break;case 23:case 22:a.memoizedState!==null&&a.memoizedState.cachePool!==null&&(a=a.memoizedState.cachePool.pool,a!=null&&zi(a));break;case 24:ts(a.memoizedState.cache)}if(a=n.child,a!==null)a.return=n,Ot=a;else e:for(n=e;Ot!==null;){a=Ot;var o=a.sibling,r=a.return;if(Z0(a),a===n){Ot=null;break e}if(o!==null){o.return=r,Ot=o;break e}Ot=r}}}function IR(){lC.forEach(function(e){return e()})}function u_(){var e=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0;return e||M.actQueue===null||console.error("The current testing environment is not configured to support act(...)"),e}function _n(e){if((je&Xt)!==On&&Oe!==0)return Oe&-Oe;var t=M.T;return t!==null?(t._updatedFibers||(t._updatedFibers=new Set),t._updatedFibers.add(e),e=Vi,e!==0?e:qp()):ql()}function c_(){wn===0&&(wn=(Oe&536870912)===0||Ae?_e():536870912);var e=Jn.current;return e!==null&&(e.flags|=32),wn}function it(e,t,n){if(dl&&console.error("useInsertionEffect must not schedule updates."),gv&&(Hf=!0),(e===qe&&(Ue===Yi||Ue===Xi)||e.cancelPendingCommit!==null)&&(Ar(e,0),No(e,Oe,wn,!1)),Ao(e,n),(je&Xt)!==0&&e===qe){if(Ca)switch(t.tag){case 0:case 11:case 15:e=Se&&te(Se)||"Unknown",O1.has(e)||(O1.add(e),t=te(t)||"Unknown",console.error("Cannot update a component (`%s`) while rendering a different component (`%s`). To locate the bad setState() call inside `%s`, follow the stack trace as described in https://react.dev/link/setstate-in-render",t,e,e));break;case 1:T1||(console.error("Cannot update during an existing state transition (such as within `render`). Render methods should be a pure function of props and state."),T1=!0)}}else ca&&$l(e,t,n),az(t),e===qe&&((je&Xt)===On&&(Jo|=n),nt===Gi&&No(e,Oe,wn,!1)),wa(e)}function f_(e,t,n){if((je&(Xt|ma))!==On)throw Error("Should not already be working.");var a=!n&&(t&124)===0&&(t&e.expiredLanes)===0||xo(e,t),o=a?ZR(e,t):Hp(e,t,!0),r=a;do{if(o===uo){cl&&!a&&No(e,t,0,!1);break}else{if(n=e.current.alternate,r&&!QR(n)){o=Hp(e,t,!1),r=!1;continue}if(o===sl){if(r=t,e.errorRecoveryDisabledLanes&r)var c=0;else c=e.pendingLanes&-536870913,c=c!==0?c:c&536870912?536870912:0;if(c!==0){t=c;e:{o=e;var d=c;c=tu;var v=o.current.memoizedState.isDehydrated;if(v&&(Ar(o,d).flags|=256),d=Hp(o,d,!1),d!==sl){if(fv&&!v){o.errorRecoveryDisabledLanes|=r,Jo|=r,o=Gi;break e}o=It,It=c,o!==null&&(It===null?It=o:It.push.apply(It,o))}o=d}if(r=!1,o!==sl)continue}}if(o===Js){Ar(e,0),No(e,t,0,!0);break}e:{switch(a=e,o){case uo:case Js:throw Error("Root did not complete. This is a bug in React.");case Gi:if((t&4194048)!==t)break;case Uf:No(a,t,wn,!Zo);break e;case sl:It=null;break;case sv:case p1:break;default:throw Error("Unknown root exit status.")}if(M.actQueue!==null)Lp(a,n,t,It,nu,kf,wn,Jo,Ii);else{if((t&62914560)===t&&(r=hv+v1-Ra(),10<r)){if(No(a,t,wn,!Zo),Pa(a,0,!0)!==0)break e;a.timeoutHandle=C1(d_.bind(null,a,n,It,nu,kf,t,wn,Jo,Ii,Zo,o,fC,gT,0),r);break e}d_(a,n,It,nu,kf,t,wn,Jo,Ii,Zo,o,uC,gT,0)}}}break}while(!0);wa(e)}function d_(e,t,n,a,o,r,c,d,v,g,R,j,A,U){if(e.timeoutHandle=Wi,j=t.subtreeFlags,(j&8192||(j&16785408)===16785408)&&(su={stylesheets:null,count:0,unsuspend:Rz},o_(t),j=Dz(),j!==null)){e.cancelPendingCommit=j(Lp.bind(null,e,t,r,n,a,o,c,d,v,R,cC,A,U)),No(e,r,c,!g);return}Lp(e,t,r,n,a,o,c,d,v)}function QR(e){for(var t=e;;){var n=t.tag;if((n===0||n===11||n===15)&&t.flags&16384&&(n=t.updateQueue,n!==null&&(n=n.stores,n!==null)))for(var a=0;a<n.length;a++){var o=n[a],r=o.getSnapshot;o=o.value;try{if(!qt(r(),o))return!1}catch{return!1}}if(n=t.child,t.subtreeFlags&16384&&n!==null)n.return=t,t=n;else{if(t===e)break;for(;t.sibling===null;){if(t.return===null||t.return===e)return!0;t=t.return}t.sibling.return=t.return,t=t.sibling}}return!0}function No(e,t,n,a){t&=~dv,t&=~Jo,e.suspendedLanes|=t,e.pingedLanes&=~t,a&&(e.warmLanes|=t),a=e.expirationTimes;for(var o=t;0<o;){var r=31-$t(o),c=1<<r;a[r]=-1,o&=~c}n!==0&&Bl(e,n,t)}function xr(){return(je&(Xt|ma))===On?(ys(0),!1):!0}function kp(){if(Se!==null){if(Ue===rn)var e=Se.return;else e=Se,gc(),Xh(e),tl=null,Is=0,e=Se;for(;e!==null;)B0(e.alternate,e),e=e.return;Se=null}}function Ar(e,t){var n=e.timeoutHandle;n!==Wi&&(e.timeoutHandle=Wi,EC(n)),n=e.cancelPendingCommit,n!==null&&(e.cancelPendingCommit=null,n()),kp(),qe=e,Se=n=Ga(e.current,null),Oe=t,Ue=rn,En=null,Zo=!1,cl=xo(e,t),fv=!1,nt=uo,Ii=wn=dv=Jo=Ko=0,It=tu=null,kf=!1,(t&8)!==0&&(t|=t&32);var a=e.entangledLanes;if(a!==0)for(e=e.entanglements,a&=t;0<a;){var o=31-$t(a),r=1<<o;t|=e[o],a&=~r}return Na=t,pc(),t=vT(),1e3<t-mT&&(M.recentlyCreatedOwnerStacks=0,mT=t),da.discardPendingWarnings(),n}function h_(e,t){ce=null,M.H=Mf,M.getCurrentStack=null,Ca=!1,Tn=null,t===qs||t===Af?(t=Vb(),Ue=Fs):t===ST?(t=Vb(),Ue=m1):Ue=t===i1?cv:t!==null&&typeof t=="object"&&typeof t.then=="function"?ul:Ws,En=t;var n=Se;if(n===null)nt=Js,Pc(e,yn(t,e.current));else switch(n.mode&Mt&&kh(n),Nn(),Ue){case Ws:X!==null&&typeof X.markComponentErrored=="function"&&X.markComponentErrored(n,t,Oe);break;case Yi:case Xi:case Fs:case ul:case eu:X!==null&&typeof X.markComponentSuspended=="function"&&X.markComponentSuspended(n,t,Oe)}}function p_(){var e=M.H;return M.H=Mf,e===null?Mf:e}function m_(){var e=M.A;return M.A=rC,e}function Np(){nt=Gi,Zo||(Oe&4194048)!==Oe&&Jn.current!==null||(cl=!0),(Ko&134217727)===0&&(Jo&134217727)===0||qe===null||No(qe,Oe,wn,!1)}function Hp(e,t,n){var a=je;je|=Xt;var o=p_(),r=m_();if(qe!==e||Oe!==t){if(ca){var c=e.memoizedUpdaters;0<c.size&&(vs(e,Oe),c.clear()),Pl(e,t)}nu=null,Ar(e,t)}yi(t),t=!1,c=nt;e:do try{if(Ue!==rn&&Se!==null){var d=Se,v=En;switch(Ue){case cv:kp(),c=Uf;break e;case Fs:case Yi:case Xi:case ul:Jn.current===null&&(t=!0);var g=Ue;if(Ue=rn,En=null,Rr(e,d,v,g),n&&cl){c=uo;break e}break;default:g=Ue,Ue=rn,En=null,Rr(e,d,v,g)}}v_(),c=nt;break}catch(R){h_(e,R)}while(!0);return t&&e.shellSuspendCounter++,gc(),je=a,M.H=o,M.A=r,Hl(),Se===null&&(qe=null,Oe=0,pc()),c}function v_(){for(;Se!==null;)y_(Se)}function ZR(e,t){var n=je;je|=Xt;var a=p_(),o=m_();if(qe!==e||Oe!==t){if(ca){var r=e.memoizedUpdaters;0<r.size&&(vs(e,Oe),r.clear()),Pl(e,t)}nu=null,Nf=Ra()+y1,Ar(e,t)}else cl=xo(e,t);yi(t);e:do try{if(Ue!==rn&&Se!==null)t:switch(t=Se,r=En,Ue){case Ws:Ue=rn,En=null,Rr(e,t,r,Ws);break;case Yi:case Xi:if(Lb(r)){Ue=rn,En=null,g_(t);break}t=function(){Ue!==Yi&&Ue!==Xi||qe!==e||(Ue=eu),wa(e)},r.then(t,t);break e;case Fs:Ue=eu;break e;case m1:Ue=uv;break e;case eu:Lb(r)?(Ue=rn,En=null,g_(t)):(Ue=rn,En=null,Rr(e,t,r,eu));break;case uv:var c=null;switch(Se.tag){case 26:c=Se.memoizedState;case 5:case 27:var d=Se;if(!c||lS(c)){Ue=rn,En=null;var v=d.sibling;if(v!==null)Se=v;else{var g=d.return;g!==null?(Se=g,Qc(g)):Se=null}break t}break;default:console.error("Unexpected type of fiber triggered a suspensey commit. This is a bug in React.")}Ue=rn,En=null,Rr(e,t,r,uv);break;case ul:Ue=rn,En=null,Rr(e,t,r,ul);break;case cv:kp(),nt=Uf;break e;default:throw Error("Unexpected SuspendedReason. This is a bug in React.")}M.actQueue!==null?v_():KR();break}catch(R){h_(e,R)}while(!0);return gc(),M.H=a,M.A=o,je=n,Se!==null?(X!==null&&typeof X.markRenderYielded=="function"&&X.markRenderYielded(),uo):(Hl(),qe=null,Oe=0,pc(),nt)}function KR(){for(;Se!==null&&!Xz();)y_(Se)}function y_(e){var t=e.alternate;(e.mode&Mt)!==Xe?(Uh(e),t=W(e,xp,t,e,Na),kh(e)):t=W(e,xp,t,e,Na),e.memoizedProps=e.pendingProps,t===null?Qc(e):Se=t}function g_(e){var t=W(e,JR,e);e.memoizedProps=e.pendingProps,t===null?Qc(e):Se=t}function JR(e){var t=e.alternate,n=(e.mode&Mt)!==Xe;switch(n&&Uh(e),e.tag){case 15:case 0:t=C0(t,e,e.pendingProps,e.type,void 0,Oe);break;case 11:t=C0(t,e,e.pendingProps,e.type.render,e.ref,Oe);break;case 5:Xh(e);default:B0(t,e),e=Se=Ab(e,Na),t=xp(t,e,Na)}return n&&kh(e),t}function Rr(e,t,n,a){gc(),Xh(t),tl=null,Is=0;var o=t.return;try{if(NR(e,o,t,n,Oe)){nt=Js,Pc(e,yn(n,e.current)),Se=null;return}}catch(r){if(o!==null)throw Se=o,r;nt=Js,Pc(e,yn(n,e.current)),Se=null;return}t.flags&32768?(Ae||a===Ws?e=!0:cl||(Oe&536870912)!==0?e=!1:(Zo=e=!0,(a===Yi||a===Xi||a===Fs||a===ul)&&(a=Jn.current,a!==null&&a.tag===13&&(a.flags|=16384))),b_(t,e)):Qc(t)}function Qc(e){var t=e;do{if((t.flags&32768)!==0){b_(t,Zo);return}var n=t.alternate;if(e=t.return,Uh(t),n=W(t,LR,n,t,Na),(t.mode&Mt)!==Xe&&Ub(t),n!==null){Se=n;return}if(t=t.sibling,t!==null){Se=t;return}Se=t=e}while(t!==null);nt===uo&&(nt=p1)}function b_(e,t){do{var n=BR(e.alternate,e);if(n!==null){n.flags&=32767,Se=n;return}if((e.mode&Mt)!==Xe){Ub(e),n=e.actualDuration;for(var a=e.child;a!==null;)n+=a.actualDuration,a=a.sibling;e.actualDuration=n}if(n=e.return,n!==null&&(n.flags|=32768,n.subtreeFlags=0,n.deletions=null),!t&&(e=e.sibling,e!==null)){Se=e;return}Se=e=n}while(e!==null);nt=Uf,Se=null}function Lp(e,t,n,a,o,r,c,d,v){e.cancelPendingCommit=null;do ms();while(Ut!==Qi);if(da.flushLegacyContextWarning(),da.flushPendingUnsafeLifecycleWarnings(),(je&(Xt|ma))!==On)throw Error("Should not already be working.");if(X!==null&&typeof X.markCommitStarted=="function"&&X.markCommitStarted(n),t===null)et();else{if(n===0&&console.error("finishedLanes should not be empty during a commit. This is a bug in React."),t===e.current)throw Error("Cannot commit the same tree as before. This error is likely caused by a bug in React. Please file an issue.");if(r=t.lanes|t.childLanes,r|=$m,oc(e,n,r,c,d,v),e===qe&&(Se=qe=null,Oe=0),fl=t,Fo=e,ei=n,mv=r,vv=o,S1=a,(t.subtreeFlags&10256)!==0||(t.flags&10256)!==0?(e.callbackNode=null,e.callbackPriority=0,nz(Ur,function(){return E_(),null})):(e.callbackNode=null,e.callbackPriority=0),Of=Kr(),a=(t.flags&13878)!==0,(t.subtreeFlags&13878)!==0||a){a=M.T,M.T=null,o=Me.p,Me.p=Pn,c=je,je|=ma;try{YR(e,t,n)}finally{je=c,Me.p=o,M.T=a}}Ut=g1,__(),S_(),T_()}}function __(){if(Ut===g1){Ut=Qi;var e=Fo,t=fl,n=ei,a=(t.flags&13878)!==0;if((t.subtreeFlags&13878)!==0||a){a=M.T,M.T=null;var o=Me.p;Me.p=Pn;var r=je;je|=ma;try{rl=n,ll=e,W0(t,e),ll=rl=null,n=Av;var c=Tb(e.containerInfo),d=n.focusedElem,v=n.selectionRange;if(c!==d&&d&&d.ownerDocument&&Sb(d.ownerDocument.documentElement,d)){if(v!==null&&_h(d)){var g=v.start,R=v.end;if(R===void 0&&(R=g),"selectionStart"in d)d.selectionStart=g,d.selectionEnd=Math.min(R,d.value.length);else{var j=d.ownerDocument||document,A=j&&j.defaultView||window;if(A.getSelection){var U=A.getSelection(),Z=d.textContent.length,re=Math.min(v.start,Z),Ge=v.end===void 0?re:Math.min(v.end,Z);!U.extend&&re>Ge&&(c=Ge,Ge=re,re=c);var we=_b(d,re),S=_b(d,Ge);if(we&&S&&(U.rangeCount!==1||U.anchorNode!==we.node||U.anchorOffset!==we.offset||U.focusNode!==S.node||U.focusOffset!==S.offset)){var T=j.createRange();T.setStart(we.node,we.offset),U.removeAllRanges(),re>Ge?(U.addRange(T),U.extend(S.node,S.offset)):(T.setEnd(S.node,S.offset),U.addRange(T))}}}}for(j=[],U=d;U=U.parentNode;)U.nodeType===1&&j.push({element:U,left:U.scrollLeft,top:U.scrollTop});for(typeof d.focus=="function"&&d.focus(),d=0;d<j.length;d++){var E=j[d];E.element.scrollLeft=E.left,E.element.scrollTop=E.top}}Wf=!!xv,Av=xv=null}finally{je=r,Me.p=o,M.T=a}}e.current=t,Ut=b1}}function S_(){if(Ut===b1){Ut=Qi;var e=Fo,t=fl,n=ei,a=(t.flags&8772)!==0;if((t.subtreeFlags&8772)!==0||a){a=M.T,M.T=null;var o=Me.p;Me.p=Pn;var r=je;je|=ma;try{X!==null&&typeof X.markLayoutEffectsStarted=="function"&&X.markLayoutEffectsStarted(n),rl=n,ll=e,Q0(e,t.alternate,t),ll=rl=null,X!==null&&typeof X.markLayoutEffectsStopped=="function"&&X.markLayoutEffectsStopped()}finally{je=r,Me.p=o,M.T=a}}Ut=_1}}function T_(){if(Ut===dC||Ut===_1){Ut=Qi,Iz();var e=Fo,t=fl,n=ei,a=S1,o=(t.subtreeFlags&10256)!==0||(t.flags&10256)!==0;o?Ut=pv:(Ut=Qi,fl=Fo=null,O_(e,e.pendingLanes),Zi=0,ou=null);var r=e.pendingLanes;if(r===0&&(Wo=null),o||R_(e),o=pr(n),t=t.stateNode,Ht&&typeof Ht.onCommitFiberRoot=="function")try{var c=(t.current.flags&128)===128;switch(o){case Pn:var d=bm;break;case Da:d=_m;break;case to:d=Ur;break;case df:d=Sm;break;default:d=Ur}Ht.onCommitFiberRoot(kr,t,d,c)}catch(j){za||(za=!0,console.error("React instrumentation encountered an error: %s",j))}if(ca&&e.memoizedUpdaters.clear(),IR(),a!==null){c=M.T,d=Me.p,Me.p=Pn,M.T=null;try{var v=e.onRecoverableError;for(t=0;t<a.length;t++){var g=a[t],R=WR(g.stack);W(g.source,v,g.value,R)}}finally{M.T=c,Me.p=d}}(ei&3)!==0&&ms(),wa(e),r=e.pendingLanes,(n&4194090)!==0&&(r&42)!==0?(wf=!0,e===yv?au++:(au=0,yv=e)):au=0,ys(0),et()}}function WR(e){return e={componentStack:e},Object.defineProperty(e,"digest",{get:function(){console.error('You are accessing "digest" from the errorInfo object passed to onRecoverableError. This property is no longer provided as part of errorInfo but can be accessed as a property of the Error instance itself.')}}),e}function O_(e,t){(e.pooledCacheLanes&=t)===0&&(t=e.pooledCache,t!=null&&(e.pooledCache=null,ts(t)))}function ms(e){return __(),S_(),T_(),E_()}function E_(){if(Ut!==pv)return!1;var e=Fo,t=mv;mv=0;var n=pr(ei),a=to>n?to:n;n=M.T;var o=Me.p;try{Me.p=a,M.T=null,a=vv,vv=null;var r=Fo,c=ei;if(Ut=Qi,fl=Fo=null,ei=0,(je&(Xt|ma))!==On)throw Error("Cannot flush passive effects while already rendering.");gv=!0,Hf=!1,X!==null&&typeof X.markPassiveEffectsStarted=="function"&&X.markPassiveEffectsStarted(c);var d=je;if(je|=ma,r_(r.current),n_(r,r.current,c,a),X!==null&&typeof X.markPassiveEffectsStopped=="function"&&X.markPassiveEffectsStopped(),R_(r),je=d,ys(0,!1),Hf?r===ou?Zi++:(Zi=0,ou=r):Zi=0,Hf=gv=!1,Ht&&typeof Ht.onPostCommitFiberRoot=="function")try{Ht.onPostCommitFiberRoot(kr,r)}catch(g){za||(za=!0,console.error("React instrumentation encountered an error: %s",g))}var v=r.current.stateNode;return v.effectDuration=0,v.passiveEffectDuration=0,!0}finally{Me.p=o,M.T=n,O_(e,t)}}function w_(e,t,n){t=yn(n,t),t=mp(e.stateNode,t,2),e=Mo(e,t,2),e!==null&&(Ao(e,2),wa(e))}function Be(e,t,n){if(dl=!1,e.tag===3)w_(e,e,n);else{for(;t!==null;){if(t.tag===3){w_(t,e,n);return}if(t.tag===1){var a=t.stateNode;if(typeof t.type.getDerivedStateFromError=="function"||typeof a.componentDidCatch=="function"&&(Wo===null||!Wo.has(a))){e=yn(n,e),n=vp(2),a=Mo(t,n,2),a!==null&&(yp(n,a,t,e),Ao(a,2),wa(a));return}}t=t.return}console.error(`Internal React error: Attempted to capture a commit phase error inside a detached tree. This indicates a bug in React. Potential causes include deleting the same fiber more than once, committing an already-finished tree, or an inconsistent return pointer.

Error message:

%s`,n)}}function Bp(e,t,n){var a=e.pingCache;if(a===null){a=e.pingCache=new sC;var o=new Set;a.set(t,o)}else o=a.get(t),o===void 0&&(o=new Set,a.set(t,o));o.has(n)||(fv=!0,o.add(n),a=FR.bind(null,e,t,n),ca&&vs(e,n),t.then(a,a))}function FR(e,t,n){var a=e.pingCache;a!==null&&a.delete(t),e.pingedLanes|=e.suspendedLanes&n,e.warmLanes&=~n,u_()&&M.actQueue===null&&console.error(`A suspended resource finished loading inside a test, but the event was not wrapped in act(...).

When testing, code that resolves suspended data should be wrapped into act(...):

act(() => {
  /* finish loading suspended data */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://react.dev/link/wrap-tests-with-act`),qe===e&&(Oe&n)===n&&(nt===Gi||nt===sv&&(Oe&62914560)===Oe&&Ra()-hv<v1?(je&Xt)===On&&Ar(e,0):dv|=n,Ii===Oe&&(Ii=0)),wa(e)}function x_(e,t){t===0&&(t=gi()),e=Ft(e,t),e!==null&&(Ao(e,t),wa(e))}function ez(e){var t=e.memoizedState,n=0;t!==null&&(n=t.retryLane),x_(e,n)}function tz(e,t){var n=0;switch(e.tag){case 13:var a=e.stateNode,o=e.memoizedState;o!==null&&(n=o.retryLane);break;case 19:a=e.stateNode;break;case 22:a=e.stateNode._retryCache;break;default:throw Error("Pinged unknown suspense boundary type. This is probably a bug in React.")}a!==null&&a.delete(t),x_(e,n)}function Vp(e,t,n){if((t.subtreeFlags&67117056)!==0)for(t=t.child;t!==null;){var a=e,o=t,r=o.type===lf;r=n||r,o.tag!==22?o.flags&67108864?r&&W(o,A_,a,o,(o.mode&dT)===Xe):Vp(a,o,r):o.memoizedState===null&&(r&&o.flags&8192?W(o,A_,a,o):o.subtreeFlags&67108864&&W(o,Vp,a,o,r)),t=t.sibling}}function A_(e,t){var n=2<arguments.length&&arguments[2]!==void 0?arguments[2]:!0;Ce(!0);try{e_(t),n&&l_(t),t_(e,t.alternate,t,!1),n&&a_(e,t,0,null,!1,0)}finally{Ce(!1)}}function R_(e){var t=!0;e.current.mode&(Bt|fa)||(t=!1),Vp(e,e.current,t)}function z_(e){if((je&Xt)===On){var t=e.tag;if(t===3||t===1||t===0||t===11||t===14||t===15){if(t=te(e)||"ReactComponent",Lf!==null){if(Lf.has(t))return;Lf.add(t)}else Lf=new Set([t]);W(e,function(){console.error("Can't perform a React state update on a component that hasn't mounted yet. This indicates that you have a side-effect in your render function that asynchronously later calls tries to update the component. Move this work to useEffect instead.")})}}}function vs(e,t){ca&&e.memoizedUpdaters.forEach(function(n){$l(e,n,t)})}function nz(e,t){var n=M.actQueue;return n!==null?(n.push(t),mC):gm(e,t)}function az(e){u_()&&M.actQueue===null&&W(e,function(){console.error(`An update to %s inside a test was not wrapped in act(...).

When testing, code that causes React state updates should be wrapped into act(...):

act(() => {
  /* fire events that update state */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://react.dev/link/wrap-tests-with-act`,te(e))})}function wa(e){e!==hl&&e.next===null&&(hl===null?Bf=hl=e:hl=hl.next=e),Vf=!0,M.actQueue!==null?_v||(_v=!0,j_()):bv||(bv=!0,j_())}function ys(e,t){if(!Sv&&Vf){Sv=!0;do for(var n=!1,a=Bf;a!==null;){if(e!==0){var o=a.pendingLanes;if(o===0)var r=0;else{var c=a.suspendedLanes,d=a.pingedLanes;r=(1<<31-$t(42|e)+1)-1,r&=o&~(c&~d),r=r&201326741?r&201326741|1:r?r|2:0}r!==0&&(n=!0,M_(a,r))}else r=Oe,r=Pa(a,a===qe?r:0,a.cancelPendingCommit!==null||a.timeoutHandle!==Wi),(r&3)===0||xo(a,r)||(n=!0,M_(a,r));a=a.next}while(n);Sv=!1}}function oz(){$p()}function $p(){Vf=_v=bv=!1;var e=0;Ki!==0&&(uz()&&(e=Ki),Ki=0);for(var t=Ra(),n=null,a=Bf;a!==null;){var o=a.next,r=D_(a,t);r===0?(a.next=null,n===null?Bf=o:n.next=o,o===null&&(hl=n)):(n=a,(e!==0||(r&3)!==0)&&(Vf=!0)),a=o}ys(e)}function D_(e,t){for(var n=e.suspendedLanes,a=e.pingedLanes,o=e.expirationTimes,r=e.pendingLanes&-62914561;0<r;){var c=31-$t(r),d=1<<c,v=o[c];v===-1?((d&n)===0||(d&a)!==0)&&(o[c]=rh(d,t)):v<=t&&(e.expiredLanes|=d),r&=~d}if(t=qe,n=Oe,n=Pa(e,e===t?n:0,e.cancelPendingCommit!==null||e.timeoutHandle!==Wi),a=e.callbackNode,n===0||e===t&&(Ue===Yi||Ue===Xi)||e.cancelPendingCommit!==null)return a!==null&&Pp(a),e.callbackNode=null,e.callbackPriority=0;if((n&3)===0||xo(e,n)){if(t=n&-n,t!==e.callbackPriority||M.actQueue!==null&&a!==Tv)Pp(a);else return t;switch(pr(n)){case Pn:case Da:n=_m;break;case to:n=Ur;break;case df:n=Sm;break;default:n=Ur}return a=C_.bind(null,e),M.actQueue!==null?(M.actQueue.push(a),n=Tv):n=gm(n,a),e.callbackPriority=t,e.callbackNode=n,t}return a!==null&&Pp(a),e.callbackPriority=2,e.callbackNode=null,2}function C_(e,t){if(wf=Ef=!1,Ut!==Qi&&Ut!==pv)return e.callbackNode=null,e.callbackPriority=0,null;var n=e.callbackNode;if(ms()&&e.callbackNode!==n)return null;var a=Oe;return a=Pa(e,e===qe?a:0,e.cancelPendingCommit!==null||e.timeoutHandle!==Wi),a===0?null:(f_(e,a,t),D_(e,Ra()),e.callbackNode!=null&&e.callbackNode===n?C_.bind(null,e):null)}function M_(e,t){if(ms())return null;Ef=wf,wf=!1,f_(e,t,!0)}function Pp(e){e!==Tv&&e!==null&&Yz(e)}function j_(){M.actQueue!==null&&M.actQueue.push(function(){return $p(),null}),wC(function(){(je&(Xt|ma))!==On?gm(bm,oz):$p()})}function qp(){return Ki===0&&(Ki=_e()),Ki}function U_(e){return e==null||typeof e=="symbol"||typeof e=="boolean"?null:typeof e=="function"?e:(ee(e,"action"),Ql(""+e))}function k_(e,t){var n=t.ownerDocument.createElement("input");return n.name=t.name,n.value=t.value,e.id&&n.setAttribute("form",e.id),t.parentNode.insertBefore(n,t),e=new FormData(e),n.parentNode.removeChild(n),e}function iz(e,t,n,a,o){if(t==="submit"&&n&&n.stateNode===o){var r=U_((o[nn]||null).action),c=a.submitter;c&&(t=(t=c[nn]||null)?U_(t.formAction):c.getAttribute("formAction"),t!==null&&(r=t,c=null));var d=new yf("action","action",null,a,o);e.push({event:d,listeners:[{instance:null,listener:function(){if(a.defaultPrevented){if(Ki!==0){var v=c?k_(o,c):new FormData(o),g={pending:!0,data:v,method:o.method,action:r};Object.freeze(g),sp(n,g,null,v)}}else typeof r=="function"&&(d.preventDefault(),v=c?k_(o,c):new FormData(o),g={pending:!0,data:v,method:o.method,action:r},Object.freeze(g),sp(n,g,r,v))},currentTarget:o}]})}}function Zc(e,t,n){e.currentTarget=n;try{t(e)}catch(a){av(a)}e.currentTarget=null}function N_(e,t){t=(t&4)!==0;for(var n=0;n<e.length;n++){var a=e[n];e:{var o=void 0,r=a.event;if(a=a.listeners,t)for(var c=a.length-1;0<=c;c--){var d=a[c],v=d.instance,g=d.currentTarget;if(d=d.listener,v!==o&&r.isPropagationStopped())break e;v!==null?W(v,Zc,r,d,g):Zc(r,d,g),o=v}else for(c=0;c<a.length;c++){if(d=a[c],v=d.instance,g=d.currentTarget,d=d.listener,v!==o&&r.isPropagationStopped())break e;v!==null?W(v,Zc,r,d,g):Zc(r,d,g),o=v}}}}function Ee(e,t){Ov.has(e)||console.error('Did not expect a listenToNonDelegatedEvent() call for "%s". This is a bug in React. Please file an issue.',e);var n=t[Tm];n===void 0&&(n=t[Tm]=new Set);var a=e+"__bubble";n.has(a)||(H_(t,e,2,!1),n.add(a))}function Gp(e,t,n){Ov.has(e)&&!t&&console.error('Did not expect a listenToNativeEvent() call for "%s" in the bubble phase. This is a bug in React. Please file an issue.',e);var a=0;t&&(a|=4),H_(n,e,a,t)}function Yp(e){if(!e[$f]){e[$f]=!0,_S.forEach(function(n){n!=="selectionchange"&&(Ov.has(n)||Gp(n,!1,e),Gp(n,!0,e))});var t=e.nodeType===9?e:e.ownerDocument;t===null||t[$f]||(t[$f]=!0,Gp("selectionchange",!1,t))}}function H_(e,t,n,a){switch(dS(t)){case Pn:var o=kz;break;case Da:o=Nz;break;default:o=rm}n=o.bind(null,t,n,e),o=void 0,!Dm||t!=="touchstart"&&t!=="touchmove"&&t!=="wheel"||(o=!0),a?o!==void 0?e.addEventListener(t,n,{capture:!0,passive:o}):e.addEventListener(t,n,!0):o!==void 0?e.addEventListener(t,n,{passive:o}):e.addEventListener(t,n,!1)}function Xp(e,t,n,a,o){var r=a;if((t&1)===0&&(t&2)===0&&a!==null)e:for(;;){if(a===null)return;var c=a.tag;if(c===3||c===4){var d=a.stateNode.containerInfo;if(d===o)break;if(c===4)for(c=a.return;c!==null;){var v=c.tag;if((v===3||v===4)&&c.stateNode.containerInfo===o)return;c=c.return}for(;d!==null;){if(c=ra(d),c===null)return;if(v=c.tag,v===5||v===6||v===26||v===27){a=r=c;continue e}d=d.parentNode}}a=a.return}ub(function(){var g=r,R=gh(n),j=[];e:{var A=fT.get(e);if(A!==void 0){var U=yf,Z=e;switch(e){case"keypress":if(fc(n)===0)break e;case"keydown":case"keyup":U=MD;break;case"focusin":Z="focus",U=Um;break;case"focusout":Z="blur",U=Um;break;case"beforeblur":case"afterblur":U=Um;break;case"click":if(n.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":U=JS;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":U=_D;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":U=kD;break;case lT:case sT:case uT:U=OD;break;case cT:U=HD;break;case"scroll":case"scrollend":U=gD;break;case"wheel":U=BD;break;case"copy":case"cut":case"paste":U=wD;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":U=FS;break;case"toggle":case"beforetoggle":U=$D}var re=(t&4)!==0,Ge=!re&&(e==="scroll"||e==="scrollend"),we=re?A!==null?A+"Capture":null:A;re=[];for(var S=g,T;S!==null;){var E=S;if(T=E.stateNode,E=E.tag,E!==5&&E!==26&&E!==27||T===null||we===null||(E=Zl(S,we),E!=null&&re.push(gs(S,E,T))),Ge)break;S=S.return}0<re.length&&(A=new U(A,Z,null,n,R),j.push({event:A,listeners:re}))}}if((t&7)===0){e:{if(A=e==="mouseover"||e==="pointerover",U=e==="mouseout"||e==="pointerout",A&&n!==Rs&&(Z=n.relatedTarget||n.fromElement)&&(ra(Z)||Z[qo]))break e;if((U||A)&&(A=R.window===R?R:(A=R.ownerDocument)?A.defaultView||A.parentWindow:window,U?(Z=n.relatedTarget||n.toElement,U=g,Z=Z?ra(Z):null,Z!==null&&(Ge=ie(Z),re=Z.tag,Z!==Ge||re!==5&&re!==27&&re!==6)&&(Z=null)):(U=null,Z=g),U!==Z)){if(re=JS,E="onMouseLeave",we="onMouseEnter",S="mouse",(e==="pointerout"||e==="pointerover")&&(re=FS,E="onPointerLeave",we="onPointerEnter",S="pointer"),Ge=U==null?A:zo(U),T=Z==null?A:zo(Z),A=new re(E,S+"leave",U,n,R),A.target=Ge,A.relatedTarget=T,E=null,ra(R)===g&&(re=new re(we,S+"enter",Z,n,R),re.target=T,re.relatedTarget=Ge,E=re),Ge=E,U&&Z)t:{for(re=U,we=Z,S=0,T=re;T;T=zr(T))S++;for(T=0,E=we;E;E=zr(E))T++;for(;0<S-T;)re=zr(re),S--;for(;0<T-S;)we=zr(we),T--;for(;S--;){if(re===we||we!==null&&re===we.alternate)break t;re=zr(re),we=zr(we)}re=null}else re=null;U!==null&&L_(j,A,U,re,!1),Z!==null&&Ge!==null&&L_(j,Ge,Z,re,!0)}}e:{if(A=g?zo(g):window,U=A.nodeName&&A.nodeName.toLowerCase(),U==="select"||U==="input"&&A.type==="file")var k=vb;else if(pb(A))if(iT)k=wR;else{k=OR;var P=TR}else U=A.nodeName,!U||U.toLowerCase()!=="input"||A.type!=="checkbox"&&A.type!=="radio"?g&&Il(g.elementType)&&(k=vb):k=ER;if(k&&(k=k(e,g))){mb(j,k,n,R);break e}P&&P(e,A,g),e==="focusout"&&g&&A.type==="number"&&g.memoizedProps.value!=null&&dh(A,"number",A.value)}switch(P=g?zo(g):window,e){case"focusin":(pb(P)||P.contentEditable==="true")&&(Pr=P,Nm=g,ks=null);break;case"focusout":ks=Nm=Pr=null;break;case"mousedown":Hm=!0;break;case"contextmenu":case"mouseup":case"dragend":Hm=!1,Ob(j,n,R);break;case"selectionchange":if(YD)break;case"keydown":case"keyup":Ob(j,n,R)}var fe;if(km)e:{switch(e){case"compositionstart":var K="onCompositionStart";break e;case"compositionend":K="onCompositionEnd";break e;case"compositionupdate":K="onCompositionUpdate";break e}K=void 0}else $r?db(e,n)&&(K="onCompositionEnd"):e==="keydown"&&n.keyCode===eT&&(K="onCompositionStart");K&&(tT&&n.locale!=="ko"&&($r||K!=="onCompositionStart"?K==="onCompositionEnd"&&$r&&(fe=cb()):(Go=R,Cm="value"in Go?Go.value:Go.textContent,$r=!0)),P=Kc(g,K),0<P.length&&(K=new WS(K,e,null,n,R),j.push({event:K,listeners:P}),fe?K.data=fe:(fe=hb(n),fe!==null&&(K.data=fe)))),(fe=qD?gR(e,n):bR(e,n))&&(K=Kc(g,"onBeforeInput"),0<K.length&&(P=new AD("onBeforeInput","beforeinput",null,n,R),j.push({event:P,listeners:K}),P.data=fe)),iz(j,e,g,n,R)}N_(j,t)})}function gs(e,t,n){return{instance:e,listener:t,currentTarget:n}}function Kc(e,t){for(var n=t+"Capture",a=[];e!==null;){var o=e,r=o.stateNode;if(o=o.tag,o!==5&&o!==26&&o!==27||r===null||(o=Zl(e,n),o!=null&&a.unshift(gs(e,o,r)),o=Zl(e,t),o!=null&&a.push(gs(e,o,r))),e.tag===3)return a;e=e.return}return[]}function zr(e){if(e===null)return null;do e=e.return;while(e&&e.tag!==5&&e.tag!==27);return e||null}function L_(e,t,n,a,o){for(var r=t._reactName,c=[];n!==null&&n!==a;){var d=n,v=d.alternate,g=d.stateNode;if(d=d.tag,v!==null&&v===a)break;d!==5&&d!==26&&d!==27||g===null||(v=g,o?(g=Zl(n,r),g!=null&&c.unshift(gs(n,g,v))):o||(g=Zl(n,r),g!=null&&c.push(gs(n,g,v)))),n=n.return}c.length!==0&&e.push({event:t,listeners:c})}function Ip(e,t){pR(e,t),e!=="input"&&e!=="textarea"&&e!=="select"||t==null||t.value!==null||ZS||(ZS=!0,e==="select"&&t.multiple?console.error("`value` prop on `%s` should not be null. Consider using an empty array when `multiple` is set to `true` to clear the component or `undefined` for uncontrolled components.",e):console.error("`value` prop on `%s` should not be null. Consider using an empty string to clear the component or `undefined` for uncontrolled components.",e));var n={registrationNameDependencies:ki,possibleRegistrationNames:Om};Il(e)||typeof t.is=="string"||vR(e,t,n),t.contentEditable&&!t.suppressContentEditableWarning&&t.children!=null&&console.error("A component is `contentEditable` and contains `children` managed by React. It is now your responsibility to guarantee that none of those nodes are unexpectedly modified or duplicated. This is probably not intentional.")}function St(e,t,n,a){t!==n&&(n=Ho(n),Ho(t)!==n&&(a[e]=t))}function rz(e,t,n){t.forEach(function(a){n[$_(a)]=a==="style"?Zp(e):e.getAttribute(a)})}function xa(e,t){t===!1?console.error("Expected `%s` listener to be a function, instead got `false`.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.",e,e,e):console.error("Expected `%s` listener to be a function, instead got a value of `%s` type.",e,typeof t)}function B_(e,t){return e=e.namespaceURI===pf||e.namespaceURI===Hr?e.ownerDocument.createElementNS(e.namespaceURI,e.tagName):e.ownerDocument.createElement(e.tagName),e.innerHTML=t,e.innerHTML}function Ho(e){return L(e)&&(console.error("The provided HTML markup uses a value of unsupported type %s. This value must be coerced to a string before using it here.",De(e)),ae(e)),(typeof e=="string"?e:""+e).replace(vC,`
`).replace(yC,"")}function V_(e,t){return t=Ho(t),Ho(e)===t}function Jc(){}function Ve(e,t,n,a,o,r){switch(n){case"children":typeof a=="string"?(cc(a,t,!1),t==="body"||t==="textarea"&&a===""||Xl(e,a)):(typeof a=="number"||typeof a=="bigint")&&(cc(""+a,t,!1),t!=="body"&&Xl(e,""+a));break;case"className":me(e,"class",a);break;case"tabIndex":me(e,"tabindex",a);break;case"dir":case"role":case"viewBox":case"width":case"height":me(e,n,a);break;case"style":rb(e,a,r);break;case"data":if(t!=="object"){me(e,"data",a);break}case"src":case"href":if(a===""&&(t!=="a"||n!=="href")){console.error(n==="src"?'An empty string ("") was passed to the %s attribute. This may cause the browser to download the whole page again over the network. To fix this, either do not render the element at all or pass null to %s instead of an empty string.':'An empty string ("") was passed to the %s attribute. To fix this, either do not render the element at all or pass null to %s instead of an empty string.',n,n),e.removeAttribute(n);break}if(a==null||typeof a=="function"||typeof a=="symbol"||typeof a=="boolean"){e.removeAttribute(n);break}ee(a,n),a=Ql(""+a),e.setAttribute(n,a);break;case"action":case"formAction":if(a!=null&&(t==="form"?n==="formAction"?console.error("You can only pass the formAction prop to <input> or <button>. Use the action prop on <form>."):typeof a=="function"&&(o.encType==null&&o.method==null||Gf||(Gf=!0,console.error("Cannot specify a encType or method for a form that specifies a function as the action. React provides those automatically. They will get overridden.")),o.target==null||qf||(qf=!0,console.error("Cannot specify a target for a form that specifies a function as the action. The function will always be executed in the same window."))):t==="input"||t==="button"?n==="action"?console.error("You can only pass the action prop to <form>. Use the formAction prop on <input> or <button>."):t!=="input"||o.type==="submit"||o.type==="image"||Pf?t!=="button"||o.type==null||o.type==="submit"||Pf?typeof a=="function"&&(o.name==null||x1||(x1=!0,console.error('Cannot specify a "name" prop for a button that specifies a function as a formAction. React needs it to encode which action should be invoked. It will get overridden.')),o.formEncType==null&&o.formMethod==null||Gf||(Gf=!0,console.error("Cannot specify a formEncType or formMethod for a button that specifies a function as a formAction. React provides those automatically. They will get overridden.")),o.formTarget==null||qf||(qf=!0,console.error("Cannot specify a formTarget for a button that specifies a function as a formAction. The function will always be executed in the same window."))):(Pf=!0,console.error('A button can only specify a formAction along with type="submit" or no type.')):(Pf=!0,console.error('An input can only specify a formAction along with type="submit" or type="image".')):console.error(n==="action"?"You can only pass the action prop to <form>.":"You can only pass the formAction prop to <input> or <button>.")),typeof a=="function"){e.setAttribute(n,"javascript:throw new Error('A React form was unexpectedly submitted. If you called form.submit() manually, consider using form.requestSubmit() instead. If you\\'re trying to use event.stopPropagation() in a submit event handler, consider also calling event.preventDefault().')");break}else typeof r=="function"&&(n==="formAction"?(t!=="input"&&Ve(e,t,"name",o.name,o,null),Ve(e,t,"formEncType",o.formEncType,o,null),Ve(e,t,"formMethod",o.formMethod,o,null),Ve(e,t,"formTarget",o.formTarget,o,null)):(Ve(e,t,"encType",o.encType,o,null),Ve(e,t,"method",o.method,o,null),Ve(e,t,"target",o.target,o,null)));if(a==null||typeof a=="symbol"||typeof a=="boolean"){e.removeAttribute(n);break}ee(a,n),a=Ql(""+a),e.setAttribute(n,a);break;case"onClick":a!=null&&(typeof a!="function"&&xa(n,a),e.onclick=Jc);break;case"onScroll":a!=null&&(typeof a!="function"&&xa(n,a),Ee("scroll",e));break;case"onScrollEnd":a!=null&&(typeof a!="function"&&xa(n,a),Ee("scrollend",e));break;case"dangerouslySetInnerHTML":if(a!=null){if(typeof a!="object"||!("__html"in a))throw Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://react.dev/link/dangerously-set-inner-html for more information.");if(n=a.__html,n!=null){if(o.children!=null)throw Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");e.innerHTML=n}}break;case"multiple":e.multiple=a&&typeof a!="function"&&typeof a!="symbol";break;case"muted":e.muted=a&&typeof a!="function"&&typeof a!="symbol";break;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":break;case"autoFocus":break;case"xlinkHref":if(a==null||typeof a=="function"||typeof a=="boolean"||typeof a=="symbol"){e.removeAttribute("xlink:href");break}ee(a,n),n=Ql(""+a),e.setAttributeNS(Ji,"xlink:href",n);break;case"contentEditable":case"spellCheck":case"draggable":case"value":case"autoReverse":case"externalResourcesRequired":case"focusable":case"preserveAlpha":a!=null&&typeof a!="function"&&typeof a!="symbol"?(ee(a,n),e.setAttribute(n,""+a)):e.removeAttribute(n);break;case"inert":a!==""||Yf[n]||(Yf[n]=!0,console.error("Received an empty string for a boolean attribute `%s`. This will treat the attribute as if it were false. Either pass `false` to silence this warning, or pass `true` if you used an empty string in earlier versions of React to indicate this attribute is true.",n));case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":a&&typeof a!="function"&&typeof a!="symbol"?e.setAttribute(n,""):e.removeAttribute(n);break;case"capture":case"download":a===!0?e.setAttribute(n,""):a!==!1&&a!=null&&typeof a!="function"&&typeof a!="symbol"?(ee(a,n),e.setAttribute(n,a)):e.removeAttribute(n);break;case"cols":case"rows":case"size":case"span":a!=null&&typeof a!="function"&&typeof a!="symbol"&&!isNaN(a)&&1<=a?(ee(a,n),e.setAttribute(n,a)):e.removeAttribute(n);break;case"rowSpan":case"start":a==null||typeof a=="function"||typeof a=="symbol"||isNaN(a)?e.removeAttribute(n):(ee(a,n),e.setAttribute(n,a));break;case"popover":Ee("beforetoggle",e),Ee("toggle",e),xe(e,"popover",a);break;case"xlinkActuate":_t(e,Ji,"xlink:actuate",a);break;case"xlinkArcrole":_t(e,Ji,"xlink:arcrole",a);break;case"xlinkRole":_t(e,Ji,"xlink:role",a);break;case"xlinkShow":_t(e,Ji,"xlink:show",a);break;case"xlinkTitle":_t(e,Ji,"xlink:title",a);break;case"xlinkType":_t(e,Ji,"xlink:type",a);break;case"xmlBase":_t(e,Ev,"xml:base",a);break;case"xmlLang":_t(e,Ev,"xml:lang",a);break;case"xmlSpace":_t(e,Ev,"xml:space",a);break;case"is":r!=null&&console.error('Cannot update the "is" prop after it has been initialized.'),xe(e,"is",a);break;case"innerText":case"textContent":break;case"popoverTarget":A1||a==null||typeof a!="object"||(A1=!0,console.error("The `popoverTarget` prop expects the ID of an Element as a string. Received %s instead.",a));default:!(2<n.length)||n[0]!=="o"&&n[0]!=="O"||n[1]!=="n"&&n[1]!=="N"?(n=lb(n),xe(e,n,a)):ki.hasOwnProperty(n)&&a!=null&&typeof a!="function"&&xa(n,a)}}function Qp(e,t,n,a,o,r){switch(n){case"style":rb(e,a,r);break;case"dangerouslySetInnerHTML":if(a!=null){if(typeof a!="object"||!("__html"in a))throw Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://react.dev/link/dangerously-set-inner-html for more information.");if(n=a.__html,n!=null){if(o.children!=null)throw Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");e.innerHTML=n}}break;case"children":typeof a=="string"?Xl(e,a):(typeof a=="number"||typeof a=="bigint")&&Xl(e,""+a);break;case"onScroll":a!=null&&(typeof a!="function"&&xa(n,a),Ee("scroll",e));break;case"onScrollEnd":a!=null&&(typeof a!="function"&&xa(n,a),Ee("scrollend",e));break;case"onClick":a!=null&&(typeof a!="function"&&xa(n,a),e.onclick=Jc);break;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"innerHTML":case"ref":break;case"innerText":case"textContent":break;default:if(ki.hasOwnProperty(n))a!=null&&typeof a!="function"&&xa(n,a);else e:{if(n[0]==="o"&&n[1]==="n"&&(o=n.endsWith("Capture"),t=n.slice(2,o?n.length-7:void 0),r=e[nn]||null,r=r!=null?r[n]:null,typeof r=="function"&&e.removeEventListener(t,r,o),typeof a=="function")){typeof r!="function"&&r!==null&&(n in e?e[n]=null:e.hasAttribute(n)&&e.removeAttribute(n)),e.addEventListener(t,a,o);break e}n in e?e[n]=a:a===!0?e.setAttribute(n,""):xe(e,n,a)}}}function Dt(e,t,n){switch(Ip(t,n),t){case"div":case"span":case"svg":case"path":case"a":case"g":case"p":case"li":break;case"img":Ee("error",e),Ee("load",e);var a=!1,o=!1,r;for(r in n)if(n.hasOwnProperty(r)){var c=n[r];if(c!=null)switch(r){case"src":a=!0;break;case"srcSet":o=!0;break;case"children":case"dangerouslySetInnerHTML":throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");default:Ve(e,t,r,c,n,null)}}o&&Ve(e,t,"srcSet",n.srcSet,n,null),a&&Ve(e,t,"src",n.src,n,null);return;case"input":J("input",n),Ee("invalid",e);var d=r=c=o=null,v=null,g=null;for(a in n)if(n.hasOwnProperty(a)){var R=n[a];if(R!=null)switch(a){case"name":o=R;break;case"type":c=R;break;case"checked":v=R;break;case"defaultChecked":g=R;break;case"value":r=R;break;case"defaultValue":d=R;break;case"children":case"dangerouslySetInnerHTML":if(R!=null)throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");break;default:Ve(e,t,a,R,n,null)}}Gg(e,n),Yg(e,r,d,v,g,c,o,!1),lc(e);return;case"select":J("select",n),Ee("invalid",e),a=c=r=null;for(o in n)if(n.hasOwnProperty(o)&&(d=n[o],d!=null))switch(o){case"value":r=d;break;case"defaultValue":c=d;break;case"multiple":a=d;default:Ve(e,t,o,d,n,null)}Qg(e,n),t=r,n=c,e.multiple=!!a,t!=null?vr(e,!!a,t,!1):n!=null&&vr(e,!!a,n,!0);return;case"textarea":J("textarea",n),Ee("invalid",e),r=o=a=null;for(c in n)if(n.hasOwnProperty(c)&&(d=n[c],d!=null))switch(c){case"value":a=d;break;case"defaultValue":o=d;break;case"children":r=d;break;case"dangerouslySetInnerHTML":if(d!=null)throw Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");break;default:Ve(e,t,c,d,n,null)}Zg(e,n),Jg(e,a,o,r),lc(e);return;case"option":Xg(e,n);for(v in n)if(n.hasOwnProperty(v)&&(a=n[v],a!=null))switch(v){case"selected":e.selected=a&&typeof a!="function"&&typeof a!="symbol";break;default:Ve(e,t,v,a,n,null)}return;case"dialog":Ee("beforetoggle",e),Ee("toggle",e),Ee("cancel",e),Ee("close",e);break;case"iframe":case"object":Ee("load",e);break;case"video":case"audio":for(a=0;a<iu.length;a++)Ee(iu[a],e);break;case"image":Ee("error",e),Ee("load",e);break;case"details":Ee("toggle",e);break;case"embed":case"source":case"link":Ee("error",e),Ee("load",e);case"area":case"base":case"br":case"col":case"hr":case"keygen":case"meta":case"param":case"track":case"wbr":case"menuitem":for(g in n)if(n.hasOwnProperty(g)&&(a=n[g],a!=null))switch(g){case"children":case"dangerouslySetInnerHTML":throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");default:Ve(e,t,g,a,n,null)}return;default:if(Il(t)){for(R in n)n.hasOwnProperty(R)&&(a=n[R],a!==void 0&&Qp(e,t,R,a,n,void 0));return}}for(d in n)n.hasOwnProperty(d)&&(a=n[d],a!=null&&Ve(e,t,d,a,n,null))}function lz(e,t,n,a){switch(Ip(t,a),t){case"div":case"span":case"svg":case"path":case"a":case"g":case"p":case"li":break;case"input":var o=null,r=null,c=null,d=null,v=null,g=null,R=null;for(U in n){var j=n[U];if(n.hasOwnProperty(U)&&j!=null)switch(U){case"checked":break;case"value":break;case"defaultValue":v=j;default:a.hasOwnProperty(U)||Ve(e,t,U,null,a,j)}}for(var A in a){var U=a[A];if(j=n[A],a.hasOwnProperty(A)&&(U!=null||j!=null))switch(A){case"type":r=U;break;case"name":o=U;break;case"checked":g=U;break;case"defaultChecked":R=U;break;case"value":c=U;break;case"defaultValue":d=U;break;case"children":case"dangerouslySetInnerHTML":if(U!=null)throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");break;default:U!==j&&Ve(e,t,A,U,a,j)}}t=n.type==="checkbox"||n.type==="radio"?n.checked!=null:n.value!=null,a=a.type==="checkbox"||a.type==="radio"?a.checked!=null:a.value!=null,t||!a||w1||(console.error("A component is changing an uncontrolled input to be controlled. This is likely caused by the value changing from undefined to a defined value, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://react.dev/link/controlled-components"),w1=!0),!t||a||E1||(console.error("A component is changing a controlled input to be uncontrolled. This is likely caused by the value changing from a defined to undefined, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://react.dev/link/controlled-components"),E1=!0),fh(e,c,d,v,g,R,r,o);return;case"select":U=c=d=A=null;for(r in n)if(v=n[r],n.hasOwnProperty(r)&&v!=null)switch(r){case"value":break;case"multiple":U=v;default:a.hasOwnProperty(r)||Ve(e,t,r,null,a,v)}for(o in a)if(r=a[o],v=n[o],a.hasOwnProperty(o)&&(r!=null||v!=null))switch(o){case"value":A=r;break;case"defaultValue":d=r;break;case"multiple":c=r;default:r!==v&&Ve(e,t,o,r,a,v)}a=d,t=c,n=U,A!=null?vr(e,!!t,A,!1):!!n!=!!t&&(a!=null?vr(e,!!t,a,!0):vr(e,!!t,t?[]:"",!1));return;case"textarea":U=A=null;for(d in n)if(o=n[d],n.hasOwnProperty(d)&&o!=null&&!a.hasOwnProperty(d))switch(d){case"value":break;case"children":break;default:Ve(e,t,d,null,a,o)}for(c in a)if(o=a[c],r=n[c],a.hasOwnProperty(c)&&(o!=null||r!=null))switch(c){case"value":A=o;break;case"defaultValue":U=o;break;case"children":break;case"dangerouslySetInnerHTML":if(o!=null)throw Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");break;default:o!==r&&Ve(e,t,c,o,a,r)}Kg(e,A,U);return;case"option":for(var Z in n)if(A=n[Z],n.hasOwnProperty(Z)&&A!=null&&!a.hasOwnProperty(Z))switch(Z){case"selected":e.selected=!1;break;default:Ve(e,t,Z,null,a,A)}for(v in a)if(A=a[v],U=n[v],a.hasOwnProperty(v)&&A!==U&&(A!=null||U!=null))switch(v){case"selected":e.selected=A&&typeof A!="function"&&typeof A!="symbol";break;default:Ve(e,t,v,A,a,U)}return;case"img":case"link":case"area":case"base":case"br":case"col":case"embed":case"hr":case"keygen":case"meta":case"param":case"source":case"track":case"wbr":case"menuitem":for(var re in n)A=n[re],n.hasOwnProperty(re)&&A!=null&&!a.hasOwnProperty(re)&&Ve(e,t,re,null,a,A);for(g in a)if(A=a[g],U=n[g],a.hasOwnProperty(g)&&A!==U&&(A!=null||U!=null))switch(g){case"children":case"dangerouslySetInnerHTML":if(A!=null)throw Error(t+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");break;default:Ve(e,t,g,A,a,U)}return;default:if(Il(t)){for(var Ge in n)A=n[Ge],n.hasOwnProperty(Ge)&&A!==void 0&&!a.hasOwnProperty(Ge)&&Qp(e,t,Ge,void 0,a,A);for(R in a)A=a[R],U=n[R],!a.hasOwnProperty(R)||A===U||A===void 0&&U===void 0||Qp(e,t,R,A,a,U);return}}for(var we in n)A=n[we],n.hasOwnProperty(we)&&A!=null&&!a.hasOwnProperty(we)&&Ve(e,t,we,null,a,A);for(j in a)A=a[j],U=n[j],!a.hasOwnProperty(j)||A===U||A==null&&U==null||Ve(e,t,j,A,a,U)}function $_(e){switch(e){case"class":return"className";case"for":return"htmlFor";default:return e}}function Zp(e){var t={};e=e.style;for(var n=0;n<e.length;n++){var a=e[n];t[a]=e.getPropertyValue(a)}return t}function P_(e,t,n){if(t!=null&&typeof t!="object")console.error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.");else{var a,o=a="",r;for(r in t)if(t.hasOwnProperty(r)){var c=t[r];c!=null&&typeof c!="boolean"&&c!==""&&(r.indexOf("--")===0?(Le(c,r),a+=o+r+":"+(""+c).trim()):typeof c!="number"||c===0||IS.has(r)?(Le(c,r),a+=o+r.replace(PS,"-$1").toLowerCase().replace(qS,"-ms-")+":"+(""+c).trim()):a+=o+r.replace(PS,"-$1").toLowerCase().replace(qS,"-ms-")+":"+c+"px",o=";")}a=a||null,t=e.getAttribute("style"),t!==a&&(a=Ho(a),Ho(t)!==a&&(n.style=Zp(e)))}}function Vn(e,t,n,a,o,r){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":return}else if(a!=null)switch(typeof a){case"function":case"symbol":case"boolean":break;default:if(ee(a,t),e===""+a)return}St(t,e,a,r)}function q_(e,t,n,a,o,r){if(o.delete(n),e=e.getAttribute(n),e===null){switch(typeof a){case"function":case"symbol":return}if(!a)return}else switch(typeof a){case"function":case"symbol":break;default:if(a)return}St(t,e,a,r)}function Kp(e,t,n,a,o,r){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":return}else if(a!=null)switch(typeof a){case"function":case"symbol":break;default:if(ee(a,n),e===""+a)return}St(t,e,a,r)}function G_(e,t,n,a,o,r){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":return;default:if(isNaN(a))return}else if(a!=null)switch(typeof a){case"function":case"symbol":case"boolean":break;default:if(!isNaN(a)&&(ee(a,t),e===""+a))return}St(t,e,a,r)}function Jp(e,t,n,a,o,r){if(o.delete(n),e=e.getAttribute(n),e===null)switch(typeof a){case"undefined":case"function":case"symbol":case"boolean":return}else if(a!=null)switch(typeof a){case"function":case"symbol":case"boolean":break;default:if(ee(a,t),n=Ql(""+a),e===n)return}St(t,e,a,r)}function Y_(e,t,n,a){for(var o={},r=new Set,c=e.attributes,d=0;d<c.length;d++)switch(c[d].name.toLowerCase()){case"value":break;case"checked":break;case"selected":break;default:r.add(c[d].name)}if(Il(t)){for(var v in n)if(n.hasOwnProperty(v)){var g=n[v];if(g!=null){if(ki.hasOwnProperty(v))typeof g!="function"&&xa(v,g);else if(n.suppressHydrationWarning!==!0)switch(v){case"children":typeof g!="string"&&typeof g!="number"||St("children",e.textContent,g,o);continue;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":continue;case"dangerouslySetInnerHTML":c=e.innerHTML,g=g?g.__html:void 0,g!=null&&(g=B_(e,g),St(v,c,g,o));continue;case"style":r.delete(v),P_(e,g,o);continue;case"offsetParent":case"offsetTop":case"offsetLeft":case"offsetWidth":case"offsetHeight":case"isContentEditable":case"outerText":case"outerHTML":r.delete(v.toLowerCase()),console.error("Assignment to read-only property will result in a no-op: `%s`",v);continue;case"className":r.delete("class"),c=pe(e,"class",g),St("className",c,g,o);continue;default:a.context===fo&&t!=="svg"&&t!=="math"?r.delete(v.toLowerCase()):r.delete(v),c=pe(e,v,g),St(v,c,g,o)}}}}else for(g in n)if(n.hasOwnProperty(g)&&(v=n[g],v!=null)){if(ki.hasOwnProperty(g))typeof v!="function"&&xa(g,v);else if(n.suppressHydrationWarning!==!0)switch(g){case"children":typeof v!="string"&&typeof v!="number"||St("children",e.textContent,v,o);continue;case"suppressContentEditableWarning":case"suppressHydrationWarning":case"value":case"checked":case"selected":case"defaultValue":case"defaultChecked":case"innerHTML":case"ref":continue;case"dangerouslySetInnerHTML":c=e.innerHTML,v=v?v.__html:void 0,v!=null&&(v=B_(e,v),c!==v&&(o[g]={__html:c}));continue;case"className":Vn(e,g,"class",v,r,o);continue;case"tabIndex":Vn(e,g,"tabindex",v,r,o);continue;case"style":r.delete(g),P_(e,v,o);continue;case"multiple":r.delete(g),St(g,e.multiple,v,o);continue;case"muted":r.delete(g),St(g,e.muted,v,o);continue;case"autoFocus":r.delete("autofocus"),St(g,e.autofocus,v,o);continue;case"data":if(t!=="object"){r.delete(g),c=e.getAttribute("data"),St(g,c,v,o);continue}case"src":case"href":if(!(v!==""||t==="a"&&g==="href"||t==="object"&&g==="data")){console.error(g==="src"?'An empty string ("") was passed to the %s attribute. This may cause the browser to download the whole page again over the network. To fix this, either do not render the element at all or pass null to %s instead of an empty string.':'An empty string ("") was passed to the %s attribute. To fix this, either do not render the element at all or pass null to %s instead of an empty string.',g,g);continue}Jp(e,g,g,v,r,o);continue;case"action":case"formAction":if(c=e.getAttribute(g),typeof v=="function"){r.delete(g.toLowerCase()),g==="formAction"?(r.delete("name"),r.delete("formenctype"),r.delete("formmethod"),r.delete("formtarget")):(r.delete("enctype"),r.delete("method"),r.delete("target"));continue}else if(c===gC){r.delete(g.toLowerCase()),St(g,"function",v,o);continue}Jp(e,g,g.toLowerCase(),v,r,o);continue;case"xlinkHref":Jp(e,g,"xlink:href",v,r,o);continue;case"contentEditable":Kp(e,g,"contenteditable",v,r,o);continue;case"spellCheck":Kp(e,g,"spellcheck",v,r,o);continue;case"draggable":case"autoReverse":case"externalResourcesRequired":case"focusable":case"preserveAlpha":Kp(e,g,g,v,r,o);continue;case"allowFullScreen":case"async":case"autoPlay":case"controls":case"default":case"defer":case"disabled":case"disablePictureInPicture":case"disableRemotePlayback":case"formNoValidate":case"hidden":case"loop":case"noModule":case"noValidate":case"open":case"playsInline":case"readOnly":case"required":case"reversed":case"scoped":case"seamless":case"itemScope":q_(e,g,g.toLowerCase(),v,r,o);continue;case"capture":case"download":e:{d=e;var R=c=g,j=o;if(r.delete(R),d=d.getAttribute(R),d===null)switch(typeof v){case"undefined":case"function":case"symbol":break e;default:if(v===!1)break e}else if(v!=null)switch(typeof v){case"function":case"symbol":break;case"boolean":if(v===!0&&d==="")break e;break;default:if(ee(v,c),d===""+v)break e}St(c,d,v,j)}continue;case"cols":case"rows":case"size":case"span":e:{if(d=e,R=c=g,j=o,r.delete(R),d=d.getAttribute(R),d===null)switch(typeof v){case"undefined":case"function":case"symbol":case"boolean":break e;default:if(isNaN(v)||1>v)break e}else if(v!=null)switch(typeof v){case"function":case"symbol":case"boolean":break;default:if(!(isNaN(v)||1>v)&&(ee(v,c),d===""+v))break e}St(c,d,v,j)}continue;case"rowSpan":G_(e,g,"rowspan",v,r,o);continue;case"start":G_(e,g,g,v,r,o);continue;case"xHeight":Vn(e,g,"x-height",v,r,o);continue;case"xlinkActuate":Vn(e,g,"xlink:actuate",v,r,o);continue;case"xlinkArcrole":Vn(e,g,"xlink:arcrole",v,r,o);continue;case"xlinkRole":Vn(e,g,"xlink:role",v,r,o);continue;case"xlinkShow":Vn(e,g,"xlink:show",v,r,o);continue;case"xlinkTitle":Vn(e,g,"xlink:title",v,r,o);continue;case"xlinkType":Vn(e,g,"xlink:type",v,r,o);continue;case"xmlBase":Vn(e,g,"xml:base",v,r,o);continue;case"xmlLang":Vn(e,g,"xml:lang",v,r,o);continue;case"xmlSpace":Vn(e,g,"xml:space",v,r,o);continue;case"inert":v!==""||Yf[g]||(Yf[g]=!0,console.error("Received an empty string for a boolean attribute `%s`. This will treat the attribute as if it were false. Either pass `false` to silence this warning, or pass `true` if you used an empty string in earlier versions of React to indicate this attribute is true.",g)),q_(e,g,g,v,r,o);continue;default:if(!(2<g.length)||g[0]!=="o"&&g[0]!=="O"||g[1]!=="n"&&g[1]!=="N"){d=lb(g),c=!1,a.context===fo&&t!=="svg"&&t!=="math"?r.delete(d.toLowerCase()):(R=g.toLowerCase(),R=mf.hasOwnProperty(R)&&mf[R]||null,R!==null&&R!==g&&(c=!0,r.delete(R)),r.delete(d));e:if(R=e,j=d,d=v,ge(j))if(R.hasAttribute(j))R=R.getAttribute(j),ee(d,j),d=R===""+d?d:R;else{switch(typeof d){case"function":case"symbol":break e;case"boolean":if(R=j.toLowerCase().slice(0,5),R!=="data-"&&R!=="aria-")break e}d=d===void 0?void 0:null}else d=void 0;c||St(g,d,v,o)}}}return 0<r.size&&n.suppressHydrationWarning!==!0&&rz(e,r,o),Object.keys(o).length===0?null:o}function sz(e,t){switch(e.length){case 0:return"";case 1:return e[0];case 2:return e[0]+" "+t+" "+e[1];default:return e.slice(0,-1).join(", ")+", "+t+" "+e[e.length-1]}}function Wc(e){return e.nodeType===9?e:e.ownerDocument}function X_(e){switch(e){case Hr:return pl;case pf:return Qf;default:return fo}}function I_(e,t){if(e===fo)switch(t){case"svg":return pl;case"math":return Qf;default:return fo}return e===pl&&t==="foreignObject"?fo:e}function Wp(e,t){return e==="textarea"||e==="noscript"||typeof t.children=="string"||typeof t.children=="number"||typeof t.children=="bigint"||typeof t.dangerouslySetInnerHTML=="object"&&t.dangerouslySetInnerHTML!==null&&t.dangerouslySetInnerHTML.__html!=null}function uz(){var e=window.event;return e&&e.type==="popstate"?e===Rv?!1:(Rv=e,!0):(Rv=null,!1)}function cz(e){setTimeout(function(){throw e})}function fz(e,t,n){switch(t){case"button":case"input":case"select":case"textarea":n.autoFocus&&e.focus();break;case"img":n.src?e.src=n.src:n.srcSet&&(e.srcset=n.srcSet)}}function dz(e,t,n,a){lz(e,t,n,a),e[nn]=a}function Q_(e){Xl(e,"")}function hz(e,t,n){e.nodeValue=n}function Lo(e){return e==="head"}function pz(e,t){e.removeChild(t)}function mz(e,t){(e.nodeType===9?e.body:e.nodeName==="HTML"?e.ownerDocument.body:e).removeChild(t)}function Z_(e,t){var n=t,a=0,o=0;do{var r=n.nextSibling;if(e.removeChild(n),r&&r.nodeType===8)if(n=r.data,n===If){if(0<a&&8>a){n=a;var c=e.ownerDocument;if(n&_C&&bs(c.documentElement),n&SC&&bs(c.body),n&TC)for(n=c.head,bs(n),c=n.firstChild;c;){var d=c.nextSibling,v=c.nodeName;c[xs]||v==="SCRIPT"||v==="STYLE"||v==="LINK"&&c.rel.toLowerCase()==="stylesheet"||n.removeChild(c),c=d}}if(o===0){e.removeChild(r),Os(t);return}o--}else n===Xf||n===co||n===ru?o++:a=n.charCodeAt(0)-48;else a=0;n=r}while(n);Os(t)}function vz(e){e=e.style,typeof e.setProperty=="function"?e.setProperty("display","none","important"):e.display="none"}function yz(e){e.nodeValue=""}function gz(e,t){t=t[OC],t=t!=null&&t.hasOwnProperty("display")?t.display:null,e.style.display=t==null||typeof t=="boolean"?"":(""+t).trim()}function bz(e,t){e.nodeValue=t}function Fp(e){var t=e.firstChild;for(t&&t.nodeType===10&&(t=t.nextSibling);t;){var n=t;switch(t=t.nextSibling,n.nodeName){case"HTML":case"HEAD":case"BODY":Fp(n),Ro(n);continue;case"SCRIPT":case"STYLE":continue;case"LINK":if(n.rel.toLowerCase()==="stylesheet")continue}e.removeChild(n)}}function _z(e,t,n,a){for(;e.nodeType===1;){var o=n;if(e.nodeName.toLowerCase()!==t.toLowerCase()){if(!a&&(e.nodeName!=="INPUT"||e.type!=="hidden"))break}else if(a){if(!e[xs])switch(t){case"meta":if(!e.hasAttribute("itemprop"))break;return e;case"link":if(r=e.getAttribute("rel"),r==="stylesheet"&&e.hasAttribute("data-precedence"))break;if(r!==o.rel||e.getAttribute("href")!==(o.href==null||o.href===""?null:o.href)||e.getAttribute("crossorigin")!==(o.crossOrigin==null?null:o.crossOrigin)||e.getAttribute("title")!==(o.title==null?null:o.title))break;return e;case"style":if(e.hasAttribute("data-precedence"))break;return e;case"script":if(r=e.getAttribute("src"),(r!==(o.src==null?null:o.src)||e.getAttribute("type")!==(o.type==null?null:o.type)||e.getAttribute("crossorigin")!==(o.crossOrigin==null?null:o.crossOrigin))&&r&&e.hasAttribute("async")&&!e.hasAttribute("itemprop"))break;return e;default:return e}}else if(t==="input"&&e.type==="hidden"){ee(o.name,"name");var r=o.name==null?null:""+o.name;if(o.type==="hidden"&&e.getAttribute("name")===r)return e}else return e;if(e=$n(e.nextSibling),e===null)break}return null}function Sz(e,t,n){if(t==="")return null;for(;e.nodeType!==3;)if((e.nodeType!==1||e.nodeName!=="INPUT"||e.type!=="hidden")&&!n||(e=$n(e.nextSibling),e===null))return null;return e}function em(e){return e.data===ru||e.data===co&&e.ownerDocument.readyState===z1}function Tz(e,t){var n=e.ownerDocument;if(e.data!==co||n.readyState===z1)t();else{var a=function(){t(),n.removeEventListener("DOMContentLoaded",a)};n.addEventListener("DOMContentLoaded",a),e._reactRetry=a}}function $n(e){for(;e!=null;e=e.nextSibling){var t=e.nodeType;if(t===1||t===3)break;if(t===8){if(t=e.data,t===Xf||t===ru||t===co||t===wv||t===R1)break;if(t===If)return null}}return e}function K_(e){if(e.nodeType===1){for(var t=e.nodeName.toLowerCase(),n={},a=e.attributes,o=0;o<a.length;o++){var r=a[o];n[$_(r.name)]=r.name.toLowerCase()==="style"?Zp(e):r.value}return{type:t,props:n}}return e.nodeType===8?{type:"Suspense",props:{}}:e.nodeValue}function J_(e,t,n){return n===null||n[bC]!==!0?(e.nodeValue===t?e=null:(t=Ho(t),e=Ho(e.nodeValue)===t?null:e.nodeValue),e):null}function W_(e){e=e.nextSibling;for(var t=0;e;){if(e.nodeType===8){var n=e.data;if(n===If){if(t===0)return $n(e.nextSibling);t--}else n!==Xf&&n!==ru&&n!==co||t++}e=e.nextSibling}return null}function F_(e){e=e.previousSibling;for(var t=0;e;){if(e.nodeType===8){var n=e.data;if(n===Xf||n===ru||n===co){if(t===0)return e;t--}else n===If&&t++}e=e.previousSibling}return null}function Oz(e){Os(e)}function Ez(e){Os(e)}function eS(e,t,n,a,o){switch(o&&yh(e,a.ancestorInfo),t=Wc(n),e){case"html":if(e=t.documentElement,!e)throw Error("React expected an <html> element (document.documentElement) to exist in the Document but one was not found. React never removes the documentElement for any Document it renders into so the cause is likely in some other script running on this page.");return e;case"head":if(e=t.head,!e)throw Error("React expected a <head> element (document.head) to exist in the Document but one was not found. React never removes the head for any Document it renders into so the cause is likely in some other script running on this page.");return e;case"body":if(e=t.body,!e)throw Error("React expected a <body> element (document.body) to exist in the Document but one was not found. React never removes the body for any Document it renders into so the cause is likely in some other script running on this page.");return e;default:throw Error("resolveSingletonInstance was called with an element type that is not supported. This is a bug in React.")}}function wz(e,t,n,a){if(!n[qo]&&la(n)){var o=n.tagName.toLowerCase();console.error("You are mounting a new %s component when a previous one has not first unmounted. It is an error to render more than one %s component at a time and attributes and children of these components will likely fail in unpredictable ways. Please only render a single instance of <%s> and if you need to mount a new one, ensure any previous ones have unmounted first.",o,o,o)}switch(e){case"html":case"head":case"body":break;default:console.error("acquireSingletonInstance was called with an element type that is not supported. This is a bug in React.")}for(o=n.attributes;o.length;)n.removeAttributeNode(o[0]);Dt(n,e,t),n[Lt]=a,n[nn]=t}function bs(e){for(var t=e.attributes;t.length;)e.removeAttributeNode(t[0]);Ro(e)}function Fc(e){return typeof e.getRootNode=="function"?e.getRootNode():e.nodeType===9?e:e.ownerDocument}function tS(e,t,n){var a=ml;if(a&&typeof t=="string"&&t){var o=Bn(t);o='link[rel="'+e+'"][href="'+o+'"]',typeof n=="string"&&(o+='[crossorigin="'+n+'"]'),k1.has(o)||(k1.add(o),e={rel:e,crossOrigin:n,href:t},a.querySelector(o)===null&&(t=a.createElement("link"),Dt(t,"link",e),w(t),a.head.appendChild(t)))}}function nS(e,t,n,a){var o=(o=$o.current)?Fc(o):null;if(!o)throw Error('"resourceRoot" was expected to exist. This is a bug in React.');switch(e){case"meta":case"title":return null;case"style":return typeof n.precedence=="string"&&typeof n.href=="string"?(n=Dr(n.href),t=m(o).hoistableStyles,a=t.get(n),a||(a={type:"style",instance:null,count:0,state:null},t.set(n,a)),a):{type:"void",instance:null,count:0,state:null};case"link":if(n.rel==="stylesheet"&&typeof n.href=="string"&&typeof n.precedence=="string"){e=Dr(n.href);var r=m(o).hoistableStyles,c=r.get(e);if(!c&&(o=o.ownerDocument||o,c={type:"stylesheet",instance:null,count:0,state:{loading:Fi,preload:null}},r.set(e,c),(r=o.querySelector(_s(e)))&&!r._p&&(c.instance=r,c.state.loading=lu|Wn),!Fn.has(e))){var d={rel:"preload",as:"style",href:n.href,crossOrigin:n.crossOrigin,integrity:n.integrity,media:n.media,hrefLang:n.hrefLang,referrerPolicy:n.referrerPolicy};Fn.set(e,d),r||xz(o,e,d,c.state)}if(t&&a===null)throw n=`

  - `+ef(t)+`
  + `+ef(n),Error("Expected <link> not to update to be updated to a stylesheet with precedence. Check the `rel`, `href`, and `precedence` props of this component. Alternatively, check whether two different <link> components render in the same slot or share the same key."+n);return c}if(t&&a!==null)throw n=`

  - `+ef(t)+`
  + `+ef(n),Error("Expected stylesheet with precedence to not be updated to a different kind of <link>. Check the `rel`, `href`, and `precedence` props of this component. Alternatively, check whether two different <link> components render in the same slot or share the same key."+n);return null;case"script":return t=n.async,n=n.src,typeof n=="string"&&t&&typeof t!="function"&&typeof t!="symbol"?(n=Cr(n),t=m(o).hoistableScripts,a=t.get(n),a||(a={type:"script",instance:null,count:0,state:null},t.set(n,a)),a):{type:"void",instance:null,count:0,state:null};default:throw Error('getResource encountered a type it did not expect: "'+e+'". this is a bug in React.')}}function ef(e){var t=0,n="<link";return typeof e.rel=="string"?(t++,n+=' rel="'+e.rel+'"'):eo.call(e,"rel")&&(t++,n+=' rel="'+(e.rel===null?"null":"invalid type "+typeof e.rel)+'"'),typeof e.href=="string"?(t++,n+=' href="'+e.href+'"'):eo.call(e,"href")&&(t++,n+=' href="'+(e.href===null?"null":"invalid type "+typeof e.href)+'"'),typeof e.precedence=="string"?(t++,n+=' precedence="'+e.precedence+'"'):eo.call(e,"precedence")&&(t++,n+=" precedence={"+(e.precedence===null?"null":"invalid type "+typeof e.precedence)+"}"),Object.getOwnPropertyNames(e).length>t&&(n+=" ..."),n+" />"}function Dr(e){return'href="'+Bn(e)+'"'}function _s(e){return'link[rel="stylesheet"]['+e+"]"}function aS(e){return be({},e,{"data-precedence":e.precedence,precedence:null})}function xz(e,t,n,a){e.querySelector('link[rel="preload"][as="style"]['+t+"]")?a.loading=lu:(t=e.createElement("link"),a.preload=t,t.addEventListener("load",function(){return a.loading|=lu}),t.addEventListener("error",function(){return a.loading|=j1}),Dt(t,"link",n),w(t),e.head.appendChild(t))}function Cr(e){return'[src="'+Bn(e)+'"]'}function Ss(e){return"script[async]"+e}function oS(e,t,n){if(t.count++,t.instance===null)switch(t.type){case"style":var a=e.querySelector('style[data-href~="'+Bn(n.href)+'"]');if(a)return t.instance=a,w(a),a;var o=be({},n,{"data-href":n.href,"data-precedence":n.precedence,href:null,precedence:null});return a=(e.ownerDocument||e).createElement("style"),w(a),Dt(a,"style",o),tf(a,n.precedence,e),t.instance=a;case"stylesheet":o=Dr(n.href);var r=e.querySelector(_s(o));if(r)return t.state.loading|=Wn,t.instance=r,w(r),r;a=aS(n),(o=Fn.get(o))&&tm(a,o),r=(e.ownerDocument||e).createElement("link"),w(r);var c=r;return c._p=new Promise(function(d,v){c.onload=d,c.onerror=v}),Dt(r,"link",a),t.state.loading|=Wn,tf(r,n.precedence,e),t.instance=r;case"script":return r=Cr(n.src),(o=e.querySelector(Ss(r)))?(t.instance=o,w(o),o):(a=n,(o=Fn.get(r))&&(a=be({},n),nm(a,o)),e=e.ownerDocument||e,o=e.createElement("script"),w(o),Dt(o,"link",a),e.head.appendChild(o),t.instance=o);case"void":return null;default:throw Error('acquireResource encountered a resource type it did not expect: "'+t.type+'". this is a bug in React.')}else t.type==="stylesheet"&&(t.state.loading&Wn)===Fi&&(a=t.instance,t.state.loading|=Wn,tf(a,n.precedence,e));return t.instance}function tf(e,t,n){for(var a=n.querySelectorAll('link[rel="stylesheet"][data-precedence],style[data-precedence]'),o=a.length?a[a.length-1]:null,r=o,c=0;c<a.length;c++){var d=a[c];if(d.dataset.precedence===t)r=d;else if(r!==o)break}r?r.parentNode.insertBefore(e,r.nextSibling):(t=n.nodeType===9?n.head:n,t.insertBefore(e,t.firstChild))}function tm(e,t){e.crossOrigin==null&&(e.crossOrigin=t.crossOrigin),e.referrerPolicy==null&&(e.referrerPolicy=t.referrerPolicy),e.title==null&&(e.title=t.title)}function nm(e,t){e.crossOrigin==null&&(e.crossOrigin=t.crossOrigin),e.referrerPolicy==null&&(e.referrerPolicy=t.referrerPolicy),e.integrity==null&&(e.integrity=t.integrity)}function iS(e,t,n){if(Zf===null){var a=new Map,o=Zf=new Map;o.set(n,a)}else o=Zf,a=o.get(n),a||(a=new Map,o.set(n,a));if(a.has(e))return a;for(a.set(e,null),n=n.getElementsByTagName(e),o=0;o<n.length;o++){var r=n[o];if(!(r[xs]||r[Lt]||e==="link"&&r.getAttribute("rel")==="stylesheet")&&r.namespaceURI!==Hr){var c=r.getAttribute(t)||"";c=e+c;var d=a.get(c);d?d.push(r):a.set(c,[r])}}return a}function rS(e,t,n){e=e.ownerDocument||e,e.head.insertBefore(n,t==="title"?e.querySelector("head > title"):null)}function Az(e,t,n){var a=!n.ancestorInfo.containerTagInScope;if(n.context===pl||t.itemProp!=null)return!a||t.itemProp==null||e!=="meta"&&e!=="title"&&e!=="style"&&e!=="link"&&e!=="script"||console.error("Cannot render a <%s> outside the main document if it has an `itemProp` prop. `itemProp` suggests the tag belongs to an `itemScope` which can appear anywhere in the DOM. If you were intending for React to hoist this <%s> remove the `itemProp` prop. Otherwise, try moving this tag into the <head> or <body> of the Document.",e,e),!1;switch(e){case"meta":case"title":return!0;case"style":if(typeof t.precedence!="string"||typeof t.href!="string"||t.href===""){a&&console.error('Cannot render a <style> outside the main document without knowing its precedence and a unique href key. React can hoist and deduplicate <style> tags if you provide a `precedence` prop along with an `href` prop that does not conflict with the `href` values used in any other hoisted <style> or <link rel="stylesheet" ...> tags.  Note that hoisting <style> tags is considered an advanced feature that most will not use directly. Consider moving the <style> tag to the <head> or consider adding a `precedence="default"` and `href="some unique resource identifier"`.');break}return!0;case"link":if(typeof t.rel!="string"||typeof t.href!="string"||t.href===""||t.onLoad||t.onError){if(t.rel==="stylesheet"&&typeof t.precedence=="string"){e=t.href;var o=t.onError,r=t.disabled;n=[],t.onLoad&&n.push("`onLoad`"),o&&n.push("`onError`"),r!=null&&n.push("`disabled`"),o=sz(n,"and"),o+=n.length===1?" prop":" props",r=n.length===1?"an "+o:"the "+o,n.length&&console.error('React encountered a <link rel="stylesheet" href="%s" ... /> with a `precedence` prop that also included %s. The presence of loading and error handlers indicates an intent to manage the stylesheet loading state from your from your Component code and React will not hoist or deduplicate this stylesheet. If your intent was to have React hoist and deduplciate this stylesheet using the `precedence` prop remove the %s, otherwise remove the `precedence` prop.',e,r,o)}a&&(typeof t.rel!="string"||typeof t.href!="string"||t.href===""?console.error("Cannot render a <link> outside the main document without a `rel` and `href` prop. Try adding a `rel` and/or `href` prop to this <link> or moving the link into the <head> tag"):(t.onError||t.onLoad)&&console.error("Cannot render a <link> with onLoad or onError listeners outside the main document. Try removing onLoad={...} and onError={...} or moving it into the root <head> tag or somewhere in the <body>."));break}switch(t.rel){case"stylesheet":return e=t.precedence,t=t.disabled,typeof e!="string"&&a&&console.error('Cannot render a <link rel="stylesheet" /> outside the main document without knowing its precedence. Consider adding precedence="default" or moving it into the root <head> tag.'),typeof e=="string"&&t==null;default:return!0}case"script":if(e=t.async&&typeof t.async!="function"&&typeof t.async!="symbol",!e||t.onLoad||t.onError||!t.src||typeof t.src!="string"){a&&(e?t.onLoad||t.onError?console.error("Cannot render a <script> with onLoad or onError listeners outside the main document. Try removing onLoad={...} and onError={...} or moving it into the root <head> tag or somewhere in the <body>."):console.error("Cannot render a <script> outside the main document without `async={true}` and a non-empty `src` prop. Ensure there is a valid `src` and either make the script async or move it into the root <head> tag or somewhere in the <body>."):console.error('Cannot render a sync or defer <script> outside the main document without knowing its order. Try adding async="" or moving it into the root <head> tag.'));break}return!0;case"noscript":case"template":a&&console.error("Cannot render <%s> outside the main document. Try moving it into the root <head> tag.",e)}return!1}function lS(e){return!(e.type==="stylesheet"&&(e.state.loading&U1)===Fi)}function Rz(){}function zz(e,t,n){if(su===null)throw Error("Internal React Error: suspendedState null when it was expected to exists. Please report this as a React bug.");var a=su;if(t.type==="stylesheet"&&(typeof n.media!="string"||matchMedia(n.media).matches!==!1)&&(t.state.loading&Wn)===Fi){if(t.instance===null){var o=Dr(n.href),r=e.querySelector(_s(o));if(r){e=r._p,e!==null&&typeof e=="object"&&typeof e.then=="function"&&(a.count++,a=nf.bind(a),e.then(a,a)),t.state.loading|=Wn,t.instance=r,w(r);return}r=e.ownerDocument||e,n=aS(n),(o=Fn.get(o))&&tm(n,o),r=r.createElement("link"),w(r);var c=r;c._p=new Promise(function(d,v){c.onload=d,c.onerror=v}),Dt(r,"link",n),t.instance=r}a.stylesheets===null&&(a.stylesheets=new Map),a.stylesheets.set(t,e),(e=t.state.preload)&&(t.state.loading&U1)===Fi&&(a.count++,t=nf.bind(a),e.addEventListener("load",t),e.addEventListener("error",t))}}function Dz(){if(su===null)throw Error("Internal React Error: suspendedState null when it was expected to exists. Please report this as a React bug.");var e=su;return e.stylesheets&&e.count===0&&am(e,e.stylesheets),0<e.count?function(t){var n=setTimeout(function(){if(e.stylesheets&&am(e,e.stylesheets),e.unsuspend){var a=e.unsuspend;e.unsuspend=null,a()}},6e4);return e.unsuspend=t,function(){e.unsuspend=null,clearTimeout(n)}}:null}function nf(){if(this.count--,this.count===0){if(this.stylesheets)am(this,this.stylesheets);else if(this.unsuspend){var e=this.unsuspend;this.unsuspend=null,e()}}}function am(e,t){e.stylesheets=null,e.unsuspend!==null&&(e.count++,Kf=new Map,t.forEach(Cz,e),Kf=null,nf.call(e))}function Cz(e,t){if(!(t.state.loading&Wn)){var n=Kf.get(e);if(n)var a=n.get(Dv);else{n=new Map,Kf.set(e,n);for(var o=e.querySelectorAll("link[data-precedence],style[data-precedence]"),r=0;r<o.length;r++){var c=o[r];(c.nodeName==="LINK"||c.getAttribute("media")!=="not all")&&(n.set(c.dataset.precedence,c),a=c)}a&&n.set(Dv,a)}o=t.instance,c=o.getAttribute("data-precedence"),r=n.get(c)||a,r===a&&n.set(Dv,o),n.set(c,o),this.count++,a=nf.bind(this),o.addEventListener("load",a),o.addEventListener("error",a),r?r.parentNode.insertBefore(o,r.nextSibling):(e=e.nodeType===9?e.head:e,e.insertBefore(o,e.firstChild)),t.state.loading|=Wn}}function Mz(e,t,n,a,o,r,c,d){for(this.tag=1,this.containerInfo=e,this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=Wi,this.callbackNode=this.next=this.pendingContext=this.context=this.cancelPendingCommit=null,this.callbackPriority=0,this.expirationTimes=hr(-1),this.entangledLanes=this.shellSuspendCounter=this.errorRecoveryDisabledLanes=this.expiredLanes=this.warmLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=hr(0),this.hiddenUpdates=hr(null),this.identifierPrefix=a,this.onUncaughtError=o,this.onCaughtError=r,this.onRecoverableError=c,this.pooledCache=null,this.pooledCacheLanes=0,this.formState=d,this.incompleteTransitions=new Map,this.passiveEffectDuration=this.effectDuration=-0,this.memoizedUpdaters=new Set,e=this.pendingUpdatersLaneMap=[],t=0;31>t;t++)e.push(new Set);this._debugRootType=n?"hydrateRoot()":"createRoot()"}function sS(e,t,n,a,o,r,c,d,v,g,R,j){return e=new Mz(e,t,n,c,d,v,g,j),t=ZD,r===!0&&(t|=Bt|fa),ca&&(t|=Mt),r=O(3,null,null,t),e.current=r,r.stateNode=e,t=jh(),zi(t),e.pooledCache=t,zi(t),r.memoizedState={element:a,isDehydrated:n,cache:t},Hh(r),e}function uS(e){return e?(e=Yo,e):Yo}function om(e,t,n,a,o,r){if(Ht&&typeof Ht.onScheduleFiberRoot=="function")try{Ht.onScheduleFiberRoot(kr,a,n)}catch(c){za||(za=!0,console.error("React instrumentation encountered an error: %s",c))}X!==null&&typeof X.markRenderScheduled=="function"&&X.markRenderScheduled(t),o=uS(o),a.context===null?a.context=o:a.pendingContext=o,Ca&&Tn!==null&&!B1&&(B1=!0,console.error(`Render methods should be a pure function of props and state; triggering nested component updates from render is not allowed. If necessary, trigger nested updates in componentDidUpdate.

Check the render method of %s.`,te(Tn)||"Unknown")),a=Co(t),a.payload={element:n},r=r===void 0?null:r,r!==null&&(typeof r!="function"&&console.error("Expected the last optional `callback` argument to be a function. Instead received: %s.",r),a.callback=r),n=Mo(e,a,t),n!==null&&(it(n,e,t),as(n,e,t))}function cS(e,t){if(e=e.memoizedState,e!==null&&e.dehydrated!==null){var n=e.retryLane;e.retryLane=n!==0&&n<t?n:t}}function im(e,t){cS(e,t),(e=e.alternate)&&cS(e,t)}function fS(e){if(e.tag===13){var t=Ft(e,67108864);t!==null&&it(t,e,67108864),im(e,67108864)}}function jz(){return Tn}function Uz(){for(var e=new Map,t=1,n=0;31>n;n++){var a=Ll(t);e.set(t,a),t*=2}return e}function kz(e,t,n,a){var o=M.T;M.T=null;var r=Me.p;try{Me.p=Pn,rm(e,t,n,a)}finally{Me.p=r,M.T=o}}function Nz(e,t,n,a){var o=M.T;M.T=null;var r=Me.p;try{Me.p=Da,rm(e,t,n,a)}finally{Me.p=r,M.T=o}}function rm(e,t,n,a){if(Wf){var o=lm(a);if(o===null)Xp(e,t,a,Ff,n),hS(e,a);else if(Hz(o,e,t,n,a))a.stopPropagation();else if(hS(e,a),t&4&&-1<AC.indexOf(e)){for(;o!==null;){var r=la(o);if(r!==null)switch(r.tag){case 3:if(r=r.stateNode,r.current.memoizedState.isDehydrated){var c=ft(r.pendingLanes);if(c!==0){var d=r;for(d.pendingLanes|=2,d.entangledLanes|=2;c;){var v=1<<31-$t(c);d.entanglements[1]|=v,c&=~v}wa(r),(je&(Xt|ma))===On&&(Nf=Ra()+y1,ys(0))}}break;case 13:d=Ft(r,2),d!==null&&it(d,r,2),xr(),im(r,2)}if(r=lm(a),r===null&&Xp(e,t,a,Ff,n),r===o)break;o=r}o!==null&&a.stopPropagation()}else Xp(e,t,a,null,n)}}function lm(e){return e=gh(e),sm(e)}function sm(e){if(Ff=null,e=ra(e),e!==null){var t=ie(e);if(t===null)e=null;else{var n=t.tag;if(n===13){if(e=Qe(t),e!==null)return e;e=null}else if(n===3){if(t.stateNode.current.memoizedState.isDehydrated)return t.tag===3?t.stateNode.containerInfo:null;e=null}else t!==e&&(e=null)}}return Ff=e,null}function dS(e){switch(e){case"beforetoggle":case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"toggle":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return Pn;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return Da;case"message":switch(Qz()){case bm:return Pn;case _m:return Da;case Ur:case Zz:return to;case Sm:return df;default:return to}default:return to}}function hS(e,t){switch(e){case"focusin":case"focusout":ti=null;break;case"dragenter":case"dragleave":ni=null;break;case"mouseover":case"mouseout":ai=null;break;case"pointerover":case"pointerout":cu.delete(t.pointerId);break;case"gotpointercapture":case"lostpointercapture":fu.delete(t.pointerId)}}function Ts(e,t,n,a,o,r){return e===null||e.nativeEvent!==r?(e={blockedOn:t,domEventName:n,eventSystemFlags:a,nativeEvent:r,targetContainers:[o]},t!==null&&(t=la(t),t!==null&&fS(t)),e):(e.eventSystemFlags|=a,t=e.targetContainers,o!==null&&t.indexOf(o)===-1&&t.push(o),e)}function Hz(e,t,n,a,o){switch(t){case"focusin":return ti=Ts(ti,e,t,n,a,o),!0;case"dragenter":return ni=Ts(ni,e,t,n,a,o),!0;case"mouseover":return ai=Ts(ai,e,t,n,a,o),!0;case"pointerover":var r=o.pointerId;return cu.set(r,Ts(cu.get(r)||null,e,t,n,a,o)),!0;case"gotpointercapture":return r=o.pointerId,fu.set(r,Ts(fu.get(r)||null,e,t,n,a,o)),!0}return!1}function pS(e){var t=ra(e.target);if(t!==null){var n=ie(t);if(n!==null){if(t=n.tag,t===13){if(t=Qe(n),t!==null){e.blockedOn=t,mr(e.priority,function(){if(n.tag===13){var a=_n(n);a=Vl(a);var o=Ft(n,a);o!==null&&it(o,n,a),im(n,a)}});return}}else if(t===3&&n.stateNode.current.memoizedState.isDehydrated){e.blockedOn=n.tag===3?n.stateNode.containerInfo:null;return}}}e.blockedOn=null}function af(e){if(e.blockedOn!==null)return!1;for(var t=e.targetContainers;0<t.length;){var n=lm(e.nativeEvent);if(n===null){n=e.nativeEvent;var a=new n.constructor(n.type,n),o=a;Rs!==null&&console.error("Expected currently replaying event to be null. This error is likely caused by a bug in React. Please file an issue."),Rs=o,n.target.dispatchEvent(a),Rs===null&&console.error("Expected currently replaying event to not be null. This error is likely caused by a bug in React. Please file an issue."),Rs=null}else return t=la(n),t!==null&&fS(t),e.blockedOn=n,!1;t.shift()}return!0}function mS(e,t,n){af(e)&&n.delete(t)}function Lz(){Cv=!1,ti!==null&&af(ti)&&(ti=null),ni!==null&&af(ni)&&(ni=null),ai!==null&&af(ai)&&(ai=null),cu.forEach(mS),fu.forEach(mS)}function of(e,t){e.blockedOn===t&&(e.blockedOn=null,Cv||(Cv=!0,dt.unstable_scheduleCallback(dt.unstable_NormalPriority,Lz)))}function vS(e){ed!==e&&(ed=e,dt.unstable_scheduleCallback(dt.unstable_NormalPriority,function(){ed===e&&(ed=null);for(var t=0;t<e.length;t+=3){var n=e[t],a=e[t+1],o=e[t+2];if(typeof a!="function"){if(sm(a||n)===null)continue;break}var r=la(n);r!==null&&(e.splice(t,3),t-=3,n={pending:!0,data:o,method:n.method,action:a},Object.freeze(n),sp(r,n,a,o))}}))}function Os(e){function t(v){return of(v,e)}ti!==null&&of(ti,e),ni!==null&&of(ni,e),ai!==null&&of(ai,e),cu.forEach(t),fu.forEach(t);for(var n=0;n<oi.length;n++){var a=oi[n];a.blockedOn===e&&(a.blockedOn=null)}for(;0<oi.length&&(n=oi[0],n.blockedOn===null);)pS(n),n.blockedOn===null&&oi.shift();if(n=(e.ownerDocument||e).$$reactFormReplay,n!=null)for(a=0;a<n.length;a+=3){var o=n[a],r=n[a+1],c=o[nn]||null;if(typeof r=="function")c||vS(n);else if(c){var d=null;if(r&&r.hasAttribute("formAction")){if(o=r,c=r[nn]||null)d=c.formAction;else if(sm(o)!==null)continue}else d=c.action;typeof d=="function"?n[a+1]=d:(n.splice(a,3),a-=3),vS(n)}}}function um(e){this._internalRoot=e}function rf(e){this._internalRoot=e}function yS(e){e[qo]&&(e._reactRootContainer?console.error("You are calling ReactDOMClient.createRoot() on a container that was previously passed to ReactDOM.render(). This is not supported."):console.error("You are calling ReactDOMClient.createRoot() on a container that has already been passed to createRoot() before. Instead, call root.render() on the existing root instead if you want to update it."))}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var dt=eO(),cm=bl(),Bz=Vv(),be=Object.assign,Vz=Symbol.for("react.element"),Bo=Symbol.for("react.transitional.element"),Mr=Symbol.for("react.portal"),jr=Symbol.for("react.fragment"),lf=Symbol.for("react.strict_mode"),fm=Symbol.for("react.profiler"),$z=Symbol.for("react.provider"),dm=Symbol.for("react.consumer"),Aa=Symbol.for("react.context"),Es=Symbol.for("react.forward_ref"),hm=Symbol.for("react.suspense"),pm=Symbol.for("react.suspense_list"),sf=Symbol.for("react.memo"),Sn=Symbol.for("react.lazy"),mm=Symbol.for("react.activity"),Pz=Symbol.for("react.memo_cache_sentinel"),gS=Symbol.iterator,qz=Symbol.for("react.client.reference"),Ct=Array.isArray,M=cm.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,Me=Bz.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,Gz=Object.freeze({pending:!1,data:null,method:null,action:null}),vm=[],ym=[],Fa=-1,Vo=at(null),ws=at(null),$o=at(null),uf=at(null),eo=Object.prototype.hasOwnProperty,gm=dt.unstable_scheduleCallback,Yz=dt.unstable_cancelCallback,Xz=dt.unstable_shouldYield,Iz=dt.unstable_requestPaint,Ra=dt.unstable_now,Qz=dt.unstable_getCurrentPriorityLevel,bm=dt.unstable_ImmediatePriority,_m=dt.unstable_UserBlockingPriority,Ur=dt.unstable_NormalPriority,Zz=dt.unstable_LowPriority,Sm=dt.unstable_IdlePriority,Kz=dt.log,Jz=dt.unstable_setDisableYieldValue,kr=null,Ht=null,X=null,za=!1,ca=typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u",$t=Math.clz32?Math.clz32:ac,Wz=Math.log,Fz=Math.LN2,cf=256,ff=4194304,Pn=2,Da=8,to=32,df=268435456,Po=Math.random().toString(36).slice(2),Lt="__reactFiber$"+Po,nn="__reactProps$"+Po,qo="__reactContainer$"+Po,Tm="__reactEvents$"+Po,eD="__reactListeners$"+Po,tD="__reactHandles$"+Po,bS="__reactResources$"+Po,xs="__reactMarker$"+Po,_S=new Set,ki={},Om={},nD={button:!0,checkbox:!0,image:!0,hidden:!0,radio:!0,reset:!0,submit:!0},aD=RegExp("^[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),SS={},TS={},As=0,OS,ES,wS,xS,AS,RS,zS;qa.__reactDisabledLog=!0;var Em,DS,wm=!1,xm=new(typeof WeakMap=="function"?WeakMap:Map),Tn=null,Ca=!1,oD=/[\n"\\]/g,CS=!1,MS=!1,jS=!1,US=!1,kS=!1,NS=!1,HS=["value","defaultValue"],LS=!1,BS=/["'&<>\n\t]|^\s|\s$/,iD="address applet area article aside base basefont bgsound blockquote body br button caption center col colgroup dd details dir div dl dt embed fieldset figcaption figure footer form frame frameset h1 h2 h3 h4 h5 h6 head header hgroup hr html iframe img input isindex li link listing main marquee menu menuitem meta nav noembed noframes noscript object ol p param plaintext pre script section select source style summary table tbody td template textarea tfoot th thead title tr track ul wbr xmp".split(" "),VS="applet caption html table td th marquee object template foreignObject desc title".split(" "),rD=VS.concat(["button"]),lD="dd dt li option optgroup p rp rt".split(" "),$S={current:null,formTag:null,aTagInScope:null,buttonTagInScope:null,nobrTagInScope:null,pTagInButtonScope:null,listItemTagAutoclosing:null,dlItemTagAutoclosing:null,containerTagInScope:null,implicitRootScope:!1},hf={},Am={animation:"animationDelay animationDirection animationDuration animationFillMode animationIterationCount animationName animationPlayState animationTimingFunction".split(" "),background:"backgroundAttachment backgroundClip backgroundColor backgroundImage backgroundOrigin backgroundPositionX backgroundPositionY backgroundRepeat backgroundSize".split(" "),backgroundPosition:["backgroundPositionX","backgroundPositionY"],border:"borderBottomColor borderBottomStyle borderBottomWidth borderImageOutset borderImageRepeat borderImageSlice borderImageSource borderImageWidth borderLeftColor borderLeftStyle borderLeftWidth borderRightColor borderRightStyle borderRightWidth borderTopColor borderTopStyle borderTopWidth".split(" "),borderBlockEnd:["borderBlockEndColor","borderBlockEndStyle","borderBlockEndWidth"],borderBlockStart:["borderBlockStartColor","borderBlockStartStyle","borderBlockStartWidth"],borderBottom:["borderBottomColor","borderBottomStyle","borderBottomWidth"],borderColor:["borderBottomColor","borderLeftColor","borderRightColor","borderTopColor"],borderImage:["borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth"],borderInlineEnd:["borderInlineEndColor","borderInlineEndStyle","borderInlineEndWidth"],borderInlineStart:["borderInlineStartColor","borderInlineStartStyle","borderInlineStartWidth"],borderLeft:["borderLeftColor","borderLeftStyle","borderLeftWidth"],borderRadius:["borderBottomLeftRadius","borderBottomRightRadius","borderTopLeftRadius","borderTopRightRadius"],borderRight:["borderRightColor","borderRightStyle","borderRightWidth"],borderStyle:["borderBottomStyle","borderLeftStyle","borderRightStyle","borderTopStyle"],borderTop:["borderTopColor","borderTopStyle","borderTopWidth"],borderWidth:["borderBottomWidth","borderLeftWidth","borderRightWidth","borderTopWidth"],columnRule:["columnRuleColor","columnRuleStyle","columnRuleWidth"],columns:["columnCount","columnWidth"],flex:["flexBasis","flexGrow","flexShrink"],flexFlow:["flexDirection","flexWrap"],font:"fontFamily fontFeatureSettings fontKerning fontLanguageOverride fontSize fontSizeAdjust fontStretch fontStyle fontVariant fontVariantAlternates fontVariantCaps fontVariantEastAsian fontVariantLigatures fontVariantNumeric fontVariantPosition fontWeight lineHeight".split(" "),fontVariant:"fontVariantAlternates fontVariantCaps fontVariantEastAsian fontVariantLigatures fontVariantNumeric fontVariantPosition".split(" "),gap:["columnGap","rowGap"],grid:"gridAutoColumns gridAutoFlow gridAutoRows gridTemplateAreas gridTemplateColumns gridTemplateRows".split(" "),gridArea:["gridColumnEnd","gridColumnStart","gridRowEnd","gridRowStart"],gridColumn:["gridColumnEnd","gridColumnStart"],gridColumnGap:["columnGap"],gridGap:["columnGap","rowGap"],gridRow:["gridRowEnd","gridRowStart"],gridRowGap:["rowGap"],gridTemplate:["gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],listStyle:["listStyleImage","listStylePosition","listStyleType"],margin:["marginBottom","marginLeft","marginRight","marginTop"],marker:["markerEnd","markerMid","markerStart"],mask:"maskClip maskComposite maskImage maskMode maskOrigin maskPositionX maskPositionY maskRepeat maskSize".split(" "),maskPosition:["maskPositionX","maskPositionY"],outline:["outlineColor","outlineStyle","outlineWidth"],overflow:["overflowX","overflowY"],padding:["paddingBottom","paddingLeft","paddingRight","paddingTop"],placeContent:["alignContent","justifyContent"],placeItems:["alignItems","justifyItems"],placeSelf:["alignSelf","justifySelf"],textDecoration:["textDecorationColor","textDecorationLine","textDecorationStyle"],textEmphasis:["textEmphasisColor","textEmphasisStyle"],transition:["transitionDelay","transitionDuration","transitionProperty","transitionTimingFunction"],wordWrap:["overflowWrap"]},PS=/([A-Z])/g,qS=/^ms-/,sD=/^(?:webkit|moz|o)[A-Z]/,uD=/^-ms-/,cD=/-(.)/g,GS=/;\s*$/,Nr={},Rm={},YS=!1,XS=!1,IS=new Set("animationIterationCount aspectRatio borderImageOutset borderImageSlice borderImageWidth boxFlex boxFlexGroup boxOrdinalGroup columnCount columns flex flexGrow flexPositive flexShrink flexNegative flexOrder gridArea gridRow gridRowEnd gridRowSpan gridRowStart gridColumn gridColumnEnd gridColumnSpan gridColumnStart fontWeight lineClamp lineHeight opacity order orphans scale tabSize widows zIndex zoom fillOpacity floodOpacity stopOpacity strokeDasharray strokeDashoffset strokeMiterlimit strokeOpacity strokeWidth MozAnimationIterationCount MozBoxFlex MozBoxFlexGroup MozLineClamp msAnimationIterationCount msFlex msZoom msFlexGrow msFlexNegative msFlexOrder msFlexPositive msFlexShrink msGridColumn msGridColumnSpan msGridRow msGridRowSpan WebkitAnimationIterationCount WebkitBoxFlex WebKitBoxFlexGroup WebkitBoxOrdinalGroup WebkitColumnCount WebkitColumns WebkitFlex WebkitFlexGrow WebkitFlexPositive WebkitFlexShrink WebkitLineClamp".split(" ")),pf="http://www.w3.org/1998/Math/MathML",Hr="http://www.w3.org/2000/svg",fD=new Map([["acceptCharset","accept-charset"],["htmlFor","for"],["httpEquiv","http-equiv"],["crossOrigin","crossorigin"],["accentHeight","accent-height"],["alignmentBaseline","alignment-baseline"],["arabicForm","arabic-form"],["baselineShift","baseline-shift"],["capHeight","cap-height"],["clipPath","clip-path"],["clipRule","clip-rule"],["colorInterpolation","color-interpolation"],["colorInterpolationFilters","color-interpolation-filters"],["colorProfile","color-profile"],["colorRendering","color-rendering"],["dominantBaseline","dominant-baseline"],["enableBackground","enable-background"],["fillOpacity","fill-opacity"],["fillRule","fill-rule"],["floodColor","flood-color"],["floodOpacity","flood-opacity"],["fontFamily","font-family"],["fontSize","font-size"],["fontSizeAdjust","font-size-adjust"],["fontStretch","font-stretch"],["fontStyle","font-style"],["fontVariant","font-variant"],["fontWeight","font-weight"],["glyphName","glyph-name"],["glyphOrientationHorizontal","glyph-orientation-horizontal"],["glyphOrientationVertical","glyph-orientation-vertical"],["horizAdvX","horiz-adv-x"],["horizOriginX","horiz-origin-x"],["imageRendering","image-rendering"],["letterSpacing","letter-spacing"],["lightingColor","lighting-color"],["markerEnd","marker-end"],["markerMid","marker-mid"],["markerStart","marker-start"],["overlinePosition","overline-position"],["overlineThickness","overline-thickness"],["paintOrder","paint-order"],["panose-1","panose-1"],["pointerEvents","pointer-events"],["renderingIntent","rendering-intent"],["shapeRendering","shape-rendering"],["stopColor","stop-color"],["stopOpacity","stop-opacity"],["strikethroughPosition","strikethrough-position"],["strikethroughThickness","strikethrough-thickness"],["strokeDasharray","stroke-dasharray"],["strokeDashoffset","stroke-dashoffset"],["strokeLinecap","stroke-linecap"],["strokeLinejoin","stroke-linejoin"],["strokeMiterlimit","stroke-miterlimit"],["strokeOpacity","stroke-opacity"],["strokeWidth","stroke-width"],["textAnchor","text-anchor"],["textDecoration","text-decoration"],["textRendering","text-rendering"],["transformOrigin","transform-origin"],["underlinePosition","underline-position"],["underlineThickness","underline-thickness"],["unicodeBidi","unicode-bidi"],["unicodeRange","unicode-range"],["unitsPerEm","units-per-em"],["vAlphabetic","v-alphabetic"],["vHanging","v-hanging"],["vIdeographic","v-ideographic"],["vMathematical","v-mathematical"],["vectorEffect","vector-effect"],["vertAdvY","vert-adv-y"],["vertOriginX","vert-origin-x"],["vertOriginY","vert-origin-y"],["wordSpacing","word-spacing"],["writingMode","writing-mode"],["xmlnsXlink","xmlns:xlink"],["xHeight","x-height"]]),mf={accept:"accept",acceptcharset:"acceptCharset","accept-charset":"acceptCharset",accesskey:"accessKey",action:"action",allowfullscreen:"allowFullScreen",alt:"alt",as:"as",async:"async",autocapitalize:"autoCapitalize",autocomplete:"autoComplete",autocorrect:"autoCorrect",autofocus:"autoFocus",autoplay:"autoPlay",autosave:"autoSave",capture:"capture",cellpadding:"cellPadding",cellspacing:"cellSpacing",challenge:"challenge",charset:"charSet",checked:"checked",children:"children",cite:"cite",class:"className",classid:"classID",classname:"className",cols:"cols",colspan:"colSpan",content:"content",contenteditable:"contentEditable",contextmenu:"contextMenu",controls:"controls",controlslist:"controlsList",coords:"coords",crossorigin:"crossOrigin",dangerouslysetinnerhtml:"dangerouslySetInnerHTML",data:"data",datetime:"dateTime",default:"default",defaultchecked:"defaultChecked",defaultvalue:"defaultValue",defer:"defer",dir:"dir",disabled:"disabled",disablepictureinpicture:"disablePictureInPicture",disableremoteplayback:"disableRemotePlayback",download:"download",draggable:"draggable",enctype:"encType",enterkeyhint:"enterKeyHint",fetchpriority:"fetchPriority",for:"htmlFor",form:"form",formmethod:"formMethod",formaction:"formAction",formenctype:"formEncType",formnovalidate:"formNoValidate",formtarget:"formTarget",frameborder:"frameBorder",headers:"headers",height:"height",hidden:"hidden",high:"high",href:"href",hreflang:"hrefLang",htmlfor:"htmlFor",httpequiv:"httpEquiv","http-equiv":"httpEquiv",icon:"icon",id:"id",imagesizes:"imageSizes",imagesrcset:"imageSrcSet",inert:"inert",innerhtml:"innerHTML",inputmode:"inputMode",integrity:"integrity",is:"is",itemid:"itemID",itemprop:"itemProp",itemref:"itemRef",itemscope:"itemScope",itemtype:"itemType",keyparams:"keyParams",keytype:"keyType",kind:"kind",label:"label",lang:"lang",list:"list",loop:"loop",low:"low",manifest:"manifest",marginwidth:"marginWidth",marginheight:"marginHeight",max:"max",maxlength:"maxLength",media:"media",mediagroup:"mediaGroup",method:"method",min:"min",minlength:"minLength",multiple:"multiple",muted:"muted",name:"name",nomodule:"noModule",nonce:"nonce",novalidate:"noValidate",open:"open",optimum:"optimum",pattern:"pattern",placeholder:"placeholder",playsinline:"playsInline",poster:"poster",preload:"preload",profile:"profile",radiogroup:"radioGroup",readonly:"readOnly",referrerpolicy:"referrerPolicy",rel:"rel",required:"required",reversed:"reversed",role:"role",rows:"rows",rowspan:"rowSpan",sandbox:"sandbox",scope:"scope",scoped:"scoped",scrolling:"scrolling",seamless:"seamless",selected:"selected",shape:"shape",size:"size",sizes:"sizes",span:"span",spellcheck:"spellCheck",src:"src",srcdoc:"srcDoc",srclang:"srcLang",srcset:"srcSet",start:"start",step:"step",style:"style",summary:"summary",tabindex:"tabIndex",target:"target",title:"title",type:"type",usemap:"useMap",value:"value",width:"width",wmode:"wmode",wrap:"wrap",about:"about",accentheight:"accentHeight","accent-height":"accentHeight",accumulate:"accumulate",additive:"additive",alignmentbaseline:"alignmentBaseline","alignment-baseline":"alignmentBaseline",allowreorder:"allowReorder",alphabetic:"alphabetic",amplitude:"amplitude",arabicform:"arabicForm","arabic-form":"arabicForm",ascent:"ascent",attributename:"attributeName",attributetype:"attributeType",autoreverse:"autoReverse",azimuth:"azimuth",basefrequency:"baseFrequency",baselineshift:"baselineShift","baseline-shift":"baselineShift",baseprofile:"baseProfile",bbox:"bbox",begin:"begin",bias:"bias",by:"by",calcmode:"calcMode",capheight:"capHeight","cap-height":"capHeight",clip:"clip",clippath:"clipPath","clip-path":"clipPath",clippathunits:"clipPathUnits",cliprule:"clipRule","clip-rule":"clipRule",color:"color",colorinterpolation:"colorInterpolation","color-interpolation":"colorInterpolation",colorinterpolationfilters:"colorInterpolationFilters","color-interpolation-filters":"colorInterpolationFilters",colorprofile:"colorProfile","color-profile":"colorProfile",colorrendering:"colorRendering","color-rendering":"colorRendering",contentscripttype:"contentScriptType",contentstyletype:"contentStyleType",cursor:"cursor",cx:"cx",cy:"cy",d:"d",datatype:"datatype",decelerate:"decelerate",descent:"descent",diffuseconstant:"diffuseConstant",direction:"direction",display:"display",divisor:"divisor",dominantbaseline:"dominantBaseline","dominant-baseline":"dominantBaseline",dur:"dur",dx:"dx",dy:"dy",edgemode:"edgeMode",elevation:"elevation",enablebackground:"enableBackground","enable-background":"enableBackground",end:"end",exponent:"exponent",externalresourcesrequired:"externalResourcesRequired",fill:"fill",fillopacity:"fillOpacity","fill-opacity":"fillOpacity",fillrule:"fillRule","fill-rule":"fillRule",filter:"filter",filterres:"filterRes",filterunits:"filterUnits",floodopacity:"floodOpacity","flood-opacity":"floodOpacity",floodcolor:"floodColor","flood-color":"floodColor",focusable:"focusable",fontfamily:"fontFamily","font-family":"fontFamily",fontsize:"fontSize","font-size":"fontSize",fontsizeadjust:"fontSizeAdjust","font-size-adjust":"fontSizeAdjust",fontstretch:"fontStretch","font-stretch":"fontStretch",fontstyle:"fontStyle","font-style":"fontStyle",fontvariant:"fontVariant","font-variant":"fontVariant",fontweight:"fontWeight","font-weight":"fontWeight",format:"format",from:"from",fx:"fx",fy:"fy",g1:"g1",g2:"g2",glyphname:"glyphName","glyph-name":"glyphName",glyphorientationhorizontal:"glyphOrientationHorizontal","glyph-orientation-horizontal":"glyphOrientationHorizontal",glyphorientationvertical:"glyphOrientationVertical","glyph-orientation-vertical":"glyphOrientationVertical",glyphref:"glyphRef",gradienttransform:"gradientTransform",gradientunits:"gradientUnits",hanging:"hanging",horizadvx:"horizAdvX","horiz-adv-x":"horizAdvX",horizoriginx:"horizOriginX","horiz-origin-x":"horizOriginX",ideographic:"ideographic",imagerendering:"imageRendering","image-rendering":"imageRendering",in2:"in2",in:"in",inlist:"inlist",intercept:"intercept",k1:"k1",k2:"k2",k3:"k3",k4:"k4",k:"k",kernelmatrix:"kernelMatrix",kernelunitlength:"kernelUnitLength",kerning:"kerning",keypoints:"keyPoints",keysplines:"keySplines",keytimes:"keyTimes",lengthadjust:"lengthAdjust",letterspacing:"letterSpacing","letter-spacing":"letterSpacing",lightingcolor:"lightingColor","lighting-color":"lightingColor",limitingconeangle:"limitingConeAngle",local:"local",markerend:"markerEnd","marker-end":"markerEnd",markerheight:"markerHeight",markermid:"markerMid","marker-mid":"markerMid",markerstart:"markerStart","marker-start":"markerStart",markerunits:"markerUnits",markerwidth:"markerWidth",mask:"mask",maskcontentunits:"maskContentUnits",maskunits:"maskUnits",mathematical:"mathematical",mode:"mode",numoctaves:"numOctaves",offset:"offset",opacity:"opacity",operator:"operator",order:"order",orient:"orient",orientation:"orientation",origin:"origin",overflow:"overflow",overlineposition:"overlinePosition","overline-position":"overlinePosition",overlinethickness:"overlineThickness","overline-thickness":"overlineThickness",paintorder:"paintOrder","paint-order":"paintOrder",panose1:"panose1","panose-1":"panose1",pathlength:"pathLength",patterncontentunits:"patternContentUnits",patterntransform:"patternTransform",patternunits:"patternUnits",pointerevents:"pointerEvents","pointer-events":"pointerEvents",points:"points",pointsatx:"pointsAtX",pointsaty:"pointsAtY",pointsatz:"pointsAtZ",popover:"popover",popovertarget:"popoverTarget",popovertargetaction:"popoverTargetAction",prefix:"prefix",preservealpha:"preserveAlpha",preserveaspectratio:"preserveAspectRatio",primitiveunits:"primitiveUnits",property:"property",r:"r",radius:"radius",refx:"refX",refy:"refY",renderingintent:"renderingIntent","rendering-intent":"renderingIntent",repeatcount:"repeatCount",repeatdur:"repeatDur",requiredextensions:"requiredExtensions",requiredfeatures:"requiredFeatures",resource:"resource",restart:"restart",result:"result",results:"results",rotate:"rotate",rx:"rx",ry:"ry",scale:"scale",security:"security",seed:"seed",shaperendering:"shapeRendering","shape-rendering":"shapeRendering",slope:"slope",spacing:"spacing",specularconstant:"specularConstant",specularexponent:"specularExponent",speed:"speed",spreadmethod:"spreadMethod",startoffset:"startOffset",stddeviation:"stdDeviation",stemh:"stemh",stemv:"stemv",stitchtiles:"stitchTiles",stopcolor:"stopColor","stop-color":"stopColor",stopopacity:"stopOpacity","stop-opacity":"stopOpacity",strikethroughposition:"strikethroughPosition","strikethrough-position":"strikethroughPosition",strikethroughthickness:"strikethroughThickness","strikethrough-thickness":"strikethroughThickness",string:"string",stroke:"stroke",strokedasharray:"strokeDasharray","stroke-dasharray":"strokeDasharray",strokedashoffset:"strokeDashoffset","stroke-dashoffset":"strokeDashoffset",strokelinecap:"strokeLinecap","stroke-linecap":"strokeLinecap",strokelinejoin:"strokeLinejoin","stroke-linejoin":"strokeLinejoin",strokemiterlimit:"strokeMiterlimit","stroke-miterlimit":"strokeMiterlimit",strokewidth:"strokeWidth","stroke-width":"strokeWidth",strokeopacity:"strokeOpacity","stroke-opacity":"strokeOpacity",suppresscontenteditablewarning:"suppressContentEditableWarning",suppresshydrationwarning:"suppressHydrationWarning",surfacescale:"surfaceScale",systemlanguage:"systemLanguage",tablevalues:"tableValues",targetx:"targetX",targety:"targetY",textanchor:"textAnchor","text-anchor":"textAnchor",textdecoration:"textDecoration","text-decoration":"textDecoration",textlength:"textLength",textrendering:"textRendering","text-rendering":"textRendering",to:"to",transform:"transform",transformorigin:"transformOrigin","transform-origin":"transformOrigin",typeof:"typeof",u1:"u1",u2:"u2",underlineposition:"underlinePosition","underline-position":"underlinePosition",underlinethickness:"underlineThickness","underline-thickness":"underlineThickness",unicode:"unicode",unicodebidi:"unicodeBidi","unicode-bidi":"unicodeBidi",unicoderange:"unicodeRange","unicode-range":"unicodeRange",unitsperem:"unitsPerEm","units-per-em":"unitsPerEm",unselectable:"unselectable",valphabetic:"vAlphabetic","v-alphabetic":"vAlphabetic",values:"values",vectoreffect:"vectorEffect","vector-effect":"vectorEffect",version:"version",vertadvy:"vertAdvY","vert-adv-y":"vertAdvY",vertoriginx:"vertOriginX","vert-origin-x":"vertOriginX",vertoriginy:"vertOriginY","vert-origin-y":"vertOriginY",vhanging:"vHanging","v-hanging":"vHanging",videographic:"vIdeographic","v-ideographic":"vIdeographic",viewbox:"viewBox",viewtarget:"viewTarget",visibility:"visibility",vmathematical:"vMathematical","v-mathematical":"vMathematical",vocab:"vocab",widths:"widths",wordspacing:"wordSpacing","word-spacing":"wordSpacing",writingmode:"writingMode","writing-mode":"writingMode",x1:"x1",x2:"x2",x:"x",xchannelselector:"xChannelSelector",xheight:"xHeight","x-height":"xHeight",xlinkactuate:"xlinkActuate","xlink:actuate":"xlinkActuate",xlinkarcrole:"xlinkArcrole","xlink:arcrole":"xlinkArcrole",xlinkhref:"xlinkHref","xlink:href":"xlinkHref",xlinkrole:"xlinkRole","xlink:role":"xlinkRole",xlinkshow:"xlinkShow","xlink:show":"xlinkShow",xlinktitle:"xlinkTitle","xlink:title":"xlinkTitle",xlinktype:"xlinkType","xlink:type":"xlinkType",xmlbase:"xmlBase","xml:base":"xmlBase",xmllang:"xmlLang","xml:lang":"xmlLang",xmlns:"xmlns","xml:space":"xmlSpace",xmlnsxlink:"xmlnsXlink","xmlns:xlink":"xmlnsXlink",xmlspace:"xmlSpace",y1:"y1",y2:"y2",y:"y",ychannelselector:"yChannelSelector",z:"z",zoomandpan:"zoomAndPan"},QS={"aria-current":0,"aria-description":0,"aria-details":0,"aria-disabled":0,"aria-hidden":0,"aria-invalid":0,"aria-keyshortcuts":0,"aria-label":0,"aria-roledescription":0,"aria-autocomplete":0,"aria-checked":0,"aria-expanded":0,"aria-haspopup":0,"aria-level":0,"aria-modal":0,"aria-multiline":0,"aria-multiselectable":0,"aria-orientation":0,"aria-placeholder":0,"aria-pressed":0,"aria-readonly":0,"aria-required":0,"aria-selected":0,"aria-sort":0,"aria-valuemax":0,"aria-valuemin":0,"aria-valuenow":0,"aria-valuetext":0,"aria-atomic":0,"aria-busy":0,"aria-live":0,"aria-relevant":0,"aria-dropeffect":0,"aria-grabbed":0,"aria-activedescendant":0,"aria-colcount":0,"aria-colindex":0,"aria-colspan":0,"aria-controls":0,"aria-describedby":0,"aria-errormessage":0,"aria-flowto":0,"aria-labelledby":0,"aria-owns":0,"aria-posinset":0,"aria-rowcount":0,"aria-rowindex":0,"aria-rowspan":0,"aria-setsize":0},Lr={},dD=RegExp("^(aria)-[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),hD=RegExp("^(aria)[A-Z][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),ZS=!1,Pt={},KS=/^on./,pD=/^on[^A-Z]/,mD=RegExp("^(aria)-[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),vD=RegExp("^(aria)[A-Z][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$"),yD=/^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*:/i,Rs=null,Br=null,Vr=null,zm=!1,Ma=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),Dm=!1;if(Ma)try{var zs={};Object.defineProperty(zs,"passive",{get:function(){Dm=!0}}),window.addEventListener("test",zs,zs),window.removeEventListener("test",zs,zs)}catch{Dm=!1}var Go=null,Cm=null,vf=null,Ni={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(e){return e.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},yf=Wt(Ni),Ds=be({},Ni,{view:0,detail:0}),gD=Wt(Ds),Mm,jm,Cs,gf=be({},Ds,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:bh,button:0,buttons:0,relatedTarget:function(e){return e.relatedTarget===void 0?e.fromElement===e.srcElement?e.toElement:e.fromElement:e.relatedTarget},movementX:function(e){return"movementX"in e?e.movementX:(e!==Cs&&(Cs&&e.type==="mousemove"?(Mm=e.screenX-Cs.screenX,jm=e.screenY-Cs.screenY):jm=Mm=0,Cs=e),Mm)},movementY:function(e){return"movementY"in e?e.movementY:jm}}),JS=Wt(gf),bD=be({},gf,{dataTransfer:0}),_D=Wt(bD),SD=be({},Ds,{relatedTarget:0}),Um=Wt(SD),TD=be({},Ni,{animationName:0,elapsedTime:0,pseudoElement:0}),OD=Wt(TD),ED=be({},Ni,{clipboardData:function(e){return"clipboardData"in e?e.clipboardData:window.clipboardData}}),wD=Wt(ED),xD=be({},Ni,{data:0}),WS=Wt(xD),AD=WS,RD={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},zD={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},DD={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"},CD=be({},Ds,{key:function(e){if(e.key){var t=RD[e.key]||e.key;if(t!=="Unidentified")return t}return e.type==="keypress"?(e=fc(e),e===13?"Enter":String.fromCharCode(e)):e.type==="keydown"||e.type==="keyup"?zD[e.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:bh,charCode:function(e){return e.type==="keypress"?fc(e):0},keyCode:function(e){return e.type==="keydown"||e.type==="keyup"?e.keyCode:0},which:function(e){return e.type==="keypress"?fc(e):e.type==="keydown"||e.type==="keyup"?e.keyCode:0}}),MD=Wt(CD),jD=be({},gf,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),FS=Wt(jD),UD=be({},Ds,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:bh}),kD=Wt(UD),ND=be({},Ni,{propertyName:0,elapsedTime:0,pseudoElement:0}),HD=Wt(ND),LD=be({},gf,{deltaX:function(e){return"deltaX"in e?e.deltaX:"wheelDeltaX"in e?-e.wheelDeltaX:0},deltaY:function(e){return"deltaY"in e?e.deltaY:"wheelDeltaY"in e?-e.wheelDeltaY:"wheelDelta"in e?-e.wheelDelta:0},deltaZ:0,deltaMode:0}),BD=Wt(LD),VD=be({},Ni,{newState:0,oldState:0}),$D=Wt(VD),PD=[9,13,27,32],eT=229,km=Ma&&"CompositionEvent"in window,Ms=null;Ma&&"documentMode"in document&&(Ms=document.documentMode);var qD=Ma&&"TextEvent"in window&&!Ms,tT=Ma&&(!km||Ms&&8<Ms&&11>=Ms),nT=32,aT=String.fromCharCode(nT),oT=!1,$r=!1,GD={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0},js=null,Us=null,iT=!1;Ma&&(iT=_R("input")&&(!document.documentMode||9<document.documentMode));var qt=typeof Object.is=="function"?Object.is:xR,YD=Ma&&"documentMode"in document&&11>=document.documentMode,Pr=null,Nm=null,ks=null,Hm=!1,qr={animationend:_i("Animation","AnimationEnd"),animationiteration:_i("Animation","AnimationIteration"),animationstart:_i("Animation","AnimationStart"),transitionrun:_i("Transition","TransitionRun"),transitionstart:_i("Transition","TransitionStart"),transitioncancel:_i("Transition","TransitionCancel"),transitionend:_i("Transition","TransitionEnd")},Lm={},rT={};Ma&&(rT=document.createElement("div").style,"AnimationEvent"in window||(delete qr.animationend.animation,delete qr.animationiteration.animation,delete qr.animationstart.animation),"TransitionEvent"in window||delete qr.transitionend.transition);var lT=Si("animationend"),sT=Si("animationiteration"),uT=Si("animationstart"),XD=Si("transitionrun"),ID=Si("transitionstart"),QD=Si("transitioncancel"),cT=Si("transitionend"),fT=new Map,Bm="abort auxClick beforeToggle cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");Bm.push("scrollEnd");var Vm=new WeakMap,bf=1,no=2,qn=[],Gr=0,$m=0,Yo={};Object.freeze(Yo);var Gn=null,Yr=null,Xe=0,ZD=1,Mt=2,Bt=8,fa=16,dT=64,hT=!1;try{var pT=Object.preventExtensions({})}catch{hT=!0}var Xr=[],Ir=0,_f=null,Sf=0,Yn=[],Xn=0,Hi=null,ao=1,oo="",Gt=null,tt=null,Ae=!1,io=!1,In=null,Li=null,ja=!1,Pm=Error("Hydration Mismatch Exception: This is not a real error, and should not leak into userspace. If you're seeing this, it's likely a bug in React."),mT=0;if(typeof performance=="object"&&typeof performance.now=="function")var KD=performance,vT=function(){return KD.now()};else{var JD=Date;vT=function(){return JD.now()}}var qm=at(null),Gm=at(null),yT={},Tf=null,Qr=null,Zr=!1,WD=typeof AbortController<"u"?AbortController:function(){var e=[],t=this.signal={aborted:!1,addEventListener:function(n,a){e.push(a)}};this.abort=function(){t.aborted=!0,e.forEach(function(n){return n()})}},FD=dt.unstable_scheduleCallback,eC=dt.unstable_NormalPriority,yt={$$typeof:Aa,Consumer:null,Provider:null,_currentValue:null,_currentValue2:null,_threadCount:0,_currentRenderer:null,_currentRenderer2:null},Kr=dt.unstable_now,gT=-0,Of=-0,an=-1.1,Bi=-0,Ef=!1,wf=!1,Ns=null,Ym=0,Vi=0,Jr=null,bT=M.S;M.S=function(e,t){typeof t=="object"&&t!==null&&typeof t.then=="function"&&RR(e,t),bT!==null&&bT(e,t)};var $i=at(null),da={recordUnsafeLifecycleWarnings:function(){},flushPendingUnsafeLifecycleWarnings:function(){},recordLegacyContextWarning:function(){},flushLegacyContextWarning:function(){},discardPendingWarnings:function(){}},Hs=[],Ls=[],Bs=[],Vs=[],$s=[],Ps=[],Pi=new Set;da.recordUnsafeLifecycleWarnings=function(e,t){Pi.has(e.type)||(typeof t.componentWillMount=="function"&&t.componentWillMount.__suppressDeprecationWarning!==!0&&Hs.push(e),e.mode&Bt&&typeof t.UNSAFE_componentWillMount=="function"&&Ls.push(e),typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps.__suppressDeprecationWarning!==!0&&Bs.push(e),e.mode&Bt&&typeof t.UNSAFE_componentWillReceiveProps=="function"&&Vs.push(e),typeof t.componentWillUpdate=="function"&&t.componentWillUpdate.__suppressDeprecationWarning!==!0&&$s.push(e),e.mode&Bt&&typeof t.UNSAFE_componentWillUpdate=="function"&&Ps.push(e))},da.flushPendingUnsafeLifecycleWarnings=function(){var e=new Set;0<Hs.length&&(Hs.forEach(function(d){e.add(te(d)||"Component"),Pi.add(d.type)}),Hs=[]);var t=new Set;0<Ls.length&&(Ls.forEach(function(d){t.add(te(d)||"Component"),Pi.add(d.type)}),Ls=[]);var n=new Set;0<Bs.length&&(Bs.forEach(function(d){n.add(te(d)||"Component"),Pi.add(d.type)}),Bs=[]);var a=new Set;0<Vs.length&&(Vs.forEach(function(d){a.add(te(d)||"Component"),Pi.add(d.type)}),Vs=[]);var o=new Set;0<$s.length&&($s.forEach(function(d){o.add(te(d)||"Component"),Pi.add(d.type)}),$s=[]);var r=new Set;if(0<Ps.length&&(Ps.forEach(function(d){r.add(te(d)||"Component"),Pi.add(d.type)}),Ps=[]),0<t.size){var c=x(t);console.error(`Using UNSAFE_componentWillMount in strict mode is not recommended and may indicate bugs in your code. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.

Please update the following components: %s`,c)}0<a.size&&(c=x(a),console.error(`Using UNSAFE_componentWillReceiveProps in strict mode is not recommended and may indicate bugs in your code. See https://react.dev/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://react.dev/link/derived-state

Please update the following components: %s`,c)),0<r.size&&(c=x(r),console.error(`Using UNSAFE_componentWillUpdate in strict mode is not recommended and may indicate bugs in your code. See https://react.dev/link/unsafe-component-lifecycles for details.

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

Please update the following components: %s`,c))};var xf=new Map,_T=new Set;da.recordLegacyContextWarning=function(e,t){for(var n=null,a=e;a!==null;)a.mode&Bt&&(n=a),a=a.return;n===null?console.error("Expected to find a StrictMode component in a strict mode tree. This error is likely caused by a bug in React. Please file an issue."):!_T.has(e.type)&&(a=xf.get(n),e.type.contextTypes!=null||e.type.childContextTypes!=null||t!==null&&typeof t.getChildContext=="function")&&(a===void 0&&(a=[],xf.set(n,a)),a.push(e))},da.flushLegacyContextWarning=function(){xf.forEach(function(e){if(e.length!==0){var t=e[0],n=new Set;e.forEach(function(o){n.add(te(o)||"Component"),_T.add(o.type)});var a=x(n);W(t,function(){console.error(`Legacy context API has been detected within a strict-mode tree.

The old API will be supported in all 16.x releases, but applications using it should migrate to the new version.

Please update the following components: %s

Learn more about this warning here: https://react.dev/link/legacy-context`,a)})}})},da.discardPendingWarnings=function(){Hs=[],Ls=[],Bs=[],Vs=[],$s=[],Ps=[],xf=new Map};var qs=Error("Suspense Exception: This is not a real error! It's an implementation detail of `use` to interrupt the current render. You must either rethrow it immediately, or move the `use` call outside of the `try/catch` block. Capturing without rethrowing will lead to unexpected behavior.\n\nTo handle async errors, wrap your component in an error boundary, or call the promise's `.catch` method and pass the result to `use`."),ST=Error("Suspense Exception: This is not a real error, and should not leak into userspace. If you're seeing this, it's likely a bug in React."),Af=Error("Suspense Exception: This is not a real error! It's an implementation detail of `useActionState` to interrupt the current render. You must either rethrow it immediately, or move the `useActionState` call outside of the `try/catch` block. Capturing without rethrowing will lead to unexpected behavior.\n\nTo handle async errors, wrap your component in an error boundary."),Xm={then:function(){console.error('Internal React error: A listener was unexpectedly attached to a "noop" thenable. This is a bug in React. Please file an issue.')}},Gs=null,Rf=!1,Qn=0,Zn=1,Yt=2,jt=4,gt=8,TT=0,OT=1,ET=2,Im=3,Xo=!1,wT=!1,Qm=null,Zm=!1,Wr=at(null),zf=at(0),Fr,xT=new Set,AT=new Set,Km=new Set,RT=new Set,Io=0,ce=null,$e=null,ht=null,Df=!1,el=!1,qi=!1,Cf=0,Ys=0,ro=null,tC=0,nC=25,C=null,Kn=null,lo=-1,Xs=!1,Mf={readContext:Je,use:jo,useCallback:ot,useContext:ot,useEffect:ot,useImperativeHandle:ot,useLayoutEffect:ot,useInsertionEffect:ot,useMemo:ot,useReducer:ot,useRef:ot,useState:ot,useDebugValue:ot,useDeferredValue:ot,useTransition:ot,useSyncExternalStore:ot,useId:ot,useHostTransitionStatus:ot,useFormState:ot,useActionState:ot,useOptimistic:ot,useMemoCache:ot,useCacheRefresh:ot},Jm=null,zT=null,Wm=null,DT=null,Ua=null,ha=null,jf=null;Jm={readContext:function(e){return Je(e)},use:jo,useCallback:function(e,t){return C="useCallback",Te(),br(t),op(e,t)},useContext:function(e){return C="useContext",Te(),Je(e)},useEffect:function(e,t){return C="useEffect",Te(),br(t),Cc(e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",Te(),br(n),ap(e,t,n)},useInsertionEffect:function(e,t){C="useInsertionEffect",Te(),br(t),Ci(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",Te(),br(t),np(e,t)},useMemo:function(e,t){C="useMemo",Te(),br(t);var n=M.H;M.H=Ua;try{return ip(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",Te();var a=M.H;M.H=Ua;try{return Qh(e,t,n)}finally{M.H=a}},useRef:function(e){return C="useRef",Te(),tp(e)},useState:function(e){C="useState",Te();var t=M.H;M.H=Ua;try{return Wh(e)}finally{M.H=t}},useDebugValue:function(){C="useDebugValue",Te()},useDeferredValue:function(e,t){return C="useDeferredValue",Te(),rp(e,t)},useTransition:function(){return C="useTransition",Te(),up()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",Te(),Kh(e,t,n)},useId:function(){return C="useId",Te(),cp()},useFormState:function(e,t){return C="useFormState",Te(),xc(),Sr(e,t)},useActionState:function(e,t){return C="useActionState",Te(),Sr(e,t)},useOptimistic:function(e){return C="useOptimistic",Te(),Fh(e)},useHostTransitionStatus:Mi,useMemoCache:Di,useCacheRefresh:function(){return C="useCacheRefresh",Te(),fp()}},zT={readContext:function(e){return Je(e)},use:jo,useCallback:function(e,t){return C="useCallback",V(),op(e,t)},useContext:function(e){return C="useContext",V(),Je(e)},useEffect:function(e,t){return C="useEffect",V(),Cc(e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",V(),ap(e,t,n)},useInsertionEffect:function(e,t){C="useInsertionEffect",V(),Ci(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",V(),np(e,t)},useMemo:function(e,t){C="useMemo",V();var n=M.H;M.H=Ua;try{return ip(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",V();var a=M.H;M.H=Ua;try{return Qh(e,t,n)}finally{M.H=a}},useRef:function(e){return C="useRef",V(),tp(e)},useState:function(e){C="useState",V();var t=M.H;M.H=Ua;try{return Wh(e)}finally{M.H=t}},useDebugValue:function(){C="useDebugValue",V()},useDeferredValue:function(e,t){return C="useDeferredValue",V(),rp(e,t)},useTransition:function(){return C="useTransition",V(),up()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",V(),Kh(e,t,n)},useId:function(){return C="useId",V(),cp()},useActionState:function(e,t){return C="useActionState",V(),Sr(e,t)},useFormState:function(e,t){return C="useFormState",V(),xc(),Sr(e,t)},useOptimistic:function(e){return C="useOptimistic",V(),Fh(e)},useHostTransitionStatus:Mi,useMemoCache:Di,useCacheRefresh:function(){return C="useCacheRefresh",V(),fp()}},Wm={readContext:function(e){return Je(e)},use:jo,useCallback:function(e,t){return C="useCallback",V(),jc(e,t)},useContext:function(e){return C="useContext",V(),Je(e)},useEffect:function(e,t){C="useEffect",V(),tn(2048,gt,e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",V(),Mc(e,t,n)},useInsertionEffect:function(e,t){return C="useInsertionEffect",V(),tn(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",V(),tn(4,jt,e,t)},useMemo:function(e,t){C="useMemo",V();var n=M.H;M.H=ha;try{return Uc(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",V();var a=M.H;M.H=ha;try{return _r(e,t,n)}finally{M.H=a}},useRef:function(){return C="useRef",V(),He().memoizedState},useState:function(){C="useState",V();var e=M.H;M.H=ha;try{return _r(ua)}finally{M.H=e}},useDebugValue:function(){C="useDebugValue",V()},useDeferredValue:function(e,t){return C="useDeferredValue",V(),l0(e,t)},useTransition:function(){return C="useTransition",V(),h0()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",V(),Ac(e,t,n)},useId:function(){return C="useId",V(),He().memoizedState},useFormState:function(e){return C="useFormState",V(),xc(),Rc(e)},useActionState:function(e){return C="useActionState",V(),Rc(e)},useOptimistic:function(e,t){return C="useOptimistic",V(),Jb(e,t)},useHostTransitionStatus:Mi,useMemoCache:Di,useCacheRefresh:function(){return C="useCacheRefresh",V(),He().memoizedState}},DT={readContext:function(e){return Je(e)},use:jo,useCallback:function(e,t){return C="useCallback",V(),jc(e,t)},useContext:function(e){return C="useContext",V(),Je(e)},useEffect:function(e,t){C="useEffect",V(),tn(2048,gt,e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",V(),Mc(e,t,n)},useInsertionEffect:function(e,t){return C="useInsertionEffect",V(),tn(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",V(),tn(4,jt,e,t)},useMemo:function(e,t){C="useMemo",V();var n=M.H;M.H=jf;try{return Uc(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",V();var a=M.H;M.H=jf;try{return ls(e,t,n)}finally{M.H=a}},useRef:function(){return C="useRef",V(),He().memoizedState},useState:function(){C="useState",V();var e=M.H;M.H=jf;try{return ls(ua)}finally{M.H=e}},useDebugValue:function(){C="useDebugValue",V()},useDeferredValue:function(e,t){return C="useDeferredValue",V(),s0(e,t)},useTransition:function(){return C="useTransition",V(),p0()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",V(),Ac(e,t,n)},useId:function(){return C="useId",V(),He().memoizedState},useFormState:function(e){return C="useFormState",V(),xc(),zc(e)},useActionState:function(e){return C="useActionState",V(),zc(e)},useOptimistic:function(e,t){return C="useOptimistic",V(),Fb(e,t)},useHostTransitionStatus:Mi,useMemoCache:Di,useCacheRefresh:function(){return C="useCacheRefresh",V(),He().memoizedState}},Ua={readContext:function(e){return z(),Je(e)},use:function(e){return y(),jo(e)},useCallback:function(e,t){return C="useCallback",y(),Te(),op(e,t)},useContext:function(e){return C="useContext",y(),Te(),Je(e)},useEffect:function(e,t){return C="useEffect",y(),Te(),Cc(e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",y(),Te(),ap(e,t,n)},useInsertionEffect:function(e,t){C="useInsertionEffect",y(),Te(),Ci(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",y(),Te(),np(e,t)},useMemo:function(e,t){C="useMemo",y(),Te();var n=M.H;M.H=Ua;try{return ip(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",y(),Te();var a=M.H;M.H=Ua;try{return Qh(e,t,n)}finally{M.H=a}},useRef:function(e){return C="useRef",y(),Te(),tp(e)},useState:function(e){C="useState",y(),Te();var t=M.H;M.H=Ua;try{return Wh(e)}finally{M.H=t}},useDebugValue:function(){C="useDebugValue",y(),Te()},useDeferredValue:function(e,t){return C="useDeferredValue",y(),Te(),rp(e,t)},useTransition:function(){return C="useTransition",y(),Te(),up()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",y(),Te(),Kh(e,t,n)},useId:function(){return C="useId",y(),Te(),cp()},useFormState:function(e,t){return C="useFormState",y(),Te(),Sr(e,t)},useActionState:function(e,t){return C="useActionState",y(),Te(),Sr(e,t)},useOptimistic:function(e){return C="useOptimistic",y(),Te(),Fh(e)},useMemoCache:function(e){return y(),Di(e)},useHostTransitionStatus:Mi,useCacheRefresh:function(){return C="useCacheRefresh",Te(),fp()}},ha={readContext:function(e){return z(),Je(e)},use:function(e){return y(),jo(e)},useCallback:function(e,t){return C="useCallback",y(),V(),jc(e,t)},useContext:function(e){return C="useContext",y(),V(),Je(e)},useEffect:function(e,t){C="useEffect",y(),V(),tn(2048,gt,e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",y(),V(),Mc(e,t,n)},useInsertionEffect:function(e,t){return C="useInsertionEffect",y(),V(),tn(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",y(),V(),tn(4,jt,e,t)},useMemo:function(e,t){C="useMemo",y(),V();var n=M.H;M.H=ha;try{return Uc(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",y(),V();var a=M.H;M.H=ha;try{return _r(e,t,n)}finally{M.H=a}},useRef:function(){return C="useRef",y(),V(),He().memoizedState},useState:function(){C="useState",y(),V();var e=M.H;M.H=ha;try{return _r(ua)}finally{M.H=e}},useDebugValue:function(){C="useDebugValue",y(),V()},useDeferredValue:function(e,t){return C="useDeferredValue",y(),V(),l0(e,t)},useTransition:function(){return C="useTransition",y(),V(),h0()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",y(),V(),Ac(e,t,n)},useId:function(){return C="useId",y(),V(),He().memoizedState},useFormState:function(e){return C="useFormState",y(),V(),Rc(e)},useActionState:function(e){return C="useActionState",y(),V(),Rc(e)},useOptimistic:function(e,t){return C="useOptimistic",y(),V(),Jb(e,t)},useMemoCache:function(e){return y(),Di(e)},useHostTransitionStatus:Mi,useCacheRefresh:function(){return C="useCacheRefresh",V(),He().memoizedState}},jf={readContext:function(e){return z(),Je(e)},use:function(e){return y(),jo(e)},useCallback:function(e,t){return C="useCallback",y(),V(),jc(e,t)},useContext:function(e){return C="useContext",y(),V(),Je(e)},useEffect:function(e,t){C="useEffect",y(),V(),tn(2048,gt,e,t)},useImperativeHandle:function(e,t,n){return C="useImperativeHandle",y(),V(),Mc(e,t,n)},useInsertionEffect:function(e,t){return C="useInsertionEffect",y(),V(),tn(4,Yt,e,t)},useLayoutEffect:function(e,t){return C="useLayoutEffect",y(),V(),tn(4,jt,e,t)},useMemo:function(e,t){C="useMemo",y(),V();var n=M.H;M.H=ha;try{return Uc(e,t)}finally{M.H=n}},useReducer:function(e,t,n){C="useReducer",y(),V();var a=M.H;M.H=ha;try{return ls(e,t,n)}finally{M.H=a}},useRef:function(){return C="useRef",y(),V(),He().memoizedState},useState:function(){C="useState",y(),V();var e=M.H;M.H=ha;try{return ls(ua)}finally{M.H=e}},useDebugValue:function(){C="useDebugValue",y(),V()},useDeferredValue:function(e,t){return C="useDeferredValue",y(),V(),s0(e,t)},useTransition:function(){return C="useTransition",y(),V(),p0()},useSyncExternalStore:function(e,t,n){return C="useSyncExternalStore",y(),V(),Ac(e,t,n)},useId:function(){return C="useId",y(),V(),He().memoizedState},useFormState:function(e){return C="useFormState",y(),V(),zc(e)},useActionState:function(e){return C="useActionState",y(),V(),zc(e)},useOptimistic:function(e,t){return C="useOptimistic",y(),V(),Fb(e,t)},useMemoCache:function(e){return y(),Di(e)},useHostTransitionStatus:Mi,useCacheRefresh:function(){return C="useCacheRefresh",V(),He().memoizedState}};var CT={react_stack_bottom_frame:function(e,t,n){var a=Ca;Ca=!0;try{return e(t,n)}finally{Ca=a}}},Fm=CT.react_stack_bottom_frame.bind(CT),MT={react_stack_bottom_frame:function(e){var t=Ca;Ca=!0;try{return e.render()}finally{Ca=t}}},jT=MT.react_stack_bottom_frame.bind(MT),UT={react_stack_bottom_frame:function(e,t){try{t.componentDidMount()}catch(n){Be(e,e.return,n)}}},ev=UT.react_stack_bottom_frame.bind(UT),kT={react_stack_bottom_frame:function(e,t,n,a,o){try{t.componentDidUpdate(n,a,o)}catch(r){Be(e,e.return,r)}}},NT=kT.react_stack_bottom_frame.bind(kT),HT={react_stack_bottom_frame:function(e,t){var n=t.stack;e.componentDidCatch(t.value,{componentStack:n!==null?n:""})}},aC=HT.react_stack_bottom_frame.bind(HT),LT={react_stack_bottom_frame:function(e,t,n){try{n.componentWillUnmount()}catch(a){Be(e,t,a)}}},BT=LT.react_stack_bottom_frame.bind(LT),VT={react_stack_bottom_frame:function(e){e.resourceKind!=null&&console.error("Expected only SimpleEffects when enableUseEffectCRUDOverload is disabled, got %s",e.resourceKind);var t=e.create;return e=e.inst,t=t(),e.destroy=t}},oC=VT.react_stack_bottom_frame.bind(VT),$T={react_stack_bottom_frame:function(e,t,n){try{n()}catch(a){Be(e,t,a)}}},iC=$T.react_stack_bottom_frame.bind($T),PT={react_stack_bottom_frame:function(e){var t=e._init;return t(e._payload)}},Qo=PT.react_stack_bottom_frame.bind(PT),tl=null,Is=0,ye=null,tv,qT=tv=!1,GT={},YT={},XT={};b=function(e,t,n){if(n!==null&&typeof n=="object"&&n._store&&(!n._store.validated&&n.key==null||n._store.validated===2)){if(typeof n._store!="object")throw Error("React Component in warnForMissingKey should have a _store. This error is likely caused by a bug in React. Please file an issue.");n._store.validated=1;var a=te(e),o=a||"null";if(!GT[o]){GT[o]=!0,n=n._owner,e=e._debugOwner;var r="";e&&typeof e.tag=="number"&&(o=te(e))&&(r=`

Check the render method of \``+o+"`."),r||a&&(r=`

Check the top-level render call using <`+a+">.");var c="";n!=null&&e!==n&&(a=null,typeof n.tag=="number"?a=te(n):typeof n.name=="string"&&(a=n.name),a&&(c=" It was passed a child from "+a+".")),W(t,function(){console.error('Each child in a list should have a unique "key" prop.%s%s See https://react.dev/link/warning-keys for more information.',r,c)})}}};var nl=g0(!0),IT=g0(!1),Jn=at(null),ka=null,al=1,Qs=2,bt=at(0),QT={},ZT=new Set,KT=new Set,JT=new Set,WT=new Set,FT=new Set,e1=new Set,t1=new Set,n1=new Set,a1=new Set,o1=new Set;Object.freeze(QT);var nv={enqueueSetState:function(e,t,n){e=e._reactInternals;var a=_n(e),o=Co(a);o.payload=t,n!=null&&(hp(n),o.callback=n),t=Mo(e,o,a),t!==null&&(it(t,e,a),as(t,e,a)),wo(e,a)},enqueueReplaceState:function(e,t,n){e=e._reactInternals;var a=_n(e),o=Co(a);o.tag=OT,o.payload=t,n!=null&&(hp(n),o.callback=n),t=Mo(e,o,a),t!==null&&(it(t,e,a),as(t,e,a)),wo(e,a)},enqueueForceUpdate:function(e,t){e=e._reactInternals;var n=_n(e),a=Co(n);a.tag=ET,t!=null&&(hp(t),a.callback=t),t=Mo(e,a,n),t!==null&&(it(t,e,n),as(t,e,n)),X!==null&&typeof X.markForceUpdateScheduled=="function"&&X.markForceUpdateScheduled(e,n)}},av=typeof reportError=="function"?reportError:function(e){if(typeof window=="object"&&typeof window.ErrorEvent=="function"){var t=new window.ErrorEvent("error",{bubbles:!0,cancelable:!0,message:typeof e=="object"&&e!==null&&typeof e.message=="string"?String(e.message):String(e),error:e});if(!window.dispatchEvent(t))return}else if(typeof process=="object"&&typeof process.emit=="function"){process.emit("uncaughtException",e);return}console.error(e)},ol=null,ov=null,i1=Error("This is not a real error. It's an implementation detail of React's selective hydration feature. If this leaks into userspace, it's a bug in React. Please file an issue."),Tt=!1,r1={},l1={},s1={},u1={},il=!1,c1={},iv={},rv={dehydrated:null,treeContext:null,retryLane:0,hydrationErrors:null},f1=!1,d1=null;d1=new Set;var so=!1,rt=!1,lv=!1,h1=typeof WeakSet=="function"?WeakSet:Set,Ot=null,rl=null,ll=null,pt=null,on=!1,pa=null,Zs=8192,rC={getCacheForType:function(e){var t=Je(yt),n=t.data.get(e);return n===void 0&&(n=e(),t.data.set(e,n)),n},getOwner:function(){return Tn}};if(typeof Symbol=="function"&&Symbol.for){var Ks=Symbol.for;Ks("selector.component"),Ks("selector.has_pseudo_class"),Ks("selector.role"),Ks("selector.test_id"),Ks("selector.text")}var lC=[],sC=typeof WeakMap=="function"?WeakMap:Map,On=0,Xt=2,ma=4,uo=0,Js=1,sl=2,sv=3,Gi=4,Uf=6,p1=5,je=On,qe=null,Se=null,Oe=0,rn=0,Ws=1,Yi=2,Fs=3,m1=4,uv=5,ul=6,eu=7,cv=8,Xi=9,Ue=rn,En=null,Zo=!1,cl=!1,fv=!1,Na=0,nt=uo,Ko=0,Jo=0,dv=0,wn=0,Ii=0,tu=null,It=null,kf=!1,hv=0,v1=300,Nf=1/0,y1=500,nu=null,Wo=null,uC=0,cC=1,fC=2,Qi=0,g1=1,b1=2,_1=3,dC=4,pv=5,Ut=0,Fo=null,fl=null,ei=0,mv=0,vv=null,S1=null,hC=50,au=0,yv=null,gv=!1,Hf=!1,pC=50,Zi=0,ou=null,dl=!1,Lf=null,T1=!1,O1=new Set,mC={},Bf=null,hl=null,bv=!1,_v=!1,Vf=!1,Sv=!1,Ki=0,Tv={};(function(){for(var e=0;e<Bm.length;e++){var t=Bm[e],n=t.toLowerCase();t=t[0].toUpperCase()+t.slice(1),sa(n,"on"+t)}sa(lT,"onAnimationEnd"),sa(sT,"onAnimationIteration"),sa(uT,"onAnimationStart"),sa("dblclick","onDoubleClick"),sa("focusin","onFocus"),sa("focusout","onBlur"),sa(XD,"onTransitionRun"),sa(ID,"onTransitionStart"),sa(QD,"onTransitionCancel"),sa(cT,"onTransitionEnd")})(),G("onMouseEnter",["mouseout","mouseover"]),G("onMouseLeave",["mouseout","mouseover"]),G("onPointerEnter",["pointerout","pointerover"]),G("onPointerLeave",["pointerout","pointerover"]),$("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),$("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),$("onBeforeInput",["compositionend","keypress","textInput","paste"]),$("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),$("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),$("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var iu="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),Ov=new Set("beforetoggle cancel close invalid load scroll scrollend toggle".split(" ").concat(iu)),$f="_reactListening"+Math.random().toString(36).slice(2),E1=!1,w1=!1,Pf=!1,x1=!1,qf=!1,Gf=!1,A1=!1,Yf={},vC=/\r\n?/g,yC=/\u0000|\uFFFD/g,Ji="http://www.w3.org/1999/xlink",Ev="http://www.w3.org/XML/1998/namespace",gC="javascript:throw new Error('React form unexpectedly submitted.')",bC="suppressHydrationWarning",Xf="$",If="/$",co="$?",ru="$!",_C=1,SC=2,TC=4,wv="F!",R1="F",z1="complete",OC="style",fo=0,pl=1,Qf=2,xv=null,Av=null,D1={dialog:!0,webview:!0},Rv=null,C1=typeof setTimeout=="function"?setTimeout:void 0,EC=typeof clearTimeout=="function"?clearTimeout:void 0,Wi=-1,M1=typeof Promise=="function"?Promise:void 0,wC=typeof queueMicrotask=="function"?queueMicrotask:typeof M1<"u"?function(e){return M1.resolve(null).then(e).catch(cz)}:C1,zv=null,Fi=0,lu=1,j1=2,U1=3,Wn=4,Fn=new Map,k1=new Set,ho=Me.d;Me.d={f:function(){var e=ho.f(),t=xr();return e||t},r:function(e){var t=la(e);t!==null&&t.tag===5&&t.type==="form"?d0(t):ho.r(e)},D:function(e){ho.D(e),tS("dns-prefetch",e,null)},C:function(e,t){ho.C(e,t),tS("preconnect",e,t)},L:function(e,t,n){ho.L(e,t,n);var a=ml;if(a&&e&&t){var o='link[rel="preload"][as="'+Bn(t)+'"]';t==="image"&&n&&n.imageSrcSet?(o+='[imagesrcset="'+Bn(n.imageSrcSet)+'"]',typeof n.imageSizes=="string"&&(o+='[imagesizes="'+Bn(n.imageSizes)+'"]')):o+='[href="'+Bn(e)+'"]';var r=o;switch(t){case"style":r=Dr(e);break;case"script":r=Cr(e)}Fn.has(r)||(e=be({rel:"preload",href:t==="image"&&n&&n.imageSrcSet?void 0:e,as:t},n),Fn.set(r,e),a.querySelector(o)!==null||t==="style"&&a.querySelector(_s(r))||t==="script"&&a.querySelector(Ss(r))||(t=a.createElement("link"),Dt(t,"link",e),w(t),a.head.appendChild(t)))}},m:function(e,t){ho.m(e,t);var n=ml;if(n&&e){var a=t&&typeof t.as=="string"?t.as:"script",o='link[rel="modulepreload"][as="'+Bn(a)+'"][href="'+Bn(e)+'"]',r=o;switch(a){case"audioworklet":case"paintworklet":case"serviceworker":case"sharedworker":case"worker":case"script":r=Cr(e)}if(!Fn.has(r)&&(e=be({rel:"modulepreload",href:e},t),Fn.set(r,e),n.querySelector(o)===null)){switch(a){case"audioworklet":case"paintworklet":case"serviceworker":case"sharedworker":case"worker":case"script":if(n.querySelector(Ss(r)))return}a=n.createElement("link"),Dt(a,"link",e),w(a),n.head.appendChild(a)}}},X:function(e,t){ho.X(e,t);var n=ml;if(n&&e){var a=m(n).hoistableScripts,o=Cr(e),r=a.get(o);r||(r=n.querySelector(Ss(o)),r||(e=be({src:e,async:!0},t),(t=Fn.get(o))&&nm(e,t),r=n.createElement("script"),w(r),Dt(r,"link",e),n.head.appendChild(r)),r={type:"script",instance:r,count:1,state:null},a.set(o,r))}},S:function(e,t,n){ho.S(e,t,n);var a=ml;if(a&&e){var o=m(a).hoistableStyles,r=Dr(e);t=t||"default";var c=o.get(r);if(!c){var d={loading:Fi,preload:null};if(c=a.querySelector(_s(r)))d.loading=lu|Wn;else{e=be({rel:"stylesheet",href:e,"data-precedence":t},n),(n=Fn.get(r))&&tm(e,n);var v=c=a.createElement("link");w(v),Dt(v,"link",e),v._p=new Promise(function(g,R){v.onload=g,v.onerror=R}),v.addEventListener("load",function(){d.loading|=lu}),v.addEventListener("error",function(){d.loading|=j1}),d.loading|=Wn,tf(c,t,a)}c={type:"stylesheet",instance:c,count:1,state:d},o.set(r,c)}}},M:function(e,t){ho.M(e,t);var n=ml;if(n&&e){var a=m(n).hoistableScripts,o=Cr(e),r=a.get(o);r||(r=n.querySelector(Ss(o)),r||(e=be({src:e,async:!0,type:"module"},t),(t=Fn.get(o))&&nm(e,t),r=n.createElement("script"),w(r),Dt(r,"link",e),n.head.appendChild(r)),r={type:"script",instance:r,count:1,state:null},a.set(o,r))}}};var ml=typeof document>"u"?null:document,Zf=null,su=null,Dv=null,Kf=null,er=Gz,uu={$$typeof:Aa,Provider:null,Consumer:null,_currentValue:er,_currentValue2:er,_threadCount:0},N1="%c%s%c ",H1="background: #e6e6e6;background: light-dark(rgba(0,0,0,0.1), rgba(255,255,255,0.25));color: #000000;color: light-dark(#000000, #ffffff);border-radius: 2px",L1="",Jf=" ",xC=Function.prototype.bind,B1=!1,V1=null,$1=null,P1=null,q1=null,G1=null,Y1=null,X1=null,I1=null,Q1=null;V1=function(e,t,n,a){t=i(e,t),t!==null&&(n=l(t.memoizedState,n,0,a),t.memoizedState=n,t.baseState=n,e.memoizedProps=be({},e.memoizedProps),n=Ft(e,2),n!==null&&it(n,e,2))},$1=function(e,t,n){t=i(e,t),t!==null&&(n=f(t.memoizedState,n,0),t.memoizedState=n,t.baseState=n,e.memoizedProps=be({},e.memoizedProps),n=Ft(e,2),n!==null&&it(n,e,2))},P1=function(e,t,n,a){t=i(e,t),t!==null&&(n=u(t.memoizedState,n,a),t.memoizedState=n,t.baseState=n,e.memoizedProps=be({},e.memoizedProps),n=Ft(e,2),n!==null&&it(n,e,2))},q1=function(e,t,n){e.pendingProps=l(e.memoizedProps,t,0,n),e.alternate&&(e.alternate.pendingProps=e.pendingProps),t=Ft(e,2),t!==null&&it(t,e,2)},G1=function(e,t){e.pendingProps=f(e.memoizedProps,t,0),e.alternate&&(e.alternate.pendingProps=e.pendingProps),t=Ft(e,2),t!==null&&it(t,e,2)},Y1=function(e,t,n){e.pendingProps=u(e.memoizedProps,t,n),e.alternate&&(e.alternate.pendingProps=e.pendingProps),t=Ft(e,2),t!==null&&it(t,e,2)},X1=function(e){var t=Ft(e,2);t!==null&&it(t,e,2)},I1=function(e){p=e},Q1=function(e){h=e};var Wf=!0,Ff=null,Cv=!1,ti=null,ni=null,ai=null,cu=new Map,fu=new Map,oi=[],AC="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset".split(" "),ed=null;if(rf.prototype.render=um.prototype.render=function(e){var t=this._internalRoot;if(t===null)throw Error("Cannot update an unmounted root.");var n=arguments;typeof n[1]=="function"?console.error("does not support the second callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."):ne(n[1])?console.error("You passed a container to the second argument of root.render(...). You don't need to pass it again since you already passed it to create the root."):typeof n[1]<"u"&&console.error("You passed a second argument to root.render(...) but it only accepts one argument."),n=e;var a=t.current,o=_n(a);om(a,o,n,t,null,null)},rf.prototype.unmount=um.prototype.unmount=function(){var e=arguments;if(typeof e[0]=="function"&&console.error("does not support a callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."),e=this._internalRoot,e!==null){this._internalRoot=null;var t=e.containerInfo;(je&(Xt|ma))!==On&&console.error("Attempted to synchronously unmount a root while React was already rendering. React cannot finish unmounting the root until the current render has completed, which may lead to a race condition."),om(e.current,2,null,e,null,null),xr(),t[qo]=null}},rf.prototype.unstable_scheduleHydration=function(e){if(e){var t=ql();e={blockedOn:null,target:e,priority:t};for(var n=0;n<oi.length&&t!==0&&t<oi[n].priority;n++);oi.splice(n,0,e),n===0&&pS(e)}},function(){var e=cm.version;if(e!=="19.1.1")throw Error(`Incompatible React versions: The "react" and "react-dom" packages must have the exact same version. Instead got:
  - react:      `+(e+`
  - react-dom:  19.1.1
Learn more: https://react.dev/warnings/version-mismatch`))}(),typeof Map=="function"&&Map.prototype!=null&&typeof Map.prototype.forEach=="function"&&typeof Set=="function"&&Set.prototype!=null&&typeof Set.prototype.clear=="function"&&typeof Set.prototype.forEach=="function"||console.error("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://react.dev/link/react-polyfills"),Me.findDOMNode=function(e){var t=e._reactInternals;if(t===void 0)throw typeof e.render=="function"?Error("Unable to find node on an unmounted component."):(e=Object.keys(e).join(","),Error("Argument appears to not be a ReactComponent. Keys: "+e));return e=ke(t),e=e!==null?Ze(e):null,e=e===null?null:e.stateNode,e},!function(){var e={bundleType:1,version:"19.1.1",rendererPackageName:"react-dom",currentDispatcherRef:M,reconcilerVersion:"19.1.1"};return e.overrideHookState=V1,e.overrideHookStateDeletePath=$1,e.overrideHookStateRenamePath=P1,e.overrideProps=q1,e.overridePropsDeletePath=G1,e.overridePropsRenamePath=Y1,e.scheduleUpdate=X1,e.setErrorHandler=I1,e.setSuspenseHandler=Q1,e.scheduleRefresh=Y,e.scheduleRoot=H,e.setRefreshHandler=F,e.getCurrentFiber=jz,e.getLaneLabelMap=Uz,e.injectProfilingHooks=vi,ct(e)}()&&Ma&&window.top===window.self&&(-1<navigator.userAgent.indexOf("Chrome")&&navigator.userAgent.indexOf("Edge")===-1||-1<navigator.userAgent.indexOf("Firefox"))){var Z1=window.location.protocol;/^(https?|file):$/.test(Z1)&&console.info("%cDownload the React DevTools for a better development experience: https://react.dev/link/react-devtools"+(Z1==="file:"?`
You might need to use a local HTTP server (instead of file://): https://react.dev/link/react-devtools-faq`:""),"font-weight:bold")}_l.createRoot=function(e,t){if(!ne(e))throw Error("Target container is not a DOM element.");yS(e);var n=!1,a="",o=T0,r=O0,c=E0,d=null;return t!=null&&(t.hydrate?console.warn("hydrate through createRoot is deprecated. Use ReactDOMClient.hydrateRoot(container, <App />) instead."):typeof t=="object"&&t!==null&&t.$$typeof===Bo&&console.error(`You passed a JSX element to createRoot. You probably meant to call root.render instead. Example usage:

  let root = createRoot(domContainer);
  root.render(<App />);`),t.unstable_strictMode===!0&&(n=!0),t.identifierPrefix!==void 0&&(a=t.identifierPrefix),t.onUncaughtError!==void 0&&(o=t.onUncaughtError),t.onCaughtError!==void 0&&(r=t.onCaughtError),t.onRecoverableError!==void 0&&(c=t.onRecoverableError),t.unstable_transitionCallbacks!==void 0&&(d=t.unstable_transitionCallbacks)),t=sS(e,1,!1,null,null,n,a,o,r,c,d,null),e[qo]=t.current,Yp(e),new um(t)},_l.hydrateRoot=function(e,t,n){if(!ne(e))throw Error("Target container is not a DOM element.");yS(e),t===void 0&&console.error("Must provide initial children as second argument to hydrateRoot. Example usage: hydrateRoot(domContainer, <App />)");var a=!1,o="",r=T0,c=O0,d=E0,v=null,g=null;return n!=null&&(n.unstable_strictMode===!0&&(a=!0),n.identifierPrefix!==void 0&&(o=n.identifierPrefix),n.onUncaughtError!==void 0&&(r=n.onUncaughtError),n.onCaughtError!==void 0&&(c=n.onCaughtError),n.onRecoverableError!==void 0&&(d=n.onRecoverableError),n.unstable_transitionCallbacks!==void 0&&(v=n.unstable_transitionCallbacks),n.formState!==void 0&&(g=n.formState)),t=sS(e,1,!0,t,n??null,a,o,r,c,d,v,g),t.context=uS(null),n=t.current,a=_n(n),a=Vl(a),o=Co(a),o.callback=null,Mo(n,o,a),n=a,t.current.lanes=n,Ao(t,n),wa(t),e[qo]=t.current,Yp(e),new rf(t)},_l.version="19.1.1",typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())}(),_l}var Pv;function aO(){return Pv||(Pv=1,ad.exports=nO()),ad.exports}var oO=aO(),Q=bl();const qv=vl(Q);var Qt={NODE_ENV:'"production"',WEBPACK_ENV:'"production"'},iO={0:"Invalid value for configuration 'enforceActions', expected 'never', 'always' or 'observed'",1:function(l,u){return"Cannot apply '"+l+"' to '"+u.toString()+"': Field not found."},5:"'keys()' can only be used on observable objects, arrays, sets and maps",6:"'values()' can only be used on observable objects, arrays, sets and maps",7:"'entries()' can only be used on observable objects, arrays and maps",8:"'set()' can only be used on observable objects, arrays and maps",9:"'remove()' can only be used on observable objects, arrays and maps",10:"'has()' can only be used on observable objects, arrays and maps",11:"'get()' can only be used on observable objects, arrays and maps",12:"Invalid annotation",13:"Dynamic observable objects cannot be frozen. If you're passing observables to 3rd party component/function that calls Object.freeze, pass copy instead: toJS(observable)",14:"Intercept handlers should return nothing or a change object",15:"Observable arrays cannot be frozen. If you're passing observables to 3rd party component/function that calls Object.freeze, pass copy instead: toJS(observable)",16:"Modification exception: the internal structure of an observable array was changed.",17:function(l,u){return"[mobx.array] Index out of bounds, "+l+" is larger than "+u},18:"mobx.map requires Map polyfill for the current browser. Check babel-polyfill or core-js/es6/map.js",19:function(l){return"Cannot initialize from classes that inherit from Map: "+l.constructor.name},20:function(l){return"Cannot initialize map from "+l},21:function(l){return"Cannot convert to map from '"+l+"'"},22:"mobx.set requires Set polyfill for the current browser. Check babel-polyfill or core-js/es6/set.js",23:"It is not possible to get index atoms from arrays",24:function(l){return"Cannot obtain administration from "+l},25:function(l,u){return"the entry '"+l+"' does not exist in the observable map '"+u+"'"},26:"please specify a property",27:function(l,u){return"no observable property '"+l.toString()+"' found on the observable object '"+u+"'"},28:function(l){return"Cannot obtain atom from "+l},29:"Expecting some object",30:"invalid action stack. did you forget to finish an action?",31:"missing option for computed: get",32:function(l,u){return"Cycle detected in computation "+l+": "+u},33:function(l){return"The setter of computed value '"+l+"' is trying to update itself. Did you intend to update an _observable_ value, instead of the computed property?"},34:function(l){return"[ComputedValue '"+l+"'] It is not possible to assign a new value to a computed value."},35:"There are multiple, different versions of MobX active. Make sure MobX is loaded only once or use `configure({ isolateGlobalState: true })`",36:"isolateGlobalState should be called before MobX is running any reactions",37:function(l){return"[mobx] `observableArray."+l+"()` mutates the array in-place, which is not allowed inside a derivation. Use `array.slice()."+l+"()` instead"},38:"'ownKeys()' can only be used on observable objects",39:"'defineProperty()' can only be used on observable objects"},rO=iO;function I(i){for(var l=arguments.length,u=new Array(l>1?l-1:0),s=1;s<l;s++)u[s-1]=arguments[s];{var f=typeof i=="string"?i:rO[i];throw typeof f=="function"&&(f=f.apply(null,u)),new Error("[MobX] "+f)}}var lO={};function du(){return typeof globalThis<"u"?globalThis:typeof window<"u"?window:typeof global<"u"?global:typeof self<"u"?self:lO}var Gv=Object.assign,hu=Object.getOwnPropertyDescriptor,va=Object.defineProperty,pu=Object.prototype,mu=[];Object.freeze(mu);var Yv={};Object.freeze(Yv);var sO=typeof Proxy<"u",uO=Object.toString();function Xv(){sO||I("`Proxy` objects are not available in the current environment. Please configure MobX to enable a fallback implementation.`")}function Sl(i){B.verifyProxies&&I("MobX is currently configured to be able to run in ES5 mode, but in ES5 MobX won't be able to "+i)}function ea(){return++B.mobxGuid}function ld(i){var l=!1;return function(){if(!l)return l=!0,i.apply(this,arguments)}}var tr=function(){};function mt(i){return typeof i=="function"}function ii(i){var l=typeof i;switch(l){case"string":case"symbol":case"number":return!0}return!1}function vu(i){return i!==null&&typeof i=="object"}function ln(i){if(!vu(i))return!1;var l=Object.getPrototypeOf(i);if(l==null)return!0;var u=Object.hasOwnProperty.call(l,"constructor")&&l.constructor;return typeof u=="function"&&u.toString()===uO}function Iv(i){var l=i?.constructor;return l?l.name==="GeneratorFunction"||l.displayName==="GeneratorFunction":!1}function Tl(i,l,u){va(i,l,{enumerable:!1,writable:!0,configurable:!0,value:u})}function Qv(i,l,u){va(i,l,{enumerable:!1,writable:!1,configurable:!0,value:u})}function ri(i,l){var u="isMobX"+i;return l.prototype[u]=!0,function(s){return vu(s)&&s[u]===!0}}function nr(i){return i!=null&&Object.prototype.toString.call(i)==="[object Map]"}function cO(i){var l=Object.getPrototypeOf(i),u=Object.getPrototypeOf(l),s=Object.getPrototypeOf(u);return s===null}function Ha(i){return i!=null&&Object.prototype.toString.call(i)==="[object Set]"}var Zv=typeof Object.getOwnPropertySymbols<"u";function fO(i){var l=Object.keys(i);if(!Zv)return l;var u=Object.getOwnPropertySymbols(i);return u.length?[].concat(l,u.filter(function(s){return pu.propertyIsEnumerable.call(i,s)})):l}var ar=typeof Reflect<"u"&&Reflect.ownKeys?Reflect.ownKeys:Zv?function(i){return Object.getOwnPropertyNames(i).concat(Object.getOwnPropertySymbols(i))}:Object.getOwnPropertyNames;function sd(i){return typeof i=="string"?i:typeof i=="symbol"?i.toString():new String(i).toString()}function Kv(i){return i===null?null:typeof i=="object"?""+i:i}function xn(i,l){return pu.hasOwnProperty.call(i,l)}var dO=Object.getOwnPropertyDescriptors||function(l){var u={};return ar(l).forEach(function(s){u[s]=hu(l,s)}),u};function sn(i,l){return!!(i&l)}function un(i,l,u){return u?i|=l:i&=~l,i}function Jv(i,l){(l==null||l>i.length)&&(l=i.length);for(var u=0,s=Array(l);u<l;u++)s[u]=i[u];return s}function hO(i,l){for(var u=0;u<l.length;u++){var s=l[u];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(i,mO(s.key),s)}}function or(i,l,u){return l&&hO(i.prototype,l),Object.defineProperty(i,"prototype",{writable:!1}),i}function ir(i,l){var u=typeof Symbol<"u"&&i[Symbol.iterator]||i["@@iterator"];if(u)return(u=u.call(i)).next.bind(u);if(Array.isArray(i)||(u=vO(i))||l){u&&(i=u);var s=0;return function(){return s>=i.length?{done:!0}:{done:!1,value:i[s++]}}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function La(){return La=Object.assign?Object.assign.bind():function(i){for(var l=1;l<arguments.length;l++){var u=arguments[l];for(var s in u)({}).hasOwnProperty.call(u,s)&&(i[s]=u[s])}return i},La.apply(null,arguments)}function Wv(i,l){i.prototype=Object.create(l.prototype),i.prototype.constructor=i,ud(i,l)}function ud(i,l){return ud=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(u,s){return u.__proto__=s,u},ud(i,l)}function pO(i,l){if(typeof i!="object"||!i)return i;var u=i[Symbol.toPrimitive];if(u!==void 0){var s=u.call(i,l);if(typeof s!="object")return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(i)}function mO(i){var l=pO(i,"string");return typeof l=="symbol"?l:l+""}function vO(i,l){if(i){if(typeof i=="string")return Jv(i,l);var u={}.toString.call(i).slice(8,-1);return u==="Object"&&i.constructor&&(u=i.constructor.name),u==="Map"||u==="Set"?Array.from(i):u==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(u)?Jv(i,l):void 0}}var Zt=Symbol("mobx-stored-annotations");function ya(i){function l(u,s){if(El(s))return i.decorate_20223_(u,s);Ol(u,s,i)}return Object.assign(l,i)}function Ol(i,l,u){if(xn(i,Zt)||Tl(i,Zt,La({},i[Zt])),_u(u)&&!xn(i[Zt],l)){var s=i.constructor.name+".prototype."+l.toString();I("'"+s+"' is decorated with 'override', but no such decorated member was found on prototype.")}yO(i,u,l),_u(u)||(i[Zt][l]=u)}function yO(i,l,u){if(!_u(l)&&xn(i[Zt],u)){var s=i.constructor.name+".prototype."+u.toString(),f=i[Zt][u].annotationType_,h=l.annotationType_;I("Cannot apply '@"+h+"' to '"+s+"':"+(`
The field is already decorated with '@`+f+"'.")+`
Re-decorating fields is not allowed.
Use '@override' decorator for methods overridden by subclass.`)}}function gO(i){return xn(i,Zt)||Tl(i,Zt,La({},i[Zt])),i[Zt]}function El(i){return typeof i=="object"&&typeof i.kind=="string"}function yu(i,l){l.includes(i.kind)||I("The decorator applied to '"+String(i.name)+"' cannot be used on a "+i.kind+" element")}var le=Symbol("mobx administration"),mo=function(){function i(u){u===void 0&&(u="Atom@"+ea()),this.name_=void 0,this.flags_=0,this.observers_=new Set,this.lastAccessedBy_=0,this.lowestObserverState_=ze.NOT_TRACKING_,this.onBOL=void 0,this.onBUOL=void 0,this.name_=u}var l=i.prototype;return l.onBO=function(){this.onBOL&&this.onBOL.forEach(function(s){return s()})},l.onBUO=function(){this.onBUOL&&this.onBUOL.forEach(function(s){return s()})},l.reportObserved=function(){return gy(this)},l.reportChanged=function(){zn(),by(this),Dn()},l.toString=function(){return this.name_},or(i,[{key:"isBeingObserved",get:function(){return sn(this.flags_,i.isBeingObservedMask_)},set:function(s){this.flags_=un(this.flags_,i.isBeingObservedMask_,s)}},{key:"isPendingUnobservation",get:function(){return sn(this.flags_,i.isPendingUnobservationMask_)},set:function(s){this.flags_=un(this.flags_,i.isPendingUnobservationMask_,s)}},{key:"diffValue",get:function(){return sn(this.flags_,i.diffValueMask_)?1:0},set:function(s){this.flags_=un(this.flags_,i.diffValueMask_,s===1)}}])}();mo.isBeingObservedMask_=1,mo.isPendingUnobservationMask_=2,mo.diffValueMask_=4;var cd=ri("Atom",mo);function Fv(i,l,u){l===void 0&&(l=tr),u===void 0&&(u=tr);var s=new mo(i);return l!==tr&&zE(s,l),u!==tr&&Ay(s,u),s}function bO(i,l){return Ky(i,l)}function _O(i,l){return Object.is?Object.is(i,l):i===l?i!==0||1/i===1/l:i!==i&&l!==l}var gu={structural:bO,default:_O};function li(i,l,u){return zl(i)?i:Array.isArray(i)?Et.array(i,{name:u}):ln(i)?Et.object(i,void 0,{name:u}):nr(i)?Et.map(i,{name:u}):Ha(i)?Et.set(i,{name:u}):typeof i=="function"&&!lr(i)&&!Rl(i)?Iv(i)?sr(i):Al(u,i):i}function SO(i,l,u){if(i==null||ur(i)||Uu(i)||yo(i)||ba(i))return i;if(Array.isArray(i))return Et.array(i,{name:u,deep:!1});if(ln(i))return Et.object(i,void 0,{name:u,deep:!1});if(nr(i))return Et.map(i,{name:u,deep:!1});if(Ha(i))return Et.set(i,{name:u,deep:!1});I("The shallow modifier / decorator can only used in combination with arrays, objects, maps and sets")}function bu(i){return i}function TO(i,l){return zl(i)&&I("observable.struct should not be used with observable values"),Ky(i,l)?l:i}var OO="override";function _u(i){return i.annotationType_===OO}function wl(i,l){return{annotationType_:i,options_:l,make_:EO,extend_:wO,decorate_20223_:xO}}function EO(i,l,u,s){var f;if((f=this.options_)!=null&&f.bound)return this.extend_(i,l,u,!1)===null?0:1;if(s===i.target_)return this.extend_(i,l,u,!1)===null?0:2;if(lr(u.value))return 1;var h=ey(i,this,l,u,!1);return va(s,l,h),2}function wO(i,l,u,s){var f=ey(i,this,l,u);return i.defineProperty_(l,f,s)}function xO(i,l){yu(l,["method","field"]);var u=l.kind,s=l.name,f=l.addInitializer,h=this,p=function(z){var D,x,O,H;return si((D=(x=h.options_)==null?void 0:x.name)!=null?D:s.toString(),z,(O=(H=h.options_)==null?void 0:H.autoAction)!=null?O:!1)};if(u=="field")return function(y){var z,D=y;return lr(D)||(D=p(D)),(z=h.options_)!=null&&z.bound&&(D=D.bind(this),D.isMobxAction=!0),D};if(u=="method"){var b;return lr(i)||(i=p(i)),(b=this.options_)!=null&&b.bound&&f(function(){var y=this,z=y[s].bind(y);z.isMobxAction=!0,y[s]=z}),i}I("Cannot apply '"+h.annotationType_+"' to '"+String(s)+"' (kind: "+u+"):"+(`
'`+h.annotationType_+"' can only be used on properties with a function value."))}function AO(i,l,u,s){var f=l.annotationType_,h=s.value;mt(h)||I("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' can only be used on properties with a function value."))}function ey(i,l,u,s,f){var h,p,b,y,z,D,x;f===void 0&&(f=B.safeDescriptors),AO(i,l,u,s);var O=s.value;if((h=l.options_)!=null&&h.bound){var H;O=O.bind((H=i.proxy_)!=null?H:i.target_)}return{value:si((p=(b=l.options_)==null?void 0:b.name)!=null?p:u.toString(),O,(y=(z=l.options_)==null?void 0:z.autoAction)!=null?y:!1,(D=l.options_)!=null&&D.bound?(x=i.proxy_)!=null?x:i.target_:void 0),configurable:f?i.isPlainObject_:!0,enumerable:!1,writable:!f}}function ty(i,l){return{annotationType_:i,options_:l,make_:RO,extend_:zO,decorate_20223_:DO}}function RO(i,l,u,s){var f;if(s===i.target_)return this.extend_(i,l,u,!1)===null?0:2;if((f=this.options_)!=null&&f.bound&&(!xn(i.target_,l)||!Rl(i.target_[l]))&&this.extend_(i,l,u,!1)===null)return 0;if(Rl(u.value))return 1;var h=ny(i,this,l,u,!1,!1);return va(s,l,h),2}function zO(i,l,u,s){var f,h=ny(i,this,l,u,(f=this.options_)==null?void 0:f.bound);return i.defineProperty_(l,h,s)}function DO(i,l){var u;yu(l,["method"]);var s=l.name,f=l.addInitializer;return Rl(i)||(i=sr(i)),(u=this.options_)!=null&&u.bound&&f(function(){var h=this,p=h[s].bind(h);p.isMobXFlow=!0,h[s]=p}),i}function CO(i,l,u,s){var f=l.annotationType_,h=s.value;mt(h)||I("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' can only be used on properties with a generator function value."))}function ny(i,l,u,s,f,h){h===void 0&&(h=B.safeDescriptors),CO(i,l,u,s);var p=s.value;if(Rl(p)||(p=sr(p)),f){var b;p=p.bind((b=i.proxy_)!=null?b:i.target_),p.isMobXFlow=!0}return{value:p,configurable:h?i.isPlainObject_:!0,enumerable:!1,writable:!h}}function fd(i,l){return{annotationType_:i,options_:l,make_:MO,extend_:jO,decorate_20223_:UO}}function MO(i,l,u){return this.extend_(i,l,u,!1)===null?0:1}function jO(i,l,u,s){return kO(i,this,l,u),i.defineComputedProperty_(l,La({},this.options_,{get:u.get,set:u.set}),s)}function UO(i,l){yu(l,["getter"]);var u=this,s=l.name,f=l.addInitializer;return f(function(){var h=hi(this)[le],p=La({},u.options_,{get:i,context:this});p.name||(p.name=h.name_+"."+s.toString()),h.values_.set(s,new An(p))}),function(){return this[le].getObservablePropValue_(s)}}function kO(i,l,u,s){var f=l.annotationType_,h=s.get;h||I("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' can only be used on getter(+setter) properties."))}function Su(i,l){return{annotationType_:i,options_:l,make_:NO,extend_:HO,decorate_20223_:LO}}function NO(i,l,u){return this.extend_(i,l,u,!1)===null?0:1}function HO(i,l,u,s){var f,h;return BO(i,this,l,u),i.defineObservableProperty_(l,u.value,(f=(h=this.options_)==null?void 0:h.enhancer)!=null?f:li,s)}function LO(i,l){{if(l.kind==="field")throw I("Please use `@observable accessor "+String(l.name)+"` instead of `@observable "+String(l.name)+"`");yu(l,["accessor"])}var u=this,s=l.kind,f=l.name,h=new WeakSet;function p(b,y){var z,D,x=hi(b)[le],O=new ui(y,(z=(D=u.options_)==null?void 0:D.enhancer)!=null?z:li,x.name_+"."+f.toString(),!1);x.values_.set(f,O),h.add(b)}if(s=="accessor")return{get:function(){return h.has(this)||p(this,i.get.call(this)),this[le].getObservablePropValue_(f)},set:function(y){return h.has(this)||p(this,y),this[le].setObservablePropValue_(f,y)},init:function(y){return h.has(this)||p(this,y),y}}}function BO(i,l,u,s){var f=l.annotationType_;"value"in s||I("Cannot apply '"+f+"' to '"+i.name_+"."+u.toString()+"':"+(`
'`+f+"' cannot be used on getter/setter properties"))}var VO="true",$O=ay();function ay(i){return{annotationType_:VO,options_:i,make_:PO,extend_:qO,decorate_20223_:GO}}function PO(i,l,u,s){var f,h;if(u.get)return Eu.make_(i,l,u,s);if(u.set){var p=si(l.toString(),u.set);return s===i.target_?i.defineProperty_(l,{configurable:B.safeDescriptors?i.isPlainObject_:!0,set:p})===null?0:2:(va(s,l,{configurable:!0,set:p}),2)}if(s!==i.target_&&typeof u.value=="function"){var b;if(Iv(u.value)){var y,z=(y=this.options_)!=null&&y.autoBind?sr.bound:sr;return z.make_(i,l,u,s)}var D=(b=this.options_)!=null&&b.autoBind?Al.bound:Al;return D.make_(i,l,u,s)}var x=((f=this.options_)==null?void 0:f.deep)===!1?Et.ref:Et;if(typeof u.value=="function"&&(h=this.options_)!=null&&h.autoBind){var O;u.value=u.value.bind((O=i.proxy_)!=null?O:i.target_)}return x.make_(i,l,u,s)}function qO(i,l,u,s){var f,h;if(u.get)return Eu.extend_(i,l,u,s);if(u.set)return i.defineProperty_(l,{configurable:B.safeDescriptors?i.isPlainObject_:!0,set:si(l.toString(),u.set)},s);if(typeof u.value=="function"&&(f=this.options_)!=null&&f.autoBind){var p;u.value=u.value.bind((p=i.proxy_)!=null?p:i.target_)}var b=((h=this.options_)==null?void 0:h.deep)===!1?Et.ref:Et;return b.extend_(i,l,u,s)}function GO(i,l){I("'"+this.annotationType_+"' cannot be used as a decorator")}var YO="observable",XO="observable.ref",IO="observable.shallow",QO="observable.struct",oy={deep:!0,name:void 0,defaultDecorator:void 0,proxy:!0};Object.freeze(oy);function Tu(i){return i||oy}var dd=Su(YO),ZO=Su(XO,{enhancer:bu}),KO=Su(IO,{enhancer:SO}),JO=Su(QO,{enhancer:TO}),iy=ya(dd);function Ou(i){return i.deep===!0?li:i.deep===!1?bu:FO(i.defaultDecorator)}function WO(i){var l;return i?(l=i.defaultDecorator)!=null?l:ay(i):void 0}function FO(i){var l,u;return i&&(l=(u=i.options_)==null?void 0:u.enhancer)!=null?l:li}function ry(i,l,u){if(El(l))return dd.decorate_20223_(i,l);if(ii(l)){Ol(i,l,dd);return}return zl(i)?i:ln(i)?Et.object(i,l,u):Array.isArray(i)?Et.array(i,l):nr(i)?Et.map(i,l):Ha(i)?Et.set(i,l):typeof i=="object"&&i!==null?i:Et.box(i,l)}Gv(ry,iy);var eE={box:function(l,u){var s=Tu(u);return new ui(l,Ou(s),s.name,!0,s.equals)},array:function(l,u){var s=Tu(u);return(B.useProxies===!1||s.proxy===!1?FE:qE)(l,Ou(s),s.name)},map:function(l,u){var s=Tu(u);return new Ny(l,Ou(s),s.name)},set:function(l,u){var s=Tu(u);return new Ly(l,Ou(s),s.name)},object:function(l,u,s){return go(function(){return zy(B.useProxies===!1||s?.proxy===!1?hi({},s):BE({},s),l,u)})},ref:ya(ZO),shallow:ya(KO),deep:iy,struct:ya(JO)},Et=Gv(ry,eE),ly="computed",tE="computed.struct",hd=fd(ly),nE=fd(tE,{equals:gu.structural}),Eu=function(l,u){if(El(u))return hd.decorate_20223_(l,u);if(ii(u))return Ol(l,u,hd);if(ln(l))return ya(fd(ly,l));mt(l)||I("First argument to `computed` should be an expression."),mt(u)&&I("A setter as second argument is no longer supported, use `{ set: fn }` option instead");var s=ln(u)?u:{};return s.get=l,s.name||(s.name=l.name||""),new An(s)};Object.assign(Eu,hd),Eu.struct=ya(nE);var sy,uy,wu=0,aE=1,oE=(sy=(uy=hu(function(){},"name"))==null?void 0:uy.configurable)!=null?sy:!1,cy={value:"action",configurable:!0,writable:!1,enumerable:!1};function si(i,l,u,s){u===void 0&&(u=!1),mt(l)||I("`action` can only be invoked on functions"),(typeof i!="string"||!i)&&I("actions should have valid names, got: '"+i+"'");function f(){return fy(i,u,l,s||this,arguments)}return f.isMobxAction=!0,f.toString=function(){return l.toString()},oE&&(cy.value=i,va(f,"name",cy)),f}function fy(i,l,u,s,f){var h=iE(i,l,s,f);try{return u.apply(s,f)}catch(p){throw h.error_=p,p}finally{rE(h)}}function iE(i,l,u,s){var f=wt()&&!!i,h=0;if(f){h=Date.now();var p=s?Array.from(s):mu;cn({type:_d,name:i,object:u,arguments:p})}var b=B.trackingDerivation,y=!l||!b;zn();var z=B.allowStateChanges;y&&(ci(),z=pd(!0));var D=gd(!0),x={runAsAction_:y,prevDerivation_:b,prevAllowStateChanges_:z,prevAllowStateReads_:D,notifySpy_:f,startTime_:h,actionId_:aE++,parentActionId_:wu};return wu=x.actionId_,x}function rE(i){wu!==i.actionId_&&I(30),wu=i.parentActionId_,i.error_!==void 0&&(B.suppressReactionErrors=!0),md(i.prevAllowStateChanges_),xl(i.prevAllowStateReads_),Dn(),i.runAsAction_&&Ba(i.prevDerivation_),i.notifySpy_&&fn({time:Date.now()-i.startTime_}),B.suppressReactionErrors=!1}function pd(i){var l=B.allowStateChanges;return B.allowStateChanges=i,l}function md(i){B.allowStateChanges=i}var lE="create",ui=function(i){function l(s,f,h,p,b){var y;return h===void 0&&(h="ObservableValue@"+ea()),p===void 0&&(p=!0),b===void 0&&(b=gu.default),y=i.call(this,h)||this,y.enhancer=void 0,y.name_=void 0,y.equals=void 0,y.hasUnreportedChange_=!1,y.interceptors_=void 0,y.changeListeners_=void 0,y.value_=void 0,y.dehancer=void 0,y.enhancer=f,y.name_=h,y.equals=b,y.value_=f(s,void 0,h),p&&wt()&&fi({type:lE,object:y,observableKind:"value",debugObjectName:y.name_,newValue:""+y.value_}),y}Wv(l,i);var u=l.prototype;return u.dehanceValue=function(f){return this.dehancer!==void 0?this.dehancer(f):f},u.set=function(f){var h=this.value_;if(f=this.prepareNewValue_(f),f!==B.UNCHANGED){var p=wt();p&&cn({type:aa,object:this,observableKind:"value",debugObjectName:this.name_,newValue:f,oldValue:h}),this.setNewValue_(f),p&&fn()}},u.prepareNewValue_=function(f){if(ga(this),Cn(this)){var h=Mn(this,{object:this,type:aa,newValue:f});if(!h)return B.UNCHANGED;f=h.newValue}return f=this.enhancer(f,this.value_,this.name_),this.equals(this.value_,f)?B.UNCHANGED:f},u.setNewValue_=function(f){var h=this.value_;this.value_=f,this.reportChanged(),ta(this)&&na(this,{type:aa,object:this,newValue:f,oldValue:h})},u.get=function(){return this.reportObserved(),this.dehanceValue(this.value_)},u.intercept_=function(f){return Dl(this,f)},u.observe_=function(f,h){return h&&f({observableKind:"value",debugObjectName:this.name_,object:this,type:aa,newValue:this.value_,oldValue:void 0}),Cl(this,f)},u.raw=function(){return this.value_},u.toJSON=function(){return this.get()},u.toString=function(){return this.name_+"["+this.value_+"]"},u.valueOf=function(){return Kv(this.get())},u[Symbol.toPrimitive]=function(){return this.valueOf()},l}(mo),An=function(){function i(u){this.dependenciesState_=ze.NOT_TRACKING_,this.observing_=[],this.newObserving_=null,this.observers_=new Set,this.runId_=0,this.lastAccessedBy_=0,this.lowestObserverState_=ze.UP_TO_DATE_,this.unboundDepsCount_=0,this.value_=new Au(null),this.name_=void 0,this.triggeredBy_=void 0,this.flags_=0,this.derivation=void 0,this.setter_=void 0,this.isTracing_=Rn.NONE,this.scope_=void 0,this.equals_=void 0,this.requiresReaction_=void 0,this.keepAlive_=void 0,this.onBOL=void 0,this.onBUOL=void 0,u.get||I(31),this.derivation=u.get,this.name_=u.name||"ComputedValue@"+ea(),u.set&&(this.setter_=si(this.name_+"-setter",u.set)),this.equals_=u.equals||(u.compareStructural||u.struct?gu.structural:gu.default),this.scope_=u.context,this.requiresReaction_=u.requiresReaction,this.keepAlive_=!!u.keepAlive}var l=i.prototype;return l.onBecomeStale_=function(){pE(this)},l.onBO=function(){this.onBOL&&this.onBOL.forEach(function(s){return s()})},l.onBUO=function(){this.onBUOL&&this.onBUOL.forEach(function(s){return s()})},l.get=function(){if(this.isComputing&&I(32,this.name_,this.derivation),B.inBatch===0&&this.observers_.size===0&&!this.keepAlive_)vd(this)&&(this.warnAboutUntrackedRead_(),zn(),this.value_=this.computeValue_(!1),Dn());else if(gy(this),vd(this)){var s=B.trackingContext;this.keepAlive_&&!s&&(B.trackingContext=this),this.trackAndCompute()&&hE(this),B.trackingContext=s}var f=this.value_;if(Ru(f))throw f.cause;return f},l.set=function(s){if(this.setter_){this.isRunningSetter&&I(33,this.name_),this.isRunningSetter=!0;try{this.setter_.call(this.scope_,s)}finally{this.isRunningSetter=!1}}else I(34,this.name_)},l.trackAndCompute=function(){var s=this.value_,f=this.dependenciesState_===ze.NOT_TRACKING_,h=this.computeValue_(!0),p=f||Ru(s)||Ru(h)||!this.equals_(s,h);return p&&(this.value_=h,wt()&&fi({observableKind:"computed",debugObjectName:this.name_,object:this.scope_,type:"update",oldValue:s,newValue:h})),p},l.computeValue_=function(s){this.isComputing=!0;var f=pd(!1),h;if(s)h=dy(this,this.derivation,this.scope_);else if(B.disableErrorBoundaries===!0)h=this.derivation.call(this.scope_);else try{h=this.derivation.call(this.scope_)}catch(p){h=new Au(p)}return md(f),this.isComputing=!1,h},l.suspend_=function(){this.keepAlive_||(yd(this),this.value_=void 0,this.isTracing_!==Rn.NONE&&console.log("[mobx.trace] Computed value '"+this.name_+"' was suspended and it will recompute on the next access."))},l.observe_=function(s,f){var h=this,p=!0,b=void 0;return EE(function(){var y=h.get();if(!p||f){var z=ci();s({observableKind:"computed",debugObjectName:h.name_,type:aa,object:h,newValue:y,oldValue:b}),Ba(z)}p=!1,b=y})},l.warnAboutUntrackedRead_=function(){this.isTracing_!==Rn.NONE&&console.log("[mobx.trace] Computed value '"+this.name_+"' is being read outside a reactive context. Doing a full recompute."),(typeof this.requiresReaction_=="boolean"?this.requiresReaction_:B.computedRequiresReaction)&&console.warn("[mobx] Computed value '"+this.name_+"' is being read outside a reactive context. Doing a full recompute.")},l.toString=function(){return this.name_+"["+this.derivation.toString()+"]"},l.valueOf=function(){return Kv(this.get())},l[Symbol.toPrimitive]=function(){return this.valueOf()},or(i,[{key:"isComputing",get:function(){return sn(this.flags_,i.isComputingMask_)},set:function(s){this.flags_=un(this.flags_,i.isComputingMask_,s)}},{key:"isRunningSetter",get:function(){return sn(this.flags_,i.isRunningSetterMask_)},set:function(s){this.flags_=un(this.flags_,i.isRunningSetterMask_,s)}},{key:"isBeingObserved",get:function(){return sn(this.flags_,i.isBeingObservedMask_)},set:function(s){this.flags_=un(this.flags_,i.isBeingObservedMask_,s)}},{key:"isPendingUnobservation",get:function(){return sn(this.flags_,i.isPendingUnobservationMask_)},set:function(s){this.flags_=un(this.flags_,i.isPendingUnobservationMask_,s)}},{key:"diffValue",get:function(){return sn(this.flags_,i.diffValueMask_)?1:0},set:function(s){this.flags_=un(this.flags_,i.diffValueMask_,s===1)}}])}();An.isComputingMask_=1,An.isRunningSetterMask_=2,An.isBeingObservedMask_=4,An.isPendingUnobservationMask_=8,An.diffValueMask_=16;var xu=ri("ComputedValue",An),ze;(function(i){i[i.NOT_TRACKING_=-1]="NOT_TRACKING_",i[i.UP_TO_DATE_=0]="UP_TO_DATE_",i[i.POSSIBLY_STALE_=1]="POSSIBLY_STALE_",i[i.STALE_=2]="STALE_"})(ze||(ze={}));var Rn;(function(i){i[i.NONE=0]="NONE",i[i.LOG=1]="LOG",i[i.BREAK=2]="BREAK"})(Rn||(Rn={}));var Au=function(l){this.cause=void 0,this.cause=l};function Ru(i){return i instanceof Au}function vd(i){switch(i.dependenciesState_){case ze.UP_TO_DATE_:return!1;case ze.NOT_TRACKING_:case ze.STALE_:return!0;case ze.POSSIBLY_STALE_:{for(var l=gd(!0),u=ci(),s=i.observing_,f=s.length,h=0;h<f;h++){var p=s[h];if(xu(p)){if(B.disableErrorBoundaries)p.get();else try{p.get()}catch{return Ba(u),xl(l),!0}if(i.dependenciesState_===ze.STALE_)return Ba(u),xl(l),!0}}return py(i),Ba(u),xl(l),!1}}}function ga(i){var l=i.observers_.size>0;!B.allowStateChanges&&(l||B.enforceActions==="always")&&console.warn("[MobX] "+(B.enforceActions?"Since strict-mode is enabled, changing (observed) observable values without using an action is not allowed. Tried to modify: ":"Side effects like changing state are not allowed at this point. Are you trying to modify state from, for example, a computed value or the render function of a React component? You can wrap side effects in 'runInAction' (or decorate functions with 'action') if needed. Tried to modify: ")+i.name_)}function sE(i){!B.allowStateReads&&B.observableRequiresReaction&&console.warn("[mobx] Observable '"+i.name_+"' being read outside a reactive context.")}function dy(i,l,u){var s=gd(!0);py(i),i.newObserving_=new Array(i.runId_===0?100:i.observing_.length),i.unboundDepsCount_=0,i.runId_=++B.runId;var f=B.trackingDerivation;B.trackingDerivation=i,B.inBatch++;var h;if(B.disableErrorBoundaries===!0)h=l.call(u);else try{h=l.call(u)}catch(p){h=new Au(p)}return B.inBatch--,B.trackingDerivation=f,cE(i),uE(i),xl(s),h}function uE(i){i.observing_.length===0&&(typeof i.requiresObservable_=="boolean"?i.requiresObservable_:B.reactionRequiresObservable)&&console.warn("[mobx] Derivation '"+i.name_+"' is created/updated without reading any observable value.")}function cE(i){for(var l=i.observing_,u=i.observing_=i.newObserving_,s=ze.UP_TO_DATE_,f=0,h=i.unboundDepsCount_,p=0;p<h;p++){var b=u[p];b.diffValue===0&&(b.diffValue=1,f!==p&&(u[f]=b),f++),b.dependenciesState_>s&&(s=b.dependenciesState_)}for(u.length=f,i.newObserving_=null,h=l.length;h--;){var y=l[h];y.diffValue===0&&vy(y,i),y.diffValue=0}for(;f--;){var z=u[f];z.diffValue===1&&(z.diffValue=0,dE(z,i))}s!==ze.UP_TO_DATE_&&(i.dependenciesState_=s,i.onBecomeStale_())}function yd(i){var l=i.observing_;i.observing_=[];for(var u=l.length;u--;)vy(l[u],i);i.dependenciesState_=ze.NOT_TRACKING_}function hy(i){var l=ci();try{return i()}finally{Ba(l)}}function ci(){var i=B.trackingDerivation;return B.trackingDerivation=null,i}function Ba(i){B.trackingDerivation=i}function gd(i){var l=B.allowStateReads;return B.allowStateReads=i,l}function xl(i){B.allowStateReads=i}function py(i){if(i.dependenciesState_!==ze.UP_TO_DATE_){i.dependenciesState_=ze.UP_TO_DATE_;for(var l=i.observing_,u=l.length;u--;)l[u].lowestObserverState_=ze.UP_TO_DATE_}}var zu=function(){this.version=6,this.UNCHANGED={},this.trackingDerivation=null,this.trackingContext=null,this.runId=0,this.mobxGuid=0,this.inBatch=0,this.pendingUnobservations=[],this.pendingReactions=[],this.isRunningReactions=!1,this.allowStateChanges=!1,this.allowStateReads=!0,this.enforceActions=!0,this.spyListeners=[],this.globalReactionErrorHandlers=[],this.computedRequiresReaction=!1,this.reactionRequiresObservable=!1,this.observableRequiresReaction=!1,this.disableErrorBoundaries=!1,this.suppressReactionErrors=!1,this.useProxies=!0,this.verifyProxies=!1,this.safeDescriptors=!0},Du=!0,my=!1,B=function(){var i=du();return i.__mobxInstanceCount>0&&!i.__mobxGlobals&&(Du=!1),i.__mobxGlobals&&i.__mobxGlobals.version!==new zu().version&&(Du=!1),Du?i.__mobxGlobals?(i.__mobxInstanceCount+=1,i.__mobxGlobals.UNCHANGED||(i.__mobxGlobals.UNCHANGED={}),i.__mobxGlobals):(i.__mobxInstanceCount=1,i.__mobxGlobals=new zu):(setTimeout(function(){my||I(35)},1),new zu)}();function fE(){if((B.pendingReactions.length||B.inBatch||B.isRunningReactions)&&I(36),my=!0,Du){var i=du();--i.__mobxInstanceCount===0&&(i.__mobxGlobals=void 0),B=new zu}}function dE(i,l){i.observers_.add(l),i.lowestObserverState_>l.dependenciesState_&&(i.lowestObserverState_=l.dependenciesState_)}function vy(i,l){i.observers_.delete(l),i.observers_.size===0&&yy(i)}function yy(i){i.isPendingUnobservation===!1&&(i.isPendingUnobservation=!0,B.pendingUnobservations.push(i))}function zn(){B.inBatch++}function Dn(){if(--B.inBatch===0){Oy();for(var i=B.pendingUnobservations,l=0;l<i.length;l++){var u=i[l];u.isPendingUnobservation=!1,u.observers_.size===0&&(u.isBeingObserved&&(u.isBeingObserved=!1,u.onBUO()),u instanceof An&&u.suspend_())}B.pendingUnobservations=[]}}function gy(i){sE(i);var l=B.trackingDerivation;return l!==null?(l.runId_!==i.lastAccessedBy_&&(i.lastAccessedBy_=l.runId_,l.newObserving_[l.unboundDepsCount_++]=i,!i.isBeingObserved&&B.trackingContext&&(i.isBeingObserved=!0,i.onBO())),i.isBeingObserved):(i.observers_.size===0&&B.inBatch>0&&yy(i),!1)}function by(i){i.lowestObserverState_!==ze.STALE_&&(i.lowestObserverState_=ze.STALE_,i.observers_.forEach(function(l){l.dependenciesState_===ze.UP_TO_DATE_&&(l.isTracing_!==Rn.NONE&&_y(l,i),l.onBecomeStale_()),l.dependenciesState_=ze.STALE_}))}function hE(i){i.lowestObserverState_!==ze.STALE_&&(i.lowestObserverState_=ze.STALE_,i.observers_.forEach(function(l){l.dependenciesState_===ze.POSSIBLY_STALE_?(l.dependenciesState_=ze.STALE_,l.isTracing_!==Rn.NONE&&_y(l,i)):l.dependenciesState_===ze.UP_TO_DATE_&&(i.lowestObserverState_=ze.UP_TO_DATE_)}))}function pE(i){i.lowestObserverState_===ze.UP_TO_DATE_&&(i.lowestObserverState_=ze.POSSIBLY_STALE_,i.observers_.forEach(function(l){l.dependenciesState_===ze.UP_TO_DATE_&&(l.dependenciesState_=ze.POSSIBLY_STALE_,l.onBecomeStale_())}))}function _y(i,l){if(console.log("[mobx.trace] '"+i.name_+"' is invalidated due to a change in: '"+l.name_+"'"),i.isTracing_===Rn.BREAK){var u=[];Sy(Dy(i),u,1),new Function(`debugger;
/*
Tracing '`+i.name_+`'

You are entering this break point because derivation '`+i.name_+"' is being traced and '"+l.name_+`' is now forcing it to update.
Just follow the stacktrace you should now see in the devtools to see precisely what piece of your code is causing this update
The stackframe you are looking for is at least ~6-8 stack-frames up.

`+(i instanceof An?i.derivation.toString().replace(/[*]\//g,"/"):"")+`

The dependencies for this derivation are:

`+u.join(`
`)+`
*/
    `)()}}function Sy(i,l,u){if(l.length>=1e3){l.push("(and many more)");return}l.push(""+"	".repeat(u-1)+i.name),i.dependencies&&i.dependencies.forEach(function(s){return Sy(s,l,u+1)})}var Va=function(){function i(u,s,f,h){u===void 0&&(u="Reaction@"+ea()),this.name_=void 0,this.onInvalidate_=void 0,this.errorHandler_=void 0,this.requiresObservable_=void 0,this.observing_=[],this.newObserving_=[],this.dependenciesState_=ze.NOT_TRACKING_,this.runId_=0,this.unboundDepsCount_=0,this.flags_=0,this.isTracing_=Rn.NONE,this.name_=u,this.onInvalidate_=s,this.errorHandler_=f,this.requiresObservable_=h}var l=i.prototype;return l.onBecomeStale_=function(){this.schedule_()},l.schedule_=function(){this.isScheduled||(this.isScheduled=!0,B.pendingReactions.push(this),Oy())},l.runReaction_=function(){if(!this.isDisposed){zn(),this.isScheduled=!1;var s=B.trackingContext;if(B.trackingContext=this,vd(this)){this.isTrackPending=!0;try{this.onInvalidate_(),Qt.NODE_ENV!=="production"&&this.isTrackPending&&wt()&&fi({name:this.name_,type:"scheduled-reaction"})}catch(f){this.reportExceptionInDerivation_(f)}}B.trackingContext=s,Dn()}},l.track=function(s){if(!this.isDisposed){zn();var f=wt(),h;f&&(h=Date.now(),cn({name:this.name_,type:"reaction"})),this.isRunning=!0;var p=B.trackingContext;B.trackingContext=this;var b=dy(this,s,void 0);B.trackingContext=p,this.isRunning=!1,this.isTrackPending=!1,this.isDisposed&&yd(this),Ru(b)&&this.reportExceptionInDerivation_(b.cause),f&&fn({time:Date.now()-h}),Dn()}},l.reportExceptionInDerivation_=function(s){var f=this;if(this.errorHandler_){this.errorHandler_(s,this);return}if(B.disableErrorBoundaries)throw s;var h="[mobx] Encountered an uncaught exception that was thrown by a reaction or observer component, in: '"+this+"'";B.suppressReactionErrors?console.warn("[mobx] (error in reaction '"+this.name_+"' suppressed, fix error of causing action below)"):console.error(h,s),wt()&&fi({type:"error",name:this.name_,message:h,error:""+s}),B.globalReactionErrorHandlers.forEach(function(p){return p(s,f)})},l.dispose=function(){this.isDisposed||(this.isDisposed=!0,this.isRunning||(zn(),yd(this),Dn()))},l.getDisposer_=function(s){var f=this,h=function p(){f.dispose(),s==null||s.removeEventListener==null||s.removeEventListener("abort",p)};return s==null||s.addEventListener==null||s.addEventListener("abort",h),h[le]=this,h},l.toString=function(){return"Reaction["+this.name_+"]"},l.trace=function(s){s===void 0&&(s=!1),NE(this,s)},or(i,[{key:"isDisposed",get:function(){return sn(this.flags_,i.isDisposedMask_)},set:function(s){this.flags_=un(this.flags_,i.isDisposedMask_,s)}},{key:"isScheduled",get:function(){return sn(this.flags_,i.isScheduledMask_)},set:function(s){this.flags_=un(this.flags_,i.isScheduledMask_,s)}},{key:"isTrackPending",get:function(){return sn(this.flags_,i.isTrackPendingMask_)},set:function(s){this.flags_=un(this.flags_,i.isTrackPendingMask_,s)}},{key:"isRunning",get:function(){return sn(this.flags_,i.isRunningMask_)},set:function(s){this.flags_=un(this.flags_,i.isRunningMask_,s)}},{key:"diffValue",get:function(){return sn(this.flags_,i.diffValueMask_)?1:0},set:function(s){this.flags_=un(this.flags_,i.diffValueMask_,s===1)}}])}();Va.isDisposedMask_=1,Va.isScheduledMask_=2,Va.isTrackPendingMask_=4,Va.isRunningMask_=8,Va.diffValueMask_=16;var Ty=100,bd=function(l){return l()};function Oy(){B.inBatch>0||B.isRunningReactions||bd(mE)}function mE(){B.isRunningReactions=!0;for(var i=B.pendingReactions,l=0;i.length>0;){++l===Ty&&(console.error("Reaction doesn't converge to a stable state after "+Ty+" iterations."+(" Probably there is a cycle in the reactive function: "+i[0])),i.splice(0));for(var u=i.splice(0),s=0,f=u.length;s<f;s++)u[s].runReaction_()}B.isRunningReactions=!1}var Cu=ri("Reaction",Va);function vE(i){var l=bd;bd=function(s){return i(function(){return l(s)})}}function wt(){return!!B.spyListeners.length}function fi(i){if(B.spyListeners.length)for(var l=B.spyListeners,u=0,s=l.length;u<s;u++)l[u](i)}function cn(i){var l=La({},i,{spyReportStart:!0});fi(l)}var yE={type:"report-end",spyReportEnd:!0};function fn(i){fi(i?La({},i,{type:"report-end",spyReportEnd:!0}):yE)}function gE(i){return B.spyListeners.push(i),ld(function(){B.spyListeners=B.spyListeners.filter(function(l){return l!==i})})}var _d="action",bE="action.bound",Ey="autoAction",_E="autoAction.bound",wy="<unnamed action>",Sd=wl(_d),SE=wl(bE,{bound:!0}),Td=wl(Ey,{autoAction:!0}),TE=wl(_E,{autoAction:!0,bound:!0});function xy(i){var l=function(s,f){if(mt(s))return si(s.name||wy,s,i);if(mt(f))return si(s,f,i);if(El(f))return(i?Td:Sd).decorate_20223_(s,f);if(ii(f))return Ol(s,f,i?Td:Sd);if(ii(s))return ya(wl(i?Ey:_d,{name:s,autoAction:i}));I("Invalid arguments for `action`")};return l}var rr=xy(!1);Object.assign(rr,Sd);var Al=xy(!0);Object.assign(Al,Td),rr.bound=ya(SE),Al.bound=ya(TE);function OE(i){return fy(i.name||wy,!1,i,this,void 0)}function lr(i){return mt(i)&&i.isMobxAction===!0}function EE(i,l){var u,s,f,h;l===void 0&&(l=Yv),mt(i)||I("Autorun expects a function as first argument"),lr(i)&&I("Autorun does not accept actions since actions are untrackable");var p=(u=(s=l)==null?void 0:s.name)!=null?u:i.name||"Autorun@"+ea(),b=!l.scheduler&&!l.delay,y;if(b)y=new Va(p,function(){this.track(x)},l.onError,l.requiresObservable);else{var z=xE(l),D=!1;y=new Va(p,function(){D||(D=!0,z(function(){D=!1,y.isDisposed||y.track(x)}))},l.onError,l.requiresObservable)}function x(){i(y)}return(f=l)!=null&&(f=f.signal)!=null&&f.aborted||y.schedule_(),y.getDisposer_((h=l)==null?void 0:h.signal)}var wE=function(l){return l()};function xE(i){return i.scheduler?i.scheduler:i.delay?function(l){return setTimeout(l,i.delay)}:wE}var AE="onBO",RE="onBUO";function zE(i,l,u){return Ry(AE,i,l,u)}function Ay(i,l,u){return Ry(RE,i,l,u)}function Ry(i,l,u,s){var f=cr(l),h=mt(s)?s:u,p=i+"L";return f[p]?f[p].add(h):f[p]=new Set([h]),function(){var b=f[p];b&&(b.delete(h),b.size===0&&delete f[p])}}var DE="never",Mu="always",CE="observed";function st(i){i.isolateGlobalState===!0&&fE();var l=i.useProxies,u=i.enforceActions;if(l!==void 0&&(B.useProxies=l===Mu?!0:l===DE?!1:typeof Proxy<"u"),l==="ifavailable"&&(B.verifyProxies=!0),u!==void 0){var s=u===Mu?Mu:u===CE;B.enforceActions=s,B.allowStateChanges=!(s===!0||s===Mu)}["computedRequiresReaction","reactionRequiresObservable","observableRequiresReaction","disableErrorBoundaries","safeDescriptors"].forEach(function(f){f in i&&(B[f]=!!i[f])}),B.allowStateReads=!B.observableRequiresReaction,B.disableErrorBoundaries===!0&&console.warn("WARNING: Debug feature only. MobX will NOT recover from errors when `disableErrorBoundaries` is enabled."),i.reactionScheduler&&vE(i.reactionScheduler)}function zy(i,l,u,s){arguments.length>4&&I("'extendObservable' expected 2-4 arguments"),typeof i!="object"&&I("'extendObservable' expects an object as first argument"),yo(i)&&I("'extendObservable' should not be used on maps, use map.merge instead"),ln(l)||I("'extendObservable' only accepts plain objects as second argument"),(zl(l)||zl(u))&&I("Extending an object with another observable (object) is not supported");var f=dO(l);return go(function(){var h=hi(i,s)[le];ar(f).forEach(function(p){h.extend_(p,f[p],u&&p in u?u[p]:!0)})}),i}function Dy(i,l){return Cy(cr(i,l))}function Cy(i){var l={name:i.name_};return i.observing_&&i.observing_.length>0&&(l.dependencies=ME(i.observing_).map(Cy)),l}function ME(i){return Array.from(new Set(i))}var jE=0;function My(){this.message="FLOW_CANCELLED"}My.prototype=Object.create(Error.prototype);var Od=ty("flow"),UE=ty("flow.bound",{bound:!0}),sr=Object.assign(function(l,u){if(El(u))return Od.decorate_20223_(l,u);if(ii(u))return Ol(l,u,Od);arguments.length!==1&&I("Flow expects single argument with generator function");var s=l,f=s.name||"<unnamed flow>",h=function(){var b=this,y=arguments,z=++jE,D=rr(f+" - runid: "+z+" - init",s).apply(b,y),x,O=void 0,H=new Promise(function(Y,F){var ne=0;x=F;function ie(ke){O=void 0;var Ze;try{Ze=rr(f+" - runid: "+z+" - yield "+ne++,D.next).call(D,ke)}catch(Fe){return F(Fe)}Pe(Ze)}function Qe(ke){O=void 0;var Ze;try{Ze=rr(f+" - runid: "+z+" - yield "+ne++,D.throw).call(D,ke)}catch(Fe){return F(Fe)}Pe(Ze)}function Pe(ke){if(mt(ke?.then)){ke.then(Pe,F);return}return ke.done?Y(ke.value):(O=Promise.resolve(ke.value),O.then(ie,Qe))}ie(void 0)});return H.cancel=rr(f+" - runid: "+z+" - cancel",function(){try{O&&jy(O);var Y=D.return(void 0),F=Promise.resolve(Y.value);F.then(tr,tr),jy(F),x(new My)}catch(ne){x(ne)}}),H};return h.isMobXFlow=!0,h},Od);sr.bound=ya(UE);function jy(i){mt(i.cancel)&&i.cancel()}function Rl(i){return i?.isMobXFlow===!0}function kE(i,l){return i?ur(i)||!!i[le]||cd(i)||Cu(i)||xu(i):!1}function zl(i){return arguments.length!==1&&I("isObservable expects only 1 argument. Use isObservableProp to inspect the observability of a property"),kE(i)}function NE(){for(var i=!1,l=arguments.length,u=new Array(l),s=0;s<l;s++)u[s]=arguments[s];typeof u[u.length-1]=="boolean"&&(i=u.pop());var f=HE(u);if(!f)return I("'trace(break?)' can only be used inside a tracked computed value or a Reaction. Consider passing in the computed value or reaction explicitly");f.isTracing_===Rn.NONE&&console.log("[mobx.trace] '"+f.name_+"' tracing enabled"),f.isTracing_=i?Rn.BREAK:Rn.LOG}function HE(i){switch(i.length){case 0:return B.trackingDerivation;case 1:return cr(i[0]);case 2:return cr(i[0],i[1])}}function $a(i,l){l===void 0&&(l=void 0),zn();try{return i.apply(l)}finally{Dn()}}function di(i){return i[le]}var LE={has:function(l,u){return B.trackingDerivation&&Sl("detect new properties using the 'in' operator. Use 'has' from 'mobx' instead."),di(l).has_(u)},get:function(l,u){return di(l).get_(u)},set:function(l,u,s){var f;return ii(u)?(di(l).values_.has(u)||Sl("add a new observable property through direct assignment. Use 'set' from 'mobx' instead."),(f=di(l).set_(u,s,!0))!=null?f:!0):!1},deleteProperty:function(l,u){var s;return Sl("delete properties from an observable object. Use 'remove' from 'mobx' instead."),ii(u)?(s=di(l).delete_(u,!0))!=null?s:!0:!1},defineProperty:function(l,u,s){var f;return Sl("define property on an observable object. Use 'defineProperty' from 'mobx' instead."),(f=di(l).defineProperty_(u,s))!=null?f:!0},ownKeys:function(l){return B.trackingDerivation&&Sl("iterate keys to detect added / removed properties. Use 'keys' from 'mobx' instead."),di(l).ownKeys_()},preventExtensions:function(l){I(13)}};function BE(i,l){var u,s;return Xv(),i=hi(i,l),(s=(u=i[le]).proxy_)!=null?s:u.proxy_=new Proxy(i,LE)}function Cn(i){return i.interceptors_!==void 0&&i.interceptors_.length>0}function Dl(i,l){var u=i.interceptors_||(i.interceptors_=[]);return u.push(l),ld(function(){var s=u.indexOf(l);s!==-1&&u.splice(s,1)})}function Mn(i,l){var u=ci();try{for(var s=[].concat(i.interceptors_||[]),f=0,h=s.length;f<h&&(l=s[f](l),l&&!l.type&&I(14),!!l);f++);return l}finally{Ba(u)}}function ta(i){return i.changeListeners_!==void 0&&i.changeListeners_.length>0}function Cl(i,l){var u=i.changeListeners_||(i.changeListeners_=[]);return u.push(l),ld(function(){var s=u.indexOf(l);s!==-1&&u.splice(s,1)})}function na(i,l){var u=ci(),s=i.changeListeners_;if(s){s=s.slice();for(var f=0,h=s.length;f<h;f++)s[f](l);Ba(u)}}function VE(i,l,u){return go(function(){var s,f=hi(i,u)[le];Qt.NODE_ENV!=="production"&&l&&i[Zt]&&I("makeObservable second arg must be nullish when using decorators. Mixing @decorator syntax with annotations is not supported."),(s=l)!=null||(l=gO(i)),ar(l).forEach(function(h){return f.make_(h,l[h])})}),i}var Ed=Symbol("mobx-keys");function vt(i,l,u){return!ln(i)&&!ln(Object.getPrototypeOf(i))&&I("'makeAutoObservable' can only be used for classes that don't have a superclass"),ur(i)&&I("makeAutoObservable can only be used on objects not already made observable"),ln(i)?zy(i,i,l,u):(go(function(){var s=hi(i,u)[le];if(!i[Ed]){var f=Object.getPrototypeOf(i),h=new Set([].concat(ar(i),ar(f)));h.delete("constructor"),h.delete(le),Tl(f,Ed,h)}i[Ed].forEach(function(p){return s.make_(p,l&&p in l?l[p]:!0)})}),i)}var Uy="splice",aa="update",$E=1e4,PE={get:function(l,u){var s=l[le];return u===le?s:u==="length"?s.getArrayLength_():typeof u=="string"&&!isNaN(u)?s.get_(parseInt(u)):xn(ju,u)?ju[u]:l[u]},set:function(l,u,s){var f=l[le];return u==="length"&&f.setArrayLength_(s),typeof u=="symbol"||isNaN(u)?l[u]=s:f.set_(parseInt(u),s),!0},preventExtensions:function(){I(15)}},wd=function(){function i(u,s,f,h){u===void 0&&(u="ObservableArray@"+ea()),this.owned_=void 0,this.legacyMode_=void 0,this.atom_=void 0,this.values_=[],this.interceptors_=void 0,this.changeListeners_=void 0,this.enhancer_=void 0,this.dehancer=void 0,this.proxy_=void 0,this.lastKnownLength_=0,this.owned_=f,this.legacyMode_=h,this.atom_=new mo(u),this.enhancer_=function(p,b){return s(p,b,u+"[..]")}}var l=i.prototype;return l.dehanceValue_=function(s){return this.dehancer!==void 0?this.dehancer(s):s},l.dehanceValues_=function(s){return this.dehancer!==void 0&&s.length>0?s.map(this.dehancer):s},l.intercept_=function(s){return Dl(this,s)},l.observe_=function(s,f){return f===void 0&&(f=!1),f&&s({observableKind:"array",object:this.proxy_,debugObjectName:this.atom_.name_,type:"splice",index:0,added:this.values_.slice(),addedCount:this.values_.length,removed:[],removedCount:0}),Cl(this,s)},l.getArrayLength_=function(){return this.atom_.reportObserved(),this.values_.length},l.setArrayLength_=function(s){(typeof s!="number"||isNaN(s)||s<0)&&I("Out of range: "+s);var f=this.values_.length;if(s!==f)if(s>f){for(var h=new Array(s-f),p=0;p<s-f;p++)h[p]=void 0;this.spliceWithArray_(f,0,h)}else this.spliceWithArray_(s,f-s)},l.updateArrayLength_=function(s,f){s!==this.lastKnownLength_&&I(16),this.lastKnownLength_+=f,this.legacyMode_&&f>0&&Iy(s+f+1)},l.spliceWithArray_=function(s,f,h){var p=this;ga(this.atom_);var b=this.values_.length;if(s===void 0?s=0:s>b?s=b:s<0&&(s=Math.max(0,b+s)),arguments.length===1?f=b-s:f==null?f=0:f=Math.max(0,Math.min(f,b-s)),h===void 0&&(h=mu),Cn(this)){var y=Mn(this,{object:this.proxy_,type:Uy,index:s,removedCount:f,added:h});if(!y)return mu;f=y.removedCount,h=y.added}if(h=h.length===0?h:h.map(function(x){return p.enhancer_(x,void 0)}),this.legacyMode_||Qt.NODE_ENV!=="production"){var z=h.length-f;this.updateArrayLength_(b,z)}var D=this.spliceItemsIntoValues_(s,f,h);return(f!==0||h.length!==0)&&this.notifyArraySplice_(s,h,D),this.dehanceValues_(D)},l.spliceItemsIntoValues_=function(s,f,h){if(h.length<$E){var p;return(p=this.values_).splice.apply(p,[s,f].concat(h))}else{var b=this.values_.slice(s,s+f),y=this.values_.slice(s+f);this.values_.length+=h.length-f;for(var z=0;z<h.length;z++)this.values_[s+z]=h[z];for(var D=0;D<y.length;D++)this.values_[s+h.length+D]=y[D];return b}},l.notifyArrayChildUpdate_=function(s,f,h){var p=!this.owned_&&wt(),b=ta(this),y=b||p?{observableKind:"array",object:this.proxy_,type:aa,debugObjectName:this.atom_.name_,index:s,newValue:f,oldValue:h}:null;p&&cn(y),this.atom_.reportChanged(),b&&na(this,y),p&&fn()},l.notifyArraySplice_=function(s,f,h){var p=!this.owned_&&wt(),b=ta(this),y=b||p?{observableKind:"array",object:this.proxy_,debugObjectName:this.atom_.name_,type:Uy,index:s,removed:h,added:f,removedCount:h.length,addedCount:f.length}:null;p&&cn(y),this.atom_.reportChanged(),b&&na(this,y),p&&fn()},l.get_=function(s){if(this.legacyMode_&&s>=this.values_.length){console.warn("[mobx.array] Attempt to read an array index ("+s+") that is out of bounds ("+this.values_.length+"). Please check length first. Out of bound indices will not be tracked by MobX");return}return this.atom_.reportObserved(),this.dehanceValue_(this.values_[s])},l.set_=function(s,f){var h=this.values_;if(this.legacyMode_&&s>h.length&&I(17,s,h.length),s<h.length){ga(this.atom_);var p=h[s];if(Cn(this)){var b=Mn(this,{type:aa,object:this.proxy_,index:s,newValue:f});if(!b)return;f=b.newValue}f=this.enhancer_(f,p);var y=f!==p;y&&(h[s]=f,this.notifyArrayChildUpdate_(s,f,p))}else{for(var z=new Array(s+1-h.length),D=0;D<z.length-1;D++)z[D]=void 0;z[z.length-1]=f,this.spliceWithArray_(h.length,0,z)}},i}();function qE(i,l,u,s){return u===void 0&&(u="ObservableArray@"+ea()),s===void 0&&(s=!1),Xv(),go(function(){var f=new wd(u,l,s,!1);Qv(f.values_,le,f);var h=new Proxy(f.values_,PE);return f.proxy_=h,i&&i.length&&f.spliceWithArray_(0,0,i),h})}var ju={clear:function(){return this.splice(0)},replace:function(l){var u=this[le];return u.spliceWithArray_(0,u.values_.length,l)},toJSON:function(){return this.slice()},splice:function(l,u){for(var s=arguments.length,f=new Array(s>2?s-2:0),h=2;h<s;h++)f[h-2]=arguments[h];var p=this[le];switch(arguments.length){case 0:return[];case 1:return p.spliceWithArray_(l);case 2:return p.spliceWithArray_(l,u)}return p.spliceWithArray_(l,u,f)},spliceWithArray:function(l,u,s){return this[le].spliceWithArray_(l,u,s)},push:function(){for(var l=this[le],u=arguments.length,s=new Array(u),f=0;f<u;f++)s[f]=arguments[f];return l.spliceWithArray_(l.values_.length,0,s),l.values_.length},pop:function(){return this.splice(Math.max(this[le].values_.length-1,0),1)[0]},shift:function(){return this.splice(0,1)[0]},unshift:function(){for(var l=this[le],u=arguments.length,s=new Array(u),f=0;f<u;f++)s[f]=arguments[f];return l.spliceWithArray_(0,0,s),l.values_.length},reverse:function(){return B.trackingDerivation&&I(37,"reverse"),this.replace(this.slice().reverse()),this},sort:function(){B.trackingDerivation&&I(37,"sort");var l=this.slice();return l.sort.apply(l,arguments),this.replace(l),this},remove:function(l){var u=this[le],s=u.dehanceValues_(u.values_).indexOf(l);return s>-1?(this.splice(s,1),!0):!1}};Ye("at",dn),Ye("concat",dn),Ye("flat",dn),Ye("includes",dn),Ye("indexOf",dn),Ye("join",dn),Ye("lastIndexOf",dn),Ye("slice",dn),Ye("toString",dn),Ye("toLocaleString",dn),Ye("toSorted",dn),Ye("toSpliced",dn),Ye("with",dn),Ye("every",oa),Ye("filter",oa),Ye("find",oa),Ye("findIndex",oa),Ye("findLast",oa),Ye("findLastIndex",oa),Ye("flatMap",oa),Ye("forEach",oa),Ye("map",oa),Ye("some",oa),Ye("toReversed",oa),Ye("reduce",ky),Ye("reduceRight",ky);function Ye(i,l){typeof Array.prototype[i]=="function"&&(ju[i]=l(i))}function dn(i){return function(){var l=this[le];l.atom_.reportObserved();var u=l.dehanceValues_(l.values_);return u[i].apply(u,arguments)}}function oa(i){return function(l,u){var s=this,f=this[le];f.atom_.reportObserved();var h=f.dehanceValues_(f.values_);return h[i](function(p,b){return l.call(u,p,b,s)})}}function ky(i){return function(){var l=this,u=this[le];u.atom_.reportObserved();var s=u.dehanceValues_(u.values_),f=arguments[0];return arguments[0]=function(h,p,b){return f(h,p,b,l)},s[i].apply(s,arguments)}}var GE=ri("ObservableArrayAdministration",wd);function Uu(i){return vu(i)&&GE(i[le])}var YE={},vo="add",ku="delete",Ny=function(){function i(u,s,f){var h=this;s===void 0&&(s=li),f===void 0&&(f="ObservableMap@"+ea()),this.enhancer_=void 0,this.name_=void 0,this[le]=YE,this.data_=void 0,this.hasMap_=void 0,this.keysAtom_=void 0,this.interceptors_=void 0,this.changeListeners_=void 0,this.dehancer=void 0,this.enhancer_=s,this.name_=f,mt(Map)||I(18),go(function(){h.keysAtom_=Fv(Qt.NODE_ENV!=="production"?h.name_+".keys()":"ObservableMap.keys()"),h.data_=new Map,h.hasMap_=new Map,u&&h.merge(u)})}var l=i.prototype;return l.has_=function(s){return this.data_.has(s)},l.has=function(s){var f=this;if(!B.trackingDerivation)return this.has_(s);var h=this.hasMap_.get(s);if(!h){var p=h=new ui(this.has_(s),bu,this.name_+"."+sd(s)+"?",!1);this.hasMap_.set(s,p),Ay(p,function(){return f.hasMap_.delete(s)})}return h.get()},l.set=function(s,f){var h=this.has_(s);if(Cn(this)){var p=Mn(this,{type:h?aa:vo,object:this,newValue:f,name:s});if(!p)return this;f=p.newValue}return h?this.updateValue_(s,f):this.addValue_(s,f),this},l.delete=function(s){var f=this;if(ga(this.keysAtom_),Cn(this)){var h=Mn(this,{type:ku,object:this,name:s});if(!h)return!1}if(this.has_(s)){var p=wt(),b=ta(this),y=b||p?{observableKind:"map",debugObjectName:this.name_,type:ku,object:this,oldValue:this.data_.get(s).value_,name:s}:null;return p&&cn(y),$a(function(){var z;f.keysAtom_.reportChanged(),(z=f.hasMap_.get(s))==null||z.setNewValue_(!1);var D=f.data_.get(s);D.setNewValue_(void 0),f.data_.delete(s)}),b&&na(this,y),p&&fn(),!0}return!1},l.updateValue_=function(s,f){var h=this.data_.get(s);if(f=h.prepareNewValue_(f),f!==B.UNCHANGED){var p=wt(),b=ta(this),y=b||p?{observableKind:"map",debugObjectName:this.name_,type:aa,object:this,oldValue:h.value_,name:s,newValue:f}:null;p&&cn(y),h.setNewValue_(f),b&&na(this,y),p&&fn()}},l.addValue_=function(s,f){var h=this;ga(this.keysAtom_),$a(function(){var z,D=new ui(f,h.enhancer_,h.name_+"."+sd(s),!1);h.data_.set(s,D),f=D.value_,(z=h.hasMap_.get(s))==null||z.setNewValue_(!0),h.keysAtom_.reportChanged()});var p=wt(),b=ta(this),y=b||p?{observableKind:"map",debugObjectName:this.name_,type:vo,object:this,name:s,newValue:f}:null;p&&cn(y),b&&na(this,y),p&&fn()},l.get=function(s){return this.has(s)?this.dehanceValue_(this.data_.get(s).get()):this.dehanceValue_(void 0)},l.dehanceValue_=function(s){return this.dehancer!==void 0?this.dehancer(s):s},l.keys=function(){return this.keysAtom_.reportObserved(),this.data_.keys()},l.values=function(){var s=this,f=this.keys();return Hy({next:function(){var p=f.next(),b=p.done,y=p.value;return{done:b,value:b?void 0:s.get(y)}}})},l.entries=function(){var s=this,f=this.keys();return Hy({next:function(){var p=f.next(),b=p.done,y=p.value;return{done:b,value:b?void 0:[y,s.get(y)]}}})},l[Symbol.iterator]=function(){return this.entries()},l.forEach=function(s,f){for(var h=ir(this),p;!(p=h()).done;){var b=p.value,y=b[0],z=b[1];s.call(f,z,y,this)}},l.merge=function(s){var f=this;return yo(s)&&(s=new Map(s)),$a(function(){ln(s)?fO(s).forEach(function(h){return f.set(h,s[h])}):Array.isArray(s)?s.forEach(function(h){var p=h[0],b=h[1];return f.set(p,b)}):nr(s)?(cO(s)||I(19,s),s.forEach(function(h,p){return f.set(p,h)})):s!=null&&I(20,s)}),this},l.clear=function(){var s=this;$a(function(){hy(function(){for(var f=ir(s.keys()),h;!(h=f()).done;){var p=h.value;s.delete(p)}})})},l.replace=function(s){var f=this;return $a(function(){for(var h=XE(s),p=new Map,b=!1,y=ir(f.data_.keys()),z;!(z=y()).done;){var D=z.value;if(!h.has(D)){var x=f.delete(D);if(x)b=!0;else{var O=f.data_.get(D);p.set(D,O)}}}for(var H=ir(h.entries()),Y;!(Y=H()).done;){var F=Y.value,ne=F[0],ie=F[1],Qe=f.data_.has(ne);if(f.set(ne,ie),f.data_.has(ne)){var Pe=f.data_.get(ne);p.set(ne,Pe),Qe||(b=!0)}}if(!b)if(f.data_.size!==p.size)f.keysAtom_.reportChanged();else for(var ke=f.data_.keys(),Ze=p.keys(),Fe=ke.next(),Ne=Ze.next();!Fe.done;){if(Fe.value!==Ne.value){f.keysAtom_.reportChanged();break}Fe=ke.next(),Ne=Ze.next()}f.data_=p}),this},l.toString=function(){return"[object ObservableMap]"},l.toJSON=function(){return Array.from(this)},l.observe_=function(s,f){return f===!0&&I("`observe` doesn't support fireImmediately=true in combination with maps."),Cl(this,s)},l.intercept_=function(s){return Dl(this,s)},or(i,[{key:"size",get:function(){return this.keysAtom_.reportObserved(),this.data_.size}},{key:Symbol.toStringTag,get:function(){return"Map"}}])}(),yo=ri("ObservableMap",Ny);function Hy(i){return i[Symbol.toStringTag]="MapIterator",Dd(i)}function XE(i){if(nr(i)||yo(i))return i;if(Array.isArray(i))return new Map(i);if(ln(i)){var l=new Map;for(var u in i)l.set(u,i[u]);return l}else return I(21,i)}var IE={},Ly=function(){function i(u,s,f){var h=this;s===void 0&&(s=li),f===void 0&&(f="ObservableSet@"+ea()),this.name_=void 0,this[le]=IE,this.data_=new Set,this.atom_=void 0,this.changeListeners_=void 0,this.interceptors_=void 0,this.dehancer=void 0,this.enhancer_=void 0,this.name_=f,mt(Set)||I(22),this.enhancer_=function(p,b){return s(p,b,f)},go(function(){h.atom_=Fv(h.name_),u&&h.replace(u)})}var l=i.prototype;return l.dehanceValue_=function(s){return this.dehancer!==void 0?this.dehancer(s):s},l.clear=function(){var s=this;$a(function(){hy(function(){for(var f=ir(s.data_.values()),h;!(h=f()).done;){var p=h.value;s.delete(p)}})})},l.forEach=function(s,f){for(var h=ir(this),p;!(p=h()).done;){var b=p.value;s.call(f,b,b,this)}},l.add=function(s){var f=this;if(ga(this.atom_),Cn(this)){var h=Mn(this,{type:vo,object:this,newValue:s});if(!h)return this;s=h.newValue}if(!this.has(s)){$a(function(){f.data_.add(f.enhancer_(s,void 0)),f.atom_.reportChanged()});var p=wt(),b=ta(this),y=b||p?{observableKind:"set",debugObjectName:this.name_,type:vo,object:this,newValue:s}:null;p&&Qt.NODE_ENV!=="production"&&cn(y),b&&na(this,y),p&&Qt.NODE_ENV!=="production"&&fn()}return this},l.delete=function(s){var f=this;if(Cn(this)){var h=Mn(this,{type:ku,object:this,oldValue:s});if(!h)return!1}if(this.has(s)){var p=wt(),b=ta(this),y=b||p?{observableKind:"set",debugObjectName:this.name_,type:ku,object:this,oldValue:s}:null;return p&&Qt.NODE_ENV!=="production"&&cn(y),$a(function(){f.atom_.reportChanged(),f.data_.delete(s)}),b&&na(this,y),p&&Qt.NODE_ENV!=="production"&&fn(),!0}return!1},l.has=function(s){return this.atom_.reportObserved(),this.data_.has(this.dehanceValue_(s))},l.entries=function(){var s=this.values();return By({next:function(){var h=s.next(),p=h.value,b=h.done;return b?{value:void 0,done:b}:{value:[p,p],done:b}}})},l.keys=function(){return this.values()},l.values=function(){this.atom_.reportObserved();var s=this,f=this.data_.values();return By({next:function(){var p=f.next(),b=p.value,y=p.done;return y?{value:void 0,done:y}:{value:s.dehanceValue_(b),done:y}}})},l.intersection=function(s){if(Ha(s)&&!ba(s))return s.intersection(this);var f=new Set(this);return f.intersection(s)},l.union=function(s){if(Ha(s)&&!ba(s))return s.union(this);var f=new Set(this);return f.union(s)},l.difference=function(s){return new Set(this).difference(s)},l.symmetricDifference=function(s){if(Ha(s)&&!ba(s))return s.symmetricDifference(this);var f=new Set(this);return f.symmetricDifference(s)},l.isSubsetOf=function(s){return new Set(this).isSubsetOf(s)},l.isSupersetOf=function(s){return new Set(this).isSupersetOf(s)},l.isDisjointFrom=function(s){if(Ha(s)&&!ba(s))return s.isDisjointFrom(this);var f=new Set(this);return f.isDisjointFrom(s)},l.replace=function(s){var f=this;return ba(s)&&(s=new Set(s)),$a(function(){Array.isArray(s)?(f.clear(),s.forEach(function(h){return f.add(h)})):Ha(s)?(f.clear(),s.forEach(function(h){return f.add(h)})):s!=null&&I("Cannot initialize set from "+s)}),this},l.observe_=function(s,f){return f===!0&&I("`observe` doesn't support fireImmediately=true in combination with sets."),Cl(this,s)},l.intercept_=function(s){return Dl(this,s)},l.toJSON=function(){return Array.from(this)},l.toString=function(){return"[object ObservableSet]"},l[Symbol.iterator]=function(){return this.values()},or(i,[{key:"size",get:function(){return this.atom_.reportObserved(),this.data_.size}},{key:Symbol.toStringTag,get:function(){return"Set"}}])}(),ba=ri("ObservableSet",Ly);function By(i){return i[Symbol.toStringTag]="SetIterator",Dd(i)}var Vy=Object.create(null),$y="remove",xd=function(){function i(u,s,f,h){s===void 0&&(s=new Map),h===void 0&&(h=$O),this.target_=void 0,this.values_=void 0,this.name_=void 0,this.defaultAnnotation_=void 0,this.keysAtom_=void 0,this.changeListeners_=void 0,this.interceptors_=void 0,this.proxy_=void 0,this.isPlainObject_=void 0,this.appliedAnnotations_=void 0,this.pendingKeys_=void 0,this.target_=u,this.values_=s,this.name_=f,this.defaultAnnotation_=h,this.keysAtom_=new mo(this.name_+".keys"),this.isPlainObject_=ln(this.target_),Fy(this.defaultAnnotation_)||I("defaultAnnotation must be valid annotation"),this.appliedAnnotations_={}}var l=i.prototype;return l.getObservablePropValue_=function(s){return this.values_.get(s).get()},l.setObservablePropValue_=function(s,f){var h=this.values_.get(s);if(h instanceof An)return h.set(f),!0;if(Cn(this)){var p=Mn(this,{type:aa,object:this.proxy_||this.target_,name:s,newValue:f});if(!p)return null;f=p.newValue}if(f=h.prepareNewValue_(f),f!==B.UNCHANGED){var b=ta(this),y=wt(),z=b||y?{type:aa,observableKind:"object",debugObjectName:this.name_,object:this.proxy_||this.target_,oldValue:h.value_,name:s,newValue:f}:null;y&&cn(z),h.setNewValue_(f),b&&na(this,z),y&&fn()}return!0},l.get_=function(s){return B.trackingDerivation&&!xn(this.target_,s)&&this.has_(s),this.target_[s]},l.set_=function(s,f,h){return h===void 0&&(h=!1),xn(this.target_,s)?this.values_.has(s)?this.setObservablePropValue_(s,f):h?Reflect.set(this.target_,s,f):(this.target_[s]=f,!0):this.extend_(s,{value:f,enumerable:!0,writable:!0,configurable:!0},this.defaultAnnotation_,h)},l.has_=function(s){if(!B.trackingDerivation)return s in this.target_;this.pendingKeys_||(this.pendingKeys_=new Map);var f=this.pendingKeys_.get(s);return f||(f=new ui(s in this.target_,bu,this.name_+"."+sd(s)+"?",!1),this.pendingKeys_.set(s,f)),f.get()},l.make_=function(s,f){if(f===!0&&(f=this.defaultAnnotation_),f!==!1){if(Gy(this,f,s),!(s in this.target_)){var h;if((h=this.target_[Zt])!=null&&h[s])return;I(1,f.annotationType_,this.name_+"."+s.toString())}for(var p=this.target_;p&&p!==pu;){var b=hu(p,s);if(b){var y=f.make_(this,s,b,p);if(y===0)return;if(y===1)break}p=Object.getPrototypeOf(p)}qy(this,f,s)}},l.extend_=function(s,f,h,p){if(p===void 0&&(p=!1),h===!0&&(h=this.defaultAnnotation_),h===!1)return this.defineProperty_(s,f,p);Gy(this,h,s);var b=h.extend_(this,s,f,p);return b&&qy(this,h,s),b},l.defineProperty_=function(s,f,h){h===void 0&&(h=!1),ga(this.keysAtom_);try{zn();var p=this.delete_(s);if(!p)return p;if(Cn(this)){var b=Mn(this,{object:this.proxy_||this.target_,name:s,type:vo,newValue:f.value});if(!b)return null;var y=b.newValue;f.value!==y&&(f=La({},f,{value:y}))}if(h){if(!Reflect.defineProperty(this.target_,s,f))return!1}else va(this.target_,s,f);this.notifyPropertyAddition_(s,f.value)}finally{Dn()}return!0},l.defineObservableProperty_=function(s,f,h,p){p===void 0&&(p=!1),ga(this.keysAtom_);try{zn();var b=this.delete_(s);if(!b)return b;if(Cn(this)){var y=Mn(this,{object:this.proxy_||this.target_,name:s,type:vo,newValue:f});if(!y)return null;f=y.newValue}var z=Py(s),D={configurable:B.safeDescriptors?this.isPlainObject_:!0,enumerable:!0,get:z.get,set:z.set};if(p){if(!Reflect.defineProperty(this.target_,s,D))return!1}else va(this.target_,s,D);var x=new ui(f,h,Qt.NODE_ENV!=="production"?this.name_+"."+s.toString():"ObservableObject.key",!1);this.values_.set(s,x),this.notifyPropertyAddition_(s,x.value_)}finally{Dn()}return!0},l.defineComputedProperty_=function(s,f,h){h===void 0&&(h=!1),ga(this.keysAtom_);try{zn();var p=this.delete_(s);if(!p)return p;if(Cn(this)){var b=Mn(this,{object:this.proxy_||this.target_,name:s,type:vo,newValue:void 0});if(!b)return null}f.name||(f.name=Qt.NODE_ENV!=="production"?this.name_+"."+s.toString():"ObservableObject.key"),f.context=this.proxy_||this.target_;var y=Py(s),z={configurable:B.safeDescriptors?this.isPlainObject_:!0,enumerable:!1,get:y.get,set:y.set};if(h){if(!Reflect.defineProperty(this.target_,s,z))return!1}else va(this.target_,s,z);this.values_.set(s,new An(f)),this.notifyPropertyAddition_(s,void 0)}finally{Dn()}return!0},l.delete_=function(s,f){if(f===void 0&&(f=!1),ga(this.keysAtom_),!xn(this.target_,s))return!0;if(Cn(this)){var h=Mn(this,{object:this.proxy_||this.target_,name:s,type:$y});if(!h)return null}try{var p;zn();var b=ta(this),y=Qt.NODE_ENV!=="production"&&wt(),z=this.values_.get(s),D=void 0;if(!z&&(b||y)){var x;D=(x=hu(this.target_,s))==null?void 0:x.value}if(f){if(!Reflect.deleteProperty(this.target_,s))return!1}else delete this.target_[s];if(Qt.NODE_ENV!=="production"&&delete this.appliedAnnotations_[s],z&&(this.values_.delete(s),z instanceof ui&&(D=z.value_),by(z)),this.keysAtom_.reportChanged(),(p=this.pendingKeys_)==null||(p=p.get(s))==null||p.set(s in this.target_),b||y){var O={type:$y,observableKind:"object",object:this.proxy_||this.target_,debugObjectName:this.name_,oldValue:D,name:s};Qt.NODE_ENV!=="production"&&y&&cn(O),b&&na(this,O),Qt.NODE_ENV!=="production"&&y&&fn()}}finally{Dn()}return!0},l.observe_=function(s,f){return f===!0&&I("`observe` doesn't support the fire immediately property for observable objects."),Cl(this,s)},l.intercept_=function(s){return Dl(this,s)},l.notifyPropertyAddition_=function(s,f){var h,p=ta(this),b=wt();if(p||b){var y=p||b?{type:vo,observableKind:"object",debugObjectName:this.name_,object:this.proxy_||this.target_,name:s,newValue:f}:null;b&&cn(y),p&&na(this,y),b&&fn()}(h=this.pendingKeys_)==null||(h=h.get(s))==null||h.set(!0),this.keysAtom_.reportChanged()},l.ownKeys_=function(){return this.keysAtom_.reportObserved(),ar(this.target_)},l.keys_=function(){return this.keysAtom_.reportObserved(),Object.keys(this.target_)},i}();function hi(i,l){var u;if(l&&ur(i)&&I("Options can't be provided for already observable objects."),xn(i,le))return Qy(i)instanceof xd||I("Cannot convert '"+Nu(i)+`' into observable object:
The target is already observable of different type.
Extending builtins is not supported.`),i;Object.isExtensible(i)||I("Cannot make the designated object observable; it is not extensible");var s=(u=l?.name)!=null?u:(ln(i)?"ObservableObject":i.constructor.name)+"@"+ea(),f=new xd(i,new Map,String(s),WO(l));return Tl(i,le,f),i}var QE=ri("ObservableObjectAdministration",xd);function Py(i){return Vy[i]||(Vy[i]={get:function(){return this[le].getObservablePropValue_(i)},set:function(u){return this[le].setObservablePropValue_(i,u)}})}function ur(i){return vu(i)?QE(i[le]):!1}function qy(i,l,u){var s;i.appliedAnnotations_[u]=l,(s=i.target_[Zt])==null||delete s[u]}function Gy(i,l,u){if(Fy(l)||I("Cannot annotate '"+i.name_+"."+u.toString()+"': Invalid annotation."),!_u(l)&&xn(i.appliedAnnotations_,u)){var s=i.name_+"."+u.toString(),f=i.appliedAnnotations_[u].annotationType_,h=l.annotationType_;I("Cannot apply '"+h+"' to '"+s+"':"+(`
The field is already annotated with '`+f+"'.")+`
Re-annotating fields is not allowed.
Use 'override' annotation for methods overridden by subclass.`)}}var ZE=Xy(0),KE=function(){var i=!1,l={};return Object.defineProperty(l,"0",{set:function(){i=!0}}),Object.create(l)[0]=1,i===!1}(),Ad=0,Yy=function(){};function JE(i,l){Object.setPrototypeOf?Object.setPrototypeOf(i.prototype,l):i.prototype.__proto__!==void 0?i.prototype.__proto__=l:i.prototype=l}JE(Yy,Array.prototype);var Rd=function(i){function l(s,f,h,p){var b;return h===void 0&&(h="ObservableArray@"+ea()),p===void 0&&(p=!1),b=i.call(this)||this,go(function(){var y=new wd(h,f,p,!0);y.proxy_=b,Qv(b,le,y),s&&s.length&&b.spliceWithArray(0,0,s),KE&&Object.defineProperty(b,"0",ZE)}),b}Wv(l,i);var u=l.prototype;return u.concat=function(){this[le].atom_.reportObserved();for(var f=arguments.length,h=new Array(f),p=0;p<f;p++)h[p]=arguments[p];return Array.prototype.concat.apply(this.slice(),h.map(function(b){return Uu(b)?b.slice():b}))},u[Symbol.iterator]=function(){var s=this,f=0;return Dd({next:function(){return f<s.length?{value:s[f++],done:!1}:{done:!0,value:void 0}}})},or(l,[{key:"length",get:function(){return this[le].getArrayLength_()},set:function(f){this[le].setArrayLength_(f)}},{key:Symbol.toStringTag,get:function(){return"Array"}}])}(Yy);Object.entries(ju).forEach(function(i){var l=i[0],u=i[1];l!=="concat"&&Tl(Rd.prototype,l,u)});function Xy(i){return{enumerable:!1,configurable:!0,get:function(){return this[le].get_(i)},set:function(u){this[le].set_(i,u)}}}function WE(i){va(Rd.prototype,""+i,Xy(i))}function Iy(i){if(i>Ad){for(var l=Ad;l<i+100;l++)WE(l);Ad=i}}Iy(1e3);function FE(i,l,u){return new Rd(i,l,u)}function cr(i,l){if(typeof i=="object"&&i!==null){if(Uu(i))return l!==void 0&&I(23),i[le].atom_;if(ba(i))return i.atom_;if(yo(i)){if(l===void 0)return i.keysAtom_;var u=i.data_.get(l)||i.hasMap_.get(l);return u||I(25,l,Nu(i)),u}if(ur(i)){if(!l)return I(26);var s=i[le].values_.get(l);return s||I(27,l,Nu(i)),s}if(cd(i)||xu(i)||Cu(i))return i}else if(mt(i)&&Cu(i[le]))return i[le];I(28)}function Qy(i,l){if(i||I(29),cd(i)||xu(i)||Cu(i)||yo(i)||ba(i))return i;if(i[le])return i[le];I(24,i)}function Nu(i,l){var u;if(l!==void 0)u=cr(i,l);else{if(lr(i))return i.name;ur(i)||yo(i)||ba(i)?u=Qy(i):u=cr(i)}return u.name_}function go(i){var l=ci(),u=pd(!0);zn();try{return i()}finally{Dn(),md(u),Ba(l)}}var Zy=pu.toString;function Ky(i,l,u){return u===void 0&&(u=-1),zd(i,l,u)}function zd(i,l,u,s,f){if(i===l)return i!==0||1/i===1/l;if(i==null||l==null)return!1;if(i!==i)return l!==l;var h=typeof i;if(h!=="function"&&h!=="object"&&typeof l!="object")return!1;var p=Zy.call(i);if(p!==Zy.call(l))return!1;switch(p){case"[object RegExp]":case"[object String]":return""+i==""+l;case"[object Number]":return+i!=+i?+l!=+l:+i==0?1/+i===1/l:+i==+l;case"[object Date]":case"[object Boolean]":return+i==+l;case"[object Symbol]":return typeof Symbol<"u"&&Symbol.valueOf.call(i)===Symbol.valueOf.call(l);case"[object Map]":case"[object Set]":u>=0&&u++;break}i=Jy(i),l=Jy(l);var b=p==="[object Array]";if(!b){if(typeof i!="object"||typeof l!="object")return!1;var y=i.constructor,z=l.constructor;if(y!==z&&!(mt(y)&&y instanceof y&&mt(z)&&z instanceof z)&&"constructor"in i&&"constructor"in l)return!1}if(u===0)return!1;u<0&&(u=-1),s=s||[],f=f||[];for(var D=s.length;D--;)if(s[D]===i)return f[D]===l;if(s.push(i),f.push(l),b){if(D=i.length,D!==l.length)return!1;for(;D--;)if(!zd(i[D],l[D],u-1,s,f))return!1}else{var x=Object.keys(i),O=x.length;if(Object.keys(l).length!==O)return!1;for(var H=0;H<O;H++){var Y=x[H];if(!(xn(l,Y)&&zd(i[Y],l[Y],u-1,s,f)))return!1}}return s.pop(),f.pop(),!0}function Jy(i){return Uu(i)?i.slice():nr(i)||yo(i)||Ha(i)||ba(i)?Array.from(i.entries()):i}var Wy,ew=((Wy=du().Iterator)==null?void 0:Wy.prototype)||{};function Dd(i){return i[Symbol.iterator]=tw,Object.assign(Object.create(ew),i)}function tw(){return this}function Fy(i){return i instanceof Object&&typeof i.annotationType_=="string"&&mt(i.make_)&&mt(i.extend_)}["Symbol","Map","Set"].forEach(function(i){var l=du();typeof l[i]>"u"&&I("MobX requires global '"+i+"' to be available or polyfilled")}),typeof __MOBX_DEVTOOLS_GLOBAL_HOOK__=="object"&&__MOBX_DEVTOOLS_GLOBAL_HOOK__.injectMobx({spy:gE,extras:{getDebugName:Nu},$mobx:le});function jn(i,l){return Ml(i,l,new Map)}function Ml(i,l,u){if(i===l||Number.isNaN(i)&&Number.isNaN(l))return!0;const s=typeof i;if(s!==typeof l)return!1;if(i===null||l===null||s!=="object")return i===l;if(u.has(i)&&u.get(i)===l)return!0;if(u.set(i,l),i instanceof Date&&l instanceof Date)return i.getTime()===l.getTime();if(i instanceof RegExp&&l instanceof RegExp)return i.toString()===l.toString();if(i instanceof Map&&l instanceof Map){if(i.size!==l.size)return!1;for(const[y,z]of i){if(!l.has(y))return!1;const D=l.get(y);if(!Ml(z,D,u))return!1}return!0}if(i instanceof Set&&l instanceof Set){if(i.size!==l.size)return!1;for(const y of i){let z=!1;for(const D of l)if(Ml(y,D,u)){z=!0;break}if(!z)return!1}return!0}if(Array.isArray(i)&&Array.isArray(l)){if(i.length!==l.length)return!1;for(let y=0;y<i.length;y++)if(!Ml(i[y],l[y],u))return!1;return!0}if(typeof i!="object"||i===null||typeof l!="object"||l===null)return!1;const h=Object.keys(i),p=Object.keys(l);if(h.length!==p.length)return!1;const b=new Set(p);for(const y of h){if(!b.has(y))return!1;const z=i[y],D=l[y];if(!Ml(z,D,u))return!1}return!0}st({enforceActions:"observed"});let nw=class{pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)}};const Cd=new nw;st({enforceActions:"observed"});let aw=class{pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)}};const eg=new aw;st({enforceActions:"observed"});let ow=class{pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)}};const Md=new ow,tg={IS_DEV:!!(window?.GLOBAL_CONFIG?.IS_DEV??!1),AUTHORIZATION:String(window?.GLOBAL_CONFIG?.AUTHORIZATION??"")},ng=i=>`${window.location.pathname}?action=${i}`,Un=async(i,l={})=>{const u={method:"GET",headers:{"Content-Type":"application/json",...tg.AUTHORIZATION?{Authorization:tg.AUTHORIZATION||""}:{}},cache:"no-cache",credentials:"omit",...l},s=await fetch(ng(i),u);return{status:s.status,data:s.ok?await s.json().catch(()=>null):null}};if(!Q.useState)throw new Error("mobx-react-lite requires React with Hooks support");if(!VE)throw new Error("mobx-react-lite@3 requires mobx at least version 6 to be available");var ag=Vv();function iw(i){i()}function rw(i){i||(i=iw,console.warn("[MobX] Failed to get unstable_batched updates from react-dom / react-native")),st({reactionScheduler:i})}function lw(i){return Dy(i)}var sw=1e4,uw=1e4,cw=function(){function i(l){var u=this;Object.defineProperty(this,"finalize",{enumerable:!0,configurable:!0,writable:!0,value:l}),Object.defineProperty(this,"registrations",{enumerable:!0,configurable:!0,writable:!0,value:new Map}),Object.defineProperty(this,"sweepTimeout",{enumerable:!0,configurable:!0,writable:!0,value:void 0}),Object.defineProperty(this,"sweep",{enumerable:!0,configurable:!0,writable:!0,value:function(s){s===void 0&&(s=sw),clearTimeout(u.sweepTimeout),u.sweepTimeout=void 0;var f=Date.now();u.registrations.forEach(function(h,p){f-h.registeredAt>=s&&(u.finalize(h.value),u.registrations.delete(p))}),u.registrations.size>0&&u.scheduleSweep()}}),Object.defineProperty(this,"finalizeAllImmediately",{enumerable:!0,configurable:!0,writable:!0,value:function(){u.sweep(0)}})}return Object.defineProperty(i.prototype,"register",{enumerable:!1,configurable:!0,writable:!0,value:function(l,u,s){this.registrations.set(s,{value:u,registeredAt:Date.now()}),this.scheduleSweep()}}),Object.defineProperty(i.prototype,"unregister",{enumerable:!1,configurable:!0,writable:!0,value:function(l){this.registrations.delete(l)}}),Object.defineProperty(i.prototype,"scheduleSweep",{enumerable:!1,configurable:!0,writable:!0,value:function(){this.sweepTimeout===void 0&&(this.sweepTimeout=setTimeout(this.sweep,uw))}}),i}(),fw=typeof FinalizationRegistry<"u"?FinalizationRegistry:cw,jd=new fw(function(i){var l;(l=i.reaction)===null||l===void 0||l.dispose(),i.reaction=null}),Ud={exports:{}},kd={},og;function dw(){if(og)return kd;og=1;return function(){function i(H,Y){return H===Y&&(H!==0||1/H===1/Y)||H!==H&&Y!==Y}function l(H,Y){D||f.startTransition===void 0||(D=!0,console.error("You are using an outdated, pre-release alpha of React 18 that does not support useSyncExternalStore. The use-sync-external-store shim will not work correctly. Upgrade to a newer pre-release."));var F=Y();if(!x){var ne=Y();h(F,ne)||(console.error("The result of getSnapshot should be cached to avoid an infinite loop"),x=!0)}ne=p({inst:{value:F,getSnapshot:Y}});var ie=ne[0].inst,Qe=ne[1];return y(function(){ie.value=F,ie.getSnapshot=Y,u(ie)&&Qe({inst:ie})},[H,F,Y]),b(function(){return u(ie)&&Qe({inst:ie}),H(function(){u(ie)&&Qe({inst:ie})})},[H]),z(F),F}function u(H){var Y=H.getSnapshot;H=H.value;try{var F=Y();return!h(H,F)}catch{return!0}}function s(H,Y){return Y()}typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(Error());var f=bl(),h=typeof Object.is=="function"?Object.is:i,p=f.useState,b=f.useEffect,y=f.useLayoutEffect,z=f.useDebugValue,D=!1,x=!1,O=typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"?s:l;kd.useSyncExternalStore=f.useSyncExternalStore!==void 0?f.useSyncExternalStore:O,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(Error())}(),kd}var ig;function hw(){return ig||(ig=1,Ud.exports=dw()),Ud.exports}var pw=hw();function rg(i){i.reaction=new Va("observer".concat(i.name),function(){var l;i.stateVersion=Symbol(),(l=i.onStoreChange)===null||l===void 0||l.call(i)})}function mw(i,l){l===void 0&&(l="observed");var u=qv.useRef(null);if(!u.current){var s={reaction:null,onStoreChange:null,stateVersion:Symbol(),name:l,subscribe:function(b){return jd.unregister(s),s.onStoreChange=b,s.reaction||(rg(s),s.stateVersion=Symbol()),function(){var y;s.onStoreChange=null,(y=s.reaction)===null||y===void 0||y.dispose(),s.reaction=null}},getSnapshot:function(){return s.stateVersion}};u.current=s}var f=u.current;f.reaction||(rg(f),jd.register(u,f,f)),qv.useDebugValue(f.reaction,lw),pw.useSyncExternalStore(f.subscribe,f.getSnapshot,f.getSnapshot);var h,p;if(f.reaction.track(function(){try{h=i()}catch(b){p=b}}),p)throw p;return h}var Nd,Hd,lg=!0,sg=typeof Symbol=="function"&&Symbol.for,vw=(Hd=(Nd=Object.getOwnPropertyDescriptor(function(){},"name"))===null||Nd===void 0?void 0:Nd.configurable)!==null&&Hd!==void 0?Hd:!1,ug=sg?Symbol.for("react.forward_ref"):typeof Q.forwardRef=="function"&&Q.forwardRef(function(i){return null}).$$typeof,cg=sg?Symbol.for("react.memo"):typeof Q.memo=="function"&&Q.memo(function(i){return null}).$$typeof;function ve(i,l){var u;if(cg&&i.$$typeof===cg)throw new Error("[mobx-react-lite] You are trying to use `observer` on a function component wrapped in either another `observer` or `React.memo`. The observer already applies 'React.memo' for you.");var s=(u=void 0)!==null&&u!==void 0?u:!1,f=i,h=i.displayName||i.name;if(ug&&i.$$typeof===ug&&(s=!0,f=i.render,typeof f!="function"))throw new Error("[mobx-react-lite] `render` property of ForwardRef was not a function");var p=function(b,y){return mw(function(){return f(b,y)},h)};return p.displayName=i.displayName,vw&&Object.defineProperty(p,"name",{value:i.name,writable:!0,configurable:!0}),i.contextTypes&&(p.contextTypes=i.contextTypes,lg&&(lg=!1,console.warn("[mobx-react-lite] Support for Legacy Context in function components will be removed in the next major release."))),s&&(p=Q.forwardRef(p)),p=Q.memo(p),gw(i,p),Object.defineProperty(p,"contextTypes",{set:function(){var b,y;throw new Error("[mobx-react-lite] `".concat(this.displayName||((b=this.type)===null||b===void 0?void 0:b.displayName)||((y=this.type)===null||y===void 0?void 0:y.name)||"Component",".contextTypes` must be set before applying `observer`."))}}),p}var yw={$$typeof:!0,render:!0,compare:!0,type:!0,displayName:!0};function gw(i,l){Object.keys(i).forEach(function(u){yw[u]||Object.defineProperty(l,u,Object.getOwnPropertyDescriptor(i,u))})}var Ld;rw(ag.unstable_batchedUpdates),Ld=jd.finalizeAllImmediately;const bw={"":{ja:`Project-Id-Version: X-Prober
POT-Creation-Date: 
PO-Revision-Date: 2025-09-05 08:46+0800
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
PO-Revision-Date: 2025-09-04 22:51+0800
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
PO-Revision-Date: 2025-09-04 22:51+0800
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
PO-Revision-Date: 2025-09-04 22:53+0800
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
PO-Revision-Date: 2025-09-05 08:47+0800
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
`},"{{days}}d {{hours}}h {{mins}}min {{secs}}s":{ja:"{{days}}日 {{hours}}時間 {{mins}}分 {{secs}}秒",zh:"{{days}} 天 {{hours}} 时 {{mins}} 分 {{secs}} 秒",zhcn:"{{days}} 天 {{hours}} 时 {{mins}} 分 {{secs}} 秒",zhhk:"{{days}}天 {{hours}}小時 {{mins}}分鐘 {{secs}}秒",zhtw:"{{days}}天 {{hours}}小時 {{mins}}分鐘 {{secs}}秒"},"{{minute}} minute average":{ja:"{{minute}}分平均負荷",zh:"{{minute}} 分钟平均负载",zhcn:"{{minute}} 分钟平均负载",zhhk:"{{minute}} 分鐘平均",zhtw:"{{minute}} 分鐘平均"},"{{oldVersion}} (Latest: {{latestPhpVersion}})":{ja:"{{oldVersion}} (最新: {{latestPhpVersion}})",zh:"{{oldVersion}}（最新版：{{latestPhpVersion}}）",zhcn:"{{oldVersion}}（最新版：{{latestPhpVersion}}）",zhhk:"{{oldVersion}}（最新版：{{latestPhpVersion}}）",zhtw:"{{oldVersion}}（最新版：{{latestPhpVersion}}）"},"{{sensor}} temperature":{ja:"{{sensor}} 温度",zh:"{{sensor}} 温度",zhcn:"{{sensor}} 温度",zhhk:"{{sensor}} 溫度",zhtw:"{{sensor}} 溫度"},"{{times}} times, min/avg/max/mdev = {{min}}/{{avg}}/{{max}}/{{mdev}} ms":{ja:"{{times}}回実行: 最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} ms",zh:"{{times}}次, 最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒",zhcn:"{{times}}次, 最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒",zhhk:"{{times}} 次，最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒",zhtw:"{{times}} 次，最小/平均/最大/偏差 = {{min}}/{{avg}}/{{max}}/{{mdev}} 毫秒"},"{{usage}}% CPU usage":{ja:"CPU使用率: {{usage}}%",zh:"{{usage}}% CPU 使用率",zhcn:"{{usage}}% CPU 使用率",zhhk:"CPU 使用率 {{usage}}%",zhtw:"CPU 使用率 {{usage}}%"},"⏳ Updating, please wait a second...":{ja:"⏳ 更新中...",zh:"⏳ 更新中...",zhcn:"⏳ 更新中...",zhhk:"⏳ 更新中，請稍候...",zhtw:"⏳ 更新中，請稍候..."},"✨ Found new version: {{oldVersion}} ⇢ {{newVersion}}":{ja:"✨ 新版検出: {{oldVersion}} → {{newVersion}}",zh:"✨ 发现新版本: {{oldVersion}} → {{newVersion}}",zhcn:"✨ 发现新版本: {{oldVersion}} → {{newVersion}}",zhhk:"✨ 發現新版本：{{oldVersion}} → {{newVersion}}",zhtw:"✨ 發現新版本：{{oldVersion}} → {{newVersion}}"},"❌ Update error, click here to try again?":{ja:"❌ 更新エラー [再試行]",zh:"❌ 更新错误 [点击重试]",zhcn:"❌ 更新错误 [点击重试]",zhhk:"❌ 更新錯誤，點此重試？",zhtw:"❌ 更新錯誤，點此重試？"},"Benchmark my browser":{ja:"ブラウザのベンチマーク",zh:"测试浏览器性能",zhcn:"测试浏览器性能",zhhk:"測試我的瀏覽器",zhtw:"測試我的瀏覽器"},"Benchmark my server":{ja:"ベンチマーク実行",zh:"测试服务器性能",zhcn:"测试服务器性能",zhhk:"測試我的伺服器",zhtw:"測試我的伺服器"},"Browser bench":{ja:"ブラウザベンチ",zh:"浏览器性能",zhcn:"浏览器性能",zhhk:"瀏覽器跑分",zhtw:"瀏覽器跑分"},"Browser Benchmark":{ja:"ブラウザベンチマーク",zh:"浏览器性能测试",zhcn:"浏览器性能测试",zhhk:"瀏覽器跑分測試",zhtw:"客戶端跑分測試"},"Browser UA":{ja:"ブラウザユーザーエージェント",zh:"浏览器 UA",zhcn:"浏览器 UA",zhhk:"瀏覽器 UA",zhtw:"瀏覽器 UA"},"Buffers are in-memory block I/O buffers. They are relatively short-lived. Prior to Linux kernel version 2.4, Linux had separate page and buffer caches. Since 2.4, the page and buffer cache are unified and Buffers is raw disk blocks not represented in the page cache—i.e., not file data.":{ja:"バッファはメモリ内のブロックI/O用一時領域です。Linuxカーネル2.4以前ではページキャッシュとバッファキャッシュが分離されていましたが、2.4以降は統合され、バッファはページキャッシュに含まれない生ディスクブロック（非ファイルデータ）を指します。",zh:"缓冲区是内存中的块 I/O 缓冲区，生命周期较短。Linux 内核 2.4 版本前，页面缓存和缓冲区缓存是分离的。2.4 版本后两者统一，缓冲区指不在页面缓存中的原始磁盘块（即非文件数据）。",zhcn:"缓冲区是内存中的块 I/O 缓冲区，生命周期较短。Linux 内核 2.4 版本前，页面缓存和缓冲区缓存是分离的。2.4 版本后两者统一，缓冲区指不在页面缓存中的原始磁盘块（即非文件数据）。",zhhk:"緩衝區是記憶體中的區塊 I/O 緩衝，生命週期較短。在 Linux 核心 2.4 版之前，頁面快取和緩衝區快取是分開的。自 2.4 版起，兩者已統一，緩衝區代表未存入頁面快取的原始磁碟區塊（即非檔案資料）。",zhtw:"緩衝區是記憶體中的區塊 I/O 緩衝，生命週期較短。在 Linux 核心 2.4 版之前，頁面快取和緩衝區快取是分開的。自 2.4 版起，兩者已統一，緩衝區代表未存入頁面快取的原始磁碟區塊（即非檔案資料）。"},'Cached memory is memory that Linux uses for disk caching. However, this does not count as "used" memory, since it will be freed when applications require it. Hence you do not have to worry if a large amount is being used.':{ja:"キャッシュメモリはディスクキャッシュ用に確保された領域です。アプリケーションが必要時に解放されるため「使用中」メモリにはカウントされず、使用量が多くても問題ありません。",zh:"缓存内存是 Linux 用于磁盘缓存的内存空间，不计入“已用”内存，因为应用程序需要时会自动释放。因此即使使用量较大也无需担心。",zhcn:"缓存内存是 Linux 用于磁盘缓存的内存空间，不计入“已用”内存，因为应用程序需要时会自动释放。因此即使使用量较大也无需担心。",zhhk:"快取記憶體是 Linux 用於磁碟快取的空間，不計入「已用」記憶體，因應用程式需要時會自動釋放。故即使使用量較大亦無需擔心。",zhtw:"快取記憶體是 Linux 用於磁碟快取的空間，不計入「已用」記憶體，因應用程式需要時會自動釋放。故即使使用量較大亦無需擔心。"},"Can not fetch IP":{ja:"IP取得失敗",zh:"无法获取 IP",zhcn:"无法获取 IP",zhhk:"無法取得 IP",zhtw:"無法取得 IP"},"Can not fetch location.":{ja:"位置情報を取得できません。",zh:"无法获取位置信息。",zhcn:"无法获取位置信息。",zhhk:"無法取得地理位置。",zhtw:"無法取得地理位置。"},"Can not fetch marks data from GitHub.":{ja:"GitHubからスコアデータを取得できません。",zh:"无法从GitHub获取测试数据。",zhcn:"无法从GitHub获取测试数据。",zhhk:"無法從 GitHub 取得跑分數據。",zhtw:"無法從 GitHub 取得跑分資料。"},"Can not update file, please check the server permissions and space.":{ja:"ファイル更新失敗 サーバーの権限/空き容量を確認してください。",zh:"无法更新文件，请检查服务器权限和空间。",zhcn:"无法更新文件，请检查服务器权限和空间。",zhhk:"無法更新檔案，請檢查伺服器權限及空間。",zhtw:"無法更新檔案，請檢查伺服器權限及空間。"},"Click to close":{ja:"クリックで閉じる",zh:"点击关闭",zhcn:"点击关闭",zhhk:"點擊關閉",zhtw:"點擊關閉"},"Click to fetch":{ja:"クリックして取得",zh:"点击获取",zhcn:"点击获取",zhhk:"點擊獲取",zhtw:"點擊獲取"},"Click to update":{ja:"クリックで更新",zh:"点击更新",zhcn:"点击更新",zhhk:"點擊更新",zhtw:"點擊更新"},CPU:{ja:"CPU",zh:"处理器",zhcn:"处理器",zhhk:"中央處理器",zhtw:"中央處理器"},"CPU model":{ja:"CPUモデル",zh:"CPU 型号",zhcn:"CPU 型号",zhhk:"CPU 型號",zhtw:"CPU 型號"},"CPU usage":{ja:"CPU使用率",zh:"CPU 使用率",zhcn:"CPU 使用率",zhhk:"CPU 使用率",zhtw:"CPU 使用率"},Dark:{ja:"ダークモード",zh:"深色模式",zhcn:"深色模式",zhhk:"暗黑模式",zhtw:"深色模式"},Database:{ja:"データベース",zh:"数据库",zhcn:"数据库",zhhk:"資料庫",zhtw:"資料庫"},DB:{ja:"データベース",zh:"数据库",zhcn:"数据库",zhhk:"資料庫",zhtw:"資料庫"},Default:{ja:"デフォルト",zh:"默认",zhcn:"默认",zhhk:"預設",zhtw:"預設"},Detail:{ja:"詳細",zh:"详情",zhcn:"详情",zhhk:"詳細資料",zhtw:"詳細資料"},"Different versions cannot be compared, and different time clients have different loads, just for reference.":{ja:"異なるバージョンを比較することはできません。また、異なる時間のクライアントには異なる負荷がかかりますが、これはあくまで参考値です。",zh:"不同版本无法直接比较，不同时间浏览器负载各异，结果仅供参考。",zhcn:"不同版本无法直接比较，不同时间浏览器负载各异，结果仅供参考。",zhhk:"不同版本無法直接比較，且不同時間瀏覽器負載各異，結果僅供參考。",zhtw:"不同版本無法比較，不同時間客戶端負載不同，僅供參考。"},"Different versions cannot be compared, and different time servers have different loads, just for reference.":{ja:"異なるバージョン間の比較は不可。タイムサーバーの負荷状態により結果が変動します（参考値）",zh:"不同版本无法直接比较，不同时间服务器负载各异，结果仅供参考。",zhcn:"不同版本无法直接比较，不同时间服务器负载各异，结果仅供参考。",zhhk:"不同版本無法直接比較，且不同時間伺服器負載各異，結果僅供參考。",zhtw:"不同版本無法直接比較，且不同時間伺服器負載各異，結果僅供參考。"},"Disabled classes":{ja:"無効化クラス",zh:"禁用类",zhcn:"禁用类",zhhk:"已停用類別",zhtw:"已停用類別"},"Disabled functions":{ja:"無効化関数",zh:"禁用函数",zhcn:"禁用函数",zhhk:"已停用函式",zhtw:"已停用函式"},Disk:{ja:"ディスク",zh:"磁盘",zhcn:"磁盘",zhhk:"磁碟",zhtw:"磁碟"},"Disk Usage":{ja:"ディスク使用量",zh:"磁盘使用量",zhcn:"磁盘使用量",zhhk:"磁碟用量",zhtw:"磁碟使用量"},"Display errors":{ja:"エラー表示設定",zh:"显示错误",zhcn:"显示错误",zhhk:"顯示錯誤",zhtw:"顯示錯誤"},"Download speed test":{ja:"ダウンロード速度テスト",zh:"下载速度测试",zhcn:"下载速度测试",zhhk:"下載速度測試",zhtw:"下載速度測試"},"Error reporting":{ja:"エラーレポート設定",zh:"错误报告",zhcn:"错误报告",zhhk:"錯誤報告",zhtw:"錯誤報告"},"Error: {{error}}":{ja:"エラー: {{error}}",zh:"错误: {{error}}",zhcn:"错误: {{error}}",zhhk:"錯誤: {{error}}",zhtw:"錯誤: {{error}}"},"Failed to fetch data. Please try again later.":{ja:"データの取得に失敗しました。しばらくしてからもう一度お試しください。",zh:"无法获取数据，请稍后重试。",zhcn:"无法获取数据，请稍后重试。",zhhk:"無法獲取數據，請稍後重試。",zhtw:"無法獲取資料，請稍後重試。"},"Generate by {{appName}} and developed by {{authorName}}":{ja:"{{appName}} によって生成され、{{authorName}} によって開発されました",zh:"由 {{appName}} 生成并由 {{authorName}} 开发",zhcn:"由 {{appName}} 生成并由 {{authorName}} 开发",zhhk:"由{{appName}}生成並由{{authorName}}開發",zhtw:"由{{appName}}生成並由{{authorName}}開發"},"idle: {{idle}} \\nnice: {{nice}} \\nsys: {{sys}} \\nuser: {{user}}":{ja:"アイドル: {{idle}} \\n低優先: {{nice}} \\nシステム: {{sys}} \\nユーザー: {{user}}",zh:"空闲: {{idle}} \\n低优先级: {{nice}} \\n系统: {{sys}} \\n用户: {{user}}",zhcn:"空闲: {{idle}} \\n低优先级: {{nice}} \\n系统: {{sys}} \\n用户: {{user}}",zhhk:"閒置: {{idle}} \\n優先: {{nice}} \\n系統: {{sys}} \\n用戶: {{user}}",zhtw:"閒置: {{idle}} \\n優先: {{nice}} \\n系統: {{sys}} \\n使用者: {{user}}"},Info:{ja:"情報",zh:"信息",zhcn:"信息",zhhk:"資訊",zhtw:"資訊"},IPv4:{ja:"IPv4",zh:"IPv4",zhcn:"IPv4",zhhk:"IPv4",zhtw:"IPv4"},IPv6:{ja:"IPv6",zh:"IPv6",zhcn:"IPv6",zhhk:"IPv6",zhtw:"IPv6"},"JS Browser languages":{ja:"JS ブラウザ言語",zh:"JS 浏览器语言",zhcn:"JS 浏览器语言",zhhk:"JS 瀏覽器語言",zhtw:"JS瀏覽器語言"},'Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.':{ja:"Linuxにはメモリ使用量確認コマンドが複数存在します。「free」コマンドは物理メモリ/スワップの使用状況とカーネルバッファを表示し、「top」コマンドはシステムのリアルタイム状態を動的に表示します。",zh:"Linux 提供多种内存检测命令：“free”命令显示系统物理内存和交换空间的总用量及内核缓冲区；“top”命令提供运行中系统的实时动态视图。",zhcn:"Linux 提供多种内存检测命令：“free”命令显示系统物理内存和交换空间的总用量及内核缓冲区；“top”命令提供运行中系统的实时动态视图。",zhhk:"Linux 提供多種記憶體檢測指令：「free」指令顯示系統實體記憶體及交換區的總用量與緩衝區使用情況；「top」指令則提供運行中系統的即時動態檢視。",zhtw:"Linux 提供多種記憶體檢測指令：「free」指令顯示系統實體記憶體及交換區的總用量與緩衝區使用情況；「top」指令則提供運行中系統的即時動態檢視。"},"Loaded extensions":{ja:"ロード済み拡張機能",zh:"已加载扩展",zhcn:"已加载扩展",zhhk:"已載入擴充功能",zhtw:"已載入擴充功能"},"Loading...":{ja:"読込中...",zh:"加载中...",zhcn:"加载中...",zhhk:"載入中...",zhtw:"載入中..."},"Local IPv4":{ja:"ネイティブ IPv4",zh:"本地 IPv4",zhcn:"本地 IPv4",zhhk:"本地 IPv4",zhtw:"本地 IPv4"},"Local IPv6":{ja:"ネイティブ IPv6",zh:"本地 IPv6",zhcn:"本地 IPv6",zhhk:"本地 IPv6",zhtw:"本地 IPv6"},"Location (IPv4)":{ja:"位置情報 (IPv4)",zh:"位置 (IPv4)",zhcn:"位置 (IPv4)",zhhk:"位置 (IPv4)",zhtw:"位置 (IPv4)"},"Max execution time":{ja:"最大実行時間",zh:"最长执行时间",zhcn:"最长执行时间",zhhk:"最長執行時間",zhtw:"最長執行時間"},"Max input variables":{ja:"最大入力変数",zh:"最大输入变量数",zhcn:"最大输入变量数",zhhk:"最大輸入變數",zhtw:"最大輸入變數"},"Max memory limit":{ja:"最大メモリ制限",zh:"最大内存限制",zhcn:"最大内存限制",zhhk:"最大記憶體限制",zhtw:"最大記憶體限制"},"Max POST size":{ja:"最大POSTサイズ",zh:"最大 POST 大小",zhcn:"最大 POST 大小",zhhk:"最大 POST 容量",zhtw:"最大 POST 容量"},"Max upload size":{ja:"最大アップロードサイズ",zh:"最大上传大小",zhcn:"最大上传大小",zhhk:"最大上載容量",zhtw:"最大上傳容量"},"Memory buffers":{ja:"メモリバッファ",zh:"内存缓冲区",zhcn:"内存缓冲区",zhhk:"記憶體緩衝區",zhtw:"記憶體緩衝區"},"Memory cached":{ja:"キャッシュメモリ",zh:"缓存内存",zhcn:"缓存内存",zhhk:"記憶體快取",zhtw:"記憶體快取"},"Memory real usage":{ja:"実メモリ使用量",zh:"实际内存使用",zhcn:"实际内存使用",zhhk:"實際記憶體用量",zhtw:"實際記憶體用量"},Mine:{ja:"マイデータ",zh:"我的",zhcn:"我的",zhhk:"我的",zhtw:"我的"},"Move down":{ja:"下へ移動",zh:"下移",zhcn:"下移",zhhk:"下移",zhtw:"下移"},"Move up":{ja:"上へ移動",zh:"上移",zhcn:"上移",zhhk:"上移",zhtw:"上移"},"My Info":{ja:"マイ情報",zh:"我的信息",zhcn:"我的信息",zhhk:"我的資訊",zhtw:"我的資訊"},Name:{ja:"名称",zh:"名称",zhcn:"名称",zhhk:"名稱",zhtw:"名稱"},Network:{ja:"ネットワーク",zh:"网络",zhcn:"网络",zhhk:"網絡",zhtw:"網路"},"Network error, please try again later.":{ja:"ネットワークエラー。後ほど再試行してください。",zh:"网络错误，请稍后重试。",zhcn:"网络错误，请稍后重试。",zhhk:"網絡錯誤，請稍後重試。",zhtw:"網路錯誤，請稍後重試。"},"Network Stats":{ja:"ネットワーク統計",zh:"网络统计",zhcn:"网络统计",zhhk:"網絡統計",zhtw:"網路統計"},Nodes:{ja:"ノード",zh:"节点",zhcn:"节点",zhhk:"節點",zhtw:"節點"},"Not support":{ja:"非対応",zh:"不支持",zhcn:"不支持",zhhk:"不支援",zhtw:"不支援"},"Opcache enabled":{ja:"Opcache 有効",zh:"Opcache 已启用",zhcn:"Opcache 已启用",zhhk:"Opcache 已啟用",zhtw:"Opcache 已啟用"},"Opcache JIT enabled":{ja:"Opcache JIT 有効",zh:"Opcache JIT 已启用",zhcn:"Opcache JIT 已启用",zhhk:"Opcache JIT 已啟用",zhtw:"Opcache JIT 已啟用"},OS:{ja:"OS",zh:"操作系统",zhcn:"操作系统",zhhk:"作業系統",zhtw:"作業系統"},"PHP Browser languages":{ja:"PHP ブラウザ言語",zh:"PHP 浏览器语言",zhcn:"PHP 浏览器语言",zhhk:"PHP 瀏覽器語言",zhtw:"PHP瀏覽器語言"},"PHP Ext":{ja:"PHP拡張",zh:"PHP扩展",zhcn:"PHP扩展",zhhk:"PHP擴充",zhtw:"PHP擴充"},"PHP Extensions":{ja:"PHP拡張機能",zh:"PHP 扩展",zhcn:"PHP 扩展",zhhk:"PHP 擴充功能",zhtw:"PHP 擴充功能"},"PHP Info":{ja:"PHP情報",zh:"PHP信息",zhcn:"PHP信息",zhhk:"PHP資訊",zhtw:"PHP資訊"},"PHP Information":{ja:"PHP情報",zh:"PHP 信息",zhcn:"PHP 信息",zhhk:"PHP 資訊",zhtw:"PHP 資訊"},Ping:{ja:"ネットワーク診断",zh:"网络检测",zhcn:"网络检测",zhhk:"網絡檢測",zhtw:"網路檢測"},"Please wait {{seconds}}s":{ja:"{{seconds}}秒お待ちください",zh:"请等待 {{seconds}} 秒",zhcn:"请等待 {{seconds}} 秒",zhhk:"請等候 {{seconds}} 秒",zhtw:"請等候 {{seconds}} 秒"},"Public IPv4":{ja:"パブリック IPv4",zh:"公网 IPv4",zhcn:"公网 IPv4",zhhk:"公眾 IPv4",zhtw:"公開 IPv4"},"Public IPv6":{ja:"パブリック IPv6",zh:"公网 IPv6",zhcn:"公网 IPv6",zhhk:"公眾 IPv6",zhtw:"公開 IPv6"},Ram:{ja:"RAM",zh:"内存",zhcn:"内存",zhhk:"記憶體",zhtw:"記憶體"},Read:{ja:"読取",zh:"读取",zhcn:"读取",zhhk:"讀取",zhtw:"讀取"},"Recived: {{total}}":{ja:"受信: {{total}}",zh:"接收: {{total}}",zhcn:"接收: {{total}}",zhhk:"接收: {{total}}",zhtw:"接收: {{total}}"},Results:{ja:"診断結果",zh:"结果",zhcn:"结果",zhhk:"結果",zhtw:"結果"},"Running the benchmark may freeze the browser interface for a few seconds. Do you want to continue?":{ja:"ベンチマークを実行すると、ブラウザインターフェースが数秒間フリーズする場合があります。続行しますか？",zh:"执行性能测试可能会冻结浏览器界面数秒，是否继续？",zhcn:"执行性能测试可能会冻结浏览器界面数秒，是否继续？",zhhk:"執行基準測試可能會導致瀏覽器介面凍結幾秒鐘。是否繼續？",zhtw:"執行基準測試可能會導致瀏覽器介面凍結幾秒鐘。是否繼續？"},"SAPI interface":{ja:"SAPIインターフェース",zh:"SAPI 接口",zhcn:"SAPI 接口",zhhk:"SAPI 介面",zhtw:"SAPI 介面"},"Script path":{ja:"スクリプトパス",zh:"脚本路径",zhcn:"脚本路径",zhhk:"腳本路徑",zhtw:"腳本路徑"},"Sent: {{total}}":{ja:"送信: {{total}}",zh:"发送: {{total}}",zhcn:"发送: {{total}}",zhhk:"傳送: {{total}}",zhtw:"傳送: {{total}}"},"Server ⇄ Browser":{ja:"サーバー ⇄ ブラウザー",zh:"服务器 ⇄ 浏览器",zhcn:"服务器 ⇄ 浏览器",zhhk:"伺服器 ⇄ 瀏覽器",zhtw:"伺服器 ⇄ 瀏覽器"},"Server bench":{ja:"サーバーベンチ",zh:"服务器性能",zhcn:"服务器性能",zhhk:"伺服器跑分",zhtw:"伺服器跑分"},"Server Benchmark":{ja:"サーバーベンチマーク",zh:"服务器性能测试",zhcn:"服务器性能测试",zhhk:"伺服器跑分測試",zhtw:"伺服器跑分測試"},"Server Info":{ja:"サーバー情報",zh:"服务器信息",zhcn:"服务器信息",zhhk:"伺服器資訊",zhtw:"伺服器資訊"},"Server Status":{ja:"サーバー状態",zh:"服务器状态",zhcn:"服务器状态",zhhk:"伺服器狀態",zhtw:"伺服器狀態"},"SMTP support":{ja:"SMTPサポート",zh:"SMTP 支持",zhcn:"SMTP 支持",zhhk:"SMTP 支援",zhtw:"SMTP 支援"},"Start ping":{ja:"Ping 開始",zh:"开始 Ping",zhcn:"开始 Ping",zhhk:"開始 Ping",zhtw:"開始 Ping"},"Stop ping":{ja:"Ping 停止",zh:"停止 Ping",zhcn:"停止 Ping",zhhk:"停止 Ping",zhtw:"停止 Ping"},Swap:{ja:"スワップ",zh:"交换空间",zhcn:"交换空间",zhhk:"交換區",zhtw:"交換區"},"Swap cached":{ja:"スワップキャッシュ",zh:"交换区缓存",zhcn:"交换区缓存",zhhk:"交換區快取",zhtw:"交換區快取"},"Swap usage":{ja:"スワップ使用量",zh:"交换区使用",zhcn:"交换区使用",zhhk:"交換區用量",zhtw:"交換區用量"},"System load":{ja:"システム負荷",zh:"系统负载",zhcn:"系统负载",zhhk:"系統負載",zhtw:"系統負載"},Temperature:{ja:"温度",zh:"温度",zhcn:"温度",zhhk:"溫度",zhtw:"溫度"},"Temperature sensor":{ja:"温度センサー",zh:"温度传感器",zhcn:"温度传感器",zhhk:"溫度感測器",zhtw:"溫度感測器"},"Testing, please wait...":{ja:"テスト実行中...",zh:"测试中，请稍候...",zhcn:"测试中，请稍候...",zhhk:"測試中，請稍候...",zhtw:"測試中，請稍候..."},Time:{ja:"時間",zh:"时间",zhcn:"时间",zhhk:"時間",zhtw:"時間"},"Timeout for socket":{ja:"ソケットタイムアウト",zh:"Socket 超时时间",zhcn:"Socket 超时时间",zhhk:"Socket 逾時時間",zhtw:"Socket 逾時時間"},"Touch to copy marks":{ja:"タップでスコアをコピー",zh:"点击复制分数",zhcn:"点击复制分数",zhhk:"點擊複製跑分數據",zhtw:"點擊複製跑分數據"},"Treatment URLs file":{ja:"リモートファイル処理",zh:"远程文件处理",zhcn:"远程文件处理",zhhk:"檔案遠端開啟功能",zhtw:"檔案遠端開啟功能"},Unavailable:{ja:"取得不可",zh:"不可用",zhcn:"不可用",zhhk:"無法取得",zhtw:"無法取得"},"Update is disabled in dev mode.":{ja:"開発モードでは更新不可。",zh:"开发模式下禁用更新。",zhcn:"开发模式下禁用更新。",zhhk:"開發模式下停用更新功能。",zhtw:"開發模式下停用更新功能。"},"Update success, refreshing...":{ja:"更新成功 再読込中...",zh:"更新成功，刷新中...",zhcn:"更新成功，刷新中...",zhhk:"更新成功，重新整理中...",zhtw:"更新成功，重新整理中..."},Uptime:{ja:"稼働時間",zh:"运行时间",zhcn:"运行时间",zhhk:"運行時間",zhtw:"運行時間"},Version:{ja:"バージョン",zh:"版本",zhcn:"版本",zhhk:"版本",zhtw:"版本"},"Visit PHP.net Official website":{ja:"PHP.net公式サイトへ",zh:"访问 PHP.net 官网",zhcn:"访问 PHP.net 官网",zhhk:"瀏覽 PHP.net 官方網站",zhtw:"瀏覽 PHP.net 官方網站"},"Visit probe page":{ja:"プローブページへ",zh:"访问检测页面",zhcn:"访问检测页面",zhhk:"瀏覽檢測頁面",zhtw:"瀏覽檢測頁面"},"Visit the official website":{ja:"公式サイトへ",zh:"访问官网",zhcn:"访问官网",zhhk:"瀏覽官方網站",zhtw:"瀏覽官方網站"},"Web server":{ja:"ウェブサーバー",zh:"Web 服务器",zhcn:"Web 服务器",zhhk:"Web伺服器",zhtw:"Web伺服器"},Write:{ja:"書込",zh:"写入",zhcn:"写入",zhhk:"寫入",zhtw:"寫入"}},_w=navigator.language.replace("-","").replace("_","").toLowerCase(),N=(i,l="")=>{const u=`${l?`${l}|`:""}${i}`;return bw?.[u]?.[_w]??i};st({enforceActions:"observed"});let Sw=class{pollData=null;constructor(){vt(this)}setPollData=l=>{this.pollData=l}};const Bd=new Sw;function Kt(i,l){let u=i;for(const[s,f]of Object.entries(l)){const h=new RegExp(`\\{\\{${s}\\}\\}`,"g");u=u.replace(h,String(f))}return u}const Tw={main:"_main_17cch_12"},Ow=ve(()=>{const{pollData:i}=Bd;if(!i?.config)return null;const{APP_NAME:l,APP_URL:u,AUTHOR_NAME:s,AUTHOR_URL:f}=i.config;return _.jsx("div",{className:Tw.main,dangerouslySetInnerHTML:{__html:Kt(N("Generate by {{appName}} and developed by {{authorName}}"),{appName:`<a href="${u}" target="_blank">${l}</a>`,authorName:`<a href="${f}" target="_blank">${s}</a>`})}})}),Ew={main:"_main_1jpdc_16"},ia=200,ww=201,xw=403,Aw=429,Rw=500,zw=507;st({enforceActions:"observed"});let Dw=class{isUpdating=!1;isUpdateError=!1;targetVersion="";constructor(){vt(this)}setTargetVersion=l=>{this.targetVersion=l};setIsUpdating=l=>{this.isUpdating=l};setIsUpdateError=l=>{this.isUpdateError=l};get notiText(){return this.isUpdating?N("⏳ Updating, please wait a second..."):this.isUpdateError?N("❌ Update error, click here to try again?"):this.targetVersion?Kt(N("✨ Found new version: {{oldVersion}} ⇢ {{newVersion}}"),{oldVersion:Cd.pollData?.APP_VERSION??"-",newVersion:this.targetVersion}):""}};const Vd=new Dw,fg={main:"_main_p5526_16"},Cw=i=>_.jsx("a",{className:fg.main,...i}),Mw=i=>_.jsx("button",{className:fg.main,...i});st({enforceActions:"observed"});let jw=class{isOpen=!1;msg="";constructor(){vt(this)}setMsg=l=>{this.msg=l};close=(l=0)=>{setTimeout(()=>{OE(()=>{this.isOpen=!1})},l*1e3)};open=l=>{this.msg=l,this.isOpen=!0}};const bo=new jw,Uw=ve(()=>{const{isUpdating:i,setIsUpdating:l,setIsUpdateError:u,notiText:s}=Vd,{open:f}=bo,h=Q.useCallback(async p=>{if(p.preventDefault(),p.stopPropagation(),i)return;l(!0);const{status:b}=await Un("update");switch(b){case ww:f(N("Update success, refreshing...")),window.location.reload();return;case xw:f(N("Update is disabled in dev mode.")),l(!1),u(!0);return;case zw:case Rw:f(N("Can not update file, please check the server permissions and space.")),l(!1),u(!0);return}f(N("Network error, please try again later.")),l(!1),u(!0)},[i,l,u,f]);return _.jsx(Mw,{onClick:h,title:N("Click to update"),children:s})}),dg=(i,l)=>{if(typeof i+typeof l!="stringstring")return 0;const u=i.split("."),s=l.split("."),f=Math.max(u.length,s.length);for(let h=0;h<f;h+=1){if(u[h]&&!s[h]&&Number(u[h])>0||Number(u[h])>Number(s[h]))return 1;if(s[h]&&!u[h]&&Number(s[h])>0||Number(u[h])<Number(s[h]))return-1}return 0},$d={main:"_main_1k8xz_1",name:"_name_1k8xz_6",version:"_version_1k8xz_10"},kw=ve(()=>{const{pollData:i}=Cd,{setTargetVersion:l,targetVersion:u}=Vd;if(Q.useEffect(()=>{if(!i)return;(async()=>{const{data:b,status:y}=await Un("latestVersion");!b?.version||y!==ia||l(b.version)})()},[i,l]),!i)return null;const{APP_NAME:s,APP_URL:f,APP_VERSION:h}=i;return _.jsx("h1",{className:$d.main,children:u&&dg(h,u)<0?_.jsx(Uw,{}):_.jsxs(Cw,{href:f,rel:"noreferrer",target:"_blank",children:[_.jsx("span",{className:$d.name,children:s}),_.jsx("span",{className:$d.version,children:h})]})})}),Nw=()=>_.jsx("div",{className:Ew.main,children:_.jsx(kw,{})});st({enforceActions:"observed"});let Hw=class{pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)}};const Pd=new Hw;st({enforceActions:"observed"});let Lw=class{pollData=null;constructor(){vt(this)}setPollData(l){jn(l,this.pollData)||(this.pollData=l)}get networks(){return this.pollData?.networks??[]}get timestamp(){return this.pollData?.timestamp??0}get sortNetworks(){return this.networks.filter(({tx:l})=>!!l).toSorted((l,u)=>l.tx-u.tx)}get networksCount(){return this.sortNetworks.length}};const qd=new Lw;st({enforceActions:"observed"});let Bw=class{DEFAULT_ITEM={id:"",url:"",fetchUrl:"",loading:!0,status:204,data:null};items=[];pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)};setItems=l=>{this.items=l};setItem=({id:l,...u})=>{const s=this.items.findIndex(f=>f.id===l);s!==-1&&(this.items[s]={...this.items[s],...u})}};const Gd=new Bw;st({enforceActions:"observed"});let Vw=class{pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)}};const Yd=new Vw;st({enforceActions:"observed"});let $w=class{pollData=null;latestPhpVersion="";constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)};setLatestPhpVersion=l=>{this.latestPhpVersion=l}};const Hu=new $w;st({enforceActions:"observed"});let Pw=class{pollData=null;publicIpv4="";publicIpv6="";constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)};setPublicIpv4=l=>{this.publicIpv4=l};setPublicIpv6=l=>{this.publicIpv6=l}};const Xd=new Pw;st({enforceActions:"observed"});let qw=class{pollData=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)};get sysLoad(){return this.pollData?.sysLoad||[0,0,0]}get cpuUsage(){return this.pollData?.cpuUsage??{usage:0,idle:100,sys:0,user:0}}get memRealUsage(){return this.pollData?.memRealUsage??{max:0,value:0}}get memCached(){return this.pollData?.memCached??{max:0,value:0}}get memBuffers(){return this.pollData?.memBuffers??{max:0,value:0}}get swapUsage(){return this.pollData?.swapUsage??{max:0,value:0}}get swapCached(){return this.pollData?.swapCached??{max:0,value:0}}};const _o=new qw,Gw=i=>{const l=Q.useRef(document.createElement("div"));return Q.useEffect(()=>(document.body.appendChild(l.current),()=>{l.current.remove()}),[i]),l.current},Yw=({children:i})=>{const l=Gw();return ag.createPortal(i,l)},Xw={main:"_main_17sik_12"},Iw=ve(()=>{const{isOpen:i,msg:l,close:u}=bo,s=f=>{f.preventDefault(),f.stopPropagation(),u()};return i?_.jsx(Yw,{children:_.jsx("button",{className:Xw.main,onClick:s,title:N("Click to close"),type:"button",children:l})}):null});st({enforceActions:"observed"});let Qw=class{data=null;constructor(){vt(this)}setPollData=l=>{jn(l,this.data)||(this.data=l)}};const Zw=new Qw,Lu={id:"browserBenchmark"},Bu={id:"ping"},Vu={id:"database"},$u={id:"diskUsage"},Pu={id:"myInfo"},qu={id:"networkStats"},Gu={id:"nodes"},Yu={id:"phpExtensions"},Xu={id:"phpInfo"},Iu={id:"serverBenchmark"},Qu={id:"serverInfo"},Zu={id:"serverStatus"},Ku={id:"temperatureSensor"},Id=[Gu.id,Ku.id,Zu.id,qu.id,$u.id,Qu.id,Bu.id,Xu.id,Yu.id,Vu.id,Iu.id,Lu.id,Pu.id],Kw={container:"_container_30sck_1"};const Jw=i=>i.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase(),Ww=i=>i.replace(/^([A-Z])|[\s-_]+(\w)/g,(l,u,s)=>s?s.toUpperCase():u.toLowerCase()),hg=i=>{const l=Ww(i);return l.charAt(0).toUpperCase()+l.slice(1)},pg=(...i)=>i.filter((l,u,s)=>!!l&&l.trim()!==""&&s.indexOf(l)===u).join(" ").trim(),Fw=i=>{for(const l in i)if(l.startsWith("aria-")||l==="role"||l==="title")return!0};var ex={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:2,strokeLinecap:"round",strokeLinejoin:"round"};const tx=Q.forwardRef(({color:i="currentColor",size:l=24,strokeWidth:u=2,absoluteStrokeWidth:s,className:f="",children:h,iconNode:p,...b},y)=>Q.createElement("svg",{ref:y,...ex,width:l,height:l,stroke:i,strokeWidth:s?Number(u)*24/Number(l):u,className:pg("lucide",f),...!h&&!Fw(b)&&{"aria-hidden":"true"},...b},[...p.map(([z,D])=>Q.createElement(z,D)),...Array.isArray(h)?h:[h]]));const So=(i,l)=>{const u=Q.forwardRef(({className:s,...f},h)=>Q.createElement(tx,{ref:h,iconNode:l,className:pg(`lucide-${Jw(hg(i))}`,`lucide-${i}`,s),...f}));return u.displayName=hg(i),u};const nx=So("chevron-down",[["path",{d:"m6 9 6 6 6-6",key:"qrunsl"}]]);const ax=So("chevron-up",[["path",{d:"m18 15-6-6-6 6",key:"153udz"}]]);const ox=So("cloud-download",[["path",{d:"M12 13v8l-4-4",key:"1f5nwf"}],["path",{d:"m12 21 4-4",key:"1lfcce"}],["path",{d:"M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284",key:"ui1hmy"}]]);const ix=So("link",[["path",{d:"M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71",key:"1cjeqo"}],["path",{d:"M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71",key:"19qd67"}]]);const rx=So("loader-pinwheel",[["path",{d:"M22 12a1 1 0 0 1-10 0 1 1 0 0 0-10 0",key:"1lzz15"}],["path",{d:"M7 20.7a1 1 0 1 1 5-8.7 1 1 0 1 0 5-8.6",key:"1gnrpi"}],["path",{d:"M7 3.3a1 1 0 1 1 5 8.6 1 1 0 1 0 5 8.6",key:"u9yy5q"}],["circle",{cx:"12",cy:"12",r:"10",key:"1mglay"}]]);const lx=So("pointer",[["path",{d:"M22 14a8 8 0 0 1-8 8",key:"56vcr3"}],["path",{d:"M18 11v-1a2 2 0 0 0-2-2a2 2 0 0 0-2 2",key:"1agjmk"}],["path",{d:"M14 10V9a2 2 0 0 0-2-2a2 2 0 0 0-2 2v1",key:"wdbh2u"}],["path",{d:"M10 9.5V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v10",key:"1ibuk9"}],["path",{d:"M18 11a2 2 0 1 1 4 0v3a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15",key:"g6ys72"}]]);const sx=So("triangle-alert",[["path",{d:"m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3",key:"wmoenq"}],["path",{d:"M12 9v4",key:"juzpu7"}],["path",{d:"M12 17h.01",key:"p32p05"}]]);const ux=So("x",[["path",{d:"M18 6 6 18",key:"1bl5f8"}],["path",{d:"m6 6 12 12",key:"d8bk6v"}]]),cx={arrow:"_arrow_1qtu9_14"},mg="module-priority",vg={getItems(){const i=localStorage.getItem(mg);if(!i)return{};try{return JSON.parse(i)}catch{return{}}},setItems(i){localStorage.setItem(mg,JSON.stringify(i))},getPriority(i){return this.getItems()[i]||0},setPriority({id:i,priority:l}){const u=this.getItems();u[i]=l,this.setItems(u)}};st({enforceActions:"observed"});const yg=i=>{const l={};for(const u of i)l[u.id]=u.priority;vg.setItems(l)};let fx=class{sortedModules=[];constructor(){vt(this)}setSortedModules=l=>{this.sortedModules=l.toSorted((u,s)=>u.priority-s.priority)};get availableModules(){const{pollData:l}=Bd;return Lg.items.filter(({id:s})=>!!l?.[s]).toSorted((s,f)=>{const h=this.sortedModules.find(b=>b.id===s.id),p=this.sortedModules.find(b=>b.id===f.id);return Number(h?.priority??Id.indexOf(s.id))-Number(p?.priority??Id.indexOf(f.id))})}moveUp=l=>{const u=this.sortedModules.findIndex(f=>f.id===l);if(u===0)return;const s=this.sortedModules[u].priority;this.sortedModules[u].priority=this.sortedModules[u-1].priority,this.sortedModules[u-1].priority=s,this.sortedModules.sort((f,h)=>f.priority-h.priority),yg(this.sortedModules)};moveDown=l=>{const u=this.sortedModules.findIndex(f=>f.id===l);if(u===this.sortedModules.length-1)return;const s=this.sortedModules[u].priority;this.sortedModules[u].priority=this.sortedModules[u+1].priority,this.sortedModules[u+1].priority=s,this.sortedModules.sort((f,h)=>f.priority-h.priority),yg(this.sortedModules)};get disabledMoveUpId(){const l=this.availableModules;return l.length<=1?"":l[0].id}get disabledMoveDownId(){const l=this.availableModules;return l.length<=1?"":l.at(-1)?.id??""}};const Qd=new fx,gg=ve(({isDown:i,id:l})=>{const{disabledMoveUpId:u,disabledMoveDownId:s,moveUp:f,moveDown:h}=Qd,p=i?s===l:u===l,b=Q.useCallback(y=>{if(y.preventDefault(),y.stopPropagation(),i){h(l);return}f(l)},[i,h,f,l]);return _.jsx("button",{className:cx.arrow,"data-disabled":p||void 0,disabled:p,onClick:b,title:N(i?"Move down":"Move up"),type:"button",children:i?_.jsx(nx,{}):_.jsx(ax,{})})}),Ju={main:"_main_60fl9_23",header:"_header_60fl9_29",title:"_title_60fl9_41",body:"_body_60fl9_45"},dx=({id:i,title:l})=>_.jsxs("h2",{className:Ju.header,children:[_.jsx(gg,{id:i,isDown:!1}),_.jsx("span",{className:Ju.title,children:l}),_.jsx(gg,{id:i,isDown:!0})]}),hn=({id:i,title:l,children:u,...s})=>_.jsxs("div",{className:Ju.main,id:i,...s,children:[_.jsx(dx,{id:i,title:l}),_.jsx("div",{className:Ju.body,children:u})]}),bg={main:"_main_1hf64_14",item:"_item_1hf64_22"},_g=({items:i})=>_.jsx("ul",{className:bg.main,children:i.map(({id:l,text:u})=>_.jsx("li",{className:bg.item,children:u},l))}),hx={main:"_main_vvbro_25"},Zd=({height:i=5})=>_.jsx("div",{className:hx.main,style:{height:`${i}rem`}}),px={main:"_main_1ogv8_16"},Kd=({children:i})=>_.jsx("div",{className:px.main,role:"alert",children:i});var Jd,Sg;function mx(){return Sg||(Sg=1,Jd=function(){var i=document.getSelection();if(!i.rangeCount)return function(){};for(var l=document.activeElement,u=[],s=0;s<i.rangeCount;s++)u.push(i.getRangeAt(s));switch(l.tagName.toUpperCase()){case"INPUT":case"TEXTAREA":l.blur();break;default:l=null;break}return i.removeAllRanges(),function(){i.type==="Caret"&&i.removeAllRanges(),i.rangeCount||u.forEach(function(f){i.addRange(f)}),l&&l.focus()}}),Jd}var Wd,Tg;function vx(){if(Tg)return Wd;Tg=1;var i=mx(),l={"text/plain":"Text","text/html":"Url",default:"Text"},u="Copy to clipboard: #{key}, Enter";function s(h){var p=(/mac os x/i.test(navigator.userAgent)?"⌘":"Ctrl")+"+C";return h.replace(/#{\s*key\s*}/g,p)}function f(h,p){var b,y,z,D,x,O,H=!1;p||(p={}),b=p.debug||!1;try{z=i(),D=document.createRange(),x=document.getSelection(),O=document.createElement("span"),O.textContent=h,O.ariaHidden="true",O.style.all="unset",O.style.position="fixed",O.style.top=0,O.style.clip="rect(0, 0, 0, 0)",O.style.whiteSpace="pre",O.style.webkitUserSelect="text",O.style.MozUserSelect="text",O.style.msUserSelect="text",O.style.userSelect="text",O.addEventListener("copy",function(F){if(F.stopPropagation(),p.format)if(F.preventDefault(),typeof F.clipboardData>"u"){b&&console.warn("unable to use e.clipboardData"),b&&console.warn("trying IE specific stuff"),window.clipboardData.clearData();var ne=l[p.format]||l.default;window.clipboardData.setData(ne,h)}else F.clipboardData.clearData(),F.clipboardData.setData(p.format,h);p.onCopy&&(F.preventDefault(),p.onCopy(F.clipboardData))}),document.body.appendChild(O),D.selectNodeContents(O),x.addRange(D);var Y=document.execCommand("copy");if(!Y)throw new Error("copy command was unsuccessful");H=!0}catch(F){b&&console.error("unable to copy using execCommand: ",F),b&&console.warn("trying IE specific stuff");try{window.clipboardData.setData(p.format||"text",h),p.onCopy&&p.onCopy(window.clipboardData),H=!0}catch(ne){b&&console.error("unable to copy using clipboardData: ",ne),b&&console.error("falling back to prompt"),y=s("message"in p?p.message:u),window.prompt(y,h)}}finally{x&&(typeof x.removeRange=="function"?x.removeRange(D):x.removeAllRanges()),O&&document.body.removeChild(O),z()}return H}return Wd=f,Wd}var yx=vx();const Og=vl(yx),gx={main:"_main_1j8ow_12"},To=({ruby:i,rt:l,isResult:u=!1,...s})=>_.jsxs("ruby",{className:gx.main,"data-is-result":u||void 0,...s,children:[i,_.jsx("rp",{children:"("}),_.jsx("rt",{children:l}),_.jsx("rp",{children:")"})]}),jl={main:"_main_1p71d_13",header:"_header_1p71d_22",marks:"_marks_1p71d_46",sign:"_sign_1p71d_62"},kn=(i,l=2)=>{if(i===0)return"0";const u=1024,s=["B","K","M","G","T","P","E","Z","Y"];let f=Math.floor(Math.log(i)/Math.log(u));f=f<0?0:f;const h=Number.parseFloat((i/u**f).toFixed(l));return h?`${h.toFixed(2)} ${s[f]}`:"0"},fr={main:"_main_1isor_18",percent:"_percent_1isor_25",name:"_name_1isor_30",nameText:"_nameText_1isor_41",overview:"_overview_1isor_49",core:"_core_1isor_53"},Ul=Q.memo(({value:i,max:l=100,low:u=60,optimum:s,high:f=80})=>_.jsx("meter",{className:fr.core,high:f,low:u,max:l,optimum:s,value:i})),bx=({title:i,name:l="",value:u,max:s,isCapacity:f,percentTag:h="%",percent:p,percentRender:b,progressPercent:y})=>{const z=Q.useCallback(O=>{O.preventDefault(),O.stopPropagation();const H=i||l;bo.open(H),!(i?.length??!1)&&navigator.clipboard.writeText(l)},[l,i]),D=s===0||u===0?0:u/s*100,x=f?`${kn(u)} / ${kn(s)}`:`${u.toFixed(1)}${h} / ${s}${h}`;return _.jsxs("div",{className:fr.main,title:i,children:[_.jsx("div",{className:fr.percent,children:b??`${(p??D).toFixed(1)}%`}),_.jsx("button",{className:fr.name,onClick:z,title:l,type:"button",children:_.jsx("div",{className:fr.nameText,children:l})}),_.jsx("div",{className:fr.overview,children:x}),_.jsx(Ul,{value:y??D})]})},pi=Q.memo(bx),_x={main:"_main_fajqi_1"},Sx=({totalMarks:i,total:l})=>_.jsx("div",{className:_x.main,children:_.jsx(Ul,{high:i*.7,low:i*.5,max:i,optimum:i,value:l})}),Tx=({js:i,dom:l,canvas:u,date:s})=>{const f=i+l+u,h=i.toLocaleString(),p=l.toLocaleString(),b=u.toLocaleString(),y=f.toLocaleString(),z=Kt("{{js}} (JS) + {{dom}} (DOM) + {{canvas}} (Canvas) = {{total}}",{js:h,dom:p,canvas:b,total:y}),D=_.jsx("span",{className:jl.sign,children:"+"}),x=O=>{O.preventDefault(),O.stopPropagation(),Og(z)};return _.jsxs("button",{className:jl.marks,onClick:x,title:N("Touch to copy marks"),type:"button",children:[_.jsx(To,{rt:"JS",ruby:h}),D,_.jsx(To,{rt:"DOM",ruby:p}),D,_.jsx(To,{rt:"Canvas",ruby:b}),D,_.jsx("span",{className:jl.sign,children:"="}),_.jsx(To,{isResult:!0,rt:s||"",ruby:y})]})},Eg=({ua:i,header:l,marks:u,maxMarks:s,date:f})=>{const{js:h,dom:p,canvas:b}=u;return _.jsxs("div",{className:jl.main,children:[_.jsx("div",{className:jl.header,title:i,children:l}),_.jsx(Tx,{canvas:b,date:f,dom:p,js:h}),_.jsx(Sx,{total:h+p+b,totalMarks:s})]})},Ox={browsers:"_browsers_9r2wb_5"},Fd={button:"_button_1shxn_25",icon:"_icon_1shxn_49"},Jt={Error:"error",Loading:"loading",Warning:"warning",Pointer:"pointer"},wg=({status:i})=>_.jsx("span",{className:Fd.icon,"data-status":i,children:{[Jt.Error]:_.jsx(ux,{}),[Jt.Loading]:_.jsx(rx,{}),[Jt.Warning]:_.jsx(sx,{}),[Jt.Pointer]:_.jsx(lx,{})}[i]??null}),Wu=({status:i=Jt.Pointer,children:l,...u})=>_.jsxs("button",{className:Fd.button,type:"button",...u,children:[_.jsx(wg,{status:i}),l]}),xg=({status:i=Jt.Pointer,children:l,...u})=>_.jsxs("a",{className:Fd.button,"data-link":!0,...u,children:[_.jsx(wg,{status:i}),l]});st({enforceActions:"observed"});let Ex=class{benchmarking=!1;maxMarks=0;browsers=[];constructor(){vt(this)}setMaxMarks=l=>{this.maxMarks=l};setBrowsers=l=>{this.browsers=l};setBrowser=(l,u)=>{const s=this.browsers.findIndex(f=>f.id===l);s!==-1&&(this.browsers[s]=u)};setBenchmarking=l=>{this.benchmarking=l}};const Ag=new Ex;let wx=class{getRndint=(l,u)=>Math.floor(Math.random()*(u-l+1))+l;runJs=()=>{let u=0;const s=performance.now();for(;;){const f=new Array(1e3).fill(0).map((h,p)=>p);if(f.sort(()=>Math.random()-.5),f.sort((h,p)=>h-p),u++,performance.now()-s>1e3)break}return u};runDom=()=>{let u=0;const s=window.innerWidth,f=window.innerHeight,h=performance.now(),p=document.createElement("div");for(p.style.cssText=`
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
`,document.body.appendChild(p);;){for(let y=0;y<100;y++){const z=document.createElement("div");z.className="benchmark-dom",z.style.position="fixed",z.style.left="0px",z.style.top="0px",z.style.width=`${this.getRndint(50,s)}px`,z.style.height=`${this.getRndint(50,f)}px`,z.style.border="1px solid green",p.appendChild(z)}const b=document.querySelectorAll(".benchmark-dom");for(const y of Array.from(b))y.style.borderColor="red";for(;p.firstChild;)p.removeChild(p.firstChild);if(u++,performance.now()-h>1e3)break}return document.body.removeChild(p),u};runCanvas=()=>{let u=0;const s=document.createElement("canvas"),f=window.innerWidth,h=window.innerHeight;s.width=f,s.height=h,s.style.position="fixed",s.style.top="0px",s.style.left="0px",document.body.appendChild(s);const p=s.getContext("2d"),b=performance.now();for(;;){for(let y=0;y<100;y++){p.clearRect(0,0,s.width,s.height);const z=s.width/2,D=s.height/2,x=s.width/2;p.lineWidth=this.getRndint(1,5),p.strokeStyle="red",p.lineCap="round",p.beginPath(),p.moveTo(z-x,D-x),p.lineTo(z+x,D+x),p.rotate(this.getRndint(0,360)),p.stroke(),p.beginPath(),p.moveTo(z+x,D-x),p.lineTo(z-x,D+x),p.rotate(this.getRndint(0,360)),p.stroke()}if(u++,performance.now()-b>1e3)break}return p.clearRect(0,0,s.width,s.height),document.body.removeChild(s),u}};const eh=new wx,xx=ve(()=>{const[i,l]=Q.useState(!1),{setMaxMarks:u,maxMarks:s}=Ag,[f,h]=Q.useState({js:0,dom:0,canvas:0}),p=Q.useCallback(z=>{if(z.preventDefault(),z.stopPropagation(),i||!window.confirm(N("Running the benchmark may freeze the browser interface for a few seconds. Do you want to continue?")))return;l(!0);const D={js:eh.runJs(),dom:eh.runDom(),canvas:eh.runCanvas()};l(!1),h(D);const x=Object.values(D).reduce((O,H)=>O+H,0);x>s&&u(x)},[i,s,u]),b=new Date,y=_.jsx(Wu,{disabled:i,onClick:p,status:i?Jt.Loading:Jt.Pointer,children:N("Benchmark my browser")});return _.jsx(Eg,{date:`${b.getFullYear()}-${b.getMonth()+1}-${b.getDate()}`,header:y,marks:f,maxMarks:s})}),Ax=ve(()=>{const[i,l]=Q.useState(!0),[u,s]=Q.useState(!1),{browsers:f,setBrowsers:h,setMaxMarks:p,maxMarks:b}=Ag;Q.useEffect(()=>{(async()=>{l(!0);const{data:D,status:x}=await Un("browserBenchmarks");if(l(!1),!D?.length||x!==ia){s(!0);return}s(!1);let O=0;h(D.map(H=>(H.total=H.detail?Object.values(H.detail).reduce((Y,F)=>Y+F,0):0,H.total>O&&(O=H.total),H)).toSorted((H,Y)=>(Y?.total??0)-(H?.total??0))),p(O)})()},[h,p]);const y=f.map(({name:z,version:D,ua:x,detail:O,date:H})=>{if(!O)return null;const{js:Y=0,dom:F=0,canvas:ne=0}=O;return _.jsx(Eg,{date:H,header:`${z}/v${D}`,marks:{js:Y,dom:F,canvas:ne},maxMarks:b,ua:x},z)});return _.jsxs("div",{className:Ox.browsers,children:[_.jsx(xx,{}),i?[...new Array(5)].map(()=>_.jsx(Zd,{},Math.random())):y,u&&_.jsx(Kd,{children:N("Can not fetch marks data from GitHub.")})]})}),Rx=Q.memo(()=>_.jsxs(hn,{id:Lu.id,title:N("Browser Benchmark"),children:[_.jsx(_g,{items:[{id:"browserBenchmarkTos",text:N("Different versions cannot be compared, and different time clients have different loads, just for reference.")}]}),_.jsx(Ax,{})]})),Rg={main:"_main_1ygx7_18",link:"_link_1ygx7_35"},pn=({id:i,title:l})=>_.jsx("a",{className:Rg.link,href:`#${i}`,children:l},i),zx=()=>_.jsx(pn,{id:Lu.id,title:N("Browser bench")}),Dx={id:Lu.id,content:Rx,nav:zx},th={main:"_main_11zmy_14",label:"_label_11zmy_24",content:"_content_11zmy_34"},mn=({label:i="",title:l="",minWidth:u=4,maxWidth:s=8,children:f})=>{const h={"--min-width":`${u}rem`,"--max-width":`${s}rem`};return _.jsxs("div",{className:th.main,style:h,children:[!!i&&_.jsx("div",{className:th.label,title:l,children:i}),_.jsx("div",{className:th.content,children:f})]})},Cx={main:"_main_z8p86_1"},Fu=({minWidth:i=16,...l})=>{const u={"--min-width":`${i}rem`};return _.jsx("div",{className:Cx.main,style:u,...l})},Mx={main:"_main_xo4z4_2"},kl=({isEnable:i,text:l=""})=>_.jsx("div",{className:Mx.main,"data-error":!i||void 0,"data-icon":!l||void 0,"data-ok":i||void 0,children:l}),jx=Q.memo(ve(()=>{const{pollData:i}=eg,l=[["SQLite3",i?.sqlite3??!1],["MySQLi client",i?.mysqliClientVersion??!1],["Mongo",i?.mongo??!1],["MongoDB",i?.mongoDb??!1],["PostgreSQL",i?.postgreSql??!1],["Paradox",i?.paradox??!1],["MS SQL",i?.msSql??!1],["PDO",i?.pdo??!1]];return _.jsx(hn,{id:Vu.id,title:N("Database"),children:_.jsx(Fu,{minWidth:14,children:l.map(([u,s])=>_.jsx(mn,{label:u,maxWidth:7,minWidth:4,children:_.jsx(kl,{isEnable:!!s,text:s})},u))})})})),Ux=()=>_.jsx(pn,{id:Vu.id,title:N("DB")}),kx={id:Vu.id,content:jx,nav:Ux},Nx={main:"_main_b4lx8_1"},Hx=ve(()=>{const{pollData:i}=Md,l=i?.items??[];return l.length?_.jsx(hn,{id:$u.id,title:N("Disk Usage"),children:_.jsx("div",{className:Nx.main,children:l.map(({id:u,free:s,total:f})=>_.jsx(pi,{isCapacity:!0,max:f,name:u,value:f-s},u))})}):null}),Lx=ve(()=>{const{pollData:i}=Md;return(i?.items??[]).length?_.jsx(pn,{id:$u.id,title:N("Disk")}):null}),Bx={id:$u.id,content:Hx,nav:Lx},zg=ve(({ip:i})=>{const[l,u]=Q.useState(!1),[s,f]=Q.useState(null),h=Q.useCallback(async p=>{if(p.preventDefault(),p.stopPropagation(),l)return;u(!0);const{data:b,status:y}=await Un(`locationIpv4&ip=${i}`);if(u(!1),b&&y===ia){f(b);return}bo.open(N("Can not fetch location."))},[i,l]);return _.jsx(Wu,{onClick:h,status:l?Jt.Loading:Jt.Pointer,children:s?Object.values(s).filter(Boolean).join(", "):N("Click to fetch")})}),Dg=i=>{const[l,u]=Q.useState({ip:"",msg:N("Loading..."),isLoading:!0});return Q.useEffect(()=>{(async()=>{const f=await fetch(`https://ipv${i}.inn-studio.com/ip/?json`);await f.json().catch(()=>{u({ip:"",msg:N("Not support"),isLoading:!1})}).then(h=>{if(h?.ip&&f.status===ia){u({ip:h.ip,msg:"",isLoading:!1});return}u({ip:"",msg:N("Can not fetch IP"),isLoading:!1})})})()},[i]),l},Vx={main:"_main_mc2kq_1"},dr=i=>_.jsx("div",{className:Vx.main,...i}),$x=ve(()=>{const{pollData:i}=Pd,{ip:l,msg:u,isLoading:s}=Dg(4),{ip:f,msg:h,isLoading:p}=Dg(6);let b="",y="";s?b=u:l?b=l:i?.ipv4?b=i.ipv4:b=u,p?y=h:f?y=f:i?.ipv6?y=i.ipv6:y=h;const z=[[N("IPv4"),b],[N("IPv6"),y],[N("Location (IPv4)"),_.jsx(zg,{ip:b},"myLocalIpv4")],[N("Browser UA"),navigator.userAgent],[N("JS Browser languages"),navigator.languages.join(",")],[N("PHP Browser languages"),i?.phpLanguage]];return i?_.jsx(hn,{id:Pu.id,title:N("My Info"),children:_.jsx(dr,{children:z.map(([D,x])=>_.jsx(mn,{label:D,children:x},D))})}):null}),Px=ve(()=>{const{pollData:i}=Pd;return i?_.jsx(pn,{id:Pu.id,title:N("Mine")}):null}),qx={id:Pu.id,content:$x,nav:Px};function Cg(i){const l=Q.useRef(null);return Q.useEffect(()=>{l.current=i},[i]),l.current}const Gx={container:"_container_1i47d_2"},Oo={main:"_main_1cyw0_17",id:"_id_1cyw0_24",type:"_type_1cyw0_29",rx:"_rx_1cyw0_35",tx:"_tx_1cyw0_36",rateRx:"_rateRx_1cyw0_57",rateTx:"_rateTx_1cyw0_58"},Mg=({id:i,totalRx:l=0,rateRx:u=0,totalTx:s=0,rateTx:f=0})=>i?_.jsxs("div",{className:Oo.main,children:[_.jsx("div",{className:Oo.id,children:i}),_.jsxs("div",{className:Oo.rx,children:[_.jsx("div",{className:Oo.type,children:Kt(N("Recived: {{total}}"),{total:kn(l)})}),_.jsxs("div",{className:Oo.rateRx,children:[kn(u),"/s"]})]}),_.jsxs("div",{className:Oo.tx,children:[_.jsx("div",{className:Oo.type,children:Kt(N("Sent: {{total}}"),{total:kn(s)})}),_.jsxs("div",{className:Oo.rateTx,children:[kn(f),"/s"]})]})]}):null,Yx=ve(()=>{const{sortNetworks:i,networksCount:l,timestamp:u}=qd,s=Cg({items:i,timestamp:u});if(!l)return null;const f=u-(s?.timestamp||u);return _.jsx(hn,{id:qu.id,title:N("Network Stats"),children:_.jsx("div",{className:Gx.container,children:i.map(({id:h,rx:p,tx:b})=>{if(!(p||b))return null;const y=(s?.items||i).find(x=>x.id===h),z=y?.rx||0,D=y?.tx||0;return _.jsx(Mg,{id:h,rateRx:(p-z)/f,rateTx:(b-D)/f,totalRx:p,totalTx:b},h)})})})}),Xx=ve(()=>{const{networksCount:i}=qd;return i?_.jsx(pn,{id:qu.id,title:N("Network")}):null}),Ix={id:qu.id,content:Yx,nav:Xx},Qx={main:"_main_zmhfm_1"},mi={main:"_main_1xqpo_13",label:"_label_1xqpo_20",meter:"_meter_1xqpo_27",usage:"_usage_1xqpo_33",group:"_group_1xqpo_38",groupItem:"_groupItem_1xqpo_45"},Zx=({load:i,title:l})=>_.jsx("div",{className:mi.groupItem,title:l,children:i.toFixed(2)}),Kx=({sysLoad:i})=>{const l=[1,5,15],u=i.map((s,f)=>({id:`${l[f]}minAvg`,load:s,text:Kt(N("{{minute}} minute average"),{minute:l[f]})}));return _.jsx("div",{className:mi.group,children:u.map(({id:s,load:f,text:h})=>_.jsx("div",{className:mi.groupItem,title:h,children:f.toFixed(2)},s))})},Jx=ve(()=>{const{sysLoad:i,cpuUsage:l}=_o,u=l.user+l.idle+l.sys,s=`
user: ${(l.user/u*100).toFixed(2)}%
idle: ${(l.idle/u*100).toFixed(2)}%
sys: ${(l.sys/u*100).toFixed(2)}%
`;return _.jsxs("div",{className:mi.main,children:[_.jsx("div",{className:mi.label,children:N("System load")}),_.jsx(Kx,{sysLoad:i}),_.jsx("div",{className:mi.usage,title:s,children:Kt(N("{{usage}}% CPU usage"),{usage:l.usage})}),_.jsx("div",{className:mi.meter,children:_.jsx(Ul,{value:l.usage>100?100:l.usage})})]})}),Wx={sysLoad:"_sysLoad_mqy5s_1"},Nl={main:"_main_66xvd_1",meter:"_meter_66xvd_10",label:"_label_66xvd_16",overview:"_overview_66xvd_20",percent:"_percent_66xvd_31"},ec=({children:i,percent:l})=>_.jsxs("div",{className:Nl.main,children:[i,_.jsxs("div",{className:Nl.percent,children:[l,"%"]}),_.jsx("div",{className:Nl.meter,children:_.jsx(Ul,{max:100,value:l})})]}),tc=i=>_.jsx("div",{className:Nl.label,...i}),nc=i=>_.jsx("div",{className:Nl.overview,...i}),Fx=({items:i})=>_.jsx("div",{className:Wx.sysLoad,children:i.map(l=>_.jsx(Zx,{load:l},Math.random()))}),eA=Q.memo(({sysLoad:i,cpuUsage:l})=>{const{user:u,idle:s,sys:f,usage:h}=l,p=u+s+f,b=`
user: ${(u/p*100).toFixed(2)}%
idle: ${(s/p*100).toFixed(2)}%
sys: ${(f/p*100).toFixed(2)}%
`;return _.jsxs(ec,{percent:h,children:[_.jsx(tc,{children:N("CPU")}),_.jsx(nc,{title:b,children:_.jsx(Fx,{items:i})})]})}),jg={main:"_main_1gdd5_1",item:"_item_1gdd5_12"},tA=Q.memo(({id:i,free:l,total:u})=>_.jsx("div",{className:jg.item,children:_.jsxs(ec,{percent:u?Math.round(l/u*100):0,children:[_.jsx(tc,{children:`🖴 ${i}`}),_.jsx(nc,{children:`${kn(l)} / ${kn(u)}`})]})},i)),nA=({data:i})=>{const l=i?.items??[];return _.jsx("div",{className:jg.main,children:l.map(({id:u,free:s,total:f})=>_.jsx(tA,{free:s,id:u,total:f},u))})},aA={main:"_main_mc2kq_1"},oA=({data:i})=>{const{networks:l,timestamp:u}=i,s=Cg({items:l,timestamp:u}),f=u-(s?.timestamp||u);return _.jsx("div",{className:aA.main,children:l.map(({id:h,rx:p,tx:b})=>{if(!(p||b))return null;const y=(s?.items||l).find(x=>x.id===h),z=y?.rx||0,D=y?.tx||0;return _.jsx(Mg,{id:h,rateRx:(p-z)/f,rateTx:(b-D)/f,totalRx:p,totalTx:b},h)})})},Ug={main:"_main_18siw_1",name:"_name_18siw_6"},iA=Q.memo(({data:i})=>{const{value:l,max:u}=i,s=u?Math.round(l/u*100):0;return _.jsxs(ec,{percent:s,children:[_.jsx(tc,{children:`🐏 ${N("Ram")}`}),_.jsx(nc,{children:`${kn(l)} / ${kn(u)}`})]})}),rA=Q.memo(({data:i})=>{const{value:l,max:u}=i,s=u?Math.round(l/u*100):0;return _.jsxs(ec,{percent:s,children:[_.jsx(tc,{children:`🐏 ${N("Swap")}`}),_.jsx(nc,{children:`${kn(l)} / ${kn(u)}`})]})}),lA=Q.memo(({id:i})=>{const[l,u]=Q.useState(!0),[s,f]=Q.useState(0),[h,p]=Q.useState(null);Q.useEffect(()=>{let Y,F=!0;const ne=async()=>{try{const{data:ie,status:Qe}=await Un(`nodes&nodeId=${i}`);if(l&&u(!1),!ie||Qe!==ia){f(Qe);return}p(ie)}finally{F&&(Y=setTimeout(ne,2e3))}};return ne(),()=>{F=!1,clearTimeout(Y)}},[i,l]);const b=h?.serverStatus??null,y=h?.diskUsage??null,z=h?.networkStats??null,D=h?.serverStatus?.memRealUsage??null,x=h?.serverStatus?.swapUsage??null,O=b?.sysLoad??[],H=b?.cpuUsage??null;return _.jsxs("div",{className:Ug.main,children:[_.jsx("header",{className:Ug.name,children:i}),s!==0&&_.jsx(Kd,{children:Kt(N("Error: {{error}}"),{error:s})}),l&&_.jsx(Zd,{height:10}),!l&&b&&_.jsxs(_.Fragment,{children:[H?_.jsx(eA,{cpuUsage:H,sysLoad:O}):null,D?_.jsx(iA,{data:D}):null,x?_.jsx(rA,{data:x}):null,y?_.jsx(nA,{data:y}):null,z?_.jsx(oA,{data:z}):null]})]})}),sA=ve(()=>{const{pollData:i}=Gd,l=i?.nodesIds??[];return l.length?_.jsx(hn,{id:Gu.id,title:N("Nodes"),children:_.jsx("div",{className:Qx.main,children:l.map(u=>_.jsx(lA,{id:u},u))})}):null}),uA=ve(()=>{const{pollData:i}=Gd;return(i?.nodesIds??[]).length?_.jsx(pn,{id:Gu.id,title:N("Nodes")}):null}),cA={id:Gu.id,content:sA,nav:uA},fA={main:"_main_uj7jp_16"},nh=({keyword:i})=>_.jsx("a",{className:fA.main,href:`https://www.google.com/search?q=php+${encodeURIComponent(i)}`,rel:"nofollow noreferrer",target:"_blank",children:i}),dA=Q.memo(ve(()=>{const{pollData:i}=Yd;if(!i)return null;const l=[["Redis",!!i.redis],["SQLite3",!!i.sqlite3],["Memcache",!!i.memcache],["Memcached",!!i.memcached],["Opcache",!!i.opcache],[N("Opcache enabled"),!!i.opcacheEnabled],[N("Opcache JIT enabled"),!!i.opcacheJitEnabled],["Swoole",!!i.swoole],["Image Magick",!!i.imagick],["Graphics Magick",!!i.gmagick],["Exif",!!i.exif],["Fileinfo",!!i.fileinfo],["SimpleXML",!!i.simplexml],["Sockets",!!i.sockets],["MySQLi",!!i.mysqli],["Zip",!!i.zip],["Multibyte String",!!i.mbstring],["Phalcon",!!i.phalcon],["Xdebug",!!i.xdebug],["Zend Optimizer",!!i.zendOptimizer],["ionCube",!!i.ionCube],["Source Guardian",!!i.sourceGuardian],["LDAP",!!i.ldap],["cURL",!!i.curl]];l.slice().sort((s,f)=>{const h=s[0].toLowerCase(),p=f[0].toLowerCase();return h<p?-1:h>p?1:0});const u=i.loadedExtensions||[];return u.slice().sort((s,f)=>{const h=s.toLowerCase(),p=f.toLowerCase();return h<p?-1:h>p?1:0}),_.jsxs(hn,{id:Yu.id,title:N("PHP Extensions"),children:[_.jsx(Fu,{minWidth:14,children:l.map(([s,f])=>_.jsx(mn,{label:s,maxWidth:10,minWidth:4,children:_.jsx(kl,{isEnable:f})},s))}),_.jsx(dr,{children:!!u.length&&_.jsx(mn,{label:N("Loaded extensions"),maxWidth:6,minWidth:4,children:u.map(s=>_.jsx(nh,{keyword:s},s))})})]})})),hA=ve(()=>{const{pollData:i}=Yd;return i?_.jsx(pn,{id:Yu.id,title:N("PHP Ext")}):null}),pA={id:Yu.id,content:dA,nav:hA},mA=ve(()=>{const{pollData:i,latestPhpVersion:l,setLatestPhpVersion:u}=Hu;Q.useEffect(()=>{(async()=>{const{data:p,status:b}=await Un("latestPhpVersion");p?.version&&b===ia&&u(p.version)})()},[u]);const s=i?.phpVersion??"",f=dg(s,l);return _.jsx(xg,{href:"https://www.php.net/",title:N("Visit PHP.net Official website"),children:f===-1?` ${Kt(N("{{oldVersion}} (Latest: {{latestPhpVersion}})"),{oldVersion:s,latestPhpVersion:l})}`:s})}),vA=Q.memo(ve(()=>{const{pollData:i}=Hu;if(!i)return null;const l=[["PHP info",_.jsx(xg,{href:ng("phpInfoDetail"),target:"_blank",children:N("Detail")},"phpInfoDetail")],[N("Version"),_.jsx(mA,{},"phpVersion")]],u=[[N("SAPI interface"),i?.sapi],[N("Display errors"),_.jsx(kl,{isEnable:i?.displayErrors},"displayErrors")],[N("Error reporting"),i.errorReporting],[N("Max memory limit"),i.memoryLimit],[N("Max POST size"),i.postMaxSize],[N("Max upload size"),i.uploadMaxFilesize],[N("Max input variables"),i.maxInputVars],[N("Max execution time"),i.maxExecutionTime],[N("Timeout for socket"),i.defaultSocketTimeout],[N("Treatment URLs file"),_.jsx(kl,{isEnable:i.allowUrlFopen},"allowUrlFopen")],[N("SMTP support"),_.jsx(kl,{isEnable:i.smtp},"smtp")]],{disableFunctions:s,disableClasses:f}=i;s.slice().sort(),f.slice().sort();const h=[[N("Disabled functions"),s.length?s.map(p=>_.jsx(nh,{keyword:p},p)):"-"],[N("Disabled classes"),f.length?f.map(p=>_.jsx(nh,{keyword:p},p)):"-"]];return _.jsxs(hn,{id:Xu.id,title:N("PHP Information"),children:[_.jsxs(Fu,{children:[l.map(([p,b])=>_.jsx(mn,{label:p,children:b},p)),u.map(([p,b])=>_.jsx(mn,{label:p,children:b},p))]}),_.jsx(dr,{children:h.map(([p,b])=>_.jsx(mn,{label:p,maxWidth:7,minWidth:4,children:b},p))})]})})),yA=ve(()=>{const{pollData:i}=Hu;return i?_.jsx(pn,{id:Xu.id,title:N("PHP Info")}):null}),gA={id:Xu.id,content:vA,nav:yA},bA=i=>{const u=i.reduce((h,p)=>h+p,0)/i.length,f=i.map(h=>{const p=h-u;return p*p}).reduce((h,p)=>h+p,0)/i.length;return Math.sqrt(f)};st({enforceActions:"observed"});let _A=class{isPing=!1;isPingServerToBrowser=!1;isPingServerToServer=!1;serverToBrowserPingItems=[];serverToServerPingItems=[];constructor(){vt(this)}setIsPing=l=>{this.isPing=l};setIsPingServerToBrowser=l=>{this.isPingServerToBrowser=l};setIsPingServerToServer=l=>{this.isPingServerToServer=l};setServerToBrowserPingItems=l=>{this.serverToBrowserPingItems=l};setServerToServerPingItems=l=>{this.serverToServerPingItems=l};addServerToBrowserPingItem=l=>{this.serverToBrowserPingItems.push(l)};addServerToServerPingItem=l=>{this.serverToServerPingItems.push(l)}};const ah=new _A,oh={itemContainer:"_itemContainer_y6c35_12",resultContainer:"_resultContainer_y6c35_27",result:"_result_y6c35_27"},SA=ve(()=>{const{serverToBrowserPingItems:i}=ah,l=i.length,u=i.map(({time:b})=>b),s=l?(u.reduce((b,y)=>b+y,0)/l).toFixed(2):0,f=l?Math.max(...u):0,h=l?Math.min(...u):0,p=bA(u).toFixed(2);return _.jsx("div",{className:oh.result,children:Kt(N("{{times}} times, min/avg/max/mdev = {{min}}/{{avg}}/{{max}}/{{mdev}} ms"),{times:l,min:h,max:f,avg:s,mdev:p})})}),TA=ve(({refContainer:i})=>{const{serverToBrowserPingItems:l}=ah,u=l.length;return _.jsx(mn,{label:N("Results"),children:_.jsxs("div",{className:oh.resultContainer,children:[!u&&"-",!!u&&_.jsx("ul",{className:oh.itemContainer,ref:i,children:l.map(({id:s,time:f})=>_.jsx("li",{children:`${f} ms`},s))}),!!u&&_.jsx(SA,{})]})})}),OA=ve(()=>{const{setIsPing:i,setIsPingServerToBrowser:l,addServerToBrowserPingItem:u,isPing:s,isPingServerToBrowser:f}=ah,h=Q.useRef(null),p=Q.useRef(0),b=1e3,y=1e3,z=100,D=Q.useCallback(async()=>{const H=Date.now(),{data:Y,status:F}=await Un("ping");if(Y?.time&&F===ia){const{id:ne,time:ie}=Y,Qe=Date.now(),Pe=ie*b;u({id:ne,time:Math.floor(Qe-H-Pe)}),setTimeout(()=>{if(!h.current)return;const ke=h.current.scrollTop,Ze=h.current.scrollHeight;ke<Ze&&(h.current.scrollTop=Ze)},z)}},[u]),x=Q.useCallback(async()=>{await D(),p.current=window.setTimeout(async()=>{await x()},y)},[D]),O=Q.useCallback(async()=>{if(s||f){i(!1),l(!1),clearTimeout(p.current);return}i(!0),l(!0),await x()},[s,f,x,i,l]);return _.jsxs(dr,{children:[_.jsx(mn,{label:N("Server ⇄ Browser"),children:_.jsx(Wu,{onClick:O,status:s?Jt.Loading:Jt.Pointer,children:N(s?"Stop ping":"Start ping")})}),_.jsx(TA,{refContainer:h})]})}),EA=Q.memo(()=>_.jsx(hn,{id:Bu.id,title:N("Ping"),children:_.jsx(OA,{})})),wA=()=>_.jsx(pn,{id:Bu.id,title:N("Ping")}),xA={id:Bu.id,content:EA,nav:wA},AA={servers:"_servers_1dtle_5"},RA={main:"_main_fajqi_1"},zA=({totalMarks:i,total:l})=>_.jsx("div",{className:RA.main,children:_.jsx(Ul,{high:i*.7,low:i*.5,max:i,optimum:i,value:l})}),Eo={main:"_main_1e6oe_13",header:"_header_1e6oe_22",link:"_link_1e6oe_28",marks:"_marks_1e6oe_46",sign:"_sign_1e6oe_62"},DA=({cpu:i,read:l,write:u,date:s})=>{const f=i+l+u,h=i.toLocaleString(),p=l.toLocaleString(),b=u.toLocaleString(),y=f.toLocaleString(),z=Kt("{{cpu}} (CPU) + {{read}} (Read) + {{write}} (Write) = {{total}}",{cpu:h,read:p,write:b,total:y}),D=_.jsx("span",{className:Eo.sign,children:"+"}),x=O=>{O.preventDefault(),O.stopPropagation(),Og(z)};return _.jsxs("button",{className:Eo.marks,onClick:x,title:N("Touch to copy marks"),type:"button",children:[_.jsx(To,{rt:"CPU",ruby:h}),D,_.jsx(To,{rt:N("Read"),ruby:p}),D,_.jsx(To,{rt:N("Write"),ruby:b}),_.jsx("span",{className:Eo.sign,children:"="}),_.jsx(To,{isResult:!0,rt:s||"",ruby:y})]})},kg=({header:i,marks:l,maxMarks:u,date:s})=>{const{cpu:f,read:h,write:p}=l;return _.jsxs("div",{className:Eo.main,children:[_.jsx("div",{className:Eo.header,children:i}),_.jsx(DA,{cpu:f,date:s,read:h,write:p}),_.jsx(zA,{total:f+h+p,totalMarks:u})]})};st({enforceActions:"observed"});let CA=class{benchmarking=!1;maxMarks=0;servers=[];constructor(){vt(this)}setMaxMarks=l=>{this.maxMarks=l};setServers=l=>{this.servers=l};setServer=(l,u)=>{const s=this.servers.findIndex(f=>f.id===l);s!==-1&&(this.servers[s]=u)};setBenchmarking=l=>{this.benchmarking=l}};const Ng=new CA,MA=ve(()=>{const[i,l]=Q.useState(!1),{setMaxMarks:u,maxMarks:s}=Ng,[f,h]=Q.useState({cpu:0,read:0,write:0}),p=Q.useCallback(async z=>{if(z.preventDefault(),z.stopPropagation(),i)return;l(!0);const{data:D,status:x}=await Un("benchmarkPerformance");if(l(!1),x===ia){if(D?.marks){h(D.marks);const O=Object.values(D.marks).reduce((H,Y)=>H+Y,0);O>s&&u(O);return}bo.open(N("Network error, please try again later."));return}if(D?.seconds&&x===Aw){bo.open(Kt(N("Please wait {{seconds}}s"),{seconds:D.seconds}));return}bo.open(N("Network error, please try again later."))},[i,s,u]),b=new Date,y=_.jsx(Wu,{onClick:p,status:i?Jt.Loading:Jt.Pointer,children:N("Benchmark my server")});return _.jsx(kg,{date:`${b.getFullYear()}-${b.getMonth()+1}-${b.getDate()}`,header:y,marks:f,maxMarks:s})}),jA=ve(()=>{const[i,l]=Q.useState(!0),[u,s]=Q.useState(!1),{servers:f,setServers:h,setMaxMarks:p,maxMarks:b}=Ng;Q.useEffect(()=>{(async()=>{l(!0);const{data:D,status:x}=await Un("benchmarkServers");if(l(!1),!D?.length||x!==ia){s(!0);return}s(!1);let O=0;h(D.map(H=>(H.total=H.detail?Object.values(H.detail).reduce((Y,F)=>Y+F,0):0,H.total>O&&(O=H.total),H)).toSorted((H,Y)=>(Y?.total??0)-(H?.total??0))),p(O)})()},[h,p]);const y=f.map(({name:z,url:D,date:x,probeUrl:O,binUrl:H,detail:Y})=>{if(!Y)return null;const{cpu:F=0,read:ne=0,write:ie=0}=Y,Qe=O?_.jsx("a",{className:Eo.link,href:O,rel:"noreferrer",target:"_blank",title:N("Visit probe page"),children:_.jsx(ix,{})}):"",Pe=H?_.jsx("a",{className:Eo.link,href:H,rel:"noreferrer",target:"_blank",title:N("Download speed test"),children:_.jsx(ox,{})}):"",ke=_.jsx("a",{className:Eo.link,href:D,rel:"noreferrer",target:"_blank",title:N("Visit the official website"),children:z});return _.jsx(kg,{date:x,header:_.jsxs(_.Fragment,{children:[ke,Qe,Pe]}),marks:{cpu:F,read:ne,write:ie},maxMarks:b},z)});return _.jsxs("div",{className:AA.servers,children:[_.jsx(MA,{}),i?[...new Array(5)].map(()=>_.jsx(Zd,{},Math.random())):y,u&&_.jsx(Kd,{children:N("Can not fetch marks data from GitHub.")})]})}),UA=Q.memo(()=>_.jsxs(hn,{id:Iu.id,title:N("Server Benchmark"),children:[_.jsx(_g,{items:[{id:"serverBenchmarkTos",text:N("Different versions cannot be compared, and different time servers have different loads, just for reference.")}]}),_.jsx(jA,{})]})),kA=()=>_.jsx(pn,{id:Iu.id,title:N("Server bench")}),NA={id:Iu.id,content:UA,nav:kA},HA=ve(({serverUptime:i,serverTime:l})=>{const{days:u,hours:s,mins:f,secs:h}=i,p=Kt(N("{{days}}d {{hours}}h {{mins}}min {{secs}}s"),{days:u,hours:s,mins:f,secs:h}),b=[[N("Time"),l],[N("Uptime"),p]];return _.jsx(_.Fragment,{children:b.map(([y,z])=>_.jsx(mn,{label:y,maxWidth:6,children:z},y))})}),LA=Q.memo(({cpuModel:i,serverOs:l,scriptPath:u,publicIpv4:s})=>{const f=[[N("Location (IPv4)"),_.jsx(zg,{ip:s},"serverLocalIpv4")],[N("CPU model"),i??N("Unavailable")],[N("OS"),l??N("Unavailable")],[N("Script path"),u??N("Unavailable")]];return _.jsx(dr,{children:f.map(([h,p])=>_.jsx(mn,{label:h,maxWidth:6,children:p},h))})}),BA=Q.memo(({serverName:i,serverSoftware:l,publicIpv4:u,publicIpv6:s,localIpv4:f,localIpv6:h})=>{const p=[[N("Name"),i??N("Unavailable")],[N("Web server"),l??N("Unavailable")],[N("Public IPv4"),u||"-"],[N("Public IPv6"),s||"-"],[N("Local IPv4"),f||"-"],[N("Local IPv6"),h||"-"]];return _.jsx(_.Fragment,{children:p.map(([b,y])=>_.jsx(mn,{label:b,maxWidth:6,children:y},b))})}),VA=ve(()=>{const{pollData:i,publicIpv4:l,publicIpv6:u,setPublicIpv4:s,setPublicIpv6:f}=Xd;return Q.useEffect(()=>{(async()=>{const{data:p,status:b}=await Un("serverPublicIpv4");p?.ip&&b===ia&&s(p.ip)})()},[s]),Q.useEffect(()=>{(async()=>{const{data:p,status:b}=await Un("serverPublicIpv6");p?.ip&&b===ia&&f(p.ip)})()},[f]),i?_.jsxs(hn,{id:Qu.id,title:N("Server Info"),children:[_.jsxs(Fu,{minWidth:20,children:[_.jsx(HA,{serverTime:i.serverTime,serverUptime:i.serverUptime}),_.jsx(BA,{localIpv4:i.localIpv4,localIpv6:i.localIpv6,publicIpv4:l,publicIpv6:u,serverName:i.serverName,serverSoftware:i.serverSoftware})]}),_.jsx(LA,{cpuModel:i.cpuModel,publicIpv4:l,scriptPath:i.scriptPath,serverOs:i.serverOs})]}):null}),$A=ve(()=>{const{pollData:i}=Xd;return i?_.jsx(pn,{id:Qu.id,title:N("Info")}):null}),PA={id:Qu.id,content:VA,nav:$A},Hg={main:"_main_raw5t_1",modules:"_modules_raw5t_6"},qA=ve(()=>{const{max:i,value:l}=_o.memBuffers;return _.jsx(pi,{isCapacity:!0,max:i,name:N("Memory buffers"),title:N("Buffers are in-memory block I/O buffers. They are relatively short-lived. Prior to Linux kernel version 2.4, Linux had separate page and buffer caches. Since 2.4, the page and buffer cache are unified and Buffers is raw disk blocks not represented in the page cache—i.e., not file data."),value:l})}),GA=ve(()=>{const{max:i,value:l}=_o.memCached;return _.jsx(pi,{isCapacity:!0,max:i,name:N("Memory cached"),title:N('Cached memory is memory that Linux uses for disk caching. However, this does not count as "used" memory, since it will be freed when applications require it. Hence you do not have to worry if a large amount is being used.'),value:l})}),YA=ve(()=>{const{max:i,value:l}=_o.memRealUsage;return _.jsx(pi,{isCapacity:!0,max:i,name:N("Memory real usage"),title:N('Linux comes with many commands to check memory usage. The "free" command usually displays the total amount of free and used physical and swap memory in the system, as well as the buffers used by the kernel. The "top" command provides a dynamic real-time view of a running system.'),value:l})}),XA=ve(()=>{const{max:i,value:l}=_o.swapCached;return i?_.jsx(pi,{isCapacity:!0,max:i,name:N("Swap cached"),value:l}):null}),IA=ve(()=>{const{max:i,value:l}=_o.swapUsage;return i?_.jsx(pi,{isCapacity:!0,max:i,name:N("Swap usage"),value:l}):null}),QA=()=>_.jsx(hn,{id:Zu.id,title:N("Server Status"),children:_.jsx("div",{className:Hg.main,children:_.jsxs("div",{className:Hg.modules,children:[_.jsx(Jx,{}),_.jsx(YA,{}),_.jsx(GA,{}),_.jsx(qA,{}),_.jsx(IA,{}),_.jsx(XA,{})]})})}),ZA=ve(()=>{const{pollData:i}=_o;return i?_.jsx(pn,{id:Zu.id,title:N("Info")}):null}),KA={id:Zu.id,content:QA,nav:ZA};st({enforceActions:"observed"});class JA{pollData=null;latestPhpVersion="";constructor(){vt(this)}setPollData=l=>{jn(l,this.pollData)||(this.pollData=l)};setLatestPhpVersion=l=>{this.latestPhpVersion=l}}const ih=new JA,WA=ve(()=>{const{pollData:i}=ih;if(!i?.items?.length)return null;const{items:l}=i;return _.jsx(hn,{id:Ku.id,title:N("Temperature sensor"),children:_.jsx(dr,{children:l.map(({id:u,name:s,celsius:f})=>_.jsx(mn,{title:Kt(N("{{sensor}} temperature"),{sensor:s}),children:_.jsx(pi,{isCapacity:!1,max:150,percentTag:"℃",value:f})},u))})})}),FA=ve(()=>{const{pollData:i}=ih;if(!i?.items?.length)return null;const{items:l}=i;return l.length?_.jsx(pn,{id:Ku.id,title:N("Temperature")}):null}),eR={id:Ku.id,content:WA,nav:FA},Lg={items:[cA,eR,KA,Ix,Bx,xA,PA,gA,pA,kx,NA,Dx,qx]},tR=ve(()=>{const{setSortedModules:i,availableModules:l}=Qd;return Q.useEffect(()=>{const u=vg.getItems(),s=[];for(const f of Lg.items)s.push({id:f.id,priority:Number(u?.[f.id])||Id.indexOf(f.id)});i(s)},[i]),l.length?_.jsx("div",{className:Kw.container,children:l.map(({id:u,content:s})=>_.jsx(s,{},u))}):null}),nR=ve(()=>{const{availableModules:i}=Qd,l=i.map(({id:u,nav:s})=>_.jsx(s,{},u));return _.jsx("div",{className:Rg.main,children:l})}),aR={main:"_main_nuyl9_6"},oR=()=>_.jsx("div",{className:aR.main,children:"Loading..."}),iR=()=>{const[i,l]=Q.useState(!0),{isUpdating:u}=Vd;return Q.useEffect(()=>{let s,f=!0;const h=async()=>{try{if(u)return;const{data:p,status:b}=await Un("poll");p&&b===200?(Bd.setPollData(p),Cd.setPollData(p?.config),Zw.setPollData(p?.userConfig),eg.setPollData(p?.database),Pd.setPollData(p?.myInfo),Hu.setPollData(p?.phpInfo),Md.setPollData(p?.diskUsage),Yd.setPollData(p?.phpExtensions),qd.setPollData(p?.networkStats),_o.setPollData(p?.serverStatus),Xd.setPollData(p?.serverInfo),Gd.setPollData(p?.nodes),ih.setPollData(p?.temperatureSensor)):bo.open(N("Failed to fetch data. Please try again later.")),i&&l(!1)}finally{f&&(s=setTimeout(h,2e3))}};return h(),()=>{f=!1,clearTimeout(s)}},[i,u]),i?_.jsx(oR,{}):_.jsxs(_.Fragment,{children:[_.jsx(Nw,{}),_.jsx(tR,{}),_.jsx(Ow,{}),_.jsx(nR,{}),_.jsx(Iw,{})]})};document.addEventListener("DOMContentLoaded",()=>{document.body.innerHTML="",oO.createRoot(document.body).render(_.jsx(iR,{}))})});

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
  'APP_VERSION' => '9.1.0',
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
  'BROWSER_BENCHMARKS_URLS' => 
  array (
    0 => 'https://raw.githubusercontent.com/kmvan/x-prober/master/browser-benchmarks.json',
    1 => 'https://api.inn-studio.com/download/?id=xprober-browser-benchmarks',
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
use InnStudio\Prober\Components\Bootstrap\Bootstrap;
use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
final class BrowserBenchmarkBrowsersAction
{
    public function render($action)
    {
        if ('browserBenchmarks' !== $action) {
            return;
        }
        $reponse = new RestResponse();
        if (\defined('XPROBER_IS_DEV') && XPROBER_IS_DEV) {
            $reponse
                ->setData($this->getDevItems())
                ->end();
        }
        foreach (ConfigApi::$config['BROWSER_BENCHMARKS_URLS'] as $url) {
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
        $path = Bootstrap::$dir . '/browser-benchmarks.json';
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
namespace InnStudio\Prober\Components\BrowserBenchmark;
class BrowserBenchmarkConstants
{
    const ID = 'browserBenchmark';
}new \InnStudio\Prober\Components\Bootstrap\Bootstrap(__DIR__);