<?php

/*自訂Feed*/

// 優先註冊自訂 feed，確保在其他 hook 之前執行
add_action('init', 'customRSS', 5);
function customRSS()
{
    add_feed('gdfeed', 'customRSSFunc');

    // 強制刷新重寫規則（僅在開發時使用）
    if (defined('WP_DEBUG') && WP_DEBUG) {
        flush_rewrite_rules();
    }
}

function customRSSFunc()
{
    // 確保這是 gdfeed 請求
    global $wp_query;
    if (isset($wp_query->query_vars['feed']) && $wp_query->query_vars['feed'] === 'gdfeed') {
        // 載入自訂 RSS 模板
        $template = locate_template('rss-gdfeed.php');
        if ($template) {
            load_template($template);
        } else {
            // 如果找不到模板檔案，直接包含
            include get_template_directory() . '/rss-gdfeed.php';
        }
        exit; // 確保不會繼續執行其他程式碼
    }
}

// 主題啟用時刷新重寫規則
function gd_theme_activation()
{
    // 註冊自訂 feed
    add_feed('gdfeed', 'customRSSFunc');

    // 初始化分類前綴移除功能
    if (function_exists('init_category_prefix_remover')) {
        init_category_prefix_remover();
    }

    // 刷新重寫規則
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gd_theme_activation');


/*禁用官方Feed*/

function disable_official_feed()
{
    global $wp_query;

    // 允許自訂的 gdfeed 通過
    if (isset($wp_query->query_vars['feed']) && $wp_query->query_vars['feed'] === 'gdfeed') {
        return;
    }

    wp_die(__('No feed available, please visit the <a href="' . esc_url(home_url('/')) . '">homepage</a>!'));
}

add_action('do_feed', 'disable_official_feed', 1);
add_action('do_feed_rdf', 'disable_official_feed', 1);
add_action('do_feed_rss', 'disable_official_feed', 1);
add_action('do_feed_rss2', 'disable_official_feed', 1);
add_action('do_feed_atom', 'disable_official_feed', 1);
add_action('do_feed_rss2_comments', 'disable_official_feed', 1);
add_action('do_feed_atom_comments', 'disable_official_feed', 1);

remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
