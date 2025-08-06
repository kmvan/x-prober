<?php

namespace InnStudio\Prober\Components\UserConfig;

final class UserConfigPoll extends UserConfigConstants
{
    public function render()
    {
        return [
            $this->ID => UserConfigApi::get(),
        ];
    }
}
