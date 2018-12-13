<?php

namespace Certificate\Controllers;


use Slim\Container;

class Controller
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        if ($this->container->{$name}) {
            return $this->container->{$name};
        }
        return null;
    }

    public function __invoke($request, $response, $args) {
        return $response;
    }
}