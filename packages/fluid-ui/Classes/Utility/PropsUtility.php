<?php

declare(strict_types=1);

namespace Jramke\FluidUI\Utility;

use Jramke\FluidUI\Constants;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;

class PropsUtility
{
    public static function cleanupReservedProps(array $props, bool $useKeys = true): array
    {
        return array_filter($props, function ($key) {
            return !self::isReservedProp($key);
        }, $useKeys ? ARRAY_FILTER_USE_KEY : 0);
    }

    public static function isReservedProp(string $propKey): bool
    {
        return in_array($propKey, array_values(Constants::RESERVED_PROPS), true);
    }

    public static function createPropsMarkedForClientArgumentDefinition(mixed $defaultValue): ArgumentDefinition
    {
        return new ArgumentDefinition(
            Constants::PROPS_MARKED_FOR_CLIENT_KEY,
            'array',
            'DO NOT USE THIS ARGUMENT, IT IS FOR INTERNAL USE ONLY',
            false,
            $defaultValue
        );
    }

    public static function createPropsMarkedForContextArgumentDefinition(mixed $defaultValue): ArgumentDefinition
    {
        return new ArgumentDefinition(
            Constants::PROPS_MARKED_FOR_CONTEXT_KEY,
            'array',
            'DO NOT USE THIS ARGUMENT, IT IS FOR INTERNAL USE ONLY',
            false,
            $defaultValue
        );
    }

    public static function createSpreadPropsArgumentDefinition(mixed $defaultValue = false): ArgumentDefinition
    {
        return new ArgumentDefinition(
            'spreadProps',
            'mixed',
            'Spread props from a component to another fluid component.',
            false,
            $defaultValue
        );
    }
}
