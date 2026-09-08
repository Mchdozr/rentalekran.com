(function (root) {
  'use strict';
  function calculate(width, height, cabinetWidth, cabinetHeight) {
    for (const n of [width, height, cabinetWidth, cabinetHeight]) {
      if (typeof n !== 'number' || !Number.isFinite(n) || n <= 0 || n > 100) throw new RangeError('Ölçüler 0 ile 100 metre arasında olmalıdır.');
    }
    const columns = Math.ceil(width / cabinetWidth - 1e-10);
    const rows = Math.ceil(height / cabinetHeight - 1e-10);
    const w = Number((columns * cabinetWidth).toFixed(4));
    const h = Number((rows * cabinetHeight).toFixed(4));
    return {columns, rows, count: columns * rows, width: w, height: h, area: Number((w * h).toFixed(4))};
  }
  if (typeof module !== 'undefined' && module.exports) module.exports = {calculate};
  if (typeof document === 'undefined') return;
  const menu = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#main-nav');
  if (menu && nav) {
    menu.addEventListener('click', () => {const open = menu.getAttribute('aria-expanded') !== 'true'; menu.setAttribute('aria-expanded', String(open)); nav.classList.toggle('is-open', open);});
    document.addEventListener('keydown', e => {if (e.key === 'Escape' && menu.getAttribute('aria-expanded') === 'true') {menu.setAttribute('aria-expanded','false');nav.classList.remove('is-open');menu.focus();}});
  }
  const dropdowns = Array.from(document.querySelectorAll('#main-nav .nav-group'));
  const closeDropdowns = except => dropdowns.forEach(group => {if (group !== except) group.removeAttribute('open');});
  dropdowns.forEach(group => {
    group.addEventListener('toggle', () => {if (group.open) closeDropdowns(group);});
    group.addEventListener('pointerleave', () => {if (root.matchMedia && root.matchMedia('(hover: hover)').matches) group.removeAttribute('open');});
  });
  document.addEventListener('pointerdown', event => {if (nav && !nav.contains(event.target)) closeDropdowns();});
  document.querySelectorAll('[data-rle-slider]').forEach(slider => {
    const slides = Array.from(slider.querySelectorAll('.product-slider-slide'));
    const dots = Array.from(slider.querySelectorAll('[data-slider-dot]'));
    const count = slider.querySelector('[data-slider-count]');
    let current = 0;
    const show = index => {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => {slide.hidden = i !== current;});
      dots.forEach((dot, i) => {if (i === current) dot.setAttribute('aria-current','true'); else dot.removeAttribute('aria-current');});
      if (count) count.textContent = `${current + 1} / ${slides.length}`;
    };
    const previous = slider.querySelector('[data-slider-prev]'); const next = slider.querySelector('[data-slider-next]');
    if (previous) previous.addEventListener('click', () => show(current - 1));
    if (next) next.addEventListener('click', () => show(current + 1));
    dots.forEach((dot, i) => dot.addEventListener('click', () => show(i)));
  });
  const viewer = document.createElement('dialog');
  viewer.className = 'rle-lightbox';
  viewer.setAttribute('aria-label', 'Ürün görseli');
  viewer.innerHTML = '<div class="rle-lightbox-bar"><span>Ürünü yakından inceleyin</span><button type="button" data-close aria-label="Görseli kapat">✕</button></div><img alt=""><p aria-live="polite"></p><div class="rle-lightbox-bar"><button type="button" data-prev aria-label="Önceki görsel">←</button><button type="button" data-next aria-label="Sonraki görsel">→</button></div>';
  document.body.appendChild(viewer);
  let activeImages = [], activeIndex = 0, opener = null;
  const renderImage = i => {
    activeIndex = (i + activeImages.length) % activeImages.length;
    const source = activeImages[activeIndex];
    viewer.querySelector('img').src = source.src;
    viewer.querySelector('img').alt = source.alt;
    viewer.querySelector('p').textContent = `${source.alt} · ${activeIndex + 1} / ${activeImages.length}`;
    viewer.querySelector('[data-prev]').hidden = viewer.querySelector('[data-next]').hidden = activeImages.length < 2;
  };
  document.querySelectorAll('[data-rle-slider], .legacy-image-grid').forEach(group => {
    const images = Array.from(group.querySelectorAll('img'));
    images.forEach((img, index) => {
      let trigger = img.closest('a,button');
      if (!trigger) { trigger = document.createElement('button'); trigger.type = 'button'; img.parentNode.insertBefore(trigger,img); trigger.appendChild(img); }
      trigger.classList.add('rle-zoom');
      trigger.setAttribute('aria-label', `${img.alt || 'Ürün görseli'} — büyüt`);
      trigger.setAttribute('aria-haspopup','dialog');
      trigger.addEventListener('click', event => {
        event.preventDefault(); opener = trigger; activeImages = images; renderImage(index);
        document.documentElement.classList.add('rle-lightbox-open'); viewer.showModal(); viewer.querySelector('[data-close]').focus();
      });
    });
  });
  viewer.querySelector('[data-close]').addEventListener('click',()=>viewer.close());
  viewer.querySelector('[data-prev]').addEventListener('click',()=>renderImage(activeIndex-1));
  viewer.querySelector('[data-next]').addEventListener('click',()=>renderImage(activeIndex+1));
  viewer.addEventListener('click',e=>{if(e.target===viewer){const r=viewer.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)viewer.close();}});
  viewer.addEventListener('keydown',e=>{if(e.key==='ArrowLeft'){e.preventDefault();renderImage(activeIndex-1);}if(e.key==='ArrowRight'){e.preventDefault();renderImage(activeIndex+1);}});
  viewer.addEventListener('close',()=>{document.documentElement.classList.remove('rle-lightbox-open');if(opener)opener.focus();});
  document.querySelectorAll('main table').forEach(table=>{
    const rows=Array.from(table.rows);
    if(!rows.length)return;
    const headers=Array.from(rows[0].cells).map(c=>c.textContent.trim());
    rows.forEach((row,i)=>Array.from(row.cells).forEach((cell,j)=>{if(i>0)cell.dataset.label=headers[j]||'';}));
    table.classList.add('rle-stacked-table');
  });
  const form = document.querySelector('#rle-planner-form');
  if (!form) return;
  const generate = document.querySelector('#rle-generate');
  const selected = new URLSearchParams(root.location.search).get('urun');
  if (Array.from(form.elements.product.options).some(o => o.value === selected)) form.elements.product.value = selected;
  if (new URLSearchParams(root.location.search).get('talep') === 'kiralama') {
    for (const option of form.elements.request.options) if (option.text === 'Kiralama talebi') form.elements.request.value = option.value;
  }
  const result = document.querySelector('#rle-result');
  form.addEventListener('input', () => {result.hidden = true;});
  form.addEventListener('change', () => {result.hidden = true;});
  const prepareQuote = () => {
    if (!form.reportValidity()) return false;
    const data = new FormData(form);
    const [cw,ch] = data.get('cabinet').split(',').map(Number);
    let answer;
    try {answer = calculate(Number(data.get('width')), Number(data.get('height')), cw, ch);} catch (error) {return false;}
    const format = n => n.toLocaleString('tr-TR', {maximumFractionDigits: 2});
    const summary = `${answer.columns} sütun × ${answer.rows} sıra = ${answer.count} kabin. Örnek ekran: ${format(answer.width)} × ${format(answer.height)} m (${format(answer.area)} m²).`;
    const product = form.elements.product.options[form.elements.product.selectedIndex].text;
    const message = [
      'Merhaba, LED ekran projem için bilgi / teklif almak istiyorum.',
      `Ad: ${data.get('contact_name') || ''}`, `Telefon: ${data.get('contact_phone') || ''}`,
      `Ürün: ${product}`, `Talep: ${data.get('request')}`, `Ortam: ${data.get('environment')}`,
      `Şehir / ilçe: ${data.get('city') || 'Belirlenecek'}`,
      `Hedef ölçü: ${format(Number(data.get('width')))} × ${format(Number(data.get('height')))} m`,
      `İzleme mesafesi: ${data.get('distance') || 'Belirlenecek'}`,
      `Örnek kabin: ${cw*1000} × ${ch*1000} mm`, summary,
      'Kabin ölçüsü ve teknik uygunluğun seçilen modele göre doğrulanmasını rica ederim.',
      `Proje notu: ${data.get('note') || 'Yok'}`
    ].join('\n');
    document.querySelector('#rle-calculation').textContent = summary;
    document.querySelector('#rle-message').value = message;
    const container = document.querySelector('.planner');
    const whatsapp = document.querySelector('#rle-whatsapp');
    const email = document.querySelector('#rle-email');
    whatsapp.href = `https://wa.me/${container.dataset.wa}?text=${encodeURIComponent(message)}`;
    email.href = `mailto:${container.dataset.email}?subject=${encodeURIComponent('LED ekran proje teklifi — '+product)}&body=${encodeURIComponent(message)}`;
    result.hidden = false;
    document.querySelector('#rle-message').focus();
    root.dispatchEvent(new CustomEvent('rentalekran:quote_prepared', {detail:{product:data.get('product'),environment:data.get('environment')}}));
    return true;
  };
  if (generate) generate.addEventListener('click', prepareQuote);
})(typeof window !== 'undefined' ? window : globalThis);
