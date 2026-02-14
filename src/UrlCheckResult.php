<?php
declare(strict_types = 1);

namespace Spaze\UrlChecker;

final readonly class UrlCheckResult
{

	public function __construct(
		public bool $success,
		public int $exitCode,
		public ?string $message = null,
	) {
	}

}
