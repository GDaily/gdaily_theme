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

// 取得父分類
$parent_category = $post_data['category'][0]->category_parent ? 
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

// 準備共用參數
$template_args = [
    'final_base_color' => $post_data['final_base_color'],
    'final_light_color' => $post_data['final_light_color'],
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
    $max_size = trimImageWhitespace($server_file_path);
    
    $template_args['scale'] = 0.6 + 1 - $max_size / 192;
    $template_args['thumbnail_app_url'] = $thumbnail_app_url;
    
    get_template_part('part/single-app', get_post_format(), $template_args);
} else {
    // 一般文章
    $template_args['imagePath'] = $post_data['thumbnail_url'];
    
    get_template_part('part/single-normal', get_post_format(), $template_args);
}
?>

<style type="text/css">
h2,
h3 {
    background-color: <?php echo esc_attr($post_data['final_light_color']);
    ?> !important;
    color: <?php echo esc_attr($post_data['final_base_color']);
    ?> !important;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
}
</style>

<?php
get_footer();