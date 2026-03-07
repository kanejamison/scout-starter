# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Scout Starter is a plain WordPress theme (no build step, no npm, no frameworks). All styles live in `style.css` and the only JavaScript is `assets/js/navigation.js` for the mobile menu toggle.

## Development

There is no build process. Edit PHP, CSS, and JS files directly. To test changes, the theme must be installed in a WordPress instance at `/wp-content/themes/scout-starter/` (or symlinked there).

WordPress coding standards use tabs for indentation in PHP files. CSS uses 2-space indentation.

## Architecture

### File organization

`functions.php` is a thin bootstrap that defines `SCOUT_STARTER_VERSION` and requires four files from `inc/`:
- `inc/setup.php` — theme support, enqueues, widgets, favicon, font preconnect hints
- `inc/customizer.php` — Customizer registration, inline CSS output, color/checkbox helpers
- `inc/activation.php` — `after_switch_theme` hook: creates default pages, sets static front page, builds primary nav menu. Page content is loaded from `inc/page-content/*.html` via `file_get_contents()` — edit those HTML files to change starter copy without touching PHP
- `inc/template-tags.php` — reusable output functions for templates

### Color scheme

All colors are driven by CSS custom properties defined in `:root` in `style.css`. The Customizer exposes five color pickers (primary, accent, nav background, hero background, footer background) registered in `inc/customizer.php` under `scout_starter_customize_register()`. Settings use the `scout_color_` prefix and are sanitized with `sanitize_hex_color`.

`scout_starter_customizer_css()` (hooked to `wp_head`) outputs an inline `<style>` tag that overrides the `:root` custom properties based on saved theme mods. It also calls `scout_starter_darken_color()` to compute `--color-primary-dark` and `--color-accent-dark` for hover states. This inline CSS always loads, so the `:root` values in `style.css` are effectively fallbacks only.

The Scouting America official palette is: Scouting Blue `#003F87`, Scouting Red `#CE1126`, Yellow `#FFCC00`, Brown `#996633`, Light Gray `#eae6e6`.

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
