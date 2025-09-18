<?php

defined('TYPO3') or die('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vite'] = ['Praetorius\\ViteAssetCollector\\ViewHelpers'];

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Docs',
    'Docs',
    [
        \FluidUI\Docs\Controller\DocsController::class => 'show',
    ],
    [],
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);


$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['UrlEncodeMapper'] = \FluidUI\Docs\Routing\Aspect\UrlEncodeMapper::class;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = 'FluidUI\\Docs\\Components\\ComponentCollection';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['docs'] = ['FluidUI\\Docs\\ViewHelpers'];
