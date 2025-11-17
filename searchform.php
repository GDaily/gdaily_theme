<?php

/**
 * 自定義搜索表單模板
 * 使用自定義 "gs" 參數而非預設的 "s" 參數
 */
?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form">
    <div class="search-form-wrapper flex">
        <label for="search-field" class="screen-reader-text"><?php _e('搜尋關鍵字:', 'textdomain'); ?></label>
        <input
            type="search"
            id="search-field"
            class="search-field flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="<?php echo esc_attr_x('輸入搜尋關鍵字...', 'placeholder', 'textdomain'); ?>"
            value="<?php echo get_search_query(); ?>"
            name="gs"
            autocomplete="off"
            required />
        <button
            type="submit"
            class="search-submit px-6 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            aria-label="<?php echo esc_attr_x('提交搜尋', 'submit button', 'textdomain'); ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="sr-only"><?php echo _x('搜尋', 'submit button', 'textdomain'); ?></span>
        </button>
    </div>
</form>