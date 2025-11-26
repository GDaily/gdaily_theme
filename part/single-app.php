<article class="mx-auto bg-opacity-20 " itemscope itemtype="https://schema.org/Article" id="post-<?php the_ID(); ?>"
    <?php post_class(); ?>>

    <figure class="py-3 pt-10 text-center md:py-10" <?php if ($args['should_render_light_style'] ?? false): ?>
        style="background-color: <?php echo esc_attr($args['final_light_color']); ?>;" <?php endif; ?>>

        <div class="flex mx-auto w-[256px] h-[256px] overflow-hidden bg-white rounded-full"
            <?php if (($args['image_width'] ?? false) && ($args['image_height'] ?? false)): ?>
            style="aspect-ratio: <?php echo esc_attr(($args['image_width'] ?? 1) / ($args['image_height'] ?? 1)); ?>;"
            <?php endif; ?>>
            <img src="<?php echo esc_url($args['thumbnail_url'] ?? ''); ?>"
                alt="<?php echo esc_html(carbon_get_post_meta($args['post_id'], 'app_name')); ?>" loading="eager"
                decoding="async" srcset="<?php echo esc_attr($args['thumbnail_app_url'] ?? ''); ?>"
                class="h-auto max-h-full mx-auto rounded-xl"
                <?php if (($args['image_width'] ?? false) && ($args['image_height'] ?? false)): ?>
                width="<?php echo esc_attr($args['image_width']); ?>" height="<?php echo esc_attr($args['image_height']); ?>"
                <?php endif; ?>
                style="transform: scale(<?php echo esc_attr($args['scale'] ?? 1); ?>); object-fit: contain;" itemprop="image">
        </div>

        <figcaption class="py-5 mt-5 text-4xl font-extrabold text-center" itemprop="name"
            <?php if ($args['should_render_base_style'] ?? false): ?>
            style="color: <?php echo esc_attr($args['final_base_color']); ?>;" <?php endif; ?>>
            <?php echo esc_html(carbon_get_post_meta($args['post_id'], 'app_name')); ?>
        </figcaption>
    </figure>

    <!-- Article title -->
    <h1 class="pt-5 pb-10 text-2xl font-extrabold text-center" itemprop="headline">
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