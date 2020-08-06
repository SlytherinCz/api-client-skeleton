<?php

namespace SlytherinCz\ApiClient;

use SlytherinCz\ApiClient\Service\ResponseHandler;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class RequestWrapper
{
    /**
     * @var ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private RequestFactoryInterface $requestFactory;

    /**
     * @var string
     */
    private string $basePath;
    /**
     * @var StreamFactoryInterface
     */
    private StreamFactoryInterface $streamFactory;

    private array $defaultHeaders;

    private array $options;
    /**
     * @var UriFactoryInterface
     */
    private UriFactoryInterface $uriFactory;
    /**
     * @var ResponseHandler
     */
    private ResponseHandler $responseHandler;

    /**
     * RequestWrapper constructor.
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param UriFactoryInterface $uriFactory
     * @param string $basePath
     * @param array $defaultHeaders
     * @param array $options
     * @param ResponseHandler $responseHandler
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory,
        string $basePath,
        array $defaultHeaders,
        array $options,
        ResponseHandler $responseHandler
    )
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->basePath = $basePath;
        $this->streamFactory = $streamFactory;
        $this->defaultHeaders = $defaultHeaders;
        $this->options = $options;
        $this->uriFactory = $uriFactory;
        $this->responseHandler = $responseHandler;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param $body
     * @param $protocolVersion
     * @return RequestInterface
     */
    private function buildRequest(
        string $method,
        string $uri,
        array $headers,
        $body,
        $protocolVersion
    ): RequestInterface
    {
        $request = $this->requestFactory->createRequest(
            $method,
            $this->uriFactory->createUri($uri),
        )->withProtocolVersion($protocolVersion);

        if (!is_null($body)) {
            $request = $request->withBody($this->streamFactory->createStream($body));
        }

        $request = $this->attachHeaders($request, $headers, $this->defaultHeaders);
        return $request;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string|null $body
     * @param array $headers
     * @param string $protocolVersion
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     * @throws Service\RequestException
     * @throws Service\ServerException
     */
    public function request(
        string $method,
        string $uri,
        string $body = null,
        array $headers = [],
        string $protocolVersion = '1.1'
    ): ResponseInterface
    {
        $request = $this->buildRequest(
            $method,
            $this->basePath . $uri,
            $headers,
            $body,
            $protocolVersion
        );
        $response = $this->httpClient->sendRequest($request);
        $this->responseHandler->handleResponse($response, $request);
        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param array $headers
     * @param array $defaultHeaders
     * @return RequestInterface
     */
    private function attachHeaders(RequestInterface $request, array $headers, array $defaultHeaders): RequestInterface
    {
        foreach (array_merge($defaultHeaders, $headers) as $header => $value) {
            $request = $request->withHeader($header, $value);
        }
        return $request;
    }
}