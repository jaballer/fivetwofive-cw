#!/usr/bin/env python3
"""Re-copy the landing page from its static HTML source into this theme.

    python3 tools/sync-from-html.py path/to/916-marketing.html

Overwrites:
  assets/css/design-system.css   the source's DESIGN SYSTEM block (palette .. logo)
  assets/css/site.css            the source's BASE + SITE SECTIONS blocks
  template-parts/sections/*.php  one file per <!-- ==== NAME ==== --> <section>

Never touches header.php, footer.php, functions.php, assets/css/wordpress.css,
or assets/js/main.js — port changes to those by hand (then review with git diff).
"""
import re
import sys
from pathlib import Path

THEME = Path(__file__).resolve().parent.parent

# Source comment label -> (template part slug, docblock description)
SECTIONS = {
    'HERO': ('hero', 'hero'),
    'TRUSTED BY': ('trusted-by', 'trusted by'),
    'TENSION / PROBLEM': ('tension', 'problem / tension'),
    'SERVICES': ('services', 'services'),
    'WORK': ('work', 'selected work + stats'),
    'TESTIMONIALS': ('testimonials', 'testimonials'),
    'PROCESS': ('process', 'process'),
    'FAQ': ('faq', 'FAQ accordion (behavior in assets/js/main.js)'),
    'FINAL CTA / CONTACT': ('contact', 'final CTA + lead form.\n *\n'
                            ' * The form has no backend yet — assets/js/main.js confirms receipt in-page.\n'
                            ' * Point it at a real handler before relying on it for leads'),
}

# Hard-coded contact details -> the constants in functions.php
REPLACEMENTS = [
    ('tel:+19165559160', 'tel:<?php echo esc_attr( MARKETING916_PHONE_TEL ); ?>'),
    ('>(916) 555-9160<', '><?php echo esc_html( MARKETING916_PHONE ); ?><'),
    ('mailto:info@916marketing.com', 'mailto:<?php echo esc_attr( MARKETING916_EMAIL ); ?>'),
    ('>info@916marketing.com<', '><?php echo esc_html( MARKETING916_EMAIL ); ?><'),
]

DS_HEADER = """/* =====================================================================
   916 MARKETING — DESIGN SYSTEM
   Copied verbatim from the landing page source (916-marketing.html), which
   copies it from the 916 Marketing style guide. Change values in the guide
   first, then re-sync with tools/sync-from-html.py.
   Components use semantic tokens only; palette steps exist to define them.
   ===================================================================== */

"""


def dedent(lines):
    return '\n'.join(l[2:] if l.startswith('  ') else l for l in lines).strip('\n') + '\n'


def sync_css(src):
    lines = src.split('<style>', 1)[1].split('</style>', 1)[0].split('\n')
    find = lambda pat: next(i for i, l in enumerate(lines) if re.search(pat, l))
    palette = find(r'Palette \(base colors')
    base = find(r'^\s*BASE\s*$') - 1  # the /* ==== rule line above BASE
    (THEME / 'assets/css/design-system.css').write_text(DS_HEADER + dedent(lines[palette:base]))
    (THEME / 'assets/css/site.css').write_text(dedent(lines[base:]))
    print('css: design-system.css, site.css')


def sync_sections(src):
    for label, (slug, desc) in SECTIONS.items():
        m = re.search(r'<!-- =+ ' + re.escape(label) + r' =+ -->\n<section.*?\n</section>\n', src, re.S)
        if not m:
            sys.exit(f'Section "{label}" not found in source — update SECTIONS in {__file__}')
        body = m.group(0)
        for old, new in REPLACEMENTS:
            body = body.replace(old, new)
        head = f"<?php\n/**\n * Section: {desc}.\n *\n * @package 916_Marketing\n */\n\ndefined( 'ABSPATH' ) || exit;\n?>\n"
        (THEME / 'template-parts/sections' / f'{slug}.php').write_text(head + body)
        print(f'section: {slug}.php')


if __name__ == '__main__':
    if len(sys.argv) != 2:
        sys.exit(__doc__)
    source = Path(sys.argv[1]).read_text()
    sync_css(source)
    sync_sections(source)
