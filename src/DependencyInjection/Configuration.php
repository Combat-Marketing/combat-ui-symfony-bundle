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

namespace CombatUI\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('combat_ui_core');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('encore')
                    ->addDefaultsIfNotSet()
                    ->info('Webpack Encore integration used by the cui_assets() Twig function')
                    ->children()
                        ->scalarNode('build_name')
                            ->defaultValue('combat_ui')
                            ->info('The name of the Webpack Encore build to use for cui_assets()')
                        ->end()
                        ->scalarNode('entry')
                            ->defaultValue('combat-ui')
                            ->info('The entry point to use for cui_assets()')
                        ->end()
                            ->scalarNode('build_path')
                            ->defaultValue('%kernel.project_dir%/public/bundles/combatuicore/build')
                            ->info('Directory containing the entrypoints.json of the Combat UI build. Defaults to the assets:install location of this bundle\'s prebuilt assets.')
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('theme_guard')
                    ->defaultTrue()
                    ->info('If true, the cui_theme() Twig function will throw an exception if the theme is not set in the request attributes.')
                ->end()
                ->arrayNode('component_defaults')
                    ->info('Default values for component props. These will be merged with the props passed to the component in the Twig template.')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('component')
                    ->variablePrototype()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
