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

namespace CombatUI\Bundle\CoreBundle\Twig\TokenParser;

use CombatUI\Bundle\CoreBundle\Twig\Node\ComponentNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Parses the `{% cui %}` tag.
 *
 * **Example usage:**
 * ```
 * {% cui 'component_name' with { prop1: 'value1', prop2: 'value2' } %}
 *    Read the docs
 * {% endcui %}
 * ```
 *
 *  The tag body becomes the component's default slot; named slots are captured
 *  with the nested `{% cui_slot %}` tag.
 */
final class ComponentTokenParser extends AbstractTokenParser {
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();

        $name = $this->parser->parseExpression();

        $props = null;
        if (!$stream->nextIf(Token::NAME_TYPE, 'with')) {
            $props = $this->parser->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse($this->decideEnd(...), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new ComponentNode($name, $props, $body, $token->getLine());
    }

    public function decideEnd(Token $token): bool
    {
        return $token->test('endcui');
    }

    public function getTag(): string
    {
        return 'cui';
    }
}
