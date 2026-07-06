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

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class ComponentRenderingTest extends TwigIntegrationTestCase {
    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testCuiComponentFunctionRendersButton(): void {
        $twig = $this->createTwig([
            'index' => "{{ cui_component('button', { variant: 'primary', href: '/docs'}, 'Read the docs') }}",
        ]);
        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-button', $html);
        $this->assertHtmlContains('href="/docs"', $html);
        $this->assertHtmlContains('data-variant="primary"', $html);
        $this->assertHtmlContains('>Read the docs', $html);
        $this->assertHtmlContains('</cui-button>', $html);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testCuiComponentFunctionEscapesStringSlot(): void {
        $twig = $this->createTwig([
            'index' => "{{ cui_component('button', {}, '<script>alert(1)</script>') }}",
        ]);
        $html = $twig->render('index');
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('alert(1)', $html);
        self::assertStringNotContainsString('</script>', $html);
    }

    public function testCuiTagCapturesBodyAsDefaultSlot(): void {
        $twig = $this->createTwig([
            'index' => "{% cui 'button' with { variant: 'primary' } %}Save{% endcui %}",
        ]);
        $html = $twig->render('index');
        $this->assertHtmlContains('<cui-button', $html);
        $this->assertHtmlContains('data-variant="primary"', $html);
        $this->assertHtmlContains('>Save', $html);
        $this->assertHtmlContains('</cui-button>', $html);
    }

    public function testCuiTagWithoutProps(): void {
        $twig = $this->createTwig([
            'index' => "{% cui 'button' %}Plain{% endcui %}",
        ]);
        $html = $twig->render('index');
        $this->assertHtmlContains('<cui-button>', $html);
        $this->assertHtmlContains('Plain', $html);
        $this->assertHtmlContains('</cui-button>', $html);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testNamedSlots(): void {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'hero' with {title: 'Launch pages', eyebrow: 'Campaign'} %}
                    {% cui_slot 'media' %}<img src="/hero.jpg" alt="">{% endcui_slot %}
                    {% cui_slot 'actions' %}
                        {% cui 'button' with {variant: 'primary'} %}Contact{% endcui %}
                    {% endcui_slot %}
                {% endcui %}
            TWIG,
        ]);
        $html = $twig->render('index');
        $this->assertHtmlContains('<section', $html);
        $this->assertHtmlContains('class="cui-hero"', $html);
        $this->assertHtmlContains('Launch pages', $html);
        $this->assertHtmlContains('<cui-button', $html);
        $this->assertHtmlContains('data-variant="primary"', $html);
        $this->assertHtmlContains('Contact', $html);
        $this->assertHtmlContains('</cui-button>', $html);
        $this->assertHtmlContains('</section>', $html);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testNestedCuiTagsKeepSlotsSeparate(): void {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'section' with {tone: 'muted'} %}
                    {% cui 'button' with {variant: 'primary'} %}Inner{% endcui %}
                    Outer text
                {% endcui %}
            TWIG,
        ]);
        $html = $twig->render('index');

        $this->assertHtmlContains('class="cui-section cui-section-muted"', $html);
        $this->assertHtmlContains('<cui-button data-variant="primary">Inner</cui-button>', $html);
        $this->assertHtmlContains('Outer text', $html);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testGenericElementNamedSlots(): void {
        $twig = $this->createTwig([
           'index' => <<<'TWIG'
                {% cui 'cookie-banner' with {categories: 'analytics,marketing'} %}
                    {% cui_slot 'title' %}Cookies{% endcui_slot %}
                {% endcui %}
            TWIG
        ]);

        $html = $twig->render('index');
        $this->assertHtmlContains('<cui-cookie-banner categories="analytics,marketing">', $html);
        $this->assertHtmlContains('<div slot="title">Cookies</div>', $html);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testUnknownComponentThrows(): void {
        $twig = $this->createTwig([
            'index' => "{{ cui_component('unknown-component') }}",
        ]);
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessageMatches('/Unknown Combat UI component "unknown-component"/');
        $twig->render('index');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testSlotOutsideCuiTagThrows(): void {
        $twig = $this->createTwig([
            'index' => "{% cui_slot 'title' %}Title{% endcui_slot %}",
        ]);
        $this->expectException(RuntimeError::class);
        $this->expectExceptionMessageMatches('/may only be used inside a "cui" tag/');
        $twig->render('index');
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testComponentDefaultsAreMergedUnderProps(): void {
        $twig = $this->createTwig(
            ['index' => "{% cui 'button' %}A{% endcui %}{% cui 'button' with {variant: 'ghost'} %}B{% endcui %}"],
            ['button' => ['variant' => 'primary']],
        );

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-button data-variant="primary">A</cui-button>', $html);
        $this->assertHtmlContains('<cui-button data-variant="ghost">B</cui-button>', $html);
    }

    /**
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testCuiPrefixedNamesAreNormalized(): void {
        $twig = $this->createTwig([
            'index' => "{{ cui_component('cui-button', {}, 'X') }}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-button>X</cui-button>', $html);
    }
}
