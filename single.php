<?php
/**
 * Single post template
 */

// 初始化數據
$post_data = [
    'category' => get_the_category(),
    'tailwind_hex_base_color' => carbon_get_post_meta($post->ID, 'tailwind_hex_base_color'),
    'tailwind_hex_light_color' => carbon_get_post_meta($post->ID, 'tailwind_hex_light_color'),
    'adsense_enable' => carbon_get_post_meta($post->ID, 'adsense_enable'),
    'size' => [800, 400],
    'thumbnail_url' => ''
];

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

// 在第二個標題前插入廣告的函式
function insert_adsense_before_second_heading($content) {
    preg_match_all('/<h[23][^>]*>.*?<\/h[23]>/i', $content, $matches, PREG_OFFSET_CAPTURE);
    
    if (isset($matches[0][1])) {
        $insert_pos = $matches[0][1][1];
        ob_start();
        get_template_part('part/adsense/adsense_content');
        $insert_html = ob_get_clean();
        $content = substr_replace($content, $insert_html, $insert_pos, 0);
    }
    
    return $content;
}

// 註冊內容過濾器
add_filter('the_content', 'insert_adsense_before_second_heading');

get_header();

// 準備共用參數
$template_args = [
    'tailwind_hex_base_color' => $post_data['tailwind_hex_base_color'],
    'tailwind_hex_light_color' => $post_data['tailwind_hex_light_color'],
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
    $relative_path = str_replace($parsed_url['scheme'] . '://' . $parsed_url['host'], '.', $thumbnail_app_url);
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

get_footer();