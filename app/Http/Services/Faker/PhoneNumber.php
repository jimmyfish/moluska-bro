<?php

namespace App\Http\Services\Faker;

class PhoneNumber
{
	private array $headers = [
		"ID" => ["089", "087", "082", "081", "083", "085", "088"],
	];

	private array $commonLength = [
		"ID" => [11, 12, 13]
	];

	private string $numeric = "0123456789";

	public function generate($localization = "ID"): string
	{
		$header = $this->headers[$localization][rand(0, count($this->headers) - 1)];
		$length = $this->commonLength[$localization][rand(0, count($this->commonLength) - 1)];

		$phoneNumber = $header;

		while (strlen($phoneNumber) < $length) {
			$phoneNumber .= rand(0, 9);
		}

		return (string) $phoneNumber;
	}
}