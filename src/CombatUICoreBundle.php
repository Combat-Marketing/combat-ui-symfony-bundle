<?php

declare(strict_types=1);

/**
 * Combat UI Symfony Bundle
 *
 * This source file is subject to the MIT license bundled with this package in the file LICENSE.
 *
 * @copyright Copyright (c) 2026 Combat Jongerenmarketing en -communicatie B.V. (https://www.combat.nl)
 * @license MIT https://opensource.org/licenses/MIT
 * @author Combat UI contributors
 */

namespace CombatUI\Bundle\CoreBundle;

use CombatUI\Bundle\CoreBundle\DependencyInjection\CombatUICoreExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle for Combat UI to Twig. CMS-specific functionality can be provided by wrapper bundles that depend
 * on this bundle.
 *
 * @package CombatUI\Bundle\CoreBundle
 */
class CombatUICoreBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new CombatUICoreExtension();
        }
        return $this->extension ?: null;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
