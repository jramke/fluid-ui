<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ArrayFillViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('count', 'int', 'Number of items in the array', true);
        $this->registerArgument('fill', 'mixed', 'Optional value to fill array with', false, null);
    }

    public function render(): array
    {
        $count = (int) $this->arguments['count'];
        $fill = $this->arguments['fill'];

        return array_fill(0, $count, $fill);
    }
}
