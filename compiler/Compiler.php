<?php

namespace InnStudio\Prober\Compiler;

use Exception;
use Iterator;

final class Compiler
{
    private $ROOT = '';

    private $BASE_DIR = '';

    private $COMPILE_FILE_PATH = '';

    private $COMPONENTS_DIR = '';

    public function __construct(string $dir)
    {
        $this->ROOT = $dir;
        $this->BASE_DIR = "{$dir}/src";
        $this->COMPONENTS_DIR = "{$this->BASE_DIR}/Components";
        $this->COMPILE_FILE_PATH = $this->isDev() ? "{$dir}/dev/api.php" : "{$dir}/dist/prober.php";

        // generate config
        new ConfigGeneration([
            'phpConfigPath' => "{$this->COMPONENTS_DIR}/Config/ConfigApi.php",
            'configPath' => "{$this->ROOT}/AppConfig.json",
            'configPathDev' => "{$this->ROOT}/dev/AppConfig.json",
        ]);

        echo "Compile starting...\n";

        $code = '';

        if ( ! $this->isDev()) {
            foreach ($this->yieldFiles($this->COMPONENTS_DIR) as $filePath) {
                if (is_dir($filePath) || ! str_contains($filePath, '.php')) {
                    continue;
                }

                $content = $this->getCodeViaFilePath($filePath);
                $code .= $content;
            }
        }

        $preDefineCode = $this->preDefine([
            $this->genTimerCode(),
            $this->genDevMode(),
            $this->genDirPath(),
            $this->genVendorCode(),
        ]);
        $code = "<?php\n{$preDefineCode}\n{$code}";
        $code .= $this->loader();
        $code = preg_replace("/(\r|\n)+/", "\n", $code);

        if ( ! $this->writeFile($code)) {
            throw new Exception('Failed to write file.');
        }
        if ( ! $this->isDev()) {
            new ScriptGeneration([
                'scriptFilePath' => "{$this->ROOT}/.tmp/app.js",
                'distFilePath' => $this->COMPILE_FILE_PATH,
            ]);
            new StyleGeneration([
                'styleFilePath' => "{$this->ROOT}/.tmp/app.css",
                'distFilePath' => $this->COMPILE_FILE_PATH,
            ]);
        }

        if ( ! $this->isDev()) {
            // if ($this->isDebug()) {
            $this->writeFile(file_get_contents($this->COMPILE_FILE_PATH));
            // } else {
            //     $this->writeFile(php_strip_whitespace($this->COMPILE_FILE_PATH));
            // }
        }

        echo 'Compiled!';
    }

    private function getCodeViaFilePath(string $filePath): string
    {
        $code = '';

        echo "Packing `{$filePath}...";

        $code = file_get_contents($filePath);
        $code = trim($code, "\n");

        echo "OK\n";

        return $code ? substr($code, 5) : $code;
    }

    private function isDev(): bool
    {
        global $argv;

        return \in_array('dev', $argv, true);
    }

    private function isDebug(): bool
    {
        global $argv;

        return \in_array('debug', $argv, true);
    }

    private function preDefine(array $code): string
    {
        $codeStr = implode("\n", $code);

        return <<<PHP
namespace InnStudio\\Prober\\Components\\PreDefine;
\$version = phpversion();
version_compare(\$version, '5.4.0','<') && exit("PHP 5.4+ is required. Currently installed version is: {\$version}");
{$codeStr}
PHP;
    }

    private function genDevMode(): string
    {
        $isDev = $this->isDev() ? 'true' : 'false';

        return <<<PHP
\\define('XPROBER_IS_DEV', {$isDev});
PHP;
    }

    private function genDirPath(): string
    {
        return <<<'PHP'
\define('XPROBER_DIR', __DIR__);
PHP;
    }

    private function genTimerCode(): string
    {
        return <<<'PHP'
\define('XPROBER_TIMER', \microtime(true));
PHP;
    }

    private function loader(): string
    {
        $dirs = glob($this->COMPONENTS_DIR . '/*');

        if ( ! $dirs) {
            return '';
        }
        $bootstrapDir = $this->isDev() ? 'dirname(__DIR__)' : '__DIR__';
        $files = [];
        $files[] = <<<PHP
new \\InnStudio\\Prober\\Components\\Bootstrap\\Bootstrap({$bootstrapDir});
PHP;

        return implode("\n", $files);
    }

    private function genVendorCode(): string
    {
        if ( ! $this->isDev()) {
            return '';
        }

        return <<<'PHP'
include \dirname(__DIR__) . '/vendor/autoload.php';
PHP;
    }

    private function yieldFiles(string $dir): Iterator
    {
        if (is_dir($dir)) {
            $dh = opendir($dir);

            if ( ! $dh) {
                yield false;
            }

            while (false !== ($file = readdir($dh))) {
                if ('.' === $file || '..' === $file) {
                    continue;
                }

                $filePath = "{$dir}/{$file}";

                if (is_dir($filePath)) {
                    foreach ($this->yieldFiles($filePath) as $yieldFilepath) {
                        yield $yieldFilepath;
                    }
                } else {
                    yield $filePath;
                }
            }

            closedir($dh);
        }

        yield $dir;
    }

    private function writeFile(string $data): bool
    {
        $dir = \dirname($this->COMPILE_FILE_PATH);

        if ( ! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return (bool) file_put_contents($this->COMPILE_FILE_PATH, $data);
    }
}
