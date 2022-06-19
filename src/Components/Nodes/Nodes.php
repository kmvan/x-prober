<?php

namespace InnStudio\Prober\Components\Nodes;

final class Nodes
{
    public function __construct()
    {
        new Conf();
        new Fetch();
    }
}
