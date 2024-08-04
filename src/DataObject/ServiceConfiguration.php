<?php

namespace App\DataObject;

class ServiceConfiguration
{
    private string $name;
    /** @var class-string */
    private string $class;
    private bool $forceNew;
    private array $constructorArgs;

    public function __construct($name, $values = [])
    {
        $this->name = $name;

        if (isset($values['__class'])) {
            $this->class = $values['__class'];
            unset($values['__class']);
        } else {
            $this->class = $name;
        }

        if (isset($values['__forceNew'])) {
            $this->forceNew = $values['__forceNew'];
            unset($values['__forceNew']);
        } else {
            $this->forceNew = false;
        }

        $this->constructorArgs = array_filter($values, fn($key) => !str_starts_with($key, '__'), ARRAY_FILTER_USE_KEY);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getConstructorArgs(): array
    {
        return $this->constructorArgs;
    }

    public function isForceNew(): bool
    {
        return $this->forceNew;
    }
}