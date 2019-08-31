<?php

namespace InnStudio\Prober\Components\PhpInfo;

use InnStudio\Prober\Components\Events\EventsApi;

class Conf extends PhpInfoConstants
{
    public function __construct()
    {
        EventsApi::on('conf', array($this, 'conf'));
    }

    public function conf(array $conf)
    {
        $conf[$this->ID] = array(
            'version'              => \PHP_VERSION,
            'sapi'                 => \PHP_SAPI,
            'displayErrors'        => (bool) \ini_get('display_errors'),
            'errorReporting'       => (int) \ini_get('error_reporting'),
            'memoryLimit'          => (string) \ini_get('memory_limit'),
            'postMaxSize'          => (string) \ini_get('post_max_size'),
            'uploadMaxFilesize'    => (string) \ini_get('upload_max_filesize'),
            'maxInputVars'         => (int) \ini_get('max_input_vars'),
            'maxExecutionTime'     => (int) \ini_get('max_execution_time'),
            'defaultSocketTimeout' => (int) \ini_get('default_socket_timeout'),
            'allowUrlFopen'        => (bool) \ini_get('allow_url_fopen'),
            'smtp'                 => (bool) \ini_get('SMTP'),
            'disableFunctions'     => \array_filter(\explode(',', (string) \ini_get('disable_functions'))),
            'disableClasses'       => \array_filter(\explode(',', (string) \ini_get('disable_classes'))),
        );

        return $conf;
    }
}
