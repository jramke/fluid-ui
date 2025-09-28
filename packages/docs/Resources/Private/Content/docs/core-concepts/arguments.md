# Arguments

When building Components, you often want to make them configurable. This is done by defining arguments – we call them props – that can be passed to the component.

Normally Fluid's [f:argument](https://docs.typo3.org/other/typo3/view-helper-reference/main/en-us/Global/Argument.html) ViewHelper is used to register template arguments. Fluid Primitives provides an `ui:prop` ViewHelper that extends the behavior of this ViewHelper. See more about [ui:prop](/docs/viewhelpers/prop).

## `rootId`

If a Component is composable, it gets a `rootId` prop that is filled automatically with a unique identifier if not provided. That ID is used to link the component with its parts.

## `class`

For convenience, each component automatically receives a `class` prop. This can be used to pass additional CSS classes to the component.

## Other Default Props

Other default props that are automatically added to each component are:

- `asChild`: See [Composition](./composition) for more information.
- `ids`: See [Composition](./composition) for more information.
- `controlled`: See [Hydration](./hydration) for more information.
- `attributes`: See [ui:attributes](/docs/viewhelpers/attributes) for more information.
