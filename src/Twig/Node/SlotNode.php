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

namespace CombatUI\Bundle\CoreBundle\Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * Compiled for of `{% cui_slot %}`. Captures its body into the surrounding `{% cui %}` tag's slot map instead of the
 * regular output buffer.
 */
class SlotNode extends Node
{
    public function __construct(AbstractExpression $name, Node $body, int $lineno)
    {
        parent::__construct(['name' => $name, 'body' => $body], [], $lineno);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this)
            ->write("if (!\\array_key_exists('_cui_slots', \$context)) {\n")
            ->indent()
            ->write(sprintf(
                "throw new \\Twig\\Error\\RuntimeError('The \"cui_slot\" tag may only be used inside a \"cui\" tag.'"
                . ", %d, \$this->getSourceContext());\n",
                $this->getTemplateLine()))
            ->outdent()
            ->write("}\n")
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$context['_cui_slots'][(string) (")
            ->subcompile($this->getNode('name'))
            ->raw(")] = ob_get_clean();\n");
    }
}
