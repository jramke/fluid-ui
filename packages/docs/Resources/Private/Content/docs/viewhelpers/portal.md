# ui:portal

The `ui:portal` ViewHelper allows you to render content in a different part of the DOM tree than where it is defined. This is particularly useful for modals, tooltips, or any component that needs to break out of its parent container for styling or positioning reasons.

Currently the `ui:portal` ViewHelper only supports rendering content into the end of the `<body>` element. Future versions may include support for custom target selectors.

{% component: "ui:alert.simple", arguments: {"title": "The portalled content is added back to the DOM inside a custom middleware at the end of TYPO3's rendering process, right before the `typo3/cms-frontend/content-length-headers` middleware.", "variant": "warning"} %}
