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
        EventsApi::on('mods', array($this, 'filter'), 300);
    }

    public function filter(array $mods)
    {
        $mods[$this->ID] = array(
            'title'     => I18nApi::_('PHP information'),
            'tinyTitle' => I18nApi::_('PHP'),
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

    private function getContent()
    {
        $errLevels = HelperApi::getErrNameByCode(\ini_get('error_reporting'));
        $langClick = I18nApi::_('Click for detail');

        $displayError = '';

        $items = array(
            array(
                'label'   => I18nApi::_('PHP info detail'),
                'content' => HelperApi::getBtn("👆 {$langClick}", '?action=phpInfo'),
            ),
            array(
                'label'   => I18nApi::_('Version'),
                'title'   => \sprintf(I18nApi::_('X Prober builtin latest PHP stable version: %s'), ConfigApi::$LATEST_PHP_STABLE_VERSION),
                'content' => \PHP_VERSION . ' ' . (\version_compare(\PHP_VERSION, ConfigApi::$LATEST_PHP_STABLE_VERSION, '<') ? I18nApi::_('(Old)') : I18nApi::_('(Up to date)')),
            ),
            array(
                'label'   => I18nApi::_('SAPI interface'),
                'content' => \PHP_SAPI,
            ),
            array(
                'label'   => I18nApi::_('Display errors'),
                'title'   => 'display_errors',
                'content' => HelperApi::alert(\ini_get('display_errors')),
            ),
            array(
                'label'   => I18nApi::_('Error reporting'),
                'title'   => "error_reporting: {$errLevels}",
                'content' => '' === $errLevels ? HelperApi::alert(false) : HelperApi::strcut($errLevels),
            ),
            array(
                'label'   => I18nApi::_('Max memory limit'),
                'title'   => 'memory_limit',
                'content' => \ini_get('memory_limit'),
            ),
            array(
                'label'   => I18nApi::_('Max POST size'),
                'title'   => 'post_max_size',
                'content' => \ini_get('post_max_size'),
            ),
            array(
                'label'   => I18nApi::_('Max upload size'),
                'title'   => 'upload_max_filesize',
                'content' => \ini_get('upload_max_filesize'),
            ),
            array(
                'label'   => I18nApi::_('Max input variables'),
                'title'   => 'max_input_vars',
                'content' => \ini_get('max_input_vars'),
            ),
            array(
                'label'   => I18nApi::_('Max execution time'),
                'title'   => 'max_execution_time',
                'content' => \ini_get('max_execution_time'),
            ),
            array(
                'label'   => I18nApi::_('Timeout for socket'),
                'title'   => 'default_socket_timeout',
                'content' => \ini_get('default_socket_timeout'),
            ),
            array(
                'label'   => I18nApi::_('Treatment URLs file'),
                'title'   => 'allow_url_fopen',
                'content' => HelperApi::alert((bool) \ini_get('allow_url_fopen')),
            ),
            array(
                'label'   => I18nApi::_('SMTP support'),
                'title'   => 'SMTP',
                'content' => HelperApi::alert((bool) \ini_get('SMTP')),
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('Disabled functions'),
                'title'   => 'disable_functions',
                'id'      => 'break-normal',
                'content' => HelperApi::getGroupItemLists(\explode(',', \ini_get('disable_functions')), true) ?: HelperApi::alert(false),
            ),
            array(
                'col'     => '1-1',
                'label'   => I18nApi::_('Disabled classes'),
                'title'   => 'disable_classes',
                'id'      => 'break-normal',
                'content' => HelperApi::getGroupItemLists(\explode(',', \ini_get('disable_classes')), true) ?: HelperApi::alert(false),
            ),
        );

        return \implode('', \array_map(function (array $item) {
            return HelperApi::getGroup($item);
        }, $items));
    }

    private function _($str)
    {
        return I18nApi::_($str);
    }
}
