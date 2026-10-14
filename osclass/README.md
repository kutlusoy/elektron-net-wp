# Elektron Net: `osclass-elektron` Osclass Theme

- **Version:** 1.0.0 (draft)
- **Date:** October 14, 2026
- **Audience:** Site owners and developers who run an Osclass classifieds site in the Elektron look
- **Reference implementation:** [`elektron-net-wp`](https://github.com/kutlusoy/elektron-net-wp) (`themes/elektron/`), the WordPress theme whose color themes and effects this theme mirrors
- **Fork base:** [`Osclass`](https://github.com/osclass-classifieds/Osclass) Sigma theme structure, Apache License 2.0
- **Consumer:** [`elektron-net-osclass`](https://github.com/kutlusoy/elektron-net-osclass), the repository of the escrow plugin this theme styles
- **See also:** [`README.md`](../README.md)

---

## 1. Contents

| Path | What it is |
|---|---|
| `osclass-elektron/` | The theme. The folder name inside `oc-content/themes/` MUST stay `osclass-elektron`. |
| `../screenshots/osclass-elektron/` | Preview images taken from a real Osclass 8.4 installation. They are not part of the theme package. |

## 2. Installation

1. Pack `osclass/osclass-elektron` as a zip file. The folder name inside the zip MUST stay `osclass-elektron`.
2. Upload it under Appearance > Themes > Add new theme in the Osclass admin, or copy the folder to `oc-content/themes/`.
3. Activate **Osclass Elektron**. The first activation stores the default style options.
4. Optional: upload a logo under Appearance > Header logo. Without an upload the theme shows the Elektron mark and the site title.

Requirements: Osclass 8.x, PHP 7.4 or newer.

## 3. Color themes, variations and effects

The theme ships with the same seven color themes as the WordPress theme, each in a dark and a light variant.

| Color theme | Idea |
|---|---|
| Elektron Blue | The blue of the logo, the default |
| Neon Grid | Cyan and magenta on near black |
| Synthwave | Hot pink, violet and amber |
| Matrix | Terminal green on black |
| Rust | Rusty orange and brass |
| Iron | Steel and graphite with hazard amber |
| Violet Night | Soft violet and pink |

| Variation | Options |
|---|---|
| Color scheme | Dark, Light, Follow system |
| Effects | Off, Subtle (glow, HUD brackets, accent lines, faint grid), Full cyberpunk (scanlines, glitch headline, cut corners, Orbitron and Share Tech Mono) |
| Home hero | Animated network, Perspective grid, Minimal |
| Corner style | Rounded, Sharp |
| Header | Glass and sticky, Solid and static |

Site owners pick the defaults under Appearance > Theme settings. Visitors can switch the color theme, the effects and the light or dark scheme in the header. The choice is remembered in their browser. All animation stops for visitors who prefer reduced motion.

### Previews

![Elektron Blue](../screenshots/osclass-elektron/home-blue.jpg)

![Neon Grid with full effects](../screenshots/osclass-elektron/home-neon-full.jpg)

![Rust, light variant](../screenshots/osclass-elektron/home-rust-light.jpg)

![Synthwave with sharp corners, search page](../screenshots/osclass-elektron/search-synthwave-sharp.jpg)

![Listing page](../screenshots/osclass-elektron/item.jpg)

## 4. Photo frames

Every listing photo sits in a framed mat: a padded surface with a thin inner ring and a soft shadow. Photos with a white background keep a clean, defined edge on dark and light schemes and in every color theme. The same frame is used for the main photo and the thumbnails on the listing page. Listings without a photo show a neutral placeholder.

![Framed photos on the dark scheme](../screenshots/osclass-elektron/photo-frame-grid-dark.jpg)

![Framed photos on the light scheme](../screenshots/osclass-elektron/photo-frame-grid-light.jpg)

## 5. Elektron Net Escrow plugin

The theme styles the pages of [`osclass-escrow`](https://github.com/kutlusoy/elektron-net-osclass/tree/main/osclass-escrow) (wallet connection, checkout, order status, order list) with `css/escrow.css`. The stylesheet loads only while the plugin is active and needs no change in the plugin. The plugin's inline styles use fixed light colors, so the theme overrides them with its own tokens. They follow the active color theme, scheme and corner style. The "Buy with Elektron (ELEK)" widget appears on the listing page, and the account menu shows the orders and wallet links with icons.

![Buy with Elektron widget](../screenshots/osclass-elektron/escrow-widget.jpg)

![Checkout](../screenshots/osclass-elektron/escrow-checkout.jpg)

![Connect wallet](../screenshots/osclass-elektron/escrow-wallet.jpg)

![Order page, Elektron Blue dark](../screenshots/osclass-elektron/escrow-order.jpg)

![Order page, Neon Grid light](../screenshots/osclass-elektron/escrow-order-neon-light.jpg)

## 6. Theme settings

Appearance > Theme settings contains the style options above, the hero title and text, the search placeholder, the default listing view (list or gallery), the location input mode and the ad slots. All Osclass hooks of the Sigma theme are kept, so existing plugins keep working.

## 7. Notes

- Category icons use Font Awesome 5 classes, loaded from cdnjs as in the Sigma theme. The header controls use inline SVG and work without it.
- Fonts for the full effect level are bundled in `fonts/` (SIL Open Font License).
- The theme is derived from the Sigma theme of Osclass and keeps the Apache License 2.0, see `osclass-elektron/LICENSE`.
