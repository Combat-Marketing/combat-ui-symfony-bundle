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

namespace CombatUI\Bundle\CoreBundle\Twig\Node;

use CombatUI\Bundle\CoreBundle\Twig\ComponentRenderer;
use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\CaptureNode;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

/**
 * Compiled form of `{% cui %}`. Captures the tag body as the "default" slot (nested `{% cui_slot %}` tags register
 * named slots in `_cui_slots`) and delegates rendering to the `ComponentRenderer` runtime.
 */
#[YieldReady]
final class ComponentNode extends Node
{
    public function __construct(AbstractExpression $name, ?AbstractExpression $props, Node $body, int $lineno)
    {
        $nodes = ['name' => $name, 'body' => $body];

        if ($props !== null) {
            $nodes['props'] = $props;
        }

        parent::__construct($nodes, [], $lineno);
    }

    /**
     * Compiles the node to PHP code. The compiled code captures the body of the tag as the "default" slot,
     * collects any named slots from nested `{% cui_slot %}` tags, and calls the `ComponentRenderer` runtime
     * to render the component.
     *
     * Twig compiles template bodies to `yield` statements, so the body is captured through Twig's own
     * `CaptureNode` (a self-invoking closure whose yields are joined into a string) instead of output buffering.
     *
     * compiles the following code:
     * ```
     * $parentSlots = $context['_cui_slots'] ?? null;
     * $context['_cui_slots'] = [];
     * // Render the body; nested `{% cui_slot %}` tags register named slots in `$context['_cui_slots']`.
     * $default = implode('', iterator_to_array((function () use (&$context, $macros, $blocks) { ...body... })(), false));
     * $slots = $context['_cui_slots'];
     * $slots['default'] = $default;
     * if ($parentSlots !== null) {
     *     $context['_cui_slots'] = $parentSlots;
     * } else {
     *     unset($context['_cui_slots']);
     * }
     * yield $this->env->getRuntime(ComponentRenderer::class)->render($this->env, <name>, (array) (<props>), $slots);
     * ```
     *
     * @inheritDoc
     */
    public function compile(Compiler $compiler): void
    {
        $parentSlots = $compiler->getVarName();
        $slots = $compiler->getVarName();
        $default = $compiler->getVarName();

        $capture = new CaptureNode($this->getNode('body'), $this->getTemplateLine());
        $capture->setAttribute('raw', true);

        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("\$%s = \$context['_cui_slots'] ?? null;\n", $parentSlots))
            ->write("\$context['_cui_slots'] = [];\n")
            ->write(sprintf("\$%s = ", $default))
            ->subcompile($capture)
            ->raw("\n")
            ->write(sprintf("\$%s = \$context['_cui_slots'];\n", $slots))
            ->write(sprintf("\$%s['default'] = \$%s;\n", $slots, $default))
            ->write(sprintf("if (\$%s !== null) { \$context['_cui_slots'] = \$%s; } else { unset(\$context['_cui_slots']); }\n", $parentSlots, $parentSlots))
            ->write(sprintf("yield \$this->env->getRuntime(\%s::class)->render(\$this->env, ", ComponentRenderer::class))
            ->subcompile($this->getNode('name'))
            ->raw(", (array) (");

        if ($this->hasNode('props')) {
            $compiler->subcompile($this->getNode('props'));
        } else {
            $compiler->raw("[]");
        }

        $compiler->raw(sprintf("), \$%s);\n", $slots));
    }
}
