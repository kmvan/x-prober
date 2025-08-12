<?php

namespace InnStudio\Prober\Components\ServerBenchmark;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class ServerBenchmarkPoll
{
    public function render()
    {
        $id = ServerBenchmarkConstants::ID;
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
