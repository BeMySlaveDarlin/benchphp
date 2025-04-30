<?php

declare(strict_types=1);

use App\Benchmarks\DatabaseReadBenchmark;
use App\Benchmarks\DatabaseWriteBenchmark;
use App\Benchmarks\FileBenchmark;
use App\Benchmarks\HelloWorldBenchmark;
use App\Benchmarks\MemoryStressBenchmark;
use App\Service\Container;
use App\Service\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

return [
    Log::class => [
        'service' => static function () {
            /** @var HandlerInterface $streamHandler */
            $streamHandler = new StreamHandler(ROOT_PATH . '/var/logs/benchmark.log', Level::Info);
            $logger = new Logger('benchmark');
            $logger->pushHandler($streamHandler);

            return new Log($logger);
        },
        'shared' => true,
    ],
    HelloWorldBenchmark::class => [
        'service' => static function (Container $container) {
            return new HelloWorldBenchmark($container->get(Log::class));
        },
        'shared' => true,
    ],
    FileBenchmark::class => [
        'service' => static function (Container $container) {
            return new FileBenchmark($container->get(Log::class));
        },
        'shared' => true,
    ],
    MemoryStressBenchmark::class => [
        'service' => static function (Container $container) {
            return new MemoryStressBenchmark($container->get(Log::class));
        },
        'shared' => true,
    ],
    DatabaseReadBenchmark::class => [
        'service' => static function (Container $container) {
            $pdo = new PDO(
                "pgsql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );

            return new DatabaseReadBenchmark($container->get(Log::class), $pdo);
        },
        'shared' => true,
    ],
    DatabaseWriteBenchmark::class => [
        'service' => static function (Container $container) {
            $pdo = new PDO(
                "pgsql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );

            return new DatabaseWriteBenchmark($container->get(Log::class), $pdo);
        },
        'shared' => true,
    ],
];
