# Primitives

Fluid Primitives exposes headless primitives that you can use to build your own components. These primitives are built on top of [Zag.js](https://zagjs.com/) state machines and provide the necessary behavior and accessibility features.

They are exposed in the `primitives` namespace.

## Anatomy

For example the `Tooltip` anatomy looks like this:

```html
<primitives:tooltip.root>
    <primitives:tooltip.trigger />
    <primitives:tooltip.positioner>
        <primitives:tooltip.content>
            <primitives:tooltip.arrow />
        </primitives:tooltip.content>
    </primitives:tooltip.positioner>
</primitives:tooltip.root>
```

You could use them directly in your templates and add your classes and styles. But they are mainly meant to be used as building blocks for your own components.

## Building Components with Primitives

Its recommended to create components for each primitive part, so you can easily customize the styling and behavior of your design system in one place. See the [File Structure](/docs/core-concepts/file-structure) documentation for more details.

In this case we could simplify it a little bit and wrap the `positioner`, `content` and `arrow` parts into a single `content` part. Inside there you can then also use the [ui:portal](/docs/viewhelpers/portal) ViewHelper if you want. Take a look at the [Tooltip component from the docs sitepackage](https://github.com/jramke/fluid-primitives/tree/main/packages/docs/Resources/Private/Components/ui/Tooltip).

Inside the `Root.html` file of your component you can then use the [ui:useProps](/docs/viewhelpers/useprops) ViewHelper to import all props from the primitive and pass them to the primitive root part with `spreadProps="{true}"`.

```html
<ui:useProps name="primitives:tooltip.root" />

<primitives:tooltip.root spreadProps="{true}">
    <f:slot />
</primitives:tooltip.root>

<vite:asset entry="EXT:docs/Resources/Private/Components/ui/Tooltip/source/Tooltip.entry.ts" />
```

Note that we also load the component's JavaScript entry file with the [Vite Asset Collector](https://extensions.typo3.org/extension/vite_asset_collector) ViewHelper here instead of in our main JavaScript file so its only included if we use the component.

Now we can simply use our `ui:tooltip` component like this:

```html
<ui:tooltip.root>
    <ui:tooltip.trigger>Hover me</ui:tooltip.trigger>
    <ui:tooltip.content>This is the tooltip content.</ui:tooltip.content>
</ui:tooltip.root>
```
