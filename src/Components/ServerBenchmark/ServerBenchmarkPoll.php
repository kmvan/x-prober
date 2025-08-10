<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class ServerBenchmarkPoll extends ServerBenchmarkConstants
{
    public function render()
    {
        if (UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }

        return [
            $this->ID => true,
        ];
    }
}
