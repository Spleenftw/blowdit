# Blowdit

A clean, minimal theme for [Bludit](https://www.bludit.com) inspired by the
[Blowfish](https://blowfish.page/) Hugo theme.

![Bludit](https://img.shields.io/badge/Bludit-3.9+-informational) ![License](https://img.shields.io/badge/license-MIT-green)

## Features

- 🌗 **Light / dark mode toggle** — remembers the visitor's choice and falls back to the OS preference.
- 🧱 **Monochrome design** — restrained grayscale palette with crisp typography (Inter).
- 👤 **Profile hero** — circular avatar, site title, slogan and social links on the homepage.
- 📚 **Right sidebar** — drop any Bludit sidebar plugin into it (categories, tags, search…).
- ⏱️ **Reading time & dates** shown on the post list and single posts.
- 🔗 **Social icons** in the navbar and hero, using SVG files in `img/` (GitHub, Mastodon, Bluesky, LinkedIn, and more).
- 🏷️ **Tags & categories** rendered as pill badges.
- 📱 Fully responsive, no flash of the wrong theme on load.

## Installation

1. Download or clone this repository into your Bludit `bl-themes` directory:

   ```
   bl-themes/blowdit/
   ```

2. In the Bludit admin panel go to **Settings → Themes** and activate **Blowdit**.

3. Add your assets to `blowdit/img/`:
   - The social-network SVG icons (`github.svg`, `mastodon.svg`, …) named after each
     Bludit social-network key.
   - Your avatar as `spleenftw.jpeg` — used for the homepage profile hero **and** the favicon.
     To use a different filename, change it in [php/head.php](php/head.php) (favicon) and
     [php/home.php](php/home.php) (`$profileImage`).

## Layout

```
blowdit/
├── index.php          Main template
├── metadata.json      Theme metadata
├── css/
│   └── style.css      Light & dark styles (CSS custom properties)
├── php/
│   ├── head.php       <head>, fonts, favicon, anti-FOUC theme script
│   ├── navbar.php     Navbar, social SVG icons, theme toggle
│   ├── home.php       Profile hero + post listing
│   ├── page.php       Single page / post
│   ├── sidebar.php    Sidebar plugin container
│   └── footer.php     Footer
├── img/               Social SVG icons + avatar (spleenftw.jpeg)
└── languages/         Translations
```

## Dark mode

The colour scheme is driven by CSS custom properties. Light mode lives in
`:root`; dark mode overrides them under `[data-theme="dark"]`. The attribute is
set on `<html>` before paint (in `php/head.php`) to avoid a flash, and the
toggle button in the navbar persists the choice to `localStorage`.

To tweak the palette, edit the variables at the top of
[css/style.css](css/style.css).

## License

[MIT](LICENSE)
