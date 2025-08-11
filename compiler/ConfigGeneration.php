<?php

namespace InnStudio\Prober\Compiler;

final class ConfigGeneration
{
    private $phpConfigPath = '';

    private $configPath = '';

    private $configPathDev = '';

    public function __construct(array $args)
    {
        [
            'phpConfigPath' => $this->phpConfigPath,
            'configPath' => $this->configPath,
            'configPathDev' => $this->configPathDev,
        ] = $args;

        if ( ! is_file($this->configPath)) {
            $this->die("File invalid: {$this->configPath}");
        }

        if ( ! $this->genPhpConfig()) {
            $this->die('Error: can not generate content to dist.');
        }

        $this->copyConfigToTmp();

        $this->die('PHP config file generated successful.', false);
    }

    private function copyConfigToTmp(): bool
    {
        return copy($this->configPath, $this->configPathDev);
    }

    private function genPhpConfig(): bool
    {
        $config = file_get_contents($this->configPath) ?: '';

        if ( ! $config) {
            return false;
        }

        $config = json_decode($config, true);

        if ( ! $config) {
            return false;
        }

        $config = var_export($config, true);
        $configContent = <<<PHP
<?php
/**
 * The file is automatically generated.
 */

namespace InnStudio\\Prober\\Components\\Config;

class ConfigApi
{
    public static \$config = {$config};
}

PHP;

        return (bool) file_put_contents($this->phpConfigPath, $configContent);
    }

    private function die(string $msg, bool $die = true): void
    {
        $msg = "[StyleGeneration] {$msg}\n";

        if ($die) {
            exit($msg);
        }

        echo $msg;
    }
}
