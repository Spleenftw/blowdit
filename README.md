# Blowdit

A clean, minimal theme for [Bludit](https://www.bludit.com) inspired by the
[Blowfish](https://blowfish.page/) Hugo theme.

![Bludit](https://img.shields.io/badge/Bludit-3.9+-informational) ![License](https://img.shields.io/badge/license-MIT-green) ![Version](https://img.shields.io/badge/version-1.2.0-blue)

## Features

- 🎨 **Five-theme swatch picker** — click the theme button to open a dropdown with coloured swatches for Light, Dark, Nord, Dracula and Catppuccin. Choice persists via `localStorage` + cookie (no flash on next load); falls back to OS preference.
- 🧱 **Monochrome base design** — restrained grayscale palette with crisp Inter typography.
- 👤 **Profile hero** — circular avatar, site title, slogan and social links on the homepage front page.
- 📖 **Sticky Table of Contents** — articles that contain headings (`h2`/`h3`/`h4`) get a left sidebar ToC that sticks while scrolling and highlights the active section. Layout automatically switches to 3-column with a wider container.
- 📚 **Smart right sidebar** — shows only About / Categories / Hit Counter on the homepage (navigation is redundant there); shows the full plugin set on article pages.
- 🔀 **Randomized navigation** — the Navigation/Pages plugin list is shuffled on each load.
- ⏱️ **Reading time & dates** shown on the post list and single posts.
- 🔗 **Social icons** in the navbar and hero (SVG files in `img/`).
- 🏷️ **Tags & categories** rendered as pill badges.
- 🖼️ **Image lightbox** — clicking any article image opens it full-screen. A dark overlay with a magnifier icon appears on hover to hint at the interaction. Closes on click or Esc.
- 🎠 **Image carousel** — use a ` ```carousel ` fenced block, one image per line (markdown image syntax or plain URL).
- 📑 **Tabbed code blocks** — use a ` ```tabs ` fenced block with `[Tab Name]` or `@tab Name` headers to show content from multiple servers/environments side by side.
- 📜 **Code block scrollbar** — always-visible horizontal scrollbar on `<pre>` blocks with a custom slim style that respects the active theme.
- 📱 Fully responsive; no flash of the wrong theme on load.

## Installation

1. Download or clone this repository into your Bludit `bl-themes` directory:

   ```
   /usr/share/nginx/html/bl-themes/
   ```

2. In the Bludit admin panel go to **Settings → Themes** and activate **Blowdit**.

3. Add your assets to `blowdit/img/`:
   - Your avatar as `spleenftw.jpeg` — used in the homepage hero and the article sidebar compact card.
     To change the filename, update `php/sidebar.php` and `php/home.php`.

## Layout

```
blowdit/
├── index.php          Main template (layout, all inline JS)
├── metadata.json      Theme metadata
├── css/
│   └── style.css      All styles (CSS custom properties, 5 themes)
├── php/
│   ├── head.php       <head>, fonts, favicon, anti-FOUC theme script
│   ├── navbar.php     Navbar, social SVG icons, theme-picker dropdown
│   ├── home.php       Profile hero + post listing
│   ├── page.php       Single page / post
│   ├── sidebar.php    Right sidebar plugin container
│   ├── toc.php        Left ToC sidebar (article pages with headings)
│   └── footer.php     Footer
├── img/               Social SVG icons + avatar
└── languages/         Translations (10 languages)
```

## Themes

Each theme is a set of CSS custom properties. Light lives in `:root`; the others
override it under `[data-theme="dark"]`, `[data-theme="nord"]`,
`[data-theme="dracula"]` and `[data-theme="catppuccin"]`.

The attribute is set on `<html>` before the first paint (in `php/head.php`) to
avoid a flash. The navbar swatch picker updates it at runtime and persists the
choice to both `localStorage` and a cookie.

To add a new theme, add a variable block in `css/style.css` and add the theme
name + swatch colours to the picker in `php/navbar.php` and the `THEMES` array
in `index.php`.

## Table of Contents

When an article's rendered HTML contains any `h2`, `h3` or `h4` headings, the
layout automatically switches from 2-column to 3-column:

```
[ Left ToC (col-md-3) ] [ Article (col-md-6) ] [ Right sidebar (col-md-3) ]
```

The ToC is hidden on mobile. The left column has no `align-self-start` so it
stretches to the full article height, which is what `position: sticky` needs to
function. The container switches to `.container-wide` (1400 px max-width) and
the navbar inner container widens to match via `body.has-toc .navbar .container`.

## Tabbed code blocks

Use a ` ```tabs ` fenced block. Delimit tabs with `[Tab Name]` (brackets) or
`@tab Tab Name` (@ prefix):

````
```tabs
[pgs01]
root@pgs01:~# psql -c "SELECT …"

[pgs02]
root@pgs02:~# psql -c "SELECT …"
```
````

## Image carousel

Use a ` ```carousel ` fenced block. Each non-empty line is one slide. Accepts
standard markdown image syntax or a bare URL with an optional `| caption`:

````
```carousel
![Front panel](/bl-content/uploads/…/server1.png)
![Rear view](/bl-content/uploads/…/server2.png)
```
````

Use `![]()` (empty alt) to show no caption. Supports swipe on touch devices.

## Randomized navigation

If the **Navigation** (or **Pages**) plugin is in the sidebar, its list is
shuffled on every page load. The plugin output is wrapped in `.js-random-nav`;
adjust the detection in `index.php` if your plugin uses a different class.

## License

[MIT](LICENSE)
