<?php

namespace App\Service;

use Closure;

class Route
{
    private string $pathinfo;
    /** @var callable  */
    private $controllerClass;

    /**
     * @param string $pathinfo
     * @param callable|class-string<callable> $controllerClass
     * @param array $args
     */
    public function __construct(string $pathinfo, callable|string $controllerClass, private array $args = [])
    {
        $this->controllerClass = $controllerClass;
        $this->pathinfo = trim($pathinfo, '/');
    }

    public function getPathinfo(): string
    {
        return $this->pathinfo;
    }

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function getControllerArgs(): array
    {
        return $this->args;
    }

    /**
     * @return ?string
     */
    public function getControllerMethod(): ?string
    {
        return null;
    }

    public function matches($pathinfo): bool
    {
        return ($this->pathinfo === $pathinfo);
    }

    public function getParams(): array
    {
        return [];
    }
}

