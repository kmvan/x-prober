<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Restful\HttpStatus;
use InnStudio\Prober\Components\Restful\RestfulResponse;

class Fetch extends NodesApi
{
    public function __construct()
    {
        EventsApi::on('init', array($this, 'filter'), 100);
    }

    public function filter($action)
    {
        switch ($action) {
            case 'nodes':
                EventsApi::emit('fetchNodesBefore');
                $response = new RestfulResponse(EventsApi::emit('nodes', array()));
                $response->dieJson();
                // no break
            case 'node':
                EventsApi::emit('fetchNodeBefore');
                $nodeId   = \filter_input(\INPUT_GET, 'nodeId', \FILTER_SANITIZE_STRING);
                $response = new RestfulResponse();

                if ( ! $nodeId) {
                    $response->setStatus(HttpStatus::$BAD_REQUEST)->dieJson();
                }

                $data = $this->getNodeData($nodeId);

                if ( ! $data) {
                    $response->setStatus(HttpStatus::$NO_CONTENT)->dieJson();
                }

                $response->setData($data)->dieJson();
        }

        return $action;
    }

    private function getNodeData($nodeId)
    {
        foreach ($this->getNodes() as $item) {
            if ( ! isset($item['id']) || ! isset($item['url']) || $item['id'] !== $nodeId) {
                continue;
            }

            return $this->getRemoteContent("{$item['url']}?action=fetch");
        }

        return null;
    }

    private function getRemoteContent($url)
    {
        $content = '';

        if (\function_exists('\curl_init')) {
            $ch = \curl_init();
            \curl_setopt_array($ch, array(
                \CURLOPT_URL            => $url,
                \CURLOPT_RETURNTRANSFER => true,
            ));
            $content = \curl_exec($ch);
            \curl_close($ch);

            return \json_decode($content, true) ?: null;
        }

        return \json_decode(\file_get_contents($url), true) ?: null;
    }
}
