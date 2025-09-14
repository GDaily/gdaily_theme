<?php 

/*  獲取縮圖 ID */

$thumbnail_id = get_post_thumbnail_id(get_the_ID());

$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, array(64,64,true))[0];
 
// 獲取 hex 顏色欄位
$tailwind_hex_base_color = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_base_color');
$tailwind_hex_light_color = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_light_color');

$app_name = carbon_get_post_meta(get_the_ID(), 'app_name');
  
 
?>




<div x-data="{ scale: false }" @mouseenter="scale = true" @mouseleave="scale = false"
    class="mx-auto mt-16 text-gray-900 rounded-lg shadow-sm max-w-80 bg-opacity-40"
    style="background-color: <?php echo $tailwind_hex_light_color; ?>; background-opacity: 0.4;">

    <a href="<?php the_permalink(); ?>">
        <div class="w-full h-24 overflow-hidden rounded-t-lg bg-opacity-30"
             style="background-color: <?php echo $tailwind_hex_light_color; ?>; background-opacity: 0.3;">

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