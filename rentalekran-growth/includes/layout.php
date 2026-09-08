<?php
defined('ABSPATH') || exit;

function rle_shell_active() {
    if (!rle_enabled() || rle_original()) return false;
    if (rle_route() !== null) return true;
    return is_singular('post') && !is_attachment();
}

function rle_render_header() {
    $settings = rle_settings();
    $product_count = count(rle_products());
    ?>
<header class="site-header">
    <a class="brand" href="<?php echo rle_link(''); ?>" aria-label="Rental Ekran ana sayfa">
        <img src="<?php echo esc_url(rle_logo_url()); ?>" alt="Rental Ekran" width="928" height="168">
    </a>
    <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="main-nav">Menü <span aria-hidden="true">☰</span></button>
    <nav id="main-nav" aria-label="Ana gezinme">
        <a class="nav-link" href="<?php echo rle_link(''); ?>"><span class="nav-label">Ana sayfa</span></a>
        <details class="nav-group nav-group--products">
            <summary><span class="nav-label">Ürünlerimiz</span><span class="nav-badge"><?php echo esc_html($product_count); ?> seri</span></summary>
            <div class="nav-drop">
                <a href="<?php echo rle_link('urunlerimiz'); ?>"><span class="nav-drop-kicker">Katalog</span>Tüm ürünler</a>
                <?php foreach (rle_products() as $p): ?>
                <a href="<?php echo rle_link($p['slug']); ?>"><?php echo esc_html($p['name']); ?></a>
                <?php endforeach; ?>
                <a href="https://drive.google.com/file/d/17NBv7dyLgH8kbWdPiN_vM-fTWF9a3VF5/view" target="_blank" rel="noopener">Stadium Screen dokümanı ↗</a>
            </div>
        </details>
        <details class="nav-group nav-group--guides">
            <summary><span class="nav-label">Teknik bilgi</span><span class="nav-badge nav-badge--soft">Rehber</span></summary>
            <div class="nav-drop">
                <a href="<?php echo rle_link('led-ekran-satisi'); ?>">LED ekran satışı</a>
                <a href="<?php echo rle_link('led-ekran-kiralama'); ?>">LED ekran kiralama</a>
                <a href="<?php echo rle_link('led-ekran-secim-rehberi'); ?>">LED ekran seçim rehberi</a>
                <a href="<?php echo rle_link('led-ekran-fiyatlari'); ?>">LED ekran fiyatları</a>
                <a href="<?php echo rle_link('ic-mekan-dis-mekan-led-ekran'); ?>">İç / dış mekân seçimi</a>
                <a href="<?php echo rle_link('blog-sayfasi'); ?>">Blog yazıları</a>
            </div>
        </details>
        <details class="nav-group nav-group--corp">
            <summary><span class="nav-label">Kurumsal</span></summary>
            <div class="nav-drop">
                <a href="<?php echo rle_link('hakkimizda'); ?>">Hakkımızda</a>
                <a href="<?php echo rle_link('firma-bilgilerimiz'); ?>">Firma bilgileri</a>
                <a href="<?php echo rle_link('services'); ?>">Hizmetler</a>
                <a href="<?php echo rle_link('case-studies'); ?>">Projeler ve referanslar</a>
                <a href="<?php echo rle_link('shipment'); ?>">Sevkiyat ve kurulum</a>
            </div>
        </details>
        <a class="nav-link nav-link--accent" href="<?php echo rle_link('led-ekran-kiralama'); ?>"><span class="nav-label">Kiralama</span></a>
        <a class="nav-link" href="<?php echo rle_link('led-ekran-satisi'); ?>"><span class="nav-label">Satış</span></a>
        <a class="nav-cta" href="<?php echo rle_link('iletisim'); ?>">
            <span class="nav-label">Projenizi konuşalım</span>
            <span class="nav-cta-dot" aria-hidden="true"></span>
            <span aria-hidden="true">↗</span>
        </a>
    </nav>
</header>
    <?php
}

function rle_render_footer() {
    $settings = rle_settings();
    ?>
<footer class="site-footer">
    <div class="footer-inner footer-reference-layout">
        <section class="footer-brand">
            <a class="brand" href="<?php echo rle_link(''); ?>"><img src="<?php echo esc_url(rle_logo_url()); ?>" alt="Rental Ekran" width="928" height="168"></a>
            <p>Rental Ekran; sahne, mağaza, fuar ve kurumsal mekânlar için LED ekran satış ve kiralama projelerinde doğru çözümü belirlemenize yardımcı olur.</p>
            <a class="footer-phone" href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/', '', $settings['phone'])); ?>"><?php echo esc_html($settings['phone']); ?> ↗</a>
        </section>
        <nav class="footer-list footer-quick" aria-label="Hızlı bağlantılar">
            <h2>Hızlı Linkler</h2>
            <a href="<?php echo rle_link(''); ?>">Ana sayfa</a>
            <a href="<?php echo rle_link('hakkimizda'); ?>">Hakkımızda</a>
            <a href="<?php echo rle_link('firma-bilgilerimiz'); ?>">Firma bilgileri</a>
            <a href="<?php echo rle_link('led-ekran-kiralama'); ?>">LED ekran kiralama</a>
            <a href="<?php echo rle_link('led-ekran-satisi'); ?>">LED ekran satışı</a>
            <a href="<?php echo rle_link('blog-sayfasi'); ?>">Blog</a>
            <a href="<?php echo rle_link('iletisim'); ?>">İletişim</a>
        </nav>
        <section class="footer-gallery" aria-labelledby="footer-gallery-title">
            <h2 id="footer-gallery-title">Galeri</h2>
            <div class="footer-gallery-grid">
                <a href="<?php echo rle_link('rental-led-ekran'); ?>"><img src="<?php echo esc_url(RLE_URL . 'assets/rental-led-ekran.webp'); ?>" alt="Rental LED ekran"><span>Rental LED</span></a>
                <a href="<?php echo rle_link('front'); ?>"><img src="<?php echo esc_url(RLE_URL . 'assets/front.webp'); ?>" alt="Front LED ekran"><span>Front LED</span></a>
                <a href="<?php echo rle_link('cob'); ?>"><img src="<?php echo esc_url(RLE_URL . 'assets/cob.webp'); ?>" alt="COB LED ekran"><span>COB LED</span></a>
                <a href="<?php echo rle_link('floor'); ?>"><img src="<?php echo esc_url(RLE_URL . 'assets/floor.webp'); ?>" alt="Floor zemin LED ekran"><span>Floor LED</span></a>
            </div>
        </section>
        <nav class="footer-list footer-products" aria-label="Ürün bağlantıları">
            <h2>Ürünler</h2>
            <?php foreach (rle_products() as $p): ?><a href="<?php echo rle_link($p['slug']); ?>"><?php echo esc_html($p['name']); ?></a><?php endforeach; ?>
            <a href="https://drive.google.com/file/d/17NBv7dyLgH8kbWdPiN_vM-fTWF9a3VF5/view" target="_blank" rel="noopener">Stadium Screen ↗</a>
        </nav>
        <section class="footer-contact">
            <h2>İletişim</h2>
            <address>
                <p><?php echo esc_html($settings['address']); ?></p>
                <p><a href="mailto:<?php echo esc_attr($settings['email']); ?>"><?php echo esc_html($settings['email']); ?></a></p>
                <p><?php foreach (rle_phones() as $phone): ?><a href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a><br><?php endforeach; ?></p>
                <p><a href="https://wa.me/<?php echo esc_attr($settings['whatsapp']); ?>" target="_blank" rel="noopener">WhatsApp ile yazın ↗</a></p>
            </address>
        </section>
        <div class="footer-bottom">
            <span>© <?php echo esc_html(wp_date('Y')); ?> Rental Ekran. Tüm hakları saklıdır.</span>
            <span>İstanbul merkezli · Türkiye geneli</span>
        </div>
    </div>
</footer>
<a class="mobile-cta" href="<?php echo rle_link('iletisim'); ?>">Projeniz için teklif isteyin ↗</a>
    <?php
}

function rle_render_shell_open() {
    ?><!doctype html><html lang="tr"><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body class="rle-site"><a class="skip-link" href="#main">İçeriğe geç</a><?php wp_body_open(); ?>
    <?php
    rle_render_header();
    echo '<main id="main">';
    if (rle_preview()) {
        echo '<div class="preview-notice wrap">Yönetici önizlemesi — bu görünüm ziyaretçilere açık değildir.</div>';
    }
}

function rle_render_shell_close() {
    echo '</main>';
    rle_render_footer();
    wp_footer();
    echo '</body></html>';
}
