<?php 

/*  獲取縮圖 */
$post_id = get_the_ID();
$imagePath = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), array(300,150,true))[0];
 


$categories = get_the_category();

if (!empty($categories)) {
    // 获取第一个分类
    $category_name = $categories[0]->name; // 分类名称
    $category_url = get_category_link($categories[0]->term_id); // 分类 URL
} 

?>
<div x-data="{ scale: false }" @mouseenter="scale = true" @mouseleave="scale = false"
    class=" overflow-hidden rounded-xl md:max-h-[150px] mx-auto max-w-[900px]   w-[300px] flex-col md:flex-row  bg-white  md:w-full flex">


    <a href="<?php the_permalink(); ?>">
        <div class="overflow-hidden ">
            <img src="<?php echo $imagePath; ?>" alt="" width="300" height="150" loading="lazy" decoding="async"
                class="h-auto max-h-full transition-transform duration-300" :class="scale ? 'scale-110' : ''">

        </div>


        <div class="flex flex-col flex-1 pt-5 pb-3 pl-5 pr-3 ">
            <h3 class="font-extrabold text-gray-500 text-xxl lg:max-h-[75px]  break-all  overflow-hidden">
                <?php the_title(); ?></h3>

            <div class="flex justify-center mt-auto md:justify-end">

                <a href="<?php echo $category_url; ?>"
                    class="px-3 py-1 mt-5 text-xs font-bold text-opacity-75 bg-gray-100  rounded-xl">
                    <?php echo $category_name; ?>
                </a>

            </div>
        </div>
    </a>

</div>