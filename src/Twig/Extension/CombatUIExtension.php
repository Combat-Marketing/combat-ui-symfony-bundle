<?php
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

namespace CombatUI\Bundle\CoreBundle\Twig\Extension;

use CombatUI\Bundle\CoreBundle\Twig\AssetRenderer;
use CombatUI\Bundle\CoreBundle\Twig\ComponentRenderer;
use CombatUI\Bundle\CoreBundle\Twig\HtmlAttributes;
use CombatUI\Bundle\CoreBundle\Twig\TokenParser\ComponentTokenParser;
use CombatUI\Bundle\CoreBundle\Twig\TokenParser\SlotTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CombatUIExtension extends AbstractExtension
{
    public function getTokenParsers(): array
    {
        return [
            new ComponentTokenParser(),
            new SlotTokenParser(),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('cui_component', [ComponentRenderer::class, 'renderFunction'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
            new TwigFunction('cui_attrs', [HtmlAttributes::class, 'render'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('cui_assets', [AssetRenderer::class, 'renderAssets'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('cui_theme_script', [AssetRenderer::class, 'renderThemeScript'], [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
