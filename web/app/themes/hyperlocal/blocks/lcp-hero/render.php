<?php
/**
 * Server-rendered block: LCP Hero (Featured Image)
 */

if (!defined('ABSPATH')) {
    exit;
}

/** @var array $attributes */
/** @var string $content */

$post = get_post();
if (!$post) {
    return '';
}

// Require a featured image.
$thumb_id = get_post_thumbnail_id($post);
if (!$thumb_id) {
    return '';
}

// Build wrapper classes.
$classes = ['wp-block-hyperlocal-lcp-hero'];
if (!empty($attributes['align'])) {
    $classes[] = 'align' . sanitize_html_class($attributes['align']);
}
if (!empty($attributes['className'])) {
    $classes[] = sanitize_html_class($attributes['className']);
}

// Image markup at custom size with LCP-friendly attributes.
$img_html = wp_get_attachment_image(
    $thumb_id,
    'lcp-hero',
    false,
    [
        'class' => 'lcp-hero__image',
        'loading' => 'eager',
        'decoding' => 'async',
        'fetchpriority' => 'high',
    ]
);

// Content pieces.
$kicker = isset($attributes['kicker']) ? trim((string) $attributes['kicker']) : '';
$show_excerpt = !empty($attributes['showExcerpt']);
$title = get_the_title($post);
$excerpt_html = $show_excerpt ? get_the_excerpt($post) : '';

ob_start();
?>
<section class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <figure class="lcp-hero__media">
        <?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </figure>
    <header class="lcp-hero__header">
        <?php if ($kicker !== '') : ?>
            <p class="lcp-hero__kicker"><?php echo esc_html($kicker); ?></p>
        <?php endif; ?>
        <h1 class="lcp-hero__title"><?php echo esc_html($title); ?></h1>
        <?php if ($excerpt_html) : ?>
            <div class="lcp-hero__excerpt"><?php echo wp_kses_post($excerpt_html); ?></div>
        <?php endif; ?>
    </header>
</section>
<?php
return (string) ob_get_clean();

