<?php

declare(strict_types=1);

namespace App\Benchmarks;

use App\Service\Log;
use PDO;

readonly class DatabaseReadBenchmark implements BenchmarkInterface
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
        $stmtInsert = $this->pdo->prepare("INSERT INTO benchmark_log (message) VALUES (:message)");
        for ($i = 0; $i < $iterations; $i++) {
            $stmtInsert->execute(['message' => "Benchmark read $i"]);
        }

        $lastId = (int)$this->pdo->lastInsertId();
        $firstId = $lastId - $iterations + 1;

        $stmt = $this->pdo->prepare("SELECT message FROM benchmark_log WHERE id = :id");

        $minTime = PHP_FLOAT_MAX;
        $maxTime = 0;
        $totalTime = 0;
        $memoryUsage = 0;

        $startTest = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $stmt->execute(['id' => $firstId + $i]);
            $stmt->fetch(PDO::FETCH_ASSOC);
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
        $this->logger->log("DatabaseReadBenchmark", $results);

        return $results;
    }
}
