<?php

namespace SlytherinCz\ApiClient\Service;

use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Throwable;

class RequestException extends \Exception implements RequestExceptionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    public function __construct(RequestInterface $request, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}