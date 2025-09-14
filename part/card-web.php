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

 <div x-data="{ scale: false }" @mouseenter="scale = true" @mouseleave="scale = false" 
      class="block max-w-2xl mb-12 overflow-hidden shadow-md rounded-xl max-w-[400px] mx-auto relative" 
      style="background-color: <?php echo $final_light_color; ?>; background-opacity: 0.3;"
      @mouseenter="scale = true">

     <a href="<?php the_permalink(); ?>">
         <div class="overflow-hidden ">
             <img class="object-cover w-full   h-[200px] transition-transform duration-300"
                 :class="scale ? 'scale-110' : ''" src="<?php echo $thumbnail_url; ?>" alt="Article">
         </div>

         <div class="h-20 px-6 mt-10 mb-10 overflow-hidden ">
             <h3 class="font-semibold text-center text-gray-500 text-xxl">
                 <?php the_title(); ?>
             </h3>
         </div>
     </a>

     <div class="flex items-center justify-center pb-5 mt-8 font-extrabold">
         <div class="w-1/2 text-center">
             <a href="<?php echo $category_url; ?>"
                 class="px-3 py-1 text-opacity-75 bg-opacity-50 rounded-xl" 
                 style="background-color: <?php echo $final_light_color; ?>; color: <?php echo $final_base_color; ?>;">
                 <?php echo $category_name; ?>
             </a>
         </div>

         <span class="w-1/2 text-xs text-center text-gray-600 no-copy"><?php echo get_the_date(); ?></span>
     </div>
 </div>