<?php
declare(strict_types = 1);

namespace Spaze\UrlChecker\Client;

use RuntimeException;
use Spaze\UrlChecker\HttpResponse;

final class NativeHttpClient implements HttpClientInterface
{

	public function get(string $url, int $timeout): HttpResponse
	{
		$context = stream_context_create([
			'http' => [
				'method' => 'GET',
				'timeout' => $timeout,
				'ignore_errors' => true,
				'header' => "User-Agent: URLChecker/1.0 <+https://github.com/spaze/url-checker>\r\n",
			],
		]);

		$body = @file_get_contents($url, false, $context);
		if ($body === false) {
			throw new RuntimeException('Failed to fetch URL');
		}

		$statusCode = $this->extractHttpCode($http_response_header);
		if ($statusCode === null) {
			throw new RuntimeException('Unable to determine HTTP status code');
		}

		return new HttpResponse($statusCode, $body);
	}


	/**
	 * @param list<string> $headers
	 */
	private function extractHttpCode(array $headers): ?int
	{
		if (!isset($headers[0])) {
			return null;
		}

		if (preg_match('#HTTP/\d+\.\d+\s+(\d{3})#', $headers[0], $matches)) {
			return (int)$matches[1];
		}

		return null;
	}

}
