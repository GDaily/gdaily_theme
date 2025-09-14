<article class="mx-auto bg-opacity-20">
    <div class="bg"></div>
 
    <?php if ( have_posts() ) : ?>
        <?php
            $post_id = get_the_ID();
            $adsense_enable = $args['adsense_enable'] ;
            $tailwind_hex_base_color = esc_attr($args['final_base_color'] ?? '#6b7280');
            $tailwind_hex_light_color = esc_attr($args['final_light_color'] ?? '#f9fafb');
            $thumbnail_url = esc_url($args['thumbnail_url'] ?? '');
            $image_path = esc_attr($args['imagePath'] ?? '');
        ?>    <figure class="px-3 py-10 md:px-auto" style="background-color: <?php echo $tailwind_hex_light_color; ?>;">
        <img src="<?php echo $thumbnail_url; ?>" srcset="<?php echo $image_path; ?>" alt="" loading="lazy"
            decoding="async" class="h-auto max-h-full mx-auto rounded-xl">
    </figure>

    <h1 class="pt-5 pb-10 text-4xl font-extrabold text-center" 
        style="color: <?php echo $tailwind_hex_base_color; ?>; background-color: <?php echo $tailwind_hex_light_color; ?>;">
        <?php the_title(); ?>
    </h1>

    <?php if ( $adsense_enable ) : ?>
        <?php get_template_part('part/adsense/adsense_content'); ?>
    <?php endif; ?>

    <div class="mt-10">
        <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php if ( $adsense_enable ) : ?>
                    <?php get_template_part('part/adsense/adsense_content'); ?>
                <?php endif; ?>
            </div>
        </article>

        <?php
                    if ( comments_open() || get_comments_number() ) :
                        comments_template();
                    endif;
                ?>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</article>