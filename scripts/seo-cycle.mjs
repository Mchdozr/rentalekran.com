/**
 * Publish queue items and replenish with new SEO briefs.
 * Usage: node scripts/seo-cycle.mjs [--dry-run] [--flush]
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const pluginRoot = path.join(root, 'rentalekran-growth');
const dry = process.argv.includes('--dry-run');
const flush = process.argv.includes('--flush');
const QUEUE_MIN = 6;

function readJson(abs) {
  return JSON.parse(fs.readFileSync(abs, 'utf8'));
}

function assetUrl(rel) {
  return 'https://rentalekran.com/wp-content/plugins/rentalekran-growth/' + String(rel).replace(/^\//, '');
}

export function renderQueuedPost(item) {
  const sections = (item.sections || []).map((s) => {
    const paras = (s.p || []).map((p) => `<p>${p}</p>`).join('');
    return `<div class="blog-section"><h2>${s.h2}</h2>${paras}</div>`;
  }).join('');
  const split = item.featured
    ? `<div class="blog-split"><figure><img src="${assetUrl(item.featured)}" alt="${item.imageAlt || item.title}" loading="lazy"></figure><div><h3>${item.focus || 'Özet'}</h3><p>${item.excerpt || ''}</p></div></div>`
    : '';
  const links = (item.links || [])
    .map((slug) => `<a href="https://rentalekran.com/${slug}/">${slug.replace(/-/g, ' ')}</a>`)
    .join(' · ');
  const callout = links ? `<div class="blog-callout"><p>İlgili sayfalar: ${links}</p></div>` : '';
  return split + sections + callout;
}

export function nextQueueItem(keywords, posts) {
  const published = new Set((posts || []).map((p) => p.slug));
  return (keywords.queue || []).find((item) => item.slug && !published.has(item.slug)) || null;
}

export function bumpSync(version) {
  const parts = String(version || '2.5.0').split('.').map((n) => parseInt(n, 10) || 0);
  while (parts.length < 3) parts.push(0);
  parts[2] += 1;
  return parts.join('.');
}

function addDays(iso, days) {
  const d = new Date(`${iso}T12:00:00Z`);
  if (Number.isNaN(d.getTime())) {
    const now = new Date();
    now.setUTCDate(now.getUTCDate() + days);
    return now.toISOString().slice(0, 10);
  }
  d.setUTCDate(d.getUTCDate() + days);
  return d.toISOString().slice(0, 10);
}

function latestDate(posts, queue) {
  const dates = [...(posts || []), ...(queue || [])]
    .map((item) => item.date)
    .filter(Boolean)
    .sort();
  return dates.length ? dates[dates.length - 1] : new Date().toISOString().slice(0, 10);
}

export function briefFromSeed(seed, date) {
  return {
    slug: seed.slug,
    title: seed.title,
    date,
    category: seed.category || 'LED Ekran Rehberi',
    focus: seed.focus,
    excerpt: seed.excerpt,
    featured: seed.featured || 'assets/rental-led-ekran.webp',
    imageAlt: seed.imageAlt || seed.title,
    links: seed.links || ['led-ekran-kiralama', 'led-ekran-satisi', 'iletisim'],
    sections: [
      { h2: seed.h2a, p: [seed.pa] },
      { h2: seed.h2b, p: [seed.pb] },
    ],
  };
}

export function usedSlugs(posts, queue) {
  return new Set([...(posts || []), ...(queue || [])].map((item) => item.slug).filter(Boolean));
}

export function replenishQueue(keywords, posts, topics, min = QUEUE_MIN) {
  const queue = Array.isArray(keywords.queue) ? keywords.queue.slice() : [];
  const used = usedSlugs(posts, queue);
  let date = addDays(latestDate(posts, queue), 7);
  for (const seed of topics) {
    if (queue.length >= min) break;
    if (!seed.slug || used.has(seed.slug)) continue;
    queue.push(briefFromSeed(seed, date));
    used.add(seed.slug);
    date = addDays(date, 7);
  }
  keywords.queue = queue;
  return keywords;
}

function toPost(item) {
  return {
    slug: item.slug,
    title: item.title,
    date: item.date || new Date().toISOString().slice(0, 10),
    category: item.category || 'LED Ekran Rehberi',
    excerpt: item.excerpt || '',
    featured: item.featured || 'assets/rental-led-ekran.webp',
    content: item.content || renderQueuedPost(item),
  };
}

export function runCycle({ keywords, posts, topics, flushQueue = false }) {
  const published = [];
  keywords.queue = keywords.queue || [];
  keywords = replenishQueue(keywords, posts, topics, QUEUE_MIN);
  const limit = flushQueue ? 50 : 1;
  for (let i = 0; i < limit; i += 1) {
    const item = nextQueueItem(keywords, posts);
    if (!item) break;
    posts.push(toPost(item));
    keywords.queue = keywords.queue.filter((q) => q.slug !== item.slug);
    keywords.blogSync = bumpSync(keywords.blogSync);
    published.push(item.slug);
    if (!flushQueue) break;
  }
  keywords = replenishQueue(keywords, posts, topics, QUEUE_MIN);
  return { keywords, posts, published };
}

function run() {
  const keywordsPath = path.join(pluginRoot, 'includes/keywords.json');
  const postsPath = path.join(pluginRoot, 'includes/blog-posts.json');
  const topics = readJson(path.join(root, 'scripts/seo-topics.json'));
  let keywords = readJson(keywordsPath);
  let posts = readJson(postsPath);
  const result = runCycle({ keywords, posts, topics, flushQueue: flush });
  if (!result.published.length) {
    if (dry) {
      console.log('seo-cycle dry-run: queue replenished', result.keywords.queue.length);
      return result;
    }
    fs.writeFileSync(keywordsPath, JSON.stringify(result.keywords, null, 2) + '\n');
    console.log('seo-cycle: no new post; queue', result.keywords.queue.length);
    return result;
  }
  if (dry) {
    console.log('seo-cycle dry-run:', result.published.join(', '), 'queue', result.keywords.queue.length);
    return result;
  }
  fs.writeFileSync(postsPath, JSON.stringify(result.posts, null, 2) + '\n');
  fs.writeFileSync(keywordsPath, JSON.stringify(result.keywords, null, 2) + '\n');
  console.log('seo-cycle published:', result.published.join(', '), 'sync', result.keywords.blogSync, 'queue', result.keywords.queue.length);
  return result;
}

function isDirectRun() {
  const entry = process.argv[1];
  if (!entry) return false;
  return path.resolve(entry) === path.resolve(fileURLToPath(import.meta.url));
}

if (isDirectRun()) {
  run();
}
