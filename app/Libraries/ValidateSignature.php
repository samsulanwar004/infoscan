<?php

namespace App\Libraries;

class ValidateSignature
{
	private $signature;

	private $payloads;

	public function __construct($signature, $payloads)
	{
		$this->signature = $signature;
		$this->payloads = $payloads;
	}

	public function sortPayload(array $payload)
	{
		return ksort($payload);
	}

	public function makeSignature(array $payloads)
	{
		$sortedData = ksort($payload)
	}
}