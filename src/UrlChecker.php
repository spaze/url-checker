<?php
declare(strict_types = 1);

namespace Spaze\UrlChecker;

use Spaze\UrlChecker\Client\HttpClientInterface;
use Throwable;

final readonly class UrlChecker
{
	public const int EXIT_OK = 0;
	public const int EXIT_HTTP_MISMATCH = 1;
	public const int EXIT_REQUIRED_TEXT_MISSING = 2;
	public const int EXIT_FORBIDDEN_TEXT_FOUND = 3;
	public const int EXIT_RUNTIME_ERROR = 4;


	public function __construct(
		private HttpClientInterface $httpClient,
		private int $expectedCode,
		private string $requiredText = '',
		private string $forbiddenText = '',
		private int $timeout = 30,
	) {
	}


	public function check(string $url): UrlCheckResult
	{
		if ($this->requiredText === $this->forbiddenText) {
			return new UrlCheckResult(
				false,
				self::EXIT_RUNTIME_ERROR,
				"Required text '{$this->requiredText}' must not be the same as forbidden text '{$this->forbiddenText}'",
			);
		}

		try {
			$response = $this->httpClient->get($url, $this->timeout);
		} catch (Throwable $e) {
			return new UrlCheckResult(
				false,
				self::EXIT_RUNTIME_ERROR,
				$e->getMessage(),
			);
		}

		if ($response->statusCode !== $this->expectedCode) {
			return new UrlCheckResult(
				false,
				self::EXIT_HTTP_MISMATCH,
				"HTTP code mismatch; expected {$this->expectedCode}, got {$response->statusCode}",
			);
		}

		if (
			$this->requiredText !== '' &&
			!str_contains($response->body, $this->requiredText)
		) {
			return new UrlCheckResult(
				false,
				self::EXIT_REQUIRED_TEXT_MISSING,
				"Required text '{$this->requiredText}' not found",
			);
		}

		if (
			$this->forbiddenText !== '' &&
			str_contains($response->body, $this->forbiddenText)
		) {
			return new UrlCheckResult(
				false,
				self::EXIT_FORBIDDEN_TEXT_FOUND,
				"Forbidden text '{$this->forbiddenText}' found",
			);
		}

		return new UrlCheckResult(true, self::EXIT_OK, "{$url} status {$this->expectedCode}, contains '{$this->requiredText}', doesn't contain '{$this->forbiddenText}'");
	}

}
