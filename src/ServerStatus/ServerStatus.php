<?php

namespace InnStudio\Prober\ServerStatus;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class ServerStatus
{
    private $ID = 'serverStatus';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'));
        Events::on('style', array($this, 'filterStyle'));
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18n::_('Server status'),
            'tinyTitle' => I18n::_('Status'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        ?>
<div class="form-group">
    <div class="group-label"><?php echo I18n::_('System load'); ?></div>
    <div class="group-content small-group-container" id="systemLoadAvg"><?php echo Helper::getSysLoadAvg(); ?></div>
</div> 
<div class="form-group">
    <div class="group-label"><?php echo I18n::_('CPU usage'); ?></div>
    <div class="group-content small-group-container" id="cpuUsage">
        <div class="progress-container">
            <div class="number">
                <span id="cpuUsagePercent">
                    10%
                </span>
            </div>
            <div class="progress" id="cpuUsageProgress">
                <div id="cpuUsageProgressValue" class="progress-value" style="width: 10%"></div>
            </div>
        </div>
    </div>
</div>
<div class="form-group memory-usage">
    <div class="group-label"><?php echo I18n::_('Real memory usage'); ?></div>
    <div class="group-content">
        <div class="progress-container">
            <div class="percent" id="memRealUsagePercent"><?php echo $this->getMemUsage('MemRealUsage', true); ?>%</div>
            <div class="number">
                <span id="memRealUsage">
                    <?php echo Helper::getHumamMemUsage('MemRealUsage'); ?>
                    /
                    <?php echo Helper::getHumamMemUsage('MemTotal'); ?>
                </span>
            </div>
            <div class="progress" id="memRealUsageProgress">
                <div id="memRealUsageProgressValue" class="progress-value" style="width: <?php echo $this->getMemUsage('MemRealUsage', true); ?>%"></div>
            </div>
        </div>
    </div>
</div>
<div class="form-group swap-usage">
    <div class="group-label"><?php echo I18n::_('Real swap usage'); ?></div>
    <div class="group-content">
        <div class="progress-container">
            <div class="percent" id="swapRealUsagePercent"><?php echo $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'); ?>%</div>
            <div class="number">
                <span id="swapRealUsage">
                    <?php echo Helper::getHumamMemUsage('SwapRealUsage'); ?>
                    /
                    <?php echo Helper::getHumamMemUsage('SwapTotal'); ?>
                </span>
            </div>
            <div class="progress" id="swapRealUsageProgress">
                <div id="swapRealUsageProgressValue" class="progress-value" style="width: <?php echo $this->getMemUsage('SwapRealUsage', true, 'SwapTotal'); ?>%"></div>
            </div>
        </div>
    </div>
</div>
        <?php
    }

    public function filterStyle()
    {
        ?>
<style>
.small-group{
    display: inline-block;
    background: #eee;
    border-radius: 1rem;
    margin: 0 .2rem;
    padding: 0 .5rem;
    width: 7rem;
    text-align: center;
}
#scriptPath.group-content{
    word-break: break-all;
}
</style>
        <?php
    }

    private function getMemUsage($key, $precent = false, $totalKey = 'MemTotal')
    {
        if (false === $precent) {
            return Helper::getMemoryUsage($key);
        }

        return Helper::getMemoryUsage($key) ? \sprintf('%01.2f', Helper::getMemoryUsage($key) / Helper::getMemoryUsage($totalKey) * 100) : 0;
    }
}
