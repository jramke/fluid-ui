# Arguments

Normally Fluid's [f:argument](https://docs.typo3.org/other/typo3/view-helper-reference/main/en-us/Global/Argument.html) ViewHelper is used to register template arguments. Fluid UI provides the `ui:prop` ViewHelper that extends its functionality.

-   `name`: The name of the property
-   `type`: The data type of the property
-   `optional`: Whether the property is optional
-   `default`: The default value of the property
-   `client`: Whether the property should be exposed to the client-side
-   `context`: Whether the property should be exposed to the components context

```html
<ui:prop name="variant" type="string" optional="{true}" default="primary" />
<ui:prop name="size" type="string" optional="{true}" default="medium" client="{true}" />
```

Each Component automatically receives as `class` prop.
Composable components also get a `rootId` prop that is filled automatically.
