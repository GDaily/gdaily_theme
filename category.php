<?php get_header(); ?>

<div class="pb-8 mx-auto"
    style="background-image: url('<?php echo get_template_directory_uri(); ?>/source/img/beams.webp');">

    <div>

        <?php 
            $cat_Id = $wp_query->get_queried_object_id();
            $parent_id = get_term($cat_Id, 'category')->parent ?: $cat_Id;
            $is_parent = ($parent_id == $cat_Id);
       ?>


        <?php get_template_part( 'part/category', 'nav', array('cat_Id' => $cat_Id, 'parent_id' => $parent_id , 'is_parent' => $is_parent) ); ?>


        <?php 
    

        if ($parent_id  == 1768) {
            echo '<div class="grid grid-cols-1 gap-y-10 gap-x-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">';

            if (have_posts()):
                $post_num = 0;
                while (have_posts()): the_post();
                    $post_num++;
                    if ( $post_num == 4 || $post_num == 10 ) {
                        get_template_part( 'part/card', 'app-adsense', get_post_format() );
                        get_template_part( 'part/card', 'app', get_post_format() );
                    } else {
                        get_template_part('part/card', 'app', get_post_format());
                    }
                endwhile;
                wp_reset_postdata();
            else:
                _e('No posts found.', 'text-domain'); 
            endif;

            echo '</div>';

       
        }
        else{
            echo '<div class="grid grid-cols-1 gap-y-10 gap-x-4 md:grid-cols-2 lg:grid-cols-3">';

            if (have_posts()):
                $post_num = 0;
                while (have_posts()): the_post();
                    $post_num++;
                    if ( $post_num == 4 || $post_num == 10 ) {
                        get_template_part( 'part/card', 'web-adsense', get_post_format() );
                        get_template_part( 'part/card', 'web', get_post_format() );
                    } else {
                        get_template_part('part/card', 'web', get_post_format());
                    }
                endwhile;
                wp_reset_postdata();
            else:
                _e('No posts found.', 'text-domain'); 
            endif;

            echo '</div>';
        }
        
        ?>


        <?php wpbeginner_numeric_posts_nav();?>
    </div>

</div>

<?php get_footer(); ?>