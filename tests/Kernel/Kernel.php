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

namespace CombatUI\Bundle\CoreBundle\Tests\Kernel;

use CombatUI\Bundle\CoreBundle\CombatUICoreBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

/**
 * Minimal kernel used by the functional test suite to boot the bundle against a real Symfony container and Twig.
 */
final class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new WebpackEncoreBundle();
        yield new CombatUICoreBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'secret' => 'combat-ui-test',
                'test' => true,
                'http_method_override' => false,
                'handle_all_throwables' => true,
                'php_errors' => [
                    'log' => true,
                ],
            ]);

            // The bundle ships a prebuilt Encore build at public/build; in an application this would live at the
            // assets:install location (public/bundles/combatuicore/build), so point both builds at the shipped one.
            $container->loadFromExtension('webpack_encore', [
                'output_path' => '%kernel.project_dir%/public/build',
            ]);

            $container->loadFromExtension('combat_ui_core', [
                'encore' => [
                    'build_path' => '%kernel.project_dir%/public/build',
                ],
            ]);
        });
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/tests/_output/kernel/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/tests/_output/kernel/log';
    }
}
