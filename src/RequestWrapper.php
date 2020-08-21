<?php

namespace SlytherinCz\ApiClient;

use SlytherinCz\ApiClient\Service\RequestException;
use SlytherinCz\ApiClient\Service\ResponseHandler;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class RequestWrapper {
    private ClientInterface $httpClient;

    private RequestFactoryInterface $requestFactory;

    private string $basePath;

    private StreamFactoryInterface $streamFactory;

    private array $defaultHeaders;

    private array $options;

    private UriFactoryInterface $uriFactory;

    private ResponseHandler $responseHandler;

    private ?RequestInterface $request = null;

    private int $maxRedirects = 10;

    private int $redirectCount = 0;

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

        if(!empty($options['max_redirects']) && is_int($options['max_redirects'])) {
            $this->maxRedirects = $options['max_redirects'];
        }
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
        $this->request = $this->buildRequest(
            $method,
            $this->basePath . $uri,
            $headers,
            $body,
            $protocolVersion
        );
        $sentRequest = clone $this->request;
        $response = $this->sendRequest($sentRequest);
        $this->responseHandler->handleResponse($response, $sentRequest);
        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws ClientExceptionInterface
     */
    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->httpClient->sendRequest($request);
        if($response->getStatusCode() >= 300 && $response->getStatusCode() < 400 ) {
            if($this->redirectCount > $this->maxRedirects) {
                throw new RequestException($request, 'Too many Redirects');
            }
            $redirectedRequest = clone $this->request;
            $redirectUri = $response->getHeader('location')[0];
            if(!is_string($redirectUri)) {
                throw new RequestException(
                    $redirectedRequest,
                    'Redirect HTTP Status received, but no Location header was present in the response'
                );
            }
            $redirectedRequest = $redirectedRequest->withUri($this->uriFactory->createUri($redirectUri));
            ++$this->redirectCount;
            return $this->sendRequest($redirectedRequest);
        }
        $this->redirectCount = 0;
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