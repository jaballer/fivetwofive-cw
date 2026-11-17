# CLAUDE.md — Projects post type plugin

Registers personal projects as their own post type, the sibling of Work (`fivetwofive-work-post-type`). Work stays paid work only (employee and consulting). Issue #195.

## What it registers
- **`ftf_project`** post type: label "Projects", rewrite slug and archive `/projects/`, REST base `projects`, block editor on. Can use the Modules Template (`Template Post Type:` in the parent's `page-templates/template-module.php`).
- **`topic`** taxonomy: flat, REST enabled, admin column, rewrite slug `topic`. Attached to `ftf_work`, `ftf_project` and `post` (filter `ftf_project_topic_post_types`). `/topic/{term}/` lists all three types through the parent's `archive.php`.
- Activation seeds Topic terms (AI agents, MCP, Email, WordPress, Laravel, Developer tools) and flushes rewrite rules. Deactivation unregisters both and flushes.

## Fields
ACF group `group_ftf_project_details` in the parent theme's `acf-json/` (the theme forces all ACF JSON there):
`project_status` (idea, building, shipped, archived), `project_repo_url`, `project_demo_url`, `project_writeup_url`. Read through `ftf_project_get_field()`, which falls back to post meta if ACF is off.

## How it shows up (theme hooks, no duplicated layout)
- `fivetwofive_theme_after_post_meta`: Status badge on the shared card (`post-card.php`) and item (`post-item.php`).
- `fivetwofive_theme_after_card_content` (added to `post-card.php`, `post-item.php`, `post-list.php` for #195): Repo, Demo and Write-up links, each only when filled in. The list view also gets the badge here.
- `fivetwofive_theme_enable_sidebar`: `/projects/` and topic archives are full width.
- `fivetwofive_theme_archive_card_taxonomy` (added to the parent `archive.php` for #195): those archive cards show Topics instead of post tags.
- `fivetwofive_modules_template_hide_editor_post_types` (added to the parent's content editor manager for #195): the block editor is hidden when a project uses the Modules Template.
- `[ftf_project_meta]` (optional `id`): badge and links inside a Modules Template page, e.g. in a Code module. A project on the default template gets its links above the content automatically; its badge is already in the post meta header.
- Works module: its `works` relationship field accepts `ftf_work` and `ftf_project`, so the homepage grid can mix them. Project cards show Topics; Work cards show Work Categories.

Do not reuse the old `featured-projects` slug or post type name (see #46).
