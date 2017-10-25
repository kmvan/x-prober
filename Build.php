<?php

namespace InnStudio\Prober;

include __DIR__ . '/vendor/autoload.php';

class Build
{
    const BASE_DIR       = __DIR__ . '/src/';
    const DIST_FILE_PATH = __DIR__ . '/dist/prober.php';
    const DEBUG          = false;

    public function __construct()
    {
        $code = '';

        foreach ($this->yieldFiles(self::BASE_DIR) as $file) {
            if (\is_dir($file) || false === \strpos($file, '.php')) {
                continue;
            }

            if (self::DEBUG === true) {
                $content = \file_get_contents($file);
            } else {
                $content = \php_strip_whitespace($file);
            }

            $content = \substr($content, 5);
            $code .= $content;
        }

        $preDefineCode = $this->preDefine(array(
            $this->getTimerCode(),
            $this->getDebugCode(),
            $this->getLangLoaderCode(),
        ));
        $code = "<?php\n{$preDefineCode}\n{$code}";
        $code .= $this->loader();

        if (true === $this->writeFile($code)) {
            echo 'Done!';
        } else {
            echo 'Failed.';
        }
    }

    private function preDefine(array $code): string
    {
        $codeStr = \implode("\n", $code);

        return <<<EOT
namespace InnStudio\Prober\PreDefine;
{$codeStr}
EOT;
    }

    private function getTimerCode(): string
    {
        return <<<EOT
\define('TIMER', \microtime(true));
EOT;
    }

    private function getDebugCode(): string
    {
        $debug = self::DEBUG ? 'true' : 'false';

        return <<<EOT
\define('DEBUG', {$debug});
EOT;
    }

    private function getLangLoaderCode(): string
    {
        $filePath = self::BASE_DIR . '/I18n/Lang.json';

        if ( ! \is_readable($filePath)) {
            die('Language is missing.');
        }

        $lines = \file($filePath);

        $lines = \array_map(function ($line) {
            return 0 === \strpos(\trim($line), '// ') ? '' : $line;
        }, $lines);

        $json = \implode('', $lines);
        $json = \json_decode($json, true);

        if ( ! $json) {
            die('Invalid json format.');
        }

        $json = \base64_encode(\json_encode($json));
        $json = <<<EOT
\define('LANG', '{$json}');
EOT;

        return $json;
    }

    private function loader(): string
    {
        $dirs = \glob(self::BASE_DIR . '/*');

        if ( ! $dirs) {
            return '';
        }

        $files = array();

        foreach ($dirs as $dir) {
            $basename = \basename($dir);
            $filePath = "{$dir}/{$basename}.php";

            if ( ! \is_file($filePath)) {
                continue;
            }

            if ('Entry' === $basename) {
                continue;
            }

            $files[] = "new \\InnStudio\\Prober\\{$basename}\\{$basename}();";
        }

        $files[] = 'new \\InnStudio\\Prober\\Entry\\Entry();';

        return \implode("\n", $files);
    }

    private function yieldFiles(string $dir): \Iterator
    {
        if (\is_dir($dir)) {
            $dh = \opendir($dir);

            if ( ! $dh) {
                yield false;
            }

            while (false !== ($file = \readdir($dh))) {
                if ('.' === $file || '..' === $file) {
                    continue;
                }

                $filepath = "{$dir}/{$file}";

                if (\is_dir($filepath)) {
                    foreach (self::yieldFiles($filepath) as $yieldFilepath) {
                        yield $yieldFilepath;
                    }
                } else {
                    yield $filepath;
                }
            }

            \closedir($dh);
        }

        yield $dir;
    }

    private function writeFile(string $data): bool
    {
        $dir = \dirname(self::DIST_FILE_PATH);

        if ( ! \is_dir($dir)) {
            \mkdir($dir, 0755);
        }

        return (bool) \file_put_contents(self::DIST_FILE_PATH, $data);
    }
}

new Build();
