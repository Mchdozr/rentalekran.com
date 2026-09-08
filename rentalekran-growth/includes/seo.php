<?php
defined('ABSPATH') || exit;
function rle_has_seo_plugin() { return defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION') || defined('THE_SEO_FRAMEWORK_VERSION'); }
function rle_description() {
    $route = rle_route();
    if ($route !== null) return rle_routes()[$route]['description'];
    if (is_singular()) {
        $post = get_queried_object();
        $text = $post->post_excerpt ?: wp_strip_all_tags(strip_shortcodes($post->post_content));
        return wp_html_excerpt(preg_replace('/\s+/u', ' ', $text), 155, '…');
    }
    return '';
}
function rle_legacy_seo() {
    $data=array(
        'urunlerimiz'=>array('title'=>'LED Ekran Modelleri | Rental Ekran','description'=>'Rental, Front, AIR / STAR, COB, GOB, Floor ve transparan LED ekran ürünlerini kullanım alanına göre inceleyin.'),
        'iletisim'=>array('title'=>'LED Ekran İletişim | Rental Ekran İstanbul','description'=>'LED ekran satış ve kiralama projeniz için Rental Ekran İstanbul iletişim bilgilerine ulaşın.'),
        'hakkimizda'=>array('title'=>'Hakkımızda | Rental Ekran','description'=>'Rental Ekran hakkında bilgi alın; LED ekran çözümleri ve ürün hizmet alanlarını inceleyin.'),
        'firma-bilgilerimiz'=>array('title'=>'Firma Bilgileri | Rental Ekran','description'=>'Rental Ekran firma ve iletişim bilgilerine ulaşın.'),
        'blog-sayfasi'=>array('title'=>'LED Ekran Blog | Rental Ekran','description'=>'LED ekran teknolojileri, kullanım alanları ve proje seçimi hakkında içerikleri inceleyin.'),
        'rental-led-ekran'=>array('title'=>'Rental LED Ekran | Rental Ekran','description'=>'Rental LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'front'=>array('title'=>'Front LED Ekran | Rental Ekran','description'=>'Front LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'air-star'=>array('title'=>'AIR / STAR LED Ekran | Rental Ekran','description'=>'AIR / STAR LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'cob'=>array('title'=>'COB LED Ekran | Rental Ekran','description'=>'COB LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'gob'=>array('title'=>'GOB LED Ekran | Rental Ekran','description'=>'GOB LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'floor'=>array('title'=>'Floor Zemin LED Ekran | Rental Ekran','description'=>'Floor zemin LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'elementor-3277'=>array('title'=>'Transparan LED Ekran | Rental Ekran','description'=>'Transparan LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.'),
        'transparan-led-ekran'=>array('title'=>'Transparan LED Ekran | Rental Ekran','description'=>'Transparan LED ekran serisinin mevcut ürün, görsel ve teknik tablolarını inceleyin.')
    );
    $post=get_queried_object();
    return is_object($post) && isset($post->post_name,$data[$post->post_name]) ? $data[$post->post_name] : null;
}
add_filter('pre_get_document_title', function ($title) { $meta=rle_route() !== null ? rle_routes()[rle_route()] : rle_legacy_seo(); return !empty($meta['title']) ? $meta['title'] : $title; }, 999);
add_filter('wp_robots', function ($robots) {
    if (rle_preview() || (rle_enabled() && (rle_original() || is_search() || is_404() || is_attachment() || is_author() || is_page(rle_demo_ids())))) {
        $robots['noindex'] = true; unset($robots['index']);
    }
    return $robots;
});
function rle_schema($slug) {
    $settings = rle_settings(); $page = rle_routes()[$slug]; $url = rle_canonical_for($slug); $home = home_url('/');
    $org = array('@type'=>'Organization','@id'=>$home.'#organization','name'=>'Rental Ekran','url'=>$home,'telephone'=>$settings['phone'],'email'=>$settings['email']);
    $web = array('@type'=>'WebSite','@id'=>$home.'#website','url'=>$home,'name'=>'Rental Ekran','publisher'=>array('@id'=>$home.'#organization'),'inLanguage'=>'tr-TR');
    $entry = array('@type'=> $slug === 'urunlerimiz' ? 'CollectionPage' : 'WebPage','@id'=>$url.'#webpage','url'=>$url,'name'=>$page['title'],'description'=>$page['description'],'isPartOf'=>array('@id'=>$home.'#website'),'inLanguage'=>'tr-TR');
    $nodes = array($org,$web,$entry);
    if ($slug !== '') {
        $crumbs = array(array('@type'=>'ListItem','position'=>1,'name'=>'Ana sayfa','item'=>$home));
        if (rle_product($slug)) $crumbs[] = array('@type'=>'ListItem','position'=>2,'name'=>'LED ekran modelleri','item'=>rle_canonical_for('urunlerimiz'));
        $crumbs[] = array('@type'=>'ListItem','position'=>count($crumbs)+1,'name'=>$page['name'],'item'=>$url);
        $nodes[] = array('@type'=>'BreadcrumbList','@id'=>$url.'#breadcrumb','itemListElement'=>$crumbs);
        $nodes[2]['breadcrumb'] = array('@id'=>$url.'#breadcrumb');
    }
    if ($slug === 'urunlerimiz') {
        $items = array(); foreach (rle_products() as $i=>$p) $items[] = array('@type'=>'ListItem','position'=>$i+1,'name'=>$p['name'],'url'=>rle_canonical_for($p['slug']));
        $nodes[] = array('@type'=>'ItemList','itemListElement'=>$items);
    }
    return array('@context'=>'https://schema.org','@graph'=>$nodes);
}
add_action('wp_head', function () {
    if (!rle_enabled()) return;
    $ga = isset(rle_settings()['ga_id']) ? rle_settings()['ga_id'] : '';
    if ($ga && preg_match('/^G-[A-Z0-9]+$/', $ga)) {
        echo '<script async src="https://www.googletagmanager.com/gtag/js?id='.esc_attr($ga).'"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","'.esc_js($ga).'");</script>'."\n";
    } elseif ($ga && preg_match('/^GTM-[A-Z0-9]+$/', $ga)) {
        echo '<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src="https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);})(window,document,"script","dataLayer","'.esc_js($ga).'");</script>'."\n";
    }
    if (rle_has_seo_plugin()) return; // Avoid competing canonical/schema publishers.
    $route = rle_route(); $legacy=rle_legacy_seo(); $desc = $legacy && !empty($legacy['description']) ? $legacy['description'] : rle_description();
    if ($desc) echo '<meta name="description" content="'.esc_attr($desc).'">' . "\n";
    if ($route === null) return;
    $page = rle_routes()[$route]; $url = rle_canonical_for($route);
    echo '<link rel="canonical" href="'.esc_url($url).'">' . "\n";
    echo '<meta property="og:locale" content="tr_TR"><meta property="og:type" content="website">' . "\n";
    foreach (array('title'=>$page['title'],'description'=>$desc,'url'=>$url,'site_name'=>'Rental Ekran') as $key=>$value) echo '<meta property="og:'.esc_attr($key).'" content="'.esc_attr($value).'">' . "\n";
    echo '<meta name="twitter:card" content="summary">' . "\n";
    echo '<script type="application/ld+json">'.wp_json_encode(rle_schema($route), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>' . "\n";
}, 5);
// Core sitemaps remain responsible for posts and other genuine content.
add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
    unset($taxonomies['liquid-portfolio-category']);
    return $taxonomies;
});
add_filter('wp_sitemaps_post_types', function ($types) {
    foreach (array_keys((array) $types) as $name) {
        if (strpos($name, 'liquid-') === 0 || $name === 'metform-form') unset($types[$name]);
    }
    return $types;
});
add_filter('wp_sitemaps_posts_query_args', function ($args, $post_type) {
    if (!rle_live()) return $args;
    if ($post_type !== 'page') return $args;
    $exclude = rle_demo_ids();
    foreach (rle_routes() as $slug=>$page) {
        $post = $slug === '' ? get_post(get_option('page_on_front')) : get_page_by_path($slug === 'transparan-led-ekran' ? 'elementor-3277' : $slug);
        if ($post) $exclude[] = $post->ID;
    }
    $args['post__not_in'] = array_unique(array_merge(isset($args['post__not_in']) ? $args['post__not_in'] : array(), $exclude));
    return $args;
}, 10, 2);
add_filter('wp_sitemaps_add_provider', function ($provider, $name) { return rle_live() && $name === 'users' ? false : $provider; }, 10, 2);
add_action('init', function () {
    if (!rle_live()) return;
    if (!class_exists('WP_Sitemaps_Provider')) return;
    if (!class_exists('RLE_Sitemap_Provider')) {
        class RLE_Sitemap_Provider extends WP_Sitemaps_Provider {
            public function __construct() { $this->name = 'rentalekran'; $this->object_type = 'rentalekran'; }
            public function get_url_list($page_num, $object_subtype = '') {
                if ((int)$page_num !== 1) return array();
                $urls = array(); foreach (rle_routes() as $slug=>$page) {
                    if ($slug === 'ornek-sayfa' || $slug === 'elementor-3277') continue;
                    $urls[] = array('loc'=>rle_canonical_for($slug));
                }
                return $urls;
            }
            public function get_max_num_pages($object_subtype = '') { return 1; }
        }
    }
    wp_register_sitemap_provider('rentalekran', new RLE_Sitemap_Provider());
});
