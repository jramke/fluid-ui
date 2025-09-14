<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Component\ComponentPrimitivesCollection;
use Jramke\FluidUI\Constants;
use Jramke\FluidUI\Utility\ComponentUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperNodeInitializedEventInterface;
use TYPO3Fluid\Fluid\ViewHelpers\ArgumentViewHelper;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\TextNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Variables\ScopedVariableProvider;
use TYPO3Fluid\Fluid\Core\Variables\StandardVariableProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolverDelegateInterface;
use TYPO3Fluid\Fluid\ViewHelpers\SlotViewHelper;
use TYPO3Fluid\Fluid\Core\Component\ComponentDefinitionProviderInterface;

class UsePropsViewHelper extends AbstractViewHelper implements ViewHelperNodeInitializedEventInterface
{
    protected $escapeOutput = false;

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
        if (isset($arguments['name'])) {
            $name = $arguments['name'] instanceof TextNode ? $arguments['name']->getText() : '';
            if (empty($name)) {
                throw new \RuntimeException('The name argument must not be empty.', 1755936423);
            }

            if (str_starts_with($name, 'primitives:')) {
                $name = substr($name, strlen('primitives:'));
                $externalArgumentDefinitions = GeneralUtility::makeInstance(ComponentPrimitivesCollection::class)->getComponentDefinition($name)->getArgumentDefinitions();
            } else {
                $externalArgumentDefinitions = GeneralUtility::makeInstance(end($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui']))->getComponentDefinition($name)->getArgumentDefinitions();
            }

            $externalArgumentDefinitionsWithoutReserved = [...$externalArgumentDefinitions];
            foreach ($externalArgumentDefinitions as $name => $definition) {
                if (in_array($name, array_values(Constants::RESERVED_PROPS), true)) {
                    unset($externalArgumentDefinitionsWithoutReserved[$name]);
                }
            }

            $argumentDefinitions = $parsingState->getArgumentDefinitions();

            // Merge props marked for client from external component definition and current
            if (isset($argumentDefinitions['__propsMarkedForClient']) && isset($externalArgumentDefinitions['__propsMarkedForClient'])) {
                $argumentDefinitions['__propsMarkedForClient'] = new ArgumentDefinition(
                    '__propsMarkedForClient',
                    'array',
                    'DO NOT USE THIS ARGUMENT, IT IS FOR INTERNAL USE ONLY',
                    false,
                    array_merge($argumentDefinitions['__propsMarkedForClient']->getDefaultValue(), $externalArgumentDefinitions['__propsMarkedForClient']->getDefaultValue())
                );
                unset($externalArgumentDefinitionsWithoutReserved['__propsMarkedForClient']);
            }

            // Merge props marked for context from external component definition and current
            if (isset($argumentDefinitions['__propsMarkedForContext']) && isset($externalArgumentDefinitions['__propsMarkedForContext'])) {
                $argumentDefinitions['__propsMarkedForContext'] = new ArgumentDefinition(
                    '__propsMarkedForContext',
                    'array',
                    'DO NOT USE THIS ARGUMENT, IT IS FOR INTERNAL USE ONLY',
                    false,
                    array_merge($argumentDefinitions['__propsMarkedForContext']->getDefaultValue(), $externalArgumentDefinitions['__propsMarkedForContext']->getDefaultValue())
                );
                unset($externalArgumentDefinitionsWithoutReserved['__propsMarkedForContext']);
            }

            $mergedArgumentDefinitions = array_merge($externalArgumentDefinitionsWithoutReserved, $argumentDefinitions);

            $mergedArgumentDefinitions['spreadProps'] = new ArgumentDefinition(
                'spreadProps',
                'mixed',
                'Spread props from a component to another fluid component.',
                false,
                array_keys($externalArgumentDefinitions)
            );

            $parsingState->setArgumentDefinitions($mergedArgumentDefinitions);
        }
    }
}
