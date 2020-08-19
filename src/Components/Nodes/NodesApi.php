<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Xconfig\XconfigApi;

class NodesApi
{
    public $ID = 'nodes';

    public function getNodes()
    {
        $items = XconfigApi::getNodes();

        if ( ! $items || ! \is_array($items)) {
            return array();
        }

        return \array_filter(\array_map(function ($item) {
            if (2 !== \count($item)) {
                return null;
            }

            return array(
                'id'  => $item[0],
                'url' => $item[1],
            );
        }, $items));
    }
}
