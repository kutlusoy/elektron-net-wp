# Elektron Net: `osclass-elektron` Osclass Theme

- **Version:** 1.0.0 (draft)
- **Date:** October 14, 2026
- **Audience:** Site owners and developers who run an Osclass classifieds site in the Elektron look
- **Reference implementation:** [`elektron-net-wp`](https://github.com/kutlusoy/elektron-net-wp) (`themes/elektron/`), the WordPress theme whose color themes and effects this theme mirrors
- **Fork base:** [`Osclass`](https://github.com/osclass-classifieds/Osclass) Sigma theme structure, Apache License 2.0
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

## 4. Theme settings

Appearance > Theme settings contains the style options above, the hero title and text, the search placeholder, the default listing view (list or gallery), the location input mode and the ad slots. All Osclass hooks of the Sigma theme are kept, so existing plugins keep working.

## 5. Notes

- Category icons use Font Awesome 5 classes, loaded from cdnjs as in the Sigma theme. The header controls use inline SVG and work without it.
- Fonts for the full effect level are bundled in `fonts/` (SIL Open Font License).
- The theme is derived from the Sigma theme of Osclass and keeps the Apache License 2.0, see `osclass-elektron/LICENSE`.
