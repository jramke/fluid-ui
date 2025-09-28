# Why Fluid Primitives?

Why should you use Fluid Primitives for your next TYPO3 project and not just Bootstrap (Package).

## Composable Design

Traditional (Fluid) components can quickly end up with bloated props that look like this.

```html
<ui:card rootClass="some-additional-class" image="path/to/image" title="Hello World" titleLevel="3" text="Lorem ipsum" cta="1" ctaVariant="secondary" ctaText="Learn more" ... />
```

Now imagine you need a use-case where you need two buttons. You will likely end up with another prop.

This becomes hard to maintain with all the conditional logic in templates. Fluid Primitives enables a [composable composition approach](https://medium.com/@guilherme.pomp/creating-react-components-with-the-composition-pattern-f59c895f27bc) inspired by modern frontend libraries like [Base UI](https://base-ui.com/), [Radix Primitives](https://www.radix-ui.com/primitives) or [Zag JS](https://zagjs.com/):

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

## Unstyled

Fluid Primitives components are unstyled by default, allowing you to apply your own styles and design system without any interference. This approach promotes consistency across your application and makes it easier to adapt the components to your specific needs. Check out the [Styling Guide](/docs/core-concepts/styling).

## Accessibility

Thanks to [Zag JS](https://zagjs.com/), Fluid Primitives components ship with built-in accessibility, ensuring that all users can interact with your application effectively. This includes proper ARIA attributes, keyboard navigation, and focus management.
