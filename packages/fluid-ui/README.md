# Fluid UI – The headless component library for TYPO3

Build beautiful, composable component compositions in Fluid with better maintainability and client-side integration.

- [Fluid UI – The headless component library for TYPO3](#fluid-ui--the-headless-component-library-for-typo3)
    - [Introduction](#introduction)
    - [Why Fluid UI?](#why-fluid-ui)
        - [Composable Design](#composable-design)
        - [Unstyled](#unstyled)
        - [Accessibility](#accessibility)
    - [Getting Started](#getting-started)
        - [Installation](#installation)
        - [Setup ComponentCollection](#setup-componentcollection)
        - [Register Global Namespace](#register-global-namespace)
        - [Optional: Component Settings](#optional-component-settings)
        - [Creating Your First Component](#creating-your-first-component)
    - [Core Concepts](#core-concepts)
        - [Template Arguments](#template-arguments)
        - [Context](#context)
        - [Hydration](#hydration)
        - [Composition](#composition)
        - [Attributes](#attributes)
        - [Styling](#styling)
        - [Flexible Attributes with `ui:attributes`](#flexible-attributes-with-uiattributes)
        - [Building Composable Components](#building-composable-components)
    - [Advanced Features](#advanced-features)
        - [Component Context \& Hydration](#component-context--hydration)
        - [Client-Side Integration](#client-side-integration)
        - [Global Arguments](#global-arguments)
        - [Component-Specific Arguments](#component-specific-arguments)
    - [Development Setup](#development-setup)
        - [Local Development](#local-development)
        - [Start Environment](#start-environment)
        - [TYPO3 Setup](#typo3-setup)
    - [Inspiration](#inspiration)

## Introduction

Fluid UI is a headless component library for TYPO3 that allows developers to create flexible and reusable UI components using the Fluid templating engine. It promotes a composable approach to building user interfaces, making it easier to manage and maintain complex layouts.

It provides fully accessible and customizable primitives thanks to [Zag JS](https://zagjs.com/) as barebones.

## Why Fluid UI?

### Composable Design

Traditional (Fluid) components can quickly end up with bloated props that look like this.

```html
<ui:card
    rootClass="some-additional-class"
    image="path/to/image"
    title="Hello World"
    titleLevel="3"
    text="Lorem ipsum"
    cta="1"
    ctaVariant="secondary"
    ctaText="Learn more"
    ...
/>
```

Now imagine you need a use-case where you need two buttons. You will likely end up with another prop.

This becomes hard to maintain with all the conditional logic in templates. Fluid UI enables a [composable composition approach](https://medium.com/@guilherme.pomp/creating-react-components-with-the-composition-pattern-f59c895f27bc) inspired by modern frontend libraries like [Base UI](https://base-ui.com/), [Radix Primitives](https://www.radix-ui.com/primitives) or [Zag JS](https://zagjs.com/):

```html
<ui:card.root class="some-additional-class">
    <ui:card.image image="path/to/image" />
    <ui:card.title level="3">Hello World</ui:card.title>
    <ui:card.content>
        Lorem ipsum
        <ui:button link="1" variant="secondary">Learn more</ui:button>
    </ui:card.content>
</ui:card.root>
```

### Unstyled

Fluid UI components are unstyled by default, allowing you to apply your own styles and design system without any interference. This approach promotes consistency across your application and makes it easier to adapt the components to your specific needs. Check out the [Styling Guide]().

<!-- TODO styling guide link -->

### Accessibility

Thanks to [Zag JS](https://zagjs.com/), Fluid UI components ship with built-in accessibility, ensuring that all users can interact with your application effectively. This includes proper ARIA attributes, keyboard navigation, and focus management.

## Getting Started

Please take a look at the [documentation of fluid components](https://docs.typo3.org/other/typo3fluid/fluid/main/en-us/Usage/Components.html) first.

### Installation

```bash
composer req jramke/fluid-ui
```

### Setup ComponentCollection

Create a `ComponentCollection` class in your sitepackage:

```php
<?php

declare(strict_types=1);

namespace MyVendor\MyExt\Components;

use Jramke\FluidUI\Component\AbstractComponentCollection;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3Fluid\Fluid\View\TemplatePaths;

final class ComponentCollection extends AbstractComponentCollection
{
    public function getTemplatePaths(): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths([
            ExtensionManagementUtility::extPath('my_ext', 'Resources/Private/Components/ui'),
            ExtensionManagementUtility::extPath('my_ext', 'Resources/Private/Components'),
        ]);
        return $templatePaths;
    }
}
```

> [!TIP]
> A recommended structure is to keep your low level ui components like `Card`, `Button`, `Dialog`, `Accordion` and so on, in a dedicated `ui` folder and larger, more abstracted components like for example a `SomeSpecialDialog` in the `Components` root folder. Thats why we register two paths in the `ComponentCollection`.

### Register Global Namespace

Register the `ui` namespace for easier component usage:

```php
// ext_localconf.php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['ui'][] = 'MyVendor\\MyExt\\Components\\ComponentCollection';
```

> [!IMPORTANT]
> Fluid UI uses the `ui` namespace for its ViewHelpers. Add your path to the array rather than overwriting it.

### Optional: Component Settings

Provide custom settings that are exposed as `{settings}` inside the component templates (defaults to `lib.contentElement.settings`).

```typoscript
plugin.tx_fluidui {
    settings {}
}
```

### Creating Your First Component

Create a button component at `Resources/Private/Components/ui/Button/Button.html`:

```html
<ui:prop name="link" type="mixed" optional="{true}" />
<ui:prop name="variant" type="string" optional="{true}" default="primary" />

<ui:cn value="button button--{variant} {class}" as="class" />

<f:if condition="{link}">
    <f:then>
        <f:link.typolink
            parameter="{link}"
            class="{class}"
            additionalAttributes="{ui:attributes(asArray: true)}"
        >
            <f:slot />
        </f:link.typolink>
    </f:then>
    <f:else>
        <button class="{class}" {ui:attributes()}>
            <f:slot />
        </button>
    </f:else>
</f:if>
```

Use it in your templates:

```html
<ui:button variant="secondary" class="test" data-test="abc">Hello World</ui:button>
```

## Core Concepts

### Template Arguments

Normally Fluid's [f:argument](https://docs.typo3.org/other/typo3/view-helper-reference/main/en-us/Global/Argument.html) ViewHelper is used to register template arguments. Fluid UI provides the `ui:prop` ViewHelper that extends its functionality.

- `name`: The name of the property
- `type`: The data type of the property
- `optional`: Whether the property is optional
- `default`: The default value of the property
- `client`: Whether the property should be exposed to the client-side
- `context`: Whether the property should be exposed to the components context

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />
<ui:prop name="size" type="string" optional="{true}" default="medium" client="{true}" />
```

Each Component automatically receives as `class` prop.
Composable components also get a `rootId` prop that is filled automatically.

### Context

When building composable component parts we rely on a shared context. Each component has access to its context. This context exposes, among other things, the components `rootId`, `baseName` and all props defined in the root component.

As mentioned you can also expose other properties as needed via the `context` parameter in the `ui:prop` ViewHelper.

You can also use the `ui:context` ViewHelper to access others component contexts as well.

<!-- TODO -->

### Hydration

To connect component parts between server and client you can simply use the `ui:ref` ViewHelper. This abstracts away verbose data attributes or classes to identifiy parts manually with something like `my-complex-component-name-sub-part`. Next to the required `name` argument you can pass an optional `data` argument to the ViewHelper. The `data` argument is an array were each item is a key-value pair representing a data attribute. The keys are prefixed with `data-` automatically.

```html
<!-- Tooltip.Trigger.html -->

<div {ui:ref(name: 'trigger' , data: '{state: state}' )}></div>
```

would result in

```html
<!-- Tooltip.Trigger.html -->

<!-- TODO -->
<div data-scope="" data-state="{state}"></div>
```

Fluid UI adds a script tag to the document head for client-side hydration. It contains the props and rootId of each component on the page. To quickly access this data we have a `getHydrationData` function. This can be useful when you want to customize the components initialization.

For basic usage you can just use the `initAllComponentInstances` function to initialize all components of a type.

```js
// Collapsible.entry.ts

initAllComponentInstances('collapsible', data => {
    const collapsible = new Collapsible(data);
    collapsible.init();
    return collapsible;
});
```

### Composition

<!-- ids composition with tooltip and dialog trigger -->
<!--  -->

### Attributes

### Styling

### Flexible Attributes with `ui:attributes`

The usage of the `ui:attributes()` viewhelper automatically enables `additionalArguments`, allowing any HTML attribute to be passed to components:

```html
<ui:button class="custom-class" data-test="value" aria-label="Custom button"> Click me </ui:button>
```

Supports `asArray`, `skip`, and `only` parameters for fine-grained control.

### Building Composable Components

Create structured components by organizing sub-components in folders:

```
Resources/Private/Components/
├── Card/
│   ├── Root.html
│   ├── Image.html
│   ├── Title.html
│   └── Content.html
└── Button/
    └── Button.html
```

This enables the composable syntax shown in the introduction.

## Advanced Features

### Component Context & Hydration

Every component gets a unique `rootId` for client-side identification. The `ui:ref` viewhelper creates references for client-side hydration:

```html
<button {ui:ref(name: 'trigger')}>Click me</button>
```

This generates data attributes used by the `ComponentHydrator` class for seamless client-side integration.

### Client-Side Integration

Each components data is exposed via `window.__componentHydrationData` for easy client initialization.

Pass additional client data with `clientProps`:

```html
<ui:dialog.root clientProps="{animated: true, closeOnEscape: false}">
    <!-- ... -->
</ui:dialog.root>
```

### Global Arguments

Every component automatically has the following arguments registered:

- `class`: For CSS classes
- `rootId`: For manual ID specification on root components

### Component-Specific Arguments

**For components using `ui:ref`:**

- `clientProps`: Additional data for client-side hydration
- `controlled`: Manual initialization flag (passed to the hydration data as `__controlled`)

**For components using `ui:attributes`:**

- `attributes`: Pass generated data attributes to other components

Example tooltip trigger:

```html
<ui:button attributes="{ui:ref(name: 'trigger')} {ui:attributes()}" class="{class}">
    <f:slot />
</ui:button>
```

> [!IMPORTANT]
> When any of this conditions change you need to flush the cache so the component definition is regenerated.

## Development Setup

This extension uses DDEV based on [this example](https://github.com/a-r-m-i-n/ddev-for-typo3-extensions). However for this extension i would recommend adding `fluid-ui` as dependency to a standalone TYPO3 project.

### Local Development

Create a `docker-compose.mounts.yaml` in the standalone project to include your local `fluid-ui` package:

```yaml
services:
    web:
        volumes:
            - '/absolute/path/to/fluid-ui:/var/www/html/packages/fluid-ui'
```

### Start Environment

```bash
ddev start
```

### TYPO3 Setup

Install all TYPO3 versions:

```bash
ddev install-all
```

Or specific version:

```bash
ddev install-v13
```

Access at: https://v13.fluid-ui.ddev.site/

**Backend Login:**

- Username: `admin`
- Password: `Password1#`

## Inspiration

Fluid UI was created because there was no elegant solution for building robust, composable components in Fluid. The composition approach is inspired by the broader web ecosystem, particularly [Radix Primitives](https://www.radix-ui.com/primitives), while solving the challenge of connecting DOM nodes between client and server-rendered markup.

- Zag.js as barebones
- Radix UI
- Base UI
- Ark UI
