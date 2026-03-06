# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Scout Starter is a plain WordPress theme (no build step, no npm, no frameworks). All styles live in `style.css` and the only JavaScript is `assets/js/navigation.js` for the mobile menu toggle.

## Development

There is no build process. Edit PHP, CSS, and JS files directly. To test changes, the theme must be installed in a WordPress instance at `/wp-content/themes/scout-starter/` (or symlinked there).

WordPress coding standards use tabs for indentation in PHP files. CSS uses 2-space indentation.

## Architecture

### Color scheme switching

The Pack/Troop toggle works entirely via CSS custom properties and a body class. `functions.php:151` adds `scout-type-pack` or `scout-type-troop` to `<body>`. `style.css:56` overrides `--color-accent` for `body.scout-type-troop`. Any new accent-colored element should use `var(--color-accent)` to automatically respond to this toggle.

### Customizer settings

All theme options are registered in `functions.php` under `scout_starter_customize_register()`. Settings use the `scout_` prefix. Helper functions (`scout_starter_unit_name()`, `scout_starter_unit_subtitle()`, `scout_starter_unit_type_label()`) wrap `get_theme_mod()` calls for use in templates.

### Template hierarchy

- `front-page.php` — Homepage: hero section + optional static page content + latest 3 posts grid
- `index.php` — Blog listing / fallback
- `template-parts/content-card.php` — Post card used in the homepage grid
- `template-parts/content-post.php` — Full single post layout
- `template-parts/content-page.php` — Page layout
- `inc/template-tags.php` — Reusable output functions (`scout_starter_posted_on()`, `scout_starter_posted_by()`, `scout_starter_entry_footer()`, `scout_starter_social_links()`)

### Key CSS patterns

CSS custom properties are defined in `:root` in `style.css`. Layout uses `.container` (max-width 1140px, centered) inside semantic section elements. Card grids use CSS Grid with `auto-fill` and a 300px minimum column width.

### Image sizes

Three registered sizes: default post thumbnail (1200×675), `scout-hero` (1920×800), `scout-card` (600×338). All cropped to exact dimensions.
