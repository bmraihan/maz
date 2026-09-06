# MAZ Heights — WordPress theme

Custom WordPress theme implementing the approved **MAZ Heights Website**
design (graphite / brick / stone palette, Archivo + IBM Plex Sans/Mono)
from the Claude Design handoff bundle in the repo root (`project/MAZ
Heights Website.dc.html`, `chats/chat1.md`).

## What's built

- **Homepage** (`front-page.php`) — pixel-matches the approved design:
  sticky header, hero with inline quote form, 3 services, 5-stage
  process, recent work, price guide, testimonials + accreditations,
  CTA band, footer.
- **Services, Projects (case studies) and Testimonials** are WordPress
  custom post types (`maz_service`, `maz_project`, `maz_testimonial`),
  not hard-coded markup — a non-developer can add/edit them from
  wp-admin. On first activation they're seeded with the exact copy
  from the design, so the site looks finished immediately.
- **"Get a fixed price" quote form** posts to a **Formspree** endpoint
  you configure at *Appearance → Customize → Quote Form*. It's a real
  `<form>` (works with JS off) progressively enhanced by
  `assets/js/quote-form.js` + `assets/js/main.js` into an inline
  AJAX submit with validation and success/error messaging.
- **Business details** (phone, hours, email, address, areas covered,
  Google rating) are editable at *Appearance → Customize → Business
  Details* rather than hard-coded, so the placeholder phone number
  etc. from the design can be replaced without touching code.
- **Extensions / Kitchens / Bathrooms pages** — the design chat
  transcript calls these out as pages to design "next" but they were
  never actually designed. Rather than invent unapproved content, each
  is seeded as a WordPress Page using the `Service Landing`
  template (`page-templates/service-landing.php`), which pulls the
  matching service's real copy/price and related case studies. Swap in
  a full design for these later without changing the nav or URLs.
- **Case studies** get a real single template (`single-maz_project.php`)
  and archive (`archive-maz_project.php`) — the "Full case study →" and
  "All N projects →" links in the design go somewhere real.
- **Photo placeholders**: the design's grey "PHOTO · …" boxes are
  rendered as branded inline-SVG graphics (`maz_heights_placeholder_svg()`
  in `inc/template-tags.php`) rather than stock photography — the
  environment this theme was built in has no general internet access,
  only package registries, so real stock photos couldn't be fetched.
  Upload a featured image to any Service/Project post in wp-admin and
  it's used automatically instead.

## Requirements

- WordPress 6.4+, PHP 8.0+
- A [Formspree](https://formspree.io) account (free tier is fine) to
  receive quote-form submissions
- PHP 8.x + Composer, and Node 18+, if you want to run the test suites

## Installation

1. Copy (or symlink) this `maz-heights` directory into
   `wp-content/themes/`.
2. Activate it at *Appearance → Themes*. This seeds the default
   services/projects/testimonials and the three service landing pages
   (idempotent — re-activating won't create duplicates).
3. Go to *Appearance → Customize*:
   - **Quote Form** → paste your Formspree endpoint URL
     (`https://formspree.io/f/xxxxxxxx`). Until this is set, the form
     shows "call us instead" rather than silently failing.
   - **Business Details** → real phone number, hours, email, address,
     areas covered, Google rating.
   - **Site Identity** → upload a logo (optional; falls back to the
     inline SVG roofline mark from the design) and set the site title.
4. Under *Appearance → Menus*, optionally assign a menu to "Primary
   Navigation" / "Footer Navigation" — until you do, the header uses
   the same anchor-based nav as the design (Extensions/Kitchens/
   Bathrooms → `#services`, etc.).
5. Edit the seeded content under the **Services**, **Projects** and
   **Testimonials** admin menu items as real photos/copy become
   available (each has an "MAZ Heights details" meta box for its
   price/location/rating/etc. fields, plus a normal featured image).

## Content architecture — what's CMS-editable vs. code-configured

| Content | Where it lives | Why |
|---|---|---|
| Services, Projects, Testimonials | Custom post types + meta boxes | Changes often; a non-developer should be able to add a new finished job or review without a deploy. |
| Business details (phone/hours/etc.) | Customizer | Changes rarely, but is exactly the kind of thing a client edits without asking a developer. |
| Formspree endpoint | Customizer | Same reasoning; also keeps the real endpoint out of source control. |
| 5-stage process, price guide table, trust/accreditation badges | Filterable PHP data (`inc/template-tags.php`) | Describes business policy (how the process works, published pricing), which changes as a deliberate decision alongside other business/pricing changes, not as day-to-day content. Still overridable without editing this file, via the `maz_heights_process_stages`, `maz_heights_price_guide_rows`, `maz_heights_trust_badges` and `maz_heights_accreditation_badges` filters (e.g. from a small site-specific plugin, or a future admin screen). |

## Testing

Two independent, fast unit-test suites — no WordPress install, database
or browser required:

```bash
composer install   # once
composer test       # PHPUnit + Brain Monkey — 76 tests

npm install         # once
npm test             # Jest — 26 tests
```

**What's covered:** every sanitize/validation callback (post meta,
Formspree endpoint URL, quote-form client-side validation for UK
phone/postcode formats), the custom-post-type and meta-field
registration contracts, the seeded default content's shape and
internal consistency (e.g. every seeded meta key is actually a
registered field; exactly one project is marked "featured"), the
Customizer field list matching the business-info defaults it backs,
the placeholder-SVG output (including that it escapes its label
against injection), the fallback navigation links, and the Google
Fonts URL's exact character set.

**What's *not* covered, and why:** this is unit testing against
[Brain Monkey](https://brain-wp.github.io/BrainMonkey/) WordPress
function stubs, run in a sandboxed build environment with no MySQL, no
`wp-cli`, and no browser automation available. That means there is
**no automated verification that WordPress actually renders these
templates correctly end to end** — the seeded posts really appear on
the homepage, the sticky header behaves correctly, the Formspree
submission round-trips, the responsive breakpoints hold up in a real
browser. I have not opened this site in a browser and cannot claim the
rendered page matches the design beyond careful reading of the PHP/CSS.
Before launch, add:
- an integration layer via [`@wordpress/env`](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/) (`wp-env`) to run a real
  WordPress + MySQL locally/in CI, and
- a [Playwright](https://playwright.dev/) suite against that instance
  covering: homepage render, quote-form happy path + validation errors
  against a test Formspree endpoint, mobile breakpoints, and the
  service-landing/case-study/archive templates.

## File structure

```
maz-heights/
├── style.css                  WP theme header (required by WP)
├── functions.php               bootstraps inc/*.php
├── header.php / footer.php     shared chrome
├── front-page.php              homepage
├── page.php / index.php        generic Page / fallback templates
├── single-maz_project.php      case study
├── archive-maz_project.php     "Our work" index
├── page-templates/
│   └── service-landing.php     Extensions/Kitchens/Bathrooms placeholder
├── inc/
│   ├── helpers.php              business-info + formatting helpers
│   ├── setup.php                theme supports, nav menus, asset enqueue
│   ├── customizer.php           Business Details settings
│   ├── post-types.php           CPTs + meta field contracts + sanitizers
│   ├── meta-boxes.php           admin UI for the CPT meta fields
│   ├── seed-content.php         one-time default content + landing pages
│   ├── forms.php                Formspree endpoint setting + form markup
│   ├── nav-fallback.php         default header nav before a menu is set
│   └── template-tags.php        queries, price/process data, SVG placeholders
├── assets/
│   ├── css/style.css            all component styles + responsive rules
│   └── js/
│       ├── quote-form.js        pure validation logic (no DOM)
│       └── main.js              DOM wiring + Formspree fetch submit
├── tests/
│   ├── php/                     PHPUnit + Brain Monkey
│   └── js/                      Jest
├── composer.json / phpunit.xml
└── package.json
```
