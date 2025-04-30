#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\App;
use Dotenv\Dotenv;

require __DIR__ . '/../bootstrap.php';
require ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

$options = getopt('', ['*']);

$options = array_reduce(
    array_keys($options),
    static function ($carry, $key) use ($options) {
        $carry[$key] = $options[$key] ?? null;

        return $carry;
    },
    []
);

$benchmark = $argv[1] ?? null;
if (!$benchmark) {
    print "Usage: bin/benchmark <BenchmarkName> [--option=value]" . PHP_EOL . PHP_EOL;
    exit(1);
}

$kernel = new App();
if ($benchmark === 'run-all') {
    foreach ($kernel->getBenchmarks() as $benchmarkName) {
        $kernel->run($benchmarkName, $options);
    }
    exit(0);
}

$kernel->run($benchmark, $options);
