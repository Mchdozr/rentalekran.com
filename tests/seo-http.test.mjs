import assert from 'node:assert/strict';
const base=process.env.RLE_TEST_URL||'http://127.0.0.1:8080';
for(const slug of ['', 'led-ekran-kiralama','led-ekran-satisi','rental-led-ekran','istanbul-led-ekran','iletisim']) {
 const url=base+(slug?'/'+slug+'/':'/');const res=await fetch(url);assert.equal(res.status,200,url);
 const html=await res.text();
 assert.equal((html.match(/<h1\b/g)||[]).length,1,url);
 assert.equal((html.match(/<link rel="canonical"/g)||[]).length,1,url);
 const graph=JSON.parse(html.match(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/)[1])['@graph'];
 assert.ok(!JSON.stringify(graph).includes('SearchAction'));
 const org=graph.find(x=>[].concat(x['@type']).includes('Organization'));assert.ok(org.logo);
 assert.ok(html.includes('property="og:image"'));assert.ok(html.includes('content="summary_large_image"'));
 assert.ok(html.includes('property="og:image:width" content="1024"'));
 if(!slug){assert.match(html,/<h1>LED ekran<\/h1>/);assert.match(html,/<title>LED Ekran \| Rental Ekran Satış ve Kiralama<\/title>/);assert.equal(graph.find(x=>x['@type']==='FAQPage').mainEntity.length,4);}
 if(['istanbul-led-ekran','iletisim'].includes(slug)){assert.ok(org.hasMap);assert.ok(html.includes('output=embed'));}
 if(slug==='rental-led-ekran'){assert.ok(graph.find(x=>x['@type']==='FAQPage').mainEntity.length>0);assert.match(html,/<title>Rental LED Ekran Kabinleri \| Sahne ve Fuar<\/title>/);}
 console.log('SEO HTTP passed:',url);
}
