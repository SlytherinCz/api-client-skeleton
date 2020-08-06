<?php

namespace SlytherinCz\ApiClient;

use SlytherinCz\ApiClient\Service\ListObject\ListFactory;
use SlytherinCz\ApiClient\Service\ResponseHandler;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class Client
{
    private ClientInterface $httpClient;
    private string $basePath;
    private RequestFactoryInterface $requestFactory;
    private RequestWrapper $requestWrapper;
    private array $defaultHeaders;
    private array $options;
    private ?StreamFactoryInterface $streamFactory;
    private ?UriFactoryInterface $uriFactory;
    /**
     * @var ListFactory
     */
    private ListFactory $listFactory;

    /**
     * Client constructor.
     * @param ClientInterface $httpClient
     * @param string $basePath
     * @param array $defaultHeaders
     * @param array $options
     * @param RequestFactoryInterface|null $requestFactory
     * @param StreamFactoryInterface|null $streamFactory
     * @param UriFactoryInterface|null $uriFactory
     */
    public function __construct(
        ClientInterface $httpClient,
        string $basePath,
        $options = [],
        $defaultHeaders = [
            'Content-Type' => 'application/json'
        ],
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?UriFactoryInterface $uriFactory = null
    ) {
        $this->httpClient = $httpClient;
        $this->basePath = $basePath;
        $this->defaultHeaders = $defaultHeaders;
        $this->options = $options;
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
        $this->uriFactory = $uriFactory ?? Psr17FactoryDiscovery::findUrlFactory();
        $this->createRequestWrapper();
    }

    private function createRequestWrapper()
    {
        $this->requestWrapper = new RequestWrapper(
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
            $this->basePath,
            $this->defaultHeaders,
            $this->options,
            new ResponseHandler($this->options)
        );
    }
}