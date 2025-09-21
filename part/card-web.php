 <?php
 
 
 
  

/*  獲取縮圖 ID */

$thumbnail_id = get_post_thumbnail_id(get_the_ID());

$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, array(400,200,true))[0];
 
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
     class="group block max-w-2xl mb-12 overflow-hidden shadow-md rounded-xl max-w-[400px] mx-auto relative"
     style="background-color: <?php echo esc_attr($final_light_color); ?>; background-opacity: 0.3;">

     <div class="overflow-hidden">
         <img onload="this.classList.add('opacity-100','scale-100')"
             class="object-cover w-full h-[200px] opacity-0 scale-100 transition-all duration-300 ease-in-out group-hover:scale-110"
             src="<?php echo esc_url($thumbnail_url); ?>"
             alt="<?php echo esc_attr(get_the_title() . ' - ' . $category_name); ?>">
     </div>

     <div class="h-20 px-6 mt-10 mb-10 overflow-hidden">
         <h2 class="font-semibold text-center text-gray-500 text-xxl">
             <?php the_title(); ?>
         </h2>
     </div>

     <div class="flex items-center justify-center pb-5 mt-8 font-extrabold">
         <div class="w-1/2 text-center">
             <span class="inline-block px-3 py-1 text-opacity-75 rounded-xl" style="background-color: <?php echo esc_attr($final_base_color ?: '#f3f3f3'); ?>80; /* 16进制加 alpha */
             color: <?php echo esc_attr($final_light_color ?: '#000'); ?>;">
                 <?php echo esc_html($category_name ?: '未分類'); ?>
             </span>
         </div>
         <time class="w-1/2 text-xs text-center text-gray-600 no-copy">
             <?php echo esc_html(get_the_date()); ?>
         </time>
     </div>
 </a>