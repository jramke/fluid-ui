<?php

declare(strict_types=1);

namespace FluidPrimitives\Docs\Components;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('docs', 'Resources/Private/Components/ui'),
            ExtensionManagementUtility::extPath('docs', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }
}
