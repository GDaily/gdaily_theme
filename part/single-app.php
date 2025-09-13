 <article class="pb-10 mx-auto bg-opacity-20">

     <?php if ( have_posts() ) : ?>



     <figure class=" bg-<?php echo $args['tailwind_color']; ?>-50  pt-10 py-3   md:py-10  text-center    ">

         <div class="flex  mx-auto   w-[256px] h-[256px]  overflow-hidden bg-white rounded-full">
             <img src="<?php echo $args['thumbnail_url']; ?>" alt="" loading="lazy" decoding="async"
                 srcset=" <?php echo $args['thumbnail_app_url']; ?>" class="h-auto max-h-full mx-auto rounded-xl"
                 style=" transform: scale(<?php echo $args['scale']; ?> ); ">
         </div>

         <figcaption
             class="py-5 mt-5 text-4xl font-extrabold text-center text-gray-600 text-<?php echo $args['tailwind_color']; ?>-400 ">
             <?php echo  carbon_get_post_meta($post->ID, 'app_name'); ?>
         </figcaption>


     </figure>

     <h1 class="px-10 mt-10 mb-10 text-3xl font-extrabold text-center text-gray-600 text-xl3 text-opacity-80 ">
         <?php echo  get_the_title(); ?>
     </h1>

     <div class="adsense_content_img">
         <ins class="adsbygoogle example_responsive_1" style="display:inline-block"
             data-ad-client="ca-pub-7349735987764759" data-ad-slot="4688261049" data-ad-format="auto"
             data-full-width-responsive="true"></ins>
         <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
         </script>
     </div>

     <!--   <div class="flex justify-center">
             <div
                 class="inline-flex items-center justify-center px-4 py-1 mx-auto font-extrabold text-center text-white bg-gray-300 rounded-2xl">
                 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-6 h-6 fill-white">
                     <path
                         d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z" />
                 </svg>
                 <span class="pl-3"><?php echo meks_time_ago(); ?></span>
             </div>
     </div>
 -->



     <?php
		while ( have_posts() ) :
			the_post();
			?>


     <div class="px-10 mt-10">

         <div class="entry-content" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

             <?php the_content(); ?>

             <div class="adsense_content_img">
                 <ins class="adsbygoogle example_responsive_1" style="display:inline-block"
                     data-ad-client="ca-pub-7349735987764759" data-ad-slot="4688261049" data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                 <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
                 </script>
             </div>

         </div>



     </div>

     <?php
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
    ?>

     <?php endwhile; ?>

     <?php endif; ?>


 </article>