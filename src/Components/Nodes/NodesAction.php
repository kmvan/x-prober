<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;

final class NodesAction
{
    public function render($action)
    {
        if (NodesConstants::ID !== $action) {
            return;
        }
        $nodeId = filter_input(\INPUT_GET, 'nodeId', \FILTER_DEFAULT);
        $response = new RestResponse();
        if ( ! $nodeId) {
            $response
                ->setStatus(StatusCode::BAD_REQUEST)
                ->end();
        }
        $data = $this->getNodeData($nodeId);
        if ( ! $data) {
            $response
                ->setStatus(StatusCode::NO_CONTENT)
                ->end();
        }
        $response
            ->setData($data)
            ->end();
    }

    private function getNodeData($nodeId)
    {
        $nodes = array_filter(NodesApi::getUserConfigNodes(), function ($item) use ($nodeId) {
            return isset($item['url']) && isset($item['id']) && $item['id'] === $nodeId;
        });
        if ( ! $nodes) {
            return;
        }
        $node = $nodes[0];
        $isDev = \defined('XPROBER_IS_DEV') && XPROBER_IS_DEV;
        $url = $node['url'];
        $isRemote = ( ! str_contains($url, 'localhost') || ! str_contains($url, '127.0.0.1'));
        $params = 'action=poll';
        $url = ($isDev && ! $isRemote) ? "{$url}/api?{$params}" : "{$url}?{$params}";

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
