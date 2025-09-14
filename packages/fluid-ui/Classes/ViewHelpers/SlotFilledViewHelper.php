<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\ViewHelpers\SlotViewHelper;

class SlotFilledViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Name of the slot', false, SlotViewHelper::DEFAULT_SLOT);
    }

    public function render(): bool
    {
        $variableContainer = $this->renderingContext->getViewHelperVariableContainer();
        $slot = $variableContainer->get(SlotViewHelper::class, $this->arguments['name']);
        $content = trim(is_callable($slot) ? (string)$slot() : '');

        if (empty($content)) {
            return false;
        }

        return true;
    }
}
