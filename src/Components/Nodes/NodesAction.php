<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;

final class NodesAction extends NodesApi
{
    public function render($action)
    {
        if ($action !== $this->ID) {
            return;
        }
        $nodeId = filter_input(\INPUT_GET, 'nodeId', \FILTER_DEFAULT);
        $response = new RestResponse();
        if ( ! $nodeId) {
            $response->setStatus(StatusCode::$BAD_REQUEST)->end();
        }
        $data = $this->getNodeData($nodeId);
        if ( ! $data) {
            $response->setStatus(StatusCode::$NO_CONTENT)->end();
        }
        $response
            ->setData($data)
            ->end();
    }

    private function getNodeData($nodeId)
    {
        $node = array_find($this->getUserConfigNodes(), function ($item) use ($nodeId) {
            return isset($item['url']) && isset($item['id']) && $item['id'] === $nodeId;
        });
        if ( ! $node) {
            return;
        }
        $params = 'action=poll';
        $url = \defined('XPROBER_IS_DEV') && XPROBER_IS_DEV ? "{$node['url']}/api?{$params}" : "{$node['url']}?{$params}";

        return $this->getRemoteData($url);
    }

    private function getRemoteData($url)
    {
        if (\function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                \CURLOPT_RETURNTRANSFER => true,
            ]);
            $content = curl_exec($ch);
            curl_close($ch);

            return json_decode($content) ?: null;
        }

        return json_decode(file_get_contents($url)) ?: null;
    }
}
