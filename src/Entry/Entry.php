<?php

namespace InnStudio\Prober\Entry;

use InnStudio\Prober\Config\Api as Config;
use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class Entry
{
    public function __construct()
    {
        Events::emit('init');

        if (DEBUG === true) {
            $this->display();
        } else {
            \ob_start();
            $this->display();
            $content = \ob_get_contents();
            \ob_end_clean();

            echo Helper::htmlMinify($content);
        }
    }

    private function displayContent()
    {
        $mods = Events::apply('mods', array());

        if ( ! $mods) {
            return;
        }

        foreach ($mods as $id => $mod) {
            ?>
<fieldset id="<?php echo $id; ?>">
    <legend >
        <span class="long-title"><?php echo $mod['title']; ?></span>
        <span class="tiny-title"><?php echo $mod['tinyTitle']; ?></span>
    </legend>
    <?php \call_user_func($mod['display']); ?>
</fieldset>
            <?php
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
    <title><?php echo I18n::_(Config::$APP_NAME); ?> v<?php echo Config::$APP_VERSION; ?></title>
    <?php Events::emit('style'); ?>
</head>
<body>
<div class="poi-container">
    <h1><a href="<?php echo I18n::_(Config::$APP_URL); ?>" target="_blank"><?php echo I18n::_(Config::$APP_NAME); ?> v<?php echo Config::$APP_VERSION; ?></a></h1>
    <?php $this->displayContent(); ?>
</div>
<?php Events::emit('footer'); ?>
<?php Events::emit('script'); ?>
</body>
</html>
        <?php
    }
}
