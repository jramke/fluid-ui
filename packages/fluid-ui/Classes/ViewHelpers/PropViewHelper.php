<?php

declare(strict_types=1);

namespace Jramke\FluidUI\ViewHelpers;

use Jramke\FluidUI\Utility\ComponentUtility;
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

// This viewhelper is just doing what the f:argument viewhelper does, its just an abstraction to allow future changes
// TODO: check for reserved names like children, context, component, rootId, settings, etc.
class PropViewHelper extends AbstractViewHelper implements ViewHelperNodeInitializedEventInterface
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('name', 'string', 'name of the template argument', true);
        $this->registerArgument('type', 'string', 'type of the template argument', true); // TODO: allow typescript literals like "'primary' | 'secondary'"
        $this->registerArgument('description', 'string', 'description of the template argument');
        $this->registerArgument('optional', 'boolean', 'true if the defined argument should be optional', false, false);
        $this->registerArgument('default', 'mixed', 'default value for optional argument');
        $this->registerArgument('client', 'boolean', 'If true the argument is exposed in the components hydration data', false, false);
        $this->registerArgument('context', 'boolean', 'If true the argument is exposed in the components context', false, false);
    }

    public function render(): string
    {
        if (!ComponentUtility::isComponent($this->renderingContext)) {
            throw new \RuntimeException('The prop view helper can only be used inside a component context.', 1698255600);
        }

        if ($this->arguments['context'] && ComponentUtility::isRootComponent($this->renderingContext)) {
            throw new \RuntimeException('The context argument can only be used inside a composable component. All props from the root component are automatically available in the context.', 1698255601);
        }

        return '';
    }


    public function compile($argumentsName, $closureName, &$initializationPhpCode, ViewHelperNode $node, TemplateCompiler $compiler): string
    {
        return '\'\'';
    }

    public static function nodeInitializedEvent(ViewHelperNode $node, array $arguments, ParsingState $parsingState): void
    {
        // register an internal argumentDefinition with all the client props as default value so we can access them later in the component renderer
        if (isset($arguments['client']) && $arguments['client'] instanceof BooleanNode && $arguments['client']->evaluate(new RenderingContext())) {
            $name = $arguments['name'] instanceof TextNode ? $arguments['name']->getText() : (string)$arguments['name'];
            $argumentDefinitions = $parsingState->getArgumentDefinitions();

            $propsWithClientFlagDefinition = $argumentDefinitions['__propsMarkedForClient'] ?? null;
            $propsWithClientFlag = $propsWithClientFlagDefinition ? $propsWithClientFlagDefinition->getDefaultValue() : [];

            $propsWithClientFlag[$name] = true;

            $argumentDefinitions['__propsMarkedForClient'] = new ArgumentDefinition(
                '__propsMarkedForClient',
                'array',
                'DO NOT USE THIS ARGUMENT, IT IS FOR INTERNAL USE ONLY',
                false,
                $propsWithClientFlag
            );
            $parsingState->setArgumentDefinitions($argumentDefinitions);
        }

        if (isset($arguments['context']) && $arguments['context'] instanceof BooleanNode && $arguments['context']->evaluate(new RenderingContext())) {
            $name = $arguments['name'] instanceof TextNode ? $arguments['name']->getText() : (string)$arguments['name'];
            $argumentDefinitions = $parsingState->getArgumentDefinitions();

            $propsWithContextFlagDefinition = $argumentDefinitions['__propsMarkedForContext'] ?? null;
            $propsWithContextFlag = $propsWithContextFlagDefinition ? $propsWithContextFlagDefinition->getDefaultValue() : [];

            $propsWithContextFlag[$name] = true;

            $argumentDefinitions['__propsMarkedForContext'] = new ArgumentDefinition(
                '__propsMarkedForContext',
                'array',
                'DO NOT USE THIS ARGUMENT, IT IS FOR INTERNAL USE ONLY',
                false,
                $propsWithContextFlag
            );
            $parsingState->setArgumentDefinitions($argumentDefinitions);
        }

        ArgumentViewHelper::nodeInitializedEvent($node, $arguments, $parsingState);
    }
}
