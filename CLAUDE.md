# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Scout Starter is a plain WordPress theme (no build step, no npm, no frameworks). All styles live in `style.css` and the only JavaScript is `assets/js/navigation.js` for the mobile menu toggle.

## Development

There is no build process. Edit PHP, CSS, and JS files directly. To test changes, the theme must be installed in a WordPress instance at `/wp-content/themes/scout-starter/` (or symlinked there).

WordPress coding standards use tabs for indentation in PHP files. CSS uses 2-space indentation.

## Architecture

### Color scheme

All colors are driven by CSS custom properties defined in `:root` in `style.css`. The Customizer exposes five color pickers (primary, accent, nav background, hero background, footer background) registered in `functions.php` under `scout_starter_customize_register()`. Settings use the `scout_color_` prefix and are sanitized with `sanitize_hex_color`.

`scout_starter_customizer_css()` (hooked to `wp_head`) outputs an inline `<style>` tag that overrides the `:root` custom properties based on saved theme mods. It also calls `scout_starter_darken_color()` to compute `--color-primary-dark` and `--color-accent-dark` for hover states. This inline CSS always loads, so the `:root` values in `style.css` are effectively fallbacks only.

The BSA official palette is: Scouting Blue `#003F87`, Scouting Red `#CE1126`, Yellow `#FFCC00`, Brown `#996633`, Light Gray `#eae6e6`.

Site title and tagline come from WP Settings > General (`bloginfo('name')` / `bloginfo('description')`), not from Customizer settings.

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
