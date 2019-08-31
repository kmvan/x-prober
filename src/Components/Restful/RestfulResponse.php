<?php

namespace InnStudio\Prober\Components\Restful;

class RestfulResponse
{
    protected $data;

    protected $headers = array();

    protected $status = 200;

    public function __construct(array $data = null, $status = 200, array $headers = array())
    {
        $this->setData($data);
        $this->setStatus($status);
        $this->setHeaders($headers);
    }

    public function setHeader($key, $value, $replace = true)
    {
        if ($replace || ! isset($this->headers[$key])) {
            $this->headers[$key] = $value;
        } else {
            $this->headers[$key] .= ", {$value}";
        }
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toJson()
    {
        $data = $this->getData();

        if (null === $data) {
            return '';
        }

        return \json_encode($data);
    }

    public function dieJson()
    {
        \http_response_code($this->status);
        \header('Content-Type: application/json');
        \header('Expires: 0');
        \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s') . ' GMT');
        \header('Cache-Control: no-store, no-cache, must-revalidate');
        \header('Pragma: no-cache');

        $json = $this->toJson();

        if ('' === $json) {
            die;
        }

        die($json);
    }
}
