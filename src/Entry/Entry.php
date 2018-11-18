<?php

namespace InnStudio\Prober\Entry;

use InnStudio\Prober\Config\ConfigApi;
use InnStudio\Prober\Events\EventsApi;
use InnStudio\Prober\Helper\HelperApi;
use InnStudio\Prober\I18n\I18nApi;

class Entry
{
    public function __construct()
    {
        EventsApi::emit('init');

        if (DEBUG === true) {
            $this->display();
        } else {
            \ob_start();
            $this->display();
            $content = \ob_get_contents();
            \ob_end_clean();

            echo HelperApi::htmlMinify($content);
        }
    }

    private function displayContent()
    {
        $mods = EventsApi::emit('mods', array());

        if ( ! $mods) {
            return;
        }

        foreach ($mods as $id => $mod) {
            $content = \call_user_func($mod['display']);

            echo <<<HTML
<fieldset id="{$id}">
    <legend >
        <span class="long-title">{$mod['title']}</span>
        <span class="tiny-title">{$mod['tinyTitle']}</span>
    </legend>
    {$content}
</fieldset>
HTML;
        }
    }

    private function display()
    {
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo I18nApi::_(ConfigApi::$APP_NAME); ?> v<?php echo ConfigApi::$APP_VERSION; ?></title>
    <?php EventsApi::emit('style'); ?>
</head>
<body>
<div class="poi-container">
    <h1><a href="<?php echo I18nApi::_(ConfigApi::$APP_URL); ?>" target="_blank"><?php echo I18nApi::_(ConfigApi::$APP_NAME); ?> v<?php echo ConfigApi::$APP_VERSION; ?></a></h1>
    <?php $this->displayContent(); ?>
</div>
<?php EventsApi::emit('footer'); ?>
<?php EventsApi::emit('script'); ?>
</body>
</html>
        <?php
    }
}
