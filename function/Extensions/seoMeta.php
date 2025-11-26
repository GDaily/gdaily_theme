<?php
/**
 * Plugin Name: GD SEO
 * Description: 自訂 SEO Meta 與 JSON-LD，取代 Yoast SEO。
 * Version: 1.1
 * Author: ff
 */

// 取得 SEO 設定
function gd_get_seo_data() {
    global $post;

    $data = [];
    $data['site_name'] = get_bloginfo('name');
    $data['site_desc'] = get_bloginfo('description');
    $data['site_url']  = home_url('/');

    if (is_singular() && $post) {
        $data['title']       = get_the_title($post);
        $data['description'] = wp_strip_all_tags(get_the_excerpt($post));
        $data['url']         = get_permalink($post);
        $data['image']       = gd_get_og_compatible_image($post);
        $data['published']   = get_the_date(DATE_W3C, $post);
        $data['modified']    = get_the_modified_date(DATE_W3C, $post);
        $data['author']      = get_the_author_meta('display_name', $post->post_author);
        $data['author_url']  = get_author_posts_url($post->post_author);
        $data['type']        = 'article';
    } else {
        $data['title']       = $data['site_name'];
        $data['description'] = $data['site_desc'];
        $data['url']         = home_url(add_query_arg([], $GLOBALS['wp']->request));
        $data['image']       = get_site_icon_url();
        $data['published']   = '';
        $data['modified']    = '';
        $data['author']      = $data['site_name'];
        $data['author_url']  = $data['site_url'];
        $data['type']        = 'website';
    }

    $data['locale'] = get_locale() ?: 'zh_TW';

    return $data;
}

// 取得適合 OG 的圖片（避免 WebP 格式）
function gd_get_og_compatible_image($post) {
    $image_url = get_the_post_thumbnail_url($post, 'full');
    
    if (empty($image_url)) {
        return gd_get_site_logo();
    }
    
    // 檢查是否為 WebP 格式
    if (preg_match('/\.webp$/i', $image_url)) {
        $thumbnail_id = get_post_thumbnail_id($post);
        if ($thumbnail_id) {
            // 嘗試取得 JPG 或 PNG 版本
            $attachment_meta = wp_get_attachment_metadata($thumbnail_id);
            
            // 檢查是否有其他格式的圖片
            if (!empty($attachment_meta['sources'])) {
                // WordPress 5.8+ 支援多格式圖片
                if (isset($attachment_meta['sources']['image/jpeg'])) {
                    $upload_dir = wp_upload_dir();
                    $file_path = dirname($attachment_meta['file']) . '/' . $attachment_meta['sources']['image/jpeg']['file'];
                    return $upload_dir['baseurl'] . '/' . $file_path;
                } elseif (isset($attachment_meta['sources']['image/png'])) {
                    $upload_dir = wp_upload_dir();
                    $file_path = dirname($attachment_meta['file']) . '/' . $attachment_meta['sources']['image/png']['file'];
                    return $upload_dir['baseurl'] . '/' . $file_path;
                }
            }
            
            // 如果沒有其他格式，嘗試找原始非 WebP 檔案
            $file_path = get_attached_file($thumbnail_id);
            if ($file_path && preg_match('/\.webp$/i', $file_path)) {
                // 嘗試找同名的 JPG 或 PNG
                $base_path = preg_replace('/\.webp$/i', '', $file_path);
                $upload_dir = wp_upload_dir();
                
                foreach (['.jpg', '.jpeg', '.png'] as $ext) {
                    if (file_exists($base_path . $ext)) {
                        $relative_path = str_replace($upload_dir['basedir'], '', $base_path . $ext);
                        return $upload_dir['baseurl'] . $relative_path;
                    }
                }
                
                // 如果找不到現成的，自動轉換 WebP 為 JPG 並儲存到快取
                $converted_url = gd_convert_webp_to_jpg($file_path, $thumbnail_id);
                if ($converted_url) {
                    return $converted_url;
                }
            }
        }
        
        // 如果找不到替代格式，返回 Logo
        error_log('警告：文章 ID ' . $post->ID . ' 的特色圖片為 WebP 格式，Facebook 可能無法顯示：' . $image_url);
        return gd_get_site_logo();
    }
    
    return $image_url;
}

// 將 WebP 轉換為 JPG 並儲存到快取資料夾
function gd_convert_webp_to_jpg($webp_path, $attachment_id) {
    // 檢查 GD 擴展是否支援 WebP
    if (!function_exists('imagecreatefromwebp') || !function_exists('imagejpeg')) {
        error_log('GD 擴展不支援 WebP 或 JPEG 處理');
        return false;
    }
    
    // 設定快取目錄
    $cache_dir = WP_CONTENT_DIR . '/cache/og-images';
    if (!file_exists($cache_dir)) {
        wp_mkdir_p($cache_dir);
    }
    
    // 生成快取檔案名稱（使用 attachment ID 和檔案修改時間作為唯一識別）
    $file_mtime = filemtime($webp_path);
    $cache_filename = 'og-' . $attachment_id . '-' . $file_mtime . '.jpg';
    $cache_path = $cache_dir . '/' . $cache_filename;
    
    // 如果快取檔案已存在，直接返回 URL
    if (file_exists($cache_path)) {
        return content_url('cache/og-images/' . $cache_filename);
    }
    
    // 清理該 attachment 的舊快取檔案
    $old_files = glob($cache_dir . '/og-' . $attachment_id . '-*.jpg');
    if ($old_files) {
        foreach ($old_files as $old_file) {
            @unlink($old_file);
        }
    }
    
    // 轉換 WebP 為 JPG
    try {
        $image = @imagecreatefromwebp($webp_path);
        if (!$image) {
            error_log('無法讀取 WebP 圖片：' . $webp_path);
            return false;
        }
        
        // 取得原圖尺寸
        $width = imagesx($image);
        $height = imagesy($image);
        
        // 如果圖片太大，縮小以節省空間（Facebook 建議 OG image 至少 1200x630，最多 8MB）
        $max_width = 1200;
        $max_height = 1200;
        
        if ($width > $max_width || $height > $max_height) {
            $ratio = min($max_width / $width, $max_height / $height);
            $new_width = round($width * $ratio);
            $new_height = round($height * $ratio);
            
            $new_image = imagecreatetruecolor($new_width, $new_height);
            
            // 設定白色背景（避免透明區域變黑）
            $white = imagecolorallocate($new_image, 255, 255, 255);
            imagefill($new_image, 0, 0, $white);
            
            // 縮放圖片
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($image);
            $image = $new_image;
        } else {
            // 建立新圖片並填充白色背景（處理透明度）
            $new_image = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($new_image, 255, 255, 255);
            imagefill($new_image, 0, 0, $white);
            imagecopy($new_image, $image, 0, 0, 0, 0, $width, $height);
            imagedestroy($image);
            $image = $new_image;
        }
        
        // 儲存為 JPG（85% 品質）
        $result = imagejpeg($image, $cache_path, 85);
        imagedestroy($image);
        
        if ($result) {
            // 設定檔案權限
            @chmod($cache_path, 0644);
            return content_url('cache/og-images/' . $cache_filename);
        } else {
            error_log('無法儲存 JPG 圖片到：' . $cache_path);
            return false;
        }
        
    } catch (Exception $e) {
        error_log('WebP 轉 JPG 失敗：' . $e->getMessage());
        return false;
    }
}

// 取得網站 Logo

// 輸出 <meta> 標籤
function gd_output_meta_tags() {
    $d = gd_get_seo_data();

    echo '<title>' . esc_html($d['title']) . "</title>\n";
    echo '<meta name="description" content="' . esc_attr($d['description']) . "\" />\n";
    echo '<link rel="canonical" href="' . esc_url($d['url']) . "\" />\n";

    // Open Graph
    echo '<meta property="og:locale" content="' . esc_attr($d['locale']) . "\" />\n";
    echo '<meta property="og:type" content="' . esc_attr($d['type']) . "\" />\n";
    echo '<meta property="og:title" content="' . esc_attr($d['title']) . "\" />\n";
    echo '<meta property="og:description" content="' . esc_attr($d['description']) . "\" />\n";
    echo '<meta property="og:url" content="' . esc_url($d['url']) . "\" />\n";
    echo '<meta property="og:site_name" content="' . esc_attr($d['site_name']) . "\" />\n";

    if (!empty($d['image'])) {
        echo '<meta property="og:image" content="' . esc_url($d['image']) . "\" />\n";

        $img_id = get_post_thumbnail_id();
        $img_meta = $img_id ? wp_get_attachment_metadata($img_id) : null;
        if ($img_meta && isset($img_meta['width'], $img_meta['height'])) {
            echo '<meta property="og:image:width" content="' . intval($img_meta['width']) . "\" />\n";
            echo '<meta property="og:image:height" content="' . intval($img_meta['height']) . "\" />\n";
        }
        $mime = $img_id ? get_post_mime_type($img_id) : 'image/png';
        echo '<meta property="og:image:type" content="' . esc_attr($mime) . "\" />\n";
    }

    if (!empty($d['published'])) {
        echo '<meta property="article:published_time" content="' . esc_attr($d['published']) . "\" />\n";
    }
    if (!empty($d['modified'])) {
        echo '<meta property="article:modified_time" content="' . esc_attr($d['modified']) . "\" />\n";
    }

    // 作者
    echo '<meta name="author" content="' . esc_attr($d['author']) . "\" />\n";

    // Twitter
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($d['title']) . "\" />\n";
    echo '<meta name="twitter:description" content="' . esc_attr($d['description']) . "\" />\n";
    if (!empty($d['image'])) {
        echo '<meta name="twitter:image" content="' . esc_url($d['image']) . "\" />\n";
    }
    if (!empty($d['author'])) {
        echo '<meta name="twitter:label1" content="作者:" />' . "\n";
        echo '<meta name="twitter:data1" content="' . esc_attr($d['author']) . "\" />\n";
    }
}
add_action('wp_head', 'gd_output_meta_tags', 5);

// JSON-LD @graph
function gd_output_json_ld() {
    $d = gd_get_seo_data();
    $logo = gd_get_site_logo();

    $graph = [];

    // WebPage
    $graph[] = [
        "@type" => "WebPage",
        "@id"   => $d['url'],
        "url"   => $d['url'],
        "name"  => $d['title'],
        "isPartOf" => [ "@id" => $d['site_url'] . "#website" ],
        "primaryImageOfPage" => [ "@id" => $d['url'] . "#primaryimage" ],
        "image" => [ "@id" => $d['url'] . "#primaryimage" ],
        "thumbnailUrl" => $d['image'],
        "datePublished" => $d['published'],
        "dateModified"  => $d['modified'],
        "author" => [ "@id" => $d['site_url'] . "#/schema/person" ],
        "description" => $d['description'],
        "inLanguage"  => $d['locale'],
        "potentialAction" => [[
            "@type" => "ReadAction",
            "target" => [$d['url']]
        ]]
    ];

    // ImageObject
    if ($d['image']) {
        $graph[] = [
            "@type" => "ImageObject",
            "@id"   => $d['url'] . "#primaryimage",
            "url"   => $d['image'],
            "contentUrl" => $d['image']
        ];
    }

    // WebSite
    $graph[] = [
        "@type" => "WebSite",
        "@id"   => $d['site_url'] . "#website",
        "url"   => $d['site_url'],
        "name"  => $d['site_name'],
        "description" => $d['site_desc'],
        "publisher" => [ "@id" => $d['site_url'] . "#organization" ],
        "potentialAction" => [[
            "@type" => "SearchAction",
            "target" => [
                "@type" => "EntryPoint",
                "urlTemplate" => $d['site_url'] . "?s={search_term_string}"
            ],
            "query-input" => [
                "@type" => "PropertyValueSpecification",
                "valueRequired" => true,
                "valueName"     => "search_term_string"
            ]
        ]],
        "inLanguage" => $d['locale']
    ];

    // Organization
    $graph[] = [
        "@type" => "Organization",
        "@id"   => $d['site_url'] . "#organization",
        "name"  => $d['site_name'],
        "url"   => $d['site_url'],
        "logo"  => [
            "@type" => "ImageObject",
            "url"   => $logo,
            "contentUrl" => $logo
        ]
    ];

    // Person (作者)
    $graph[] = [
        "@type" => "Person",
        "@id"   => $d['site_url'] . "#/schema/person",
        "name"  => $d['author'],
        "url"   => $d['author_url']
    ];

    // BreadcrumbList (選擇性)
    if (is_singular('post')) {
        $breadcrumbs = [];
        $pos = 1;
        $breadcrumbs[] = [
            "@type" => "ListItem",
            "position" => $pos++,
            "name" => "首頁",
            "item" => $d['site_url']
        ];
        $categories = get_the_category();
        if (!empty($categories)) {
            $cat = $categories[0];
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => $pos++,
                "name" => $cat->name,
                "item" => get_category_link($cat->term_id)
            ];
        }
        $breadcrumbs[] = [
            "@type" => "ListItem",
            "position" => $pos,
            "name" => $d['title']
        ];
        $graph[] = [
            "@type" => "BreadcrumbList",
            "@id"   => $d['url'] . "#breadcrumb",
            "itemListElement" => $breadcrumbs
        ];
    }

    $data = [
        "@context" => "https://schema.org",
        "@graph"   => $graph
    ];

    echo '<script type="application/ld+json" class="yoast-schema-graph">' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
}
add_action('wp_head', 'gd_output_json_ld', 20);


// 工具函式
function gd_trim_description($text, $len=160) {
    $text = wp_strip_all_tags($text);
    if ( mb_strlen($text) > $len ) {
        $text = mb_substr($text, 0, $len) . '…';
    }
    return $text;
}

function gd_get_post_thumbnail($post_id) {
    if ( has_post_thumbnail($post_id) ) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
        return $img[0];
    }
    return gd_get_site_logo();
}

function gd_get_site_logo() {
    $logo_id = get_theme_mod('custom_logo');
    if ( $logo_id ) {
        return wp_get_attachment_image_url($logo_id, 'full');
    }
    return get_template_directory_uri() . '/source/img/logo.svg';
}