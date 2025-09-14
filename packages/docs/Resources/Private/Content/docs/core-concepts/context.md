# Context

When building composable component parts we rely on a shared context. Each component has access to its context. This context exposes, among other things, the components `rootId`, `baseName` and all props defined in the root component.

As mentioned you can also expose other properties as needed via the `context` parameter in the `ui:prop` ViewHelper.

You can also use the `ui:context` ViewHelper to access others component contexts as well.

<!-- TODO -->
