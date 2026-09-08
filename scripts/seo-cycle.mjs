/**
 * Publish the next SEO keyword queue item into blog-posts.json.
 * Usage: node scripts/seo-cycle.mjs [--dry-run]
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const plugin = path.join(root, 'rentalekran-growth');
const dry = process.argv.includes('--dry-run');

function readJson(rel) {
  return JSON.parse(fs.readFileSync(path.join(plugin, rel), 'utf8'));
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

function run() {
  const keywords = readJson('includes/keywords.json');
  const posts = readJson('includes/blog-posts.json');
  const item = nextQueueItem(keywords, posts);
  if (!item) {
    console.log('seo-cycle: queue empty');
    return { published: false };
  }
  const post = {
    slug: item.slug,
    title: item.title,
    date: item.date || new Date().toISOString().slice(0, 10),
    category: item.category || 'LED Ekran Rehberi',
    excerpt: item.excerpt || '',
    featured: item.featured || 'assets/rental-led-ekran.webp',
    content: item.content || renderQueuedPost(item),
  };
  if (dry) {
    console.log('seo-cycle dry-run:', post.slug);
    return { published: false, slug: post.slug };
  }
  posts.push(post);
  keywords.queue = (keywords.queue || []).filter((q) => q.slug !== item.slug);
  keywords.blogSync = bumpSync(keywords.blogSync);
  fs.writeFileSync(path.join(plugin, 'includes/blog-posts.json'), JSON.stringify(posts, null, 2) + '\n');
  fs.writeFileSync(path.join(plugin, 'includes/keywords.json'), JSON.stringify(keywords, null, 2) + '\n');
  console.log('seo-cycle published:', post.slug, 'sync', keywords.blogSync);
  return { published: true, slug: post.slug };
}

function isDirectRun() {
  const entry = process.argv[1];
  if (!entry) return false;
  return path.resolve(entry) === path.resolve(fileURLToPath(import.meta.url));
}

if (isDirectRun()) {
  run();
}
