<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Component\ComponentPrimitivesCollection;
use Jramke\FluidUI\Constants;
use Jramke\FluidUI\Utility\ComponentUtility;
use Jramke\FluidUI\Utility\PropsUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperNodeInitializedEventInterface;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;

/**
 * Use props from another component.
 *
 * This ViewHelper allows you to import all props from another component and register them for the current component.
 * This is helpful/needed when consuming the `primitives` components or when you want to reuse props from another component.
 *
 * ## Example
 * 
 * `Tooltip/Root.html` that uses the tooltip primitive:
 * ```html
 * <ui:useProps name="primitives:tooltip.root" />
 * 
 * <primitives:tooltip.root spreadProps="{true}">
 *     <f:slot />
 * </primitives:tooltip.root>
 * ```
 */
class UsePropsViewHelper extends AbstractViewHelper implements ViewHelperNodeInitializedEventInterface
{
    protected $escapeOutput = false;

    protected static ?ComponentPrimitivesCollection $componentPrimitivesCollection = null;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'name of component to use the props from', true);
    }

    public function render(): string
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The useProps viewhelper can only be used inside a component context.', 1698255600);
        }

        return '';
    }


    public function compile($argumentsName, $closureName, &$initializationPhpCode, ViewHelperNode $node, TemplateCompiler $compiler): string
    {
        return '\'\'';
    }

    public static function nodeInitializedEvent(ViewHelperNode $node, array $arguments, ParsingState $parsingState): void
    {
        static $componentPrimitivesCollection = null;
        if ($componentPrimitivesCollection === null) {
            $componentPrimitivesCollection = GeneralUtility::makeInstance(ComponentPrimitivesCollection::class);
        }

        if (isset($arguments['name'])) {
            $name = $arguments['name'] instanceof TextNode ? $arguments['name']->getText() : '';
            if (empty($name)) {
                throw new \RuntimeException('The name argument must not be empty.', 1755936423);
            }

            if (str_starts_with($name, 'primitives:')) {
                $name = substr($name, strlen('primitives:'));
                $externalArgumentDefinitions = $componentPrimitivesCollection->getComponentDefinition($name)->getArgumentDefinitions();
            } else {
                [$explodedNamespace, $explodedName] = explode(':', $name);
                $externalArgumentDefinitions = GeneralUtility::makeInstance(end($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces'][$explodedNamespace]))->getComponentDefinition($explodedName)->getArgumentDefinitions();
            }

            if (empty($externalArgumentDefinitions)) {
                return;
            }

            $externalArgumentDefinitionsWithoutReserved = PropsUtility::cleanupReservedProps([...$externalArgumentDefinitions]);

            $argumentDefinitions = $parsingState->getArgumentDefinitions();

            // Merge props marked for client from external component definition and current
            if (isset($argumentDefinitions[Constants::PROPS_MARKED_FOR_CLIENT_KEY]) && isset($externalArgumentDefinitions[Constants::PROPS_MARKED_FOR_CLIENT_KEY])) {
                $argumentDefinitions[Constants::PROPS_MARKED_FOR_CLIENT_KEY] = PropsUtility::createPropsMarkedForClientArgumentDefinition(array_merge($argumentDefinitions[Constants::PROPS_MARKED_FOR_CLIENT_KEY]->getDefaultValue(), $externalArgumentDefinitions[Constants::PROPS_MARKED_FOR_CLIENT_KEY]->getDefaultValue()));
                unset($externalArgumentDefinitionsWithoutReserved[Constants::PROPS_MARKED_FOR_CLIENT_KEY]);
            }

            // Merge props marked for context from external component definition and current
            if (isset($argumentDefinitions[Constants::PROPS_MARKED_FOR_CONTEXT_KEY]) && isset($externalArgumentDefinitions[Constants::PROPS_MARKED_FOR_CONTEXT_KEY])) {
                $argumentDefinitions[Constants::PROPS_MARKED_FOR_CONTEXT_KEY] = PropsUtility::createPropsMarkedForContextArgumentDefinition(array_merge($argumentDefinitions[Constants::PROPS_MARKED_FOR_CONTEXT_KEY]->getDefaultValue(), $externalArgumentDefinitions[Constants::PROPS_MARKED_FOR_CONTEXT_KEY]->getDefaultValue()));
                unset($externalArgumentDefinitionsWithoutReserved[Constants::PROPS_MARKED_FOR_CONTEXT_KEY]);
            }

            $mergedArgumentDefinitions = array_merge($externalArgumentDefinitionsWithoutReserved, $argumentDefinitions);

            $mergedArgumentDefinitions['spreadProps'] = PropsUtility::createSpreadPropsArgumentDefinition(array_keys($externalArgumentDefinitions));

            $parsingState->setArgumentDefinitions($mergedArgumentDefinitions);
        }
    }

    protected function getComponentPrimitivesCollection(): ComponentPrimitivesCollection
    {
        if (self::$componentPrimitivesCollection === null) {
            self::$componentPrimitivesCollection = GeneralUtility::makeInstance(ComponentPrimitivesCollection::class);
        }
        return self::$componentPrimitivesCollection;
    }
}
