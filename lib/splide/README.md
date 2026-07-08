# Bundled Splide assets

Vendored for standalone carousel layout (`wrapper="full"`) when the active theme does not already provide Splide.

| File | Package | Version | License |
| --- | --- | --- | --- |
| `css/splide.min.css` | [@splidejs/splide](https://www.npmjs.com/package/@splidejs/splide) | 4.1.4 | MIT |
| `js/splide.min.js` | [@splidejs/splide](https://www.npmjs.com/package/@splidejs/splide) | 4.1.4 | MIT |
| `js/splide-extension-auto-scroll.min.js` | [@splidejs/splide-extension-auto-scroll](https://www.npmjs.com/package/@splidejs/splide-extension-auto-scroll) | 0.5.3 | MIT |

> Note on `splide-extension-auto-scroll@0.5.3`: this is the latest published
> release on npm. The upstream repo's last commit was 2022-09-06 — the
> project is effectively unmaintained but still on the registry. WordPress.org's
> "out of date libraries" check is comparing against a development branch that
> has no new tagged release. Bump only when npm publishes a new version.

Refresh from npm devDependencies:

```bash
npm install
cp node_modules/@splidejs/splide/dist/css/splide.min.css lib/splide/css/
cp node_modules/@splidejs/splide/dist/js/splide.min.js lib/splide/js/
cp node_modules/@splidejs/splide-extension-auto-scroll/dist/js/splide-extension-auto-scroll.min.js lib/splide/js/
```
