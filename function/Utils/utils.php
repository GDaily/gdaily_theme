<?php

// 返回空字符串來刪除省略號
function custom_excerpt_more($more)
{
    return '';
}
add_filter('excerpt_more', 'custom_excerpt_more');


/*多久之前的時間*/
function meks_time_ago()
{
    return human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('前');
}


/*
刪除medium_large
*/

add_filter('intermediate_image_sizes', function ($sizes) {
    return array_filter($sizes, function ($val) {
        return 'medium_large' !== $val; // Filter out 'medium_large'
    });
});


/** delete url */
function crunchify_disable_comment_url($fields)
{
    unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'crunchify_disable_comment_url');


function load_jquery_for_wpdiscuz()
{
    if (function_exists('wpdiscuz') && is_single()) {
        wp_enqueue_script('jquery');
    }
}
add_action('wp_footer', 'load_jquery_for_wpdiscuz');
