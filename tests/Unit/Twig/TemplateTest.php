<?php
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

final class TemplateTest extends TwigIntegrationTestCase
{
    public function testSectionWithContainerAndSpacing(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'section' with {spacing: 'hero', tone: 'inverse', container: 'narrow', id: 'intro'} %}Content{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<section id="intro" class="cui-section cui-section-inverse" data-spacing="hero">', $html);
        $this->assertHtmlContains('<div class="cui-container-narrow">', $html);
    }

    public function testCta(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'cta' with {variant: 'split', tone: 'inverse', eyebrow: 'Next step', title: 'Start now', lead: 'Get going.'} %}
                    {% cui_slot 'actions' %}<cui-button variant="primary">Go</cui-button>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('class="cui-cta cui-surface cui-stack"', $html);
        $this->assertHtmlContains('data-variant="split"', $html);
        $this->assertHtmlContains('data-tone="inverse"', $html);
        $this->assertHtmlContains('<h2 class="cui-cta-title">Start now</h2>', $html);
    }

    public function testPageIntroWithMetaAndAside(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'page-intro' with {variant: 'case', eyebrow: 'Case study', title: 'Atlas', meta: ['Sector — Logistics', 'Year — 2026']} %}
                    {% cui_slot 'aside' %}<strong>Stack</strong>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('class="cui-page-intro"', $html);
        $this->assertHtmlContains('data-variant="case"', $html);
        $this->assertHtmlContains('<li>Sector — Logistics</li>', $html);
        $this->assertHtmlContains('<aside class="cui-surface cui-aside"><strong>Stack</strong></aside>', $html);
    }

    public function testTabsFromProp(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'tabs' with {tabs: ['Image', 'Video']} %}
                    <section>Panel 1</section>
                    <section>Panel 2</section>
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<div class="cui-tablist">', $html);
        $this->assertHtmlContains('<button type="button" class="cui-tab" data-selected="true">Image</button>', $html);
        $this->assertHtmlContains('<button type="button" class="cui-tab">Video</button>', $html);
        $this->assertHtmlContains('<section>Panel 1</section>', $html);
    }

    public function testTabsExplicitSelection(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'tabs' with {tabs: [{label: 'A'}, {label: 'B', selected: true}]} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<button type="button" class="cui-tab">A</button>', $html);
        $this->assertHtmlContains('<button type="button" class="cui-tab" data-selected="true">B</button>', $html);
    }

    public function testFieldWithValidationMessages(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'field' with {label: 'Email', for: 'email', required: true, help: 'We never share it.', errors: {valueMissing: 'Enter your email.', typeMismatch: 'Invalid email.'}} %}
                    <input class="cui-input" type="email" id="email" name="email">
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-field required>', $html);
        $this->assertHtmlContains('<label slot="label" for="email">Email</label>', $html);
        $this->assertHtmlContains('<span slot="help">We never share it.</span>', $html);
        $this->assertHtmlContains('<span slot="error-valueMissing">Enter your email.</span>', $html);
        $this->assertHtmlContains('<span slot="error-typeMismatch">Invalid email.</span>', $html);
    }

    public function testForm(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'form' with {action: '/subscribe', method: 'post'} %}<button>Send</button>{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-form>', $html);
        $this->assertHtmlContains('<form action="/subscribe" method="post" novalidate class="cui-stack">', $html);
    }

    public function testNavbar(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'navbar' with {brand: 'Combat UI', brand_href: '/', sticky: true, sticky_z_index: 1000} %}
                    <a class="cui-nav-link" href="/">Overview</a>
                    {% cui_slot 'actions' %}<cui-theme-toggle></cui-theme-toggle>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-navbar class="cui-navbar" sticky sticky-z-index="1000">', $html);
        $this->assertHtmlContains('<a class="cui-navbar-brand" href="/">Combat UI</a>', $html);
        $this->assertHtmlContains('<nav class="cui-navbar-nav" aria-label="Main">', $html);
        $this->assertHtmlContains('<div class="cui-navbar-actions"><cui-theme-toggle></cui-theme-toggle></div>', $html);
    }

    public function testModal(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'modal' with {id: 'confirm', title: 'Delete project?'} %}
                    <p>This cannot be undone.</p>
                    {% cui_slot 'actions' %}<button class="cui-button" data-cui-modal-close="cancel">Cancel</button>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-modal id="confirm">', $html);
        $this->assertHtmlContains('<dialog class="cui-dialog cui-surface">', $html);
        $this->assertHtmlContains('<h3 class="cui-dialog-title">Delete project?</h3>', $html);
        $this->assertHtmlContains('data-cui-modal-close aria-label="Close"', $html);
        $this->assertHtmlContains('<footer class="cui-dialog-actions">', $html);
    }

    public function testDisclosure(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'disclosure' with {summary: 'API reference', variant: 'ghost'} %}Body{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<details class="cui-disclosure" data-variant="ghost">', $html);
        $this->assertHtmlContains('<summary>API reference</summary>', $html);
        $this->assertHtmlContains('<div class="cui-disclosure-body">', $html);
    }

    public function testStackLayoutWithGap(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'stack' with {gap: 'var(--cui-space-6)'} %}<p>A</p>{% endcui %}",
        ]);

        $this->assertHtmlContains(
            '<div class="cui-stack" style="--cui-stack-gap: var(--cui-space-6);"><p>A</p></div>',
            $twig->render('index')
        );
    }

    public function testGridLayout(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'grid' with {columns: 3, min: '18rem', gap: 'compact', tag: 'ul'} %}<li>1</li>{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<ul class="cui-grid" style="--cui-grid-min: 18rem;" data-columns="3" data-gap="compact">', $html);
    }

    public function testContainerWidths(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'container' %}A{% endcui %}{% cui 'container' with {width: 'wide'} %}B{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<div class="cui-container">A</div>', $html);
        $this->assertHtmlContains('<div class="cui-container-wide">B</div>', $html);
    }

    public function testLayoutVarsEscapeHatch(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'sidebar' with {vars: {'--cui-sidebar-size': '20rem'}} %}X{% endcui %}",
        ]);

        $this->assertHtmlContains('style="--cui-sidebar-size: 20rem;"', $twig->render('index'));
    }
}
