<?php 

/*  獲取縮圖 ID */

$thumbnail_id = get_post_thumbnail_id(get_the_ID());

$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, array(64,64,true))[0];
 
$tailwind_color = carbon_get_post_meta($post->ID, 'tailwind_color');

$app_name = carbon_get_post_meta($post->ID, 'app_name');
 
$tailwind_background_custom = getColorName(carbon_get_post_meta($post->ID, 'tailwind_background_custom'));

if(!empty($tailwind_background_custom) ){
    $tailwind_color = $tailwind_background_custom ; 
}
  
 
?>




<div x-data="{ scale: false }" @mouseenter="scale = true" @mouseleave="scale = false"
    class="  mx-auto   mt-16 text-gray-900  rounded-lg  shadow-sm  max-w-80 bg-opacity-40    bg-<?php echo $tailwind_color;?>-100   ">

    <a href="<?php the_permalink(); ?>">
        <div class=" h-24 overflow-hidden rounded-t-lg w-full  bg-opacity-30    bg-<?php echo $tailwind_color;?>-200">

        </div>
        <div
            class="relative flex items-center justify-center w-24 h-24 mx-auto -mt-12 overflow-hidden bg-white border-4 border-white rounded-full">
            <img class="object-cover object-center h-32 transition-transform duration-300 rounded-lg max-w-14 max-h-14"
                :class="scale ? 'scale-110' : ''" src='<?php echo $thumbnail_url  ; ?>' alt='Woman looking front'>

        </div>

        <div class="mt-3 mb-5 text-center ">
            <h3 class="font-semibold"> <?php echo $app_name; ?></h3>
            <div class="px-10 py-3 overflow-hidden tracking-widest text-gray-500 max-h-20 "> <?php the_excerpt(); ?>
            </div>
        </div>

        <div class="p-4 mx-8 mt-2 text-xs text-center ">
            <?php echo get_the_date(); ?>
        </div>

    </a>
</div>