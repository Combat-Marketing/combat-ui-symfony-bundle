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

namespace CombatUI\Bundle\CoreBundle\Twig;

use Symfony\WebpackEncoreBundle\Asset\TagRenderer;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Backs the `cui_assets()` and `cui_theme_script()` Twig functions.
 */
final readonly class AssetRenderer implements RuntimeExtensionInterface
{
    public function __construct(
        private TagRenderer $tagRenderer,
        private ?string     $buildName = 'combat_ui',
        private string      $entry = 'combat-ui',
        private bool        $themeGuard = true,
    )
    {
    }

    public function renderAssets(?string $entry = null, ?string $buildName = null): string
    {
        $entry ??= $this->entry;
        $buildName ??= $this->buildName;

        $tags = [];

        if ($this->themeGuard) {
            $tags[] = $this->renderThemeScript();
        }

        $tags[] = $this->tagRenderer->renderWebpackLinkTags($entry, null, $buildName);
        $tags[] = $this->tagRenderer->renderWebpackScriptTags($entry, null, $buildName, ['defer' => true]);

        return implode("\n", array_filter($tags));
    }

    public function renderThemeScript(): string
    {
        return '<script>try{const t=localStorage.getItem("cui-theme");if(t==="light"||t==="dark"){document.documentElement.dataset.theme=t;}}catch(e){}</script>';
    }
}
