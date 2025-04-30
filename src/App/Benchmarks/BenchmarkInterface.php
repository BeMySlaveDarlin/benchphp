<?php

declare(strict_types=1);

namespace App\Benchmarks;

interface BenchmarkInterface
{
    public function handle(array $options = []): array;
}
