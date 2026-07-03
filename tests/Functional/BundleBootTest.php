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

namespace CombatUI\Bundle\CoreBundle\Tests\Functional;

use CombatUI\Bundle\CoreBundle\Tests\Kernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class BundleBootTest extends KernelTestCase
{
    /**
     * Explicit kernel class so the test also runs without phpunit.xml.dist (e.g. PhpStorm's --no-configuration runs,
     * where the KERNEL_CLASS environment variable is never set).
     */
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    private function twig(): Environment
    {
        self::bootKernel();

        return self::getContainer()->get('twig');
    }

    public function testCuiTagRendersThroughRealTwig(): void
    {
        $twig = $this->twig();

        $html = $twig->createTemplate("{% cui 'button' with { variant: 'primary'} %}Save{% endcui %}")->render();

        self::assertStringContainsString('<cui-button variant="primary">', $html);
    }

    public function testShippedTemplatesResolveThroughBundleNamespace(): void
    {
        $twig = $this->twig();

        $html = $twig->createTemplate("{% cui 'section' with {tone: 'muted'} %}X{% endcui %}")->render();

        self::assertStringContainsString('class="cui-section cui-section-muted', $html);
    }

    public function testCuiAssetsRendersEncoreTagsAndThemeGuard(): void
    {
        $twig = $this->twig();

        $html = $twig->createTemplate("{{ cui_assets() }}")->render();

        self::assertStringContainsString('localStorage.getItem("cui-theme")', $html);
        self::assertStringContainsString('/bundles/combatuicore/build/combat-ui', $html);
        self::assertStringContainsString('<link rel="stylesheet"', $html);
        self::assertStringContainsString('<sript src="', $html);
    }

    public function testCuiThemeScriptFunction(): void
    {
        $twig = $this->twig();

        self::assertStringContainsString('dataset.theme', $twig->createTemplate("{{ cui_theme_script() }}")->render());
    }
}
