<?php

namespace LearnDash\Certificate_Builder\Mpdf\PsrLogAwareTrait;

use LearnDash\Certificate_Builder\Psr\Log\LoggerInterface;

trait PsrLogAwareTrait 
{

	/**
	 * @var \LearnDash\Certificate_Builder\Psr\Log\LoggerInterface
	 */
	protected $logger;

	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}
	
}
