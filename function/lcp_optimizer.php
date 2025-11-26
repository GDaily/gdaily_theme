<?php

/**
 * LCP (Largest Contentful Paint) 優化器
 * 專門處理首屏關鍵圖片的載入優化
 */

class LCP_Optimizer
{
    private static $first_image_processed = false;

    public function __init()
    {
        add_filter('the_content', array($this, 'optimize_content_images'), 1);
        add_filter('post_thumbnail_html', array($this, 'optimize_featured_image'), 10, 5);
        add_action('wp_head', array($this, 'preload_lcp_image'), 1);
        add_filter('wp_lazy_loading_enabled', array($this, 'custom_wp_lazy_loading_enabled'), 10, 3);
        add_filter('wp_get_attachment_image_attributes', array($this, 'add_fetchpriority_to_attachment_image'), 10, 3);
    }

    /**
     * 優化內容中的圖片
     */
    public function optimize_content_images($content)
    {
        if (!is_main_query() || is_admin()) {
            return $content;
        }

        // 只在首頁、分類頁面或單篇文章頁面優化
        if (!(is_home() || is_front_page() || is_single() || is_category())) {
            return $content;
        }

        // 首頁：處理所有圖片，單篇文章：只處理第一個圖片
        if (is_home() || is_front_page()) {
            // 首頁所有圖片都加上 fetchpriority="high"
            $content = preg_replace_callback(
                '/<img([^>]*)>/i',
                array($this, 'process_all_images_for_homepage'),
                $content
            );
        } else {
            // 單篇文章只處理第一個圖片
            $content = preg_replace_callback(
                '/<img([^>]*)>/i',
                array($this, 'process_first_image'),
                $content,
                1 // 只處理第一個匹配
            );
        }

        return $content;
    }

    /**
     * 處理首頁的所有圖片標籤
     */
    private function process_all_images_for_homepage($matches)
    {
        $img_tag = $matches[0];
        $attributes = $matches[1];

        // 移除 loading="lazy"
        $attributes = preg_replace('/\s*loading\s*=\s*["\']lazy["\']/i', '', $attributes);

        // 添加 fetchpriority="high"
        if (strpos($attributes, 'fetchpriority') === false) {
            $attributes .= ' fetchpriority="high"';
        }

        // 添加 decoding="sync" 確保同步解碼
        if (strpos($attributes, 'decoding') === false) {
            $attributes .= ' decoding="sync"';
        }

        return '<img' . $attributes . '>';
    }

    /**
     * 處理第一個圖片標籤
     */
    private function process_first_image($matches)
    {
        if (self::$first_image_processed) {
            return $matches[0];
        }

        $img_tag = $matches[0];
        $attributes = $matches[1];

        // 移除 loading="lazy"
        $attributes = preg_replace('/\s*loading\s*=\s*["\']lazy["\']/i', '', $attributes);

        // 添加 fetchpriority="high"
        if (strpos($attributes, 'fetchpriority') === false) {
            $attributes .= ' fetchpriority="high"';
        }

        // 添加 decoding="sync" 確保同步解碼
        if (strpos($attributes, 'decoding') === false) {
            $attributes .= ' decoding="sync"';
        }

        self::$first_image_processed = true;

        return '<img' . $attributes . '>';
    }

    /**
     * 優化特色圖片
     */
    public function optimize_featured_image($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        if (!is_main_query() || is_admin()) {
            return $html;
        }

        // 首頁：所有特色圖片都加上 fetchpriority，其他頁面：只有第一個
        if (is_home() || is_front_page()) {
            // 首頁所有特色圖片都優化
            $html = str_replace('loading="lazy"', '', $html);
            $html = str_replace('<img ', '<img fetchpriority="high" decoding="sync" ', $html);
        } else {
            // 其他頁面只處理第一個特色圖片
            static $featured_image_count = 0;
            $featured_image_count++;

            if ($featured_image_count === 1 && (is_single() || is_category())) {
                // 移除 loading="lazy"
                $html = str_replace('loading="lazy"', '', $html);

                // 添加高優先級屬性
                $html = str_replace('<img ', '<img fetchpriority="high" decoding="sync" ', $html);
            }
        }

        return $html;
    }

    /**
     * 預載入 LCP 圖片
     */
    public function preload_lcp_image()
    {
        if (is_single()) {
            $post_thumbnail_id = get_post_thumbnail_id();
            if ($post_thumbnail_id) {
                $image_src = wp_get_attachment_image_src($post_thumbnail_id, 'large');
                if ($image_src) {
                    echo '<link rel="preload" as="image" href="' . esc_url($image_src[0]) . '">';
                }
            }
        }

        // 在首頁預載入第一篇文章的特色圖片
        /*   if (is_home() || is_front_page()) {
            $recent_posts = get_posts(array(
                'numberposts' => 1,
                'post_status' => 'publish'
            ));

            if (!empty($recent_posts)) {
                $post_thumbnail_id = get_post_thumbnail_id($recent_posts[0]->ID);
                if ($post_thumbnail_id) {
                    $image_src = wp_get_attachment_image_src($post_thumbnail_id, 'large');
                    if ($image_src) {
                        echo '<link rel="preload" as="image" href="' . esc_url($image_src[0]) . '">';
                    }
                }
            }
        } */
    }

    /**
     * 獲取圖片的重要屬性以進行預載入
     */
    public static function get_critical_image_data($img_url)
    {
        // 可以在這裡添加圖片尺寸分析邏輯
        return array(
            'url' => $img_url,
            'is_critical' => true
        );
    }

    /**
     * 優化 WordPress 預設圖片 lazy loading
     */
    public function custom_wp_lazy_loading_enabled($default, $tag_name, $context)
    {
        // 在首屏內容中禁用 lazy loading
        if ($tag_name === 'img' && $context === 'the_content') {
            global $wp_query;
            static $image_count = 0;
            $image_count++;

            // 前兩個圖片不使用 lazy loading
            if ($image_count <= 2 && (is_home() || is_front_page() || is_single())) {
                return false;
            }
        }

        return $default;
    }

    /**
     * 確保使用 wp_get_attachment_image()/the_post_thumbnail() 生成的圖片也具有 fetchpriority
     */
    public function add_fetchpriority_to_attachment_image($attr, $attachment, $size)
    {
        static $attachment_image_counter = 0;

        // 只在前端主要查詢且非管理介面生效
        if (is_admin() || !is_main_query()) {
            return $attr;
        }

        // 首頁：所有圖片都加上 fetchpriority，其他頁面：只有第一張圖片
        if (is_home() || is_front_page()) {
            // 首頁所有圖片都加上高優先級
            if (isset($attr['loading']) && $attr['loading'] === 'lazy') {
                unset($attr['loading']);
            }
            $attr['fetchpriority'] = 'high';
            $attr['decoding'] = 'sync';
        } elseif (is_single() || is_category()) {
            // 其他頁面只對第一張圖片設定高優先級
            $attachment_image_counter++;

            if ($attachment_image_counter === 1) {
                // 移除 lazy 屬性（若存在），並加入 fetchpriority
                if (isset($attr['loading']) && $attr['loading'] === 'lazy') {
                    unset($attr['loading']);
                }
                $attr['fetchpriority'] = 'high';
                // 確保 decoding 為 sync
                $attr['decoding'] = 'sync';
            }
        }

        return $attr;
    }
}

// 初始化 LCP 優化器
$lcp_optimizer = new LCP_Optimizer();
$lcp_optimizer->__init();
