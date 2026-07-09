<?php

declare(strict_types=1);

/*
 * Combat UI Symfony Bundle
 *
 * This source file is subject to the MIT license bundled with this package in the file LICENSE.
 *
 * @copyright Copyright (c) 2026 Combat Jongerenmarketing en -communicatie B.V. (https://www.combat.nl)
 * @license MIT https://opensource.org/licenses/MIT
 * @author Combat UI contributors
 *
 */

namespace CombatUI\Bundle\CoreBundle\Twig;

use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Resolves a Combat UI component/section name to a Twig template and renders it. Backs the `{% cui %}` tag and
 * `cui_component()` function.
 *
 * Resolution order (first match wins):
 * 1. @CombatUICore/components/<name>.html.twig     (curated component wrappers)
 * 2. @CombatUICore/sections/<name>.html.twig       (CSS-framework page sections)
 * 3. @CombatUICore/layouts/<name>.html.twig        (CSS-framework layout primitives)
 * 4. generic <cui-name> element rendering for any known custom element
 *
 * Applications can override any shipped template through Symfony's regular bundle-template override mechanism.
 */
final class ComponentRenderer implements RuntimeExtensionInterface
{
    /**
     * Custom element names registered by @combat-ui/core.
     * @var array<string>
     */
    public const array ELEMENTS = [
        'article-filter',
        'button',
        'calendar',
        'carousel',
        'code',
        'cookie-banner',
        'day-planner',
        'disclosure',
        'field',
        'form',
        'map',
        'modal',
        'navbar',
        'reveal',
        'scroll-stage',
        'sidenav',
        'tabs',
        'theme-toggle',
        'toast-region',
        'tree',
    ];

    private const array TEMPLATE_PATHS = ['components', 'sections', 'layouts'];

    /**
     * Props that are consumed by the generic element template itself and must not be forwarded as HTML attributes.
     */
    private const array RESERVED_PROPS = ['attr'];

    /**
     * @param array<string, array<string, mixed>> $componentDefaults
     */
    public function __construct(private readonly array $componentDefaults = [])
    {
    }

    /**
     * Renders a Combat UI component/section by name, merging in any default props and slots.
     *
     * @param Environment $twig
     * @param string $name
     * @param array<string, mixed> $props
     * @param array<string, string|list<string>> $slots Rendered slot HTML keyed by slot name ("default" for the
     *     tag body). A named slot may be a list when several `{% cui_slot %}` tags share the name.
     * @return string
     * @throws RuntimeError
     */
    public function render(Environment $twig, string $name, array $props = [], array $slots = []): string
    {
        $name = strtolower(trim($name));

        if (str_starts_with($name, 'cui-')) {
            $name = substr($name, 4);
        }

        if ($name === '') {
            throw new RuntimeError('Combat UI component name cannot be empty.');
        }

        $props = array_replace($this->componentDefaults[$name] ?? [], $props);

        // Curated templates read each slot as a single string, so repeated `{% cui_slot %}` tags are joined.
        // The generic element instead keeps every occurrence as its own light-DOM child (see namedSlotList()).
        $flatSlots = array_map(
            static fn (mixed $value): string => is_array($value) ? implode('', $value) : (string) $value,
            $slots,
        );
        $context = ['name' => $name, 'props' => $props, 'slots' => $flatSlots];

        $loader = $twig->getLoader();

        foreach (self::TEMPLATE_PATHS as $path) {
            $tpl = sprintf('@CombatUICore/%s/%s.html.twig', $path, $name);
            if ($loader->exists($tpl)) {
                return $twig->render($tpl, $context);
            }
        }

        if (in_array($name, self::ELEMENTS, true)) {
            return $twig->render('@CombatUICore/components/_element.html.twig', $context + [
                    'tag' => 'cui-' . $name,
                    'attrs' => $this->propsToAttributes($props),
                    'named_slots' => $this->namedSlotList($slots),
                ]);
        }

        throw new RuntimeError(sprintf('Unknown Combat UI component "%s".' .
            ' Known custom elements: %s.' .
            ' Sections and layouts resolve to templates in @CombatUICore/sections and @CombatUICore/layouts.',
            $name, implode(', ', self::ELEMENTS)));
    }

    /**
     * Twig-facing wrapper allowing a plain string as the default slot
     *
     * @param Environment $twig
     * @param string $name
     * @param array $props
     * @param string|array|null $slots
     * @return string
     * @throws RuntimeError
     *
     * @example `{{ cui_component('button', {variant: 'primary'}, 'Click me') }}`.
     */
    public function renderFunction(Environment $twig, string $name, array $props = [], string|array|null $slots = null): string
    {
        if (is_string($slots)) {
            $slots = ['default' => htmlspecialchars($slots, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')];
        }

        return $this->render($twig, $name, $props, $slots ?? []);
    }

    /**
     * Flattens the slot map into an ordered list of `{name, content}` pairs, preserving repeated slots so the
     * generic element emits one light-DOM child per `{% cui_slot %}` occurrence. The "default" slot (the tag
     * body) is excluded — it is rendered separately.
     *
     * @param array<string, string|list<string>> $slots
     * @return list<array{name: string, content: string}>
     */
    private function namedSlotList(array $slots): array
    {
        $list = [];

        foreach ($slots as $slotName => $value) {
            if ($slotName === 'default') {
                continue;
            }

            foreach (is_array($value) ? $value : [$value] as $content) {
                $list[] = ['name' => $slotName, 'content' => (string) $content];
            }
        }

        return $list;
    }

    /**
     * Maps generic element props to HTML attributes. Underscores in prop names are converted to hyphens, and entries
     * of the special `attr` prop are merged in. Reserved props are not forwarded.
     *
     * @param array<string, mixed> $props
     * @return array<string, mixed>
     */
    private function propsToAttributes(array $props): array
    {
        $attrs = [];

        foreach ($props as $key => $value) {
            if (in_array($key, self::RESERVED_PROPS, true)) {
                continue;
            }

            $attrs[str_replace('_', '-', $key)] = $value;
        }

        $extra = $props['attr'] ?? [];

        return is_array($extra) ? array_merge($attrs, $extra) : $attrs;
    }
}

