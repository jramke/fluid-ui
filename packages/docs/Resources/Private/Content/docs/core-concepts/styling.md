# Styling

Fluid UI is a headless component library that works with any styling solution. It provides functional styles for elements like popovers for positioning, while leaving presentation styles up to you. Some components also expose CSS variables that can be used for styling or animations.

## Data Attributes

Fluid UI components use `data-scope` and `data-part` attributes to target specific elements within a component. Interactive components often include `data-*` attributes to indicate their state. For example, here's what an open accordion item looks like:

```html
<div data-scope="accordion" data-part="item" data-state="open"></div>
```

For more details on each component's data attributes, refer to their respective documentation.

## Styling with CSS

When styling components with CSS, you can target the data attributes assigned to each component part for easy customization.

### Styling a Part

To style a specific component part, target its data-scope and data-part attributes:

```css
[data-scope='accordion'][data-part='item'] {
    border-bottom: 1px solid #e5e5e5;
}
```

### Styling a State

To style a component based on its state, use the data-state attribute:

```css
[data-scope='accordion'][data-part='item'][data-state='open'] {
    background-color: #f5f5f5;
}
```

### Class Names

Tip: If you prefer using classes instead of data attributes, utilize the class or className prop to add custom classes to Fluid UI components.

Class Names
If you prefer using classes instead of data attributes, utilize class or className prop to add custom classes to Fluid UI components.

Pass a class:

```html
<ui:accordion.root>
    <ui:accordion.item class="accordion-item">…</ui:accordion.item>
</ui:accordion.root>
```

Then use in styles:

```css
.accordion-item {
    border-bottom: 1px solid #e5e5e5;

    &[data-state='open'] {
        background-color: #f5f5f5;
    }
}
```

## Styling with Tailwind CSS

[Tailwind CSS](https://tailwindcss.com/) is a utility-first CSS framework providing a flexible way to style your components.

### Styling a Part

To style a part, apply classes directly to the parts using either class or className, depending on the JavaScript framework.

```html
<ui:accordion.root>
    <ui:accordion.item class="border-b border-gray-300">…</ui:accordion.item>
</ui:accordion.root>
```

### Styling a State

Leverage Tailwind CSS's variant selector to style a component based on its data-state attribute.

```html
<ui:accordion.root>
    <ui:accordion.item class="border-b border-gray-300 data-[state=open]:bg-gray-100">…</ui:accordion.item>
</ui:accordion.root>
```
