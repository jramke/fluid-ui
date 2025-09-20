<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Domain\Model\TagAttributes;
use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RefViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'Name of the ref', true);
        $this->registerArgument('asArray', 'boolean', 'If true, the ref will be rendered as an array instead of a string of data-attributes', false, false);
        $this->registerArgument('data', 'array', 'Additional data attributes to include in the ref. Associative array with key-value pairs. Each key is prefixed with "data-".', false, []);
    }

    public function render(): mixed
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The ref ViewHelper can only be used inside a component context.', 1698255600);
        }

        $componentName = ComponentUtility::getComponentBaseNameFromContext($this->renderingContext);
        $rootId = ComponentUtility::getRootIdFromContext($this->renderingContext);
        if (!$rootId) {
            throw new \RuntimeException('No rootId found for component ' . ComponentUtility::getComponentFullNameFromContext($this->renderingContext) . '.', 1756025267);
        }

        $additionalData = $this->arguments['data'] ?? [];
        if (!empty($additionalData)) {
            $additionalData = array_combine(
                array_map(function ($key) {
                    return "data-{$key}";
                }, array_keys($this->arguments['data'])),
                array_values($this->arguments['data'])
            );
        }

        $attributes = new TagAttributes(
            array_merge(
                [
                    'data-scope' => $componentName,
                    'data-part' => $this->arguments['name'],
                    "data-hydrate-{$componentName}" => $rootId,
                ],
                $additionalData
            )
        );

        if ($this->arguments['asArray']) {
            return $attributes->renderAsArray();
        }

        return (string)$attributes;
    }
}
