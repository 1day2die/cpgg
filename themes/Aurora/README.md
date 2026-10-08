# Aurora

A modern light/dark theme for CtrlPanel. It keeps the markup, layout and behaviour of the
`default` theme and restyles it through one token system built on Tailwind CSS v4 and daisyUI v5.

## How it works

- `views/layouts/{main,app,errors}.blade.php` override the parent layouts. `main` and `app` are
  copies of the default layouts with small, marked changes, so they can be re-synced easily.
  Every other view falls back to `themes/default`.
- `views/aurora/head.blade.php` resolves the color mode before first paint and loads the assets.
- `css/app.css` bundles the parent theme's Bootstrap/AdminLTE/plugin CSS into the `legacy`
  cascade layer and appends the bridge (`css/bridge/*.css`), which maps that markup onto tokens.
- Tailwind utilities and daisyUI components are available in Aurora views with the `tw:` prefix
  (e.g. `tw:btn tw:btn-primary`), so they never collide with Bootstrap class names.

## Tokens

| Where | What |
| --- | --- |
| `css/app.css` (`daisyui/theme`) | `aurora-dark`, `aurora-light`: base, primary, secondary, accent, neutral, status colors, radii |
| `css/tokens.css` | `--aurora-*`: typography, surfaces, text tones, soft tints, focus rings, shadows, motion, sidebar, tables |

Changing a daisyUI theme color updates every component in both the bridge and `tw:` markup.

## Color mode

Users switch between Light, Dark and System in the header. The choice is stored in
`localStorage` (`aurora-theme`). The default for new visitors is `dark`; to change it, add
`'aurora' => ['default_mode' => 'system']` (or `light`) to `config/theme.php`.

## Build

Compiled assets ship in `public/themes/Aurora`, so a build is only needed after changing sources
(Node.js 20+):

```bash
cd themes/Aurora
npm ci
npm run build
```

This copies the self-hosted Inter font and `aurora.js` and writes `public/themes/Aurora/app.css`.
Use `npm run dev` to rebuild on change.
