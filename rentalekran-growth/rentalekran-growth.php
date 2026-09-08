<?php
/**
 * Plugin Name: Rental Ekran Growth
 * Description: Rental Ekran ürün kataloğu, proje planlayıcı ve teknik SEO katmanı. Eski içerikleri değiştirmez.
 * Version: 2.5.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */
defined('ABSPATH') || exit;
define('RLE_DIR', plugin_dir_path(__FILE__));
define('RLE_URL', plugin_dir_url(__FILE__));
require_once RLE_DIR . 'includes/catalog.php';
require_once RLE_DIR . 'includes/seo.php';
require_once RLE_DIR . 'includes/admin.php';
require_once RLE_DIR . 'includes/contact.php';
require_once RLE_DIR . 'includes/layout.php';
require_once RLE_DIR . 'includes/blog.php';

add_filter('query_vars', function ($vars) { $vars[] = 'rle_route'; return $vars; });
add_action('parse_request', function ($wp) {
    if (is_admin() || defined('REST_REQUEST') && REST_REQUEST) return;
    if (!rle_enabled()) return;
    $path = trim($wp->request, '/');
    if (isset($_GET['s']) || isset($_GET['p']) || isset($_GET['page_id']) || isset($_GET['feed']) || isset($_GET['preview']) || isset($_GET['rest_route'])) return;
    if (!array_key_exists($path, rle_routes())) return;
    $GLOBALS['rle_current_route'] = $path;
    if (!rle_original()) $wp->query_vars = array('rle_route' => $path);
    elseif ($path === 'transparan-led-ekran') $wp->query_vars = array('pagename' => 'elementor-3277');
});
function rle_route() { return isset($GLOBALS['rle_current_route']) ? $GLOBALS['rle_current_route'] : null; }
function rle_original() { return isset($_GET['rle_original']) && is_scalar($_GET['rle_original']) && (string) $_GET['rle_original'] === '1'; }
add_filter('pre_handle_404', function ($pre, $query) {
    if (rle_route() !== null && !rle_original()) {
        $query->is_404 = false; $query->is_home = false; $query->is_page = true;
        status_header(200); return true;
    }
    return $pre;
}, 10, 2);
add_action('template_redirect', function () {
    if (!rle_shell_active()) return;
    if (rle_preview()) { nocache_headers(); if(!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true); }
    global $wp;
    if (!rle_has_seo_plugin()) remove_action('wp_head', 'rel_canonical');
    // Popup headings belong to modal content, not to the standalone SEO pages.
    if (!rle_original() && function_exists('\\PopupMaker\\plugin')) {
        $controller = \PopupMaker\plugin()->get_controller('Frontend\\Popups');
        if (is_object($controller)) remove_action('wp_footer', array($controller, 'render_popups'));
    }
    if (!rle_original()) {
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    }
});
add_filter('redirect_canonical', function ($url) { return rle_route() !== null ? false : $url; });
add_filter('template_include', function ($template) {
    if (rle_original() || !rle_shell_active()) return $template;
    if (rle_route() !== null) return RLE_DIR . 'templates/page.php';
    if (is_singular('post')) return RLE_DIR . 'templates/single-post.php';
    return $template;
}, 999);
add_action('wp_enqueue_scripts', function () {
    if (!rle_shell_active()) return;
    // This standalone template uses no Elementor/theme widgets. Retain the logged-in toolbar.
    global $wp_styles, $wp_scripts;
    foreach ((array) $wp_styles->queue as $handle) if (!in_array($handle, array('admin-bar', 'dashicons'), true)) wp_dequeue_style($handle);
    foreach ((array) $wp_scripts->queue as $handle) if ($handle !== 'admin-bar') wp_dequeue_script($handle);
    wp_enqueue_style('rle-site', RLE_URL . 'assets/site.css', array(), '2.5.0');
    wp_enqueue_script('rle-planner', RLE_URL . 'assets/planner.js', array(), '2.5.0', true);
}, PHP_INT_MAX);
// No database content, credentials, theme, permalink settings or business data is changed on activation.
