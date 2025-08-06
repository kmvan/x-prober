<?php

namespace InnStudio\Prober\Components\PhpInfoDetail;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;
use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class PhpInfoDetailAction extends PhpInfoDetailConstants
{
    public function render($action)
    {
        if ($action !== $this->ID) {
            return;
        }
        if (UserConfigApi::isDisabled($this->ID)) {
            (new RestResponse())
                ->setStatus(StatusCode::$FORBIDDEN)
                ->end();
        }
        phpinfo();
        exit;
    }
}
