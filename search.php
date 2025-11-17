<?php
// 獲取搜尋關鍵字
$search_query = '';
if (isset($_GET['gs']) && !empty($_GET['gs'])) {
    $search_query = sanitize_text_field($_GET['gs']);
}

get_header();
 
// 為搜索頁面添加 SEO Meta 標籤
if (!empty($search_query)) {
    echo '<meta name="description" content="搜索 ' . esc_attr($search_query) . ' 的相關結果，找到應用程式、文章和贊助內容。" />' . "\n";
    echo '<meta property="og:title" content="搜索結果：' . esc_attr($search_query) . '" />' . "\n";
    echo '<meta property="og:description" content="搜索 ' . esc_attr($search_query) . ' 的相關結果" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta name="robots" content="noindex, follow" />' . "\n";

    // 添加結構化數據
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "SearchResultsPage",
        "url" => home_url("/?gs=" . urlencode($search_query)),
        "name" => "搜索結果：" . $search_query,
        "description" => "搜索 " . $search_query . " 的相關結果",
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => home_url("/?gs={search_term_string}"),
            "query-input" => "required name=search_term_string"
        ]
    ];

    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
}
?>

<div class="container mx-auto px-5 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">
        搜尋結果
        <?php if (!empty($search_query)) : ?>
            for "<?php echo esc_html($search_query); ?>"
        <?php endif; ?>
    </h1>

    <?php
    $results_count = $wp_query->found_posts;
    if ($results_count > 0) : ?>
        <p class="text-gray-600 mb-6">
            找到約 <?php echo $results_count; ?> 個相關結果
            <?php if (have_posts()) : ?>
                <?php
                // 預先計算總顯示數量（需要先遍歷一遍來統計）
                rewind_posts();
                $preview_app_count = 0;
                $preview_sponsored_count = 0;
                $preview_other_count = 0;
                $max_app_display = 4;
                $max_sponsored_display = 2;
                $max_other_display = 6;

                while (have_posts()) : the_post();
                    $categories = wp_get_post_categories(get_the_ID());
                    $is_app = false;
                    $is_sponsored = false;

                    foreach ($categories as $cat_id) {
                        $term = get_term($cat_id, 'category');
                        if ($term && !is_wp_error($term)) {
                            $parent_id = $term->parent ?: $cat_id;
                            if ($parent_id == 1768 || $cat_id == 1768) {
                                $is_app = true;
                            }
                            if ($cat_id == 1661 || $cat_id == 1779) {
                                $is_sponsored = true;
                            }
                        }
                    }

                    if ($is_app && !$is_sponsored && $preview_app_count < $max_app_display) {
                        $preview_app_count++;
                    } elseif ($is_sponsored && $preview_sponsored_count < $max_sponsored_display) {
                        $preview_sponsored_count++;
                    } elseif (!$is_app && !$is_sponsored && $preview_other_count < $max_other_display) {
                        $preview_other_count++;
                    }
                endwhile;

                $total_will_display = $preview_app_count + $preview_sponsored_count + $preview_other_count;
                rewind_posts();
                ?>
                (僅顯示<?php echo $total_will_display; ?>筆)
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <?php if (have_posts()) : ?>
        <?php
        // 重置查詢指針到開始
        rewind_posts();

        // 第一次遍歷：收集app文章 (限制4個)
        $app_posts_html = '';
        $app_count = 0;
        $app_displayed = 0;
        $max_app_display = 4;

        while (have_posts() && $app_displayed < $max_app_display) : the_post();
            $categories = wp_get_post_categories(get_the_ID());
            $is_app = false;
            $is_sponsored = false;

            foreach ($categories as $cat_id) {
                $term = get_term($cat_id, 'category');
                if ($term && !is_wp_error($term)) {
                    $parent_id = $term->parent ?: $cat_id;
                    // 檢查是否為app分類
                    if ($parent_id == 1768 || $cat_id == 1768) {
                        $is_app = true;
                    }
                    // 檢查是否為贊助文章
                    if ($cat_id == 1661 || $cat_id == 1779) {
                        $is_sponsored = true;
                    }
                }
            }

            // 只收集app文章，且不是贊助文章
            if ($is_app && !$is_sponsored) {
                $app_count++;
                if ($app_displayed < $max_app_display) {
                    $app_displayed++;
                    ob_start();
                    get_template_part('part/card', 'app', get_post_format());
                    $app_posts_html .= ob_get_clean();
                }
            }
        endwhile;

        // 重置查詢指針到開始
        rewind_posts();

        // 第二次遍歷：收集贊助文章 (限制2個)
        $sponsored_posts_html = '';
        $sponsored_count = 0;
        $sponsored_displayed = 0;
        $max_sponsored_display = 2;

        while (have_posts() && $sponsored_displayed < $max_sponsored_display) : the_post();
            $categories = wp_get_post_categories(get_the_ID());
            $is_sponsored = false;

            foreach ($categories as $cat_id) {
                // 檢查是否為贊助文章 (分類 ID 1661 和 1779)
                if ($cat_id == 1661 || $cat_id == 1779) {
                    $is_sponsored = true;
                    break;
                }
            }

            if ($is_sponsored) {
                $sponsored_count++;
                if ($sponsored_displayed < $max_sponsored_display) {
                    $sponsored_displayed++;
                    ob_start();
                    get_template_part('part/card', 'web', get_post_format());
                    $sponsored_posts_html .= ob_get_clean();
                }
            }
        endwhile;

        // 重置查詢指針到開始
        rewind_posts();

        // 第三次遍歷：收集文章 (限制6個)
        $other_posts_html = '';
        $other_count = 0;
        $other_displayed = 0;
        $max_other_display = 6;

        while (have_posts() && $other_displayed < $max_other_display) : the_post();
            $categories = wp_get_post_categories(get_the_ID());
            $is_app = false;
            $is_sponsored = false;

            foreach ($categories as $cat_id) {
                $term = get_term($cat_id, 'category');
                if ($term && !is_wp_error($term)) {
                    $parent_id = $term->parent ?: $cat_id;
                    // 檢查是否為app分類
                    if ($parent_id == 1768 || $cat_id == 1768) {
                        $is_app = true;
                    }
                    // 檢查是否為贊助文章
                    if ($cat_id == 1661 || $cat_id == 1779) {
                        $is_sponsored = true;
                    }
                }
            }

            // 只收集非app且非贊助的文章
            if (!$is_app && !$is_sponsored) {
                $other_count++;
                if ($other_displayed < $max_other_display) {
                    $other_displayed++;
                    ob_start();
                    get_template_part('part/card', 'web', get_post_format());
                    $other_posts_html .= ob_get_clean();
                }
            }
        endwhile;
        ?>

        <?php
        // 計算總顯示筆數
        $total_displayed = $other_displayed + $app_displayed + $sponsored_displayed;
        ?>

        <?php if ($other_displayed > 0) : ?>
            <!-- 文章區塊 -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                        </path>
                    </svg>
                    文章
                    <?php if ($other_count > $max_other_display) : ?>
                        (顯示 <?php echo $other_displayed; ?> / 共 <?php echo $other_count; ?> 個)
                    <?php else : ?>
                        (<?php echo $other_count; ?>)
                    <?php endif; ?>
                </h2>
                <div class="grid grid-cols-1 gap-y-10 gap-x-4 md:grid-cols-2 lg:grid-cols-3">
                    <?php echo $other_posts_html; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($app_displayed > 0) : ?>
            <!-- App 分類區塊 -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    APP 應用程式
                    <?php if ($app_count > $max_app_display) : ?>
                        (顯示 <?php echo $app_displayed; ?> / 共 <?php echo $app_count; ?> 個)
                    <?php else : ?>
                        (<?php echo $app_count; ?>)
                    <?php endif; ?>
                </h2>
                <div class="grid grid-cols-1 gap-y-10 gap-x-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <?php echo $app_posts_html; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($sponsored_displayed > 0) : ?>
            <!-- 贊助文章區塊 -->
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                        </path>
                    </svg>
                    贊助文章
                    <?php if ($sponsored_count > $max_sponsored_display) : ?>
                        (顯示 <?php echo $sponsored_displayed; ?> / 共 <?php echo $sponsored_count; ?> 個)
                    <?php else : ?>
                        (<?php echo $sponsored_count; ?>)
                    <?php endif; ?>
                </h2>
                <div class="grid grid-cols-1 gap-y-10 gap-x-4 md:grid-cols-2 lg:grid-cols-3">
                    <?php echo $sponsored_posts_html; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 調整關鍵字提示和搜尋框 -->
        <div class="mt-12 bg-gray-50 rounded-lg p-6 text-center">
            <p class="text-gray-700 mb-4 text-lg">
                搜尋結果限制數量，如未列出嘗試調整關鍵字
            </p>
            <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="max-w-md mx-auto">
                <div class="flex">
                    <input type="search"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500"
                        placeholder="重新輸入搜尋關鍵字..." value="<?php echo esc_attr($search_query); ?>" name="gs"
                        autocomplete="off" />
                    <button type="submit"
                        class="px-6 py-3 bg-gray-600 text-white rounded-r-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        搜尋
                    </button>
                </div>
            </form>
        </div>

    <?php else : ?>
        <!-- 重新搜尋表單 -->
        <div class="bg-white border rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">重新搜尋</h2>
            <form role="search" method="get" action="<?php echo home_url('/'); ?>">
                <div class="flex">
                    <input type="search"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                        placeholder="輸入搜尋關鍵字..." value="<?php echo esc_attr($search_query); ?>" name="gs" />
                    <button type="submit" class="px-6 py-3 bg-gray-600 text-white rounded-r-lg hover:bg-gray-700">
                        搜尋
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php
wp_reset_postdata();
get_footer();
?>