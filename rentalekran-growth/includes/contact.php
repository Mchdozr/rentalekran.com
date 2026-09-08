<?php
defined('ABSPATH') || exit;

function rle_contact_redirect($route, $status) {
    $url = rle_navigation_url($route);
    $url = add_query_arg($status === 'ok' ? 'rle_sent' : 'rle_error', '1', $url);
    wp_safe_redirect($url . '#planlayici');
    exit;
}

add_action('template_redirect', function () {
    $path = trim((string) ($GLOBALS['wp']->request ?? ''), '/');
    $redirects = array(
        'ornek-sayfa' => '/',
        'elementor-3277' => '/transparan-led-ekran/',
    );
    if (isset($redirects[$path])) {
        wp_safe_redirect(home_url($redirects[$path]), 301);
        exit;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || empty($_POST['rle_contact'])) return;
    $route = rle_route();
    if (!in_array($route, array('iletisim', 'led-ekran-teklif'), true)) return;
    if (!empty($_POST['rle_website'])) {
        rle_contact_redirect($route, 'ok');
    }
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'rle_contact')) {
        rle_contact_redirect($route, 'error');
    }
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '0';
    $key = 'rle_mail_' . md5($ip);
    $hits = (int) get_transient($key);
    if ($hits >= 5) {
        rle_contact_redirect($route, 'error');
    }
    $name = sanitize_text_field(wp_unslash(isset($_POST['contact_name']) ? $_POST['contact_name'] : ''));
    $phone = sanitize_text_field(wp_unslash(isset($_POST['contact_phone']) ? $_POST['contact_phone'] : ''));
    $email = sanitize_email(wp_unslash(isset($_POST['contact_email']) ? $_POST['contact_email'] : ''));
    $product = sanitize_text_field(wp_unslash(isset($_POST['product']) ? $_POST['product'] : ''));
    $request = sanitize_text_field(wp_unslash(isset($_POST['request']) ? $_POST['request'] : ''));
    $environment = sanitize_text_field(wp_unslash(isset($_POST['environment']) ? $_POST['environment'] : ''));
    $city = sanitize_text_field(wp_unslash(isset($_POST['city']) ? $_POST['city'] : ''));
    $width = sanitize_text_field(wp_unslash(isset($_POST['width']) ? $_POST['width'] : ''));
    $height = sanitize_text_field(wp_unslash(isset($_POST['height']) ? $_POST['height'] : ''));
    $distance = sanitize_text_field(wp_unslash(isset($_POST['distance']) ? $_POST['distance'] : ''));
    $note = sanitize_textarea_field(wp_unslash(isset($_POST['note']) ? $_POST['note'] : ''));
    if ($name === '' || !is_email($email) || strlen(preg_replace('/\D/', '', $phone)) < 8) {
        rle_contact_redirect($route, 'error');
    }
    $product_label = $product;
    foreach (rle_products() as $item) {
        if ($item['slug'] === $product) {
            $product_label = $item['name'];
            break;
        }
    }
    if ($product === 'kararsiz') $product_label = 'Seçim için destek istiyorum';
    $settings = rle_settings();
    $body = implode("\n", array(
        'Yeni LED ekran teklif talebi',
        'Ad: ' . $name,
        'Telefon: ' . $phone,
        'E-posta: ' . $email,
        'Ürün: ' . $product_label,
        'Talep: ' . $request,
        'Ortam: ' . $environment,
        'Şehir: ' . ($city !== '' ? $city : 'Belirtilmedi'),
        'Ölçü: ' . $width . ' × ' . $height . ' m',
        'İzleme mesafesi: ' . ($distance !== '' ? $distance : 'Belirtilmedi'),
        'Not: ' . ($note !== '' ? $note : 'Yok'),
        'Sayfa: ' . $route,
    ));
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    $sent = wp_mail($settings['email'], 'LED ekran teklif talebi — ' . $name, $body, $headers);
    set_transient($key, $hits + 1, HOUR_IN_SECONDS);
    rle_contact_redirect($route, $sent ? 'ok' : 'error');
}, 2);

function rle_contact_notice() {
    if (isset($_GET['rle_sent'])) {
        echo '<p class="rle-notice ok" role="status">Talebiniz iletildi. En kısa sürede dönüş yapacağız.</p>';
        return;
    }
    if (isset($_GET['rle_error'])) {
        echo '<p class="rle-notice err" role="alert">Talep gönderilemedi. Ad, telefon ve e-postayı kontrol edip tekrar deneyin veya doğrudan arayın.</p>';
    }
}
