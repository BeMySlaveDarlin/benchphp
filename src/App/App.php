<?php

declare(strict_types=1);

namespace App;

use App\Benchmarks\BenchmarkInterface;
use App\Service\Container;
use Throwable;

class App
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->initializeContainer();
    }

    private function initializeContainer(): void
    {
        $config = require ROOT_PATH . '/config/services.php';

        foreach ($config as $id => $definition) {
            $this->container->set($id, $definition['service'], $definition['shared'] ?? true);
        }
    }

    public function run(string $benchmarkName, array $options = []): void
    {
        try {
            $benchmarkClass = "App\Benchmarks\\$benchmarkName";
            if (!$this->container->has($benchmarkClass)) {
                print "Benchmark $benchmarkName не найден." . PHP_EOL . PHP_EOL;
                exit(1);
            }

            $benchmark = $this->container->get($benchmarkClass);

            if (!$benchmark instanceof BenchmarkInterface) {
                print "$benchmarkName не реализует интерфейс BenchmarkInterface." . PHP_EOL . PHP_EOL;
                exit(1);
            }

            $result = $benchmark->handle($options);

            $this->printAsTable($benchmarkName, $result);
        } catch (Throwable $throwable) {
            print $throwable->getMessage() . PHP_EOL . PHP_EOL;
        }
    }

    public function printSystemInfo(): void
    {
        $phpVersion = PHP_VERSION;
        $os = php_uname();
        $cpu = trim(shell_exec("nproc") ?? 'N/A') . " cores";
        $mem = round(trim(shell_exec("grep MemTotal /proc/meminfo | awk '{print $2}'") ?? 'N/A') / (1024 * 1024), 3) . " GB";

        echo "\033[1;34mSystem Information:\033[0m" . PHP_EOL;
        echo str_pad("OS:", 20) . $os . PHP_EOL;
        echo str_pad("PHP Version:", 20) . $phpVersion . PHP_EOL;
        echo str_pad("CPU Cores:", 20) . $cpu . PHP_EOL;
        echo str_pad("Memory:", 20) . $mem . PHP_EOL;
        echo PHP_EOL;
    }

    public function getBenchmarks(): array
    {
        $benchmarks = [];
        $directory = ROOT_PATH . '/src/App/Benchmarks';

        foreach (scandir($directory) as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $className = 'App\\Benchmarks\\' . $name;
            if (!class_exists($className)) {
                continue;
            }

            if (is_subclass_of($className, BenchmarkInterface::class)) {
                $benchmarks[] = $name;
            }
        }

        return $benchmarks;
    }

    private function printAsTable(string $title, array $result): void
    {
        $green = "\033[32m";
        $cyan = "\033[36m";
        $bold = "\033[1m";
        $reset = "\033[0m";

        echo PHP_EOL . "{$bold}{$cyan}=== {$title} ==={$reset}" . PHP_EOL;
        echo "{$bold}" . str_pad("Metric", 25) . str_pad("Value", 20) . "{$reset}" . PHP_EOL;
        echo str_repeat('-', 45) . PHP_EOL;

        foreach ($result as $key => $value) {
            $label = str_replace('_', ' ', ucfirst($key));
            if (is_float($value)) {
                $value = number_format($value, 10, '.', '');
            }
            echo str_pad($label, 25) . "{$green}{$value}{$reset}" . PHP_EOL;
        }

        echo PHP_EOL;
    }
}
