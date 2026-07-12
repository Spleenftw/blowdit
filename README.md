# Blowfish

A clean, minimal theme for [Bludit](https://www.bludit.com) inspired by the
[Blowfish](https://blowfish.page/) Hugo theme.

![Bludit](https://img.shields.io/badge/Bludit-3.22+-informational) ![License](https://img.shields.io/badge/license-MIT-green) ![Version](https://img.shields.io/badge/version-1.7.0-blue)

## Features

- **Five-theme swatch picker** — Light, Dark, Nord, Dracula, Catppuccin. Choice persists via `localStorage` + cookie (no flash on next load); falls back to OS preference.
- **Monochrome base design** — restrained grayscale palette with crisp Inter typography (self-hosted, no Google Fonts).
- **Profile hero** — circular avatar, site title, slogan and social links on the homepage.
- **Sticky Table of Contents** — articles with `h2`/`h3`/`h4` headings get a left sidebar ToC that sticks while scrolling and highlights the active section. Layout switches to 3-column with a wider container. A floating button opens a drawer on mobile.
- **Smart right sidebar** — shows only About / Categories / Hit Counter on the homepage; full plugin set on article pages.
- **Client-side search overlay** — `/` key or navbar button; searches titles, descriptions and tags client-side.
- **Keyboard shortcuts** — `/` (search), `g h` (go home), `t` (back to top), `Esc` (close overlay).
- **Randomized navigation** — the Navigation/Pages plugin list is shuffled on every load.
- **Reading time & dates** shown on the post list and single posts.
- **Post-list excerpts & thumbnails** — the page description renders as a 2-line excerpt with the cover image as a right-side thumbnail; tags are capped at 5 with a `+N` badge.
- **Older / newer post navigation** — chronological links under every article, above the related posts.
- **Social icons** in the navbar and hero (SVG files in `img/`).
- **Tags & categories** rendered as pill badges.
- **Image lightbox** — clicking any article image opens it full-screen with a dark overlay and zoom hint.
- **Image carousel** — ` ```carousel ` fenced block, one image per line (markdown syntax or bare URL with optional `| caption`).
- **Tabbed code blocks** — ` ```tabs ` fenced block with `[Tab Name]` or `@tab Name` headers.
- **Code block header bar** — language label + copy-to-clipboard button on every code block.
- **Callout/admonition boxes** — `> [!NOTE]`, `> [!TIP]`, `> [!WARNING]`, `> [!DANGER]`, `> [!IMPORTANT]`.
- **Series navigation** — posts tagged `series-*` display an ordered series box.
- **SEO** — Open Graph, Twitter Card, JSON-LD (WebSite, Article, BreadcrumbList, sameAs), canonical URL, theme-color meta, cover-image preload. Duplicate OG/Twitter/canonical tags emitted by Bludit plugins are stripped automatically.
- **Accessibility** — skip link, ARIA labels, `:focus-visible`, heading anchor links, print stylesheet.
- **PWA** — web app manifest included.
- **CLS-free images** — `width`/`height` are injected server-side into local-upload images (plus lazy-loading and async decode).
- **View transitions** — smooth cross-document fade on supporting browsers; respects `prefers-reduced-motion`.
- Fully responsive; no jQuery, no Bootstrap JS, no webfont requests.

## Installation

1. Clone or copy this repository into your Bludit `bl-themes/` directory:

   ```
   bl-themes/bludit-blowfish/
   ```

2. In the Bludit admin panel go to **Settings → Themes** and activate **Blowfish**.

3. Add your assets to `img/`:
   - Your avatar as `spleenftw.jpeg` — used in the homepage hero and the article sidebar card.  
     To change the filename, update `php/sidebar.php` and `php/home.php`.

## Layout

```
bludit-blowfish/
├── index.php          Main template (layout, sidebar logic, JS config)
├── metadata.json      Theme metadata
├── manifest.webmanifest
├── css/
│   └── style.css      All styles (CSS custom properties, 5 themes)
├── js/
│   └── blowfish.js    Deferred JS bundle (ToC, lightbox, carousel, search, …)
├── php/
│   ├── head.php       <head>, fonts, favicon, anti-FOUC theme script
│   ├── navbar.php     Navbar, social icons, theme-picker dropdown
│   ├── home.php       Profile hero + post listing
│   ├── page.php       Single page/post
│   ├── sidebar.php    Right sidebar plugin container
│   ├── toc.php        Left ToC sidebar (article pages with headings)
│   ├── footer.php     Footer + search-index JSON
│   └── icons.php      Inline-SVG icon helper + shared PHP functions
├── img/               Social SVG icons + avatar
└── languages/         Translations (10 languages)
```

## Themes

Each colour theme is a block of CSS custom properties. Light lives in `:root`; the others override it under `[data-theme="dark"]`, `[data-theme="nord"]`, `[data-theme="dracula"]` and `[data-theme="catppuccin"]`.

The `data-theme` attribute is set on `<html>` before the first paint (in `php/head.php`) to avoid a flash. The navbar swatch picker updates it at runtime and persists the choice to `localStorage` and a cookie.

To add a new theme: add a variable block in `css/style.css`, register the theme name + background colour in `index.php` (`$blowfishThemeBg`), and add a swatch entry in `php/navbar.php`.

## Table of Contents

When an article's rendered HTML contains any `h2`, `h3` or `h4` headings, the layout automatically switches from 2-column to 3-column:

```
[ Left ToC (col-md-3) ] [ Article (col-md-6) ] [ Right sidebar (col-md-3) ]
```

The sidebar ToC is hidden on mobile; a floating button opens a slide-up drawer instead. The container switches to `.container-wide` (1400 px max-width) and the navbar widens to match via `body.has-toc .navbar .container`.

## Tabbed code blocks

Use a ` ```tabs ` fenced block. Delimit tabs with `[Tab Name]` (brackets) or `@tab Tab Name` (@ prefix):

````
```tabs
[pgs01]
root@pgs01:~# psql -c "SELECT …"

[pgs02]
root@pgs02:~# psql -c "SELECT …"
```
````

## Image carousel

Use a ` ```carousel ` fenced block. Each non-empty line is one slide. Accepts standard markdown image syntax or a bare URL with an optional `| caption`:

````
```carousel
![Front panel](/bl-content/uploads/…/server1.png)
![Rear view](/bl-content/uploads/…/server2.png)
```
````

Supports swipe on touch devices.

## Callout boxes

```markdown
> [!NOTE]
> Useful information.

> [!WARNING]
> Something to watch out for.
```

Supported types: `NOTE`, `TIP`, `WARNING`, `DANGER`, `IMPORTANT`.

## License

[MIT](LICENSE)
