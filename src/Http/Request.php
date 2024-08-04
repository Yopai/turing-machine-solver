<?php

namespace App\Http;

class Request
{
    private string $pathinfo;
    private array $request = [
        'get' => [],
        'post' => [],
        'cookie' => [],
        'env' => [],
    ];

    public static function fromEnv(): static
    {
        $result = new static;
        if (php_sapi_name() === 'cli') {
            $result->initFromCli();
        } else {
            $result->initFromWeb();
        }
        return $result;
    }

    public function getPathinfo(): string
    {
        return $this->pathinfo;
    }

    private function initFromCli(): void
    {
        $this->pathinfo = '';
    }

    private function initFromWeb(): void
    {
        $this->pathinfo = urldecode($_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI']);
        $this->request['get'] = $_GET;
        $this->request['post'] = $_POST;
    }

    public function post(string $name)
    {
        return $this->request['post'][$name] ?? null;
    }

    public function get(string $name)
    {
        return $this->request['get'][$name] ?? null;
    }
}