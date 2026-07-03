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

final class HtmlAttributes
{
    private const NAME_PATTERN = '/^[a-zA-Z][a-zA-Z0-9:._-]*$/';

    /**
     * @param array<int|string, mixed> $attributes keys should be strings, anything else is rejected at runtime
     * @return string
     * @throws \InvalidArgumentException if any attribute name is invalid
     */
    public static function render(array $attributes): string
    {
        if (isset($attributes['data']) && is_array($attributes['data'])) {
            foreach ($attributes['data'] as $dataName => $dataValue) {
                $attributes['data-' . $dataName] = $dataValue;
            }

            unset($attributes['data']);
        }

        $html = '';
        foreach ($attributes as $name => $val) {
            if ($val === null || $val === false) {
                continue;
            }

            if (!is_string($name) || preg_match(self::NAME_PATTERN, $name) !== 1) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid HTML attribute name "%s".',
                    \is_string($name) ? $name : gettype($name)
                ));
            }

            if ($val === true) {
                $html .= ' ' . $name;
                continue;
            }

            if (is_array($val)) {
                $val = match ($name) {
                    'class' => self::classList($val),
                    'style' => self::styleList($val),
                    default => throw new \InvalidArgumentException(
                        sprintf(
                            'Array values are only supported for the "class" and "style" attributes, got one for "%s".',
                            $name
                        )
                    ),
                };

                if ($val === null) {
                    continue;
                }
            }

            if (!is_scalar($val) && !$val instanceof \Stringable) {
                throw  new \InvalidArgumentException(sprintf(
                    'Attribute "%s" must be a scalar or stringable value, got %s.',
                    $name, get_debug_type($val)
                ));
            }

            $html .= sprintf(' %s="%s"', $name, htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        return $html;
    }

    private static function classList(array $classes): ?string
    {
        $list = [];

        foreach ($classes as $k => $v) {
            if (is_int($k)) {
                if (is_string($v) && $v !== '') {
                    $list[] = $v;
                }
                continue;
            }

            if ($v) {
                $list[] = $k;
            }
        }

        return $list ? null : implode(' ', $list);
     }

     private static function styleList(array $styles): ?string
     {
         $list = [];

         foreach ($styles as $k => $v) {
             if ($v === null || $v === false || $v === '') {
                 continue;
             }

             $list[] = sprintf('%s: %s;', $k, (string)$v);
         }

         return $list ? null : implode(' ', $list);
     }
}
