<?php

namespace InnStudio\Prober\Compiler;

final class StyleGeneration
{
    private $styleFilePath = '';

    private $distFilePath = '';

    public function __construct(array $args)
    {
        [
            'styleFilePath' => $this->styleFilePath,
            'distFilePath'  => $this->distFilePath,
        ] = $args;

        if ( ! is_file($this->styleFilePath)) {
            $this->die("File not found: {$this->styleFilePath}");
        }

        if ( ! is_file($this->distFilePath)) {
            $this->die("File not found: {$this->distFilePath}");
        }

        if ( ! $this->setStyle($this->getStyle())) {
            $this->die('Error: can not write script content to dist.');
        }

        $this->die('Script content wrote successful.', false);
    }

    private function getStyle(): string
    {
        return (string) file_get_contents($this->styleFilePath);
    }

    private function setStyle(string $style): bool
    {
        $dist = (string) file_get_contents($this->distFilePath);

        if ( ! $dist) {
            return false;
        }

        $dist = str_replace('{INN_STYLE}', $style, $dist);

        return (bool) file_put_contents($this->distFilePath, $dist);
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
