<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// 1) Remove WordPress emoji scripts and oEmbed discovery links.
add_action('init', function () {
    // Emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url', '__return_false');

    // oEmbed discovery and host JS
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
});

// 2) Register image sizes.
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
    add_image_size('lcp-hero', 1536, 864, true);
});

// 3) Auto-register server-rendered blocks from /blocks.
function hyperlocal_register_server_rendered_blocks(): void
{
    $blocks_dir = get_stylesheet_directory() . '/blocks';
    if (!is_dir($blocks_dir)) {
        return;
    }

    $block_dirs = glob($blocks_dir . '/*', GLOB_ONLYDIR) ?: [];

    foreach ($block_dirs as $dir) {
        $block_json  = $dir . '/block.json';
        $render_file = $dir . '/render.php';

        if (!file_exists($block_json)) {
            continue;
        }

        $args = [];

        if (file_exists($render_file)) {
            $args['render_callback'] = function ($attributes = [], $content = '', $block = null) use ($render_file) {
                ob_start();
                include $render_file;
                return (string) ob_get_clean();
            };
        }

        register_block_type($dir, $args);
    }
}
add_action('init', 'hyperlocal_register_server_rendered_blocks');

// Preload the featured image when the LCP Hero block is present on singular content.
function hyperlocal_preload_lcp_hero_featured_image(): void
{
    if (!is_singular()) {
        return;
    }

    $post = get_queried_object();
    if (!($post instanceof WP_Post)) {
        return;
    }

    // Only preload when the block is used in post content.
    if (!has_block('hyperlocal/lcp-hero', $post)) {
        return;
    }

    $attachment_id = get_post_thumbnail_id($post);
    if (!$attachment_id) {
        return;
    }

    $src    = wp_get_attachment_image_url($attachment_id, 'lcp-hero');
    if (!$src) {
        return;
    }

    $srcset = wp_get_attachment_image_srcset($attachment_id, 'lcp-hero');
    $sizes  = '100vw';

    echo '<link rel="preload" as="image" href="' . esc_url($src) . '"' .
         ($srcset ? ' imagesrcset="' . esc_attr($srcset) . '"' : '') .
         ' imagesizes="' . esc_attr($sizes) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_head', 'hyperlocal_preload_lcp_hero_featured_image', 1);
