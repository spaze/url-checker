<?php
declare(strict_types = 1);

namespace Spaze\UrlChecker;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Spaze\UrlChecker\Client\HttpClientInterface;

final class UrlCheckerTest extends TestCase
{

	private function createChecker(
		int $statusCode,
		string $body,
		int $expectedCode,
		string $requiredText,
		string $forbiddenText,
	): UrlChecker {
		$fakeClient = new readonly class ($statusCode, $body) implements HttpClientInterface {

			public function __construct(
				private int $statusCode,
				private string $body,
			) {
			}


			public function get(string $url, int $timeout): HttpResponse
			{
				return new HttpResponse($this->statusCode, $this->body);
			}

		};

		return new UrlChecker(
			$fakeClient,
			$expectedCode,
			$requiredText,
			$forbiddenText,
		);
	}


	public function testSuccess(): void
	{
		$checker = $this->createChecker(200, 'Hello World', 200, 'Hello', 'Fatal');
		$result = $checker->check('https://example.com/');

		$this->assertTrue($result->success);
		$this->assertSame(UrlChecker::EXIT_OK, $result->exitCode);
	}


	public function testHttpMismatch(): void
	{
		$checker = $this->createChecker(500, 'Error', 200, 'Required', 'Forbidden');
		$result = $checker->check('https://example.com/');

		$this->assertFalse($result->success);
		$this->assertSame(UrlChecker::EXIT_HTTP_MISMATCH, $result->exitCode);
	}


	public function testRequiredTextMissing(): void
	{
		$checker = $this->createChecker(200, 'Hello World', 200, 'MissingText', '');
		$result = $checker->check('https://example.com/');

		$this->assertFalse($result->success);
		$this->assertSame(UrlChecker::EXIT_REQUIRED_TEXT_MISSING, $result->exitCode);
	}


	public function testForbiddenTextFound(): void
	{
		$checker = $this->createChecker(200, 'Fatal error occurred', 200, '', 'Fatal');
		$result = $checker->check('https://example.com/');

		$this->assertFalse($result->success);
		$this->assertSame(UrlChecker::EXIT_FORBIDDEN_TEXT_FOUND, $result->exitCode);
	}


	public function testRuntimeErrorFromClient(): void
	{
		$fakeClient = new class implements HttpClientInterface {

			public function get(string $url, int $timeout): HttpResponse
			{
				throw new RuntimeException('Network failure');
			}

		};

		$checker = new UrlChecker(
			$fakeClient,
			200,
		);

		$result = $checker->check('https://example.com/');

		$this->assertFalse($result->success);
		$this->assertSame(UrlChecker::EXIT_RUNTIME_ERROR, $result->exitCode);
	}

}
