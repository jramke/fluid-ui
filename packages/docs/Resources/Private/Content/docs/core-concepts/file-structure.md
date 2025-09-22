# File Structure

The recommended place for your components is inside the `Resources/Private/Components` folder of your extension or site package.

## Composable Components

Each component needs its own folder named after the component, e.g. `Button`, `Tooltip` or `Collapsible`. Inside that folder you should have at least a `Root.html` file that contains the root part of your component. Other parts of the component need to be in separate files, e.g. `Trigger.html`, `Content.html` or `Item.html`.

## Single-Part Components

When building a single-part component, such as a button you need to create a `Button/Button.html` file that contains the full implementation of the component.

## Opinionated Structure Recommendation

When your design system grows and you have a lot of components, its helpful to group your components inside the `Resources/Private/Components` folder. [Atomic Design](https://bradfrost.com/blog/post/atomic-web-design/) is a popular methodology to structure components. However in practice it can quickly become arbitrary and complex. Therefore we recommend a simplified structure:

Create a `ui/` folder for your more primitive and smaller components like buttons, inputs, tooltips and so on. These components should be as unopinionated as possible and provide the basic building blocks for your design system.

All other components for specific use-cases or more complex components can go into the root of the `Components/` folder or in other folders that make sense for your project.

In your `ComponentCollection` you can then just import both paths, because otherwise you would have to use the components from the `ui/` folder with their full path like `<ui:ui.button>` instead of just `<ui:button>`.

```php
$templatePaths->setTemplateRootPaths([
    ExtensionManagementUtility::extPath('ext', 'Resources/Private/Components/ui'),
    ExtensionManagementUtility::extPath('ext', 'Resources/Private/Components'),
]);
```

## Closed Components

Writing a few lines of code every time you need a simple `Alert` can be tedious. Creating a dedicated component encapsulates logic, simplifies the API, ensures consistent usage, and maintains clean code.

With the `/ui` folder approach its easy to create closed components for more common use-cases of composable components.

Here is an example file structure for a simple `Alert` component that uses the primitive parts from `ui/Alert`:

```html
<ui:prop name="title" type="string" />
<ui:prop name="text" type="string" optional="{true}" />
<ui:prop name="variant" type="string" default="info" />

<ui:alert.root class="{class}" variant="{variant}">
    <ui:alert.icon />
    <ui:alert.title>
        <h3>{title}</h3>
    </ui:alert.title>
    <f:if condition="{text}">
        <ui:alert.content>
            <p>{text}</p>
        </ui:alert.content>
    </f:if>
</ui:alert.root>
```

You can then store the file in `Components/Alert/Simple.html` and use it like this:

```html
<ui:alert.simple title="This is an alert" text="You can pass a title and text" />
```

```
└── /Alert
    └── Simple.html
└── /ui
    └── /Alert
        ├── Content.html
        ├── Icon.html
        ├── Root.html
        ├── Title.html
```
