# Getting Started

Before you start, please take a look at the core [documentation of Fluid Components](https://docs.typo3.org/other/typo3fluid/fluid/main/en-us/Usage/Components.html) first.

## Installation

Install Fluid Primitives via Composer:

```bash
composer require jramke/fluid-primitives
```

Then you need to add the client-side files. You can either use `EXT:fluid_primitives/Resources/Public/JavaScript/dist/`or you can just add `fluid-primitives` to your `package.json`.

```bash
npm install ./vendor/jramke/fluid-primitives
```

## Setup Component Collection

Create a `ComponentCollection` class in your sitepackage:

```php
<?php

declare(strict_types=1);

namespace MyVendor\MyExt\Components;

use Jramke\FluidPrimitives\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('my_ext', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }
}
```

Notice that we did not use the `TYPO3Fluid\Fluid\Core\Component\AbstractComponentCollection` and instead extended from `Jramke\FluidPrimitives\Component\AbstractComponentCollection`. This is required to make Fluid Primitives work.

## Register Global Namespace

Register the `ui` namespace for easier component usage:

```php
// ext_localconf.php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = 'MyVendor\\MyExt\\Components\\ComponentCollection';
```

{% component: "ui:alert.simple", arguments: {"title": "Fluid Primitives uses the `ui` namespace for its ViewHelpers. Add your path to the array rather than overwriting it.", "variant": "warning"} %}

## Optional: Component Settings

Provide custom settings that are exposed as `{settings}` inside the component templates (defaults to `lib.contentElement.settings`).

```typoscript
plugin.tx_fluidprimitives {
    settings {}
}
```

## First Component

Create a button component at `Resources/Private/Components/ui/Button/Button.html`:

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />

<ui:cn value="button button--{variant} {class}" as="class" />

<button class="{class}" {ui:attributes()}>
    <f:slot />
</button>
```

And use it in your templates like this:

```html
<ui:button variant="secondary" class="another-class" data-test="abc"> Hello World </ui:button>
```
