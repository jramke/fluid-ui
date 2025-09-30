# Hydration

All props of each component that is needed for hydrating them the client side is stored in a `HydrationRegistry` class that renders a script tag in the document head via the core's [AssetCollector](https://api.typo3.org/13.4/classes/TYPO3-CMS-Core-Page-AssetCollector.html). This creates a global `FluidPrimitives` window variable that can be used to initialize components on the client side.

Each component that uses the [ui:ref](/docs/viewhelpers/ref) ViewHelper automatically gets registered for hydration.

## Initializing Components

To initialize components on the client side you can use the `initAllComponentInstances` function like this:

```ts
import { Collapsible } from 'fluid-primitives/primitives/collapsible';
import { initAllComponentInstances } from 'fluid-primitives';

(() => {
    initAllComponentInstances('collapsible', ({ props }) => {
        const collapsible = new Collapsible(props);
        collapsible.init();
        return collapsible;
    });
})();
```

This initializes all `collapsible` components on the page by extracting their props from the hydration data and passing them to the provided factory function where you can create and initialize the component instance. The initialized instance is then returned and stored in the `FluidPrimitives.uncontrolledInstances` window variable.

Ideally you include the initialization script in the root part of your component via the `<f:asset.script>` or `<vite:asset>` ([Vite Asset Collector](https://extensions.typo3.org/extension/vite_asset_collector)) ViewHelper, so its just loaded when you use the component.

## Connecting Component Parts

### On the Server with `ui:ref`

To connect parts of a component between server and client you can use the [ui:ref](/docs/viewhelpers/ref) ViewHelper.

This abstracts away verbose data attributes or classes to identifiy parts manually with something like `myRootEl.querySelector('[my-complex-component-name-sub-part]')`. Instead you can simply use the `ComponentHydrator` class to find parts by their name.

Inside of the `Tooltip/Trigger.html` template you would use the `ui:ref` ViewHelper like this:

```html
<button {ui:ref(name: 'trigger' )}></button>
```

this would then result in

```html
<button data-scope="tooltip" data-part="trigger" data-hydrate-tooltip="«rootId»"></button>
```

See more about [ui:ref](/docs/viewhelpers/ref).

### On the Client with `ComponentHydrator`

On the client side you can then use the `ComponentHydrator` class to find the part like this:

```ts
import { ComponentHydrator } from 'fluid-primitives';

const hydrator = new ComponentHydrator('tooltip', rootId);
const trigger = hydrator.getElement('trigger'); // or hydrator.getElements('trigger') for multiple elements
```

Most of the time you dont need the `ComponentHydrator` directly, because the `Component` base class already provides a `getElement` and `getElements` method that uses the `ComponentHydrator` internally.

## Controlled Components

By default all components are uncontrolled, meaning that they manage their own state internally. If you want to control the state of a component from the outside, you can set the `controlled` prop to `true`. This will prevent the `initAllComponentInstances` function from initializing the component automatically. You then need to initialize the component manually.

### Manually accessing hydration data with `getHydrationData`

For example when building a bigger custom component that uses one or more primitives internally, you might want to control the state of the primitives from the outside. In this case you would set the `controlled` prop to `true` and then initialize the primitives manually like this with the `getHydrationData` function inside your custom component:

```ts
import { Collapsible } from 'fluid-primitives/primitives/collapsible';
import { getHydrationData } from 'fluid-primitives';

export class MyCustomComponent {
    private collapsible: Collapsible;

    constructor(rootId: string) {
        const collapsibleData = getHydrationData('collapsible', `${rootId}-collapsible`);
        this.collapsible = new Collapsible({
            ...collapsibleData,
            onOpenChange: ({ open }) => {
                // do something when the collapsible is opened or closed
            },
        });
        this.collapsible.init();
    }
}
```
