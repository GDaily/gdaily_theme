<article class="mx-auto bg-opacity-20" itemscope itemtype="https://schema.org/Article">

    <!-- Article featured image with structured data -->
    <figure class="px-3 py-10 md:px-auto" <?php if ($args['should_render_light_style'] ?? false): ?>
        style="background-color: <?php echo esc_attr($args['final_light_color']); ?>;" <?php endif; ?>>
        <img src="<?php echo esc_url($args['thumbnail_url'] ?? ''); ?>"
            srcset="<?php echo esc_attr($args['imagePath'] ?? ''); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"
            loading="eager" decoding="async" class="h-auto max-h-full mx-auto rounded-xl" itemprop="image"
            <?php if (($args['image_width'] ?? false) && ($args['image_height'] ?? false)): ?>
            width="<?php echo esc_attr($args['image_width']); ?>"
            height="<?php echo esc_attr($args['image_height']); ?>"
            style="aspect-ratio: <?php echo esc_attr($args['image_width'] / $args['image_height']); ?>; height: auto; max-width: 100%;"
            <?php endif; ?>>
    </figure>

    <!-- Article headline with structured data -->
    <h1 class="pt-5 pb-10 text-4xl font-extrabold text-center" itemprop="headline"
        <?php if (($args['should_render_base_style'] ?? false) && ($args['should_render_light_style'] ?? false)): ?>
        style="color: <?php echo esc_attr($args['final_base_color']); ?>; background-color: <?php echo esc_attr($args['final_light_color']); ?>;"
        <?php elseif ($args['should_render_base_style'] ?? false): ?>
        style="color: <?php echo esc_attr($args['final_base_color']); ?>;"
        <?php elseif ($args['should_render_light_style'] ?? false): ?>
        style="background-color: <?php echo esc_attr($args['final_light_color']); ?>;" <?php endif; ?>>
        <?php the_title(); ?>
    </h1>

    <div class="mt-10 mx-5 md:mx-0">
        <?php while (have_posts()) : the_post(); ?>
            <div class="entry-content mx-auto max-w-4xl select-none text-lg leading-10 tracking-wider"
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