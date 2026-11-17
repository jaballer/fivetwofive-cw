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
├── template-parts/active-campaign-form.php   the lead form (ActiveCampaign form 5)
├── assets/css/
│   ├── design-system.css      tokens, text styles, buttons, alert, logo   ← synced
│   ├── site.css               base + section styles                      ← synced
│   └── wordpress.css          not in the HTML source: admin bar, skip link, form errors, post styles
├── assets/js/
│   ├── main.js                FAQ accordion (front page only)
│   └── active-campaign-form.js  ActiveCampaign's embed script, verbatim (loads with the form)
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

The HTML's placeholder form is not synced. The script swaps it for the ActiveCampaign template part.

## Lead form (ActiveCampaign)

`template-parts/active-campaign-form.php` renders ActiveCampaign form 5 with the site's own field and button styles. Submissions go to `916marketing.activehosted.com`, and the thank-you or error message comes back from ActiveCampaign.

- **Keep ActiveCampaign's contract.** The script depends on the form action, hidden inputs, field names (`fullname`, `email`, `field[3]`, `field[2]`), the `#_form_5_` and `#_form_5_submit` ids, and the `._form-content` / `._form-thank-you` wrappers. Labels, placeholders, and classes are free to change.
- **Email stays `type="text"`.** ActiveCampaign's serializer skips `type="email"` inputs, so the address would never be sent.
- **If the form changes in ActiveCampaign** (new field, new dropdown option), re-export the embed. Then update the hidden inputs, fields, and options in the template part and replace `assets/js/active-campaign-form.js` with the export's `<script>`. Don't copy the export's `<style>`.
- To reuse the form on another page template, call `get_template_part( 'template-parts/active-campaign-form' )`. It enqueues its own script.

## Known gaps

- No `screenshot.png` yet.
