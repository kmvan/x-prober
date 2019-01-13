<?php

namespace InnStudio\Prober\Compiler;

use Gettext\Translations;

class LanguageGeneration
{
    public $langDir = '';

    private $moFiles = [];
    private $code    = [];

    public function __construct(string $langDir)
    {
        $this->langDir = $langDir;
        $this->moFiles = $this->getMoFiles();
    }

    public function writeJsonFile(string $outputJsonFilePath): bool
    {
        if ( ! \is_dir($this->langDir)) {
            return false;
        }

        $code = \array_merge(
            [],
            ...\array_map(function (string $moFile): array {
                return $this->getCode($moFile);
            }, $this->moFiles)
        );

        $code = \json_encode($code, \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT);
        $code = <<<JSON
// Do not edit the json file
{$code}
JSON;

        return (bool) \file_put_contents($outputJsonFilePath, $code);
    }

    private function getMoFiles(): array
    {
        return \glob("{$this->langDir}/*.po");
    }

    private function getCode(string $moFile): array
    {
        $translations = Translations::fromPoFile($moFile);
        $entries      = $translations->toJsonDictionaryString();
        $entries      = \json_decode($entries, true);

        return [
            $this->getLangId($moFile) => $entries,
        ];
    }

    private function getLangId(string $moFile): string
    {
        return \explode('.', \basename($moFile))[0];
    }
}
