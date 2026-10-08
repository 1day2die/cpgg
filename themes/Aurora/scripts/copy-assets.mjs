// Copies static theme assets (self-hosted Inter, color mode script) to public/themes/Aurora.
import { copyFileSync, mkdirSync } from "node:fs";
import { dirname, join } from "node:path";
import { createRequire } from "node:module";

const require = createRequire(import.meta.url);
const theme = new URL("../", import.meta.url);
const target = new URL("../../../public/themes/Aurora/", import.meta.url);
const fonts = join(dirname(require.resolve("@fontsource-variable/inter/package.json")), "files");

mkdirSync(new URL("fonts/", target), { recursive: true });

for (const subset of ["latin", "latin-ext", "cyrillic", "cyrillic-ext"]) {
    const file = `inter-${subset}-wght-normal.woff2`;
    copyFileSync(join(fonts, file), new URL(`fonts/${file}`, target));
}

copyFileSync(join(fonts, "..", "LICENSE"), new URL("fonts/LICENSE-Inter.txt", target));
copyFileSync(new URL("js/aurora.js", theme), new URL("aurora.js", target));
