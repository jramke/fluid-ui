# Dialog

**A popup that opens on top of the entire page.**

{% component: "ui:viewSourceButton", arguments: { "name": "dialog" } %}

{% component: "ui:dialog.examples.simple" %}

## Anatomy

```html
<primitives:dialog.root>
    <primitives:dialog.trigger />
    <primitives:dialog.backdrop />
    <primitives:dialog.positioner>
        <primitives:dialog.content>
            <primitives:dialog.title />
            <primitives:dialog.description />
        </primitives:dialog.content>
    </primitives:dialog.positioner>
</primitives:dialog.root>
```
