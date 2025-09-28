# Composition

When building complex UI's you will sometime need to make multiple components work together. That's where composition comes in.

## The `asChild` Prop

You can pass `asChild="{true}"` to a any component to have it merge its attributes into the first child element instead of rendering its default element. This is useful when you want to use your own element but still want the behavior of the component.

This pattern is inspired by Radix UI's [asChild API](https://www.radix-ui.com/primitives/docs/guides/composition). Note that in our case we just merge html attributes with a regex. When using `asChild` you have to make sure that the used element remains accessible and behaves as expected. For example if you use a `<div>` instead of a `<button>` for a trigger, you will have to add the necessary keyboard event handlers and ARIA attributes yourself.

### Example: Compose a trigger with your own element

```html
<ui:tooltip.root>
    <ui:tooltip.trigger asChild="{true}">
        <a href="https://fluid-primitives.joostramke.com">Fluid Primitives</a>
    </ui:tooltip.trigger>
    <ui:tooltip.content>This is the tooltip content.</ui:tooltip.content>
</ui:tooltip.root>
```

### Limitations and Notes

- You must provide exactly one element as the default slot. Text nodes or multiple top-level nodes can’t receive merged attributes.
- If the child already has an attribute, the component’s attribute is ignored.
- Currently the template part inside the `asChild` component has no access to the components context or props. See [#1132](https://github.com/TYPO3/Fluid/issues/1132) for updates.

## ID composition with `ids`

Zag.js machines operate on concrete DOM nodes. When composing components that need to work together, share IDs between them using the ids prop for proper accessibility and interaction.

```html
<f:variable name="triggerId" value="{ui:uid()}" />

<ui:collapsible.root ids="{trigger: triggerId}">
    <ui:tooltip.root ids="{trigger: triggerId}">
        <ui:collapsible.trigger asChild="1">
            <ui:tooltip.trigger>Collapsible with tooltip</ui:tooltip.trigger>
        </ui:collapsible.trigger>
        <ui:tooltip.content>Tooltip content</ui:tooltip.content>
    </ui:tooltip.root>
    <ui:collapsible.content>
        <p>Content of the collapsible section.</p>
    </ui:collapsible.content>
</ui:collapsible.root>
```

Both components share the same id through their ids props, creating proper accessibility bindings, aria-\* attributes and interaction behavior.
