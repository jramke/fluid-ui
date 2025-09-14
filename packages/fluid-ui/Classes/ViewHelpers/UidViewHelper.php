<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class UidViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('prefix', 'string', 'The prefix of the id', false, '');
    }

    public function render(): string
    {
        return ComponentUtility::uid($this->arguments['prefix']);
    }
}
