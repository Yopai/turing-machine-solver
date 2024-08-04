<?php

namespace App\Service;

use App\Http\FileResponse;
use App\Http\HttpResponseInterface;
use App\Http\Request;
use Closure;
use Exception;

class Router
{
    public function __construct(private ServiceContainer $factory, private string $publicPath)
    {
    }

    /** @var Route[] */
    private array $routes = [];

    public function addRoute(Route $route): static
    {
        $this->routes [$route->getPathinfo()] = $route;
        return $this;
    }

    public function dispatch(Request $request): HttpResponseInterface
    {
        $pathinfo = trim($request->getPathinfo(), '/');
        $route = $this->findRoute($pathinfo);

        if ($route instanceof Closure) {
            return $this->factory->call($route);
        }

        $class = $route->getControllerClass();
        $method = $route->getControllerMethod();
        $args = $route->getControllerArgs();
        $routeParams = $route->getParams();
        if (!$method) {
            return $this->factory->call($class, array_merge($args, $routeParams));
        }

        return $this->factory->call([$class, $method], $args);
    }

    private function findRoute(string $pathinfo): Route|\Closure
    {
        foreach ($this->routes as $route) {
            if ($route->matches($pathinfo)) {
                return $route;
            }
        }

        $filename = $this->publicPath . '/' . $pathinfo;
        if (is_file($filename)) {
            if (preg_match('/.php$/', $filename)) {
                require $filename;
                exit;
            }
            else {
                return fn() => new FileResponse($filename);
            }
        }

        return new Route404();
    }

    /**
     * @param string $route
     * @param class-string $controller
     * @return $this
     */
    public function add(string $route, string $controller, ...$args): self
    {
        return $this->addRoute(new ExtendedRoute($route, $controller, $args));
    }
}