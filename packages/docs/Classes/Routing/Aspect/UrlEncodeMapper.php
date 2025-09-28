<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Routing\Aspect;

use TYPO3\CMS\Core\Routing\Aspect\StaticMappableAspectInterface;

class UrlEncodeMapper implements StaticMappableAspectInterface
{
    public function generate(string $value): ?string
    {
        return urlencode($value);
    }

    public function resolve(string $value): ?string
    {
        return urldecode($value);
    }
}
