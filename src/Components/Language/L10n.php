<?php

declare(strict_types = 1);

namespace InnStudio\Theme\Apps\Language;

use InnStudio\Theme\Apps\Helpers\HelpersApi;

class L10n
{
    public static function __(string $text, ?string $context = null): string
    {
        $id = "{$context}{$text}";

        return LanguageItems::LANGS[$id][HelpersApi::getOption('WPLANG')] ?? $text;
    }
}
