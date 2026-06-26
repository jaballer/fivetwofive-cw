# Contact Form Plugin — Scope & Plan

**Status:** Draft for review · **Created:** 2026-06-17 · **Owner:** Jabal Torres

A native "Contact Me" / "Contact Us" form delivered as a first-party plugin
(`fivetwofive-contact-form`), with email delivered through `wp_mail()` and
Postmark wired in as a separate, swappable mail transport.

---

## 1. Goals & non-goals

**Goals**

- A native contact form for this site, with **no paid/SaaS form dependency**.
- Reliable delivery to the site owner's inbox, with Postmark deliverability when configured.
- **Provider-agnostic delivery** — Postmark is the recommended default, but each client
  can use their own provider (SES, Mailgun, SendGrid, Google Workspace, generic SMTP)
  with no change to the form.
- Reusable as a first-party product asset across the FiveTwoFive theme's target
  audience (consultants, solopreneurs, small businesses).
- Stay consistent with the repo's dependency-light direction (post jQuery / Font
  Awesome / Fancybox removal) — no heavyweight form builder.

**Non-goals (for now)**

- A general-purpose form builder / multiple form types (this is *one* contact form).
- File-upload attachments.
- Marketing/broadcast email or list management.
- A paid CAPTCHA service.

---

## 2. Locked decisions

| Decision | Choice | Rationale |
|---|---|---|
| **Build vs. buy** | Build a lightweight first-party plugin | Matches existing plugin conventions; avoids re-bloating after the Phase 1–3 cleanup; becomes a reusable product asset |
| **Email transport** | `wp_mail()` + a separate transport — **Postmark-first, provider-agnostic** | Form stays 100% transport-agnostic, so each client can keep Postmark or swap in their own provider without touching the form |
| **Submission storage** | Persist every submission as a CPT (source of truth) | A lead is never lost even if email delivery fails — the highest-value reliability guarantee |
| **Placement** | Shortcode (canonical) + thin theme module wrapper | Mirrors `fivetwofive-cta`'s shortcode; the module drops into the ACF page builder like every other section |

---

## 3. Architecture

### 3.1 Core principle: separate the form from the transport

Two independent concerns, never coupled:

1. **The form** — markup, validation, spam defense, storage, and building the
   notification message. It only ever calls `wp_mail()`. **It knows nothing about Postmark.**
2. **The transport** — how mail physically leaves WordPress. Postmark today; SES /
   Mailgun / plain SMTP later, with zero changes to the form.

```
[ Visitor submits ]
        │  (AJAX → REST, with no-JS POST fallback)
        ▼
[ fivetwofive-contact-form ]
   • nonce + sanitize + validate
   • honeypot + time-trap (spam)
   • store submission as CPT  ←─ source of truth (survives mail failure)
   • build message, set Reply-To: visitor
        │  wp_mail()
        ▼
[ Mail transport layer ]  ← swappable, owns From: = verified domain
   • Postmark by default · any provider via SMTP config (not code)
        │  Postmark API / SMTP
        ▼
[ Owner inbox ]  +  optional auto-reply to visitor
```

### 3.2 From / Reply-To split (important)

Because delivery goes through Postmark, the **From** address must be on a
Postmark-verified domain/sender — you **cannot** send `From: visitor@gmail.com`
(Postmark rejects it and it wrecks deliverability). Responsibilities split cleanly:

- **Form** sets `Reply-To: <visitor email>` so a reply in the inbox goes straight to them.
- **Transport** forces `From:` to the verified domain (e.g. `noreply@jabaltorres.com`)
  via the official plugin's setting, or `wp_mail_from` / `wp_mail_from_name` filters.

### 3.3 Plugin structure (mirror `fivetwofive-cta`)

- Singleton bootstrap + Composer PSR-4 autoload, namespace
  `FiveTwoFive\FiveTwoFive_Contact_Form`.
- `src/Frontend/` — shortcode render + asset enqueue (enqueue only when rendered).
- `src/Rest/` — submission endpoint (the theme already has a REST subsystem in
  `inc/public/rest-api.php` to model after).
- `src/Admin/` — Settings API page; submissions list (CPT admin UI).
- `src/PostType/` — register the submissions CPT.
- `src/Mailer/` — builds and dispatches the `wp_mail()` notification + optional auto-reply.
- `resources/views/` + `resources/assets/` — same layout as the CTA plugin.

### 3.4 Transport options

The form always calls `wp_mail()`, so **the provider is a per-site configuration choice,
not a code change.** Ship with Postmark; let any client repoint it.

- **Postmark, official plugin** — "Postmark (Official)" overrides `wp_mail`, with a
  token/From UI, test-send, and a send log. Simplest when the site will always be Postmark.
- **Postmark, tiny first-party snippet** *(leanest for sites you control)* — a small
  `phpmailer_init` hook configuring SMTP (`smtp.postmarkapp.com:587`, username/password =
  Server API Token). It's generic SMTP under the hood, so the **same snippet serves any
  provider** by changing host/credentials.
- **Multi-provider SMTP plugin** *(best for client-managed sites)* — a free plugin like
  FluentSMTP gives non-technical clients a UI to pick Postmark, SES, Mailgun, SendGrid,
  Google Workspace, or generic SMTP. The form never changes.

> Default to Postmark (your familiarity + strong sending reputation). For a client on a
> different provider, swap the transport only — form, storage, and spam handling are
> untouched. Postmark's dashboard shows all sends + bounces regardless of which Postmark
> transport you pick.

---

## 4. Data model — `ftf_submission` CPT

Private CPT (`public => false`, `show_ui => true`) so submissions are viewable/searchable
in wp-admin but never front-end queryable.

| Field | Storage | Notes |
|---|---|---|
| Name | post meta | sanitized text |
| Email | post meta | validated with `is_email()`, stored as Reply-To |
| Subject | post meta | optional |
| Message | `post_content` or meta | `wp_kses` / `sanitize_textarea_field` |
| Source page | post meta | URL the form was submitted from |
| Submitted at | `post_date` | native |
| Email status | post meta | `sent` / `failed` (from `wp_mail()` return) |
| Read flag | post meta | mark-as-read in admin list |
| IP address | post meta | **optional / privacy-gated** — store hashed or off by default (GDPR) |

Post title auto-generated, e.g. `{Name} — {date}`. Admin list shows custom columns
(name, email, status, date) and a row action to view the full message.

---

## 5. Security & spam model

- **Nonce** on the form + verify on submit (same pattern as `fivetwofive-cta`).
- **Server-side validation + sanitization** of every field; `is_email()` on the address.
- **Capability/escaping** on all admin output (`esc_*`, `wp_kses_post`).
- **Honeypot field** — hidden input that bots fill; reject if non-empty.
- **Time-trap** — embed a render timestamp; reject submissions faster than ~3s.
- **Rate limiting** (optional) — per-IP throttle via transient.
- **Cloudflare Turnstile** (optional, free) — deferred to a later phase if honeypot +
  time-trap prove insufficient. No paid reCAPTCHA.

---

## 6. Configuration & secrets

- **Recipient email** → add a "Contact recipient email" field to the existing
  **Company Options** ACF options page
  (`wp-content/themes/fivetwofive-theme/acf-json/group_68c234162189a.json`), which
  already centralizes business info (phone, city/state). Falls back to `admin_email`.
- **Postmark Server API Token** → read from a **`wp-config.php` constant**
  (e.g. `FTF_POSTMARK_TOKEN`) or env first, with an admin-field fallback. Keep the
  secret out of `wp_options` where practical. The child theme already uses `.env`.
- **From address / name** → transport-level setting (verified Postmark sender).

---

## 7. Placement

- **Shortcode `[fivetwofive_contact_form]`** — canonical renderer (mirrors
  `[fivetwofive_cta]`), usable in any content.
- **Theme module `module-contact-form`** — a thin `template-parts/modules/` template
  that calls the shortcode/render function, so the Contact page (built with
  `page-templates/template-module.php`) gets the form as a normal ACF flexible-content
  block. Logic stays in the plugin; the module is just a wrapper.

---

## 8. Phased delivery

Each phase is one cohesive, shippable PR with per-task commits.

### Phase 1 — MVP (works with zero Postmark setup)
- Plugin scaffold (Composer/PSR-4/singleton, like CTA).
- Shortcode form + styles; REST submit endpoint; no-JS fallback.
- Nonce, validation, sanitization; honeypot + time-trap.
- `ftf_submission` CPT + admin list/columns.
- Admin notification via `wp_mail()` (delivers through whatever mail WP already uses).
- **Outcome:** a working, spam-resistant contact form that captures every lead.

### Phase 2 — Postmark transport + UX
- Wire the default Postmark transport + document provider-switching; From/Reply-To handling.
- Settings page: recipient email (or wire Company Options field), From identity.
- Visitor **auto-reply** ("thanks, I'll be in touch").
- Graceful fallback to default `wp_mail()` when no token is configured.
- **Outcome:** production-grade deliverability + confirmation to the sender.

### Phase 3 — Polish & product-ready
- `module-contact-form` theme module for the page builder.
- Postmark **hosted templates** (edit email design without code) + surface delivery stats.
- Optional Cloudflare Turnstile.
- CSV export of submissions.
- **Outcome:** a reusable, client-ready contact module.

---

## 9. Email provider setup (one-time, before Phase 2)

Postmark is the recommended default; any provider works via the same pattern.

**Postmark (default):**

1. Create a Postmark account / server; note the **Server API Token**.
2. Verify a **Sender Signature** or, better, the **sending domain** (DKIM + Return-Path).
3. Use the **transactional** message stream for notifications + auto-replies.
4. Confirm the free allowance covers expected volume — Postmark's free tier is on the
   order of ~100 emails/month (**verify current limits**), which is typically ample for
   a solo/SMB contact form. Paid tiers are inexpensive when outgrown.
5. Add `FTF_POSTMARK_TOKEN` to `wp-config.php`.

**Other providers:** verify a sending domain with them, then point the transport's SMTP
host/credentials (or their own plugin) at it — the form is unchanged.

---

## 10. Repo housekeeping

- This doc lives in `docs/`, whitelisted in `.gitignore` (`!/docs`) since the root
  ignore rule (`/*`) excludes everything not explicitly allowed.
- When the plugin is created, add it to the same whitelist:
  `!wp-content/plugins/fivetwofive-contact-form`.

---

## 11. Open questions

- Auto-reply copy/branding — plain text vs. Postmark template?
- Store IP at all? If yes, hashed or raw, and with what retention?
- GDPR consent checkbox on the form (likely yes if EU visitors are expected)?
- Should the recipient be configurable per-form (future multi-page use) or global?
