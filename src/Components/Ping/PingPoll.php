<?php

namespace InnStudio\Prober\Components\Ping;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class PingPoll
{
    public function render()
    {
        $id = PingConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }

        return [
            $id => true,
        ];
    }
}
