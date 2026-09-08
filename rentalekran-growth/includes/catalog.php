<?php
defined('ABSPATH') || exit;
function rle_products() {
    static $data;
    if ($data === null) $data = json_decode(file_get_contents(RLE_DIR . 'includes/products.json'), true);
    return $data;
}
function rle_keywords() {
    static $data;
    if ($data === null) {
        $path = RLE_DIR . 'includes/keywords.json';
        $data = file_exists($path) ? json_decode(file_get_contents($path), true) : array();
        if (!is_array($data)) $data = array();
    }
    return $data;
}
function rle_keyword_page($slug) {
    $pages = isset(rle_keywords()['pages']) ? rle_keywords()['pages'] : array();
    return isset($pages[$slug]) ? $pages[$slug] : array();
}
function rle_keyword_faqs($slug) {
    $faqs = isset(rle_keywords()['faqs']) ? rle_keywords()['faqs'] : array();
    return isset($faqs[$slug]) ? $faqs[$slug] : array();
}
function rle_apply_keyword_seo($routes) {
    foreach (rle_keywords()['pages'] ?? array() as $slug => $seo) {
        if (!isset($routes[$slug])) continue;
        if (!empty($seo['title'])) $routes[$slug]['title'] = $seo['title'];
        if (!empty($seo['description'])) $routes[$slug]['description'] = $seo['description'];
        if (!empty($seo['name'])) $routes[$slug]['name'] = $seo['name'];
    }
    return $routes;
}
function rle_render_faq($slug) {
    $faqs = rle_keyword_faqs($slug);
    if (!$faqs) return;
    echo '<section class="section wrap faq"><p class="eyebrow">SIK SORULANLAR</p><h2>Arama yapanların sorduğu sorular.</h2>';
    foreach ($faqs as $qa) {
        echo '<details><summary>'.esc_html($qa[0]).'</summary><p>'.esc_html($qa[1]).'</p></details>';
    }
    echo '</section>';
}
function rle_legacy_content($slug) {
    static $data;
    if ($data === null) $data = json_decode(file_get_contents(RLE_DIR . 'includes/legacy-content.json'), true);
    if ($slug === 'transparan-led-ekran') $slug = 'elementor-3277';
    return isset($data[$slug]) ? $data[$slug] : array('images'=>array(),'tables'=>array(),'sections'=>array());
}
function rle_settings() {
    return wp_parse_args(get_option('rle_settings', array()), array(
        'phone' => '+90 543 879 51 08', 'phone_office' => '+90 212 220 40 04', 'phone_alt' => '+90 530 405 67 68',
        'whatsapp' => '905438795108',
        'email' => 'rental@ledajans.com', 'address' => 'Halide Edip Adıvar Mah. Gül 2 Sk. No:10a, 34382 Şişli / İstanbul',
        'business_mode' => 'both', 'live_enabled' => false, 'ga_id' => ''
    ));
}
function rle_routes() {
    $routes = array(
        '' => array('name'=>'LED ekran satış ve kiralama', 'title'=>'LED Ekran Satış ve Kiralama | İstanbul · Rental Ekran', 'description'=>'İstanbul merkezli LED ekran satış ve kiralama çözümleri. Türkiye genelindeki sahne, fuar, vitrin ve kurumsal projeleriniz için model seçin, teklif isteyin.'),
        'led-ekran-teklif'=>array('name'=>'LED ekran proje teklifi','title'=>'LED Ekran Projesi için İletişim ve Teklif | Rental Ekran','description'=>'Ekran ölçüsü, kullanım alanı ve şehir bilgisiyle LED ekran proje teklifinizi hazırlayın. Rental Ekran İstanbul iletişim bilgilerine ulaşın.'),
        'urunlerimiz'=>array('name'=>'LED ekran ürünleri','title'=>'LED Ekran Ürünleri | Rental Ekran','description'=>'Rental, Front, AIR / STAR, COB, GOB, Floor ve transparan LED ekran serilerini görselleri, teknik tabloları ve kullanım alanlarıyla inceleyin.'),
        'iletisim'=>array('name'=>'İletişim ve teklif','title'=>'LED Ekran İletişim ve Teklif | Rental Ekran','description'=>'LED ekran satış veya kiralama projeniz için ölçü, mekân ve şehir bilgisiyle teklif talebi hazırlayın.'),
        'blog-sayfasi'=>array('name'=>'LED ekran blog','title'=>'LED Ekran Blog | Rental Ekran','description'=>'LED ekran teknolojileri, kontrol sistemleri ve proje seçimi hakkında Rental Ekran blog yazılarını inceleyin.'),
        'hakkimizda'=>array('name'=>'Hakkımızda','title'=>'Hakkımızda | Rental Ekran','description'=>'Rental Ekran’ın LED ekran çözüm yaklaşımı, ürünleri ve iletişim bilgileri.'),
        'firma-bilgilerimiz'=>array('name'=>'Firma bilgileri','title'=>'Firma Bilgileri | Rental Ekran','description'=>'Rental Ekran firma, iletişim ve adres bilgileri.'),
        'case-studies'=>array('name'=>'Projeler ve referans içerikleri','title'=>'Projeler ve Referans İçerikleri | Rental Ekran','description'=>'Rental Ekran arşivindeki proje ve referans içeriklerini inceleyin.'),
        'shipment'=>array('name'=>'Sevkiyat ve kurulum içeriği','title'=>'LED Ekran Sevkiyat ve Kurulum | Rental Ekran','description'=>'LED ekran sevkiyat ve kurulum kapsamını proje koşullarına göre değerlendirin.'),
        'partners'=>array('name'=>'İş ortakları içeriği','title'=>'İş Ortakları | Rental Ekran','description'=>'Rental Ekran arşivindeki iş ortaklığı içeriklerini inceleyin.'),
        'services'=>array('name'=>'Hizmetler','title'=>'LED Ekran Hizmetleri | Rental Ekran','description'=>'LED ekran satış, kiralama ve proje hizmetlerini inceleyin.'),
        'led-ekran-fiyatlari'=>array('name'=>'LED ekran fiyatları nasıl belirlenir?','title'=>'LED Ekran Fiyatları: Teklifi Belirleyen Etkenler | Rental Ekran','description'=>'LED ekran fiyatını etkileyen ölçü, piksel aralığı, kabin, kontrol sistemi ve montaj kapsamını öğrenin. Karşılaştırılabilir teklif için kontrol listesi.'),
        'led-ekran-secim-rehberi'=>array('name'=>'Projeniz için LED ekran seçimi','title'=>'LED Ekran Seçim Rehberi: Ölçü, Piksel ve Mekân | Rental Ekran','description'=>'İzleme mesafesi, içerik, ekran ölçüsü ve bakım erişimine göre LED ekran seçimini planlayın. Yedi ürün ailesini ihtiyaçlarınıza göre değerlendirin.'),
        'ic-mekan-dis-mekan-led-ekran'=>array('name'=>'İç mekân mı, dış mekân mı?','title'=>'İç Mekân ve Dış Mekân LED Ekran Farkları | Rental Ekran','description'=>'İç ve dış mekân LED ekran seçerken parlaklık, koruma, bakım ve montaj farklarını inceleyin. Vitrin, sahne ve sabit projeler için seçim kriterleri.'),
        'led-ekran-kiralama'=>array('name'=>'LED ekran kiralama','title'=>'LED Ekran Kiralama | Sahne, Fuar, Etkinlik | Rental Ekran','description'=>'Sahne, fuar ve etkinlikler için LED ekran kiralama talebinizi hazırlayın. İstanbul ve Türkiye genelindeki projeler için tarih, ölçü ve mekâna göre teklif alın.'),
        'led-ekran-satisi'=>array('name'=>'LED ekran satışı','title'=>'LED Ekran Satışı | Satın Alma ve Proje | Rental Ekran','description'=>'LED ekran satışı ve satın alma: kalıcı kurulum, piksel aralığı ve montaj kapsamına göre proje teklifi. İstanbul merkezli Rental Ekran.'),
        'istanbul-led-ekran'=>array('name'=>'İstanbul LED ekran satış ve kiralama','title'=>'İstanbul LED Ekran Kiralama ve Satış | Şişli | Rental Ekran','description'=>'Şişli, İstanbul merkezli Rental Ekran ile LED ekran satış ve kiralama projenizi planlayın. Türkiye genelindeki talepler için ölçü, tarih ve mekânı paylaşın.')
    );
    foreach (rle_products() as $product) $routes[$product['slug']] = $product;
    return rle_apply_keyword_seo($routes);
}
function rle_canonical_for($slug) {
    return home_url($slug === '' ? '/' : '/' . $slug . '/');
}
function rle_logo_url() {
    return RLE_URL . 'assets/rental-ekran-logo.png';
}
function rle_phones() {
    $s = rle_settings();
    $list = array($s['phone']);
    foreach (array('phone_office', 'phone_alt') as $key) {
        if (!empty($s[$key])) $list[] = $s[$key];
    }
    return array_values(array_unique(array_filter($list)));
}
function rle_live() { return !empty(rle_settings()['live_enabled']); }
function rle_preview() {
    return isset($_GET['rle_preview']) && is_scalar($_GET['rle_preview']) && (string)$_GET['rle_preview']==='1' && current_user_can('manage_options');
}
function rle_enabled() { return rle_live() || rle_preview(); }
function rle_navigation_url($slug) {
    $url=rle_canonical_for($slug);
    return rle_preview() ? add_query_arg('rle_preview','1',$url) : $url;
}
function rle_link($slug) { return esc_url(rle_navigation_url($slug)); }
function rle_legacy_url($slug='') {
    $url=home_url($slug === '' ? '/' : '/'.$slug.'/');
    return rle_preview() ? add_query_arg('rle_preview','1',$url) : $url;
}
function rle_product($slug) {
    foreach (rle_products() as $product) if ($product['slug'] === $slug || (!empty($product['legacy']) && $product['legacy'] === $slug)) return $product;
    return null;
}
function rle_demo_ids() {
    $ids = array();
    // "Örnek Sayfa" WordPress'in kurulumla gelen sayfasıdır; diğer eski
    // kurumsal sayfalar yeni tasarımda gerçek içerik olarak yayınlanır.
    foreach (array('ornek-sayfa') as $slug) {
        $post = get_page_by_path($slug); if ($post) $ids[] = $post->ID;
    }
    return $ids;
}
