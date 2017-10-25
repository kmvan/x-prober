
(function () {
    var xhr = new XMLHttpRequest();
    xhr.onload = load;
    var cache = {};

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
            progress.classList.add('high');
            progress.classList.remove('medium');
            progress.classList.remove('medium-low');
        } else if (percent >= 50) {
            progress.classList.add('medium');
            progress.classList.remove('high');
            progress.classList.remove('medium-low');
        } else if (percent >= 30) {
            progress.classList.add('medium-low');
            progress.classList.remove('medium');
            progress.classList.remove('high');
        } else {
            progress.classList.remove('high');
            progress.classList.remove('medium');
            progress.classList.remove('medium-low');
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
                fillServerInfo(data);
                fillNetworkStats(data);
            }
        } else { }

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
    }

    function fillSwapRealUsage(data) {
        var progress = I('swapRealUsageProgress');
        var value = I('swapUsageProgressValue');
        var percent = data.swapRealUsage.percent;

        value.style.width = percent + '%';
        setColor(progress, percent);
        I('swapRealUsagePercent').innerHTML = percent + '%';
    }

    function fillServerInfo(data) {
        I('serverInfoTime').innerHTML = data.serverInfo.time;
        I('serverUpTime').innerHTML = data.serverInfo.upTime;
    }

    var lastNetworkStats = {};

    function fillNetworkStats(data) {
        var keys = Object.keys(data.networkStats);

        if (keys.length === 0) {
            return;
        }

        keys.map(k => {
            const item = data.networkStats[k];
            ['rx', 'tx'].map(type => {
                const total = data.networkStats[k][type];
                const last = lastNetworkStats[k] && lastNetworkStats[k][type] || 0;

                I('network-' + k + '-' + type + '-rate').innerHTML = last ? formatBytes((total - last) / 2) : 0;
                I('network-' + k + '-' + type + '-total').innerHTML = formatBytes(total);

                if (!lastNetworkStats[k]) {
                    lastNetworkStats[k] = {}
                }

                lastNetworkStats[k][type] = total;

            });
        });
    }

    request();
})();
