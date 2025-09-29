# Clipboard

**A component to copy text to the clipboard**

{% component: "ui:viewSourceButton", arguments: { "name": "clipboard" } %}

{% component: "ui:clipboard.examples.simple" %}

## Anatomy

```html
<primitives:clipboard.root>
    <primitives:clipboard.label />
    <primitives:clipboard.control>
        <primitives:clipboard.input />
        <primitives:clipboard.trigger>
            <primitives:clipboard.indicator />
        </primitives:clipboard.trigger>
    </primitives:clipboard.control>
</primitives:clipboard.root>
```
