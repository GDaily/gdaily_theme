<?php

//接受傳遞的參數 cat_Id parent_id 
$cat_Id = $args['cat_Id'];
$parent_id = $args['parent_id'];
$is_parent = $args['is_parent'];


if ($is_parent) {
    // 获取子分类
    $args = array(
        'child_of' => $cat_Id,
        'taxonomy' => 'category',
        'hide_empty' => false, // 是否隐藏没有文章的分类
    );
    $child_terms = get_terms($args);

    // 检查是否有子分类
    if (!empty($child_terms) && !is_wp_error($child_terms)) {
        // 獲取當前分類資訊
        $current_category = get_category($cat_Id);

        // 精簡的麵包屑導航 (Home > 下拉選單)
        echo '<ol class="flex items-center justify-center p-10 whitespace-nowrap">';
        echo '<li class="flex mx-3 my-2 bg-white shadow rounded-xl whitespace-nowrap">';
        echo '<a class="flex items-center justify-center px-3 py-2 text-sm font-bold text-gray-500 focus:outline-none " href="' . home_url() . '">';
        echo '<svg class=" shrink-0  size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
        echo '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>';
        echo '<polyline points="9 22 9 12 15 12 15 22"></polyline>';
        echo '</svg>';
        echo '<span class="ml-2 hidden md:inline">Home</span></a>';
        echo '</li>';

        echo '<svg class="md:mx-2 text-gray-400 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
        echo '<path d="m9 18 6-6-6-6"></path>';
        echo '</svg>';

        // 下拉選單作為麵包屑的一部分
        echo '<li class="flex mx-3 my-2 bg-white shadow rounded-xl whitespace-nowrap">';
        echo '<div class="relative inline-block" id="custom-dropdown">';

        // 自定義下拉按鈕 (顯示當前分類名稱)
        echo '<button type="button" class="px-5 py-2 text-sm font-bold text-gray-500 focus:outline-none flex items-center" onclick="toggleDropdown()">';
        echo '<span id="selected-text">' . esc_html($current_category->name) . '</span>';
        echo '<svg class="w-4 h-4 text-gray-500 transition-transform duration-200 ml-2 flex-shrink-0" id="dropdown-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"></path>';
        echo '</svg>';
        echo '</button>';

        // 自定義下拉選項 (獨立寬度)
        echo '<div id="dropdown-options" class="absolute top-full left-0 mt-1 bg-white shadow-xl rounded-xl border-2 border-gray-100 z-50 max-h-60 overflow-y-auto hidden min-w-[200px] whitespace-nowrap">';
        foreach ($child_terms as $child_term) {
            echo '<a href="' . esc_url(get_term_link($child_term)) . '" class="block px-6 py-3 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-800 transition-colors duration-150 first:rounded-t-xl last:rounded-b-xl font-medium">' . esc_html($child_term->name) . '</a>';
        }
        echo '</div>';

        echo '</div>';
        echo '</li>';
        echo '</ol>';

        // JavaScript for dropdown functionality
        echo '<script>
        function toggleDropdown() {
            const dropdown = document.getElementById("dropdown-options");
            const arrow = document.getElementById("dropdown-arrow");
            
            if (dropdown.classList.contains("hidden")) {
                dropdown.classList.remove("hidden");
                arrow.style.transform = "rotate(180deg)";
            } else {
                dropdown.classList.add("hidden");
                arrow.style.transform = "rotate(0deg)";
            }
        }
        
        // 點擊外部關閉下拉選單
        document.addEventListener("click", function(event) {
            const dropdown = document.getElementById("custom-dropdown");
            if (dropdown && !dropdown.contains(event.target)) {
                document.getElementById("dropdown-options").classList.add("hidden");
                document.getElementById("dropdown-arrow").style.transform = "rotate(0deg)";
            }
        });
        </script>';
    } else {
        echo '<p class="text-center text-gray-500">没有子分类。</p>';
    }
} else {
    // 開始麵包屑導航
    echo '<ol class="flex items-center justify-center p-10 whitespace-nowrap">';
    echo '<li class="flex mx-3 my-2 bg-white shadow rounded-xl whitespace-nowrap">';
    echo '<a class="flex items-center justify-center px-3 py-2 text-sm font-bold text-gray-500 focus:outline-none " href="' . home_url() . '">';
    echo '<svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
    echo '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>';
    echo '<polyline points="9 22 9 12 15 12 15 22"></polyline>';
    echo '</svg>';
    echo '<span class="ml-2 hidden md:inline">Home</span></a>';
    echo '</li>';

    echo '<svg class="md:mx-2 text-gray-400 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
    echo '<path d="m9 18 6-6-6-6"></path>';
    echo '</svg>';

    // 檢查是否是分類或單篇文章
    if (is_category() || is_single()) {
        $category = get_the_category();

        if (!empty($category)) {
            $parent_cat = $category[0];

            // 若有父分類，顯示父分類
            if ($parent_cat->parent != 0) {
                $parent_category = get_category($parent_cat->parent);


                echo '<li class="flex mx-1 md:mx-3 my-2 bg-white shadow rounded-xl whitespace-nowrap ">';
                echo '<a href="' . esc_url(get_category_link($parent_category->term_id)) . '" class="px-5 py-2 font-bold text-gray-500 no-underline">';
                echo esc_html($parent_category->name);
                echo '</a>';
                echo '</li>';
                echo '<svg class=" md:mx-2 text-gray-400 shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
                echo '<path d="m9 18 6-6-6-6"></path>';
                echo '</svg>';
            }

            // 當前分類
            echo '<li class="flex px-5 py-2 mx-3 my-2 font-bold text-gray-500 no-underline bg-white shadow rounded-xl whitespace-nowrap " aria-current="page">';
            echo esc_html($parent_cat->name);
            echo '</li>';
        }
    }

    echo '</ol>';
}
