# Combat UI Core Bundle

[![Tests](https://github.com/Combat-Marketing/combat-ui-symfony-bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/Combat-Marketing/combat-ui-symfony-bundle/actions/workflows/tests.yml)
![PHP](https://img.shields.io/badge/php-8.4%20%7C%208.5-777bb3)
![Symfony](https://img.shields.io/badge/symfony-7.4%20%7C%208.x-000000)
![License](https://img.shields.io/badge/license-MIT-green)

The official Symfony integration for [Combat UI](https://github.com/Combat-Marketing/combat-ui) — a lightweight, themeable front-end framework built on custom elements (`<cui-button>`, `<cui-modal>`, `<cui-tabs>`, …) and a utility-free CSS layer.

This bundle turns the framework into first-class Twig building blocks:

```twig
{% cui 'hero' with { eyebrow: 'New', title: 'Ship faster', lead: 'Combat UI in Twig.' } %}
    {% cui_slot 'actions' %}
        {% cui 'button' with { variant: 'primary', href: '/docs' } %}Read the docs{% endcui %}
    {% endcui_slot %}
    {% cui_slot 'media' %}<img src="/img/screenshot.png" alt="">{% endcui_slot %}
{% endcui %}
```

It is deliberately CMS-agnostic: it only needs Twig and Webpack Encore. CMS-specific integrations (such as the OpenDXP bundle) are thin wrappers on top of this one.

## What you get

- **A `{% cui %}` Twig tag** — render any Combat UI component, section, or layout by name, pass props with `with {…}`, and fill slots with the tag body and nested `{% cui_slot %}` tags.
- **A `cui_component()` function** — the same renderer in expression form, handy for one-liners and dynamic component names.
- **A curated template library** — 25+ ready-made Twig templates for components (cards, forms, modal, navbar, pagination, tabs, …), page sections (hero, CTA, page intro), and every-layout-style layout primitives (stack, cluster, grid, sidebar, switcher, cover, …). All of them overridable per-app via Symfony's standard bundle template override.
- **Generic element rendering** — any Combat UI custom element without a curated template still renders as a proper `<cui-*>` tag with props mapped to attributes.
- **`cui_attrs()`** — a safe, expressive HTML attribute renderer (boolean attributes, conditional class lists, style maps, `data-*` expansion, full escaping).
- **`cui_assets()`** — one function that outputs the framework's CSS/JS tags from a prebuilt Webpack Encore build that ships with the bundle. Zero front-end tooling required to get started.
- **`cui_theme_script()`** — a tiny inline script that restores the visitor's light/dark preference before first paint, preventing theme flash.
- **Configurable component defaults** — set project-wide default props per component in YAML.

## Requirements

| Dependency | Version |
| --- | --- |
| PHP | 8.4 or 8.5 |
| Symfony | 7.4 or 8.x |
| Twig | ^3.21 |
| symfony/webpack-encore-bundle | ^2.1 |

## Installation

```bash
composer require combat-ui/core-bundle
```

Register the bundle if you don't use Flex auto-registration:

```php
// config/bundles.php
return [
    // ...
    CombatUI\Bundle\CoreBundle\CombatUICoreBundle::class => ['all' => true],
];
```

Publish the prebuilt assets:

```bash
php bin/console assets:install
```

Then load the framework in your base layout:

```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html>
    <head>
        {{ cui_assets() }}
    </head>
    ...
</html>
```

That's it — the bundle automatically registers a `combat_ui` Encore build pointing at its prebuilt assets (installed to `public/bundles/combatuicore/build`), so `cui_assets()` works out of the box without touching your own Encore setup.

## Usage

### The `{% cui %}` tag

Render a component by name. Props go in a `with` map; the tag body becomes the component's **default slot**:

```twig
{% cui 'button' with { variant: 'primary', href: '/signup' } %}
    Sign up
{% endcui %}
```

Named slots are filled with the nested `{% cui_slot %}` tag:

```twig
{% cui 'hero' with { title: 'Launch' } %}
    {% cui_slot 'media' %}<img src="/img/rocket.jpg" alt="">{% endcui_slot %}
    <p>Everything outside a cui_slot lands in the default slot.</p>
{% endcui %}
```

### The `cui_component()` function

The same renderer as an expression. A plain string as the third argument is escaped and used as the default slot:

```twig
{{ cui_component('button', { variant: 'primary' }, 'Click me') }}
{{ cui_component(card_type ~ '-card', item.props) }}
```

### How names resolve

`{% cui 'name' %}` resolves the name in this order — first match wins:

1. `@CombatUICore/components/<name>.html.twig` — curated component wrappers
2. `@CombatUICore/sections/<name>.html.twig` — page sections
3. `@CombatUICore/layouts/<name>.html.twig` — layout primitives
4. A **generic `<cui-name>` element**, if the name is a known Combat UI custom element — props become HTML attributes (`snake_case` → `kebab-case`), extras can be added via the `attr` prop.

An unknown name throws a clear `RuntimeError` listing the known elements. The `cui-` prefix is optional: `'cui-button'` and `'button'` are equivalent.

Because resolution goes through the `@CombatUICore` Twig namespace, **any shipped template can be overridden** the standard Symfony way — drop your version in:

```text
templates/bundles/CombatUICoreBundle/components/button.html.twig
```

### What's in the box

**Components** (`templates/components/`)

`article-card`, `article-filter`, `button`, `case-card`, `contact-card`, `contact-methods`, `content-card`, `disclosure`, `event-card`, `feature-card`, `field`, `figure`, `form`, `itinerary-item`, `location-card`, `logo-item`, `media-card`, `media-full`, `media-overlay`, `modal`, `navbar`, `org-chart`, `pagination`, `person`, `stat`, `tabs`, `vacancy-card`

**Sections** (`templates/sections/`)

`hero`, `cta`, `page-intro`, `section`

**Layouts** (`templates/layouts/`) — composable primitives in the spirit of *Every Layout*:

`stack`, `cluster`, `grid`, `sidebar`, `split`, `switcher`, `center`, `cover`, `frame`, `container`, `prose`

```twig
{% cui 'stack' with { gap: 'var(--cui-space-l)' } %}
    {% cui 'grid' with { min: '18rem' } %}
        {% for item in items %}
            {% cui 'content-card' with item.props %}{% endcui %}
        {% endfor %}
    {% endcui %}
{% endcui %}
```

**Generic custom elements** — rendered without a curated wrapper:

`calendar`, `carousel`, `code`, `cookie-banner`, `day-planner`, `map`, `reveal`, `scroll-stage`, `sidenav`, `theme-toggle`, `toast-region`, `tree`

Each shipped template documents its props and slots in a comment at the top of the file — that's the per-component reference.

### `cui_attrs()` — HTML attribute rendering

Used throughout the shipped templates and available for your own:

```twig
<div{{ cui_attrs({
    id: props.id|default(null),                          {# null/false attributes are dropped #}
    disabled: props.disabled|default(false),             {# true renders as a boolean attribute #}
    class: ['cui-card', { 'is-active': props.active }],  {# arrays + conditional class maps #}
    style: { '--gap': props.gap|default(null) },         {# style maps, empty values dropped #}
    data: { variant: props.variant },                    {# expands to data-variant="…" #}
}) }}>
```

Values are HTML-escaped, and invalid attribute names throw instead of rendering — so props can never smuggle markup into your attributes.

### Assets and theming

`cui_assets()` renders the `<link>` and `<script defer>` tags for the Combat UI build, prefixed (by default) with the theme-restore script:

```twig
{{ cui_assets() }}                        {# default build & entry #}
{{ cui_assets('my-entry', 'my_build') }}  {# explicit entry / Encore build #}
```

`cui_theme_script()` is also available standalone. It reads `cui-theme` from `localStorage` and sets `data-theme` on `<html>` synchronously, so a returning dark-mode visitor never sees a light flash.

### Component defaults

Set project-wide default props per component; explicit props in the template always win:

```yaml
# config/packages/combat_ui_core.yaml
combat_ui_core:
    component_defaults:
        button:
            variant: primary
        hero:
            align: center
```

## Configuration reference

```yaml
combat_ui_core:
    # Webpack Encore integration used by cui_assets()
    encore:
        # Name of the Encore build cui_assets() reads from.
        # 'combat_ui' (the default) is auto-registered by the bundle;
        # set a different name to take over asset building yourself.
        build_name: combat_ui

        # Entry point rendered by cui_assets()
        entry: combat-ui

        # Directory containing the build's entrypoints.json.
        # Defaults to where assets:install puts the bundle's prebuilt assets.
        build_path: '%kernel.project_dir%/public/bundles/combatuicore/build'

    # Prefix cui_assets() output with the theme-restore script
    theme_guard: true

    # Default props merged into every render of the given component
    component_defaults: {}
```

### Bringing your own build

The prebuilt assets are great for getting started, but for tree-shaking or bundling Combat UI with your own front-end code you can point the bundle at your own Encore build:

1. Add `@combat-ui/core` to your app's `package.json` and create an entry that imports it.
2. Register that build in `webpack_encore.builds` (or reuse your default build).
3. Point the bundle at it:

```yaml
combat_ui_core:
    encore:
        build_name: app   # the bundle stops auto-registering its own build
        entry: app
```

## Testing & quality

```bash
vendor/bin/codecept build   # generate actor classes
vendor/bin/codecept run     # run the Codeception suites
vendor/bin/phpstan          # static analysis
```

CI runs the suites against PHP 8.4/8.5 × Symfony 7.4/8.x.

## Related packages

- [`@combat-ui/core`](https://www.npmjs.com/package/@combat-ui/core) — the underlying front-end framework (custom elements + CSS)
- **Combat UI OpenDXP Bundle** — CMS integration for OpenDXP, built on top of this bundle

## License

Released under the [MIT License](LICENSE).

Copyright © 2026 [Combat Jongerenmarketing en -communicatie B.V.](https://www.combat.nl)
