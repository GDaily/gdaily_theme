<article class="mx-auto bg-opacity-20">
    <div class="bg"></div>

    <?php if ( have_posts() ) : ?>
    <?php
            $post_id = get_the_ID();
            $adsense_enable = carbon_get_post_meta($post_id, 'adsense_enable');
            $tailwind_color = esc_attr($args['tailwind_color'] ?? 'gray');
            $thumbnail_url = esc_url($args['thumbnail_url'] ?? '');
            $image_path = esc_attr($args['imagePath'] ?? '');
        ?>

    <figure class="bg-<?php echo $tailwind_color; ?>-50 py-10 px-3 md:px-auto">
        <img src="<?php echo $thumbnail_url; ?>" srcset="<?php echo $image_path; ?>" alt="" loading="lazy"
            decoding="async" class="h-auto max-h-full mx-auto rounded-xl">
    </figure>

    <h1
        class="pt-5 pb-10 text-4xl font-extrabold text-center text-<?php echo $tailwind_color; ?>-800 bg-<?php echo $tailwind_color; ?>-50">
        <?php the_title(); ?>
    </h1>

    <?php if ( $adsense_enable ) : ?>
    <?php get_template_part('part/adsense', 'normal', get_post_format()); ?>
    <?php endif; ?>

    <div class="mt-10">
        <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php if ( $adsense_enable ) : ?>
                <?php get_template_part('part/adsense', 'normal', get_post_format()); ?>
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