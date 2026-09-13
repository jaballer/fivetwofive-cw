# 916 Marketing theme

A minimal classic WordPress theme. The landing page is hard-coded HTML in template files — no page builder, no ACF, no build step. It's a direct port of the static `916-marketing.html` landing page, which is styled with the 916 Marketing style guide.

## Structure

```
916-marketing/
├── style.css                  theme header only
├── functions.php              theme supports, enqueues, helpers, contact constants
├── header.php / footer.php    site header + footer (open/close the .page container)
├── front-page.php             the landing page: lists the sections in order
├── index.php                  fallback for posts, pages, archives, search, 404
├── template-parts/sections/   one hard-coded partial per landing-page section
├── assets/css/
│   ├── design-system.css      tokens, text styles, buttons, alert, logo   ← synced
│   ├── site.css               base + section styles                      ← synced
│   └── wordpress.css          WP-only additions (admin bar, skip link, post styles)
├── assets/js/main.js          FAQ accordion + lead-form confirmation (front page only)
├── assets/icons/              status-success.svg from the style guide
└── tools/sync-from-html.py    re-copies the synced files from the HTML source
```

## Editing

- **Copy and sections:** edit `template-parts/sections/*.php` directly. To reorder or remove a section, edit the list in `front-page.php`.
- **Phone and email:** constants at the top of `functions.php`.
- **Light/dark:** follows the system setting. An inline script in `<head>` sets `data-theme` before styles paint.

## Keeping in sync with the HTML source

If the static page changes, re-pull the CSS and section markup:

```bash
python3 tools/sync-from-html.py path/to/916-marketing.html
git diff
```

That overwrites the files marked "synced" above plus `template-parts/sections/`. Changes to the header, footer, or inline script in the HTML still have to be ported by hand to `header.php`, `footer.php`, and `assets/js/main.js`. Anything the script overwrites should only be edited in the HTML source.

## Known gaps

- **The lead form has no backend.** It shows a "Message sent" alert and doesn't send anything. Wire it to a handler (for example the `fivetwofive-contact-form` plugin or an `admin-post.php` action) before launch.
- No `screenshot.png` yet.
