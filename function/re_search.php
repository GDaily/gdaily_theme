<?php
/*自定義搜尋功能 - 使用 gs 參數*/

// 禁用原本的 s 參數搜尋，直接404
function block_default_search()
{
    if (isset($_GET['s']) && !empty($_GET['s']) && !isset($_GET['gs'])) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }
}
add_action('template_redirect', 'block_default_search');

// 攔截並處理 gs 參數的搜尋請求
function intercept_custom_search()
{
    // 只在主頁且有 gs 參數時觸發
    if (is_home() && isset($_GET['gs']) && !empty($_GET['gs'])) {
        $search_term = sanitize_text_field($_GET['gs']);

        // 載入搜尋模板
        global $wp_query;
        $wp_query = new WP_Query(array(
            's' => $search_term,
            'posts_per_page' => get_option('posts_per_page'),
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1
        ));

        // 設置搜尋狀態
        $wp_query->is_search = true;
        $wp_query->is_home = false;

        // 載入搜尋模板
        include(get_template_directory() . '/search.php');
        exit;
    }
}
add_action('template_redirect', 'intercept_custom_search');

// 獲取自定義搜尋關鍵字
function get_custom_search_query()
{
    if (isset($_GET['gs'])) {
        return sanitize_text_field($_GET['gs']);
    }
    return get_search_query();
}
