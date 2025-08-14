<?php

namespace InnStudio\Prober\Components\UserConfig;

final class UserConfigPoll
{
    public function render()
    {
        return [
            UserConfigConstants::ID => UserConfigApi::get(),
        ];
    }
}
