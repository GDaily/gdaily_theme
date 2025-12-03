<?php

/*禁止預設區塊編輯器*/
function disable_gutenberg_editor()
{
    add_filter('use_block_editor_for_post', '__return_false', 10);

    add_filter('use_block_editor_for_post_type', '__return_false', 10);
}
add_action('init', 'disable_gutenberg_editor');

//禁止WordPress新版本文章编辑器前端加载样式文件
remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');

// 移除 Gutenberg 前端 CSS
function remove_gutenberg_styles()
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'remove_gutenberg_styles', 100);

//移除后台仪表盘菜单：活动、新闻
function bzg_remove_dashboard_widgets()
{
    global $wp_meta_boxes;
    #移除 "活动" 模块
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    #移除 "WordPress 新闻" 模块
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
}
add_action('wp_dashboard_setup', 'bzg_remove_dashboard_widgets');


//移除后台仪表盘站点健康状态面板
add_action('wp_dashboard_setup', 'remove_site_health_dashboard_widget');
function remove_site_health_dashboard_widget()
{
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
}
//移除后台仪表盘菜单：站点健康状态
add_action('admin_menu', 'remove_site_health_menu');
function remove_site_health_menu()
{
    remove_submenu_page('tools.php', 'site-health.php');
}

/*关闭主题更新提示*/
add_filter('pre_site_transient_update_themes', function ($a) {
    return null;
});

/*移除后台主题更新检查*/
remove_action('load-themes.php', 'wp_update_themes');
remove_action('load-update.php', 'wp_update_themes');
remove_action('load-update-core.php', 'wp_update_themes');
remove_action('admin_init', '_maybe_update_themes');

/**
 * 隱藏非管理員的控制台和編輯菜單
 */
add_action('admin_menu', 'hide_menu_for_non_admins', 999);
function hide_menu_for_non_admins()
{
    // 檢查用戶是否已登錄且不是管理員
    if (is_user_logged_in() && !current_user_can('manage_options')) {
        global $menu;
        
        // 要隱藏的菜單項
        $hide_menus = array(
            'index.php',    // 控制台
            'edit.php',     // 文章
            'upload.php',   // 媒體
            'edit.php?post_type=page'  // 頁面
        );
        
        // 移除菜單項
        if (isset($menu)) {
            foreach ($menu as $key => $item) {
                if (isset($item[2]) && in_array($item[2], $hide_menus)) {
                    unset($menu[$key]);
                }
            }
        }
    }
}
