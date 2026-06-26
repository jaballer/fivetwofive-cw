# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-06-26

First feature-complete release (epic #135).

### Added
- **Styling foundation** (#137): design tokens, system-font stack at weights 400/500, eyebrow + numbered-tag base, 0.5px hairlines, header/footer styling; editorial Customizer defaults via `fivetwofive_theme_default_theme_mods`; Google Fonts dequeued; accent neutralization (parent yellow → near-mono + forest).
- **Bespoke component overrides** (#138): editorial works-card grid (numbered, "sector · discipline", "Read →", "View all" tile), "ways to work" monoline icon cards (3 icons registered via `fivetwofive_theme_svg_icons_ui`), beige stats band, forest CTA, ~5:4 split hero.
- **Page composition** (#139): reproducible PII-free content build script (`ftf-tools/build-editorial-content.php`) for 4 pages, 6 placeholder works, and the Editorial Primary/Footer menus; `.previously` and `.portrait` styles; "View all" wired to the case-studies page.
- **Responsive + a11y hardening** (#140): `:focus-visible` rings + reduced-motion guard, stats band 2-up on mobile, beige-contrast fix.
- Module spacing now defaults to Medium framework-wide (#150).
- `screenshot.png` placeholder (1200×900) — to be replaced with a real render.

### Notes
- Two parent-framework enablements surfaced from this build: `.button:visited` color fix (#145) and SVG stroke/primitive support in the kses ruleset (#146).

## [0.1.0] - 2026-06-22

### Added
- Initial scaffold of the FiveTwoFive Editorial child theme (parent: `fivetwofive-theme`): `style.css` header, `functions.php` asset enqueuing, the Gulp build pipeline (SASS + JS, no GSAP/vendor copy), and `assets/src` entry points. The theme activates and builds cleanly; design tokens, the hairline/eyebrow system, Customizer defaults, fonts, and component overrides land in subsequent PRs.
