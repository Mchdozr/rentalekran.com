<?php
defined('ABSPATH') || exit;
require_once RLE_DIR . 'includes/layout.php';
the_post();
$settings = rle_settings();
rle_render_shell_open();
?>
<nav class="breadcrumbs wrap" aria-label="İçerik yolu">
    <a href="<?php echo rle_link(''); ?>">Ana sayfa</a>
    <span aria-hidden="true">/</span>
    <a href="<?php echo rle_link('blog-sayfasi'); ?>">Blog</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
</nav>
<article <?php post_class('blog-article wrap rle-fade-in'); ?>>
    <header class="page-intro blog-header">
        <p class="eyebrow"><span class="red-dot"></span> LED EKRAN BLOG · <?php echo esc_html(get_the_date('j F Y')); ?></p>
        <h1><?php the_title(); ?></h1>
        <?php if (has_excerpt()): ?><p class="blog-lead"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
    </header>
    <?php if (has_post_thumbnail()): ?>
    <figure class="blog-hero-media">
        <?php the_post_thumbnail('large', array('class' => 'blog-hero-image', 'loading' => 'eager', 'decoding' => 'async')); ?>
    </figure>
    <?php endif; ?>
    <div class="blog-content rle-prose">
        <?php the_content(); ?>
    </div>
    <footer class="blog-footer-meta">
        <?php $cats = get_the_category(); if ($cats): ?>
        <p class="blog-tags"><?php foreach ($cats as $cat): ?><span class="blog-tag"><?php echo esc_html($cat->name); ?></span><?php endforeach; ?></p>
        <?php endif; ?>
        <div class="blog-share-cta">
            <a class="button" href="<?php echo rle_link('led-ekran-teklif'); ?>">Projeniz için teklif isteyin ↗</a>
            <a class="text-link" href="<?php echo rle_link('blog-sayfasi'); ?>">Tüm yazılar →</a>
        </div>
    </footer>
</article>
<?php $related = rle_blog_related_posts(3); if ($related): ?>
<section class="section wrap blog-related rle-stagger">
    <div class="section-heading"><div><p class="eyebrow">DEVAM EDİN</p><h2>İlgili blog yazıları</h2></div></div>
    <div class="blog-grid">
        <?php foreach ($related as $post): setup_postdata($post); $image = get_the_post_thumbnail_url($post, 'medium'); ?>
        <article class="blog-card rle-reveal">
            <?php if ($image): ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" loading="lazy" decoding="async"><?php else: ?>
            <div class="blog-card-placeholder" aria-hidden="true"><span>LED</span></div><?php endif; ?>
            <div>
                <p class="eyebrow"><?php echo esc_html(get_the_date('j F Y', $post)); ?></p>
                <h2><a href="<?php echo esc_url(get_permalink($post)); ?>"><?php echo esc_html(get_the_title($post)); ?></a></h2>
                <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt($post) ?: $post->post_content), 22)); ?></p>
                <a class="text-link" href="<?php echo esc_url(get_permalink($post)); ?>">Yazıyı okuyun →</a>
            </div>
        </article>
        <?php endforeach; wp_reset_postdata(); ?>
    </div>
</section>
<?php endif; ?>
<section class="wrap"><section class="project-cta"><div><p class="eyebrow">BİR EKRANDAN FAZLASI</p><h2>Ölçüyü siz verin.<br>Seçenekleri birlikte netleştirelim.</h2></div><a class="button light" href="<?php echo rle_link('led-ekran-teklif'); ?>">Projeniz için teklif isteyin <span aria-hidden="true">↗</span></a></section></section>
<?php
rle_render_shell_close();
