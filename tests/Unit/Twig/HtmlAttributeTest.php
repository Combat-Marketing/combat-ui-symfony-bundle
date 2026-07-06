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

use CombatUI\Bundle\CoreBundle\Twig\HtmlAttributes;
use PHPUnit\Framework\TestCase;

final class HtmlAttributeTest extends TestCase
{
    public function testScalarAttributesAreEscaped(): void
    {
        self::assertSame(
            ' href="/docs?a=1&amp;b=2" title="He said &quot;hi&quot;"',
            HtmlAttributes::render([
                'href' => '/docs?a=1&b=2',
                'title' => 'He said "hi"',
            ])
        );
    }

    public function testNullAndFalseAreSkipped(): void
    {
        self::assertSame(
            '',
            HtmlAttributes::render(['href' => null, 'disabled' => false])
        );
    }

    public function testTrueRendersPresenceAttribute()
    {
        self::assertSame(
            ' disabled',
            HtmlAttributes::render(['disabled' => true])
        );
    }

    public function testClassListsAndConditionalClasses(): void
    {
        self::assertSame(
            ' class="cui-section cui-section-muted"',
            HtmlAttributes::render([
                'class' => ['cui-section', 'cui-section-muted' => true, 'cui-section-hero' => false],
            ]),
        );
    }

    public function testEmptyClassListIsSkipped(): void
    {
        self::assertSame('', HtmlAttributes::render(['class' => [null, 'skipped' => false]]));
    }

    public function testStyleMapRendersDeclarations(): void
    {
        self::assertSame(
            ' style="--cui-stack-gap: 2rem; --cui-grid-min: 18rem;"',
            HtmlAttributes::render(['style' => ['--cui-stack-gap' => '2rem', '--cui-grid-min' => '18rem', '--cui-skip' => null]])
        );
    }

    public function testDataMapExpandsToDataAttributes(): void
    {
        self::assertSame(
            ' data-columns="3" data-gap="compact"',
            HtmlAttributes::render(['data' => ['columns' => 3, 'gap' => 'compact', 'skipped' => null]])
        );
    }

    public function testZeroIsRendered(): void
    {
        self::assertSame(' tabindex="0"', HtmlAttributes::render(['tabindex' => 0]));
    }

    public function testInvalidAttributeNameThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        HtmlAttributes::render(['on click' => 'x']);
    }

    public function testArrayValueOnRegularAttributeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        HtmlAttributes::render(['href' => ['a', 'b']]);
    }
}
