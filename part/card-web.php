 <?php
 
 
 
  

/*  獲取縮圖 ID */

$thumbnail_id = get_post_thumbnail_id(get_the_ID());

$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, array(400,200,true))[0];
 
$tailwind_color = carbon_get_post_meta($post->ID, 'tailwind_color');
 
$tailwind_background_custom = getColorName(carbon_get_post_meta($post->ID, 'tailwind_background_custom'));

if(!empty($tailwind_background_custom) ){
    $tailwind_color = $tailwind_background_custom ; 
}

 
$meta_data = wp_get_attachment_metadata($thumbnail_id);
 

 


// 获取当前文章的分类信息
$category_id = wp_get_post_categories(get_the_ID(), array('fields' => 'ids'))[0] ?? null;

if ($category_id) {
    $category = get_category($category_id);
    $category_name = $category->name;
    $category_url = get_category_link($category_id);
}

?>

 <div x-data="{ scale: false }" @mouseenter="scale = true" @mouseleave="scale = false" class="block max-w-2xl  bg-opacity-30 mb-12 overflow-hidden   shadow-md rounded-xl max-w-[400px] mx-auto relative  bg-<?php echo $tailwind_color;?>-200
     @mouseenter=" scale=true">

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
                 class="px-3 py-1 rounded-xl  text-opacity-75   bg-opacity-50 bg-<?php echo $tailwind_color;?>-200 text-<?php echo $tailwind_color;?>-600  ">
                 <?php echo $category_name; ?>
             </a>
         </div>

         <span class="w-1/2 text-xs text-center text-gray-600 no-copy"><?php echo get_the_date(); ?></span>
     </div>
 </div>