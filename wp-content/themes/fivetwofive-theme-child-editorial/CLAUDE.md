# CLAUDE.md

Guidance for Claude Code when working in the **FiveTwoFive Editorial** child theme.

## Overview

A second child theme of the **`fivetwofive-theme`** parent (sibling to `fivetwofive-theme-child`, the personal-portfolio child). It implements a modern-minimal **editorial** design direction: warm paper background, 0.5px hairline dividers, near-mono palette with a single accent used only on buttons / eyebrow labels / numbered tags, system-font typography at weights 400–500, and two colored section exceptions (beige stats band, forest-green CTA).

Most functionality comes from the parent framework + first-party plugins (ACF flexible-content modules, the `ftf_work` CPT, the contact-form plugin). This child owns styling, a few module/template overrides, and content composition.

> **Keep tracked artifacts generic / PII-free.** Client name, employer names, and figures are never committed here; client copy/imagery is treated as external "client-supplied" content.

## Development commands

- `npm start` — dev mode with BrowserSync (compile SASS + JS, watch, live-reload)
- `npm run build` — production build (compile + minify SASS and JS)
- `npm run watch` — watch SASS only (no BrowserSync)

Gulp tasks: `gulp styles`, `gulp scripts`, `gulp build`.

BrowserSync needs a `.env` with `PROXY_URL` set to the local site URL (see `.env.example`).

## Structure

```
fivetwofive-theme-child-editorial/
├── assets/
│   ├── src/
│   │   ├── js/main.js          # front-end scripts (minimal)
│   │   └── sass/style.scss     # main SASS entry
│   └── dist/                   # compiled output (CSS + JS committed; *.map gitignored)
├── template-parts/modules/     # child overrides of parent ACF modules
├── functions.php               # asset enqueuing + parent integration
├── gulpfile.js                 # build config
└── style.css                   # theme metadata + light overrides
```

## Asset pipeline

- SASS: `assets/src/sass/**/*.scss` → `assets/dist/css/style.css` (autoprefixer, minify, sourcemaps)
- JS: `assets/src/js/**/*.js` → `assets/dist/js/main.js` (concat, uglify, sourcemaps)
- No GSAP / vendor copy (unlike the portfolio child).

## WordPress integration

`functions.php` enqueues, in order: the child `style.css` (after the parent's `fivetwofive-theme-main` + `fivetwofive-theme-template-module` handles), the compiled SASS bundle, and `main.js` (in the footer).

## Conventions

- **Never edit `assets/dist/` directly** — edit `assets/src/` and rebuild.
- **Rebuild before committing** any SCSS/JS change.
- **Parent dependency:** requires the `fivetwofive-theme` parent theme.
- **Versioning:** keep `package.json` and `style.css` versions in sync (`functions.php` reads the version from `style.css` for cache-busting).
- Built/previewed via **theme-switch on the shared Local install** — activating it doesn't affect the deployed portfolio (the active theme is a per-install DB setting, not tracked in git).

## Tracking

Work is tracked under **epic #135** (one sub-issue per PR). See the repo root `AGENTS.md` for the branch → commit → PR flow and the deny-all `.gitignore` gotcha (new files are silently untracked until whitelisted).
