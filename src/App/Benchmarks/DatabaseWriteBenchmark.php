<?php

declare(strict_types=1);

namespace App\Benchmarks;

use App\Service\Log;
use PDO;

readonly class DatabaseWriteBenchmark implements BenchmarkInterface
{
    public function __construct(
        private Log $logger,
        private PDO $pdo
    ) {
    }

    public function handle(array $options = []): array
    {
        $iterations = $options['iterations'] ?? 1000;
        $this->pdo->exec("TRUNCATE TABLE benchmark_log RESTART IDENTITY");
        $stmt = $this->pdo->prepare("INSERT INTO benchmark_log (message) VALUES (:message)");

        $minTime = PHP_FLOAT_MAX;
        $maxTime = 0;
        $totalTime = 0;
        $memoryUsage = 0;

        $startTest = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $stmt->execute(['message' => "Benchmark write $i"]);
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
        $this->logger->log("DatabaseWriteBenchmark", $results);

        return $results;
    }
}
