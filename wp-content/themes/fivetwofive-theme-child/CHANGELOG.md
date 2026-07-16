# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Direct-access guard (`ABSPATH` check) at the top of `functions.php`.
- Text-domain loading via `load_child_theme_textdomain()` on `after_setup_theme`, making the child's UI strings translatable.
- `Requires PHP: 8.0` to the `style.css` header, documenting the floor the parent theme already requires (#165).
- `browserslist` config (`["defaults"]`) to `package.json`, making autoprefixer's build target explicit instead of implicit (#165).
- `.env` to `.gitignore` for parity with the editorial child theme; cosmetic only — the repo-root `.gitignore` already ignores `.env` globally (#165).

### Changed
- Replaced jQuery Fancybox with GLightbox in the `module-content-and-media` override, matching the parent theme's Phase 3 library modernization. Removes this module's jQuery dependency for the image/video lightbox.
- Bumped `package.json` version from `1.0.0` to `1.0.1` to match `style.css`, which `functions.php` reads for cache-busting via `$theme->get( 'Version' )` (#165).
- Migrated `assets/src/sass/style.scss` from `@import` to `@use` for the two module partials, clearing the Dart Sass deprecation warnings ahead of `@import`'s removal in 3.0 (#166). Purely mechanical: `$color_primary` is declared and consumed only within `style.scss` itself, so `@use` namespacing doesn't affect it — no `as *` or explicit namespace access needed. Compiled `assets/dist/css/style.css` output is byte-identical to the pre-migration build.
- Restructured `assets/src/sass/` into ITCSS folders (`abstracts/`, `overrides/`, `layouts/`, `components/`, `modules/`), matching the shape already proven in the editorial child theme (#167). Extracted `abstracts/_tokens.scss` — a `:root` custom-property layer de-magicing every hardcoded hex in the former grab-bag `style.scss` (`$color_primary` plus the copy-code button's ~20 state colors) — and split the remaining rules into real partials: `components/_copy-code`, `components/_post-card`, `components/_module-description`, `overrides/_bootstrap` (the `.row > *` gutter override), `layouts/_sidebar`, and `modules/_cta`. Removed two long-dead commented-out rule blocks (`.ftf-module-works .card`, `.ftf-module-hero .container`) and a stray commented-out declaration. Relocated the `.ftf-module__description` width override from `style.css` into the SCSS bundle — the parent enqueues `style.css` *before* `main.css`, so an override there loses any specificity tie; the SCSS bundle loads after both. Verified with a resolved-custom-property diff against the pre-restructure build (`style.css` + compiled SASS bundle, combined): all 28 compiled rules match value-for-value. Visual QA (screenshot comparison across home, a sidebar single post, archive grid/list, and a template-module page, per the issue's acceptance criteria) was not run against the live site in this session and still needs a human pass before merge.

### Fixed
- Corrected the child stylesheet dependency chain in `functions.php` (#164). The child re-enqueued the parent-owned `fivetwofive-theme-style` handle with a dependency array that WordPress silently discarded (the handle is already registered by the parent at priority 5), and that array named `fivetwofive-theme-template-module` — a handle the parent only registers on the module-template / `ftf_event` routes. The compiled SASS bundle now depends directly on the always-registered `fivetwofive-theme-main` (plus `fivetwofive-theme-style`), so its cascade after the parent framework is guaranteed by contract rather than by enqueue order. No visual change; removes latent dead/misleading code.

## [1.1.3] - 2025-05-08

### Fixed
- Prevented animation logic in [animations.js file](assets/src/js/animations.js) from targeting shortcode-based CTA modules by excluding elements with `.ftf-module.ftf-module-code.cta-shortcode` in `initInnerPageAnimations()`

## [1.1.2] - 2025-02-06

### Changed
- Refactored animations.js for better organization and performance
  - Added centralized ANIMATION_CONFIG for consistent animation settings
  - Split animation logic into separate specialized functions
  - Improved documentation with detailed function descriptions
  - Optimized work cards animation sequence
  - Enhanced inner page module animations
  - Standardized animation timing and easing

### Fixed
- Sequential animation issues on inner pages
- Work cards animation visibility
- Above-fold content animation timing
- Module animation consistency across different page types

## [1.1.1] - 2025-02-04

### Changed
- Moved GSAP files from node_modules to dist/js/vendor
- Updated build process to handle vendor files
- Improved dependency management best practices

### Fixed
- GSAP file loading issues
- Script dependency order

## [1.1.0] - 2025-02-04

### Added
- Integrated GSAP (GreenSock Animation Platform) for enhanced animations
  - Installed GSAP via npm
  - Added GSAP core and ScrollTrigger plugin
  - Created animations.js for centralized animation management

### Enhanced
- Homepage animations:
  - Hero section: Staggered entrance animations for title, text, and profile image
  - Services section: Scroll-triggered fade-in animations with stagger effect
  - Recent Works: Sequential animations for portfolio items with alternating directions
  - Testimonials: Bounce effect for avatars and smooth fade-in for testimonial text
  - CTA section: Subtle entrance animation
  - Buttons: Interactive hover animations

### Technical Details
- Added GSAP dependencies to package.json
- Updated gulpfile.js to handle JavaScript processing
- Configured ScrollTrigger plugin for scroll-based animations
- Implemented JavaScript build process with minification and sourcemaps
- Added proper script enqueuing in functions.php

### Developer Notes
- GSAP animations are modular and easily customizable
- ScrollTrigger configurations can be adjusted per section
- Animation timings and effects can be modified in animations.js
- Build process automatically handles JavaScript minification

## [1.0.0] - Initial Release
- Base child theme functionality 