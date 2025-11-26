<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * ==============================================================================
 * Core Functionality (核心功能)
 * ==============================================================================
 */
require_once get_template_directory() . '/function/Core/theme-setup.php';
require_once get_template_directory() . '/function/Core/security.php';
require_once get_template_directory() . '/function/Core/performance.php';
require_once get_template_directory() . '/function/Core/admin.php';
require_once get_template_directory() . '/function/Core/feed.php';
/**
 * ==============================================================================
 * Extensions (擴展功能)
 * ==============================================================================
 */
/* Carbon Fields */
require_once get_template_directory() . '/function/Extensions/carbon_fields.php';

/* SEO Meta */
require_once get_template_directory() . '/function/Extensions/seoMeta.php';

/* 廣告插入 */
require_once get_template_directory() . '/function/Extensions/adsense_inserter.php';

/* 文章搜尋優化 */
require_once get_template_directory() . '/function/Extensions/re_search.php';

/* 分頁導航 */
require_once get_template_directory() . '/function/Extensions/wpbeginner_numeric_posts_nav.php';

/* 短代碼 */
require_once get_template_directory() . '/function/Extensions/url_short_code.php';
require_once get_template_directory() . '/function/Extensions/adsense_short_code.php';

/**
 * ==============================================================================
 * Utils (輔助工具)
 * ==============================================================================
 */
/* 工具函數 */
require_once get_template_directory() . '/function/Utils/utils.php';

/* 特色圖片取色 */
require_once get_template_directory() . '/function/Utils/color_matcher.php';

/* 圖片處理 */
require_once get_template_directory() . '/function/Utils/image_processor.php';

/* LCP 優化 */
require_once get_template_directory() . '/function/Utils/lcp_optimizer.php';

/* 分類前綴移除 */
require_once get_template_directory() . '/function/Utils/category_prefix_remover.php';


/**
 * ==============================================================================
 * Initialization (初始化 - 運行一次)
 * ==============================================================================
 */
require_once get_template_directory() . '/once/migrate-app-name-meta.php';
require_once get_template_directory() . '/once/add-tailwind_color.php';
require_once get_template_directory() . '/once/convert-h3-to-h2-batch.php';




//暫時

// 修改標題分隔符號為短橫線
add_filter('document_title_separator', function($sep) {
    return '-';
});

