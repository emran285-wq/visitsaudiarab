# Theme assets

Place theme images and icons here.

## Favicon

The favicon files are generated from `favicon.svg`:

- `favicon.ico` — multi-size ICO containing 16×16, 32×32, and 48×48
- `favicon-16x16.png`
- `favicon-32x32.png`
- `apple-touch-icon.png` — 180×180

To regenerate the favicon after changing the SVG, use a tool such as
[RealFaviconGenerator](https://realfavicongenerator.net/) or ImageMagick:

```bash
magick favicon.svg -define icon:auto-resize=16,32,48 favicon.ico
magick favicon.svg -resize 16x16 favicon-16x16.png
magick favicon.svg -resize 32x32 favicon-32x32.png
magick favicon.svg -resize 180x180 apple-touch-icon.png
```
