<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;
use RuntimeException;

class Container implements ContainerInterface
{
    private array $services = [];
    private array $shared = [];

    public function set(string $id, $service, bool $shared = true): void
    {
        $this->services[$id] = ['service' => $service, 'shared' => $shared];
    }

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new RuntimeException("Service $id not found.");
        }

        if ($this->services[$id]['shared']) {
            if (!isset($this->shared[$id])) {
                $this->shared[$id] = is_callable($this->services[$id]['service'])
                    ? $this->services[$id]['service']($this)
                    : $this->services[$id]['service'];
            }

            return $this->shared[$id];
        }

        return is_callable($this->services[$id]['service'])
            ? $this->services[$id]['service']($this)
            : $this->services[$id]['service'];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
