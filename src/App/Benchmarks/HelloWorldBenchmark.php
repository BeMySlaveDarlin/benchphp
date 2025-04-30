<?php

declare(strict_types=1);

namespace App\Benchmarks;

use App\Service\Log;
use JsonException;

readonly class HelloWorldBenchmark implements BenchmarkInterface
{
    public function __construct(
        private Log $logger
    ) {
    }

    /**
     * @throws JsonException
     */
    public function handle(array $options = []): array
    {
        $iterations = $options['iterations'] ?? 100000;

        $minTime = PHP_FLOAT_MAX;
        $maxTime = 0;
        $totalTime = 0;
        $memoryUsage = 0;

        $startTest = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            json_encode(['message' => 'Hello, World!'], JSON_THROW_ON_ERROR);
            $duration = microtime(true) - $start;

            $minTime = min($minTime, $duration);
            $maxTime = max($maxTime, $duration);
            $totalTime += $duration;

            $memoryUsage = max($memoryUsage, memory_get_usage(true));
        }

        $totalTimeSec = microtime(true) - $startTest;

        $results = [
            'iterations' => $iterations,
            'avg_time,_sec' => $totalTime / $iterations,
            'min_time,_sec' => $minTime,
            'max_time,_sec' => $maxTime,
            'total_time,_sec' => $totalTimeSec,
            'memory,_bytes' => $memoryUsage,
            'memory,_kbytes' => $memoryUsage / 1024,
        ];
        $this->logger->log("HelloWorldBenchmark", $results);

        return $results;
    }
}
