<?php

/**
 * 添加資源預載入和性能優化
 */
function add_resource_preloads()
{


    // 預載入關鍵 JavaScript
    $app_js_time = filemtime(get_template_directory() . '/js/app.js');
    echo '<link rel="modulepreload" href="' . tailpress_asset('js/app.js') . '">';

    // DNS 預解析常用外部域名
    echo '<link rel="dns-prefetch" href="//www.google-analytics.com">';
    echo '<link rel="dns-prefetch" href="//googletagmanager.com">';
    echo '<link rel="dns-prefetch" href="//pagead2.googlesyndication.com">';
}
add_action('wp_head', 'add_resource_preloads', 2);

/**
 * 進階性能優化
 */
// 設定快取標頭
function add_cache_headers()
{
    if (!is_admin() && !is_user_logged_in()) {
        // 為靜態資源設定較長的快取時間
        header('Cache-Control: public, max-age=31536000, immutable'); // 1年
        header('Pragma: public');

        // 設定 ETags
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers['If-None-Match'])) {
                header('HTTP/1.1 304 Not Modified');
                exit;
            }
        }
    }
}

// 壓縮輸出
function enable_gzip_compression()
{
    if (!ob_get_level() && extension_loaded('zlib') && !headers_sent()) {
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ob_start('ob_gzhandler');
        }
    }
}
add_action('init', 'enable_gzip_compression');

// 預載入關鍵頁面
function add_page_prefetch()
{
    if (is_home() || is_front_page()) {
        // 預載入重要頁面
        echo '<link rel="prefetch" href="' . get_permalink(get_option('page_for_posts')) . '">';

        // 預載入最新文章
        $recent_posts = get_posts(array(
            'numberposts' => 3,
            'post_status' => 'publish'
        ));

        foreach ($recent_posts as $post) {
            echo '<link rel="prefetch" href="' . get_permalink($post->ID) . '">';
        }
    }
}
add_action('wp_head', 'add_page_prefetch', 10);

// 減少 DOM 大小 - 移除不必要的元素
function clean_wp_head()
{
    // 移除不必要的 meta 標籤
    remove_action('wp_head', 'wp_resource_hints', 2);
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // 移除 WordPress 預設的 preload
    remove_action('wp_head', 'wp_preload_resources', 1);
}
add_action('init', 'clean_wp_head');

// 禁用 WordPress 心跳 API（減少伺服器負載）
function stop_heartbeat()
{
    wp_deregister_script('heartbeat');
}
add_action('init', 'stop_heartbeat', 1);

// 限制修訂版本數量
if (!defined('WP_POST_REVISIONS')) {
    define('WP_POST_REVISIONS', 3);
}

/* 優化資源載入 (LCP/FCP) */

// 為特定腳本添加 defer 屬性
function add_defer_attribute($tag, $handle)
{
    // 需要 defer 的腳本 handle
    $defer_scripts = array('tailpress', 'wpdiscuz-combo-js');

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer="defer" src', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'add_defer_attribute', 10, 2);

// 完全禁用 WPDiscuz Font Awesome（最高優先級 999999）
function disable_wpdiscuz_font_awesome()
{
    wp_dequeue_style('wpdiscuz-font-awesome');
    wp_deregister_style('wpdiscuz-font-awesome');
}
add_action('wp_enqueue_scripts', 'disable_wpdiscuz_font_awesome', 999999);

// 將 WPDiscuz 樣式移到頁腳載入
function defer_wpdiscuz_styles()
{
    if (is_admin() || !is_single()) {
        return;
    }

    global $wp_styles;

    // 要延遲加載的樣式
    $defer_styles = array('wpdiscuz-combo-css', 'wpdiscuz-frontend-css');

    foreach ($defer_styles as $handle) {
        if (isset($wp_styles->registered[$handle])) {
            // 在 wp_head 中移除此樣式
            wp_dequeue_style($handle);
        }
    }
}
add_action('wp_enqueue_scripts', 'defer_wpdiscuz_styles', 999);

// 在頁腳中重新加入 WPDiscuz 樣式
function load_wpdiscuz_styles_in_footer()
{
    if (is_admin() || !is_single()) {
        return;
    }

    $defer_styles = array('wpdiscuz-combo-css', 'wpdiscuz-frontend-css');

    foreach ($defer_styles as $handle) {
        wp_enqueue_style($handle);
    }
}
add_action('wp_footer', 'load_wpdiscuz_styles_in_footer', 1);
