<?php
require 'src/Enum.php';
require 'src/Base32.php';
require 'src/Totp.php';

/**
 * Basic
 * 
 * New Time-Based One-Time Password (TOTP)
 *   Database
 *    ├ DEFAULT User->2fa_enabled = false
 *    └ DEFAULT User->2fa_secret = NULL
 */

// Initiate
$totp = new Totp();

// Create QR code
$uri = $totp->uri(
	issuer: 'Marc',
	accountName: 'Hoekstra',
);

/**
 * Show QR (generated from $uri) to user
 *   Database
 *    └ UPDATE User->2fa_secret = $totp->getSecret();
 * (Don't update User->2fa_secret when User->2fa_enabled is true)
 * User scans QR code and types the response token into a form and submits
 */

/**
 * [ NEW PAGE ]
 */

$userToken = '34551060'; // Insert the token the user submitted

/**
 * $userSecret = Database
 *                └ SELECT User->2fa_secret
 */
$userSecret = 'QQR3GR5JT7PFWSVCETVKFQH6OYRB4HUG'; // Insert the user's secret you stored in the database before

// Verify QR code
$isValid = (new Totp(secret: $userSecret))->verify($userToken, 1); // Current and previous 1 token are valid

/**
 * If $isValid is true
 *   Database
 *   └ UPDATE User->2fa_enabled = true
 * 
 * 2fa is now enabled using TOTP
 */



/**
 * Advanced
 */
$totp = new Totp(
	algorithm: Algorithm::SHA512,
	digits: Digits::LENGTH_8,
	secretLength: 20,
);
$uri = $totp->uri(
	issuer: 'Marc',
	accountName: 'Hoekstra',
);
$token = $totp->generate();
$info = [
	'Current token' => $token,
	'Token history' => [
		'-1' => $totp->generate(1),
		'-2' => $totp->generate(2),
		'-3' => $totp->generate(3),
	],
	'Secret' => Base32::encode($totp->getSecret()),
	'URI' => $uri,
];

echo '
<!DOCTYPE html>
<html lang="en">
<title>Example</title>
<style>
	body {
		background: #111111;
		box-sizing: border-box;
		color: #cccccc;
		display: grid;
		font-family: monospace;
		margin: 0;
		min-height: 100dvh;
		padding: 2rem;
		place-content: center;
	}

	h1 {
		font-size: 2.5rem;
		margin: 0;
	}

	pre {
		margin-block: 2rem 2.5rem;
	}
</style>
<h1>Example:</h1>
<pre>' . json_encode($info, JSON_PRETTY_PRINT) . '</pre>
<img alt="" src="' . base64_decode('aHR0cHM6Ly9hcGkucXJzZXJ2ZXIuY29tL3YxL2NyZWF0ZS1xci1jb2RlLz9zaXplPTIwMHgyMDAmYmdjb2xvcj1jY2MmY29sb3I9MTExJm1hcmdpbj0wJnF6b25lPTEmZm9ybWF0PXN2ZyZkYXRhPQ') . rawurlencode($uri) . '">
';
