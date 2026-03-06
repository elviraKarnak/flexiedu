<?php

namespace LearnDash\Certificate_Builder\Mpdf\Http;

use LearnDash\Certificate_Builder\Psr\Http\Message\RequestInterface;

interface ClientInterface
{

	public function sendRequest(RequestInterface $request);

}
