import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const plugin = path.join(root, 'rentalekran-growth');
const read = (rel) => fs.readFileSync(path.join(plugin, rel), 'utf8');

const page = read('templates/page.php');
const catalog = read('includes/catalog.php');
const seo = read('includes/seo.php');
const admin = read('includes/admin.php');
const pluginMain = read('rentalekran-growth.php');
const planner = read('assets/planner.js');
const products = JSON.parse(read('includes/products.json'));
const legacy = JSON.parse(read('includes/legacy-content.json'));

assert.match(page, /\$route==='led-ekran-teklif'\s*\|\|\s*\$route==='iletisim'/);
assert.match(page, /wp_nonce_field\('rle_contact'/);
assert.match(page, /name="rle_contact"/);
assert.doesNotMatch(page, /Road Freight|ArcHub|BIGLOAD/);

assert.match(catalog, /transparan-led-ekran/);
assert.doesNotMatch(catalog, /elementor-3277'\s*;\s*\$slug/);
assert.match(read('includes/contact.php'), /ornek-sayfa/);
assert.match(read('includes/contact.php'), /elementor-3277/);
assert.match(pluginMain, /includes\/contact\.php/);

assert.match(seo, /liquid-/);
assert.match(seo, /metform-form/);
assert.match(admin, /ga_id/);
assert.match(read('includes/contact.php'), /wp_mail/);

const transparent = products.find((p) => p.slug === 'transparan-led-ekran');
assert.ok(transparent);
assert.ok(transparent.image);
assert.ok(fs.existsSync(path.join(plugin, transparent.image)));

for (const key of ['services', 'partners', 'shipment', 'case-studies', 'hakkimizda']) {
  const blob = JSON.stringify(legacy[key] || {});
  assert.doesNotMatch(blob, /Road Freight|Air Freight|ArcHub|BIGLOAD|Four Bear|Urban design/);
  assert.doesNotMatch(blob, /Firmamız Ledajans/i);
}

assert.match(planner, /\[data-rle-slider\], \.legacy-image-grid/);
assert.doesNotMatch(planner, /querySelectorAll\('\.footer-gallery-grid/);

const require = createRequire(import.meta.url);
const { calculate } = require(path.join(plugin, 'assets/planner.js'));
assert.deepEqual(calculate(4, 3, 0.5, 0.5), { columns: 8, rows: 6, count: 48, width: 4, height: 3, area: 12 });
assert.throws(() => calculate(0, 3, 0.5, 0.5));

const logo = path.join(plugin, 'assets/rental-ekran-logo.png');
assert.ok(fs.existsSync(logo), 'plugin logo asset missing');

console.log('audit tests passed');
