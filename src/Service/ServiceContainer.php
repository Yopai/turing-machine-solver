<?php

namespace App\Service;

use App\DataObject\ServiceConfiguration;
use Closure;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;

class ServiceContainer
{
    private array $configuration = [];
    private array $instances = [];
    private array $namespaces = [];

    public function configure($name, $args = []): static
    {
        $this->configuration[$name] = new ServiceConfiguration($name, $args);
        return $this;
    }

    /**
     * @template T
     * @param class-string<T> $name
     * @param bool $doNotThrow
     * @return ?object<T>
     * @throws Exception
     */
    public function get(string $name, bool $doNotThrow = false): ?object
    {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $config = $this->getServiceConfiguration($name);
        if (!$config) {
            if ($doNotThrow) {
                return null;
            }
            throw new Exception("No such service $name. Make sure it is configured explicitly, or via its namespace.");
        }
        return $this->getInstance($config);
    }

    /**
     * @param Closure|string|string[] $callable
     * @param array $args
     * @return mixed|void
     * @throws ReflectionException
     * @throws Exception
     */
    public function call(array|Closure|string $callable, array $args = [])
    {
        if (is_string($callable)) {
            $className = $callable;
            $service = $this->get($className);
            $params = $this->getMethodParameters($className, '__invoke', $args);
            return $service(...$params);
        }
        if (is_array($callable)) {
            [$className, $methodName] = $callable;
            $service = $this->get($className);
            $params = $this->getMethodParameters($className, $methodName, $args);
            return $service->$methodName(...$params);
        }
        if ($callable instanceof Closure) {
            $rfunction = new ReflectionFunction($callable);
            $params = $this->getFunctionParameters($rfunction, $args);
            return $callable(...$params);
        }
    }

    /**
     * @throws Exception
     */
    private function getMethodParameters(string $className, string $methodName = null, $additionalParams = []): array
    {
        $rclass = (new ReflectionClass($className));
        if (!$methodName || $methodName === '__construct') {
            $rmethod = ($rclass->getConstructor());
            if (!$rmethod) {
                return [];
            }
        } else {
            $rmethod = $rclass->getMethod($methodName);
        }
        return $this->getFunctionParameters($rmethod, $additionalParams);
    }

    /**
     * @param ReflectionFunctionAbstract $rfunction
     * @param mixed $additionalParams
     * @return array
     * @throws Exception
     */
    public function getFunctionParameters(ReflectionFunctionAbstract $rfunction, mixed $additionalParams): array
    {
        $methodParams = $rfunction->getParameters();
        if (!$methodParams) {
            return [];
        }
        $namedParameters = [];
        foreach ($additionalParams as $key => $value) {
            if (is_string($key)) {
                $namedParameters[$key] = $value;
            }
        }

        // remove named parameters from 'additional'
        foreach ($namedParameters as $key => $value) {
            unset($additionalParams[$key]);
        }

        $params = [];

        foreach ($methodParams as $rparam) {
            $pname = $rparam->getName();
            if (isset($namedParameters[$pname])) {
                $params[$pname] = $namedParameters[$pname];
            } elseif ($type = $rparam->getType()) {
                $tname = $type->getName();
                if ($tname === self::class) {
                    $params[$pname] = $this;
                } else {
                    $instance = $this->get($tname, true);
                    if ($instance) {
                        $params[$pname] = $instance;
                    } elseif ($additionalParams) {
                        $params[$pname] = array_shift($additionalParams);
                    }
                }
            } elseif ($additionalParams) {
                $params[$pname] = array_shift($additionalParams);
            } elseif (!$rparam->isOptional()) {
                throw new Exception("No value for $pname when calling $rfunction");
            }
        }

        return $params;
    }

    public function configureNamespace(string $namespace): static
    {
        $this->namespaces [] = $namespace;
        return $this;
    }

    /**
     * @param $name
     * @return ServiceConfiguration
     * @throws Exception
     */
    public function getServiceConfiguration($name): ?ServiceConfiguration
    {
        if (isset($this->configuration[$name])) {
            return $this->configuration[$name];
        }

        foreach ($this->namespaces as $namespace) {
            if (str_starts_with($name, $namespace)) {
                return new ServiceConfiguration($name);
            }
        }

        return null;
    }

    /**
     * @param ServiceConfiguration $config
     * @return object
     * @throws Exception
     */
    public function getInstance(ServiceConfiguration $config): object
    {
        $name = $config->getName();
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $className = $config->getClass();
        $params = $this->getMethodParameters($className, '__construct', $config->getConstructorArgs());
        $instance = new $className(...$params);

        if (!$config->isForceNew()) {
            $this->instances[$name] = $instance;
        }
        return $instance;
    }

    public function set(string $name, object $object)
    {
        $this->instances[$name] = $object;
    }
}
