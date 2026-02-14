#!/usr/bin/env php
<?php
declare(strict_types = 1);

namespace Spaze\UrlChecker;

use Spaze\UrlChecker\Client\NativeHttpClient;

require __DIR__ . '/../src/Client/HttpClientInterface.php';
require __DIR__ . '/../src/Client/NativeHttpClient.php';
require __DIR__ . '/../src/HttpResponse.php';
require __DIR__ . '/../src/UrlCheckResult.php';
require __DIR__ . '/../src/UrlChecker.php';

$usageError = function (string $message): void {
	fwrite(STDERR, "{$message}\n");
	exit(UrlChecker::EXIT_RUNTIME_ERROR);
};

if ($_SERVER['argc'] !== 5) {
	$usageError('Usage: check.php <url> <expected_http_code> <required_text> <forbidden_text>');
}

if (!is_array($_SERVER['argv']) || !array_is_list($_SERVER['argv'])) {
	$usageError('argv must be a list');
}

[, $url, $expectedCodeInput, $requiredText, $forbiddenText] = $_SERVER['argv'];

if (!is_string($url) || !is_string($expectedCodeInput) || !is_string($requiredText) || !is_string($forbiddenText)) {
	$usageError('Parameters must be strings');
}

if (!ctype_digit($expectedCodeInput)) {
	$usageError('Expected HTTP code must be numeric');
}

$checker = new UrlChecker(
	new NativeHttpClient(),
	(int)$expectedCodeInput,
	$requiredText,
	$forbiddenText,
);

$result = $checker->check($url);
if (!$result->success) {
	fwrite(STDERR, "ERROR: $result->message\n");
} else {
	echo "OK: $result->message\n";
}

exit($result->exitCode);
