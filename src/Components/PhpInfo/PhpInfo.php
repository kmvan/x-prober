<?php

namespace InnStudio\Prober\Components\PhpInfo;

use InnStudio\Prober\Components\Config\ConfigApi;
use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Helper\HelperApi;
use InnStudio\Prober\Components\I18n\I18nApi;

class PhpInfo
{
    private $ID = 'phpInfo';

    public function __construct()
    {
        EventsApi::on('mods', [$this, 'filter'], 300);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = [
            'title'     => I18nApi::_('PHP information'),
            'tinyTitle' => I18nApi::_('PHP'),
            'display'   => [$this, 'display'],
        ];

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

    private function getContent()
    {
        $errLevels = HelperApi::getErrNameByCode(\ini_get('error_reporting'));
        $langClick = I18nApi::_('Click for detail');

        $displayError = '';

        $items = [
            [
                'label'   => I18nApi::_('PHP info detail'),
                'content' => HelperApi::getBtn("👆 {$langClick}", '?action=phpInfo'),
            ],
            [
                'label'   => I18nApi::_('Version'),
                'title'   => \sprintf(I18nApi::_('X Prober builtin latest PHP stable version: %s'), ConfigApi::$LATEST_PHP_STABLE_VERSION),
                'content' => \PHP_VERSION . ' ' . (\version_compare(\PHP_VERSION, ConfigApi::$LATEST_PHP_STABLE_VERSION, '<') ? I18nApi::_('(Old)') : I18nApi::_('(Up to date)')),
            ],
            [
                'label'   => I18nApi::_('SAPI interface'),
                'content' => \PHP_SAPI,
            ],
            [
                'label'   => I18nApi::_('Display errors'),
                'title'   => 'display_errors',
                'content' => HelperApi::alert(\ini_get('display_errors')),
            ],
            [
                'label'   => I18nApi::_('Error reporting'),
                'title'   => "error_reporting: {$errLevels}",
                'content' => '' === $errLevels ? HelperApi::alert(false) : HelperApi::strcut($errLevels),
            ],
            [
                'label'   => I18nApi::_('Max memory limit'),
                'title'   => 'memory_limit',
                'content' => \ini_get('memory_limit'),
            ],
            [
                'label'   => I18nApi::_('Max POST size'),
                'title'   => 'post_max_size',
                'content' => \ini_get('post_max_size'),
            ],
            [
                'label'   => I18nApi::_('Max upload size'),
                'title'   => 'upload_max_filesize',
                'content' => \ini_get('upload_max_filesize'),
            ],
            [
                'label'   => I18nApi::_('Max input variables'),
                'title'   => 'max_input_vars',
                'content' => \ini_get('max_input_vars'),
            ],
            [
                'label'   => I18nApi::_('Max execution time'),
                'title'   => 'max_execution_time',
                'content' => \ini_get('max_execution_time'),
            ],
            [
                'label'   => I18nApi::_('Timeout for socket'),
                'title'   => 'default_socket_timeout',
                'content' => \ini_get('default_socket_timeout'),
            ],
            [
                'label'   => I18nApi::_('Treatment URLs file'),
                'title'   => 'allow_url_fopen',
                'content' => HelperApi::alert((bool) \ini_get('allow_url_fopen')),
            ],
            [
                'label'   => I18nApi::_('SMTP support'),
                'title'   => 'SMTP',
                'content' => HelperApi::alert((bool) \ini_get('SMTP')),
            ],
            [
                'col'     => '1-1',
                'label'   => I18nApi::_('Disabled functions'),
                'title'   => 'disable_functions',
                'id'      => 'break-normal',
                'content' => HelperApi::getGroupItemLists(\explode(',', \ini_get('disable_functions')), true) ?: HelperApi::alert(false),
            ],
            [
                'col'     => '1-1',
                'label'   => I18nApi::_('Disabled classes'),
                'title'   => 'disable_classes',
                'id'      => 'break-normal',
                'content' => HelperApi::getGroupItemLists(\explode(',', \ini_get('disable_classes')), true) ?: HelperApi::alert(false),
            ],
        ];

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }

    private function _($str)
    {
        return I18nApi::_($str);
    }
}
