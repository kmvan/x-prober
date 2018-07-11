<?php

namespace InnStudio\Prober\Script;

use InnStudio\Prober\Events\Api as Events;

class Script
{
    private $ID = 'script';

    public function __construct()
    {
        Events::on('script', array($this, 'filter'));
    }

    public function filter()
    {
        echo <<<HTML
<script>
(function () {
    var xhr = new XMLHttpRequest();
    xhr.onload = load;
    var cache = {};

    function addClassName(el,className){
        if (el.classList){
            el.classList.add(className);
        } else {
            el.className += ' ' + className;
        }
    }

    function removeClassName(el, className){
        if (el.classList){
            el.classList.remove(className);
        } else {
            el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
        }
    }

    function formatBytes(bytes, decimals) {
        if (bytes == 0) {
            return '0';
        }
        var k = 1024,
            dm = decimals || 2,
            sizes = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
            i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function I(el) {
        if (cache[el]) {
            return cache[el];
        }

        cache[el] = document.getElementById(el);

        return cache[el];
    }

    function setColor(progress, percent) {
        if (percent >= 80) {
            addClassName(progress,'high');
            removeClassName(progress,'medium');
            removeClassName(progress,'medium-low');
        } else if (percent >= 50) {
            addClassName(progress,'medium');
            removeClassName(progress,'high');
            removeClassName(progress,'medium-low');
        } else if (percent >= 30) {
            addClassName(progress,'medium-low');
            removeClassName(progress,'medium');
            removeClassName(progress,'high');
        } else {
            removeClassName(progress,'high');
            removeClassName(progress,'medium');
            removeClassName(progress,'medium-low');
        }
    }

    function request() {
        xhr.open('get', '?action=fetch');
        xhr.send();
    }

    function load() {
        if (xhr.readyState !== 4) {
            return;
        }

        if (xhr.status >= 200 && xhr.status < 400) {
            var res = JSON.parse(xhr.responseText);
            if (res && res.code === 0) {
                var data = res.data;

                fillCpuUsage(data);
                fillSysLoadAvg(data);
                fillMemRealUsage(data);
                fillSwapRealUsage(data);
                fillServerInfo(data);
                fillNetworkStats(data);
            }
        } else {}

        setTimeout(function () {
            request();
        }, 1000);
    }

    function fillCpuUsage(data) {
        var progress = I('cpuUsageProgress');
        var value = I('cpuUsageProgressValue');
        var percent = 100 - Math.round(data.cpuUsage.idle);
        var title = [];

        for (var i in data.cpuUsage) {
            title.push(i + ': ' + data.cpuUsage[i]);
        }

        progress.title = title.join(' / ');
        value.style.width = percent + '%';
        setColor(progress, percent);
        I('cpuUsagePercent').innerHTML = percent + '%';
    }

    function fillSysLoadAvg(data) {
        I('systemLoadAvg').innerHTML = data.sysLoadAvg;
    }

    function fillMemRealUsage(data) {
        var progress = I('memRealUsageProgress');
        var value = I('memRealUsageProgressValue');
        var percent = data.memRealUsage.percent;

        value.style.width = percent + '%';
        setColor(progress, percent);
        I('memRealUsagePercent').innerHTML = percent + '%';
        I('memRealUsage').innerHTML = data.memRealUsage.number;
    }

    function fillSwapRealUsage(data) {
        var progress = I('swapRealUsageProgress');
        var value = I('swapRealUsageProgressValue');
        var percent = data.swapRealUsage.percent;

        value.style.width = percent + '%';
        setColor(progress, percent);
        I('swapRealUsagePercent').innerHTML = percent + '%';
        I('swapRealUsage').innerHTML = data.swapRealUsage.number
    }

    function fillServerInfo(data) {
        I('serverInfoTime').innerHTML = data.serverInfo.time;
        I('serverUpTime').innerHTML = data.serverInfo.upTime;
    }

    var lastNetworkStats = {};

    function fillNetworkStats(data) {
        if (typeof data.networkStats !== 'object') {
            return;
        }

        var keys = Object.keys(data.networkStats);

        if (keys.length === 0) {
            return;
        }

        keys.map(function (k) {
            var item = data.networkStats[k];
            ['rx', 'tx'].map(function (type) {
                var total = data.networkStats[k][type];
                var last = lastNetworkStats[k] && lastNetworkStats[k][type] || 0;

                I('network-' + k + '-' + type + '-rate').innerHTML = last ? formatBytes((total - last) / 2) : 0;
                I('network-' + k + '-' + type + '-total').innerHTML = formatBytes(total);

                if (!lastNetworkStats[k]) {
                    lastNetworkStats[k] = {};
                }

                lastNetworkStats[k][type] = total;
            });
        });
    }

    request();
})();
</script>
HTML;
    }
}
