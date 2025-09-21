<?php

//接受傳遞的參數 cat_Id parent_id 
$cat_Id = $args['cat_Id'];
$parent_id = $args['parent_id'];
$is_parent = $args['is_parent'];
 
 
if ($is_parent ) {
    // 获取子分类
    $args = array(
        'child_of' => $cat_Id,
        'taxonomy' => 'category',
        'hide_empty' => false, // 是否隐藏没有文章的分类
    );
    $child_terms = get_terms($args);

    // 检查是否有子分类
    if (!empty($child_terms) && !is_wp_error($child_terms)) {
        echo '<div class="flex items-center justify-center py-10">';
        echo '<div class="flex overflow-x-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">';
        foreach ($child_terms as $child_term) {
            echo '<button class="my-2 rounded-xl whitespace-nowrap  bg-white  flex mx-3 ">';
            echo '<a href="' . esc_url(get_term_link($child_term)) . '" class="    px-5 py-3  text-gray-500 font-bold no-underline">' . esc_html($child_term->name) . '</a>';
            echo '</button>';
        }
        echo '</div>';
        echo '</div>';
    } else {
        echo '<p class="text-center text-gray-500">没有子分类。</p>';
    }
} else {  
    // 開始麵包屑導航
    echo '<ol class="flex items-center whitespace-nowrap p-2  justify-center mb-5">';
    echo '<li class="my-2 rounded-xl whitespace-nowrap bg-gray-100 mx-3 flex">';
    echo '<a class="flex items-center text-sm text-gray-500   font-bold py-3 px-5  justify-center  focus:outline-none  " href="' . home_url() . '">';
    echo '<svg class="shrink-0 me-3 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
    echo '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>';
    echo '<polyline points="9 22 9 12 15 12 15 22"></polyline>';
    echo '</svg>';
    echo 'Home</a>';
    echo '</li>';

    echo '<svg class="shrink-0 mx-2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
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
        

                echo '<li class="my-2 rounded-xl whitespace-nowrap bg-gray-100 mx-3 flex ">';
                echo '<a href="' . esc_url(get_category_link($parent_category->term_id)) . '" class="text-gray-500  py-3 px-5    font-bold no-underline">';
                echo esc_html($parent_category->name);
                echo '</a>';
                echo '</li>';
                echo '<svg class="shrink-0 mx-2 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
                echo '<path d="m9 18 6-6-6-6"></path>';
                echo '</svg>';
            }

            // 當前分類
            echo '<li class="my-2 rounded-xl whitespace-nowrap bg-gray-100 mx-3 flex text-gray-500 py-3 px-5    font-bold no-underline " aria-current="page">';
            echo esc_html($parent_cat->name);
            echo '</li>';
        }
    }

    echo '</ol>';
}
?>