<?php

class Totp
{
	const DEFAULT_ALGORITHM = Algorithm::SHA1;
	const DEFAULT_DIGITS = Digits::LENGTH_6;
	const DEFAULT_PERIOD = Period::SEC_30;
	const DEFAULT_SECRET_LENGTH = 40;

	public function __construct(
		private ?Algorithm $algorithm = self::DEFAULT_ALGORITHM,
		private ?Digits $digits = self::DEFAULT_DIGITS,
		private ?Period $period = self::DEFAULT_PERIOD,
		#[\SensitiveParameter] private ?string $secret = null,
		private ?int $secretLength = self::DEFAULT_SECRET_LENGTH,
	)
	{
		// 🤫
	}

	public function generate(?int $prevToken = 0): string
	{
		/**
		 * Generate Time-Based One-Time Password (TOTP) by replacing the rolling
		 *   code event counter part from the HMAC-based One-Time Password (HOTP)
		 *   algorithm with a time key as defined in RFC 6238 (time / period)
		 */
		$timeKey = floor(time() / $this->period->value) - $prevToken;
		$counter = pack('N*', 0) . pack('N*', $timeKey);

		/**
		 * RFC 4226 5.3. Step 1 Generating an HOTP Value
		 */
		$hash = hash_hmac($this->algorithm->name, $counter, $this->getSecret(), true);

		/**
		 * RFC 4226 5.3. Step 2: Generate a 4-byte string (Dynamic Truncation)
		 */
		$offset = ord($hash[$this->algorithm->value]) & 0xf;
		$otp = (
			((ord($hash[$offset + 0]) & 0x7f) << 24) |
			((ord($hash[$offset + 1]) & 0xff) << 16) |
			((ord($hash[$offset + 2]) & 0xff) << 8) |
			(ord($hash[$offset + 3]) & 0xff)
		) % pow(10, $this->digits->value);

		return str_pad($otp, $this->digits->value, '0', STR_PAD_LEFT);
	}

	public function generateSecret(): string
	{
		return random_bytes($this->secretLength);
	}

	public function getSecret(): string
	{
		return $this->secret ??= $this->generateSecret();
	}

	/**
	 * Generate "otpauth://" URI
	 *
	 * Encode label and http query string according to:
	 * RFC 3986 Uniform Resource Identifier (URI): Generic Syntax
	 */
	public function uri(string $issuer, ?string $accountName = null): string
	{
		$data = [
			'secret'    => Base32::encode($this->getSecret()),
			'issuer'    => $issuer,
			'algorithm' => $this->algorithm->name,
			'digits'    => $this->digits->value,
			'period'    => $this->period->value,
		];
		$accountName = true === isset($accountName)
			? ':' . rawurlencode($accountName)
			: '';
		$label = rawurlencode($issuer) . $accountName;

		return 'otpauth://totp/' . $label . '?' . http_build_query(
			data: $data,
			encoding_type: PHP_QUERY_RFC3986,
		);
	}

	public function verify(string $userToken, ?int $validPrevTokens = 0): bool
	{
		$userToken = preg_replace('/[^0-9]+/', '', $userToken);
		$currentToken = $this->generate();
		$isValid = $userToken === $currentToken;

		if (false === $isValid && $validPrevTokens > 0)
		{
			for ($i = 1; $i <= $validPrevTokens; $i++)
			{
				$prevToken = $this->generate($i);

				if ($userToken === $prevToken)
				{
					$isValid = true;
					break;
				}
			}
		}

		return $isValid;
	}

}
