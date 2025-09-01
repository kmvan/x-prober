<?php

namespace InnStudio\Prober\Components\BrowserBenchmark;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class BrowserBenchmarkPoll
{
    public function render()
    {
        $id = BrowserBenchmarkConstants::ID;
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
