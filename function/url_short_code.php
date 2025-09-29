<?php
function gd_url_box_shortcode($atts) {

        $atts = shortcode_atts(array(
            'postid' => '',
        ), $atts, 'url_box');


    $post_id = intval($atts['postid']);
    if (empty($post_id)) {
        return '<p class="text-red-500">⚠ 請輸入文章 ID</p>';
    }

    $post = get_post($post_id);
    if (!$post) {
        return '<p class="text-red-500">⚠ 找不到文章</p>';
    }


    // 縮圖尺寸依分類
    $thumbnail_id = get_post_thumbnail_id($post_id);
    $use_app_size = false;
    $categories = get_the_category($post_id);
    if ($categories) {
        foreach ($categories as $cat) {
            $parent_id = $cat->term_id;
            // 檢查是否為 1768 或其子分類
            while ($parent_id && $parent_id != 0) {
                if ($parent_id == 1768) {
                    $use_app_size = true;
                    break 2;
                }
                $parent = get_category($parent_id);
                $parent_id = $parent ? $parent->parent : 0;
            }
        }
    }

    if ($use_app_size) {
        $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, array(100, 100, true))[0] : '';
        $thumbnail_url_mobile = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, array(64, 64, true))[0] : '';
    } else {
        $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, array(200, 100, true))[0] : '';
        $thumbnail_url_mobile = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, array(64, 64, true))[0] : '';
    }

    // 標題
    $title = esc_html(get_the_title($post_id));

    // 摘要
    $excerpt = has_excerpt($post_id)
        ? get_the_excerpt($post_id)
        : wp_trim_words(strip_tags($post->post_content), 150);

    // 文章連結
    $permalink = get_permalink($post_id);

    ob_start(); ?>

<a href="<?php echo esc_url($permalink); ?>" class=" w-full   flex">
    <article
        class="flex  relative w-full my-3 !mr-0 !ml-0 px-3 py-2 md:p-5 justify-center items-center  bg-gray-100 rounded-lg hover:bg-gray-150 group-hover:bg-gray-150">
        <?php if ($thumbnail_url) : ?>
        <figure class="flex-shrink-0 md:w-auto md:mb-0 md:mr-4">
            <picture>
                <source media="(max-width: 767px)" srcset="<?php echo esc_url($thumbnail_url_mobile); ?>">
                <img decoding="async" class="h-auto w-auto object-cover rounded-lg"
                    src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>">
            </picture>
        </figure>
        <?php endif; ?>

        <div class="flex-1 px-3 md:px-5">
            <header class="!p-0 !m-0">
                <h2
                    class="text-xl   font-bold !p-0 !m-0 tracking-tight leading-snug !text-gray-800 !bg-gray-100 line-clamp-1 md:line-clamp-none">
                    <?php echo esc_html($title); ?>
                </h2>
            </header>

            <div class="h-8 sm:h-12 overflow-hidden">
                <p class="!text-xs sm:!text-base text-gray-700 dark:text-gray-400">
                    <?php echo !wp_is_mobile() ? esc_html($excerpt) : esc_html(wp_trim_words(strip_tags($post->post_content), 100)); ?>
                </p>
            </div>
        </div>
        <!-- 右下角標籤 -->
        <div class="absolute right-2 bottom-0">
            <span class="bg-blue-100 text-blue-600 text-xs font-semibold px-2 py-0.5 rounded">
                相關文章
            </span>
        </div>
    </article>



</a>

<?php
    return ob_get_clean();
}
add_shortcode('url_box', 'gd_url_box_shortcode');