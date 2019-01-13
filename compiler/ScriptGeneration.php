<?php

namespace InnStudio\Prober\Compiler;

class ScriptGeneration
{
    private $scriptFilePath = '';

    private $distFilePath = '';

    public function __construct(array $args)
    {
        [
            'scriptFilePath' => $this->scriptFilePath,
            'distFilePath'   => $this->distFilePath,
        ] = $args;

        if ( ! \is_file($this->scriptFilePath)) {
            $this->die("File not found: {$this->scriptFilePath}");
        }

        if ( ! \is_file($this->distFilePath)) {
            $this->die("File not found: {$this->distFilePath}");
        }

        if ( ! $this->setScript($this->getScript())) {
            $this->die('Error: can not write script content to dist.');
        }

        $this->die('Script content wrote successful.', false);
    }

    private function getScript(): string
    {
        return (string) \file_get_contents($this->scriptFilePath);
    }

    private function setScript(string $script): bool
    {
        $dist = (string) \file_get_contents($this->distFilePath);

        if ( ! $dist) {
            return false;
        }

        $dist = \str_replace('{INN_SCRIPT}', $script, $dist);

        return (bool) \file_put_contents($this->distFilePath, $dist);
    }

    private function die(string $msg, bool $die = true): void
    {
        $msg = "[ScriptGeneration] {$msg}\n";

        if ($die) {
            die($msg);
        }

        echo $msg;
    }
}
