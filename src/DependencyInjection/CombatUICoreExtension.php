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
 */

namespace CombatUI\Bundle\CoreBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CombatUICoreExtension extends Extension implements PrependExtensionInterface
{
    private const DEFAULT_BUILD_PATH = '%kernel.project_dir%/public/bundles/combatuicore/build';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $mergedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $container->setParameter('combat_ui_core.encore_build', $mergedConfig['encore']['build_name']);
        $container->setParameter('combat_ui_core.encore_entry', $mergedConfig['encore']['entry']);
        $container->setParameter('combat_ui_core.theme_guard', $mergedConfig['theme_guard']);
        $container->setParameter('combat_ui_core.component_defaults', $mergedConfig['component_defaults']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('webpack_encore')) {
            return;
        }

        $buildName = 'combat_ui';
        $buildPath = self::DEFAULT_BUILD_PATH;
        foreach ($container->getExtensionConfig($this->getAlias()) as $config) {
            if (isset($config['encore']['build_name'])) {
                $buildName = $config['encore']['build_name'];
            }
            if (isset($config['encore']['build_path'])) {
                $buildPath = $config['encore']['build_path'];
            }
        }

        if ($buildName !== 'combat_ui') {
            return;
        }

        // The "builds" node is a scalar prototype: build name => output path.
        $container->prependExtensionConfig('webpack_encore', [
            'builds' => [
                $buildName => $buildPath,
            ],
        ]);

    }
}
