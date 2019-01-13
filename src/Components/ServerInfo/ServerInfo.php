<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class ServerInfo
{
    private $ID = 'serverInfo';

    public function __construct()
    {
        EventsApi::on('mods', array($this, 'filter'), 200);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('Server information'),
            'tinyTitle' => I18nApi::_('Info'),
            'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        return <<<HTML
<div class="inn-row">
    {$this->getContent()}
</div>
HTML;
    }

    private function getDiskInfo()
    {
        if ( ! HelperApi::getDiskTotalSpace()) {
            return I18nApi::_('Unavailable');
        }

        $percent    = \sprintf('%01.2f', (1 - (HelperApi::getDiskFreeSpace() / HelperApi::getDiskTotalSpace())) * 100);
        $hunamUsed  = HelperApi::formatBytes(HelperApi::getDiskTotalSpace() - HelperApi::getDiskFreeSpace());
        $hunamTotal = HelperApi::getDiskTotalSpace(true);

        return <<<HTML
<div class="inn-progress__container">
    <div class="inn-progress__percent" id="inn-diskUsagePercent">{$percent}%</div>
    <div class="inn-progress__number">
        <span id="inn-diskUsageOverview">
            {$hunamUsed} / {$hunamTotal}
        </span>
    </div>
    <div class="inn-progress" id="inn-diskUsageProgress">
        <div id="inn-diskUsageProgressValue" class="inn-progress__value" style="width: {$percent}%"></div>
    </div>
</div>
HTML;
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => I18nApi::_('Server name'),
                'content' => $this->getServerInfo('SERVER_NAME'),
            ),
            array(
                'id'      => 'serverInfoTime',
                'label'   => I18nApi::_('Server time'),
                'content' => HelperApi::getServerTime(),
            ),
            array(
                'id'      => 'serverInfoUpTime',
                'label'   => I18nApi::_('Server uptime'),
                'content' => HelperApi::getServerUpTime(),
            ),
            array(
                'label'   => I18nApi::_('Server IP'),
                'content' => $this->getServerInfo('SERVER_ADDR'),
            ),
            array(
                'label'   => I18nApi::_('Server software'),
                'content' => $this->getServerInfo('SERVER_SOFTWARE'),
            ),
            array(
                'label'   => I18nApi::_('PHP version'),
                'content' => \PHP_VERSION,
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('CPU model'),
                'content' => HelperApi::getCpuModel(),
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('Server OS'),
                'content' => \php_uname(),
            ),
            array(
                'id'      => 'scriptPath',
                'col'     => '1-1',
                'label'   => I18nApi::_('Script path'),
                'content' => __FILE__,
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('Disk usage'),
                'content' => $this->getDiskInfo(),
            ),
        );

        return \implode('', \array_map(array(HelperApi::class, 'getGroup'), $items));
    }

    private function getServerInfo($key)
    {
        return $_SERVER[$key] ?? '';
    }
}
