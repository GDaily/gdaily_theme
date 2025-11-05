 <?php






    $thumbnail_id = get_post_thumbnail_id(get_the_ID());

    /*  獲取縮圖 ID */
    $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, array(400, 200, true))[0];

    // 獲取 hex 顏色欄位
    $tailwind_hex_base_color = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_base_color');
    $tailwind_hex_light_color = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_light_color');
    $tailwind_hex_base_color_custom = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_base_color_custom');
    $tailwind_hex_light_color_custom = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_light_color_custom');

    // 決定最終使用的顏色值
    $final_base_color = !empty($tailwind_hex_base_color_custom) ? $tailwind_hex_base_color_custom : $tailwind_hex_base_color;
    $final_light_color = !empty($tailwind_hex_light_color_custom) ? $tailwind_hex_light_color_custom : $tailwind_hex_light_color;

    $meta_data = wp_get_attachment_metadata($thumbnail_id);





    // 获取当前文章的分类信息
    $category_id = wp_get_post_categories(get_the_ID(), array('fields' => 'ids'))[0] ?? null;

    if ($category_id) {
        $category = get_category($category_id);
        $category_name = $category->name;
        $category_url = get_category_link($category_id);
    }

    ?>


 <a href="<?php the_permalink(); ?>"
     class="group block   mb-12 overflow-hidden shadow rounded-xl max-h-[400px] max-w-[400px] mx-auto relative"
     style="background-color: <?php echo esc_attr($final_light_color); ?>; background-opacity: 0.3;">

     <div class=" m-3 overflow-hidden rounded-md aspect-video" style="border-radius: 8px;">

         <img onload=" this.classList.add('opacity-100','scale-100')"
             class="object-cover w-full h-full opacity-0 scale-100 transition-all duration-300 ease-in-out group-hover:scale-110"
             src="<?php echo esc_url($thumbnail_url); ?>"
             alt="<?php echo esc_attr(get_the_title() . ' - ' . $category_name); ?>">
     </div>


     <div class="h-20 px-6 pt-3 mt-6 mb-8 overflow-hidden">
         <h2 class="font-semibold text-center text-gray-500 text-xxl">
             <?php the_title(); ?>
         </h2>
     </div>

     <div class="flex items-center justify-center pb-5 mt-8 font-extrabold">
         <div class="w-1/2 text-left">

             <span class="inline-block px-3 py-1 ml-4 rounded-xl"
                 style="  color: <?php echo esc_attr($final_base_color ?: '#000'); ?>;">
                 <svg class="inline w-4 h-4 " fill="currentColor" viewBox="0 0 20 20">
                     <path fill-rule="evenodd"
                         d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                         clip-rule="evenodd"></path>
                 </svg>


                 <?php echo esc_html($category_name ?: '未分類'); ?>
             </span>

         </div>
         <time class="w-1/2 text-xs text-center text-gray-600 no-copy">
             <?php echo esc_html(get_the_date()); ?>
         </time>
     </div>
 </a>