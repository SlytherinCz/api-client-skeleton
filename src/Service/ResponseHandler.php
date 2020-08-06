<?php

namespace SlytherinCz\ApiClient\Service;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler
{
    private array $options;

    /**
     * ResponseHandler constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @throws RequestException
     * @throws ServerException
     */
    public function handleResponse(ResponseInterface $response, RequestInterface $request)
    {
        if(
            (!empty($this->options['exceptions']) && $this->options['exceptions'] === true)
            || empty($this->options['exceptions'])
        ) {
            $this->handleException($response, $request);
        }
    }

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @throws RequestException
     * @throws ServerException
     */
    private function handleException(ResponseInterface $response, RequestInterface $request)
    {
        $statusCode = $response->getStatusCode();
        if($statusCode >= 200 && $statusCode <= 299) {
            return;
        }
        if($statusCode >= 400 && $statusCode <= 499) {
            throw new RequestException($request, $response->getBody()->getContents(),$statusCode);
        }
        if($statusCode >= 500) {
            throw new ServerException($request, $response->getBody()->getContents(), $statusCode);
        }

    }
}