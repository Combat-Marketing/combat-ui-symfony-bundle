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

namespace CombatUI\Bundle\CoreBundle\Twig\TokenParser;

use CombatUI\Bundle\CoreBundle\Twig\Node\SlotNode;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Parses the `{% cui_slot %}` tag, used inside `{% cui %}` to fill a named slot:
 *
 * **Example usage:**
 * ```
 * {% cui 'hero' with {title: 'Launch'} %}
 *     {% cui_slot 'media' %}<img src="..." alt="">{% endcui_slot %}
 *     <p>Lead copy.</p>
 * {% endcui %}
 * ```
 */
class SlotTokenParser extends AbstractTokenParser
{

    /**
     * @inheritDoc
     */
    public function parse(Token $token): Node
    {
        $stream = $this->parser->getStream();
        $name = $this->parser->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse($this->decideEnd(...), true);
        $stream->expect(Token::BLOCK_END_TYPE);

        return new SlotNode($name, $body, $token->getLine());
    }

    public function decideEnd(Token $token): bool
    {
        return $token->test('endcui_slot');
    }

    /**
     * @inheritDoc
     */
    public function getTag(): string
    {
        return 'cui_slot';
    }
}
