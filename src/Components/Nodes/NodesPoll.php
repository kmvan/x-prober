<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class NodesPoll
{
    public function render()
    {
        $id = NodesConstants::ID;
        if (UserConfigApi::isDisabled($id)) {
            return [
                $id => null,
            ];
        }
        $items = array_map(function ($item) {
            return $item['id'];
        }, NodesApi::getUserConfigNodes());
        if ( ! $items) {
            return [
                $id => null,
            ];
        }

        return [
            $id => [
                'nodesIds' => $items,
            ],
        ];
    }
}
