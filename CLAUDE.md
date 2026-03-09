# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Scout Starter is a plain WordPress theme (no build step, no npm, no frameworks). All styles live in `style.css` and the only JavaScript is `assets/js/navigation.js` for the mobile menu toggle and `assets/js/onboarding.js` for the setup wizard.

## Versioning

Increment patch versions sequentially (`1.0.9` → `1.0.10` → `1.0.11`). Do not roll patch to a new minor or major version automatically — always ask the user before bumping to a new minor (`1.1.0`) or major (`2.0.0`) version. Version must be updated in both `style.css` (`Version:` header) and `functions.php` (`SCOUT_STARTER_VERSION` constant).

## Development

There is no build process. Edit PHP, CSS, and JS files directly. To test changes, the theme must be installed in a WordPress instance at `/wp-content/themes/scout-starter/` (or symlinked there).

WordPress coding standards use tabs for indentation in PHP files. CSS uses 2-space indentation.

## Architecture

### File organization

`functions.php` is a thin bootstrap that defines `SCOUT_STARTER_VERSION` and requires files from `inc/`:
- `inc/setup.php` — theme support, enqueues, widgets, favicon, font preconnect hints
- `inc/customizer.php` — Customizer registration, inline CSS output, color/checkbox helpers
- `inc/activation.php` — `after_switch_theme` hook sets `scout_onboarding_pending`. All setup logic is in `scout_starter_run_activation($config)`, called by the onboarding wizard or skip. Creates default pages (content from `inc/page-content/*.html`), nav menu, footer widgets, imports BSA logo. Persists config to `scout_unit_*` options and stores footer widget IDs in `scout_footer_widget_ids`.
- `inc/onboarding.php` — 3-pane setup wizard (Unit → Meeting → Review). Shown automatically on theme activation. Also contains `scout_starter_render_reset_zone()`, a shared admin-only danger zone used on both the wizard and settings pages.
- `inc/settings.php` — **Appearance > Scout Starter Settings**: edit unit info, meeting details, and integrations (Scoutbook calendar URL) at any time. Save updates footer widgets in-place and home page tagline. Includes Re-apply Setup (force-rebuilds footer) and the shared Danger Zone (full reset).
- `inc/template-tags.php` — reusable output functions for templates

### Default pages and page content

Default pages are created by `scout_starter_run_activation()`. Their starter block content lives in `inc/page-content/<slug>.html`. **When adding or changing default pages:**
1. Add/edit the `.html` file in `inc/page-content/`
2. Add the slug → title entry to the `$default_pages` array in `scout_starter_run_activation()`
3. If the page should appear in the primary nav, make sure its slug is NOT in the `$nav_exclude` array
4. If the page should appear in the footer links widget, update the widget content in the footer-2 block in `scout_starter_run_activation()` and also in `scout_starter_update_footer_widget_content()` in `inc/settings.php`
5. Update the Review pane list in `inc/onboarding.php` (`scout_starter_render_onboarding()`) so users see the new page listed during setup

### Color scheme

All colors are driven by CSS custom properties defined in `:root` in `style.css`. The Customizer exposes five color pickers (primary, accent, nav background, hero background, footer background) registered in `inc/customizer.php` under `scout_starter_customize_register()`. Settings use the `scout_color_` prefix and are sanitized with `sanitize_hex_color`.

`scout_starter_customizer_css()` (hooked to `wp_head`) outputs an inline `<style>` tag that overrides the `:root` custom properties based on saved theme mods. It also calls `scout_starter_darken_color()` to compute `--color-primary-dark` and `--color-accent-dark` for hover states. This inline CSS always loads, so the `:root` values in `style.css` are effectively fallbacks only.

The Scouting America official palette is: Scouting Blue `#003F87`, Scouting Red `#CE1126`, Yellow `#FFCC00`, Brown `#996633`, Light Gray `#eae6e6`.

**Branding:** Always use "Scouting America" — never "Boy Scouts of America" or "BSA" when referring to the organization by name.

Site title and tagline come from WP Settings > General (`bloginfo('name')` / `bloginfo('description')`), not from Customizer settings.

### Latest News section

The homepage recent-posts grid is off by default. Controlled by three Customizer settings in `inc/customizer.php` under the "Latest News Section" panel: `scout_news_enabled` (checkbox, off by default), `scout_news_heading` (text, defaults to "Latest News"), `scout_news_show_dates` (checkbox, off by default). The WP_Query is skipped entirely when the section is disabled. Post dates on cards are toggled in `template-parts/content-card.php`.

### Template hierarchy

- `front-page.php` — Homepage: hero section + optional static page content + latest 3 posts grid
- `index.php` — Blog listing / fallback
- `template-parts/content-card.php` — Post card used in the homepage grid
- `template-parts/content-post.php` — Full single post layout
- `template-parts/content-page.php` — Page layout
- `inc/template-tags.php` — Reusable output functions (`scout_starter_posted_on()`, `scout_starter_posted_by()`, `scout_starter_entry_footer()`)

### Key CSS patterns

CSS custom properties are defined in `:root` in `style.css`. Layout uses `.container` (max-width 1140px, centered) inside semantic section elements. Card grids use CSS Grid with `auto-fill` and a 300px minimum column width.

### Image sizes

Three registered sizes: default post thumbnail (1200×675), `scout-hero` (1920×800), `scout-card` (600×338). All cropped to exact dimensions.
