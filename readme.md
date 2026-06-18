# Jabal Torres Portfolio WordPress Site

This repository contains the WordPress codebase for Jabal Torres' portfolio site. It is a full WordPress install with a custom FiveTwoFive parent theme, a portfolio-specific child theme, Advanced Custom Fields JSON definitions, and several first-party plugins for custom post types and site utilities.

## Project Structure

```text
.
├── wp-content/
│   ├── themes/
│   │   ├── fivetwofive-theme/        # Custom parent theme
│   │   └── fivetwofive-theme-child/  # Portfolio-specific child theme
│   └── plugins/
│       ├── fivetwofive-contact-form/
│       ├── fivetwofive-cta/
│       ├── fivetwofive-daily-quotes/
│       ├── fivetwofive-events-post-type/
│       ├── fivetwofive-lp-post-type/
│       ├── fivetwofive-music-post-type/
│       ├── fivetwofive-resource-post-type/
│       ├── fivetwofive-template-column-display/
│       └── fivetwofive-work-post-type/
├── change-log.md
├── docs/
│   └── contact-form-plan.md
└── readme.md
```

## Themes

### `fivetwofive-theme`

The parent theme is based on Underscores and provides the shared FiveTwoFive framework:

- WordPress templates for posts, pages, archives, search, 404s, and custom post types.
- ACF-powered modular page template at `page-templates/template-module.php`.
- Module templates in `template-parts/modules/`.
- Theme customizer settings for colors and typography.
- SCSS and JavaScript source files in `assets/src/`.
- Compiled production assets in `assets/dist/`.
- ACF local JSON in `acf-json/`.

Supported module templates include accordion, announcement, code, content and media, CTA, events, gallery, hero, multi-column, resources, testimonials carousel, and works.

### `fivetwofive-theme-child`

The child theme contains portfolio-specific styling and behavior:

- Enqueues parent assets, child compiled CSS, GSAP/ScrollTrigger, and child compiled JavaScript.
- Adds GSAP and ScrollTrigger from local compiled vendor assets.
- Builds child SCSS and JavaScript into `assets/dist/`.
- Overrides the content and media module template.
- Includes BrowserSync support through `.env`.

## First-Party Plugins

The repository includes several small custom plugins:

- `fivetwofive-work-post-type`: registers the `ftf_work` post type and `ftf_work_category` taxonomy.
- `fivetwofive-resource-post-type`: registers resources, creative entries, and related taxonomies.
- `fivetwofive-events-post-type`: registers the `ftf_event` post type.
- `fivetwofive-lp-post-type`: registers landing pages.
- `fivetwofive-music-post-type`: registers music posts and music taxonomies.
- `fivetwofive-daily-quotes`: registers daily quotes and a latest quote shortcode.
- `fivetwofive-cta`: provides configurable call-to-action shortcode output.
- `fivetwofive-contact-form`: provides a dependency-light contact form shortcode, stores submissions as `ftf_submission`, and sends notifications through `wp_mail()` with a swappable mail transport.
- `fivetwofive-template-column-display`: adds a sortable page template column in the WordPress admin.

## Local Development

This site is designed to run as a local WordPress install. The repository root is the WordPress public directory (`app/public` in a Local-style setup).

Typical local workflow:

1. Start the site in Local or the configured WordPress environment.
2. Confirm the active theme is `FiveTwoFive Theme Child`.
3. Confirm required plugins are active, especially Advanced Custom Fields Pro and the first-party FiveTwoFive plugins.
4. Run asset commands from the parent theme or child theme directory as needed.

## Asset Commands

Parent theme:

```bash
cd wp-content/themes/fivetwofive-theme
npm install
npm run build
npm run watch
```

Child theme:

```bash
cd wp-content/themes/fivetwofive-theme-child
npm install
cp .env.example .env
npm run build
npm run watch
```

Set `PROXY_URL` in the child theme `.env` file to the local site URL used by Local or your WordPress environment.

Composer-backed plugins:

```bash
cd wp-content/plugins/fivetwofive-contact-form
composer install
composer run lint
composer run lint:fix
```

The CTA plugin also uses Composer autoloading, but currently only defines the autoload configuration.

## ACF

The parent theme saves and loads ACF local JSON from:

```text
wp-content/themes/fivetwofive-theme/acf-json
```

When changing field groups in the WordPress admin, review generated JSON changes before committing.

## Notes for Maintainers

- The root of this repo is a WordPress public directory, not just a theme package.
- Avoid committing local-only configuration, uploads, cache files, or generated backup artifacts.
- Keep source files and compiled assets together when the deployed site expects compiled files from `assets/dist/`.
- The child theme is the safest place for portfolio-specific customization.
- The parent theme should stay reusable unless a change is intentionally framework-level.

## Deployment

Before pushing deploy-oriented changes:

1. Confirm `git status` only contains intended files.
2. Build any changed theme assets.
3. Smoke-test the local site.
4. Commit with a clear message.
5. Push to `origin/master`.
