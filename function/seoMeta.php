<?php
// ...existing code...
/**
 * 簡易 SEO Meta 輸出（title / description / canonical / OG / Twitter / JSON-LD）
 * 支援文章層級自訂欄位：seo_title, seo_description, seo_image (附件 ID 或 URL)
 */
function gd_get_seo_data() {
    global $post;

    // 幫助函式：多位元組字元長度截斷（適用中文）
    if ( ! function_exists('gd_trim_description') ) {
        function gd_trim_description( $text, $chars = 200 ) {
            $text = wp_strip_all_tags( $text );
            $text = preg_replace('/\s+/u', ' ', $text);
            if ( function_exists('mb_strlen') ) {
                if ( mb_strlen( $text, 'UTF-8' ) > $chars ) {
                    return mb_substr( $text, 0, $chars, 'UTF-8' ) . '...';
                }
                return $text;
            }
            // fallback
            if ( strlen( $text ) > $chars ) {
                return substr( $text, 0, $chars ) . '...';
            }
            return $text;
        }
    }

    $site_name = get_bloginfo('name');
    $site_desc = get_bloginfo('description');
    $home_url  = home_url('/');

    $data = [
        'title'       => $site_name,
        'description' => $site_desc,
        'canonical'   => $home_url,
        'url'         => $home_url,
        'site_name'   => $site_name,
        'type'        => is_singular() ? 'article' : 'website',
        'locale'      => 'zh_TW',
        'author'      => $site_name,
        'published_time' => null,
        'modified_time'  => null,
        'image'       => null,
        'image_width' => null,
        'image_height'=> null,
        'image_type'  => null,
        'in_language' => 'zh-TW',
    ];

    if ( is_front_page() || is_home() ) {
        $data['title'] = $site_name . ' - ' . $site_desc;
        $data['description'] = $site_desc;
        $data['canonical'] = $home_url;
        $data['url'] = $home_url;
    }

    if ( is_singular() && isset($post) ) {
        // Title
        $meta_title = get_post_meta($post->ID, 'seo_title', true);
        $data['title'] = $meta_title ? $meta_title : get_the_title($post);

        // Description
        $meta_desc = get_post_meta($post->ID, 'seo_description', true);
        if ( $meta_desc ) {
            $data['description'] = wp_strip_all_tags( $meta_desc );
        } else {
            if ( has_excerpt($post) ) {
                $data['description'] = wp_strip_all_tags( get_the_excerpt($post) );
            } else {
                // 中文內容用字元長度截斷，預設 200 字元
                $data['description'] = gd_trim_description( $post->post_content, 200 );
            }
        }

        // Canonical / URL
        $data['canonical'] = get_permalink($post);
        $data['url'] = get_permalink($post);

        // Times
        $data['published_time'] = get_the_date('c', $post);
        $data['modified_time']  = get_the_modified_date('c', $post);

        // Author
        $author_id = $post->post_author;
        if ( $author_id ) {
            $data['author'] = get_the_author_meta('display_name', $author_id) ?: $data['author'];
            $data['author_id'] = $author_id;
            $data['author_url'] = get_author_posts_url( $author_id );
        }

        // Image: 優先自訂欄位 seo_image，可為附件 ID 或 URL；再 fallback 為特色圖
        $seo_image = get_post_meta($post->ID, 'seo_image', true);
        $img_url = null;
        $img_width = $img_height = null;
        $img_type = null;

        if ( $seo_image ) {
            if ( is_numeric($seo_image) ) {
                $src = wp_get_attachment_image_src( (int)$seo_image, 'full' );
                if ( $src ) {
                    $img_url = $src[0];
                    $img_width = $src[1];
                    $img_height = $src[2];
                }
                $mime = get_post_mime_type( (int)$seo_image );
                if ( $mime ) $img_type = $mime;
            } else {
                $img_url = esc_url_raw( $seo_image );
            }
        }

        if ( !$img_url && has_post_thumbnail($post) ) {
            $thumb_id = get_post_thumbnail_id($post);
            $src = wp_get_attachment_image_src( $thumb_id, 'full' );
            if ( $src ) {
                $img_url = $src[0];
                $img_width = $src[1];
                $img_height = $src[2];
            }
            $mime = get_post_mime_type( $thumb_id );
            if ( $mime ) $img_type = $mime;
        }

        if ( $img_url ) {
            $data['image'] = $img_url;
            $data['image_width']  = $img_width;
            $data['image_height'] = $img_height;
            $data['image_type']   = $img_type;
        }
    }

    // 全站 fallback default image (可在此修改為主題設定或常量)
    if ( empty($data['image']) ) {
        $default = get_theme_mod('gd_seo_default_image');
        if ( $default ) {
            $data['image'] = esc_url_raw($default);
        }
    }

    return $data;
}

function gd_output_seo_meta() {
    $d = gd_get_seo_data();

    // Robots meta tag for SEO
    if ( is_singular() ) {
        global $post;
        $robots_content = array();
        
        // 基本設定
        $robots_content[] = 'index';
        $robots_content[] = 'follow';
        
        // 如果是私密文章或密碼保護
        if ( $post->post_status !== 'publish' || !empty($post->post_password) ) {
            $robots_content = array('noindex', 'nofollow');
        }
        
        // 檢查自訂 robots 設定
        $custom_robots = get_post_meta($post->ID, 'seo_robots', true);
        if ( $custom_robots ) {
            $robots_content = explode(',', $custom_robots);
        }
        
        echo '<meta name="robots" content="' . esc_attr(implode(', ', $robots_content)) . '" />' . "\n";
    } else {
        echo '<meta name="robots" content="index, follow" />' . "\n";
    }

    // Title
    echo '<title>' . esc_html( $d['title'] ) . '</title>' . "\n";

    // Meta description
    if ( ! empty( $d['description'] ) ) {
        echo '<meta name="description" content="' . esc_attr( $d['description'] ) . '" />' . "\n";
    }

    // Keywords (如果有設定)
    if ( is_singular() ) {
        global $post;
        $keywords = get_post_meta($post->ID, 'seo_keywords', true);
        if ( $keywords ) {
            echo '<meta name="keywords" content="' . esc_attr($keywords) . '" />' . "\n";
        } else {
            // 自動從標籤產生關鍵字
            $tags = get_the_tags($post->ID);
            if ($tags) {
                $tag_names = array();
                foreach ($tags as $tag) {
                    $tag_names[] = $tag->name;
                }
                if (!empty($tag_names)) {
                    echo '<meta name="keywords" content="' . esc_attr(implode(', ', $tag_names)) . '" />' . "\n";
                }
            }
        }
    }

    // Canonical
    echo '<link rel="canonical" href="' . esc_url( $d['canonical'] ) . '" />' . "\n";

    // Open Graph
    echo '<meta property="og:locale" content="' . esc_attr( $d['locale'] ) . '" />' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $d['type'] ) . '" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $d['title'] ) . '" />' . "\n";
    if ( ! empty( $d['description'] ) ) {
        echo '<meta property="og:description" content="' . esc_attr( $d['description'] ) . '" />' . "\n";
    }
    echo '<meta property="og:url" content="' . esc_url( $d['url'] ) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $d['site_name'] ) . '" />' . "\n";

    if ( ! empty( $d['published_time'] ) ) {
        echo '<meta property="article:published_time" content="' . esc_attr( $d['published_time'] ) . '" />' . "\n";
    }
    if ( ! empty( $d['modified_time'] ) ) {
        echo '<meta property="article:modified_time" content="' . esc_attr( $d['modified_time'] ) . '" />' . "\n";
    }

    // 文章特定的 Open Graph 標籤
    if ( is_singular() && $d['type'] === 'article' ) {
        global $post;
        
        // Article author
        if ( ! empty( $d['author'] ) ) {
            echo '<meta property="article:author" content="' . esc_attr( $d['author'] ) . '" />' . "\n";
            // 如果有作者頁面 URL
            if ( ! empty( $d['author_url'] ) ) {
                echo '<meta property="article:author" content="' . esc_url( $d['author_url'] ) . '" />' . "\n";
            }
        }
        
        // Article section (主要分類)
        $categories = get_the_category($post->ID);
        if ( $categories ) {
            echo '<meta property="article:section" content="' . esc_attr( $categories[0]->name ) . '" />' . "\n";
        }
        
        // Article tags
        $tags = get_the_tags($post->ID);
        if ( $tags ) {
            foreach ( $tags as $tag ) {
                echo '<meta property="article:tag" content="' . esc_attr( $tag->name ) . '" />' . "\n";
            }
        }
        
        // Article expiration time (如果設定了)
        $expiry_date = get_post_meta($post->ID, 'seo_expiry_date', true);
        if ( $expiry_date ) {
            echo '<meta property="article:expiration_time" content="' . esc_attr( date('c', strtotime($expiry_date)) ) . '" />' . "\n";
        }
    }

    if ( ! empty( $d['image'] ) ) {
        echo '<meta property="og:image" content="' . esc_url( $d['image'] ) . '" />' . "\n";
        if ( $d['image_width'] ) echo '<meta property="og:image:width" content="' . esc_attr( $d['image_width'] ) . '" />' . "\n";
        if ( $d['image_height'] ) echo '<meta property="og:image:height" content="' . esc_attr( $d['image_height'] ) . '" />' . "\n";
        if ( $d['image_type'] ) echo '<meta property="og:image:type" content="' . esc_attr( $d['image_type'] ) . '" />' . "\n";
        // 圖片 alt 文字
        echo '<meta property="og:image:alt" content="' . esc_attr( $d['title'] ) . '" />' . "\n";
    }

    // Author
    if ( ! empty( $d['author'] ) ) {
        echo '<meta name="author" content="' . esc_attr( $d['author'] ) . '" />' . "\n";
    }

    // Twitter card (優化版)
    $twitter_card = ! empty( $d['image'] ) ? 'summary_large_image' : 'summary';
    echo '<meta name="twitter:card" content="' . esc_attr( $twitter_card ) . '" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $d['title'] ) . '" />' . "\n";
    if ( ! empty( $d['description'] ) ) {
        echo '<meta name="twitter:description" content="' . esc_attr( $d['description'] ) . '" />' . "\n";
    }
    if ( ! empty( $d['image'] ) ) {
        echo '<meta name="twitter:image" content="' . esc_url( $d['image'] ) . '" />' . "\n";
        echo '<meta name="twitter:image:alt" content="' . esc_attr( $d['title'] ) . '" />' . "\n";
    }
    
    // 額外的 Twitter 標籤
    echo '<meta name="twitter:label1" content="作者:" />' . "\n";
    if ( ! empty( $d['author'] ) ) {
        echo '<meta name="twitter:data1" content="' . esc_attr( $d['author'] ) . '" />' . "\n";
    }
    
    if ( is_singular() ) {
        echo '<meta name="twitter:label2" content="閱讀時間:" />' . "\n";
        global $post;
        $word_count = mb_strlen(strip_tags($post->post_content), 'UTF-8');
        $reading_time = ceil($word_count / 300); // 假設每分鐘閱讀 300 字
        echo '<meta name="twitter:data2" content="約 ' . $reading_time . ' 分鐘" />' . "\n";
    }

    // JSON-LD 結構化數據輸出
    gd_output_json_ld( $d );
}

/**
 * 輸出 JSON-LD 結構化數據
 * 根據不同頁面類型調用相應的 JSON-LD 結構
 */
function gd_output_json_ld( $data ) {
    if ( is_front_page() || is_home() ) {
        // 首頁 JSON-LD
        gd_output_homepage_json_ld( $data );
    } elseif ( is_category() || is_tag() || is_tax() ) {
        // 分類/標籤/分類法頁面 JSON-LD
        gd_output_category_json_ld( $data );
    } elseif ( is_singular() ) {
        // 單篇文章/頁面 JSON-LD
        gd_output_single_json_ld( $data );
    } else {
        // 其他頁面使用基本 JSON-LD
        gd_output_basic_json_ld( $data );
    }
}

/**
 * 單篇文章/頁面的 JSON-LD 結構
 */
function gd_output_single_json_ld( $data ) {
    global $post;
    
    $graph = [];

    // 取得分類信息
    $categories = get_the_category($post->ID);
    $category_names = array();
    $category_links = array();
    foreach ($categories as $category) {
        $category_names[] = $category->name;
        $category_links[] = get_category_link($category->term_id);
    }

    // 取得標籤信息
    $tags = get_the_tags($post->ID);
    $tag_names = array();
    if ($tags) {
        foreach ($tags as $tag) {
            $tag_names[] = $tag->name;
        }
    }

    // 計算中文字數（更準確的統計方式）
    $word_count = 0;
    if ($post) {
        $content = strip_tags($post->post_content);
        // 移除空白字符
        $content = preg_replace('/\s+/', '', $content);
        // 計算字符數（中文環境下更合適）
        $word_count = mb_strlen($content, 'UTF-8');
    }

    // Article/BlogPosting 節點
    $article = [
        "@type" => "Article",
        "@id"   => $data['url'] . '#article',
        "isPartOf" => [ "@id" => $data['url'] . '#webpage' ],
        "author" => $data['author'] ? [ "@id" => home_url('/') . '#/schema/person/' . md5($data['author']) ] : null,
        "headline" => $data['title'],
        "alternativeHeadline" => $post ? get_the_title($post) : null,
        "description" => $data['description'],
        "datePublished" => $data['published_time'],
        "dateModified"  => $data['modified_time'],
        "mainEntityOfPage" => [ "@id" => $data['url'] . '#webpage' ],
        "wordCount" => $word_count > 0 ? $word_count : null,
        "commentCount" => $post ? get_comments_number( $post->ID ) : null,
        "publisher" => [ "@id" => home_url('/') . '#organization' ],
        "image" => $data['image'] ? [ "@id" => $data['url'] . '#primaryimage' ] : null,
        "thumbnailUrl" => $data['image'] ?: null,
        "keywords" => !empty($tag_names) ? implode(', ', $tag_names) : null,
        "articleSection" => !empty($category_names) ? $category_names : null,
        "about" => !empty($category_names) ? array_map(function($name, $link) {
            return [
                "@type" => "Thing",
                "name" => $name,
                "url" => $link
            ];
        }, $category_names, $category_links) : null,
        "mentions" => !empty($tag_names) ? array_map(function($name) {
            return [
                "@type" => "Thing",
                "name" => $name
            ];
        }, $tag_names) : null,
        "inLanguage" => $data['in_language'],
        "copyrightYear" => $data['published_time'] ? date('Y', strtotime($data['published_time'])) : null,
        "copyrightHolder" => [ "@id" => home_url('/') . '#organization' ],
        "potentialAction" => [
            [
                "@type" => "ReadAction",
                "target" => [ $data['url'] ]
            ],
            [
                "@type" => "CommentAction",
                "target" => [ $data['url'] . '#comments' ]
            ]
        ]
    ];

    $article = array_filter($article, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $article;

    // WebPage 節點
    $webpage = [
        "@type" => "WebPage",
        "@id"   => $data['url'] . '#webpage',
        "url"   => $data['url'],
        "name"  => $data['title'],
        "isPartOf" => [ "@id" => home_url('/') . '#website' ],
        "primaryImageOfPage" => $data['image'] ? [ "@id" => $data['url'] . '#primaryimage' ] : null,
        "image" => $data['image'] ? [ "@id" => $data['url'] . '#primaryimage' ] : null,
        "thumbnailUrl" => $data['image'] ?: null,
        "datePublished" => $data['published_time'],
        "dateModified"  => $data['modified_time'],
        "author" => $data['author'] ? [ "@id" => home_url('/') . '#/schema/person/' . md5($data['author']) ] : null,
        "description" => $data['description'],
        "breadcrumb" => [ "@id" => $data['url'] . '#breadcrumb' ],
        "inLanguage" => $data['in_language'],
        "potentialAction" => [
            [
                "@type" => "ReadAction",
                "target" => [ $data['url'] ]
            ]
        ]
    ];

    $webpage = array_filter($webpage, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $webpage;

    // BreadcrumbList 節點
    $breadcrumb_items = [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => get_bloginfo('name'),
            "item" => home_url('/')
        ]
    ];

    // 添加分類層級
    if (!empty($categories)) {
        $main_category = $categories[0]; // 取主要分類
        
        // 如果有父分類，先添加父分類
        if ($main_category->parent > 0) {
            $parent_category = get_category($main_category->parent);
            $breadcrumb_items[] = [
                "@type" => "ListItem",
                "position" => 2,
                "name" => $parent_category->name,
                "item" => get_category_link($parent_category->term_id)
            ];
            
            $breadcrumb_items[] = [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $main_category->name,
                "item" => get_category_link($main_category->term_id)
            ];
            
            $breadcrumb_items[] = [
                "@type" => "ListItem",
                "position" => 4,
                "name" => $data['title'],
                "item" => $data['url']
            ];
        } else {
            $breadcrumb_items[] = [
                "@type" => "ListItem",
                "position" => 2,
                "name" => $main_category->name,
                "item" => get_category_link($main_category->term_id)
            ];
            
            $breadcrumb_items[] = [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $data['title'],
                "item" => $data['url']
            ];
        }
    } else {
        // 沒有分類時，直接是文章
        $breadcrumb_items[] = [
            "@type" => "ListItem",
            "position" => 2,
            "name" => $data['title'],
            "item" => $data['url']
        ];
    }

    $breadcrumb = [
        "@type" => "BreadcrumbList",
        "@id" => $data['url'] . '#breadcrumb',
        "itemListElement" => $breadcrumb_items
    ];
    $graph[] = $breadcrumb;

    // 添加共用節點
    $graph = array_merge( $graph, gd_get_common_json_ld_nodes( $data ) );

    gd_output_json_ld_script( $graph );
}

/**
 * 首頁的 JSON-LD 結構
 */
function gd_output_homepage_json_ld( $data ) {
    $graph = [];

    // WebSite 節點（首頁特有的詳細信息）
    $website = [
        "@type" => "WebSite",
        "@id"   => home_url('/') . '#website',
        "url"   => home_url('/'),
        "name"  => $data['site_name'],
        "description" => get_bloginfo('description'),
        "publisher" => [ "@id" => home_url('/') . '#organization' ],
        "image" => $data['image'] ? [ "@id" => $data['url'] . '#primaryimage' ] : null,
        "inLanguage" => $data['in_language'],
        "potentialAction" => [
            [
                "@type" => "SearchAction",
                "target" => [
                    "@type" => "EntryPoint",
                    "urlTemplate" => home_url('/') . '?s={search_term_string}',
                ],
                "query-input" => [
                    "@type" => "PropertyValueSpecification",
                    "valueRequired" => true,
                    "valueName" => "search_term_string",
                ],
            ],
        ],
    ];
    $website = array_filter($website, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $website;

    // Organization 節點
    $organization = [
        "@type" => "Organization",
        "@id"   => home_url('/') . '#organization',
        "name"  => $data['site_name'],
        "url"   => home_url('/'),
        "logo"  => [
            "@type" => "ImageObject",
            "@id"   => home_url('/') . '#logo',
            "inLanguage" => $data['in_language'],
            "url"   => get_template_directory_uri() . '/source/img/logo.svg',
            "contentUrl" => get_template_directory_uri() . '/source/img/logo.svg',
            "caption" => $data['site_name'],
        ],
        "image" => [ "@id" => home_url('/') . '#logo' ],
        "sameAs" => [], // 可以添加社交媒體連結
    ];
    $organization = array_filter($organization, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $organization;

    // WebPage 節點
    $webpage = [
        "@type" => "WebPage",
        "@id"   => $data['url'] . '#webpage',
        "url"   => $data['url'],
        "name"  => $data['title'],
        "isPartOf" => [ "@id" => home_url('/') . '#website' ],
        "about" => [ "@id" => home_url('/') . '#organization' ],
        "description" => $data['description'],
        "inLanguage" => $data['in_language'],
    ];
    $webpage = array_filter($webpage, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $webpage;

    // 添加最新文章的 ItemList
    $latest_posts = get_posts([
        'numberposts' => 10,
        'post_status' => 'publish',
        'post_type' => 'post'
    ]);

    if ( $latest_posts ) {
        $item_list_elements = [];
        foreach ( $latest_posts as $index => $post ) {
            $item_list_elements[] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "item" => [
                    "@type" => "Article",
                    "@id" => get_permalink($post) . '#article',
                    "url" => get_permalink($post),
                    "headline" => get_the_title($post),
                    "datePublished" => get_the_date('c', $post),
                    "dateModified" => get_the_modified_date('c', $post),
                    "author" => [
                        "@type" => "Person",
                        "name" => get_the_author_meta('display_name', $post->post_author)
                    ]
                ]
            ];
        }

        $item_list = [
            "@type" => "ItemList",
            "@id" => home_url('/') . '#itemlist',
            "mainEntityOfPage" => [ "@id" => $data['url'] . '#webpage' ],
            "numberOfItems" => count($latest_posts),
            "itemListElement" => $item_list_elements
        ];
        $graph[] = $item_list;
    }

    // 添加共用節點（如果有圖片）
    if ( $data['image'] ) {
        $graph = array_merge( $graph, gd_get_common_json_ld_nodes( $data ) );
    }

    gd_output_json_ld_script( $graph );
}

/**
 * 分類頁面的 JSON-LD 結構
 */
function gd_output_category_json_ld( $data ) {
    global $wp_query;
    
    $graph = [];
    $queried_object = get_queried_object();

    // CollectionPage 節點
    $collection_page = [
        "@type" => "CollectionPage",
        "@id"   => $data['url'] . '#collectionpage',
        "url"   => $data['url'],
        "name"  => $data['title'],
        "isPartOf" => [ "@id" => home_url('/') . '#website' ],
        "description" => $data['description'],
        "inLanguage" => $data['in_language'],
    ];

    // 添加分類特定信息
    if ( is_category() && $queried_object ) {
        $collection_page["about"] = [
            "@type" => "Thing",
            "@id" => get_category_link($queried_object->term_id) . '#category',
            "name" => $queried_object->name,
            "description" => $queried_object->description ?: null,
        ];
    }

    $collection_page = array_filter($collection_page, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $collection_page;

    // WebPage 節點
    $webpage = [
        "@type" => "WebPage",
        "@id"   => $data['url'] . '#webpage',
        "url"   => $data['url'],
        "name"  => $data['title'],
        "isPartOf" => [ "@id" => home_url('/') . '#website' ],
        "description" => $data['description'],
        "breadcrumb" => [ "@id" => $data['url'] . '#breadcrumb' ],
        "inLanguage" => $data['in_language'],
    ];
    $webpage = array_filter($webpage, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $webpage;

    // 分類文章的 ItemList
    if ( have_posts() ) {
        $posts = $wp_query->posts;
        $item_list_elements = [];
        
        foreach ( $posts as $index => $post ) {
            $item_list_elements[] = [
                "@type" => "ListItem",
                "position" => $index + 1,
                "item" => [
                    "@type" => "Article",
                    "@id" => get_permalink($post) . '#article',
                    "url" => get_permalink($post),
                    "headline" => get_the_title($post),
                    "datePublished" => get_the_date('c', $post),
                    "dateModified" => get_the_modified_date('c', $post),
                    "author" => [
                        "@type" => "Person",
                        "name" => get_the_author_meta('display_name', $post->post_author)
                    ],
                    "image" => has_post_thumbnail($post) ? get_the_post_thumbnail_url($post, 'full') : null,
                ]
            ];
        }

        $item_list = [
            "@type" => "ItemList",
            "@id" => $data['url'] . '#itemlist',
            "mainEntityOfPage" => [ "@id" => $data['url'] . '#webpage' ],
            "numberOfItems" => count($posts),
            "itemListElement" => array_filter($item_list_elements, function($item) {
                return !empty($item['item']['headline']);
            })
        ];
        
        if ( !empty($item_list['itemListElement']) ) {
            $graph[] = $item_list;
        }
    }

    // 添加共用節點
    $graph = array_merge( $graph, gd_get_common_json_ld_nodes( $data ) );

    gd_output_json_ld_script( $graph );
}

/**
 * 基本的 JSON-LD 結構（用於其他頁面）
 */
function gd_output_basic_json_ld( $data ) {
    $graph = [];

    // WebPage 節點
    $webpage = [
        "@type" => "WebPage",
        "@id"   => $data['url'] . '#webpage',
        "url"   => $data['url'],
        "name"  => $data['title'],
        "isPartOf" => [ "@id" => home_url('/') . '#website' ],
        "description" => $data['description'],
        "inLanguage" => $data['in_language'],
    ];
    $webpage = array_filter($webpage, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
    $graph[] = $webpage;

    // 添加共用節點
    $graph = array_merge( $graph, gd_get_common_json_ld_nodes( $data ) );

    gd_output_json_ld_script( $graph );
}

/**
 * 取得共用的 JSON-LD 節點（ImageObject, WebSite, Person 等）
 */
function gd_get_common_json_ld_nodes( $data ) {
    $nodes = [];

    // ImageObject 節點
    if ( ! empty( $data['image'] ) ) {
        $image = [
            "@type" => "ImageObject",
            "@id"   => $data['url'] . '#primaryimage',
            "inLanguage" => $data['in_language'],
            "url"   => $data['image'],
            "contentUrl" => $data['image'],
        ];
        if ( ! empty( $data['image_width'] ) ) $image['width'] = (int) $data['image_width'];
        if ( ! empty( $data['image_height'] ) ) $image['height'] = (int) $data['image_height'];
        if ( ! empty( $data['image_type'] ) ) $image['encodingFormat'] = $data['image_type'];
        $image = array_filter($image, function($v){ return !is_null($v) && $v !== ''; });
        $nodes[] = $image;
    }

    // WebSite 節點（基本版）
    if ( ! is_front_page() && ! is_home() ) {
        $website = [
            "@type" => "WebSite",
            "@id"   => home_url('/') . '#website',
            "url"   => home_url('/'),
            "name"  => $data['site_name'],
            "description" => get_bloginfo('description'),
            "publisher" => [ "@id" => home_url('/') . '#organization' ],
            "inLanguage" => $data['in_language'],
            "potentialAction" => [
                [
                    "@type" => "SearchAction",
                    "target" => [
                        "@type" => "EntryPoint",
                        "urlTemplate" => home_url('/') . '?s={search_term_string}',
                    ],
                    "query-input" => [
                        "@type" => "PropertyValueSpecification",
                        "valueRequired" => true,
                        "valueName" => "search_term_string",
                    ],
                ],
            ],
        ];
        $website = array_filter($website, function($v){ return !is_null($v) && $v !== '' && $v !== []; });
        $nodes[] = $website;
    }

    // Person 節點（作者）
    if ( ! empty( $data['author'] ) ) {
        $person = [
            "@type" => "Person",
            "@id"   => home_url('/') . '#/schema/person/' . md5($data['author']),
            "name"  => $data['author'],
        ];
        if ( ! empty( $data['author_url'] ) ) $person['url'] = $data['author_url'];
        $person = array_filter($person, function($v){ return !is_null($v) && $v !== ''; });
        $nodes[] = $person;
    }

    return $nodes;
}

/**
 * 輸出 JSON-LD 腳本標籤
 */
function gd_output_json_ld_script( $graph ) {
    $schema = [
        "@context" => "https://schema.org",
        "@graph" => $graph,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

add_action( 'wp_head', 'gd_output_seo_meta', 1 );
// ...existing code...
?>