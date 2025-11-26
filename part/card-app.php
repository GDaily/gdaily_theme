<?php

/*  獲取縮圖 ID */

$thumbnail_id = get_post_thumbnail_id(get_the_ID());

$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, array(64, 64, true))[0];

// 獲取 hex 顏色欄位
$tailwind_hex_base_color = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_base_color');
$tailwind_hex_light_color = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_light_color');
$tailwind_hex_base_color_custom = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_base_color_custom');
$tailwind_hex_light_color_custom = carbon_get_post_meta(get_the_ID(), 'tailwind_hex_light_color_custom');

// 決定最終使用的顏色值
$final_base_color = !empty($tailwind_hex_base_color_custom) ? $tailwind_hex_base_color_custom : $tailwind_hex_base_color;
$final_light_color = !empty($tailwind_hex_light_color_custom) ? $tailwind_hex_light_color_custom : $tailwind_hex_light_color;

// 檢查是否為預設值（空值或 #ffffff），如果是則不使用顏色樣式
$should_render_base_style = !empty($final_base_color) && $final_base_color !== '#ffffff' && $final_base_color !== '#FFFFFF';
$should_render_light_style = !empty($final_light_color) && $final_light_color !== '#ffffff' && $final_light_color !== '#FFFFFF';

$app_name = carbon_get_post_meta(get_the_ID(), 'app_name');


?>




<div class="mx-auto mb-16 text-gray-900 rounded-lg shadow-sm max-w-80 bg-opacity-40"
    <?php if ($should_render_light_style): ?>
    style="background-color: <?php echo $final_light_color; ?>; background-opacity: 0.4;"
    <?php endif; ?>>

    <a href="<?php the_permalink(); ?>">
        <div class="w-full h-24 overflow-hidden rounded-t-lg bg-opacity-30"
            <?php if ($should_render_light_style): ?>
            style="background-color: <?php echo $final_light_color; ?>; background-opacity: 0.3;"
            <?php endif; ?>>

        </div>
        <div
            class="relative flex items-center justify-center w-24 h-24 mx-auto -mt-12 overflow-hidden bg-white border-4 border-white rounded-full hover:scale-105"
            style="transition: transform 0.3s ease;">
            <img class="object-cover object-center h-32 transition-transform duration-300 rounded-lg max-w-14 max-h-14 group-hover:scale-110"
                src="<?php echo esc_url($thumbnail_url); ?>"
                alt="<?php echo esc_attr(! empty($app_name) ? $app_name : get_the_title()); ?>">

        </div>

        <div class="mt-3 mb-5 text-center ">
            <h2 class="font-semibold"> <?php echo esc_html($app_name); ?></h2>
            <div class="px-10 py-3 overflow-hidden tracking-widest text-gray-500 max-h-20 "> <?php the_excerpt(); ?>
            </div>
        </div>

        <div class="p-4 mx-8 mt-2 text-xs text-center ">
            <time
                datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
        </div>

    </a>
</div>