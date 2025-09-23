<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Registry\PortalRegistry;
use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This ViewHelper allows you to render content in a different part of the DOM tree than where it is defined. 
 * This is particularly useful for modals, tooltips, or any component that needs to break out of its parent container for styling or positioning reasons.
 * 
 * You need to use this ViewHelper in conjunction with the [ui:portalContainer](./portalContainer) ViewHelper, which acts as the target container for all portalled content.
 * 
 * ## Example
 * Common use case inside `Tooltip/Content.html`:
 * ```html
 * <ui:portal>
 *     <primitives:tooltip.positioner>
 *         <primitives:tooltip.content>
 *             <primitives:tooltip.arrow />
 *             <f:slot />
 *         </primitives:tooltip.content>
 *     </primitives:tooltip.positioner>
 * </ui:portal>
 * ```
 */
class PortalViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'The name of the target container', false, 'default');
    }

    public function render(): mixed
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The portal viewhelper can only be used inside a component context.', 1753646062);
        }

        $rendered = $this->renderChildren();

        if (empty($rendered)) {
            return '';
        }

        PortalRegistry::add($this->arguments['name'], $rendered);
        return '';
    }
}
