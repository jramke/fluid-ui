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
 * Currently the `ui:portal` ViewHelper only supports rendering content into the end of the `<body>` element. 
 * Future versions may include support for custom target selectors.
 * 
 * ## Limitations
 * The portalled content is added back to the DOM via a custom middleware at the end of TYPO3's rendering process, which runs right before the `typo3/cms-frontend/content-length-headers` middleware.
 * So it may not be compatible in all situations, especially when other middlewares manipulate or take over the response content.
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
