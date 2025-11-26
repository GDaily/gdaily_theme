<?php

/* === 安全防護措施 === */

// 隱藏 WordPress 版本號
remove_action('wp_head', 'wp_generator');
function remove_version_strings($src)
{
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if (!empty($query['ver']) && $query['ver'] === $wp_version) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'remove_version_strings');
add_filter('style_loader_src', 'remove_version_strings');

// 禁用文件編輯
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// 移除不必要的 meta 標籤
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

// 禁用 WordPress Emojis
function disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'disable_emojis');

// 禁用作者頁面（防止用戶名洩露）
function disable_author_page()
{
    global $wp_query;
    if (is_author()) {
        $wp_query->set_404();
        status_header(404);
    }
}
add_action('wp', 'disable_author_page');

// 移除 WordPress 註釋中的版本信息
function remove_html_comments($content)
{
    return preg_replace('/<!--(.|\s)*?-->/', '', $content);
}
add_filter('the_content', 'remove_html_comments');

// 禁用 XML-RPC pingbacks
function filter_xmlrpc_methods($methods)
{
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);
    return $methods;
}
add_filter('xmlrpc_methods', 'filter_xmlrpc_methods');

// 安全標頭
function add_security_headers()
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
add_action('send_headers', 'add_security_headers');

# 禁用 xmlrpc
add_filter('xmlrpc_enabled', '__return_false');

/*關閉Pingbacks*/
function no_self_ping(&$links)
{
    $home = get_option('home');
    foreach ($links as $l => $link)
        if (0 === strpos($link, $home))
            unset($links[$l]);
}

add_action('pre_ping', 'no_self_ping');
