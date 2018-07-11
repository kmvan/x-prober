<?php

namespace InnStudio\Prober\Updater;

use InnStudio\Prober\Config\Api as Config;
use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class Updater
{
    private $ID = 'updater';

    public function __construct()
    {
        Events::on('script', array($this, 'filter'));
        Events::on('init', array($this, 'filterInit'));
    }

    public function filterInit()
    {
        if ( ! Helper::isAction('update')) {
            return;
        }

        // check file writable
        if ( ! \is_writable(__FILE__)) {
            Helper::dieJson(array(
                'code' => -1,
                'msg'  => I18n::_('File can not update.'),
            ));
        }

        $content = \file_get_contents(Config::$UPDATE_PHP_URL);

        if ( ! $content) {
            Helper::dieJson(array(
                'code' => -1,
                'msg'  => I18n::_('Update file not found.'),
            ));
        }

        if ((bool) \file_put_contents(__FILE__, $content)) {
            Helper::dieJson(array(
                'code' => 0,
                'msg'  => I18n::_('Update success...'),
            ));
        }

        Helper::dieJson(array(
            'code' => -1,
            'msg'  => I18n::_('Update error.'),
        ));
    }

    public function filter()
    {
        $version      = Config::$APP_VERSION;
        $changeLogUrl = Config::$CHANGELOG_URL;
        $authorUrl    = Config::$AUTHOR_URL;
        echo <<<HTML
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

checkUpdate();

function update(){
    var title = document.querySelector('h1');
    title.innerHTML = '<div>⏳ {$this->_('Updating...')}</div>'; 
    var xhr = new XMLHttpRequest();
    try {
        xhr.open('get', '?action=update');
        xhr.send();
        xhr.onload = onLoadUpload;
    } catch (err) {}
}
function onLoadUpload(){
    var xhr = this;
    var msg = '';

    if (xhr.readyState === 4) {
        if (xhr.status >= 200 && xhr.status < 400) {
            var res = xhr.responseText;

            try {
                res = JSON.parse(res) 
            } catch (err){ }

            if (res && res.code === 0) {
                msg = '✔️ ' + res.msg;
                location.reload();
            } else if (res && res.code) {
                msg = '❌ ' + res.msg;
            } else {
                msg = '❌ ' + res;
            }

            title.innerHTML = '<div>' + msg + '</div>';
        } else {
            title.innerHTML = '❌ {$this->_('Update error')}';
        }

    }

}

function checkUpdate() {
    var version = "{$version}";
    var xhr = new XMLHttpRequest();
    xhr.open('get', '{$changeLogUrl}');
    xhr.send();
    xhr.onload = onLoadCheckUpdate;
}
function onLoadCheckUpdate() {
    let xhr = this;
    if (xhr.readyState === 4 ) {
        if (xhr.status >= 200 && xhr.status < 400) {
            var data = xhr.responseText;

            if (! data) {
                return;
            }

            var versionInfo = getVersionInfo(data);

            if (!versionInfo.length) {
                return;
            }
            
            if (versionCompare('{$version}', versionInfo[0]) === -1) {
                var lang = '✨ {$this->_('{APP_NAME} found update! Version {APP_OLD_VERSION} &rarr; {APP_NEW_VERSION}')}';
                lang = lang.replace('{APP_NAME}', '{$this->_(Config::$APP_NAME)}');
                lang = lang.replace('{APP_OLD_VERSION}', '{$version}');
                lang = lang.replace('{APP_NEW_VERSION}', versionInfo[0]);
                
                var updateLink = document.createElement('a');
                updateLink.addEventListener('click', update);
                updateLink.innerHTML = lang;

                var title = document.querySelector('h1');
                title.innerHTML = '';
                title.appendChild(updateLink);
            }
        }
    }
}
function getVersionInfo(data){
    var reg = /^#{2}\s+(\d+\.\d+\.\d+)\s+\-\s+(\d{4}\-\d+\-\d+)/mg;
    return reg.test(data) ? [RegExp.$1,RegExp.$2]: [];
}
})()
</script>
HTML;
    }

    private function _($str)
    {
        return I18n::_($str);
    }
}
