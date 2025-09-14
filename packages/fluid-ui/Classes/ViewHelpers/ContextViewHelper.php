<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ContextViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'The name of the component of which we want the context', true);
        $this->registerArgument('as', 'string', 'Variable name to assign the result to');
    }

    public function render(): mixed
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The context ViewHelper can only be used inside a component.', 1754253443);
        }

        if (empty($this->arguments['name'])) {
            throw new \RuntimeException('The "name" argument is required for the context view helper.', 1754253444);
        }

        $componentName = ComponentUtility::getComponentBaseNameFromContext($this->renderingContext);
        if ($componentName === $this->arguments['name']) {
            throw new \RuntimeException('You cannot access the context of the current component using the context view helper. Use the exposed "context" variable instead.', 1754253445);
        }

        $variableContainer = $this->renderingContext->getViewHelperVariableContainer();
        $context = $variableContainer->get(self::class, $this->arguments['name']);

        if ($this->arguments['as']) {
            $this->renderingContext->getVariableProvider()->add($this->arguments['as'], $context);
            return '';
        }

        return $context ?? null;
    }
}
