<?php

namespace InnStudio\Prober\Compiler;

class ConfigGeneration
{
    private $phpConfigPath = '';

    private $configPath = '';

    public function __construct(array $args)
    {
        [
            'phpConfigPath' => $this->phpConfigPath,
            'configPath'    => $this->configPath,
        ] = $args;

        if ( ! \is_file($this->configPath)) {
            $this->die("File invalid: {$this->configPath}");
        }

        if ( ! $this->genPhpConfig()) {
            $this->die('Error: can not generate content to dist.');
        }

        $this->die('PHP config file generated successful.', false);
    }

    private function genPhpConfig(): bool
    {
        $config = \file_get_contents($this->configPath) ?: '';

        if ( ! $config) {
            return false;
        }

        $config = \json_decode($config, true);

        if ( ! $config) {
            return false;
        }

        [
            'APP_VERSION'                 => $appVersion,
            'APP_NAME'                    => $appName,
            'APP_URL'                     => $appUrl,
            'AUTHOR_URL'                  => $authorUrl,
            'UPDATE_PHP_URLS'             => $updatePhpUrls,
            'AUTHOR_NAME'                 => $authorName,
            'CHANGELOG_URL'               => $changelogUrl,
            'LATEST_PHP_STABLE_VERSION'   => $latestPhpStableVersion,
            'LATEST_NGINX_STABLE_VERSION' => $latestNginxStableVersion,
        ] = $config;

        $updatePhpUrls = \implode("', '", $updatePhpUrls);

        $configContent = <<<PHP
<?php
/**
 * The file is automatically generated.
 */

namespace InnStudio\Prober\Components\Config;

class ConfigApi
{
    public static \$APP_VERSION                 = '{$appVersion}';
    public static \$APP_NAME                    = '{$appName}';
    public static \$APP_URL                     = '{$appUrl}';
    public static \$AUTHOR_URL                  = '{$authorUrl}';
    public static \$UPDATE_PHP_URLS             = array('{$updatePhpUrls}');
    public static \$AUTHOR_NAME                 = '{$authorName}';
    public static \$CHANGELOG_URL               = '{$changelogUrl}';
    public static \$LATEST_PHP_STABLE_VERSION   = '{$latestPhpStableVersion}';
    public static \$LATEST_NGINX_STABLE_VERSION = '{$latestNginxStableVersion}';
}

PHP;

        return (bool) \file_put_contents($this->phpConfigPath, $configContent);
    }

    private function die(string $msg, bool $die = true): void
    {
        $msg = "[StyleGeneration] {$msg}\n";

        if ($die) {
            die($msg);
        }

        echo $msg;
    }
}
