<?php
$before = (int) wp_count_posts()->future;
delete_option('rle_blog_sync_version');
rle_sync_blog_posts();
$after = (int) wp_count_posts()->future;
if ($before !== $after) WP_CLI::error("Scheduled posts duplicated: $before -> $after");
WP_CLI::success('Blog resync keeps scheduled post count stable');
