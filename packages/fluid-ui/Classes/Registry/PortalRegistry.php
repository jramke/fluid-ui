<?php

declare(strict_types=1);

namespace Jramke\FluidUI\Registry;

class PortalRegistry
{
    private static array $registry = [];

    public static function add(string $html): void
    {
        self::$registry[] = $html;
    }

    public static function getAll(): array
    {
        return self::$registry;
    }

    public static function clear(): void
    {
        self::$registry = [];
    }
}
