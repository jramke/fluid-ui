<?php

declare(strict_types=1);

namespace Jramke\FluidUI\Component;

use Jramke\FluidUI\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentPrimitivesCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('fluid_ui', 'Resources/Private/Primitives'),
        ]);
        return $templatePaths;
    }
}
