<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\UserConfig\UserConfigApi;

final class NodesPoll extends NodesApi
{
    public function render()
    {
        if (UserConfigApi::isDisabled($this->ID)) {
            return [
                $this->ID => null,
            ];
        }
        $items = array_map(function ($item) {
            return $item['id'];
        }, $this->getUserConfigNodes());
        if ( ! $items) {
            return [
                $this->ID => null,
            ];
        }

        return [
            $this->ID => [
                'nodesIds' => $items,
            ],
        ];
    }
}
