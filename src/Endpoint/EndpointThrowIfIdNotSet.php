<?php

namespace SlytherinCz\ApiClient\Endpoint;

trait EndpointThrowIfIdNotSet
{
    private function throwIfIdNotSet()
    {
        if(is_null($this->id)) {
            throw new \BadMethodCallException('Cannot use entity specific method without setting entity id first');
        }
    }
}