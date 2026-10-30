<?php
// Run through WP-CLI against a disposable/local WordPress database.
$original = get_option('blog_public', null);
update_option('blog_public', 0);

$settings = rle_settings();
$settings['live_enabled'] = true;
rle_sanitize_settings($settings);
$actual = (int) get_option('blog_public');

if ($original === null) {
    delete_option('blog_public');
} else {
    update_option('blog_public', $original);
}

if ($actual !== 1) {
    WP_CLI::error('Publishing the Rental Ekran site must enable WordPress search visibility.');
}

update_option('rle_settings', array_merge(rle_settings(), array('live_enabled' => true)));
update_option('blog_public', 0);
rle_sync_search_visibility();
$repaired = (int) get_option('blog_public');

if ($repaired !== 1) {
    WP_CLI::error('An already-live Rental Ekran site must repair disabled search visibility.');
}

WP_CLI::success('Publishing enables WordPress search visibility.');
