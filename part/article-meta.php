<!-- Article metadata -->
<div class="mt-8 pt-6 border-t border-gray-200 mx-auto max-w-4xl">
    <div class="flex flex-wrap items-center justify-between text-sm text-gray-600">
        <div class="flex items-center space-x-4 mb-2 md:mb-0">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                        clip-rule="evenodd"></path>
                </svg>
                作者：
                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name"><?php echo get_the_author(); ?></span>
                </span>
            </span>
        </div>
        <div class="flex items-center space-x-4 text-xs">
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd"></path>
                </svg>
                發布：
                <time itemprop="datePublished" datetime="<?php echo get_the_date('c'); ?>">
                    <?php echo get_the_date('Y年m月d日'); ?>
                </time>
            </span>
            <?php if (get_the_date() !== get_the_modified_date()) : ?>
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                    </path>
                </svg>
                更新：
                <time itemprop="dateModified" datetime="<?php echo get_the_modified_date('c'); ?>">
                    <?php echo get_the_modified_date('Y年m月d日'); ?>
                </time>
            </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Publisher info (hidden structured data) -->
    <span itemprop="publisher" itemscope itemtype="https://schema.org/Organization" style="display: none;">
        <span itemprop="name"><?php bloginfo('name'); ?></span>
        <span itemprop="url"><?php echo home_url(); ?></span>
    </span>
</div>