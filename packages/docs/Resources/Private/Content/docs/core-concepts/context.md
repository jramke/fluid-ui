# Context

When building composable component parts we rely on a shared context so each sub-component can access information and props from a parent.
Each component has its own context that can be accessed via the `{context}` variable.

## Default Context

The default context exposes, among other things, the components `rootId`, `baseName` and **all props defined in the root component**.

## Context Flag

As mentioned you can also expose props from sub-components if you set `context={true}` in the `ui:prop` ViewHelper. You will mostly not need this as this prop is then also only available in children of that sub-component.

## External Contexts

You can also get context from other components using the `ui:context` ViewHelper. It takes a `name` prop which is the name `baseName` of any other component.

So in this example we could get the cards context from inside the dialog template.

```html
<ui:card.root>
    ...
    <ui:dialog.root> ... </ui:dialog.root>
</ui:card.root>
```
