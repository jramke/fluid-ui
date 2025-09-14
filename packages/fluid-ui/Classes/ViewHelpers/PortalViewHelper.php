<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Registry\PortalRegistry;
use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PortalViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void {}

    public function render(): mixed
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The portal viewhelper can only be used inside a component context.', 1753646062);
        }

        $rendered = $this->renderChildren();

        if (empty($rendered)) {
            return '';
        }

        PortalRegistry::add($rendered);
        return '';
    }
}
