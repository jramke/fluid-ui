# Arguments

When building Components, you often want to make them configurable. This is done by defining arguments (or props) that can be passed to the component.

Normally Fluid's [f:argument](https://docs.typo3.org/other/typo3/view-helper-reference/main/en-us/Global/Argument.html) ViewHelper is used to register template arguments. Fluid UI provides an `ui:prop` ViewHelper that extends the basic `f:argument` ViewHelper.

- `name`: The name of the property
- `type`: The data type of the property
- `optional`: Whether the property is optional
- `default`: The default value of the property
- `client`: Whether the property should be exposed to the client props. See [Hydration](./hydration) for more information.
- `context`: Whether the property should be exposed to the components context. All props of root components are automatically added to the context. See [Context](./context) for more information.

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />
<ui:prop name="size" type="string" optional="{true}" default="medium" client="{true}" />
```

## Root ID Prop

If a Component is composable, it also gets a `rootId` prop that is filled automatically with a unique identifier if not provided. That id is used to link the component with its parts.

## Class Prop

For convenience, each component automatically receives a `class` prop. This can be used to pass additional CSS classes to the component.

## Other Default Props

Other default props that are automatically added to each component are:

- `asChild`: See [Composition](./composition) for more information.
- `ids`: See [Composition](./composition) for more information.
- `controlled`: See [Hydration](./hydration) for more information.
