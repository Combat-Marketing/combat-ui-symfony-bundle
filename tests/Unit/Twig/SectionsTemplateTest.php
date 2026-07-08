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

/**
 * Covers the curated templates for the sections-page patterns: content grids,
 * blog, team/people, events, contact, recruitment, pagination, media blocks.
 */
final class SectionsTemplateTest extends TwigIntegrationTestCase
{
    public function testArticleCard(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'article-card' with {variant: 'featured', title: 'Post title', href: '/post', category: 'News', category_href: '/news', excerpt: 'Summary.', image: '/img.jpg', meta: ['Jane Smith', 'May 2026'], category_key: 'news'} %}
                    {% cui_slot 'actions' %}<a class="cui-button" href="/post">Read</a>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-article-card" data-variant="featured" data-category="news">', $html);
        $this->assertHtmlContains('<a class="cui-article-card-media" href="/post" aria-label="Post title">', $html);
        $this->assertHtmlContains('<img src="/img.jpg" alt="">', $html);
        $this->assertHtmlContains('<p class="cui-eyebrow"><a href="/news">News</a></p>', $html);
        $this->assertHtmlContains('<h3 class="cui-display"><a href="/post">Post title</a></h3>', $html);
        $this->assertHtmlContains('<li>Jane Smith</li>', $html);
        $this->assertHtmlContains('<div class="cui-cluster"><a class="cui-button" href="/post">Read</a></div>', $html);
    }

    public function testArticleCardCompactOmitsMedia(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'article-card' with {variant: 'compact', title: 'Post', image: '/img.jpg'} %}{% endcui %}",
        ]);

        $this->assertStringNotContainsString('cui-article-card-media', $twig->render('index'));
    }

    public function testArticleFilterSingleSelect(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'article-filter' with {target: '#grid', categories: ['design', {value: 'dev', label: 'Development'}]} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<cui-article-filter target="#grid">', $html);
        $this->assertHtmlContains('<p class="cui-article-filter-label">Filter</p>', $html);
        $this->assertHtmlContains('<input type="radio" name="category" value="" checked>', $html);
        $this->assertHtmlContains('<span>All</span>', $html);
        $this->assertHtmlContains('<input type="radio" name="category" value="design">', $html);
        $this->assertHtmlContains('<input type="radio" name="category" value="dev">', $html);
        $this->assertHtmlContains('<span>Development</span>', $html);
    }

    public function testArticleFilterMultiSelect(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'article-filter' with {target: '#grid', multi: true, categories: ['design']} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<input type="checkbox" name="category" value="design">', $html);
        $this->assertStringNotContainsString('value="" checked', $html, 'multi-select has no all chip');
    }

    public function testFeatureCard(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'feature-card' with {icon: '01', title: 'Reusable structure', align: 'center'} %}<p>Copy.</p>{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-feature-card" data-align="center">', $html);
        $this->assertHtmlContains('<span class="cui-feature-icon" aria-hidden="true">01</span>', $html);
        $this->assertHtmlContains('<h3>Reusable structure</h3>', $html);
        $this->assertHtmlContains('<p>Copy.</p>', $html);
    }

    public function testCaseCard(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'case-card' with {meta: 'Case study', title: 'Campaign launch', href: '/case', image: '/case.jpg', variant: 'flat'} %}<p>Copy.</p>{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-case-card" data-variant="flat">', $html);
        $this->assertHtmlContains('<img class="cui-case-card-media" src="/case.jpg" alt="">', $html);
        $this->assertHtmlContains('<div class="cui-case-card-body cui-stack">', $html);
        $this->assertHtmlContains('<p class="cui-case-card-meta">Case study</p>', $html);
        $this->assertHtmlContains('<h3><a href="/case">Campaign launch</a></h3>', $html);
    }

    public function testContentCardStatAndLogoItem(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'content-card' with {title: 'Editorial card', variant: 'borderless'} %}<p>Copy.</p>{% endcui %}
                {% cui 'stat' with {value: '42%', label: 'Higher page reuse', align: 'center'} %}{% endcui %}
                {% cui 'logo-item' with {label: 'Northstar'} %}{% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-content-card" data-variant="borderless">', $html);
        $this->assertHtmlContains('<div class="cui-surface cui-stat" data-align="center">', $html);
        $this->assertHtmlContains('<span class="cui-stat-value">42%</span>', $html);
        $this->assertHtmlContains('<span class="cui-stat-label">Higher page reuse</span>', $html);
        $this->assertHtmlContains('<div class="cui-surface cui-logo-item"><strong>Northstar</strong></div>', $html);
    }

    public function testPersonCard(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'person' with {align: 'center', variant: 'flat', photo: '/p.jpg', photo_alt: 'Portrait', meta: 'Engineering', name: 'Jane Smith', role: 'Staff engineer'} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-person" data-align="center" data-variant="flat">', $html);
        $this->assertHtmlContains('<img class="cui-person-photo" src="/p.jpg" alt="Portrait">', $html);
        $this->assertHtmlContains('<p class="cui-person-meta">Engineering</p>', $html);
        $this->assertHtmlContains('<h3 class="cui-person-name">Jane Smith</h3>', $html);
        $this->assertHtmlContains('<p class="cui-person-role">Staff engineer</p>', $html);
    }

    public function testPersonLinkCardWithBioAndLinks(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'person' with {href: '/team/jane', orient: 'row', name: 'Jane Smith', bio: 'Short bio.'} %}
                    {% cui_slot 'links' %}<li><a href="mailto:j@example.com">Email</a></li>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<a class="cui-surface cui-person" href="/team/jane" data-orient="row">', $html);
        $this->assertHtmlContains('<p class="cui-person-bio">Short bio.</p>', $html);
        $this->assertHtmlContains('<ul class="cui-cluster"><li><a href="mailto:j@example.com">Email</a></li></ul>', $html);
    }

    public function testOrgChartRecursion(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'org-chart' with {layout: 'horizontal', line: 'dashed', nodes: [{name: 'Chief Person', role: 'CEO', children: [{name: 'Report One'}, {name: 'Report Two'}]}]} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<ol class="cui-org-chart" data-layout="horizontal" data-line="dashed" aria-label="Organisation chart">', $html);
        $this->assertHtmlContains('<article class="cui-surface cui-person cui-org-card" data-align="center">', $html);
        $this->assertHtmlContains('<h3 class="cui-person-name">Chief Person</h3>', $html);
        $this->assertHtmlContains('<ol class="cui-org-children">', $html);
        $this->assertHtmlContains('<h3 class="cui-person-name">Report Two</h3>', $html);
        $this->assertSame(3, substr_count($html, '<li class="cui-org-node">'));
    }

    public function testEventCard(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'event-card' with {status: 'upcoming', variant: 'featured', title: 'CSS workshop', href: '/events/css', category: 'Workshop', datetime: '2026-06-15T10:00', end_datetime: '2026-06-15T12:00', month: 'Jun', day: '15', year: '2026', meta: ['10:00–12:00', 'Amsterdam'], excerpt: 'Two hours.', status_label: 'Upcoming'} %}
                    {% cui_slot 'actions' %}<a class="cui-button" href="/events/css">Register</a>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-event-card" data-status="upcoming" data-variant="featured">', $html);
        $this->assertHtmlContains('<time class="cui-event-card-date" datetime="2026-06-15T10:00">', $html);
        $this->assertHtmlContains('<span class="cui-event-card-date-month">Jun</span>', $html);
        $this->assertHtmlContains('<span class="cui-event-card-date-day">15</span>', $html);
        $this->assertHtmlContains('<span class="cui-event-card-date-year">2026</span>', $html);
        $this->assertHtmlContains('<time datetime="2026-06-15T12:00" hidden></time>', $html);
        $this->assertHtmlContains('<h3 class="cui-display" data-cui-event-title><a href="/events/css">CSS workshop</a></h3>', $html);
        $this->assertHtmlContains('<li>Amsterdam</li>', $html);
        $this->assertHtmlContains('<span>Upcoming</span>', $html);
    }

    public function testItineraryItem(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'itinerary-item' with {marker: '08:30', datetime: '08:30', title: 'Coffee with the team'} %}<p>Plan the day.</p>{% endcui %}
                {% cui 'itinerary-item' with {tag: 'div', marker: 'Day 1', title: 'Arrival'} %}{% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<li class="cui-surface cui-itinerary-item">', $html);
        $this->assertHtmlContains('<time class="cui-itinerary-marker" datetime="08:30">08:30</time>', $html);
        $this->assertHtmlContains('<h3>Coffee with the team</h3>', $html);
        $this->assertHtmlContains('<div class="cui-surface cui-itinerary-item">', $html);
        $this->assertHtmlContains('<span class="cui-itinerary-marker">Day 1</span>', $html);
    }

    public function testContactCard(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'contact-card' with {eyebrow: 'Support', title: 'Customer success', variant: 'inverse', methods: [{term: 'Email', label: 'hello@example.com', href: 'mailto:hello@example.com'}]} %}
                    <p>We reply within one business day.</p>
                    {% cui_slot 'actions' %}<a class="cui-button" href="mailto:hello@example.com">Send a message</a>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-stack cui-contact-card" data-variant="inverse">', $html);
        $this->assertHtmlContains('<h3 class="cui-display">Customer success</h3>', $html);
        $this->assertHtmlContains('<p>We reply within one business day.</p>', $html);
        $this->assertHtmlContains('<dl class="cui-contact-methods">', $html);
        $this->assertHtmlContains('<dt>Email</dt>', $html);
        $this->assertHtmlContains('<dd><a href="mailto:hello@example.com">hello@example.com</a></dd>', $html);
        $this->assertHtmlContains('<div class="cui-cluster"><a class="cui-button" href="mailto:hello@example.com">Send a message</a></div>', $html);
    }

    public function testContactMethodsInline(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'contact-methods' with {layout: 'inline', items: [{term: 'Hours', label: 'Mon–Fri · 9:00–18:00'}]} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<dl class="cui-contact-methods" data-layout="inline">', $html);
        $this->assertHtmlContains('<dt>Hours</dt>', $html);
        $this->assertHtmlContains('<dd>Mon–Fri · 9:00–18:00</dd>', $html);
    }

    public function testLocationCard(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'location-card' with {orient: 'stacked', title: 'Amsterdam HQ', image: '/office.jpg', image_alt: 'Our office', address: ['Keizersgracht 123', '1015 CJ Amsterdam'], methods: [{term: 'Phone', label: '+31 20 123 4567', href: 'tel:+31201234567'}]} %}
                    {% cui_slot 'actions' %}<a class="cui-button" href="/directions">Get directions</a>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-location-card" data-orient="stacked">', $html);
        $this->assertHtmlContains('<div class="cui-location-card-media">', $html);
        $this->assertHtmlContains('<img src="/office.jpg" alt="Our office">', $html);
        $this->assertHtmlContains('<address>Keizersgracht 123<br>1015 CJ Amsterdam</address>', $html);
        $this->assertHtmlContains('<dl class="cui-contact-methods" data-layout="inline">', $html);
        $this->assertHtmlContains('<dd><a href="tel:+31201234567">+31 20 123 4567</a></dd>', $html);
    }

    public function testVacancyCard(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'vacancy-card' with {orient: 'row', status: 'new', status_label: 'Nieuw', title: 'WijkBOA', href: '/vacatures/wijkboa', eyebrow: 'Veiligheid', lead: 'Maak het verschil.', meta: ['36 uur', 'Vlaardingen'], image: '/wijkboa.jpg', data: {team: 'boa'}} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-vacancy-card" data-orient="row" data-status="new" data-team="boa">', $html);
        $this->assertHtmlContains('<a href="/vacatures/wijkboa" aria-label="WijkBOA"><img src="/wijkboa.jpg" alt=""></a>', $html);
        $this->assertHtmlContains('<h3 class="cui-display"><a href="/vacatures/wijkboa">WijkBOA</a></h3>', $html);
        $this->assertHtmlContains('<p class="cui-lead">Maak het verschil.</p>', $html);
        $this->assertHtmlContains('<ul class="cui-meta" data-variant="chips">', $html);
        $this->assertHtmlContains('<li>36 uur</li>', $html);
        $this->assertHtmlContains('<span>Nieuw</span>', $html);
    }

    public function testVacancyDetail(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'vacancy-detail' with {eyebrow: 'Veiligheid', title: 'Toezichthouder', lead: 'Houd toezicht.', meta: ['32–36 uur', 'Schaal 5']} %}
                    <section><h2>Wat ga je doen?</h2></section>
                    {% cui_slot 'aside' %}<p class="cui-eyebrow">Snel overzicht</p>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<div class="cui-vacancy-detail">', $html);
        $this->assertHtmlContains('<header class="cui-stack">', $html);
        $this->assertHtmlContains('<h1 class="cui-display">Toezichthouder</h1>', $html);
        $this->assertHtmlContains('<ul class="cui-meta" data-variant="chips">', $html);
        $this->assertHtmlContains('<section><h2>Wat ga je doen?</h2></section>', $html);
        $this->assertHtmlContains('<aside class="cui-vacancy-aside cui-surface cui-stack"><p class="cui-eyebrow">Snel overzicht</p></aside>', $html);
    }

    public function testMediaFullAndFigure(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'media-full' with {src: '/banner.jpg', ratio: 'wide', bleed: true, radius: 'none'} %}{% endcui %}
                {% cui 'figure' with {src: '/schema.jpg', ratio: 'square', align: 'center', caption: 'Figure 1. Composition.'} %}{% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<div class="cui-media-full" data-ratio="wide" data-bleed="full" data-radius="none"><img src="/banner.jpg" alt=""></div>', $html);
        $this->assertHtmlContains('<figure class="cui-figure" data-ratio="square" data-align="center">', $html);
        $this->assertHtmlContains('<figcaption>Figure 1. Composition.</figcaption>', $html);
    }

    public function testMediaCardWithLinkedMedia(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'media-card' with {orient: 'row', metadata: 'Pattern', title: 'Editorial spread', href: '/read', image: '/cover.jpg'} %}<p>Copy.</p>{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-surface cui-media-card" data-orient="row">', $html);
        $this->assertHtmlContains('<a class="cui-media-card-media" href="/read" aria-label="Editorial spread"><img src="/cover.jpg" alt=""></a>', $html);
        $this->assertHtmlContains('<p class="cui-metadata">Pattern</p>', $html);
        $this->assertHtmlContains('<h3><a href="/read">Editorial spread</a></h3>', $html);
        $this->assertLessThan(strpos($html, '<div class="cui-stack">'), strpos($html, 'cui-media-card-media'), 'media leads by default');
    }

    public function testMediaCardMediaPositionEnd(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'media-card' with {media_position: 'end', title: 'Spread', image: '/cover.jpg'} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertGreaterThan(strpos($html, '<div class="cui-stack">'), strpos($html, 'cui-media-card-media'), 'media follows the body');
    }

    public function testMediaOverlay(): void
    {
        $twig = $this->createTwig([
            'index' => <<<'TWIG'
                {% cui 'media-overlay' with {align: 'center', scrim: 'solid', eyebrow: 'Campaign', title: 'Above the fold', image: '/campaign.jpg'} %}
                    <p>Overlay copy.</p>
                    {% cui_slot 'actions' %}<a class="cui-button" data-variant="primary" href="/campaign">Read</a>{% endcui_slot %}
                {% endcui %}
                TWIG,
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<article class="cui-media-overlay" data-align="center" data-scrim="solid">', $html);
        $this->assertStringNotContainsString('cui-surface', $html, 'overlay cards carry no surface chrome');
        $this->assertHtmlContains('<h3 class="cui-display">Above the fold</h3>', $html);
        $this->assertHtmlContains('<p>Overlay copy.</p>', $html);
    }

    public function testPaginationNumbered(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'pagination' with {label: 'Paginering', prev: {label: 'Vorige'}, pages: [{href: '/list', label: '1', current: true}, {href: '/list?page=2', label: '2'}, {ellipsis: true}, {href: '/list?page=8', label: '8'}], next: {href: '/list?page=2', label: 'Volgende'}} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<nav class="cui-pagination" aria-label="Paginering">', $html);
        $this->assertHtmlContains('<span class="cui-pagination-link" data-control="prev" aria-disabled="true"><span aria-hidden="true">&#8592;</span> Vorige</span>', $html);
        $this->assertHtmlContains('<a class="cui-pagination-link" href="/list" aria-current="page">1</a>', $html);
        $this->assertHtmlContains('<li aria-hidden="true"><span class="cui-pagination-ellipsis">&#8230;</span></li>', $html);
        $this->assertHtmlContains('<a class="cui-pagination-link" data-control="next" href="/list?page=2">Volgende <span aria-hidden="true">&#8594;</span></a>', $html);
    }

    public function testPaginationCompact(): void
    {
        $twig = $this->createTwig([
            'index' => "{% cui 'pagination' with {variant: 'compact', prev: {href: '/list'}, status: 'Pagina 2 van 8', next: {href: '/list?page=3'}} %}{% endcui %}",
        ]);

        $html = $twig->render('index');

        $this->assertHtmlContains('<nav class="cui-pagination" data-variant="compact" aria-label="Pagination">', $html);
        $this->assertHtmlContains('<a class="cui-pagination-link" data-control="prev" href="/list"><span aria-hidden="true">&#8592;</span> Previous</a>', $html);
        $this->assertHtmlContains('<span class="cui-pagination-status">Pagina 2 van 8</span>', $html);
    }
}
