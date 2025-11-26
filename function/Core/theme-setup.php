<?php

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
    // 重新註冊 WordPress 內建 jQuery，移動到 Footer 並延遲載入，且只在單篇文章載入
    if (!is_admin()) {
        // 移除 jquery-migrate
        wp_deregister_script('jquery-migrate');

        wp_deregister_script('jquery');

        if (is_single()) {
            wp_enqueue_script(
                'jquery',
                includes_url('/js/jquery/jquery.min.js'),
                array(),
                false,
                array(
                    'in_footer' => true,
                    'strategy'  => 'defer',
                )
            );
        }
    }


    $app_css_time  = filemtime(get_template_directory() . '/css/app.css');
    $app_js_time = filemtime(get_template_directory() . '/js/app.js');



    // wp_enqueue_style('tailpress', tailpress_asset('css/app.css'), array(), $app_css_time); // 改為內聯渲染，見下方 tailpress_inline_css 函數
    // Load app.js in footer
    wp_enqueue_script('tailpress', tailpress_asset('js/app.js'), array(), $app_js_time, true);
}

add_action('wp_enqueue_scripts', 'tailpress_enqueue_scripts');

/**
 * Inline Critical CSS (tailpress app.css)
 */
function tailpress_inline_css()
{
    $css_file = get_template_directory() . '/css/app.css';
    if (file_exists($css_file)) {
        echo '<style id="tailpress-css">';
        readfile($css_file);
        echo '</style>';
    }
}
add_action('wp_head', 'tailpress_inline_css', 1);

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
