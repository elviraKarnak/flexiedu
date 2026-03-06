<?php

namespace LearnDash\Reports\Psr\Http\Client;

use LearnDash\Reports\Psr\Http\Message\RequestInterface;
use LearnDash\Reports\Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \LearnDash\Reports\Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
