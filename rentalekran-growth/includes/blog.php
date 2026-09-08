<?php
defined('ABSPATH') || exit;

function rle_blog_posts_data() {
    static $data;
    if ($data === null) {
        $path = RLE_DIR . 'includes/blog-posts.json';
        $data = file_exists($path) ? json_decode(file_get_contents($path), true) : array();
        if (!is_array($data)) $data = array();
    }
    return $data;
}

function rle_blog_featured_url($relative) {
    if (!$relative) return '';
    if (strpos($relative, 'http') === 0) return $relative;
    return RLE_URL . ltrim($relative, '/');
}

function rle_sync_blog_posts() {
    if (!function_exists('wp_insert_post')) return;
    $target = '2.4.0';
    if (get_option('rle_blog_sync_version') === $target) return;
    $GLOBALS['rle_syncing_blog'] = true;
    foreach (rle_blog_posts_data() as $item) {
        if (empty($item['slug']) || empty($item['title'])) continue;
        $existing = get_posts(array(
            'name' => $item['slug'],
            'post_type' => 'post',
            'post_status' => array('publish', 'draft', 'pending'),
            'numberposts' => 1,
        ));
        $postarr = array(
            'post_title' => $item['title'],
            'post_name' => $item['slug'],
            'post_content' => isset($item['content']) ? $item['content'] : '',
            'post_excerpt' => isset($item['excerpt']) ? $item['excerpt'] : '',
            'post_status' => 'publish',
            'post_type' => 'post',
        );
        if (!empty($item['date'])) $postarr['post_date'] = $item['date'] . ' 10:00:00';
        if ($existing) {
            $postarr['ID'] = $existing[0]->ID;
            wp_update_post($postarr);
            $post_id = $existing[0]->ID;
        } else {
            $post_id = wp_insert_post($postarr, true);
        }
        if (is_wp_error($post_id) || !$post_id) continue;
        if (!empty($item['category'])) {
            $term = term_exists($item['category'], 'category');
            if (!$term) $term = wp_insert_term($item['category'], 'category');
            if (!is_wp_error($term)) wp_set_post_categories($post_id, array((int) $term['term_id']));
        }
        if (!empty($item['featured'])) {
            rle_attach_featured_from_plugin($post_id, $item['featured']);
        }
    }
    update_option('rle_blog_sync_version', $target);
    unset($GLOBALS['rle_syncing_blog']);
}

add_filter('map_meta_cap', function ($caps, $cap) {
    if (empty($GLOBALS['rle_syncing_blog'])) return $caps;
    if (in_array($cap, array('edit_post', 'publish_post', 'upload_files', 'edit_posts'), true)) return array('exist');
    return $caps;
}, 10, 2);

add_action('init', function () {
    if (!rle_enabled()) return;
    rle_sync_blog_posts();
}, 25);

function rle_attach_featured_from_plugin($post_id, $relative) {
    $path = RLE_DIR . ltrim($relative, '/');
    if (!file_exists($path)) return;
    $filename = basename($path);
    $existing = get_posts(array(
        'post_type' => 'attachment',
        'meta_key' => '_rle_asset',
        'meta_value' => $relative,
        'numberposts' => 1,
    ));
    if ($existing) {
        set_post_thumbnail($post_id, $existing[0]->ID);
        return;
    }
    $upload = wp_upload_bits($filename, null, file_get_contents($path));
    if (!empty($upload['error'])) return;
    $attachment = array(
        'post_mime_type' => wp_check_filetype($filename)['type'],
        'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
    );
    $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
    if (!$attach_id || is_wp_error($attach_id)) return;
    update_post_meta($attach_id, '_rle_asset', $relative);
    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $upload['file']));
    set_post_thumbnail($post_id, $attach_id);
}

function rle_clean_blog_content($content) {
    if (!rle_shell_active() || !is_singular('post')) return $content;
    $content = preg_replace('/<aside[^>]*>.*?<\/aside>/is', '', $content);
    $content = preg_replace('/<div class="elementor-widget-container">\s*<\/div>/', '', $content);
    return $content;
}
add_filter('the_content', 'rle_clean_blog_content', 8);

function rle_blog_related_posts($limit = 3) {
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => $limit + 1,
        'exclude' => array(get_the_ID()),
    ));
    return array_slice($posts, 0, $limit);
}

function rle_blog_post_schema() {
    if (!rle_shell_active() || !is_singular('post')) return;
    $url = get_permalink();
    $data = array(
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => get_the_title(),
        'datePublished' => get_the_date('c'),
        'dateModified' => get_the_modified_date('c'),
        'author' => array('@type' => 'Organization', 'name' => 'Rental Ekran'),
        'publisher' => array('@type' => 'Organization', 'name' => 'Rental Ekran', 'url' => home_url('/')),
        'mainEntityOfPage' => $url,
        'inLanguage' => 'tr-TR',
    );
    if (has_post_thumbnail()) $data['image'] = get_the_post_thumbnail_url(null, 'large');
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'rle_blog_post_schema', 6);
