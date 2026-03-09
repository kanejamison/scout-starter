# Scout Starter

![Scout Starter theme preview](screenshot.png)

A minimal WordPress starter theme for Scouting America units — Cub Scout Packs, Scouts BSA Troops, Venturing Crews, Sea Scout Ships, and Exploring Posts.

On activation the theme launches a setup wizard that creates core pages, wires up navigation, and pre-populates footer widgets using your unit's details. Built for unit leaders who need a clean, functional site with minimal configuration.

## Features

- **Guided setup wizard** — enter unit details once; pages, nav, and footer widgets are built automatically
- **Scout Starter settings page** — top-level admin menu to update unit info, meeting details, and integrations at any time
- **Unit calendar** — connect a public iCal feed (Scoutbook or Google Calendar); displays via FullCalendar with month grid and agenda views
- **Per-page hero section** — full-width hero with featured image background (or color fallback) and optional subtitle
- **Custom color pickers** — set primary, accent, nav, hero, and footer colors via Customizer
- **Scouting America-aligned defaults** — Scouting Blue `#003F87` + Yellow `#FFCC00`
- **Latest News section** — optional recent posts grid on the homepage (off by default)
- **Starter page content** — homepage, about, events, join us, contact, website policies, privacy policy
- **Pre-populated footer** — unit branding, quick links, and meeting address widgets built on setup
- **Responsive** — mobile navigation toggle, fluid layout
- **No build step** — vanilla PHP, CSS, and JS; no npm, no webpack, no frameworks

## Setup

1. Upload theme folder to `/wp-content/themes/` and activate
2. The **setup wizard** launches automatically — enter your unit type, number, city, and meeting details
3. Pages, primary nav menu, and footer widgets are created from your inputs
4. Go to **Scout Starter > General** at any time to update unit or meeting info
5. Go to **Appearance > Customize > Scout Colors** to set your unit's colors
6. Upload your unit logo via **Appearance > Customize > Site Identity**

## Scout Starter Settings

A top-level **Scout Starter** admin menu provides three tabs:

- **General** — unit type, unit number, city/location, and meeting details. Saving updates the footer widgets and home page tagline immediately.
- **Calendar** — iCal feed URL for the unit calendar (see Calendar section below)
- **Admin** — Re-apply Setup (rebuilds footer widgets from saved settings) and a full site reset (Danger Zone)

## Calendar

Connect any public iCal feed — Scoutbook or a public Google Calendar — under **Scout Starter > Calendar**.

> **Privacy note:** Your website calendar is public. Do not include youth names, home addresses, or private meeting locations in events you display here. Consider a separate public Google Calendar for website use if your Scoutbook events contain sensitive details.

Once a URL is saved the calendar appears automatically on the Events page.

### Block

Insert a **Scout Calendar** block via the block editor. The inspector panel offers:

- **View** — Month Grid or Upcoming Events List
- **Number of events** — (list view only) how many upcoming events to show, 1–100

### Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[scout_calendar]` | Month grid view |
| `[scout_agenda]` | Next 12 upcoming events as a list |
| `[scout_agenda events="5"]` | Upcoming events list, custom count (1–100) |

The agenda view always starts from today and shows events within the next 12 months. Both views load nothing if no calendar URL is configured, so they are safe to leave in page content.

## Hero Section

Enable a full-width hero on any page via the **Hero Section** panel in the page editor sidebar. Set a **Featured Image** on the page to use it as the hero background, or it falls back to your configured hero color. Add a **Page Excerpt** to show it as a subtitle beneath the page title.

The home page has the hero enabled by default.

## Customizer Settings

**Scout Colors** — primary, accent, navigation background, hero background, footer background

**Latest News** — enable/disable a recent posts grid on the homepage, with optional custom heading and date display

## Deployment

Includes two GitHub Actions workflows:

- **Deploy on push** — rsync to your server via SSH on every push to `main`
- **Release zip** — builds a versioned theme zip and creates a GitHub Release when `Version:` in `style.css` is bumped

## License

GPL v2 or later.
