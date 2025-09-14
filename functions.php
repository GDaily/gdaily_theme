<?php

require_once __DIR__ . '/vendor/autoload.php';




/*  Carbon_Fields */
require_once get_template_directory() . '/function/carbon_fields.php';

/*  特色圖片取色 */
require_once get_template_directory() . '/function/color_matcher.php';

/*  分頁導航函數 */
require_once get_template_directory() . '/function/wpbeginner_numeric_posts_nav.php';


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




function trimImageWhitespace($imagePath, $bgColor = [255, 255, 255], $tolerance = 10)
{
    // 檢查圖片是否存在
    if (!file_exists($imagePath)) {
       return 192;
    }

    // 根據圖片格式創建 GD 圖片資源
    $imageInfo = getimagesize($imagePath);
    $imageType = $imageInfo[2];

    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($imagePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($imagePath);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($imagePath);
            break;
        default:
            die("不支持的圖片格式: " . $imagePath);
    }

    // 檢查圖片是否成功創建
    if (!$image) {
        die("無法創建圖片資源: " . $imagePath);
    }

    $width = imagesx($image);
    $height = imagesy($image);

    $minX = $width;
    $minY = $height;
    $maxX = 0;
    $maxY = 0;

    // 遍歷每個像素，找到非背景色的最小/最大邊界
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($image, $x, $y);
            $colors = imagecolorsforindex($image, $rgb);

            // 檢查透明度（對於 PNG 和 GIF 格式）
            if (($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) && ($rgb & 0x7F000000) >> 24 == 127) {
                // 當前像素是完全透明，視為白邊
                continue;
            }

            // 計算當前像素與背景色的顏色差異
            $rDiff = abs($colors['red'] - $bgColor[0]);
            $gDiff = abs($colors['green'] - $bgColor[1]);
            $bDiff = abs($colors['blue'] - $bgColor[2]);

            // 檢查當前像素是否接近背景色（使用容忍度）
            if ($rDiff > $tolerance || $gDiff > $tolerance || $bDiff > $tolerance) {
                if ($x < $minX) $minX = $x;
                if ($x > $maxX) $maxX = $x;
                if ($y < $minY) $minY = $y;
                if ($y > $maxY) $maxY = $y;
            }
        }
    }

    // 計算去除白邊後的寬高
    if ($minX > $maxX || $minY > $maxY) {
        // 如果沒有非背景像素，則返回原始尺寸
        return [$width, $height];
    }

    $trimmedWidth = $maxX - $minX + 1;
    $trimmedHeight = $maxY - $minY + 1;

    // 取最大值
    $maxSize = max($trimmedWidth, $trimmedHeight);

    // 返回最大值
    return $maxSize;
}


/*多久之前的時間*/
function meks_time_ago()
{
    return human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('前');
}


/*區塊編輯器*/
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


/** 刪除 oast標籤 */

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



/* 關閉 REST API，僅允許管理員使用 */
add_filter('rest_authentication_errors', function ($result) {
    // 如果之前已驗證，則不處理
    if (true === $result || is_wp_error($result)) {
        return $result;
    }

    // 取得目前使用者
    $current_user = wp_get_current_user();

    // 如果是管理員則允許
    if (in_array('administrator', (array) $current_user->roles)) {
        return $result;
    }

    // 否則拒絕訪問
    return new WP_Error(
        'rest_not_allowed',
        __('You are not allowed to access this resource.'),
        array('status' => 403)
    );
});



/**
 * 處理單篇文章的縮圖顏色分析和設定
 * 
 * @param int $post_id 文章 ID
 * @return array|false 返回處理結果或 false（如果失敗）
 */
function process_post_tailwind_color($post_id) {
    // 檢查是否有縮圖
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if (!$thumbnail_id) {
        return false;
    }
    
    // 檢查縮圖文件是否存在
    $thumbnail_serverPath = get_attached_file($thumbnail_id);
    if (!file_exists($thumbnail_serverPath)) {
        return false;
    }
    
    try {
        // 使用 ColorMatcher 分析顏色
        $matcher = new ColorMatcher();
        $colorResult = $matcher->findClosestColor($thumbnail_serverPath);
        
        // 設定 meta 值
        carbon_set_post_meta($post_id, 'tailwind_color', $colorResult['tailwind_class']);
        carbon_set_post_meta($post_id, 'tailwind_hex_base_color', $colorResult['tailwind_hex_base_color']);
        carbon_set_post_meta($post_id, 'tailwind_hex_light_color', $colorResult['tailwind_hex_light_color']);
        
        return [
            'success' => true,
            'post_id' => $post_id,
            'tailwind_class' => $colorResult['tailwind_class'],
            'base_color' => $colorResult['tailwind_hex_base_color'],
            'light_color' => $colorResult['tailwind_hex_light_color']
        ];
        
    } catch (Exception $e) {
        return false;
    }
}

/**
 * 在文章儲存時自動執行顏色分析
 * 
 * @param int $post_id 文章 ID
 */
function auto_process_tailwind_color_on_save($post_id) {
    // 檢查是否為自動儲存或修訂版本
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }
    
    // 只處理 'post' 類型的文章
    if (get_post_type($post_id) !== 'post') {
        return;
    }
    
    // 執行顏色分析（不限制發布狀態）
    process_post_tailwind_color($post_id);
}

// 註冊多個 hook：在文章儲存時自動執行
add_action('save_post', 'auto_process_tailwind_color_on_save', 10, 1);
add_action('wp_insert_post', 'auto_process_tailwind_color_on_save', 10, 1);
add_action('edit_post', 'auto_process_tailwind_color_on_save', 10, 1);

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