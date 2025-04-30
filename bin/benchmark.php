#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\App;
use Dotenv\Dotenv;

require __DIR__ . '/../bootstrap.php';
require ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();


if (empty($argv[1])) {
    print "Usage: bin/benchmark <BenchmarkName> [--option=value]" . PHP_EOL . PHP_EOL;
    exit(1);
}

$benchmark = $argv[1];

unset($argv[0], $argv[1]);
$options = [];
foreach ($argv as $item) {
	$option = explode('=', $item);
	$name = str_replace('--', '', $option[0]);
	$options[$name] = $option[1];
}

$kernel = new App();
if ($benchmark === 'run-all') {
    foreach ($kernel->getBenchmarks() as $benchmarkName) {
        $kernel->run($benchmarkName, $options);
    }
    exit(0);
}

$kernel->run($benchmark, $options);
