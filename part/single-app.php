<article class="mx-auto bg-opacity-20 " itemscope itemtype="https://schema.org/Article" id="post-<?php the_ID(); ?>"
    <?php post_class(); ?>>

    <figure class="py-3 pt-10 text-center md:py-10"
        style="background-color: <?php echo esc_attr($args['final_light_color'] ?? '#f9fafb'); ?>;">

        <div class="flex mx-auto w-[256px] h-[256px] overflow-hidden bg-white rounded-full">
            <img src="<?php echo esc_url($args['thumbnail_url'] ?? ''); ?>"
                alt="<?php echo esc_html(carbon_get_post_meta($args['post_id'], 'app_name')); ?>" loading="lazy"
                decoding="async" srcset="<?php echo esc_attr($args['thumbnail_app_url'] ?? ''); ?>"
                class="h-auto max-h-full mx-auto rounded-xl"
                style="transform: scale(<?php echo esc_attr($args['scale'] ?? 1); ?>);" itemprop="image">
        </div>

        <figcaption class="py-5 mt-5 text-4xl font-extrabold text-center" itemprop="name"
            style="color: <?php echo esc_attr($args['final_base_color'] ?? '#6b7280'); ?>;">
            <?php echo esc_html(carbon_get_post_meta($args['post_id'], 'app_name')); ?>
        </figcaption>
    </figure>

    <!-- Article title -->
    <h1 class="pt-5 pb-10 text-4xl font-extrabold text-center" itemprop="headline"
        style="color: <?php echo esc_attr($args['final_base_color'] ?? '#6b7280'); ?>;  ">
        <?php the_title(); ?>
    </h1>

    <div class="mx-5 mt-10 md:mx-0">
        <?php while (have_posts()) : the_post(); ?>

            <div class="max-w-4xl mx-auto text-lg leading-10 tracking-wider select-none entry-content"
                itemprop="articleBody">
                <?php the_content(); ?>

                <?php if ($args['adsense_enable']) : ?>
                    <?php get_template_part('part/adsense/adsense_content'); ?>
                <?php endif; ?>
            </div>

            <?php get_template_part('part/article-meta'); ?>

            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

        <?php endwhile; ?>
    </div>

</article>