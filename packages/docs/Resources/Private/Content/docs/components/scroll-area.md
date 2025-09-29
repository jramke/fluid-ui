# Scroll Area

**A native scroll container with custom scrollbars.**

{% component: "ui:viewSourceButton", arguments: { "name": "scroll-area" } %}

{% component: "ui:scrollArea.examples.simple" %}

## Anatomy

```html
<primitives:scrollArea.root>
    <primitives:scrollArea.viewport>
        <primitives:scrollArea.content />
    </primitives:scrollArea.viewport>
    <primitives:scrollArea.scrollbar>
        <primitives:scrollArea.thumb />
    </primitives:scrollArea.scrollbar>
</primitives:scrollArea.root>
```
