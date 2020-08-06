<?php

namespace SlytherinCz\ApiClient\Endpoint;

use SlytherinCz\Contracts\ApiClient\DataObjectFactoryInterface;
use SlytherinCz\Contracts\ApiClient\DataObjectInterface;
use SlytherinCz\ApiClient\RequestWrapper;

trait CrudMethodsTrait
{
    /**
     * @param DataObjectInterface $data
     * @return DataObjectInterface
     * @throws \SlytherinCz\ApiClient\Service\RequestException
     * @throws \SlytherinCz\ApiClient\Service\ServerException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function show(): DataObjectInterface
    {
        return $this->sendAndConsumeSingleEntityRequest(
            'GET',
            $this->getSingleEntityUrl(),
            null,
            $this->requestWrapper,
            $this->getDataObjectFactory()
        );
    }

    /**
     * @param DataObjectInterface $data
     * @return DataObjectInterface
     * @throws \SlytherinCz\ApiClient\Service\RequestException
     * @throws \SlytherinCz\ApiClient\Service\ServerException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function create(DataObjectInterface $data): DataObjectInterface
    {
        return $this->sendAndConsumeSingleEntityRequest(
            'POST',
            $this->getEndpointUrl(),
            json_encode($data->toArray()),
            $this->requestWrapper,
            $this->getDataObjectFactory()
        );
    }

    /**
     * @param DataObjectInterface $data
     * @return DataObjectInterface
     * @throws \SlytherinCz\ApiClient\Service\RequestException
     * @throws \SlytherinCz\ApiClient\Service\ServerException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function update(DataObjectInterface $data): DataObjectInterface
    {
        return $this->sendAndConsumeSingleEntityRequest(
            'PUT',
            $this->getSingleEntityUrl(),
            json_encode($data->toArray()),
            $this->requestWrapper,
            $this->getDataObjectFactory()
        );
    }
    /**
     * @throws \SlytherinCz\ApiClient\Service\RequestException
     * @throws \SlytherinCz\ApiClient\Service\ServerException
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function delete(): void
    {
        $this->sendAndConsumeSingleEntityRequest(
            'DELETE',
            $this->getSingleEntityUrl(),
            null,
            $this->requestWrapper,
            $this->getDataObjectFactory()
        );
    }
}