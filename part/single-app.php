 <article class="pb-10 mx-auto bg-opacity-20">

     <?php if (have_posts()) : ?>

 

         <figure class="pt-10 py-3 md:py-10 text-center" style="background-color: <?php echo $args['tailwind_hex_light_color']; ?>;">

             <div class="flex mx-auto w-[256px] h-[256px] overflow-hidden bg-white rounded-full">
                 <img src="<?php echo $args['thumbnail_url']; ?>" alt="" loading="lazy" decoding="async"
                     srcset=" <?php echo $args['thumbnail_app_url']; ?>" class="h-auto max-h-full mx-auto rounded-xl"
                     style=" transform: scale(<?php echo $args['scale']; ?> ); ">
             </div>

             <figcaption
                 class="py-5 mt-5 text-4xl font-extrabold text-center text-gray-600"
                 style="color: <?php echo $args['tailwind_hex_base_color']; ?>;">
                 <?php echo  carbon_get_post_meta($post->ID, 'app_name'); ?>
             </figcaption>


         </figure>

         <h1 class="px-10 mt-10 mb-10 text-3xl font-extrabold text-center text-gray-600 text-xl3 text-opacity-80 ">
             <?php echo  get_the_title(); ?>
         </h1>

         <?php if (!empty($args['adsense_enable'])) get_template_part('part/adsense/adsense_content'); ?>




         <?php
            while (have_posts()) :
                the_post();
            ?>


             <div class="px-10 mt-10">

                 <div class="entry-content" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                     <?php the_content(); ?>

                     <?php if (!empty($args['adsense_enable'])) get_template_part('part/adsense/adsense_content'); ?>

                 </div>



             </div>

             <?php
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>

         <?php endwhile; ?>

     <?php endif; ?>


 </article>