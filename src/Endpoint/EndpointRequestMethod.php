<?php

namespace SlytherinCz\ApiClient\Endpoint;

use SlytherinCz\Contracts\ApiClient\DataObjectFactoryInterface;
use SlytherinCz\Contracts\ApiClient\DataObjectInterface;
use SlytherinCz\ApiClient\RequestWrapper;

trait EndpointRequestMethod
{
    use EndpointThrowIfIdNotSet;

    /**
     * @param string $method
     * @param string $url
     * @param string|null $body
     * @param RequestWrapper $requestWrapper
     * @param DataObjectFactoryInterface $factory
     * @return DataObjectInterface
     * @throws \SlytherinCz\ApiClient\Service\RequestException
     * @throws \SlytherinCz\ApiClient\Service\ServerException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function sendAndConsumeSingleEntityRequest(
        string $method,
        string $url,
        ?string $body,
        RequestWrapper $requestWrapper,
        DataObjectFactoryInterface $factory
    ): DataObjectInterface
    {
        $this->throwIfIdNotSet();
        $response = $requestWrapper->request(
            $method,
            $url,
            $body
        );
        return $factory->create(
            json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)
        );
    }
}