<?php

namespace InnStudio\Prober\Updater;

use InnStudio\Prober\Config\Api as Config;
use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\I18n\Api as I18n;

class Updater
{
    private $ID = 'updater';

    public function __construct()
    {
        Events::on('script', array($this, 'filter'));
    }

    public function filter()
    {
        ?>
<script>
(function(){
var versionCompare = function(left, right) {
    if (typeof left + typeof right != 'stringstring')
        return false;

    var a = left.split('.')
    ,   b = right.split('.')
    ,   i = 0, len = Math.max(a.length, b.length);
        
    for (; i < len; i++) {
        if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
            return 1;
        } else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
            return -1;
        }
    }

    return 0;
}
var version = "<?php echo Config::$APP_VERSION; ?>";
var xhr = new XMLHttpRequest();
xhr.open('get', '<?php echo Config::$CHANGELOG_URL; ?>');
xhr.send();
xhr.onload = load;
function load(){
    if (xhr.readyState !== 4) {
        return;
    }

    if (xhr.status >= 200 && xhr.status < 400) {
        var data = xhr.responseText;

        if (! data) {
            return;
        }

        var versionInfo = getVersionInfo(data);

        if (!versionInfo.length) {
            return;
        }

        if (versionCompare(version, versionInfo[0]) === -1) {
            var lang = '<?php echo I18n::_('Found update! {APP_NAME} has new version v{APP_NEW_VERSION}'); ?>';
            lang = lang.replace('{APP_NAME}', '<?php echo I18n::_(Config::$APP_NAME); ?>');
            lang = lang.replace('{APP_NEW_VERSION}', versionInfo[0]);

            document.querySelector('h1').innerHTML = '<a href="<?php echo Config::$AUTHOR_URL; ?>" target="_blank">' + lang + '</a>';
        }
    }
}

function getVersionInfo(data){
    var reg = /^#{2}\s+(\d+\.\d+\.\d+)\s+\-\s+(\d{4}\-\d+\-\d+)/mg;
    return reg.test(data) ? [RegExp.$1,RegExp.$2]: [];
}
})()
</script>
        <?php
    }
}
