<!-- Article metadata -->
<div class="mt-8 pt-6 border-t border-gray-200 mx-auto max-w-4xl">
    <div class="flex flex-wrap items-center justify-between text-sm text-gray-600">
        <div class="flex items-center space-x-4 mb-2 md:mb-0">
            <?php
            // 獲取文章分類 (只顯示最後一個)
            $categories = get_the_category();
            if (!empty($categories)) {
                $last_category = end($categories);
                echo '<a href="' . esc_url(get_category_link($last_category->term_id)) . '" class="flex items-center hover:text-gray-800 transition-colors">';
                echo '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">';
                echo '<path fill-rule="evenodd" d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm4 2a1 1 0 100 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>';
                echo '</svg>';
                echo esc_html($last_category->name);
                echo '</a>';
            }
            ?>
        </div>
        <div class="flex items-center space-x-4 text-xs">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd"></path>
                </svg>
                發布：
                <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>" class="ml-1">
                    <?php echo get_the_date('Y年m月d日'); ?>
                </time>
            </span>
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                    </path>
                </svg>
                更新：
                <time itemprop="dateModified" datetime="<?php echo get_the_modified_date('c'); ?>" class="ml-1">
                    <?php echo get_the_modified_date('Y年m月d日'); ?>
                </time>
            </span>
        </div>
    </div>

    <!-- Publisher info (hidden structured data) -->
    <span itemprop="publisher" itemscope itemtype="https://schema.org/Organization" style="display: none;">
        <span itemprop="name"><?php bloginfo('name'); ?></span>
        <span itemprop="url"><?php echo home_url(); ?></span>
    </span>
</div>