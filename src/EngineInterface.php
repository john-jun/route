<?php
declare(strict_types=1);

namespace Air\Routing;

/**
 * Interface EngineInterface
 * @package Air\Routing\Engine
 */
interface EngineInterface
{
    public function insert(array $method, string $uri, array $userData): void;
    public function search(string $uri): array;
    public function getData(): array;
}
