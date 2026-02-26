# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress child theme for the FiveTwoFive parent theme. The child theme extends the parent theme with custom styles, animations, and functionality using GSAP (GreenSock Animation Platform) for enhanced user experiences.

## Development Commands

### Build and Watch Commands

- **`npm start`** - Full development mode with BrowserSync (compiles SASS, processes JS, copies vendor files, watches for changes, and auto-reloads browser)
- **`npm run build`** - Production build (compiles and minifies all assets)
- **`npm run watch`** - Watches SASS files only (no BrowserSync)

### Gulp Tasks

- **`gulp styles`** - Compile SASS to CSS with autoprefixer, minification, and sourcemaps
- **`gulp scripts`** - Concatenate and minify JavaScript files
- **`gulp vendor`** - Copy GSAP files from node_modules to dist/js/vendor

### BrowserSync Configuration

BrowserSync requires a `.env` file with `PROXY_URL` set to your local WordPress site URL (see `.env.example`). The proxy URL should match your Local by Flywheel or similar local development environment.

## Architecture

### File Structure

```
fivetwofive-theme-child/
├── assets/
│   ├── src/
│   │   ├── js/
│   │   │   ├── animations.js      # GSAP animation logic
│   │   │   └── copy-code.js       # Copy-to-clipboard functionality
│   │   └── sass/
│   │       └── style.scss         # Main SASS entry point
│   └── dist/                      # Compiled assets (auto-generated)
│       ├── css/
│       │   ├── style.css
│       │   └── style.css.map
│       └── js/
│           ├── main.js            # Concatenated and minified JS
│           ├── main.js.map
│           └── vendor/            # GSAP libraries
├── template-parts/
│   └── modules/                   # Custom WordPress module templates
├── functions.php                  # WordPress theme functions and asset enqueuing
├── gulpfile.js                    # Build configuration
└── style.css                      # Theme metadata and minimal overrides
```

### Asset Pipeline

The build process uses Gulp to:

1. **SASS Compilation**: `assets/src/sass/**/*.scss` → `assets/dist/css/style.css`
   - Autoprefixer for browser compatibility
   - CSS minification with CleanCSS
   - Sourcemaps for debugging

2. **JavaScript Processing**: `assets/src/js/**/*.js` → `assets/dist/js/main.js`
   - Concatenation of all JS files (animations.js, copy-code.js, etc.)
   - Uglification/minification
   - Sourcemaps for debugging

3. **Vendor Files**: GSAP libraries copied from node_modules to `assets/dist/js/vendor/`

### WordPress Integration

**functions.php** handles all asset enqueuing:

- **Child theme styles**: `style.css` (metadata and light overrides)
- **Compiled SASS**: `assets/dist/css/style.css` (main custom styles)
- **GSAP Core**: `assets/dist/js/vendor/gsap.min.js`
- **GSAP ScrollTrigger**: `assets/dist/js/vendor/ScrollTrigger.min.js` with inline registration
- **Custom scripts**: `assets/dist/js/main.js` (depends on GSAP libraries)

All scripts are loaded in the footer for optimal performance.

### Animation System

The animation system in `assets/src/js/animations.js` is modular and configuration-driven:

- **ANIMATION_CONFIG**: Centralized animation settings (duration, easing, stagger timing, etc.)
- **Page Detection**: Automatically determines homepage vs. inner pages
- **Currently Commented Out**: Homepage and inner page animations are temporarily disabled (lines 26-30)
- **Active**: Only global button hover animations are currently running

#### Animation Functions

- **`initHomePageAnimations()`**: Hero, services, works, testimonials, and CTA sections
- **`initInnerPageAnimations()`**: Module-based animations with special handling for:
  - Work cards (`.ftf-module-works`)
  - Multi-column layouts (`.ftf-module-multi-column`)
  - Standard modules with above-fold detection
  - Excludes shortcode-based CTA modules (`.ftf-module-code.cta-shortcode`)
- **`initGlobalAnimations()`**: Button hover effects (scale on hover)
- **`initTestimonialAnimations()`**: Swiper carousel slide transitions

#### Key Animation Patterns

- Sequential stagger effects for related elements
- ScrollTrigger-based animations (trigger at 75% viewport)
- Above-fold content gets immediate animation (100% trigger point)
- Module-level delays based on position (index * stagger)

### WordPress Module Templates

Custom template parts in `template-parts/modules/` override parent theme modules. Currently includes `module-content-and-media.php`.

## Important Notes

- **Do not modify** `assets/dist/` files directly - they are auto-generated
- **Always edit** source files in `assets/src/`
- **Run build command** after modifying source files
- **GSAP version**: 3.12.7 (specified in package.json)
- **Parent theme dependency**: This child theme requires the `fivetwofive-theme` parent theme
- **Animation toggle**: Page animations are currently commented out in animations.js (lines 26-30)

## Versioning

Version numbers follow semantic versioning and are maintained in:
- `package.json` (line 3)
- `style.css` (line 6)
- `functions.php` (used for cache busting via `$theme->get('Version')`)

Update all three locations when incrementing version numbers.

## CHANGELOG.md

The project maintains a detailed CHANGELOG.md following the Keep a Changelog format. Document all notable changes including features, fixes, and technical improvements.