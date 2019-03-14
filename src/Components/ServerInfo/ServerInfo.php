<?php

namespace InnStudio\Prober\Components\ServerInfo;

use InnStudio\Prober\Components\Config\ConfigApi;
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

    private function getNginxTitle()
    {
        if (false === \stripos($this->getServerInfo('SERVER_SOFTWARE'), 'nginx/')) {
            return '';
        }

        return \sprintf(I18nApi::_('X Prober builtin latest NGINX stable version: %s'), ConfigApi::$LATEST_NGINX_STABLE_VERSION);
    }

    private function getNginxContent()
    {
        $name = $this->getServerInfo('SERVER_SOFTWARE');

        if (false === \stripos($name, 'nginx/')) {
            return $name;
        }

        $version = \str_replace('nginx/', '', $name);

        if ($version === $name) {
            return $name;
        }

        $status = \version_compare($version, ConfigApi::$LATEST_NGINX_STABLE_VERSION, '<') ? I18nApi::_('(Old)') : I18nApi::_('(Up to date)');

        return "{$name} {$status}";
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
                'title'   => $this->getNginxTitle(),
                'content' => $this->getNginxContent(),
            ),
            array(
                'label'   => I18nApi::_('PHP version'),
                'content' => \PHP_VERSION,
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('CPU model'),
                'id'      => 'break-normal',
                'content' => HelperApi::getCpuModel(),
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('Server OS'),
                'id'      => 'break-normal',
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
                'content' => HelperApi::getDiskTotalSpace() ? HelperApi::getProgressTpl(array(
                    'id'    => 'diskUsage',
                    'usage' => HelperApi::getDiskTotalSpace() - HelperApi::getDiskFreeSpace(),
                    'total' => HelperApi::getDiskTotalSpace(),
                )) : I18nApi::_('Unavailable'),
            ),
        );

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }

    private function getServerInfo($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}
