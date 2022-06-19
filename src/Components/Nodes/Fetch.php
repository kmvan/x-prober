<?php

namespace InnStudio\Prober\Components\Nodes;

use InnStudio\Prober\Components\Events\EventsApi;
use InnStudio\Prober\Components\Rest\RestResponse;
use InnStudio\Prober\Components\Rest\StatusCode;

final class Fetch extends NodesApi
{
    public function __construct()
    {
        EventsApi::on('init', function ($action) {
            switch ($action) {
                case 'nodes':
                    EventsApi::emit('fetchNodesBefore');
                    $response = new RestResponse(EventsApi::emit('nodes', array()));
                    $response->json()->end();
                    // no break
                case 'node':
                    EventsApi::emit('fetchNodeBefore');
                    $nodeId   = filter_input(\INPUT_GET, 'nodeId', \FILTER_DEFAULT);
                    $response = new RestResponse();

                    if ( ! $nodeId) {
                        $response->setStatus(StatusCode::$BAD_REQUEST)->json()->end();
                    }

                    $data = $this->getNodeData($nodeId);

                    if ( ! $data) {
                        $response->setStatus(StatusCode::$NO_CONTENT)->json()->end();
                    }

                    $response->setData($data)->json()->end();
            }

            return $action;
        }, 100);
    }

    private function getNodeData($nodeId)
    {
        foreach ($this->getNodes() as $item) {
            if ( ! isset($item['id']) || ! isset($item['url']) || $item['id'] !== $nodeId) {
                continue;
            }

            return $this->getRemoteContent("{$item['url']}?action=fetch");
        }
    }

    private function getRemoteContent($url)
    {
        $content = '';

        if (\function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt_array($ch, array(
                \CURLOPT_URL            => $url,
                \CURLOPT_RETURNTRANSFER => true,
            ));
            $content = curl_exec($ch);
            curl_close($ch);

            return json_decode($content, true) ?: null;
        }

        return json_decode(file_get_contents($url), true) ?: null;
    }
}
