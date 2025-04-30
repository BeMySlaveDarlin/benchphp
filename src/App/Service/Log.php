<?php

declare(strict_types=1);

namespace App\Service;

use Monolog\Logger;

class Log
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function log(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }
}
