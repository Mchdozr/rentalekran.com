import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { createRequire } from 'node:module';
import { fileURLToPath, pathToFileURL } from 'node:url';

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
assert.doesNotMatch(page, /Önceki sayfadan taşınan|Önceki ürün sayfasındaki görseller, teknik tablolar/);
assert.match(pluginMain, /includes\/blog\.php/);
assert.match(pluginMain, /single-post\.php/);
assert.match(pluginMain, /rle_shell_active/);
assert.ok(fs.existsSync(path.join(plugin, 'templates/single-post.php')));
assert.ok(fs.existsSync(path.join(plugin, 'includes/layout.php')));
assert.ok(fs.existsSync(path.join(plugin, 'includes/blog-posts.json')));
assert.doesNotMatch(page, /Road Freight|ArcHub|BIGLOAD/);
assert.match(read('includes/layout.php'), /nav-badge/);

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

const keywords = JSON.parse(read('includes/keywords.json'));
assert.ok(keywords.pages['']);
assert.ok(keywords.pages['led-ekran-kiralama']);
assert.ok(keywords.pages['led-ekran-satisi']);
assert.match(keywords.pages[''].title, /^LED Ekran Satış ve Kiralama/);
assert.match(keywords.pages['led-ekran-kiralama'].title, /^LED Ekran Kiralama/);
assert.ok(Array.isArray(keywords.queue) && keywords.queue.length > 0);
assert.match(read('includes/layout.php'), /nav-label">Anasayfa/);
assert.match(catalog, /led-ekran-satisi/);
assert.match(read('includes/layout.php'), /led-ekran-satisi/);
assert.match(seo, /FAQPage/);
assert.match(seo, /LocalBusiness/);
assert.ok(fs.existsSync(path.join(root, 'scripts/seo-cycle.mjs')));
assert.ok(fs.existsSync(path.join(root, '.github/workflows/seo.yml')));

const posts = JSON.parse(read('includes/blog-posts.json'));
assert.ok(posts.some((p) => p.slug === 'sahne-led-ekran-kiralama'));
const { nextQueueItem, bumpSync, renderQueuedPost, replenishQueue, briefFromSeed, runCycle } = await import(pathToFileURL(path.join(root, 'scripts/seo-cycle.mjs')).href);
assert.equal(bumpSync('2.5.0'), '2.5.1');
const nxt = nextQueueItem(keywords, posts);
assert.ok(nxt && nxt.slug);
assert.match(renderQueuedPost(nxt), /blog-section/);
const topics = JSON.parse(fs.readFileSync(path.join(root, 'scripts/seo-topics.json'), 'utf8'));
assert.ok(topics.length >= 10);
const filled = replenishQueue({ queue: [] }, posts, topics, 3);
assert.equal(filled.queue.length, 3);
assert.ok(filled.queue.every((item) => item.slug && item.sections));
const unused = topics.find((t) => !posts.some((p) => p.slug === t.slug));
assert.ok(unused);
const cycle = runCycle({
  keywords: { blogSync: '2.5.0', queue: [briefFromSeed(unused, '2026-10-13')] },
  posts: posts.map((p) => ({ slug: p.slug })),
  topics,
  flushQueue: false,
});
assert.equal(cycle.published[0], unused.slug);
assert.ok(cycle.keywords.queue.length >= 3);

console.log('audit tests passed');
