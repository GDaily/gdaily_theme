 <?php get_header(); ?>



 <div class="grid grid-cols-1 px-5 py-16 gap-y-10 gap-x-4 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 "
     style="background-image: url('<?php echo get_template_directory_uri(); ?>/source/img/beams.webp');">



     <?php
		$catquery = new WP_Query( 'cat=551,1785,1770,1730,1101,562,1529,1661,1534,551,552,560,554,558,559,1528,553,555,556&posts_per_page=13' );

		$post_num = 0;
	while ( $catquery->have_posts() ) :
		$catquery->the_post();
		++$post_num;

		if ( $post_num == 4 || $post_num == 10 ) {
			get_template_part( 'part/card', 'web-adsense', get_post_format() );
			get_template_part( 'part/card', 'web', get_post_format() );
		} else {
			get_template_part( 'part/card', 'web', get_post_format() );
		}


	endwhile;
		wp_reset_postdata();
	?>


 </div>









 <?php
	get_footer();