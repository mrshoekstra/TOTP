<?php

class Base32
{
	private static $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

	public static function encode($data)
	{
		$base32Data = '';
		$binaryData = '';
		$padding = 0;

		foreach (str_split($data) as $char)
		{
			$binaryData .= str_pad(base_convert(ord($char), 10, 2), 8, '0', STR_PAD_LEFT);
		}

		$binaryDataLength = strlen($binaryData);
		$binaryDataPadded = str_pad($binaryData, ceil($binaryDataLength / 5) * 5, '0');

		for ($i = 0; $i < $binaryDataLength; $i += 5)
		{
			$chunk = substr($binaryDataPadded, $i, 5);
			$base32Data .= self::$base32Chars[bindec($chunk)];
		}

		$padding = 8 - ($binaryDataLength % 8);

		if ($padding > 0 && $padding < 8)
		{
			$base32Data = rtrim($base32Data, '=');
		}

		return $base32Data;
	}

	public static function decode($data)
	{
		$base32Data = strtoupper($data);
		$binaryData = '';
		$padding = 0;

		foreach (str_split($base32Data) as $char)
		{
			if ($char === '=')
			{
				break; // Padding character found, stop processing
			}

			$charValue = strpos(self::$base32Chars, $char);

			if ($charValue === false)
			{
				return false; // Invalid character found
			}

			$binaryData .= str_pad(base_convert($charValue, 10, 2), 5, '0', STR_PAD_LEFT);
		}

		$binaryDataLength = strlen($binaryData);
		$padding = $binaryDataLength % 8;

		if ($padding > 0)
		{
			$binaryData = substr($binaryData, 0, -$padding);
		}

		$decodedData = '';

		for ($i = 0; $i < $binaryDataLength; $i += 8)
		{
			$byte = substr($binaryData, $i, 8);
			$decodedData .= chr(bindec($byte));
		}

		return $decodedData;
	}

}
