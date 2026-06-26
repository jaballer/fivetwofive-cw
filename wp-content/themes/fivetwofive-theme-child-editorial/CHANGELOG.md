# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `screenshot.png` placeholder (1200×900) so the theme shows a preview card in Appearance → Themes instead of a blank tile. Minimal editorial mock in the theme's palette (warm paper, hairline rules, near-mono text, single accent); to be replaced with a real render once the styling foundation lands.

## [0.1.0] - 2026-06-22

### Added
- Initial scaffold of the FiveTwoFive Editorial child theme (parent: `fivetwofive-theme`): `style.css` header, `functions.php` asset enqueuing, the Gulp build pipeline (SASS + JS, no GSAP/vendor copy), and `assets/src` entry points. The theme activates and builds cleanly; design tokens, the hairline/eyebrow system, Customizer defaults, fonts, and component overrides land in subsequent PRs.
