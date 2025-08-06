<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

class NodesApi extends NodesConstants
{
    public function getUserConfigNodes()
    {
        $items = UserConfigApi::get($this->ID);
        if ( ! $items || ! \is_array($items)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map(function ($item) {
                    if (2 !== \count($item)) {
                        return;
                    }

                    return [
                        'id' => $item[0],
                        'url' => $item[1],
                    ];
                }, $items)
            )
        );
    }
}
