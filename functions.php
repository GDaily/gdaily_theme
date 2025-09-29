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



/*  初始化，運行一次 */

require_once get_template_directory() . '/once/migrate-app-name-meta.php';
require_once get_template_directory() . '/once/add-tailwind_color.php';

 


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
    add_action('wp_enqueue_scripts', function() {
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


/** 刪除 Yoast標籤 */

add_action('wp_head', function () {
    ob_start(function ($o) {
        return preg_replace('/^\n?<!--.*?[Y]oast.*?-->\n?$/mi', '', $o);
    });
}, ~PHP_INT_MAX);

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



/* 限制 REST API：前台未登入用戶禁止存取 */
add_filter('rest_authentication_errors', function ($result) {
    // 如果之前已驗證，則不處理
    if (true === $result || is_wp_error($result)) {
        return $result;
    }

    // 後台請求 (含 AJAX) 不限制
    if (is_admin()) {
        return $result;
    }

    // 如果使用者未登入且在前台，禁止存取
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_not_allowed',
            __('You are not allowed to access the REST API.'),
            array('status' => 403)
        );
    }

    // 其他情況允許
    return $result;
});





/*自訂Feed*/

add_action('init', 'customRSS');
function customRSS()
{
    add_feed('gdfeed', 'customRSSFunc');
}

function customRSSFunc()
{
    get_template_part('rss', 'gdfeed');
}
/*自訂Feed*/



/*禁用官方Feed*/

function disable_official_feed()
{
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

 