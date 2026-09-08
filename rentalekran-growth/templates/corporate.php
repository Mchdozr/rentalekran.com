<?php
defined('ABSPATH') || exit;
$phone = esc_html($settings['phone']);
$tel = esc_attr(preg_replace('/[^+0-9]/', '', $settings['phone']));
?>
<?php if ($route === 'services'): ?>
<section class="section wrap legacy-sections">
    <article><h3>LED ekran satışı</h3><p>Ürün ailesi, piksel aralığı, kontrol sistemi ve montaj kapsamına göre kalıcı ekran yatırımı planlarız. Teklif, seçilen modelin güncel belgesi ve projenizin ölçüsüyle hazırlanır.</p></article>
    <article><h3>LED ekran kiralama</h3><p>Fuar, sahne, lansman ve toplantılar için tarih, süre, şehir ve mekân bilgisiyle kiralama kapsamını netleştiririz. Stok ve ekip uygunluğu teklif aşamasında teyit edilir.</p></article>
    <article><h3>Keşif ve ölçü</h3><p>İzleme mesafesi, montaj yüzeyi, elektrik hazırlığı ve bakım erişimi teklifi belirler. İstanbul projelerinde yerinde değerlendirme; diğer şehirlerde fotoğraf ve plan üzerinden ilerler.</p></article>
    <article><h3>Kurulum ve teknik destek</h3><p>Nakliye, kurulum, söküm ve operatör ihtiyacı proje koşullarına göre ayrı kalemlerle yazılır. Garanti ve servis kapsamı model bazında teklifte belirtilir.</p></article>
</section>
<?php elseif ($route === 'case-studies'): ?>
<section class="section wrap"><div class="section-heading"><div><p class="eyebrow">UYGULAMA ALANLARI</p><h2>Hangi iş, hangi ekran.</h2></div><p>Müşteri adı uydurmadan: sık gelen proje tipleri ve başlangıç ürünleri.</p></div>
<div class="product-grid">
    <a class="product-card" href="<?php echo rle_link('led-ekran-kiralama'); ?>"><div class="card-visual"><img src="<?php echo esc_url(RLE_URL.'assets/rental-led-ekran.webp'); ?>" alt="Sahne ve fuar rental LED ekran" loading="lazy" width="720" height="480"></div><div class="card-copy"><p class="eyebrow">SAHNE · FUAR</p><h3>Etkinlik ekranı</h3><p>Kısa süreli kurulumlarda rental ve AIR kabinler başlangıç noktasıdır.</p><span class="text-link">Kiralama talebi →</span></div></a>
    <a class="product-card" href="<?php echo rle_link('transparan-led-ekran'); ?>"><div class="card-visual"><img src="<?php echo esc_url(RLE_URL.'assets/transparan.webp'); ?>" alt="Vitrin transparan LED ekran" loading="lazy" width="720" height="480"></div><div class="card-copy"><p class="eyebrow">VİTRİN · CAM</p><h3>Mağaza vitrini</h3><p>Cam yüzeyde içerik ve mekânın birlikte görünmesi gereken projeler.</p><span class="text-link">Transparan serisi →</span></div></a>
    <a class="product-card" href="<?php echo rle_link('cob'); ?>"><div class="card-visual"><img src="<?php echo esc_url(RLE_URL.'assets/cob.webp'); ?>" alt="Yakın izleme COB LED ekran" loading="lazy" width="720" height="480"></div><div class="card-copy"><p class="eyebrow">TOPLANTI · SUNUM</p><h3>Yakın izleme</h3><p>Yazı ve detayın yakından okunduğu kurumsal alanlar için COB seçenekleri.</p><span class="text-link">COB serisi →</span></div></a>
    <a class="product-card" href="<?php echo rle_link('floor'); ?>"><div class="card-visual"><img src="<?php echo esc_url(RLE_URL.'assets/floor.webp'); ?>" alt="Zemin floor LED ekran" loading="lazy" width="720" height="480"></div><div class="card-copy"><p class="eyebrow">ZEMİN</p><h3>Zemin uygulaması</h3><p>Yük, yüzey ve kablo planı model belgesiyle doğrulanmadan varsayılmaz.</p><span class="text-link">Floor serisi →</span></div></a>
</div></section>
<?php elseif ($route === 'shipment'): ?>
<section class="section wrap legacy-sections">
    <article><h3>Nakliye</h3><p>Kabin sayısı, paketleme ve varış şehri nakliye kapsamını belirler. İstanbul dışı taleplerde süre ve teslim şekli teklifte yazılır; aynı gün teslimat taahhüdü verilmez.</p></article>
    <article><h3>Kurulum</h3><p>Askı, zemin, duvar veya taşınabilir taşıyıcı yapı ayrı planlanır. Mekânın yükleme kapısı, asansör ve çalışma saatleri kurulumu etkiler.</p></article>
    <article><h3>Söküm ve iade</h3><p>Kiralama işlerinde söküm tarihi, stok dönüşü ve hasar kontrolü teslim tutanağıyla netleşir.</p></article>
    <article><h3>İletişim</h3><p>Sevkiyat için ölçü, tarih ve adresi <a href="tel:<?php echo $tel; ?>"><?php echo $phone; ?></a> veya <a href="<?php echo rle_link('iletisim'); ?>">teklif formu</a> üzerinden paylaşın.</p></article>
</section>
<?php else: ?>
<section class="section wrap legacy-sections">
    <article><h3>Proje ortaklığı</h3><p>Ajans, organizasyon ve uygulama ekipleriyle satış veya kiralama işlerini ürün ve teknik kapsam üzerinden yürütürüz. Stok ve model uygunluğu her işte ayrıca teyit edilir.</p></article>
    <article><h3>Nasıl ilerleriz</h3><p>İhtiyaç özeti, ölçü, şehir ve tarih ile başlayın. Uygun ürün ailesi, kontrol sistemi ve kurulum yöntemi yazılı teklifte toplanır.</p></article>
    <article><h3>Teklif</h3><p>Ortaklık ve bayi talepleriniz için <a href="mailto:<?php echo esc_attr($settings['email']); ?>"><?php echo esc_html($settings['email']); ?></a> veya <a href="tel:<?php echo $tel; ?>"><?php echo $phone; ?></a>.</p></article>
</section>
<?php endif; ?>
<div class="wrap"><?php rle_cta(); ?></div>
