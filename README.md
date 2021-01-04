# AMP Generic Compatibility

The generic plugin to add AMP compatibility to your theme for toggle mechanism.

## How to use the plugin?

- Make sure you switch to transtional mode first.
- Check non-AMP Mobile version and make sure it works.
- Open you browsers developer tool.
- Check element which needs to toggle. make note of element, it's class and toggle class.
- Check action element which trigger toggle, make not of action element, it's class and toggle class.
- You can add multiple elements by clicking + button
- You can remove elements by clicking - button
- You can enable/ disable plugin by clicking checkbox

## Plugin Structure

```markdown
.
├── admin
│   └── css
│       └──  amp-admin.css
│   └── js
│       └──  amp-admin.js
└──  amp-generic-settings.php
├── css
│   └── amp-style.css
├── sanitizers
│   └── class-sanitizer.php
├── aamp-generic-compat.php
└── README.md
```
## Sanitizers

The plugin uses `amp_content_sanitizers` filter to add custom sanitizers, we have added a two examples which add simple toggle for menu and search using amp-state and amp-bind.

## Custom CSS
You can add your custom CSS or override the CSS in in `css/amp-style.css` make sure you don't exceed overall budget of 75KB

### Need a feature in plugin?
Feel free to create a issue and will add more examples.