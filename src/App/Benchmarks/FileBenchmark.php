<?php

declare(strict_types=1);

namespace App\Benchmarks;

use App\Service\Log;

readonly class FileBenchmark implements BenchmarkInterface
{
    public function __construct(
        private Log $logger
    ) {
    }

    public function handle(array $options = []): array
    {
        $iterations = $options['iterations'] ?? 1000;
        $filename = ROOT_PATH . '/var/bench_file.txt';

        $minTime = PHP_FLOAT_MAX;
        $maxTime = 0;
        $totalTime = 0;
        $memoryUsage = 0;

        $startTest = microtime(true);

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            file_put_contents($filename, "iteration $i");
            file_get_contents($filename);
            $duration = microtime(true) - $start;

            $minTime = min($minTime, $duration);
            $maxTime = max($maxTime, $duration);
            $totalTime += $duration;

            $memoryUsage = max($memoryUsage, memory_get_usage(true));
        }

        unlink($filename);

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
        $this->logger->log("FileBenchmark", $results);

        return $results;
    }
}
