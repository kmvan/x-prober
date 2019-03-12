<?php

namespace InnStudio\Prober\Compiler;

class ServerBenchmarkGeneration
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
            'BENCHMARKS' => $marks,
        ] = $config;

        $marks = \serialize($marks);

        $configContent = <<<PHP
<?php
/**
 * The file is automatically generated.
 */

namespace InnStudio\Prober\Components\ServerBenchmark;

class ServerBenchmarkMarks
{
    public static \$marks = '{$marks}';
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
