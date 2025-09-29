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
        $data['image']       = get_the_post_thumbnail_url($post, 'full');
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