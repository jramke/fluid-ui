<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Domain\Model\TagAttributes;
use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class AttributesViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('skip', 'string', 'A comma-separated list of attributes to skip');
        $this->registerArgument('only', 'string', 'A comma-separated list of attributes to include. All other attributes will be skipped');
        $this->registerArgument('asArray', 'boolean', 'If true, the attributes will be rendered as an array instead of a string', false, false);
    }

    public function render(): mixed
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The attributes view helper can only be used inside a component context.', 1698255600);
        }

        $tagAttributes = $this->renderingContext->getVariableProvider()->getByPath('__tagAttributes');
        if (empty($tagAttributes)) {
            return '';
        }

        if (!$tagAttributes instanceof TagAttributes) {
            $tagAttributes = new TagAttributes($tagAttributes);
        }

        if (count($tagAttributes) === 0) {
            return '';
        }

        if ($this->arguments['skip'] && $this->arguments['only']) {
            throw new \RuntimeException('You cannot use both "skip" and "only" arguments at the same time.', 1698255600);
        }

        if ($this->arguments['asArray']) {
            return $tagAttributes->renderAsArray();
        }

        $skip = $this->arguments['skip'] ? GeneralUtility::trimExplode(',', $this->arguments['skip']) : [];
        if (!empty($skip)) {
            return $tagAttributes->renderWithSkip($skip);
        }

        $only = $this->arguments['only'] ? GeneralUtility::trimExplode(',', $this->arguments['only']) : [];
        if (!empty($only)) {
            return $tagAttributes->renderWithOnly($only);
        }
        return (string)$tagAttributes;
    }
}
