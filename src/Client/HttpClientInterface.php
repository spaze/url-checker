<?php
declare(strict_types = 1);

namespace Spaze\UrlChecker\Client;

use Spaze\UrlChecker\HttpResponse;

interface HttpClientInterface
{

	public function get(string $url, int $timeout): HttpResponse;

}
