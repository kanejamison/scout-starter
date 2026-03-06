# Scout Starter

A minimal WordPress theme for BSA Scout units. Single theme handles both Cub Scout Packs and Scout Troops via a Customizer toggle that swaps color schemes.

Built for distribution to unit leaders who need a clean, functional site with zero configuration complexity.

## Features

- **Pack/Troop toggle** — Customizer dropdown switches between Cub Scout (navy + gold) and Scout Troop (navy + red) color schemes via CSS custom properties
- **Homepage hero** — Full-width hero section with background image, tagline, and CTA button
- **Recent posts grid** — Latest 3 posts displayed as cards on the homepage
- **Responsive** — Mobile navigation toggle, fluid layout
- **No dependencies** — Vanilla CSS, no build step, no frameworks
- **Widget areas** — 3 footer columns + optional sidebar
- **Social links** — Facebook, Instagram, YouTube via Customizer

## File Structure

```
scout-starter/
├── style.css                  # Theme metadata + all styles (CSS custom properties)
├── functions.php              # Theme setup, Customizer, widgets, enqueues
├── header.php
├── footer.php
├── front-page.php             # Homepage: hero + page content + recent posts
├── index.php                  # Blog listing / fallback
├── page.php
├── single.php
├── archive.php
├── search.php
├── searchform.php
├── 404.php
├── sidebar.php
├── inc/
│   └── template-tags.php      # Posted date, author, entry footer, social links
├── template-parts/
│   ├── content-card.php       # Post card for grids
│   ├── content-post.php       # Single post layout
│   ├── content-page.php       # Page layout
│   └── content-none.php       # No results fallback
├── assets/
│   └── js/navigation.js       # Mobile menu toggle
└── readme.txt                 # WordPress.org formatted readme
```

## Customizer Settings

**Scout Unit Settings**
- Unit Type: Cub Scout Pack or Scout Troop
- Unit Number
- Location (city, state)
- Age/Grade Range

**Homepage Hero**
- Background image
- Tagline text
- CTA button text and URL

**Social Links**
- Facebook, Instagram, YouTube URLs

## Color Schemes

| Element | Pack (Cub Scouts) | Troop |
|---|---|---|
| Primary | `#003f87` (navy) | `#003f87` (navy) |
| Accent | `#fdc116` (gold) | `#ce1126` (red) |

The toggle adds `scout-type-pack` or `scout-type-troop` to `<body>`. The `body.scout-type-troop` selector overrides the `--color-accent` CSS custom property. All accent-colored elements (CTA buttons, section dividers, widget borders, social hovers) switch automatically.

## Setup

1. Upload theme folder to `/wp-content/themes/`
2. Activate via Appearance > Themes
3. Set a Static Front Page under Settings > Reading
4. Configure unit details in Appearance > Customize > Scout Unit Settings
5. Upload logo via Site Identity
6. Create pages (About, Events, Contact, Join Us) and assign to Primary Menu
7. Add widgets to footer areas (contact info, meeting schedule, quick links)

## Deployment

TODO: GitHub Actions workflow for auto-deploy to production server.

## License

GPL v2 or later.
