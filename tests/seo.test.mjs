import assert from 'node:assert/strict';
import fs from 'node:fs';
import {renderQueuedPost,briefFromSeed} from '../scripts/seo-cycle.mjs';
const read=p=>JSON.parse(fs.readFileSync(new URL('../'+p,import.meta.url),'utf8'));
assert.match(renderQueuedPost({title:'Ekran',featured:'assets/rental-led-ekran.webp'}),/width="1024" height="576"/);
assert.match(renderQueuedPost({links:['']}),/href="https:\/\/rentalekran.com\/">Rental Ekran<\/a>/);
const keywords=read('rentalekran-growth/includes/keywords.json');
assert.equal(keywords.pages[''].title,'LED Ekran | Rental Ekran Satış ve Kiralama');
assert.equal(keywords.pages[''].name,'LED ekran');
assert.equal(keywords.faqs[''].length,4);
assert.equal(keywords.pages['rental-led-ekran'].title,'Rental LED Ekran Kabinleri | Sahne ve Fuar');
const productSlugs=new Set(read('rentalekran-growth/includes/products.json').map(p=>p.slug));
function checkLinks(html, slug) {
 const paths=[...html.matchAll(/href=["']https:\/\/rentalekran\.com\/([^"']*)["']/g)].map(m=>m[1].split(/[?#]/)[0].replace(/\/$/,''));
 assert.ok(paths.some(p=>productSlugs.has(p)),slug+' links to a product');
 assert.ok(paths.some(p=>['','led-ekran-kiralama','led-ekran-satisi'].includes(p)),slug+' links to a main hub');
}
const words=html=>html.replace(/<[^>]*>/g,' ').trim().split(/\s+/u).length;
for(const post of read('rentalekran-growth/includes/blog-posts.json')) {
  assert.ok(words(post.content)>=700,post.slug+' must offer 700+ words of guidance');
  checkLinks(post.content,post.slug);
  assert.match(post.content,/href="https:\/\/rentalekran.com\//);
}
for(const item of keywords.queue) {const html=renderQueuedPost(item);assert.ok(words(html)>=700,item.slug+' queue depth');checkLinks(html,item.slug);}
for(const seed of read('scripts/seo-topics.json')) {const html=renderQueuedPost(briefFromSeed(seed,'2027-01-01'));assert.ok(words(html)>=700,seed.slug+' seed depth');checkLinks(html,seed.slug);}
console.log('SEO content tests passed');
