<?php




require_once __DIR__ . '/vendor/autoload.php';




/*  Carbon_Fields */
require_once get_template_directory() . '/function/carbon_fields.php';

/*  特色圖片取色 */
require_once get_template_directory() . '/function/color_matcher.php';

/*  圖片處理工具 */
require_once get_template_directory() . '/function/image_processor.php';

/*  分頁導航函數 */
require_once get_template_directory() . '/function/wpbeginner_numeric_posts_nav.php';

/*  廣告插入函數 */
require_once get_template_directory() . '/function/adsense_inserter.php';

/*  SEO Meta */
require_once get_template_directory() . '/function/seoMeta.php';

/*  短代碼 */
require_once get_template_directory() . '/function/url_short_code.php';

/*  文章搜尋優化 */
require_once get_template_directory() . '/function/re_search.php';



/*  初始化，運行一次 */

require_once get_template_directory() . '/once/migrate-app-name-meta.php';
require_once get_template_directory() . '/once/add-tailwind_color.php';
require_once get_template_directory() . '/once/convert-h3-to-h2-batch.php';

/**
 * Theme setup.
 */
function tailpress_setup()
{
    add_theme_support('title-tag');

    register_nav_menus(
        array(
            'primary' => __('Primary Menu', 'tailpress'),
        )
    );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        )
    );

    add_theme_support('custom-logo');
    add_theme_support('post-thumbnails');

    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');

    add_theme_support('responsive-embeds');

    add_theme_support('editor-styles');
    add_editor_style('css/editor-style.css');
}

add_action('after_setup_theme', 'tailpress_setup');

/**
 * Enqueue theme assets.
 */
function tailpress_enqueue_scripts()
{
    $theme = wp_get_theme();


    $app_css_time  = filemtime(get_template_directory() . '/css/app.css');
    $app_js_time = filemtime(get_template_directory() . '/js/app.js');



    wp_enqueue_style('tailpress', tailpress_asset('css/app.css'), array(), $app_css_time);
    wp_enqueue_script('tailpress', tailpress_asset('js/app.js'), array(), $app_js_time);



    // 載入本地 alpinejs
    add_action('wp_enqueue_scripts', function () {
        wp_enqueue_script(
            'alpinejs',
            get_template_directory_uri() . '/source/js/alpinejs.min.js',
            array(),
            null,
            array('strategy' => 'defer')
        );
    });
}

add_action('wp_enqueue_scripts', 'tailpress_enqueue_scripts');

/**
 * Get asset path.
 *
 * @param string  $path Path to asset.
 *
 * @return string
 */
function tailpress_asset($path)
{
    if (wp_get_environment_type() === 'production') {
        return get_stylesheet_directory_uri() . '/' . $path;
    }

    return add_query_arg('time', time(),  get_stylesheet_directory_uri() . '/' . $path);
}

/**
 * Adds option 'li_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function tailpress_nav_menu_add_li_class($classes, $item, $args, $depth)
{
    if (isset($args->li_class)) {
        $classes[] = $args->li_class;
    }

    if (isset($args->{"li_class_$depth"})) {
        $classes[] = $args->{"li_class_$depth"};
    }

    return $classes;
}

add_filter('nav_menu_css_class', 'tailpress_nav_menu_add_li_class', 10, 4);

/**
 * Adds option 'submenu_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function tailpress_nav_menu_add_submenu_class($classes, $args, $depth)
{
    if (isset($args->submenu_class)) {
        $classes[] = $args->submenu_class;
    }

    if (isset($args->{"submenu_class_$depth"})) {
        $classes[] = $args->{"submenu_class_$depth"};
    }

    return $classes;
}

add_filter('nav_menu_submenu_css_class', 'tailpress_nav_menu_add_submenu_class', 10, 3);



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


/*禁止預設區塊編輯器*/
function disable_gutenberg_editor()
{
    add_filter('use_block_editor_for_post', '__return_false', 10);

    add_filter('use_block_editor_for_post_type', '__return_false', 10);
}
add_action('init', 'disable_gutenberg_editor');


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


# 禁用 xmlrpc
add_filter('xmlrpc_enabled', '__return_false');


//禁止WordPress新版本文章编辑器前端加载样式文件
remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');

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

/*關閉Pingbacks*/
function no_self_ping(&$links)
{
    $home = get_option('home');
    foreach ($links as $l => $link)
        if (0 === strpos($link, $home))
            unset($links[$l]);
}

add_action('pre_ping', 'no_self_ping');

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
define('DISALLOW_FILE_EDIT', true);

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

// 移除 Gutenberg 前端 CSS
function remove_gutenberg_styles()
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('global-styles');
}
add_action('wp_enqueue_scripts', 'remove_gutenberg_styles', 100);

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
    // 刷新重寫規則
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'gd_theme_activation');

/*自訂Feed*/



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


/*禁用官方Feed*/
function load_jquery_for_wpdiscuz()
{
    if (function_exists('wpdiscuz') && is_single()) {
        wp_enqueue_script('jquery');
    }
}
add_action('wp_footer', 'load_jquery_for_wpdiscuz');
