<?php

namespace App\Service\Utils\DI;

class DIContainer
{
    private $servicesStorage;
    private $factories;

    public function __construct(array $factories)
    {
        $this->factories = $factories;
        $this->servicesStorage = [];
    }

    /**
     * @param string $serviceName
     * @param array $arguments
     * @return mixed
     */
    public function make(string $serviceName, array $arguments = [])
    {
        if (isset($this->servicesStorage[$serviceName])) {
            return $this->servicesStorage[$serviceName];
        }

        if (!isset($this->factories[$serviceName])) {
            throw new DIException('Factory method is not found');
        }

        $factoryMethod = $this->factories[$serviceName];
        array_unshift($arguments, $this);
        $service = call_user_func_array($factoryMethod, $arguments);
        $this->servicesStorage[$serviceName] = $service;

        return $service;
    }
}