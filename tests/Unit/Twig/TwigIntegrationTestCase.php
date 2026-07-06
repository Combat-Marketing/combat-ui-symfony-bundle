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

namespace CombatUI\Bundle\CoreBundle\Tests\Unit\Twig;

use CombatUI\Bundle\CoreBundle\Tests\Kernel\Kernel;
use CombatUI\Bundle\CoreBundle\Twig\ComponentRenderer;
use CombatUI\Bundle\CoreBundle\Twig\Extension\CombatUIExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Boots a bare Twig environment with the bundle's extension and shipped templates
 */
abstract class TwigIntegrationTestCase extends TestCase
{
    /**
     * Explicit kernel class so the test also runs without phpunit.xml.dist (e.g. PhpStorm's --no-configuration runs,
     * where the KERNEL_CLASS environment variable is never set).
     */
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    /**
     * @param array<string, string> $templates
     * @param array<string, array<string, mixed>> $componentDefaults
     * @throws LoaderError
     */
    protected function createTwig(array $templates = [], array $componentDefaults = []): Environment
    {
        $bundleLoader = new FilesystemLoader();
        $bundleLoader->addPath(dirname(__DIR__, 3) . '/templates', 'CombatUICore');

        $twig = new Environment(new ChainLoader([new ArrayLoader($templates), $bundleLoader]), [
            'cache' => false,
            'strict_variables' => false,
        ]);

        $twig->addExtension(new CombatUIExtension());
        $twig->addRuntimeLoader(new readonly class($componentDefaults) implements RuntimeLoaderInterface {
            public function __construct(private array $componentDefaults)
            {
            }

            public function load(string $class)
            {
                if (ComponentRenderer::class === $class) {
                    return new ComponentRenderer($this->componentDefaults);
                }

                return null;
            }
        });

        return $twig;
    }

    protected function assertHtmlContains(string $needle, string $haystack): void
    {
        self::assertStringContainsString($needle, preg_replace('/\s+/', ' ', $haystack) ?? $haystack);
    }
}
