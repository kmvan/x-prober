<?php

namespace InnStudio\Prober\Components\MyInfo;

use InnStudio\Prober\Components\Helper\HelperApi;

class FilterPing
{
    public function __construct()
    {
        HelperApi::isAction('ping') && $this->filter();
    }

    public function filter()
    {
        HelperApi::dieJson(array(
            'data' => array(
                'time' => \microtime(true) - \XPROBER_TIMER,
            ),
        ));
    }
}
