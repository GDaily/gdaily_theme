<?php

/**
 * 分類前綴移除器
 * 移除 WordPress 分類 URL 中的 "category" 前綴
 * 
 * 功能：
 * - 將 /category/分類名 改為 /分類名
 * - 自動重定向舊 URL 到新 URL (301 重定向)
 * - 處理多層分類結構
 */

// 移除分類前綴
function remove_category_base()
{
    add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');

    // 移除現有規則中的 category 基礎
    global $wp_rewrite;
    $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
}
add_action('init', 'remove_category_base');

// 自定義重寫規則
function no_category_base_rewrite_rules($category_rewrite)
{
    global $wp_rewrite;

    $category_rewrite = array();
    $categories = get_categories(array('hide_empty' => false));

    foreach ($categories as $category) {
        $category_nicename = $category->slug;
        if ($category->parent == $category->cat_ID) {
            $category->parent = 0;
        } elseif ($category->parent != 0) {
            $category_nicename = get_category_parents($category->parent, false, '/', true) . $category_nicename;
        }

        $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
    }

    return $category_rewrite;
}

// 處理分類連結
function remove_category_base_permastruct($permalink, $category_id)
{
    $cat_id = (int) $category_id;
    $category = get_category($cat_id);

    if (is_wp_error($category) || !$category) {
        return $permalink;
    }

    $category_nicename = $category->slug;
    if ($category->parent == $category->cat_ID) {
        $category->parent = 0;
    } elseif (0 != $category->parent) {
        $category_nicename = get_category_parents($category->parent, false, '/', true) . $category_nicename;
    }

    $permalink = home_url(user_trailingslashit($category_nicename, 'category'));
    return $permalink;
}
add_filter('category_link', 'remove_category_base_permastruct', 10, 2);

// 處理舊的 category URL 重定向
function redirect_old_category_urls()
{
    if (is_404()) {
        global $wp_query;

        if (!isset($_SERVER['REQUEST_URI'])) {
            return;
        }

        $request = trim($_SERVER['REQUEST_URI'], '/');

        // 檢查是否是舊的 category URL
        if (strpos($request, 'category/') === 0) {
            $category_slug = str_replace('category/', '', $request);
            $category_slug = trim($category_slug, '/');

            // 處理多層分類路徑
            $category_parts = explode('/', $category_slug);
            $last_category_slug = end($category_parts);

            // 檢查分類是否存在
            $category = get_category_by_slug($last_category_slug);
            if ($category) {
                $new_url = get_category_link($category->term_id);
                wp_redirect($new_url, 301);
                exit;
            }
        }
    }
}
add_action('template_redirect', 'redirect_old_category_urls');

/**
 * 初始化分類前綴移除功能
 * 用於主題激活時調用
 */
function init_category_prefix_remover()
{
    remove_category_base();
}
