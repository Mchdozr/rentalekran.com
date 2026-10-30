const {chromium}=require('playwright');
const assert=require('node:assert/strict');
(async()=>{
const browser=await chromium.launch({headless:true,executablePath:process.env.RLE_CHROMIUM_PATH||'/usr/bin/chromium',args:['--no-sandbox']});
const results=[];
for(const width of [390,1366]){
 const page=await browser.newPage({viewport:{width,height:900},deviceScaleFactor:1});
 const errors=[];page.on('pageerror',e=>errors.push(e.message));
 for(const slug of ['', 'led-ekran-kiralama','led-ekran-satisi','rental-led-ekran','istanbul-led-ekran','iletisim']){
  await page.goto((process.env.RLE_TEST_URL||'http://127.0.0.1:8080')+'/' +(slug?slug+'/':''),{waitUntil:'domcontentloaded'});
  const state=await page.evaluate(()=>({title:document.title,h1:[...document.querySelectorAll('h1')].map(x=>x.textContent),overflow:document.documentElement.scrollWidth>innerWidth+1}));
  assert.equal(state.h1.length,1);assert.equal(state.overflow,false,slug+' horizontal overflow @'+width);assert.deepEqual(errors,[]);
  if(!slug)assert.ok(await page.locator('.service-grid h3').first().evaluate(e=>e.getBoundingClientRect().width)>200,'Service heading has readable width');
  if(!slug)await page.screenshot({path:'/tmp/rentalekran-home-'+width+'.png',fullPage:true});
  results.push({slug:slug||'home',width,...state});
 }
 await page.close();
}
console.log(JSON.stringify(results,null,2));await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
