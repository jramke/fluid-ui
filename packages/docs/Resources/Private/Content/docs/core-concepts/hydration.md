# Hydration

To connect component parts between server and client you can simply use the `ui:ref` ViewHelper. This abstracts away verbose data attributes or classes to identifiy parts manually with something like `my-complex-component-name-sub-part`. Next to the required `name` argument you can pass an optional `data` argument to the ViewHelper. The `data` argument is an array were each item is a key-value pair representing a data attribute. The keys are prefixed with `data-` automatically.

```html
<!-- Tooltip.Trigger.html -->

<div {ui:ref(name: 'trigger' , data: '{state: state}' )}></div>
```

would result in

```html
<!-- Tooltip.Trigger.html -->

<div data-scope="tooltip" data-part="trigger" data-state="{state}"></div>
```

Fluid UI adds a script tag to the document head for client-side hydration. It contains the props and rootId of each component on the page. To quickly access this data we have a `getHydrationData` function. This can be useful when you want to customize the components initialization.

For basic usage you can just use the `initAllComponentInstances` function to initialize all components of a type.

```js
// Collapsible.entry.ts

initAllComponentInstances('collapsible', ({ props }) => {
    const collapsible = new Collapsible(props);
    collapsible.init();
    return collapsible;
});
```
