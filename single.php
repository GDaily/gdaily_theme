<?php

/**
 * Single post template with enhanced SEO structure
 */

// 初始化數據
$post_data = [
    'category' => get_the_category(),
    'base_color_default' => carbon_get_post_meta($post->ID, 'tailwind_hex_base_color'),
    'light_color_default' => carbon_get_post_meta($post->ID, 'tailwind_hex_light_color'),
    'base_color_custom' => carbon_get_post_meta($post->ID, 'tailwind_hex_base_color_custom'),
    'light_color_custom' => carbon_get_post_meta($post->ID, 'tailwind_hex_light_color_custom'),
    'final_base_color' => '',
    'final_light_color' => '',
    'adsense_enable' => carbon_get_post_meta($post->ID, 'adsense_enable'),
    'size' => [800, 400],
    'thumbnail_url' => ''
];

// 當 custom 值不為空時，使用 custom 的數值，否則使用 default 值
$post_data['final_base_color'] = !empty($post_data['base_color_custom']) ?
    $post_data['base_color_custom'] :
    $post_data['base_color_default'];

$post_data['final_light_color'] = !empty($post_data['light_color_custom']) ?
    $post_data['light_color_custom'] :
    $post_data['light_color_default'];

// 檢查是否應該渲染顏色樣式（排除空值和 #ffffff）
$should_render_base_style = !empty($post_data['final_base_color']) &&
    $post_data['final_base_color'] !== '#ffffff' &&
    $post_data['final_base_color'] !== '#FFFFFF';
$should_render_light_style = !empty($post_data['final_light_color']) &&
    $post_data['final_light_color'] !== '#ffffff' &&
    $post_data['final_light_color'] !== '#FFFFFF';

// 取得父分類 (安全檢查)
$parent_category = !empty($post_data['category']) && $post_data['category'][0]->category_parent ?
    get_category($post_data['category'][0]->category_parent) : null;

// 是否為APP分類
$is_app_category = $parent_category && $parent_category->slug === 'app';

// 取得縮圖
if (has_post_thumbnail($post->ID)) {
    $post_data['thumbnail_url'] = wp_get_attachment_image_src(
        get_post_thumbnail_id($post->ID),
        $post_data['size']
    )[0];
}

get_header();

// 檢查是否有文章
if (have_posts()) :
    // 準備共用參數
    $template_args = [
        'post_id' => $post->ID,
        'final_base_color' => $post_data['final_base_color'],
        'final_light_color' => $post_data['final_light_color'],
        'should_render_base_style' => $should_render_base_style,
        'should_render_light_style' => $should_render_light_style,
        'thumbnail_url' => $post_data['thumbnail_url'],
        'adsense_enable' => $post_data['adsense_enable']
    ];

    if ($is_app_category) {
        // APP 分類特殊處理
        $app_size = [192, 192];
        $thumbnail_app_url = wp_get_attachment_image_src(
            get_post_thumbnail_id($post->ID),
            $app_size
        )[0];

        $parsed_url = parse_url($thumbnail_app_url);
        $relative_path = str_replace($parsed_url['scheme'] . '://' . $parsed_url['host'], '', $thumbnail_app_url);
        $server_file_path = $_SERVER['DOCUMENT_ROOT'] . $relative_path;

        // 安全調用圖片處理函數
        $max_size = 192; // 預設值
        if (function_exists('trimImageWhitespace')) {
            $max_size = trimImageWhitespace($server_file_path);
        }

        $template_args['scale'] = 0.6 + 1 - $max_size / 192;
        $template_args['thumbnail_app_url'] = $thumbnail_app_url;
        $template_args['image_width'] = $app_size[0];
        $template_args['image_height'] = $app_size[1];

        get_template_part('part/single-app', get_post_format(), $template_args);
    } else {
        // 一般文章
        $template_args['imagePath'] = $post_data['thumbnail_url'];
        $template_args['image_width'] = $post_data['size'][0];
        $template_args['image_height'] = $post_data['size'][1];

        get_template_part('part/single-normal', get_post_format(), $template_args);
    }
endif;
?>

<style type="text/css">
    <?php if ($should_render_base_style && $should_render_light_style): ?>h2 {
        background-color: <?php echo esc_attr($post_data['final_light_color']);
                            ?> !important;
        color: <?php echo esc_attr($post_data['final_base_color']);
                ?> !important;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }

    h3 {
        color: <?php echo esc_attr($post_data['final_base_color']);
                ?> !important;
    }

    <?php endif;
    ?>
</style>

<?php
get_footer();
